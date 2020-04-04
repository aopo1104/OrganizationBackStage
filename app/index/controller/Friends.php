<?php
namespace app\index\controller;

use think\Controller;

class Friends extends Controller{
    
    /*
     * 加好友接口，传入fromId：执行加好友操作的id toId:被加好友的id
     * 返回status = 1,friendsArray = 新的好友列表
     */
    public function addFriends(){
        /*
         * 1、将fromId加入toId的friends_array中
         * 2、将toId加入fromId的friends_array中
         */
        if(isset($_POST['fromId'])&&isset($_POST['toId'])){
            $fromId = $_POST['fromId'];
            $toId = $_POST['toId'];
            
            $array_fromId_friends = getFriendsArray($fromId);
            $array_toId_friends = getFriendsArray($toId);
            $newArray_fromId_friends = addNumberInArray($array_fromId_friends,$toId);
            $newArray_toId_friends = addNumberInArray($array_toId_friends,$fromId);
            putFriendsArrayToData($newArray_fromId_friends,$fromId);
            putFriendsArrayToData($newArray_toId_friends,$toId);
            
            $result = array(
                "status"=>1,
                "friendsArray" => $newArray_fromId_friends,
            );
            
            echo json_encode($result); 
        }
    }
    
    /*
     * 删除好友接口，传入fromId：执行删除好友操作的id toId:被删除好友的id
     * 返回status = 1,friendsArray = 新的好友列表
     */
    public function deleteFriends(){
        if(isset($_POST['fromId'])&&isset($_POST['toId'])){
            $fromId = $_POST['fromId'];
            $toId = $_POST['toId'];
            
            $array_fromId_friends = getFriendsArray($fromId);
            $array_toId_friends = getFriendsArray($toId);
            $newArray_fromId_friends = deleteNumberInArray($array_fromId_friends,$toId);
            $newArray_toId_friends = deleteNumberInArray($array_toId_friends,$fromId);
            putFriendsArrayToData($newArray_fromId_friends,$fromId);
            putFriendsArrayToData($newArray_toId_friends,$toId);
            
            $result = array(
                "status"=>1,
                "friendsArray" => $newArray_fromId_friends,
            );
            
            echo json_encode($result);
        }
    }
    
}
