<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/3/2
 * Time: 15:39
 */

namespace app\index\controller;




class PvController extends Base
{

    public function get_pv()
    {
        $start_day = input('start_day/d');
        $end_day = input('end_day/d');
        $all_pv = 0;
        for ($i = $start_day;$i <= $end_day;$i++) {
            $p = exec('/root/lwh.sh'.' '.$i,$pv);
            $all_pv += (int)$p;
        }

        var_dump($all_pv);
    }
}