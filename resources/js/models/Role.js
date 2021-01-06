import { Model, Collection } from 'js-abstract-model'

class Role extends Model {
    constructor (user) {
        super(user, [
            { key: 'id' },
            { key: 'name' },
            { key: 'guard_name' },
            { key: 'created_at' },
            { key: 'updated_at' }
        ])
    }

}

class RoleList extends Collection {
    model () {
        return Role
    }
}

export { Role, RoleList }
