import { Model, Collection } from 'js-abstract-model'
import {Loan} from "./Loan";

class TransactionType extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/transaction_types'
            },
            { key: 'id' },
            { key: 'name' },
            { key: 'display_name' },
            { key: 'description' },
            { key: 'created_at' },
            { key: 'updated_at' },
            { key: 'deleted_at' }
        ])
    }

}

class TransactionTypeList extends Collection {
    model () {
        return TransactionType
    }
}

export { TransactionType, TransactionTypeList }
