<?php
//把一个数字放进一个从小到大排列的数组中
function addNumberInArray($array,$number){
    
    for($temp=sizeof($array)-1;$temp>=0;$temp--){
        if($number<$array[$temp]){
            $array[$temp+1] = $array[$temp];
            if($temp==0)
                $array[$temp]=$number;
        }else{
            $array[$temp+1] = $number;
            break;
        }
    }
    return $array;
}

//把一个数字从 从小到大 排列的数组中删去
function deleteNumberInArray($array,$number){
    $judge=0;   //判断有没有遇到那个数字
    for($temp=0;$temp<sizeof($array);$temp++){
        if($array[$temp]==$number){
            $judge=1;
            continue;
        }
        if($judge==1){
            $array[$temp-1]=$array[$temp];
        }
    }
    return array_slice($array, 0,sizeof($array)-1);
}

//判断一个数组中有没有一个数,若有则返回1，若没有则返回0
function isNumberInArray($array,$number){
    for($temp=0;$temp<sizeof($array);$temp++){
        if($number == $array[$temp]){
            return 1;
        }
    }
    return 0;
}

//判断一个用","分隔的字符串中有没有一个数，若有则返回1，若没有则返回0
function isNumberInString($string,$number){
    $array = explode(",",$string);
    $judge = isNumberInArray($array, $number);
    return $judge;
}

//输出数组，测试用
function echoArray($array){
    for($temp=0;$temp<sizeof($array);$temp++){
        echo $array[$temp]+" ";
    }
}