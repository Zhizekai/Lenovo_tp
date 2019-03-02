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
        var_dump('33333');die;
//        $account = input('account','','trim');
//        $status = input('status','','trim');
//
//        $where = ['account'] = ['like','%'.$account.'%'];
//        $where = ['status'] = ['like','%'.$status.'%'];
//
//        $res = Db::name('name')->where()->find();
//
//
//        return $this->output_success(200,$res,'success');
    }
}