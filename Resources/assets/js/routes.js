const RoleMaster = () => import('../components/Master.vue')
const RoleForm = () => import('../components/Form.vue')

export default class routes{
    constructor(Meta) {
        this.meta = Meta;
    }

    route(){
        return [
            {
                path: 'role/master',
                name: 'role-master',
                components : {
                    content : RoleMaster,
                },
                props: { content: true },
                meta: {...this.meta, title_dashboard: 'Role'}
            },
            {
                path: 'role/form',
                name: 'role-form',
                components : {
                    content : RoleForm,
                },
                meta: {...this.meta, title_dashboard: 'Role'}
            },
        ]
    }
}