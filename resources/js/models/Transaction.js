import { Model, Collection } from 'js-abstract-model'
import {TransactionStatus} from "./TransactionStatus"
import {Fund} from "@/models/Fund";
import {User} from "@/models/User";
import {Company} from "@/models/Company";
import {AllocatedLoan} from "@/models/AllocatedLoan";
import {AllocatedLoanInstallment} from "@/models/AllocatedLoanInstallment";
import {Loan} from "@/models/Loan";

class Transaction extends Model {
    constructor (data) {
        super(data, [
            {
                key: 'baseRoute',
                default: 'api/transactions'
            },
            { key: 'id' },

            { key: 'transaction_type' },
            { key: 'user_id' },
            { key: 'account_id' },
            { key: 'company_id' },
            { key: 'allocated_loan_id' },
            { key: 'allocated_loan_installment_id' },

            { key: 'cost' },
            { key: 'manager_comment' },
            { key: 'user_comment' },
            { key: 'parent_transaction_id' },
            {
                key: 'transaction_status',
                relatedModel: TransactionStatus
            },
            {
                key: 'related_payers',
                default: (itemValue, inputData) => {
                    if(!itemValue) {
                        return null
                    }

                    itemValue.map((item)=> {
                        if (item.transaction_payers_type === 'App\\Fund') {
                            item.transaction_payers = new Fund(item.transaction_payers)
                        } else if (item.transaction_payers_type === 'App\\User') {
                            item.transaction_payers = new User(item.transaction_payers)
                        } else if (item.transaction_payers_type === 'App\\Company') {
                            item.transaction_payers = new Company(item.transaction_payers)
                        }
                    })
                }
            },
            {
                key: 'related_recipients',
                default: (itemValue, inputData) => {
                    if(!itemValue) {
                        return null
                    }

                    itemValue.map((item)=> {
                        if (item.transaction_recipients_type === 'App\\Fund') {
                            item.transaction_recipients = new Fund(item.transaction_recipients)
                        } else if (item.transaction_recipients_type === 'App\\AllocatedLoan') {
                            item.transaction_recipients = new AllocatedLoan(item.transaction_recipients)
                        } else if (item.transaction_recipients_type === 'App\\AllocatedLoanInstallment') {
                            item.transaction_recipients = new AllocatedLoanInstallment(item.transaction_recipients)
                        }
                    })
                }
            },
            { key: 'deadline_at' },
            { key: 'paid_at' },
            { key: 'created_at' },
            { key: 'updated_at' },
            { key: 'deleted_at' }
        ])
    }

    getRelatedModelType(modelType) {
        if (modelType === 'App\\Company') {
            return 'شرکت'
        } else if (modelType === 'App\\User') {
            return 'شخص'
        } else if (modelType === 'App\\Fund') {
            return 'صندوق'
        } else if (modelType === 'App\\AllocatedLoan') {
            return 'وام تخصیص داده شده'
        } else if (modelType === 'App\\AllocatedLoanInstallment') {
            return 'قسط وام'
        }
    }

    getRelatedModelLabel(modelType, modelValue) {
        if (modelType === 'App\\Company') {
            return modelValue.name
        } else if (modelType === 'App\\User') {
            return modelValue.f_name + ' ' + modelValue.l_name
        } else if (modelType === 'App\\Fund') {
            return modelValue.name
        } else if (modelType === 'App\\AllocatedLoan') {
            if(modelValue.is_settled) {
                return 'تسویه شده'
            } else {
                return 'تسویه نشده'
            }
        } else if (modelType === 'App\\AllocatedLoanInstallment') {
            if(modelValue.is_settled) {
                return 'تسویه شده'
            } else {
                return 'تسویه نشده'
            }
        }
    }

    getRelatedModelRoute(modelType, modelValue) {
        if (modelType === 'App\\Company') {
            return '/company/'+modelValue.id
        } else if (modelType === 'App\\User') {
            return '/user/'+modelValue.id
        } else if (modelType === 'App\\Fund') {
            return '/fund/'+modelValue.id
        } else if (modelType === 'App\\AllocatedLoan') {
            return '/allocated_loan/'+modelValue.id
        } else if (modelType === 'App\\AllocatedLoanInstallment') {
            return '/allocated_loan/'+modelValue.allocated_loan_id
        }
    }
}

class TransactionList extends Collection {
    model () {
        return Transaction
    }
}

export { Transaction, TransactionList }
