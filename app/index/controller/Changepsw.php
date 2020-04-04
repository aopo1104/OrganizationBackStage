<?php
namespace app\index\controller;

use think\Controller;
Use app\index\model\userbase;

class Changepsw extends Controller{
    
    /**
     * 修改密码接口
     * 传入Id，oldPsw，newPsw
     * status = 1 修改成功
     * status = 2 errorCode = 1 ，密码错误
     * status = 2 errorCode = 2，新密码与旧密码一致
     */
    public function ChangePsw(){
        if(isset($_POST['Id']) && isset($_POST['oldPsw']) && isset($_POST['newPsw'])){
                $userbase = userbase::get($_POST['Id']);
                $pswInData = $userbase['userbase_password'];
                if(md5($_POST['oldPsw'])!=$pswInData){
                    $result = array(
                        "status"=>2,
                        "errorCode"=>1
                    );
                }
                else{
                    if($_POST['oldPsw']==$_POST['newPsw']){
                        $result = array(
                            "status"=>2,
                            "errorCode"=>2
                        );
                    }
                    else 
                    {
                        $userbase->save([
                            'userbase_password' => md5($_POST['newPsw'])
                        ]);
                        $result = array(
                            "status"=>1
                        );
                    }
                }
                return json_encode($result);
        }
    }
}