import { Model, Collection } from 'js-abstract-model'

class UserType extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/user_types'
            },
            { key: 'id' },
            { key: 'name' },
            { key: 'display_name' },
            { key: 'description' }
        ])
    }

}

class UserTypeList extends Collection {
    model () {
        return UserType
    }
}

export { UserType, UserTypeList }
