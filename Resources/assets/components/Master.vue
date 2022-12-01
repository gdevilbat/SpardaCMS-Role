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
                    <div class="row mb-4" v-if="$parent.permissions['create-role']">
                        <div class="col-md-5">
                            <router-link :to="{name: 'role-form'}" class="btn btn-brand m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                <span>
                                    <i class="la la-plus"></i>
                                    <span>Add New Role</span>
                                </span>
                            </router-link>
                        </div>
                    </div>
                    <form @submit.prevent="submit($event)">
                        <div class="table-responsive">
                            <table class="table table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">No.</th>
                                        <th>Role Name</th>
                                        <th v-for="(module, index) in data.modules" :key="index"><center>{{module.name}}</center></th>
                                        <th style="width: 50px; vertical-align: middle;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="(role, role_index) in data.roles">
                                        <tr :key="role_index" v-if="role.slug != 'super-admin' && $parent.user.role.slug != role.slug && role.permissions.read">
                                            <td>{{role_index+1}}</td>
                                            <td style="vertical-align: middle;">{{role.name}}</td>
                                            <template v-for="(module, module_index) in data.modules">
                                                <td :key="module_index">
                                                    <div class="m-form__group form-group" v-if="module.permissions.permission && role.access.hasOwnProperty(module.slug)">
                                                            <div class="m-checkbox-list">
                                                                <label class="m-checkbox" v-for="(scope, scope_index) in module.array_scope" :key="scope_index">
                                                                    <input type="checkbox" class="checkbox" :checked="role.access[module.slug][scope]" v-model="role.access[module.slug][scope]">
                                                                    {{scope}}
                                                                    <input type="hidden" class="role" :name="'access['+role_index+']['+module_index+'][access_scope]['+scope+']'" v-model="role.access[module.slug][scope]">
                                                                    <input type="hidden" :name="'access['+role_index+']['+module_index+']['+role.foreign_key+']'" :value="role.encrypted_id">
                                                                    <input type="hidden" :name="'access['+role_index+']['+module_index+']['+module.foreign_key+']'" :value="module.encrypted_id">
                                                                    <span></span>
                                                                </label>
                                                            </div>
                                                    </div>
                                                </td>
                                            </template>
                                            <td style="vertical-align: middle;">
                                                <div class="btn-group">
                                                    <a class="btn btn-outline-primary dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false"> Actions
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-left" role="menu">
                                                        <button class="dropdown-item" type="button" v-if="role.permissions.update">
                                                            <router-link class="m-link m-link--state m-link--info" :to="{name: 'role-form', query: {'code': role.encrypted_id}}"><i class="fa fa-edit"> Edit</i></router-link>
                                                        </button>
                                                        <button class="dropdown-item" type="button" v-if="role.permissions.delete"><a class="m-link m-link--state m-link--accent" data-toggle="modal" :href="'#small-'+role_index"><i class="fa fa-trash"> Delete</i></a></button>
                                                    </div>
                                                </div>
                                            </td>
                                            <div class="modal fade" :id="'small-'+role_index" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel">
                                                <div class="modal-dialog">
                                                    <form @submit.prevent="remove($event)">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Delete Confirmation</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body"> 
                                                                <h5 class="text-center">
                                                                Are You Sure ?
                                                                </h5>
                                                                <input type="hidden" :name="role.primary_key" :value="role.encrypted_id">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary confirmed">Delete</button>
                                                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <!-- /.modal-content -->
                                                </div>
                                            <!-- /.modal-dialog -->
                                            </div>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end" v-if="$parent.permissions['create-role']">
                            <button type="submit" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <loading
            :is-full-page="true"
            :active.sync="loading"/>
    </div>
</template>
<script>
    import _ from 'lodash'
    import Loading from 'vue-loading-overlay';

    export default {
        components: {
            Loading
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
                loading: false,
                data: {}
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
        mounted() {
            this.loading = false
            let self = this;

            axios({
                method: "post",
                url:'/control/role/data',
            })
            .then(response => {
                self.data = response.data.data;
                self.loading = false;
            })
            // eslint-disable-next-line
            .catch(errors => {
                //Handle Errors
            })
        },
        methods: {
            submit(e){
                const formData = new FormData(e.target);

                const self = this;

                self.loading = true;
                axios({
                    method: "post",
                    url: "/control/role/role-scope",
                    data: formData,
                })
                .then(function (response) {
                    //handle success
                    self.updated = response.data;
                    self.loading = false;

                    if(response.data.status){
                        self.$router.go(self.$router.currentRoute);
                    }else{
                        window.scrollTo(0, 0);
                        setTimeout(() => {
                            self.$set(self.updated, 'status', false);
                        }, 2000);
                    }

                })
                .catch(function (error) {
                    //handle error
                    self.loading = false;
                    self.errors = error.response.data.errors
                });
            },
            remove(e){
                const formData = new FormData(e.target);
                formData.append('_method', 'DELETE');

                const self = this;

                self.loading = true;
                axios({
                    method: "post",
                    url: '/control/role/destroy',
                    data: formData,
                })
                .then(function (response) {
                    //handle success
                    self.updated = response.data;
                    self.loading = false;

                    if(response.data.code == 200){
                        self.$router.go(self.$router.currentRoute);
                    }else{
                        window.scrollTo(0, 0);
                        setTimeout(() => {
                            self.$set(self.updated, 'status', false);
                        }, 2000);
                    }

                    window.$('.modal').modal('hide')
                })
                .catch(function (error) {
                    //handle error
                    console.log(error);
                });
            }
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