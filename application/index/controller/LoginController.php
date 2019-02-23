<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/2/23
 * Time: 15:21
 */

namespace app\index\controller;


use think\Db;

class LoginController extends Base
{

    /**
     * 登陆接口
     * @return array|string
     */
    public function login()
    {
        $code = input('code',0,'trim');

        $openid = $this->get_openid($code);

        if (!$openid){
            return $this->output_error(100,'code错误');
        }

        $res = Db::name('user')->insert(['openid'=>$openid]);

        if (!$res) {
            return $this->output_error(400,'opendi存储失败');
        }

        $uid = Db::name('user')->where(['openid'=>$openid])->value('id');

        $token = $this->token_create($uid);

        return $token;


    }



    public function ww(){
        echo 'sfsffsffs';}

    /**
     * 获取openid
     * @param $code
     * @return mixed
     */
    protected function get_openid($code){
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wx1ade6e6d6254487c&secret=43af9a084d357afb06f78b1abd9d4f90&js_code='.$code.'&grant_type=authorization_code';
        $html = file_get_contents($url);
        $vv = (array)json_decode($html);
        return $vv['openid'];
    }
}