<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;

use Gdevilbat\SpardaCMS\Modules\Role\Entities\Role as Role_m;
use Gdevilbat\SpardaCMS\Modules\Core\Entities\Module as Module_m;
use Gdevilbat\SpardaCMS\Modules\Role\Entities\AccessRole as AccessRole_m;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Validator;
use Auth;

class RoleController extends CoreController
{
    public function __construct()
    {
        parent::__construct();
        $this->role_m = new Role_m;
        $this->role_repository = new Repository(new Role_m, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->module_m = new Module_m;
        $this->module_repository = new Repository(new Module_m, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->access_role_m = new AccessRole_m;
        $this->access_role_repository = new Repository(new AccessRole_m, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $this->data['roles'] = $this->role_repository->with('modules')->get();
        $this->data['modules'] = $this->module_repository->all();
        return view('role::admin.'.$this->data['theme_cms']->value.'.content.master', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->data['method'] = method_field('POST');
        if(isset($_GET['code']))
        {
            $this->data['role'] = $this->role_repository->find(decrypt($_GET['code']));
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-role', $this->data['role']);
        }

        return view('role::admin.'.$this->data['theme_cms']->value.'.content.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
        ]);

        if($request->isMethod('POST'))
        {
            $validator->addRules([
                'slug' => 'max:191|unique:'.$this->role_m->getTable().',slug'
            ]);
        }
        else
        {
            $validator->addRules([
                'slug' => 'max:191|unique:'.$this->role_m->getTable().',slug,'.decrypt($request->input(\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::getPrimaryKey())).','.\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::getPrimaryKey()
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->isMethod('POST'))
        {
            $data = $request->except('_token', '_method');
            $role = new $this->role_m;
        }
        else
        {
            $data = $request->except('_token', '_method', \Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::getPrimaryKey());
            $role = $this->role_repository->findOrFail(decrypt($request->input(\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::getPrimaryKey())));
            $this->authorize('update-role', $role);
        }

        foreach ($data as $key => $value) 
        {
            $role->$key = $value;
        }

        if($request->isMethod('POST'))
        {
            $role->created_by = Auth::id();
            $role->modified_by = Auth::id();
        }
        else
        {
            $role->modified_by = Auth::id();
        }

        if($role->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(route('cms.role.master'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Role!'));
            }
            else
            {
                return redirect(route('cms.role.master'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Role!'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Role!'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Role!'));
            }
        }
    }

    public function accessScope(Request $request)
    {
        $this->validate($request, [
                'access' => 'required'
        ]);

        $input = $request->input('access');

        foreach ($input as $role_user) 
        {
            foreach($role_user as $value)
            {
                $role =  $this->access_role_m->where(\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::FOREIGN_KEY, decrypt($value[\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::FOREIGN_KEY]))->where(\Gdevilbat\SpardaCMS\Modules\Core\Entities\Module::FOREIGN_KEY, decrypt($value[\Gdevilbat\SpardaCMS\Modules\Core\Entities\Module::FOREIGN_KEY]))->first();
                if(empty($role))
                    $role = new $this->access_role_m;

                $role[\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::FOREIGN_KEY] = decrypt($value[\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::FOREIGN_KEY]);
                $role[\Gdevilbat\SpardaCMS\Modules\Core\Entities\Module::FOREIGN_KEY] = decrypt($value[\Gdevilbat\SpardaCMS\Modules\Core\Entities\Module::FOREIGN_KEY]);
                $role->access_scope = $value['access_scope'];
                if(!$role->save())
                {
                    return redirect(route('cms.role.master'))->with('global_message', array('status' => 400, 'message' => 'Failed To Update Role Provider!'));
                }
            }
        }

        return redirect(route('cms.role.master'))->with('global_message', array('status' => 200, 'message' => 'Successfully To Update Role Provider!'));
    }

    public function checkRole($scope ,$modules, $id)
    {
        $modules = $modules->where(\Gdevilbat\SpardaCMS\Modules\Core\Entities\Module::getPrimaryKey(), $id);
        foreach ($modules as $module) 
        {
            if(!property_exists(json_decode($module->pivot->access_scope), $scope))
                return false;

            return json_decode(json_decode($module->pivot->access_scope)->$scope);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('role::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('role::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $query = $this->role_m->findOrFail(decrypt($request->input(\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::getPrimaryKey())));

        $this->authorize('delete-role', $query);

        try {
            if($query->delete())
            {
                return redirect(route('cms.role.master'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Role!'));
            }
            
        } catch (\Exception $e) {
            return redirect(route('cms.role.master'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Role, It\'s Has Been Used!'));
        }
    }
}
