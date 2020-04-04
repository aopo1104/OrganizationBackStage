<?php
namespace app\index\controller;

use think\Controller;
Use app\index\model\organization;
Use app\index\model\userorg;

class Organizationfunction extends Controller{
    
    /*
     * 创建社团接口
     * 需要传入：orgName,schoolName,affiliatedUnityName,orgType(社团1，组织2),orgStar,createPersonId,uploadFile:头像文件   type:是1:用户头像/2:社团头像 id
     * 返回 成功：status = 1 organization_id
     * 失败：status =2
     */
    public function createOrg(){
        if(isset($_POST['orgName'])&&isset($_POST['schoolName'])&&isset($_POST['affiliatedUnityName'])&&isset($_POST['orgStar'])&&isset($_POST['createPersonId'])&&isset($_POST['orgType'])&&!empty($_FILES["uploadFile"]["tmp_name"]) && isset($_POST['type'])){
            $time = date('Y-m-d H:i:s');
            $res_organization = organization::create([
                'organization_name' => $_POST['orgName'],
                'organization_type' => $_POST['orgType'],
                'organization_affiliatedUnit' => $_POST['affiliatedUnityName'],
                'organization_schoolName' => $_POST['schoolName'],
                'organization_star' => $_POST['orgStar'],
                'organization_firstPlace' => $_POST['createPersonId'],
                'organization_createTime' => $time
            ],true);
            changeheadpicture($_FILES["uploadFile"],$res_organization -> organization_id,2);
            $res_userorg = userorg::create([
                'userbase_id' => $_POST['createPersonId'],
                'organization_id' => ($res_organization -> organization_id),
                'organization_place' => 1
            ]);
            if(($res_organization!=null)){
                $result = array("status" => "1",
                    "organization_id" => ($res_organization -> organization_id)
                );
            }else{
                $result = array("status" => "2");
                }  
                echo json_encode($result);
        }
    }
    
    /*
     * 通过id查找社团信息接口
     * 传入 社团的 id
     * 返回 status = 1 , name,type,affiliatedUnit,schoolName,star,headPicture,residentName
     */
    public function getOrgMessageById(){
        if(isset($_POST['id'])){
            $result = getOrgMessagebyId($_POST['id']);
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
    
}
