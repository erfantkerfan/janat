import { Model, Collection } from 'js-abstract-model'
import {Fund} from "./Fund";

class Account extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/accounts'
            },
            { key: 'id' },
            { key: 'user_id' },
            { key: 'acc_number' },
            {
                key: 'fund',
                relatedModel: Fund
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
