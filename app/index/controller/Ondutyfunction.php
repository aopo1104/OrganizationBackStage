<?php

namespace app\index\controller;
use think\Controller;
Use app\index\model\onduty;

class Ondutyfunction extends Controller{
    
    /*
     * 开始值班签到
     * 传入userbase_id,organization_id,startLongitude,startLatitude
     * 返回 status = 1 , onduty_id
     */
    public function startDuty(){
        
        if(isset($_POST['userbase_id'])&&isset($_POST['organization_id'])&&isset($_POST['startLongitude'])&&isset($_POST['startLatitude'])){
            $startTime = date('Y-m-d H:i:s');
            $res_onduty = onduty::create([
                'userbase_id' => $_POST['userbase_id'],
                'organization_id' => $_POST['organization_id'],
                'onduty_startLongitude' => $_POST['startLongitude'],
                'onduty_startLatitude' => $_POST['startLatitude'],
                'onduty_startTime' => $startTime,
                'onduty_status' => 1
            ],true);
            $result = array(
                "status" => "1",
                "onduty_id" => $res_onduty["onduty_id"]
            );
            echo json_encode($result);
        }
    }
    
    public function endDuty(){
        if(isset($_POST['onduty_id'])&&isset($_POST['endLongitude'])&&isset($_POST['endLatitude'])&&isset($_POST['mood'])&&isset($_POST['report'])){
            $onduty = onduty::get($_POST['onduty_id']);
            $endTime = date('Y-m-d H:i:s');
            $onduty->save([
                'onduty_endLongitude' => $_POST['endLongitude'],
                'onduty_endLatitude' => $_POST['endLatitude'],
                'onduty_endTime' => $endTime,
                'onduty_mood' => $_POST['mood'],
                'onduty_report' => $_POST['report'],
                'onduty_status' => 0
            ]);
            $result = array(
                "status" => "1",
            );
            echo json_encode($result);
        }
    }
}