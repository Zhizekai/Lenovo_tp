<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/2/23
 * Time: 12:21
 */

namespace app\index\controller;


use think\Db;
use app\index\controller\Base;

class UserController extends Base
{

    /**
     * 获取用户信息
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function info()
    {
        $token = input('token',0,'trim');
        $uid = $this->lenovo_getuid($token);


        $area = input('area',0,'trim');
        $years = input('ysers',0,'trim');

        if (!$token){
        return $this->output_error(10010,'请输入token');
        }
        if (!$area){
            return $this->output_error(10010,'请输入token');
        }
        if (!$years){
            return $this->output_error(10010,'请输入token');
        }

        $area_res = Db::name('area')->insert(['user_id'=>$uid,'area'=>$area]);
        if (!$area_res) {
            return $this->output_error(10010,'地区录入错误');
        }

        $years_res = Db::name('user')->where(['id'=>$uid])->update(['years'=>$years]);

        if (!$years_res) {
            return $this->output_error(10010,'年份录入错误');
        }

    }

}