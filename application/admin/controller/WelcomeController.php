<?php
/**
 * Created by PhpStorm.
 * User: 10551
 * Date: 2019/3/2
 * Time: 9:21
 */

namespace app\admin\controller;


use think\Db;

class WelcomeController extends AdminBase
{

    public function change () {

        $this->check_admin(1);

        $text = input('text','','trim');

        $res = Db::name('welcome')->where('id',1)->update(['text'=>$text]);

        if ($res) {
            return $this->output_success(10000,'','修改欢迎语成功');
        } else {
            return $this->output_error(10010,'修改欢迎语失败');
        }

    }

}