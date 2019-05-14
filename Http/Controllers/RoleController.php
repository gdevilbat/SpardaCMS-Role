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
        $this->role_repository = new Repository(new Role_m);
        $this->module_m = new Module_m;
        $this->module_repository = new Repository(new Module_m);
        $this->access_role_m = new AccessRole_m;
        $this->access_role_repository = new Repository(new AccessRole_m);
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $this->data['roles'] = $this->role_repository->with('modules')->all();
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
                'slug' => 'max:191|unique:'.$this->role_m->getTable().',slug,'.decrypt($request->input('id')).',id'
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
            $data = $request->except('_token', '_method', 'id');
            $role = $this->role_repository->findOrFail(decrypt($request->input('id')));
            $this->authorize('update-role', $role);
        }

        foreach ($data as $key => $value) 
        {
            $role->$key = $value;
        }
        $role->user_id = Auth::id();

        if($role->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Role!'));
            }
            else
            {
                return redirect(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Role!'));
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
        $input = $request->input('access');

        foreach ($input as $role_user) 
        {
            foreach($role_user as $value)
            {
                $role =  $this->access_role_m->where('role_id', decrypt($value['role_id']))->where('module_id', decrypt($value['module_id']))->first();
                if(empty($role))
                    $role = new $this->access_role_m;

                $role->role_id = decrypt($value['role_id']);
                $role->module_id = decrypt($value['module_id']);
                $role->access_scope = $value['access_scope'];
                if(!$role->save())
                {
                    return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Role Provider!'));
                }
            }
        }

        return redirect()->back()->with('global_message', array('status' => 200, 'message' => 'Successfully To Update Role Provider!'));
    }

    public function checkRole($scope ,$modules, $id)
    {
        $modules = $modules->where('id', $id);
        foreach ($modules as $module) 
        {
            if(!array_key_exists($scope, json_decode($module->pivot->access_scope)))
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
        $query = $this->role_m->findOrFail(decrypt($request->input('id')));

        $this->authorize('delete-role', $query);

        try {
            if($query->delete())
            {
                return redirect()->back()->with('global_message', array('status' => 200,'message' => 'Successfully Delete Role!'));
            }
            
        } catch (\Exception $e) {
            return redirect()->back()->with('global_message', array('status' => 200,'message' => 'Failed Delete Role, It\'s Has Been Used!'));
        }
    }
}
