<?php
/**
 * Created by PhpStorm.
 * User: 10551
 * Date: 2019/3/1
 * Time: 23:04
 */

namespace app\index\controller;


use think\Db;

class WelcomeController extends Base
{

    public function index () {

        $token = !empty($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        if (is_null($token)) {
            $res = Db::name('welcome')
                ->field('text')
                ->where('is_deleted',0)
                ->find();

            if ($res) {
                return $this->output_success(10000,$res,'返回欢迎语成功');
            } else {
                return $this->output_error(10010,'返回欢迎语失败');
            }
        }


    }

}