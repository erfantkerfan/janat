import { Model, Collection } from 'js-abstract-model'
import { Fund } from '@/models/Fund'

class Company extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/companies'
            },
            { key: 'id' },
            { key: 'name' },
            {
                key: 'fund',
                relatedModel: Fund
            }
        ])
    }

}

class CompanyList extends Collection {
    model () {
        return Company
    }
}

export { Company, CompanyList }
