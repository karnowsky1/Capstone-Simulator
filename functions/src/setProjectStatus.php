<?php
/**
 * Created by PhpStorm.
 * User: joselopezjr
 * Date: 12/12/17
 * Time: 12:43 AM
 * Description: This script will call the changeProjectStatus function to insert the status of Grad Student's capstone
 */


if(!isset($_POST['pid']) && !isset($_POST['sid']))
{
    echo json_encode(array('status'=>400,'msg'=>"Require parameter missing!"));
    exit;
}
$sid = htmlentities(trim($_POST['sid']));
$pid = htmlentities(trim($_POST['pid']));



if(empty($pid) || empty($sid))
{
    echo json_encode(array('status'=>400,'msg'=>"Require parameter missing!"));
    exit;
}

require_once('../Database.class.php');
require_once('../Business.class.php');
$db = new Database();
$business = new Business($db);


$res = $business->changeProjectStatus($sid, $pid);

//output the full data in json
if($res instanceof stdClass){
    if($res->status===200){
        echo json_encode(array('status'=>200, 'msg'=>$res->msg));
    }
    else{
        echo json_encode(array('status'=>400, 'msg'=>$res->msg));
    }
}
exit;
