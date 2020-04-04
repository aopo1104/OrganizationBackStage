<?php
namespace app\index\controller;

Use app\index\model\userbase;
Use app\index\model\friends;
Use app\index\model\userorg;
Use app\index\model\organization;
use think\Controller;
use think\Db;

ini_set('display_errors','on');
error_reporting(E_ALL);

class Loginregister extends Controller
{
    /*
     * 登录接口，
     * status=1:登录成功 返回status,id,name,phoneNumber,sex,email,school,academy,class,
     * studentid,birth,headpiture,isreal,friendsArray(String),
     * 名为organizationMessage的json（数组，每一项里有（organizationId(社团编号),organizationName(社团姓名),organizationPlace（该人所在的社团地位）））
     * status=2：登录失败，返回errorCode
     *      errorCode=1：帐号不存在 errorCode=2：密码错误
     */
     public function login()
    {
        $loginTime = date('Y-m-d H:i:s');
        
        $userbase = new userbase;
        if(isset($_POST['phoneNumber'])&&isset($_POST['password']))
        {
            $res = $userbase::get(function($query)
            {
                $query->where("userbase_phonenumber","eq",$_POST['phoneNumber']);
            });
            if($res==null)
            {
                $result = array("status"=>2,"errorCode"=>1);
            }
            else
            {
                $password=$res->toArray()['userbase_password'];
                if($password==md5($_POST['password']))  //登录成功后的步骤，获取昵称，电子邮箱，性别，是否实名认证，真名等信息
                {
                    
                    $res = $userbase::where("userbase_phonenumber","eq",$_POST['phoneNumber'])  //更新登录时间
                    ->update([
                        'userbase_loginTime' => $loginTime
                    ]);
                    $result = getMessageByPhoneNumber($_POST['phoneNumber']);
                }
                else
                {
                    $result = array("status"=>2,"errorCode"=>2);
                }
            }
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    } 
    
    /*
     * 注册接口
     * status = 1 成功
     * status = 2 ，errorCode = 1  userPhoneNumber 重复
     * status = 2 ，errorCode = 2  添加到数据库失败
     */
    public function register()
    {
        //global $userName,$userSex,$userPhoneNumber,$userPassword;
        // echo $_POST['userName'];
        if(isset($_POST['name'])&&isset($_POST['sex'])&&isset($_POST['phoneNumber'])&&isset($_POST['password'])&&isset($_POST['studentId'])&&isset($_POST['school'])&&isset($_POST['academy'])&&isset($_POST['class'])){
            $userName=$_POST['name'];
            $userSex=$_POST['sex'];
            $userPhoneNumber=$_POST['phoneNumber'];
            $userPassword=md5($_POST['password']);
            $userStudentId = $_POST['studentId'];
            $userLoginTime=date('Y-m-d H:i:s');
            $userSchool = $_POST['school'];
            $userAcademy = $_POST['academy'];
            $userClass = $_POST['class'];
            
            
            /**
             * 判断userPhoneNumber是否重复
             */
            if(Loginregister::ifExistsUserPhoneNumber($userPhoneNumber)==1){
                
                $return_ExistedUserPhoneNumber=$userPhoneNumber;
                $result = array('status' => 2
                    ,'errorCode' => 1);
                echo json_encode($result);
                return;
            }
            
           /*  //数据库表中的行数来设置id号
            $sql=Db::query("select * from `organization_userbase`");
            $mysqlLenth=1;
            foreach ($sql as $n) {
                # code...
                $mysqlLenth=$mysqlLenth+1;
            } */
            
            //判断性别的男女
            if(strcmp($userSex, "男"))
                $sex='1';
                else if(strcmp($userSex, "女"))
                    $sex='2';
                    else return;
                    
          
            //$res_in=Db::execute("INSERT INTO `organization_userbase` 
            //    ( `userbase_name`,`userbase_password`, `userbase_phoneNumber`, `userbase_school`, `userbase_academy`, `userbase_class` ,`userbase_sex`,`userbase_studentId`,`userbase_createTime`, `userbase_loginTime`, `userbase_lastLoginTime`, `userbase_loginCount`)
            //     VALUES ( '$userName','$userPassword', '$userPhoneNumber','$userSchool','$userAcademy','$userClass','$sex','$userStudentId','$userLoginTime', '$userLoginTime', '$userLoginTime', '1')");
            //$res_in = Db::name
            //dump($res_in);
            //$userbase_id = mysqli_insert_id($res_in);
            //$res_in2=Db::execute("INSERT INTO `organization_friends`(`userbase_id`) VALUES ('$userbase_id')");
            
            $res_in = userbase::create([
                'userbase_name' => $userName,
                'userbase_password' => $userPassword,
                'userbase_phoneNumber' => $userPhoneNumber,
                'userbase_school' => $userSchool,
                'userbase_academy' => $userAcademy,
                'userbase_class' => $userClass,
                'userbase_sex' => $sex,
                'userbase_studentid' => $userStudentId,
                'userbase_createTime' => $userLoginTime,
                'userbase_loginTime' => $userLoginTime,
                'userbase_lastLoginTime' => $userLoginTime,
                'userbase_loginCount' => 1
            ],true);
            
            $res_friends = friends::create([
                'userbase_id' => ($res_in->userbase_id)
            ],true);
            
            $result = array('status' => "1" );
            echo json_encode($result);
        }
    }
    
    
    
    
    // 判断数据库表中是否存在用户名元素
    public function ifExistsUserName($userName=''){
        
        $sql=Db::query("select * from `organization_userbase` where `userbase_name`='$userName'");
        if($sql==null){
            return 0;
        }else return 1;
    }
    
    // 判断数据库表中是否存在手机号元素
    public function ifExistsUserPhoneNumber($userPhoneNumber=''){
        
        $sql=Db::query("select * from `organization_userbase` where `userbase_phoneNumber`='$userPhoneNumber'");
        if($sql==null){
            return 0;
        }else return 1;
    }
    
}
