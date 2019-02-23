<?php
/**
 * Created by PhpStorm.
 * User: 10551
 * Date: 2019/2/23
 * Time: 15:35
 */

namespace app\index\controller;


use think\Db;

class AreaController extends Base
{
    public function index () {
        $res = Db::name('area')->where(['is_deleted'=>0])->select();
        if ($res) {
            return $this->output_success(10010,$res,'获取地区成功');
        } else {
            return $this->output_error(10000,'获取地区失败');
        }
    }

}