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
            { key: 'fund_id' },
            { key: 'account_id' },
            { key: 'company_id' },
            { key: 'allocated_loan_id' },
            { key: 'allocated_loan_installment_id' },

            { key: 'cost' },
            { key: 'manager_comment' },
            { key: 'user_comment' },
            { key: 'paid_as_payroll_deduction' },
            { key: 'parent_transaction_id' },
            {
                key: 'transaction_status',
                relatedModel: TransactionStatus
            },
            {
                key: 'related_payers',
                default: (itemValue) => {
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
                default: (itemValue) => {
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

    addPicture (data, id, url) {
        if (!this.baseRoute) {
            return new Promise(() => {
                throw new Error('baseRoute is not set');
            })
        }

        if (!id) {
            id = this.id;
        }

        let formData = new FormData();
        formData.append('picture', data);
        formData.append('transaction_id', id);

        if (!url) {
            url = this.baseRoute + '/' + id + '/add_pic';
        }


        return this.crud.update(url, formData);
    }

    getPictures (id, url) {
        if (!this.baseRoute) {
            return new Promise(() => {
                throw new Error('baseRoute is not set');
            })
        }

        if (!id) {
            id = this.id;
        }

        if (!url) {
            url = this.baseRoute + '/' + id + '/get_pics';
        }


        return this.crud.fetch(url);
    }

    getRelatedModelType(modelType) {
        if (modelType === 'App\\Company') {
            return 'شرکت'
        } else if (modelType === 'App\\User') {
            return 'شخص'
        } else if (modelType === 'App\\Account') {
            return 'حساب'
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
        } else if (modelType === 'App\\Account') {
            return modelValue.id + '(' + modelValue.user.f_name + ' ' + modelValue.user.l_name + ')'
        } else if (modelType === 'App\\User') {
            return modelValue.f_name + ' ' + modelValue.l_name
        } else if (modelType === 'App\\Fund') {
            return modelValue.name
        } else if (modelType === 'App\\AllocatedLoan') {
            let paymentStatus = 'تسویه نشده'
            if(modelValue.is_settled) {
                paymentStatus = 'تسویه شده'
            }

            return paymentStatus + '<br>' +
                '(' + modelValue.account.user.f_name + ' ' +
                modelValue.account.user.l_name + '<br>' +
                ' شماره حساب: ' + modelValue.account.id + '<br>' +
                ' شرکت: ' + modelValue.account.company.name +
                ')'
        } else if (modelType === 'App\\AllocatedLoanInstallment') {
            let paymentStatus = 'تسویه نشده'
            if(modelValue.is_settled) {
                paymentStatus = 'تسویه شده'
            }

            return paymentStatus +
                '<br>' +
                '(' + modelValue.allocated_loan.account.user.f_name + ' '
                + modelValue.allocated_loan.account.user.l_name +
                '-' +
                ' صندوق: ' + modelValue.allocated_loan.account.fund.name + '<br>' +
                ' شرکت: ' + modelValue.allocated_loan.account.company.name +
                ')'
        }
    }

    getRelatedModelRoute(modelType, modelValue) {
        if (modelType === 'App\\Company') {
            return '/company/'+modelValue.id
        } else if (modelType === 'App\\User') {
            return '/user/'+modelValue.id
        } else if (modelType === 'App\\Account') {
            return '/account/'+modelValue.id
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
