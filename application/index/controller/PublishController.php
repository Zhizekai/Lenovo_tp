<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/2/23
 * Time: 17:21
 */

namespace app\index\controller;


use think\Db;

class PublishController extends Base
{

    public function publish()
    {
        $uid = $this->lenovo_getuid(input('token'));

        $question = input('question','','tirm');

        $res = Db::name('question')->insert([
            'user_id'=>$uid,
            'question'=>$question,
        ]);

        if (!$res) {
            return $this->output_error(404,'发送失败');
        }else{
            return $this->output_success(200,[],'发送成功');
        }
    }
}