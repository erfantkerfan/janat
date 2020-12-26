import { Model, Collection } from 'js-abstract-model'
import { Fund } from '@/models/Fund'
import {LoanType} from "@/models/LoanType";

class Loan extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/loans'
            },
            { key: 'id' },
            { key: 'name' },
            { key: 'loan_amount' },
            { key: 'interest_rate' },
            { key: 'interest_amount' },
            { key: 'installment_rate' },
            { key: 'number_of_installments' },
            {
                key: 'fund',
                relatedModel: Fund
            },
            {
                key: 'loan_type',
                relatedModel: LoanType
            },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

}

class LoanList extends Collection {
    model () {
        return Loan
    }
}

export { Loan, LoanList }
