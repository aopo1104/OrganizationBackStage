<?php

namespace app\index\controller;

use think\Controller;
Use app\index\model\activity;
Use app\index\model\organization;
Use app\index\model\userbase;

class Activityfunction extends Controller{
    
    /**
     * 创建活动/比赛
     * 传入 organizationId,type(1:活动，2：比赛),personId（发布人id），title，content，picture（文件）
     * 返回 成功： status = 1
     * 失败：status = 2
     */
    public function createActivity(){
        if(isset($_POST['organizationId'])&&isset($_POST['type'])&&isset($_POST['personId'])&&isset($_POST['title'])&&isset($_POST['content'])&&!empty($_FILES["uploadFile"]["tmp_name"])){
            $time = date('Y-m-d H:i:s');
            $res_activity = activity::create([
                'organization_id' => $_POST['organizationId'],
                'userbase_id' => $_POST['personId'],
                'activity_type' => $_POST['type'],
                'activity_title' => $_POST['title'],
                'activity_content' => $_POST['content'],
                'activity_createTime' => $time
            ],true);
            changeheadpicture($_FILES["uploadFile"],$res_activity -> activity_id,3);
            if(($res_activity!=null))
                $result = array("status" => "1");
            else 
                $result = array("status" => "2");
            echo json_encode($result);
            
        }
    }
    
    /*
     * 获取比赛的详细信息，每次获取10条，是哪10条由number来定
     * 传入 number（控制获取哪10条），若为0，则是数据库的最后十条
     * 返回 成功  status = 1 ,id,type,title,content,picture,createTime,organizationName,personName
     * 没有数据可读了 status = 2
     */
    public function getActivityMessage(){
        if(isset($_POST['number'])){
             $activity = new activity();
             $userbase = new userbase;
             $organization = new organization;
             $number = $_POST['number'];
             
             $activity = $activity::all(function($query){
                 $query
                 ->field("activity_id,organization_id,userbase_id,activity_type,activity_title,activity_content,activity_picture,activity_createTime");
             });
             
             $last = count($activity)-1;
             $left = $last - ($number+1)*10;   //读取"到"这一位
             $right = $last - ($number)*10;    //"从"这一位开始读取
             if($right <= 0)    //表示没有可读的数据
                 $result = array(
                     'status' => 2
                 );
             else{
                 if($left <= 0){    //表示没有10条数据可以读
                    $left = 0;
                 }
             
             
             $message = array();
             for($temp = $right; $temp > $left; $temp-- ){
                 $organization_id = $activity[$temp]["organization_id"];
                 $userbase_id = $activity[$temp]['userbase_id'];
                 $res_userbase = $userbase::where("userbase_id",$userbase_id)
                 ->field("userbase_name")
                 ->find();
                 $res_organization = $organization::where("organization_id",$organization_id)
                 ->field("organization_name")
                 ->find();
                 
                 $message[] = array(
                     'id' => $activity[$temp]["organization_id"],
                     'type' => $activity[$temp]["activity_type"],
                     'title' => $activity[$temp]["activity_title"],
                     'content' => $activity[$temp]["activity_content"],
                     'picture' => $activity[$temp]["activity_picture"],
                     'createTime' => $activity[$temp]["activity_createTime"],
                     'organizationName' => $res_organization["organization_name"],
                     'personName' => $res_userbase["userbase_name"]
                 );
             }
            $result = array(
                'status' => 1,
                'message'=> $message
            );    
        }
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
}