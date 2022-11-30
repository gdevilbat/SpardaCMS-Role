const ModuleMaster = () => import('../components/Master.vue')
const ModuleForm = () => import('../components/Form.vue')

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
                    content : ModuleMaster,
                },
                props: { content: true },
                meta: {...this.meta, title_dashboard: 'Module'}
            },
            {
                path: 'role/form',
                name: 'role-form',
                components : {
                    content : ModuleForm,
                },
                meta: {...this.meta, title_dashboard: 'Module'}
            },
        ]
    }
}