import { Model, Collection } from 'js-abstract-model'

class Setting extends Model {
    constructor (user) {
        super(user, [
            {
                key: 'baseRoute',
                default: 'api/settings'
            },
            { key: 'id' },
            { key: 'name' },
            { key: 'display_name' },
            { key: 'value' },
            { key: 'order' }
        ])
    }

}

class SettingList extends Collection {
    model () {
        return Setting
    }
}

export { Setting, SettingList }
