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
use think\Request;
use \EasyWeChat\Factory;

vendor('overtrue.wechat.src.Factory');

class UserController extends Base
{

    /**
     *
     * @input token code area years
     * @return array|mixed|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function register()
    {
        //接收数据
        $code = input('code',0,'trim');
        $age = input('age',0,'intval');
        $years = input('years',0,'trim');


        //获取openid openid['openid']才是真正的openid
        $openid = $this->sever($code);
        if (array_key_exists('errmsg',$openid)){
            return $this->output_error(40029,'错误信息'.$openid);
        }
        if (!$age){
            return $this->output_error(10010,'请输入年龄');
        }
        if (!$years){
            return $this->output_error(10010,'请输入年份');
        }


        //检查用户是否已经注册，如果已经注册返回token
        $token_sign = Db::name('user')->where('open_id',$openid['openid'])->value('api_token');
        if ($token_sign) {
            $now_token = $this->token_create($openid['openid']);
            return $this->output_success(401,$now_token,'该用户已注册，请登录，data里的就是token');
        }

        //储存数据
        $res = Db::name('user')->insert([
            'years'=>$years,
            'age'=>$age,
            'open_id'=>$openid['openid']
        ]);

        if (!$res) {
            return $this->output_error(10010,'存储失败');
        }

        //返回token
        $token = $this->token_create($openid['openid']);

        return $this->output_success(200,$token,'注册成功');


    }


    public function sign_in()
    {
        $code = input('code',0,'trim');

        //得到openid
        $openid = $this->sever($code);
        if (array_key_exists('errmsg',$openid)){
            return $this->output_error(40029,'code错误|appkey|appscrect错误');
        }


        //更新token和token过期时间
        $token = $this->token_create($openid['openid']);
        if (empty($token)) {
            return $this->output_error(666,'生成toekn出了点问题');
        }

        //返回登陆信息
        return $this->output_success(200,$token,'登陆成功');
    }



    public function zzk($openid)
    {


        //更新token和token过期时间
        $token = $this->token_create($openid);
        if (empty($token)) {
            return $this->output_error(666,'生成toekn出了点问题');
        }

        //返回登陆信息
        return $this->output_success(200,$token,'登陆成功');
    }

    /**
     * 获取openid
     * @param $code
     * @return mixed
     */
    public function get_openid($code){

        $appid = 'wx98cb9fae6e8b0cca';
        $appsecret = 'b215d069d948ea9c79234a25b1eba9ea';

        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
        $html = file_get_contents($url);
        $vv = (array)json_decode($html);
        return $vv;
    }

    public function sever($code)
    {
        $config = [
            'app_id' => 'wx98cb9fae6e8b0cca',
            'secret' => 'b215d069d948ea9c79234a25b1eba9ea',

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

        ];

        $app = Factory::miniProgram($config);
        $pp = $app->auth->session($code);
        return $pp;
    }

}