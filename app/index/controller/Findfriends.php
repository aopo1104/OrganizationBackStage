<?php
namespace app\index\controller;

use think\Controller;
Use app\index\model\userbase;
Use app\index\model\userinfo;
use think\Db;

class Findfriends extends Controller{
    
    /*
     * 通过手机号查找接口
     * status = 2 未查询到该用户
     * status = 1 成功查询 返回返回status,id,name,phoneNumber,sex,email,school,academy,class,
     * studentid,birth,headpiture,isreal,
     * 名为organizationMessage的json（数组，每一项里有（organizationId(社团编号),organizationName(社团姓名),organizationPlace（该人所在的社团地位）））
     */
    public function findByPhoneNumber(){
        $userbase = new userbase;
        if(isset($_POST['phoneNumber'])){
            $res = $userbase::get(function($query)
            {
                $query->where("userbase_phonenumber","eq",$_POST['phoneNumber']);
            });
            if($res==null)
            {
                $result = array("status"=>2);
            }else{
                $result = getMessageByPhoneNumber($_POST['phoneNumber']);
            }
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
    
    /*
     * 通过昵称查找接口
     * status = 2 未查询到该用户
     * status = 1 成功查询 返回status,id,name,phoneNumber,sex,email,school,academy,class,
     * studentid,birth,headpiture,isreal,
     * 名为organizationMessage的json（数组，每一项里有（organizationId(社团编号),organizationName(社团姓名),organizationPlace（该人所在的社团地位）））
     */
    public function findByName(){
        $userbase = new userbase;
        if(isset($_POST['name'])){
            $res = $userbase::get(function($query)
            {
                $query->where("userbase_name","eq",$_POST['name']);
            });
            if($res==null)
            {
                $result = array("status"=>2);
            }else{
                $result = getMessageByName($_POST['name']);
            }
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
    
    /*
     * 通过id号查找接口
     * status = 2 未查询到该用户
     * status = 1 成功查询 返回status,id,name,phoneNumber,sex,email,school,academy,class,
     * studentid,birth,headpiture,isreal,
     * 名为organizationMessage的json（数组，每一项里有（organizationId(社团编号),organizationName(社团姓名),organizationPlace（该人所在的社团地位）））
     */
    public function findById(){
        $userbase = new userbase;
        if(isset($_POST['id'])){
            $res = $userbase::get(function($query)
            {
                $query->where("userbase_id","eq",$_POST['id']);
            });
            if($res==null)
            {
                $result = array("status"=>2);
            }else{
                $result = getMessageById($_POST['id']);
            }
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
    
    
}