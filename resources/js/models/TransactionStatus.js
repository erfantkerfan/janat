import { Model, Collection } from 'js-abstract-model'
import {Loan} from "./Loan";

class TransactionStatus extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/transactions'
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

class TransactionStatusList extends Collection {
    model () {
        return TransactionStatus
    }
}

export { TransactionStatus, TransactionStatusList }
