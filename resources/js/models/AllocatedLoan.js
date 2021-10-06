import { Model, Collection } from 'js-abstract-model'
// import {Account} from "./Account";
import {Loan} from "./Loan";
import {AllocatedLoanInstallmentList} from "./AllocatedLoanInstallment";
import {Transaction} from "@/models/Transaction";

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
            {
                key: 'last_payment',
                relatedModel: Transaction
            },
            { key: 'interest_rate' },
            { key: 'interest_amount' },
            { key: 'total_payments' },
            { key: 'remaining_payable_amount' },
            { key: 'count_of_paid_installments' },
            { key: 'count_of_remaining_installments' },
            { key: 'loan_amount' },
            { key: 'installment_rate' },
            { key: 'number_of_installments' },
            { key: 'payroll_deduction', default: false },
            { key: 'payroll_deduction_amount' },
            { key: 'allocated_loan_paid_at' },
            { key: 'count_of_paid_payments_as_payroll_deduction_in_date_range' },
            { key: 'sum_of_paid_payments_as_payroll_deduction_in_date_range' },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])

        this.payroll_deduction = !!this.payroll_deduction
    }

}

class AllocatedLoanList extends Collection {
    model () {
        return AllocatedLoan
    }
}

export { AllocatedLoan, AllocatedLoanList }
