<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['namespace' => 'Admin', 'middleware' => 'cross'], function () {
    Route::post("auth/login", "UserControllers@login");
});

Route::group(['namespace' => 'Admin', 'middleware' => ['auth:sanctum', 'cross']], function () {
    Route::get("user/detail", "UserControllers@detail");
    Route::get("user/permission", "UserControllers@permission");
    Route::post("auth/logout", "UserControllers@logout");
    Route::post("permission/all", "PermissionControllers@all");
    Route::post("role/all", "RoleControllers@list");
    //  Route::post("pub/user/role", "UserControllers@roleList");
    Route::post("pub/role/user_list", "RoleControllers@userList");
    Route::post('qiniu/auth', 'UploadController@qiniuAuth'); //获取图片上传token
    Route::post("public/classify/list", "ClassifyControllers@getThreeCalssifyAll");

    Route::post("public/department/list", "DepartmentController@getThreeCalssifyAll");
});

Route::group(['namespace' => 'Admin', 'middleware' => ['cross', 'auth:sanctum', 'ly.permission']], function () {
    Route::post("role/list", "RoleControllers@list")->name('role-list');
    Route::get("role/detail", "RoleControllers@detail")->name('role-detail');
    Route::post("role/add", "RoleControllers@add")->name('role-add');
    Route::post("role/update", "RoleControllers@update")->name('role-update');
    Route::post("role/delete", "RoleControllers@delete")->name('role-delete');
    Route::post("role/permission", "RoleControllers@permission")->name('role-permission');
    Route::post("role/add/permission", "RoleControllers@addPermission")->name('role-add.permission');


    Route::post("user/list", "UserControllers@list")->name('user-list');
    Route::get("user/personal/detail", "UserControllers@personalDetail")->name('user-personal.detail');
    Route::post("user/update", "UserControllers@update")->name('user-update');
    Route::post("user/add", "UserControllers@add")->name('user-add');
    Route::post("user/delete", "UserControllers@delete")->name('user-delete');
    Route::post("user/role/list", "UserControllers@roleList")->name('user-role.list');
    Route::post("user/add/role", "UserControllers@addRole")->name('user-add.role');


    Route::post("order/list", "OrderControllers@list")->name('order-list');
    Route::post("order/delete", "OrderControllers@delete")->name('order-delete');
    Route::get("order/detail", "OrderControllers@detail")->name('order-detail');
    Route::post("order/add", "OrderControllers@add")->name('order-add');
    Route::post("order/count_num", "OrderControllers@count_num")->name('order-count.num');
    Route::post("order/update", "OrderControllers@update")->name('order-update');
    Route::post("order/statistics", "OrderControllers@statistics")->name('order-statistics');
    Route::post("order/status", "OrderControllers@status")->name('order-status');
    Route::post("order/logs", "OrderControllers@logs")->name('order-logs');

    Route::post("order/check", "OrderControllers@check")->name('order-check');
    Route::post("order/edit_name", "OrderControllers@editName")->name('order-edit.name');
    Route::post("order/manuscript", "OrderControllers@manuscript")->name('order-manuscript');

//    Route::post("order/hard_grade", "OrderControllers@grade")->name('order-hard.grade');

    Route::post("order/after", "OrderControllers@after")->name('order-after');

    Route::post("classify/list", "ClassifyControllers@list")->name('classify-list');
    Route::post("classify/delete", "ClassifyControllers@delete")->name('classify-delete');
    Route::post("classify/update", "ClassifyControllers@update")->name('classify-update');
    Route::post("classify/add", "ClassifyControllers@create")->name('classify-create');

    Route::post("order/export", "OrderControllers@export")->name('order-export');

    Route::post("edit/order/list", "EditControllers@orderList")->name('edit-statistics.order.list');
    Route::post("edit/statistics/all/list", "EditControllers@allList")->name('edit-statistics.all.list');
    Route::post("edit/statistics/day/list", "EditControllers@dayList")->name('edit-statistics.day.list');

    Route::post("staff/statistics/list", "StaffControllers@list")->name('staff-statistics.list');
    Route::post("staff/statistics/list/export", "StaffControllers@export")->name('staff-statistics.export');

    Route::post("manuscript_bank/list", "ManuscriptBankControllers@list")->name('manuscript_bank-list');
    Route::post("manuscript_bank/delete", "ManuscriptBankControllers@delete")->name('manuscript_bank-delete');
    Route::post("manuscript_bank/update", "ManuscriptBankControllers@update")->name('manuscript_bank-update');
    Route::post("manuscript_bank/add", "ManuscriptBankControllers@create")->name('manuscript_bank-create');

    Route::post("department/list", "DepartmentController@list")->name('department-list');
    Route::post("department/delete", "DepartmentController@delete")->name('department-delete');
    Route::post("department/update", "DepartmentController@update")->name('department-update');
    Route::post("department/add", "DepartmentController@create")->name('department-create');
});

