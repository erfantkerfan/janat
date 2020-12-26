export default {
    filters: {
        currencyFormat: function (value) {
            if (!value) return ''
            return parseInt(value).toLocaleString('fa')
        }
    }
};
