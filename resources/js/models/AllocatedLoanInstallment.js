import { Model, Collection } from 'js-abstract-model'
import {TransactionList} from "./Transaction";

class AllocatedLoanInstallment extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/allocated_loan_Installments'
            },
            { key: 'id' },
            { key: 'rate' },
            { key: 'allocated_loan_id' },
            { key: 'is_settled' },
            { key: 'total_payments' },
            { key: 'remaining_payable_amount' },
            {
                key: 'received_transactions',
                relatedModel: TransactionList
            },
            { key: 'created_at' },
            { key: 'updated_at' },
            { key: 'deleted_at' }
        ])
    }

}

class AllocatedLoanInstallmentList extends Collection {
    model () {
        return AllocatedLoanInstallment
    }
}

export { AllocatedLoanInstallment, AllocatedLoanInstallmentList }
