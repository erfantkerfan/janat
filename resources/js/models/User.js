import { Model, Collection } from 'js-abstract-model'
import { RoleList } from '@/models/Role'
import { AccountList } from '@/models/Account'
import { UserStatus } from '@/models/UserStatus'
import {UserType} from "@/models/UserType";

class User extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/users'
            },
            { key: 'id' },
            { key: 'f_name' },
            { key: 'l_name' },
            { key: 'father_name' },
            { key: 'user_pic' },
            { key: 'SSN' },
            { key: 'staff_code' },
            { key: 'password' },
            { key: 'salary' },
            { key: 'address' },
            { key: 'phone' },
            { key: 'mobile' },
            { key: 'email' },
            { key: 'description' },
            { key: 'password' },
            { key: 'password_confirmation' },

            {
                key: 'roles',
                relatedModel: RoleList
            },
            {
                key: 'status',
                relatedModel: UserStatus
            },
            {
                key: 'user_type',
                relatedModel: UserType
            },
            {
                key: 'accounts',
                relatedModel: AccountList
            },

            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

    hasSuperAdminRole () {
        if (!this.roles) {
            return false
        }
        const adminRole = this.roles.list.find( (item) => { return  item.name === 'Super Admin' })
        if (!adminRole) {
            return false
        }

        return true
    }

    getUserPic (id, url) {
        if (!this.baseRoute) {
            return new Promise(() => {
                throw new Error('baseRoute is not set');
            })
        }

        if (!id) {
            id = this.id;
        }

        if (!url) {
            url = this.baseRoute + '/' + id + '/get_user_pic';
        }


        return this.crud.fetch(url);
    }

    getTotalBalance (id, url) {
        if (!this.baseRoute) {
            return new Promise(() => {
                throw new Error('baseRoute is not set');
            })
        }

        if (!id) {
            id = this.id;
        }

        if (!url) {
            url = this.baseRoute + '/' + id + '/get_total_balance';
        }


        return this.crud.fetch(url);
    }

    setUserPic (data, id, url) {
        if (!this.baseRoute) {
            return new Promise(() => {
                throw new Error('baseRoute is not set');
            })
        }

        if (!id) {
            id = this.id;
        }

        let formData = new FormData();
        formData.append('user_pic', data);
        formData.append('id', id);

        if (!url) {
            url = this.baseRoute + '/' + id + '/set_user_pic';
        }


        return this.crud.update(url, formData);
    }

    updatePassword (oldPass, newPass, confirmNewPassword, id, url) {
        if (!this.baseRoute) {
            return new Promise(() => {
                throw new Error('baseRoute is not set');
            })
        }

        if (!id) {
            id = this.id;
        }

        if (!url) {
            url = this.baseRoute + '/' + id + '/reset_pass';
        }


        return this.crud.update(url, {
            'old_password': oldPass,
            'new_password': newPass,
            'new_password_confirmation': confirmNewPassword
        });
    }

    setFullName () {
        if (this.f_name !== null && this.l_name !== null) {
            this.full_name = this.f_name + ' ' + this.l_name
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
        let nationalCode = this.SSN
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
        let validData = this.SSN
        if (!this.hasValidSSN()) {
            if (this.SSN !== null && this.SSN.trim().length < 10) {
                validData = this.SSN.trim().padStart(10, '0')
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
            this.SSN = validData
        }
    }
}

class UserList extends Collection {
    model () {
        return User
    }
}

export { User, UserList }
