@extends('core::admin.'.$theme_cms->value.'.template')

@section('title_dashboard', ' Module')

@section('breadcrumb')
    <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
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
    </ul>
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">

            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--tab">
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

                <div class="m-portlet__body">
                    @if (!empty(session('global_message')))
                        <div class="col-md-5">
                            <div class="alert {{session('global_message')['status'] == 200 ? 'alert-info' : 'alert-warning' }} alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                                {{session('global_message')['message']}}
                            </div>
                        </div>
                    @endif
                    @if (count($errors) > 0)
                        <div class="col-md-5">
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @can('create-role')

                        <div class="row mb-4">
                            <div class="col-md-5">
                                <a href="{{action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@create')}}" class="btn btn-brand m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>Add New Role</span>
                                    </span>
                                </a>
                            </div>
                        </div>

                    @endcan

                    <!--begin: Datatable -->
                    <form action="{{action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@accessScope')}}" method="post" id="form-role">
                        <table class="table table-striped" id="html_table" width="100%">
                            <thead>
                                <thead>
                                    <tr>
                                        <th style="width: 10px">No.</th>
                                        <th>Role Name</th>
                                        @foreach($modules as $module)
                                            <th><center>{{title_case($module->slug)}}</center></th>
                                        @endforeach
                                        <th style="width: 50px; vertical-align: middle;">Action</th>
                                    </tr>
                                </thead>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                    @if($role->slug == 'super-admin')
                                        @can('super-access')
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td style="vertical-align: middle;">{{$role->name}}</td>
                                                @foreach($modules as $module)
                                                    <td>
                                                        <div class="m-form__group form-group">
                                                            <div class="m-checkbox-list">
                                                                @foreach($module->scope as $scope)
                                                                    @if($scope != 'permission')
                                                                        <label class="m-checkbox">
                                                                           <input type="checkbox" class="checkbox" {{Route::current()->getController()->checkRole( $scope, $role->modules, $module->id) ? "checked" : ""}}>
                                                                            {{$scope}}
                                                                            <input type="hidden" class="role" name="access[{{$loop->parent->parent->index}}][{{$loop->parent->index}}][access_scope][{{$scope}}]">
                                                                            <input type="hidden" name="access[{{$loop->parent->parent->index}}][{{$loop->parent->index}}][role_id]" value="{{encrypt($role->id)}}">
                                                                            <input type="hidden" name="access[{{$loop->parent->parent->index}}][{{$loop->parent->index}}][module_id]" value="{{encrypt($module->id)}}">
                                                                            <span></span>
                                                                        </label>
                                                                    @endcan
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </td>
                                                @endforeach
                                                <td style="vertical-align: middle;">
                                                    <div class="btn-group">
                                                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-left" role="menu">
                                                            <button class="dropdown-item" type="button">
                                                                <a class="m-link m-link--state m-link--info" href="{{action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@create').'?code='.encrypt($role->id)}}"><i class="fa fa-edit"> Edit</i></a>
                                                            </button>
                                                            <form>
                                                            </form>
                                                            <form action="{{action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@destroy')}}" method="post" accept-charset="utf-8">
                                                                {{method_field('DELETE')}}                                                        
                                                                {{csrf_field()}}
                                                                <input type="hidden" name="id" value="{{encrypt($role->id)}}">
                                                            </form>
                                                            <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Delete</i></a></button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endcan
                                    @else
                                        @can('read-role', $role)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td style="vertical-align: middle;">{{$role->name}}</td>
                                                @foreach($modules as $module)
                                                    <td>
                                                        @can('permission-'.$module->slug)
                                                            <div class="m-form__group form-group">
                                                                <div class="m-checkbox-list">
                                                                    @foreach($module->scope as $scope)
                                                                        <label class="m-checkbox">
                                                                               <input type="checkbox" class="checkbox" {{Route::current()->getController()->checkRole( $scope, $role->modules, $module->id) ? "checked" : ""}}>
                                                                                {{$scope}}
                                                                                <input type="hidden" class="role" name="access[{{$loop->parent->parent->index}}][{{$loop->parent->index}}][access_scope][{{$scope}}]">
                                                                                <input type="hidden" name="access[{{$loop->parent->parent->index}}][{{$loop->parent->index}}][role_id]" value="{{encrypt($role->id)}}">
                                                                                <input type="hidden" name="access[{{$loop->parent->parent->index}}][{{$loop->parent->index}}][module_id]" value="{{encrypt($module->id)}}">
                                                                                <span></span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endcan
                                                    </td>
                                                @endforeach
                                                <td style="vertical-align: middle;">
                                                    <div class="btn-group">
                                                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-left" role="menu">
                                                            @can('update-role', $role)
                                                                <button class="dropdown-item" type="button">
                                                                    <a class="m-link m-link--state m-link--info" href="{{action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@create').'?code='.encrypt($role->id)}}"><i class="fa fa-edit"> Edit</i></a>
                                                                </button>
                                                            @endcan
                                                            <form>
                                                            </form>
                                                            @can('delete-role', $role)
                                                                <form action="{{action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@destroy')}}" method="post" accept-charset="utf-8">
                                                                    {{method_field('DELETE')}}                                                        
                                                                    {{csrf_field()}}
                                                                    <input type="hidden" name="id" value="{{encrypt($role->id)}}">
                                                                </form>
                                                                <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Delete</i></a></button>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endcan
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        {{method_field('POST')}}
                        {{csrf_field()}}
                        @can('create-role')
                            <div class="col-md-12 d-flex justify-content-end">
                                <button id="submit-role" type="button" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">Submit</button>
                            </div>
                        @endcan
                    </form>

                    <!--end: Datatable -->
                </div>


            </div>

            <!--end::Portlet-->

        </div>
    </div>

@endsection

@section('page_level_js')
    {{Html::script(module_asset_url('role:resources/views/admin/'.$theme_cms->value.'/js/role.js').'?id='.filemtime(module_asset_path('role:resources/views/admin/'.$theme_cms->value.'/js/role.js')))}}
@endsection