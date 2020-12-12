import { Model, Collection } from 'js-abstract-model'

class Fund extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/funds'
            },
            { key: 'id' },
            { key: 'name' },
            { key: 'monthly_payment' },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

}

class FundList extends Collection {
    model () {
        return Fund
    }
}

export { Fund, FundList }
