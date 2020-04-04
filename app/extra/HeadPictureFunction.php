<?php
use think\Db;
/*
 * 上传头像接口，
 * 传入 uploadFile:头像文件   type:是1:用户头像/2:社团头像 id/3:活动/比赛 图
 * 返回  成功status = 1
 * 失败 status = 2
 */
function changeHeadPicture($facefile,$user_id,$type){

                $id=$user_id;
                $tmp=$facefile["tmp_name"];
                
                $unrnumber=un_repeat_number();
                
                $filepath='D:/WAMP/wamp/www/organization/public/uploads/upload_picture/';
                $imgname=$unrnumber.$facefile["name"];
                
                //echo '文件名:' . $_FILES["uploadFile"]["name"] .'<br />';;
                //echo '类型:' . $_FILES["uploadFile"]["type"] . '<br />';
                // echo '大小:' . ($_FILES["uploadFile"]["size"] / 1024) . 'Kb<br />';
                // echo '存储位置: ' . $_FILES["uploadFile"]["tmp_name"].'<br />';
                // echo dirname(__FILE__);
                
                $db_file='http://127.0.0.1:8080/organization/public/uploads/upload_picture/'.$unrnumber.$facefile["name"];
                
                if(move_uploaded_file($tmp,$filepath.$imgname)){
                    update_info_picture($id,$db_file,$type);
                    return 1;
                    
                }else{
                    return 2;
                    
                }
}

/**
 * 更新头像
 */
function update_info_picture($user_id,$facefile,$type){
    if($type == 1)
        $sql=Db::execute("UPDATE `organization_userbase` SET `userbase_headpicture`='$facefile' WHERE `userbase_id`='$user_id'");
    else if($type == 2)
        $sql=Db::execute("UPDATE `organization_organization` SET `organization_headPicture`='$facefile' WHERE `organization_id`='$user_id'");
    else if($type == 3)
        $sql=Db::execute("UPDATE `organization_activity` SET `activity_picture`='$facefile' WHERE `activity_id`='$user_id'");
}

/**
 * 不重复的id
 * @return [type] [description]
 */
function un_repeat_number(){
    $numbers = range (1,50);
    //shuffle 将数组顺序随即打乱
    shuffle ($numbers);
    //array_slice 取该数组中的某一段
    $num=6;
    $result = array_slice($numbers,0,$num);
    $char = implode("", $result);
    return $char;
    
}
