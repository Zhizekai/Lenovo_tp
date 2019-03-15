<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/3/2
 * Time: 10:32
 */

namespace app\admin\controller;


use think\Db;

class SearchAdminController extends AdminBase
{

    public function search_admin()
    {


        $this->check_admin(1);

        $account = input('account','','trim');
        $status = input('status','','trim');

        if (empty($account)&&empty($status)) {
            return $this->output_success(200,[],'你不给我参数，那我也不给你数据');
        }

        $where['account'] = ['like','%'.$account.'%'];
        $where['status'] = ['like','%'.$status.'%'];
        $where['is_deleted'] = 0;

        $res = Db::name('admin')->where($where)->field('id,account,password,head_img,info,name,status')->select();
        if (empty($res)) {
            return $this->output_error(500,'没有此管理员');
        }


        return $this->output_success(200,$res,'这些是符合要求的管理员');
    }
}