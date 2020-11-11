import { Model, Collection } from 'js-abstract-model'

class User extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: ''
            },
            { key: 'id' },
            { key: 'f_name' },
            { key: 'l_name' },
            { key: 'fa_name' },
            { key: 'SSN' },
            { key: 'staff_code' },
            { key: 'company_id' },
            { key: 'address' },
            { key: 'phone' },
            { key: 'status' },
            { key: 'joined_at' },
            { key: 'email' },
            { key: 'email_verified_at' },
            { key: 'password' },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

    setFullName () {
        if (this.first_name !== null && this.last_name !== null) {
            this.full_name = this.first_name + ' ' + this.last_name
        }
    }

    hasValidPhone (input) {
        let mobile = this.mobile
        if (typeof input !== 'undefined') {
            mobile = input
        }
        const patt = /^09\d{9}$/g
        return patt.test(mobile)
    }

    hasValidSSN (input) {
        let nationalCode = this.nationalCode
        if (typeof input !== 'undefined') {
            nationalCode = input
        }
        if (!/^\d{10}$/.test(nationalCode)) { return false }

        const check = parseInt(nationalCode[9])
        let sum = 0
        for (let i = 0; i < 9; ++i) {
            sum += parseInt(nationalCode[i]) * (10 - i)
        }
        sum %= 11

        return (sum < 2 && check === sum) || (sum >= 2 && check + sum === 11)
    }

    convertPhoneToValidValue (buffer) {
        let validData = this.mobile
        if (!this.hasValidPhone()) {
            if (this.mobile !== null && this.mobile.trim().length < 11) {
                validData = this.mobile.trim().padStart(11, '0')
                if (!this.hasValidPhone(validData)) {
                    validData = null
                }
            } else {
                validData = null
            }
        }

        if (typeof buffer !== 'undefined' && buffer) {
            return validData
        } else {
            this.mobile = validData
        }
    }

    convertSSNToValidValue (buffer) {
        let validData = this.nationalCode
        if (!this.hasValidSSN()) {
            if (this.nationalCode !== null && this.nationalCode.trim().length < 10) {
                validData = this.nationalCode.trim().padStart(10, '0')
                if (!this.hasValidSSN(validData)) {
                    validData = null
                }
            } else {
                validData = null
            }
        }

        if (typeof buffer !== 'undefined' && buffer) {
            return validData
        } else {
            this.nationalCode = validData
        }
    }
}

class UserList extends Collection {
    model () {
        return User
    }
}

export { User, UserList }
