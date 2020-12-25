import { Model, Collection } from 'js-abstract-model'
import {TransactionStatus} from "./TransactionStatus"
import {Fund} from "@/models/Fund";
import {User} from "@/models/User";
import {Company} from "@/models/Company";
import {AllocatedLoan} from "@/models/AllocatedLoan";
import {AllocatedLoanInstallment} from "@/models/AllocatedLoanInstallment";

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
