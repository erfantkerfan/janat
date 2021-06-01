import { Model, Collection } from 'js-abstract-model'
import {Fund} from "./Fund";
// import {User} from "./User";
import {AllocatedLoanList} from "./AllocatedLoan";
import {Company} from "@/models/Company";

class Account extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/accounts'
            },
            { key: 'id' },
            { key: 'user_id' },
            { key: 'user' },
            { key: 'monthly_payment', value: 0 },
            { key: 'balance' },
            {
                key: 'payroll_deduction',
                value: function (itemVal) {
                    return !!itemVal;
                }
            },


            // {
            //     key: 'user',
            //     relatedModel: User
            // },
            {
                key: 'fund',
                relatedModel: Fund
            },
            {
                key: 'company',
                relatedModel: Company
            },
            {
                key: 'allocated_loans',
                relatedModel: AllocatedLoanList
            },
            { key: 'joined_at' },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

    getBalance () {
        let that = this
        return new Promise((myResolve, myReject) => {
            axios.get(this.baseRoute + '/' + this.id + '/balance')
                .then( (response) => {
                    that.balance = response.data
                    myResolve(response)
                })
                .catch( (error) => {
                    myReject(error)
                })
        });
    }

}

class AccountList extends Collection {
    model () {
        return Account
    }
}

export { Account, AccountList }
