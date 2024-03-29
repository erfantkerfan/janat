import persianJs from 'persianjs'
// import PN from 'persian-number'
import NumberToPersianWord from 'number_to_persian_word'

export default {
    data: () => ({
        currencyUnit: '',
    }),
    created () {
        this.currencyUnit = this.$store.getters['settings/currencyUnit']
    },
    filters: {
        currencyFormat: function (value) {
            if (!value) return '0'
            value = value.toString()
            value = value.replace(new RegExp('٬', 'g'), '')
            const digits = [
                {from: '۰', to: 0},
                {from: '۱', to: 1},
                {from: '۲', to: 2},
                {from: '۳', to: 3},
                {from: '۴', to: 4},
                {from: '۵', to: 5},
                {from: '۶', to: 6},
                {from: '۷', to: 7},
                {from: '۸', to: 8},
                {from: '۹', to: 9}
            ]
            digits.forEach( (item)=> {
                value = value.replace(new RegExp(`${item.from}`, 'g'), item.to.toString())
            })
            return parseInt(value).toLocaleString('fa')
        }
    },
    methods: {
        normilizedDigits (digits) {
            let number = digits
            if (typeof number === 'string') {
                if (number.includes('‎')) {
                    number = number.substr(1)
                    number = number.replace('‎', '')
                }
                number = number.replace('−', '-')
                number = number.trim()
            }
            return parseInt(number)
        },
        digitsToWords (input) {
            //deep copy of string
            let digits = this.normilizedDigits((' ' + input).slice(1));

            if (!digits) {
                return ''
            }

            let converted = NumberToPersianWord.convert(digits)

            return converted
        },
        convertToCurrencyFormat (value) {
            if (!value) return '0'
            value = this.toEnDigit(value.toString())
            return this.normilizedDigits(value).toLocaleString('fa')
        },
        currencyFormatInput (event) {
            this.transaction.cost = this.toEnDigit(event)
        },
        toFaDigit (value) {
            if (!value) return ''
            const digits = [
                {from: 0, to: '۰'},
                {from: 1, to: '۱'},
                {from: 2, to: '۲'},
                {from: 3, to: '۳'},
                {from: 4, to: '۴'},
                {from: 5, to: '۵'},
                {from: 6, to: '۶'},
                {from: 7, to: '۷'},
                {from: 8, to: '۸'},
                {from: 9, to: '۹'}
            ]

            return this.digitsConvertor_replaceDigits(value, digits)
        },
        toEnDigit (value) {
            if (!value) return ''
            const digits = [
                {from: '۰', to: 0},
                {from: '۱', to: 1},
                {from: '۲', to: 2},
                {from: '۳', to: 3},
                {from: '۴', to: 4},
                {from: '۵', to: 5},
                {from: '۶', to: 6},
                {from: '۷', to: 7},
                {from: '۸', to: 8},
                {from: '۹', to: 9}
            ]


            return this.digitsConvertor_replaceDigits(value, digits)
        },
        digitsConvertor_replaceDigits (value, digits) {
            let result = value.toString()
            digits.forEach( (item)=> {
                result = result.replace(new RegExp(`${item.from}`, 'g'), item.to.toString())
            })
            return result
        }
    }
};
