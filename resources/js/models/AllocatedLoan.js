import { Model, Collection } from 'js-abstract-model'
// import {Account} from "./Account";
import {Loan} from "./Loan";
import {AllocatedLoanInstallmentList} from "./AllocatedLoanInstallment";

class AllocatedLoan extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/allocated_loans'
            },
            { key: 'id' },
            { key: 'account' },
            // {
            //     key: 'account',
            //     relatedModel: Account
            // },
            {
                key: 'loan',
                relatedModel: Loan
            },
            {
                key: 'installments',
                relatedModel: AllocatedLoanInstallmentList
            },
            { key: 'is_settled' },
            { key: 'total_payments' },
            { key: 'remaining_payable_amount' },
            { key: 'count_of_paid_installments' },
            { key: 'count_of_remaining_installments' },
            { key: 'loan_amount' },
            { key: 'installment_rate' },
            { key: 'number_of_installments' },
            { key: 'payroll_deduction', default: false },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

}

class AllocatedLoanList extends Collection {
    model () {
        return AllocatedLoan
    }
}

export { AllocatedLoan, AllocatedLoanList }
