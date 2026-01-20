/**
 * Global Utility Functions
 * Used for formatting currency and other common tasks.
 */

const Utils = {
    /**
     * Format a number string into thousands separator format (e.g. 10.000)
     * @param {string|number} value 
     * @returns {string}
     */
    formatNumber: function (value) {
        if (!value && value !== 0) return '';
        // Remove non-digits first (in case of re-formatting)
        let number = value.toString().replace(/\D/g, '');
        return new Intl.NumberFormat('id-ID').format(number);
    },

    /**
     * Remove formatting to get raw integer
     * @param {string} value 
     * @returns {number}
     */
    cleanNumber: function (value) {
        if (!value) return 0;
        return parseInt(value.toString().replace(/\./g, '')) || 0;
    },

    /**
     * Initialize auto-formatting on inputs with class 'input-currency'
     */
    initCurrencyInputs: function () {
        $(document).on('input', '.input-currency', function () {
            let cursorPosition = this.selectionStart;
            let originalLength = this.value.length;

            // Format
            let val = Utils.cleanNumber(this.value);
            if (val === 0 && this.value === '') {
                this.value = '';
                return;
            }
            this.value = Utils.formatNumber(val);

            // Restore cursor position (approximate)
            let newLength = this.value.length;
            cursorPosition = cursorPosition + (newLength - originalLength);
            this.setSelectionRange(cursorPosition, cursorPosition);
        });
    }
};

// Auto Init on Load
$(document).ready(function () {
    Utils.initCurrencyInputs();
});
