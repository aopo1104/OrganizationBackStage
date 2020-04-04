<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\userinfo;
use app\index\model\friends;

//通过id查询attention数组,返回的是array
function getFriendsArray($id){  
    $friends_idMessage = friends::get(function($query) use ($id){
        $query->where("userbase_id","eq",$id)
        ->field("friends_array");
    });
    $res = $friends_idMessage['friends_array'];
    $array = explode(",",$res);
    return $array;
}


//把数组转成字符串，放到friends中去，返回的为string
function putFriendsArrayToData($array,$id){
    $string = implode(",", $array);
    $friends_idMessage = friends::get(function($query) use ($id){
        $query->where("userbase_id","eq",$id)
        ->field("friends_array");
    });
    $res = $friends_idMessage->save([
        'friends_array' => $string
    ]);
    return $string;
}


function setStringToArray($string){
    $array = explode(",",$string);
    return $array;
}





