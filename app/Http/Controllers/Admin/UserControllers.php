<?php


namespace App\Http\Controllers\Admin;


use App\Http\Constants\CodeMessageConstants;
use App\Http\Controllers\Controller;
use App\Http\Model\Department;
use App\Http\Model\User;
use App\Http\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserControllers extends Controller
{
    public function __construct(Request $request, UserServices $services)
    {
        $this->request = $request;
        $this->services = $services;
    }

    /**
     * FunctionName：list
     * Description：列表
     * Author：cherish
     * @return mixed
     */
    public function list()
    {
        $page = $this->request->input('page') ?? 1;
        $pageSize = $this->request->input('pageSize') ?? 10;
        $user = new User();
        if ($this->request->input('username')) {
            $user = $user->where('name', 'like', "%" . $this->request->input('username') . "%");
        }
        $user = $user->with('department')->paginate($pageSize, ['*'], "page", $page);
        if ($user->items()) {
            foreach ($user->items() as $values) {
                $us = User::findOrFail($values['id']);
                $values['roles'] = $us->roles;
            }
        }
        return $user;
    }

    /**
     * FunctionName：personalDetail
     * Description：用户详情
     * Author：cherish
     * @return mixed
     */
    public function personalDetail()
    {
        $this->request->validate([
            'id' => ['required', 'exists:' . (new User())->getTable() . ',id'],
        ]);
        return User::find($this->request->input('id'));
    }

    /**
     * FunctionName：add
     * Description：创建
     * Author：cherish
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function add()
    {
        $this->request->validate([
            'username' => ['required', 'unique:' . (new User())->getTable() . ',name'],
            'password' => 'required',
            'email' => ['required', 'unique:' . (new User())->getTable() . ',email'],
            'department_id' => ['required', 'exists:' . (new Department())->getTable() . ',id'],
        ]);
        return User::create([
            'name' => $this->request->input('username'),
            'password' => Hash::make($this->request->input('password')),
            'department_id' => $this->request->input('department_id'),
            'email' => $this->request->input('email')
        ]);
    }

    /**
     * FunctionName：update
     * Description：更新
     * Author：cherish
     * @return mixed
     */
    public function update()
    {
        $id = $this->request->input('id');
        $this->request->validate([
            'id' => ['required', 'exists:' . (new User())->getTable() . ',id'],
            'username' => ['required', 'unique:' . (new User())->getTable() . ',name,' . $id],
            'email' => ['unique:' . (new User())->getTable() . ',email,' . $id],
            'department_id' => ['required', 'exists:' . (new Department())->getTable() . ',id'],
        ]);
        $data['department_id'] = $this->request->input('department_id');
        $data['name'] = $this->request->input('username');
        if ($this->request->input('password'))
            $data['password'] = Hash::make($this->request->input('password'));
        $user = User::find($this->request->input('id'));
        if ($user['admin'])
            throw \ExceptionFactory::business(CodeMessageConstants::IS_ADMIN);
        return User::where('id', $this->request->input('id'))->update($data);
    }

    /**
     * FunctionName：delete
     * Description：删除
     * Author：cherish
     * @return mixed
     */
    public function delete()
    {
        $this->request->validate([
            'id' => ['required', 'exists:' . (new User())->getTable() . ',id'],
        ]);
        $user = User::find($this->request->input('id'));
        if ($user['admin'])
            throw \ExceptionFactory::business(CodeMessageConstants::IS_ADMIN);
        return User::where('id', $this->request->input('id'))->delete();
    }


    /**
     * FunctionName：roleList
     * Description：用户所属
     * Author：cherish
     * @return mixed
     */
    public function roleList()
    {
        $this->request->validate([
            'id' => ['required', 'exists:' . (new User())->getTable() . ',id'],
        ]);
        $user = User::findOrFail($this->request->input('id'));
        return $user->roles;
    }

    /**
     * FunctionName：addRole
     * Description：添加角色
     * Author：cherish
     */
    public function addRole()
    {
        $this->request->validate([
            'id' => ['required', 'exists:' . (new User())->getTable() . ',id'],
        ]);
        $user = User::findOrFail($this->request->input('id'));
        $user->syncRoles($this->request->input('roles', []));
        return;
    }

    /**
     * FunctionName：login
     * Description：授权
     * Author：cherish
     */
    public function login()
    {
        $this->request->validate([
            'username' => "required",
            "password" => "required"
        ]);
        return $this->services->login($this->request->input('username'), $this->request->input('password'));
    }

    /**
     * FunctionName：detail
     * Description：获取用户详情
     * Author：cherish
     * @return mixed
     */
    public function detail()
    {
        $user = \Auth::user();
        $user->roles;
        return $user;
    }

    /**
     * FunctionName：permission
     * Description：获取登陆用户的权限
     * Author：cherish
     * @return \Illuminate\Http\JsonResponse
     */
    public function permission()
    {
        return \Auth::user()->getAllPermissions();
    }

    /**
     * FunctionName：logout
     * Description：退出登陆
     * Author：cherish
     * @return mixed
     */
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return;
    }
}
