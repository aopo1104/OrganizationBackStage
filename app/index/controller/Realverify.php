<?php

namespace app\index\controller;

use think\Controller;
use app\index\model\userbase;
use app\index\model\userinfo;

/***
 * 实名认证接口
 * 需要传入Id(int),realName(String),sex(String，“男”或“女”),birth(String)
 * 返回：成功 status = 1
 * 失败：status = 2
 */
class Realverify extends Controller{
    
    public function realverify(){
        $userbase = new userbase;
        
        if(isset($_POST['Id'])&&isset($_POST['name'])&&isset($_POST['sex'])&&isset($_POST['birth']))
        {
            if($_POST['sex']=="男")
                $sex = 1;
            else $sex = 0;
            $userbase = userbase::get($_POST['Id']);
            $res = $userbase->save([
                'userbase_name' => $_POST['name'],
                'userbase_sex' => $sex,
                'userbase_birth' =>$_POST['birth'],
                'userbase_isreal' => 1
            ]);
            if($res==1){   //操作都成功
                $result = array("status"=>1);
            }else{
                $result = array("status"=>2);
            }
            echo json_encode($result);
        }
    }
}