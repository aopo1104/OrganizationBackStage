<?php

namespace app\index\controller;

use think\Controller;
Use app\index\model\userbase;
Use app\index\model\userorg;
Use app\index\model\organization;
Use app\index\model\friends;
use think\Db;


//通过手机号获取信息
function getMessageByPhoneNumber($phoneNumber){
    $userbase = new userbase;
    $res_userbase = $userbase::get(function($query) use($phoneNumber){
        $query -> where ("userbase_phoneNumber","eq",$phoneNumber)
        -> field("userbase_id");
    });
        $id = $res_userbase['userbase_id']; 
        $result = getMessageById($id);
        return $result;
}

//通过名字获取信息
 function getMessageByName($name){
    $userbase = new userbase;
    $userbase = new userbase;
    $res_userbase = $userbase::get(function($query) use($phoneNumber){
        $query -> where ("userbase_name","eq",$name)
        -> field("userbase_id");
    });
        $id = $res_userbase['userbase_id'];
        $result = getMessageById($id);
        return $result;
}
 
//通过id获取信息
function getMessageById($id){
    $userbase = new userbase;
    $userorg = new userorg;
    $organization = new organization;
    $friends = new friends;
    //获取好友列表
    $res_friends = $friends::where("userbase_id",$id)
    ->field("friends_array")
    ->find();
    $res_userbase = $userbase::where("userbase_id",$id)
    ->field("userbase_name,userbase_phoneNumber,userbase_sex,userbase_email,userbase_school,userbase_academy,userbase_class,userbase_studentid,userbase_birth,userbase_headpicture,userbase_isreal")
    ->find();
    //通过userbase_id获取加入社团信息
    $res_userorg = $userorg::where("userbase_id",$id)
    ->field("organization_id,organization_place")
    ->select();
    $orgArray = array();
    //通过社团id获取社团名字
    foreach ($res_userorg as $res){
        $res_organization = $organization::where("organization_id",$res['organization_id'])
        ->field("organization_name")
        ->find();
        $orgName = $res_organization['organization_name'];
        $orgArray[] =  array(
            'organizationId' => $res['organization_id'],
            'organizationName' => $orgName,
            'organizationPlace' => $res['organization_place']
        );
    }
        
    $result = array( "status"=>1,
        "id"=>$id,
        "name"=>$res_userbase['userbase_name'],
        "phoneNumber" => $res_userbase['userbase_phoneNumber'],
        "sex"=>$res_userbase['userbase_sex'],
        "email"=>$res_userbase['userbase_email'],
        "school"=>$res_userbase['userbase_school'],
        "academy"=>$res_userbase['userbase_academy'],
        "class"=>$res_userbase['userbase_class'],
        'studentid' =>$res_userbase['userbase_studentid'],
        'birth' =>$res_userbase['userbase_birth'],
        'headpicture' =>$res_userbase['userbase_headpicture'],
        'isreal' => $res_userbase['userbase_isreal'],
        'friendsArray' => $res_friends['friends_array'],
        'organizationMessage' => $orgArray  
    );
    return $result;
}