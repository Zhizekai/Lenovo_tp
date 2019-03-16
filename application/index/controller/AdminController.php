<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/2/23
 * Time: 16:51
 */

namespace app\index\controller;

use think\Db;

class AdminController extends Base
{

    //随机获取五个解忧人信息
    public function show_admin_info()
    {

        //随机从数据库里获取五个管理员信息
        $max = Db::name('admin')->count();
        if ($max < 5){
            $res = Db::name('admin')->field('name,head_img,info')->select();
            return $this->output_success(200,$res,'name是解忧人的名子,head_img是头像,info是解忧人信息');
        }
        $rand = rand(0,$max-5);
        $res = Db::name('admin')->limit($rand,5)->field('name,head_img,info')->select();
        if (!$res) {
            return $this->output_error(404,'无解忧人');
        }else{
            return $this->output_success(200,$res,'name是解忧人的名子,head_img是头像,info是解忧人信息');
        }


    }
}