import { Model, Collection } from 'js-abstract-model'

class Fund extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/funds'
            },
            {
                key: 'incomes',
                default: {
                    sum_of_charge_fund: 0,
                    sum_of_installments_interest: 0,
                    sum_of_all: 0
                }
            },
            { key: 'id' },
            { key: 'name' },
            { key: 'undertaker' },
            { key: 'balance' },
            { key: 'expenses' },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

    getIncomesAndExpenses() {
        return this.show(this.id, this.baseRoute + '/' + this.id + '/get_incomes_and_expenses')
    }
}

class FundList extends Collection {
    model () {
        return Fund
    }
}

export { Fund, FundList }
