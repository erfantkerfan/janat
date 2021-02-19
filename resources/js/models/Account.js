import { Model, Collection } from 'js-abstract-model'
import {Fund} from "./Fund";
// import {User} from "./User";
import {AllocatedLoanList} from "./AllocatedLoan";

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
                key: 'allocated_loans',
                relatedModel: AllocatedLoanList
            },
            { key: 'joined_at' },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

}

class AccountList extends Collection {
    model () {
        return Account
    }
}

export { Account, AccountList }
