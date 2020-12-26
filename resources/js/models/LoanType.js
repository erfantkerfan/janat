import { Model, Collection } from 'js-abstract-model'

class LoanType extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/loan_types'
            },
            { key: 'id' },
            { key: 'name' },
            { key: 'display_name' },
            { key: 'description' }
        ])
    }

}

class LoanTypeList extends Collection {
    model () {
        return LoanType
    }
}

export { LoanType, LoanTypeList }
