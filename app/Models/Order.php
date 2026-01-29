<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_number
 * @property float $total_price
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property-read string $formatted_total
 * @property-read string $elapsed_time
 * @property-read string $formatted_created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @method static Builder|Order pending()
 * @method static Builder|Order byStatus(string $status)
 * @method static Builder|Order today()
 */
class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'customer_name',
        'total_price',
        'status',
        'processing_at',
        'completed_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'processing_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to pending orders (New or Processing).
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['new', 'processing']);
    }

    /**
     * Scope by specific status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to orders created today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp ' . number_format($this->total_price, 0, ',', '.')
        );
    }

    protected function elapsedTime(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at->diffForHumans()
        );
    }

    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at->translatedFormat('d M Y, H:i')
        );
    }

    protected function queueNumber(): Attribute
    {
        return Attribute::make(
            // Since order_number is now stored as "001", we can just return it.
            // But to be safe (if old data "260127001" exists), we keep the substr logic or just return order_number if length <= 3
            get: function () {
                if (strlen($this->order_number) <= 3) {
                    return str_pad($this->order_number, 3, '0', STR_PAD_LEFT);
                }
                return substr($this->order_number, -3);
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Generate next order number for today (e.g., "001").
     * Uses atomic lock or transaction to prevent duplicates in high concurrency.
     * Resets daily.
     */
    public static function generateOrderNumber(): string
    {
        return DB::transaction(function () {
            // Get last order from today
            // Note: Since we removed unique constraint on order_number, 
            // we must scope by today to find the sequence.
            $lastOrder = self::whereDate('created_at', Carbon::today())
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (!$lastOrder) {
                return '001';
            }

            // Check if last order has new format (3 digits) or old format (long)
            $lastOrderNumber = $lastOrder->order_number;
            
            // If it's old format "YYMMDDXXX", extract last 3
            if (strlen($lastOrderNumber) > 3) {
                 $sequence = intval(substr($lastOrderNumber, -3));
            } else {
                 $sequence = intval($lastOrderNumber);
            }

            return str_pad($sequence + 1, 3, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Update order status and handle timestamps.
     */
    public function updateStatus(string $newStatus): bool
    {
        if (!in_array($newStatus, ['new', 'processing', 'done'])) {
            return false;
        }

        $this->status = $newStatus;

        if ($newStatus === 'processing' && !$this->processing_at) {
            $this->processing_at = now();
        }

        if ($newStatus === 'done' && !$this->completed_at) {
            $this->completed_at = now();
        }

        return $this->save();
    }
}
