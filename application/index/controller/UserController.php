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
        $code = input('code',0,'trim');
        $area = input('area',0,'trim');
        $years = input('years',0,'trim');
        $name = input('name','','trim');

        //获取openid
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

        //检查用户是否已经注册
        $res_openid = Db::name('user')->where('openid',$openid)->value('id');
        if ($res_openid) {
            return $this->output_error(400,'该用户已注册，请登录');
        }

        //判断是否输入其他地点
        $area_id = Db::name('area')->where(['area'=>$area])->value('id');
        if (!$area_id) {
            return $this->output_error(10010,'请输入正确的地理位置');
        }

        //储存数据
        $res = Db::name('user')->insert(['years'=>$years,'area_id'=>$area_id,'open_id'=>$openid['openid']]);

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
        $openid = $this->get_openid($code);
        if (array_key_exists('errmsg',$openid)){
            return $this->output_error(40029,'code错误|appkey|appscrect错误');
        }


        //更新token和token过期时间
        $token = $this->token_create($openid);
        $res = Db::name('user')->where('open_id',$openid)->update(['token'=>$token,'api_token_expire'=>time()]);
        if (empty($res)){
            return $this->output_error(40029,'登陆失败');
        }

        //返回登陆信息
        return $this->output_success(200,$token,'登陆成功');
    }



    public function zzk()
    {
        //从http头里取token
        $request = Request::instance()->header();


        if (!array_key_exists('authorization',$request)) {
            return $this->output_error(400,'请把token放在http请求头里面');
        }
        $token = explode(' ',$request['authorization']);

        $token = $token[1];
            var_dump($token);
//        $baiduUrl = "http://www.baidu.com/link?url=LZE_J6a1AcieLlTzNxUZQVpe2trQ99zx1ls85ux8dXaGlFB3eiEm_Y6SJC1sNQf_";
//
//        file_get_contents($baiduUrl);
//
//        dump($http_response_header);
        //dump($this->token_create('333333'));
        //dump(rand(10,100));
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

}