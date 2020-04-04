<?php
use app\index\model\userbase;
use app\index\model\organization;

function getOrgMessagebyId($id){
    $userbase = new userbase;
    $organization = new organization;
    
    $res_organization = organization::where("organization_id",$id)
    ->field("organization_name,organization_type,organization_affiliatedUnit,organization_schoolName,organization_star,organization_firstPlace,organization_headPicture")
    ->find();
    $res_userbase = userbase::where("userbase_id",$res_organization['organization_firstPlace'])
    ->field("userbase_name")
    ->find();
    $result = array( "status"=>1,
        "id"=>$id,
        "name"=>$res_organization['organization_name'],
        "type" => $res_organization['organization_type'],
        "affiliatedUnit"=>$res_organization['organization_affiliatedUnit'],
        "schoolName"=>$res_organization['organization_schoolName'],
        "star"=>$res_organization['organization_star'],
        "headPicutre"=>$res_organization['organization_headPicture'],
        "presidentName"=>$res_userbase['userbase_name'],
    );
    return $result;
}