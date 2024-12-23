<?php


namespace App\Http\Controllers\Admin;


use App\Http\Model\Order;
use App\Http\Model\Role;
use App\Http\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EditControllers
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * FunctionName：list
     * Description：总的统计
     * Author：cherish
     * @return mixed
     */
    public function allList()
    {
        $page = $this->request->input('page') ?? 1;
        $pageSize = $this->request->input('pageSize') ?? 10;
        $order = new Order();
        if ($this->request->input('name')) {
            $order = $order->where('edit_name', 'like', "%" . $this->request->input('name') . "%");
        }
        if ($this->request->input('end_time')) {
            $order = $order->whereDate('created_at', '<=', $this->request->input('end_time'))->whereDate('created_at', '>=', $this->request->input('created_at'));
        }
        if ($this->request->input('submission_time')) {
            $order = $order->whereDate('submission_time', '<=', $this->request->input('submission_time'));
        }
        $role = Role::where('alias', "edit")->first();
        $userName = User::role($role['name'])->pluck('name');
        $order = $order->whereIn('edit_name', $userName);
        return $order->select(
            "edit_name",
            DB::raw('sum(case when status = 1 then 1 else 0 end) as all_waiting_commit'), //总待提交数
            DB::raw("sum(case when status = 2 then 1 else 0 end) as all_waiting_alter"), //打回修改(总待修改数量)
            DB::raw("	sum(case when status = 3 then 1 else 0 end) as all_finish"), //订单完成(总已经完成数量)
            DB::raw("	sum(case when status = 4 or status = 5  then 1 else 0 end) as all_commit") //提交客户(总已经提交数量)
        )->groupBy('edit_name')->paginate($pageSize, ['*'], "page", $page);
    }


    /**
     * FunctionName：dayList
     * Description：单日统计
     * Author：cherish
     * @return mixed
     */
    public function dayList()
    {
        $page = $this->request->input('page') ?? 1;
        $pageSize = $this->request->input('pageSize') ?? 10;
        $order = new Order();
        $allOrder = new Order();
        if ($this->request->input('name')) {
            $order = $order->where('edit_name', 'like', "%" . $this->request->input('name') . "%");
        }
        if ($this->request->input('created_at')) {
            $date =$this->request->input('created_at');
        } else {
            $date = date("Y-m-d");
        }
        $role = Role::where('alias', "edit")->first();
        $userName = User::role($role['name'])->pluck('name');
        $order = $order->whereIn('edit_name', $userName);

        return $order->select(
            "edit_name",
            DB::raw("count(case when edit_submit_time like '%$date%' then edit_submit_time else null end) as commit"), //提交数量
            DB::raw("sum(case when edit_submit_time like '%$date%' then word_number else 0 end) as commit_word_number"), //提交字数
            DB::raw("count(case when edit_submit_time like '%$date%' then edit_submit_time else null end) as alter_number"), //修改数量
            DB::raw("sum(case when edit_submit_time like '%$date%' then alter_word else 0 end) as alter_word_number"), //修改字数
            DB::raw("count(case when created_at like '%$date%' then created_at else null end ) as num"), //数量
            DB::raw("	sum(case when created_at like '%$date%' then amount else 0 end) as amount"),//金额
            DB::raw("sum(case when created_at like '%$date%' then word_number else 0 end) as word_number") //字数
        )->groupBy('edit_name')->paginate($pageSize, ['*'], "page", $page);
    }

    public function orderList()
    {
        $page = $this->request->input('page') ?? 1;
        $pageSize = $this->request->input('pageSize') ?? 10;
        $order = new Order();
        if ($this->request->input('subject')) {
            $order = $order->where('subject', 'like', "%" . $this->request->input('subject') . "%");
        }
        if ($this->request->input('word_number')) {
            $order = $order->where('word_number', $this->request->input('word_number'));
        }
        if ($this->request->input('id')) {
            $order = $order->where('id', '=', $this->request->input('id'));
        }
        if ($this->request->input('classify_id')) {
            $order = $order->where('classify_id', 'like', "%" . $this->request->input('classify_id') . "%");
        }
        if ($this->request->input('status')) {
            $order = $order->where('status', '=', $this->request->input('status'));
        }
        if ($this->request->input('created_at')) {
            $order = $order->where('created_at', 'like', "%" . $this->request->input('created_at') . "%");
        }
        return $order->orderBy('created_at', 'desc')->with('classify')->paginate($pageSize, ['*'], "page", $page);
    }
}


