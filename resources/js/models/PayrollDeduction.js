import { Model, Collection } from 'js-abstract-model'
import {Transaction} from "@/models/Transaction";

class PayrollDeduction extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: '/api/payroll_deduction'
            },
            { key: 'id' },
            { key: 'paid_for_loan' },
            { key: 'paid_for_monthly_payment' },
            { key: 'from' },
            { key: 'to' },
            { key: 'paid_at' },
            {
                key: 'transactions',
                relatedModel: Transaction
            },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

}

class PayrollDeductionList extends Collection {
    model () {
        return PayrollDeduction
    }
}

export { PayrollDeduction, PayrollDeductionList }
