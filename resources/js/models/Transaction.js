import { Model, Collection } from 'js-abstract-model'
import {TransactionStatus} from "./TransactionStatus"

class Transaction extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/transactions'
            },
            { key: 'id' },
            { key: 'cost' },
            { key: 'manager_comment' },
            { key: 'user_comment' },
            { key: 'parent_transaction_id' },
            {
                key: 'transaction_status',
                relatedModel: TransactionStatus
            },
            { key: 'deadline_at' },
            { key: 'created_at' },
            { key: 'updated_at' },
            { key: 'deleted_at' }
        ])
    }

}

class TransactionList extends Collection {
    model () {
        return Transaction
    }
}

export { Transaction, TransactionList }
