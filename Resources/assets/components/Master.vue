<template>
	<div class="row">
        <div class="col-sm-12">

            <div class="m-portlet m-portlet--tab">
                <!--begin::Portlet-->
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <span class="m-portlet__head-icon m--hide">
                                    <i class="fa fa-gear"></i>
                                </span>
                                <h3 class="m-portlet__head-text">
                                    Master Data of Role
                                </h3>
                            </div>
                        </div>
                    </div>
                <!--end::Portlet-->


                <div class="m-portlet__body">
                    <div class="col-md-5" v-if="updated.status">
                        <div class="alert alert-dismissible fade show" v-bind:class="{'alert-info': updated.code == 200, 'alert-danger': updated.code != 200}">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                            {{updated.message}}
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-5">
                            <router-link :to="{name: 'role-form'}" class="btn btn-brand m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                <span>
                                    <i class="la la-plus"></i>
                                    <span>Add New Role</span>
                                </span>
                            </router-link>
                        </div>
                    </div>
                    <Datatable
                        url="/control/module/data"
                        :tableProps="this.tableProps"
                        :columns="this.columns"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import _ from 'lodash'

    import Datatable from '^/Core/Resources/assets/components/Datatable/Master.vue'
    import Action from '^/Core/Resources/assets/components/Datatable/Action.vue'

    export default {
        components: {
            Datatable
        },
        props: {
            updated: {
                type: Object,
                default(rawProps) {
                    return {
                            status: false,
                            code: 0,
                            message: ''
                    }
                }
            },
        },
        data(){
            return{
                data: {},
                tableProps: {
                    search: '',
                    length: 10,
                    column: 'id_module',
                    dir: 'asc'
                },
                columns: [
                    {
                        label: 'ID',
                        name: 'id_module',
                        orderable: true,
                    },
                    {
                        label: 'Name',
                        name: 'name',
                        orderable: true,
                    },
                    {
                        label: 'Slug',
                        name: 'slug',
                        orderable: true,
                    },
                    {
                        label: 'Order',
                        name: 'order',
                        orderable: true,
                    },
                    {
                        label: 'Scanable',
                        name: 'string_is_scanable',
                        orderable: false,
                    },
                    {
                        label: 'Scope',
                        name: 'string_scope',
                        orderable: false,
                    },
                    {
                        label: '',
                        name: 'View',
                        orderable: false,
                        meta: {
                            action:{
                                edit: {
                                   status: true ,
                                   link: 'module-form'
                                },
                                delete: {
                                   status: true ,
                                   link: '/control/module/form'
                                }
                            }
                        },
                        action:{
                            edit: {
                                status: true 
                            }
                        },
                        component: Action, 
                    },
                ]
            }
        },
        created() {
          this.$parent.$data.breadcumb = `<ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
                                        <li class="m-nav__item m-nav__item--home">
                                            <a href="#" class="m-nav__link m-nav__link--icon">
                                                <i class="m-nav__link-icon la la-home"></i>
                                            </a>
                                        </li>
                                        <li class="m-nav__separator">-</li>
                                        <li class="m-nav__item">
                                            <a href="" class="m-nav__link">
                                                <span class="m-nav__link-text">Home</span>
                                            </a>
                                        </li>
                                        <li class="m-nav__separator">-</li>
                                        <li class="m-nav__item">
                                            <a href="" class="m-nav__link">
                                                <span class="m-nav__link-text">Role</span>
                                            </a>
                                        </li>
                                    </ul>`;
        },
        methods: {
        }
    }
</script>
<style >
</style>
<style lang="scss">
    @import 'vue-loading-overlay/dist/vue-loading.css';
    
    .pagination{
        -webkit-box-pack: end !important;
        -ms-flex-pack: end !important;
        justify-content: flex-end !important;
    }
</style>