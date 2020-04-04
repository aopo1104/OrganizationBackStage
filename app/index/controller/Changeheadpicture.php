<?php

namespace app\index\controller;
use think\Controller;
use think\Db;
class Changeheadpicture extends Controller{
    /*
     * 上传头像接口，
     * 传入 uploadFile:头像文件   type:是1:用户头像/2:社团头像 id
     * 返回  成功status = 1
     * 失败 status = 2
     */
	public function changeheadpicture(){

		if(empty($_FILES["uploadFile"]["tmp_name"]) && !isset($_POST['id']) && !isset($_POST['type']))
			echo "null file";
		else{
			if ($_FILES["uploadFile"]["error"] > 0)
			  {
			  echo '错误: ' . $_FILES["uploadFile"]["error"] ;
			  }
			else
			  {
			  	$id=$_POST['id'];
			  	// $image = file_get_contents($_FILES['uploadFile']['tmp_name']);
			  	// print_r($image);
			  	$tmp=$_FILES["uploadFile"]["tmp_name"];
			  	
			  	// $filepath= dirname(__FILE__).'/img/';
			  	
			  	$unrnumber=Changeheadpicture::un_repeat_number();

			  	$filepath='D:/WAMP/wamp/www/organization/public/uploads/upload_picture';
			  	$imgname=$unrnumber.$_FILES["uploadFile"]["name"];

			 	//echo '文件名:' . $_FILES["uploadFile"]["name"] .'<br />';;
			 	//echo '类型:' . $_FILES["uploadFile"]["type"] . '<br />';
				// echo '大小:' . ($_FILES["uploadFile"]["size"] / 1024) . 'Kb<br />';
				// echo '存储位置: ' . $_FILES["uploadFile"]["tmp_name"].'<br />';
				// echo dirname(__FILE__);

				$db_file='http://127.0.0.1:8080/organization/public/uploads/upload_picture/'.$unrnumber.$_FILES["uploadFile"]["name"];

				if(move_uploaded_file($tmp,$filepath.$imgname)){					
					Changeheadpicture::update_info_picture($id,$db_file,$_POST['type']);
					$result = array('status' => "1" ,
					'FilePath'=>$db_file);
					echo json_encode($result);

		   		 }else{
		   		 	$result = array('status' => "2" ,
					'FilePath'=>'no filepath');
					echo json_encode($result);

			  	}
				
					
			}
    	}

	}
	/**
	 * 更新头像
	 */
	public function update_info_picture($user_id,$facefile,$type){
	    if($type == 1)
	        $sql=Db::execute("UPDATE `organization_userbase` SET `userbase_headpicture`='$facefile' WHERE `userbase_id`='$user_id'");
		else
	        $sql=Db::execute("UPDATE `organization_organization` SET `organization_headPicture`='$facefile' WHERE `organization_id`='$user_id'");

	}

	/**
	 * 不重复的id
	 * @return [type] [description]
	 */
	public function un_repeat_number(){
		$numbers = range (1,50); 
		//shuffle 将数组顺序随即打乱 
		shuffle ($numbers); 
		//array_slice 取该数组中的某一段 
		$num=6; 
		$result = array_slice($numbers,0,$num); 
		$char = implode("", $result);
		return $char; 

	}
}