import { Model, Collection } from 'js-abstract-model'

class UserStatus extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/user_statuses'
            },
            { key: 'id' },
            { key: 'name' },
            { key: 'displayName' },
            { key: 'description' }
        ])
    }

}

class UserStatusList extends Collection {
    model () {
        return UserStatus
    }
}

export { UserStatus, UserStatusList }
