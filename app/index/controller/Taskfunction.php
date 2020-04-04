<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\userbase;
use app\index\model\task;
use app\index\model\primarytask;
use app\index\model\userorg;
use app\index\model\organization;
use app\index\model\usertask;

class Taskfunction extends Controller{

    public function taskPublish(){
        if(isset($_POST['organizationId'])&&isset($_POST['personId'])&&isset($_POST['title'])&&isset($_POST['content'])&&isset($_POST['startYear'])
            &&isset($_POST['startMonth'])&&isset($_POST['startDay'])&&isset($_POST['endYear'])&&isset($_POST['endMonth'])&&isset($_POST['endDay'])
            &&isset($_POST['canSeePlace1'])&&isset($_POST['canSeePlace2'])&&isset($_POST['canSeePlace3'])&&isset($_POST['canSeePlace4'])){
                $startDate = $_POST['startYear']."-".$_POST['startMonth']."-".$_POST['startDay'];
                $endDate = $_POST['endYear']."-".$_POST['endMonth']."-".$_POST['endDay'];
                $canSeePlace = array(0);
                if($_POST['canSeePlace1'] == 1)
                    $canSeePlace = addNumberInArray($canSeePlace,1);
                if($_POST['canSeePlace2'] == 1)
                    $canSeePlace = addNumberInArray($canSeePlace,2);
                if($_POST['canSeePlace3'] == 1)
                    $canSeePlace = addNumberInArray($canSeePlace,3);
                if($_POST['canSeePlace4'] == 1)
                    $canSeePlace = addNumberInArray($canSeePlace,4);
                    $canSeePlaceString = $string = implode(",", $canSeePlace);
                $res_task = task::create([
                    'userbase_id' => $_POST['personId'],
                    'organization_id' => $_POST['organizationId'],
                    'task_title' => $_POST['title'],
                    'task_content' => $_POST['content'],
                    'task_startTime' => $startDate,
                    'task_endTime' => $endDate,
                    'task_canSeePlace' => $canSeePlaceString
                ],true);
                $result = array("status" => "1");
                echo json_encode($result);
        }
    }
    
    public function primarytaskCreate(){
        if(isset($_POST['personId'])&&isset($_POST['title'])&&isset($_POST['content'])&&isset($_POST['startYear'])
            &&isset($_POST['startMonth'])&&isset($_POST['startDay'])&&isset($_POST['endYear'])&&isset($_POST['endMonth'])&&isset($_POST['endDay'])){
                $startDate = $_POST['startYear']."-".$_POST['startMonth']."-".$_POST['startDay'];
                $endDate = $_POST['endYear']."-".$_POST['endMonth']."-".$_POST['endDay'];
                $res_primarytask = primarytask::create([
                    'userbase_id' => $_POST['personId'],
                    'primarytask_title' => $_POST['title'],
                    'primarytask_content' => $_POST['content'],
                    'primarytask_startTime' => $startDate,
                    'primarytask_endTime' => $endDate,
                ],true);
                $result = array("status" => "1");
                echo json_encode($result);
        }
    }
    
    /**
     * 查找任务接口，包括社团任务(task中的数据)和个人任务(primaryTask中的数据)
     * 传入用户id("id")
     * 输出 type(类型，1为社团任务 2为个人任务),orgName(若type==1则没有),title,content,startTime,endTime的多个json字符串
     */
    public function getTaskMessage(){
         if(isset($_POST['id'])){
            $task = new task;
            $primarytask = new primarytask;
            $usertask = new usertask;
            $userorg = new userorg;
            $organization = new organization;
            
            $res_userorg = $userorg::where("userbase_id",$_POST['id'])
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
         $taskArray = array();
         foreach ($orgArray as $orgMessage){
            $res_task = $task::where("organization_id",$orgMessage['organizationId'])
                ->field("task_id,task_title,task_content,task_startTime,task_endTime,task_canSeePlace")
                ->select();
            foreach ($res_task as $res_singletask){
                $canSeePlaceArray = explode(",",$res_singletask['task_canSeePlace']);
                if(isNumberInArray($canSeePlaceArray, $orgMessage['organizationPlace']) == 1){
                    $task_id = $res_singletask['task_id'];   //判断有没有阅读过，判断依据为 先判断usertask中表有无任务和用户id都对得上的
                    $res_usertask = $usertask::where("task_id",$task_id)
                                ->where("userbase_id",$_POST['id'])
                                ->field("usertask_isread,usertask_isreport,usertask_report")
                                ->find();
                   if($res_usertask == null){   //如果为空，则代表在usertask中没有task_id和userbase_id都对得上的记录，则没有阅读过
                       $isread = 0;
                       $isreport = 0;
                       $report = null;
                       usertask::create([   //创建一条记录，isread默认为0
                           'task_id' => $res_singletask['task_id'],
                           'userbase_id' => $_POST['id'],
                       ]);
                   }else{
                       $isread = $res_usertask['usertask_isread'];
                       $isreport = $res_usertask['usertask_isreport'];
                       $report = $res_usertask['usertask_report'];
                   }
                       
                    $taskArray[] = array(
                        'type' => 1,
                        'id' => $res_singletask['task_id'],
                        'title' => $res_singletask['task_title'],
                        'content' => $res_singletask['task_content'],
                        'startTime' => $res_singletask['task_startTime'],
                        'endTime' => $res_singletask['task_endTime'],
                        'orgName' => $orgMessage['organizationName'],
                        'isread' => $isread,
                        'isreport' => $isreport,
                        'report' => $report
                    );
                 }
                }
            }
            
            $res_primarytask = $primarytask::where("userbase_id",$_POST['id'])
            ->field("primarytask_id,primarytask_title,primarytask_content,primarytask_startTime,primarytask_endTime")
            ->select();
            foreach($res_primarytask as $res_singleprimarytask){
                $taskArray[] = array(
                    'type' => 2,
                    'id' => $res_singleprimarytask['primarytask_id'],
                    'title' => $res_singleprimarytask['primarytask_title'],
                    'content' => $res_singleprimarytask['primarytask_content'],
                    'startTime' => $res_singleprimarytask['primarytask_startTime'],
                    'endTime' => $res_singleprimarytask['primarytask_endTime'],
                );    
            }
            $result = array(
                'message' => $taskArray
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
         }
    }
    
    /**
     * 阅读任务接口，将isread 从 0 改为 1
     * 传入用户id('userbase_id')和任务id('task_id)
     */
    public function taskRead(){
        if(isset($_POST['userbase_id'])&&isset($_POST['task_id'])){
            $res_usertask = usertask::where("task_id",$_POST['task_id'])
            ->where("userbase_id",$_POST['userbase_id'])
            ->field("usertask_isread")
            ->find();
            $res_usertask->save([
                'usertask_isread' => 1
            ]);
        }
    }
    
    public function taskReport(){
        if(isset($_POST['userbase_id'])&&isset($_POST['task_id'])&&isset($_POST['report'])){
            $res_usertask = usertask::where("task_id",$_POST['task_id'])
            ->where("userbase_id",$_POST['userbase_id'])
            ->field("usertask_isreport,usertask_report")
            ->find();
            $res_usertask->save([
                'usertask_isreport' => 1,
                'usertask_report' => $_POST['report']
            ]);
        }
    }
    
    /**
     * 通过发布者Id查找任务信息接口
     * 输入发布者Id（userbase_id）
     * 返回id,title,content,startTime,endTime
     */
    public function getTaskMessageByPublishPersonId(){
        if(isset($_POST['userbase_id'])){
            $res_task = task::where("userbase_id",$_POST['userbase_id'])
                ->field("task_id,task_title,task_content,task_startTime,task_endTime")
                ->select();
            $task = array();
            foreach ($res_task as $res_singletask){
                $task[] = array(
                    'id' => $res_singletask["task_id"],
                    'title' => $res_singletask["task_title"],
                    'content' => $res_singletask["task_content"],
                    'startTime' => $res_singletask["task_startTime"],
                    'endTime' => $res_singletask["task_endTime"]
                );
            }
            $result = array(
               'message' => $task
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 查找任务的接收人的情况
     * 输入任务id(task_id)
     */
    public function getTaskMember(){
        if(isset($_POST['task_id'])){
            $res_task = task::where("task_id",$_POST['task_id'])
                ->field("organization_id,task_canSeePlace")
                ->select();
            $memberMessage = array();
            foreach($res_task as $res_singletask){
                $res_userorg = userorg::where("organization_id",$res_singletask['organization_id'])
                            -> field("userbase_id , organization_place")
                            -> select();
                    foreach($res_userorg as $res_singleuserorg){
                    if(isNumberInString($res_singletask['task_canSeePlace'], $res_singleuserorg['organization_place']) == 1){
                        $id = $res_singleuserorg['userbase_id'];
                        $res_usertask = usertask::where("userbase_id",$id)
                                ->where("task_id",$_POST['task_id'])
                                ->field("usertask_isread,usertask_isreport,usertask_report")
                                ->find();
                        if($res_usertask == null){  //没有记录，表示没有看过
                            $isread = 0;
                            $isreport = 0;
                            $report = null;
                        }else{
                            $isread = $res_usertask['usertask_isread'];
                            $isreport = $res_usertask['usertask_isreport'];
                            $report = $res_usertask['usertask_report'];
                        }
                        $res_userbase = userbase::where("userbase_id",$id)
                                ->field("userbase_name")
                                ->find();
                         $name = $res_userbase['userbase_name'];
                        $memberMessage[] = array(
                            'id' => $id,
                            'name' => $name,
                            'isread' => $isread,
                            'isreport' => $isreport,
                            'report' => $report
                        );  
                    }
                }
            }
            $result = array(
                'message' => $memberMessage
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
}