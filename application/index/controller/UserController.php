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
        }else{
            return $this->output_success(200,[],'信息录入成功');
        }

    }


    /**
     *
     * input
     * @code
     * @token
     * @area
     * @years
     *
     * @return array|mixed|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function register()
    {
        $code = input('code',0,'trim');
        $area = input('area',0,'trim');
        $years = input('years',0,'trim');
        $name = input('name','','trim');

//
//        $token = input('token',0,'trim');
//        $uid = $this->lenovo_getuid($token);
        $openid = $this->get_openid($code);
        if (array_key_exists('errmsg',$openid)){
            return $this->output_error(40029,'code错误|appkey|appscrect错误');
        }
        if (!$area){
            return $this->output_error(10010,'请输入地区');
        }
        if (!$years){
            return $this->output_error(10010,'请输入年份');
        }
        if (!$name) {
            return $this->output_error(10010,'请输入昵称');
        }

        $area_id = Db::name('area')->where(['area'=>$area])->value('id');
        if (!$area_id) {
            return $this->output_error(10010,'请输入正确的地理位置');
        }
        $res = Db::name('user')->insert(['years'=>$years,'area_id'=>$area_id,'openid'=>$openid]);

        if (!$res) {
            return $this->output_error(10010,'存储失败');
        }

        $token = $this->token_create($openid);

        return $this->output_success(200,$token,'登陆成功');


    }



    public function zzk()
    {
        $token = $this->token_create(5);
        return $this->output_success(200,$token,'登陆成功');
    }

    /**
     * 获取openid
     * @param $code
     * @return mixed
     */
    public function get_openid($code){

        $appid = 'wx1ade6e6d6254487c';
        $appsecret = '43af9a084d357afb06f78b1abd9d4f90';
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
        $html = file_get_contents($url);
        $vv = (array)json_decode($html);
        return $vv;
    }

}