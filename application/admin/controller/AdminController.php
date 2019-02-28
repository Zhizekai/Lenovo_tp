<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/2/23
 * Time: 16:51
 */

namespace app\admin\controller;

use think\Db;

class AdminController extends AdminBase
{

    public function show_admin_info()
    {

        $page = input('page',0,'intval');


        if (!$page) {
            return $this->output_error(400,'请输入页数');
        }

        $res = Db::name('admin')->page($page,15)->field('name,head_img,info')->select();


        if (!$res) {
            return $this->output_error(404,'无解忧人');
        }else{
            return $this->output_success(200,$res,'name是解忧人的名子,head_img是头像,info是解忧人信息');
        }


    }
}