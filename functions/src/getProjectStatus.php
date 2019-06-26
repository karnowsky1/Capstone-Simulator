<?php
/**
 * Created by PhpStorm.
 * User: joselopezjr
 * Date: 12/12/17
 * Time: 12:43 AM
 * Description: This script will call the getGradCapstoneStatus function to fetch the status of Grad Student's capstone
 */


if(!isset($_POST['pid']))
{
    echo json_encode(array('status'=>400,'msg'=>"Require parameter missing!"));
    exit;
}

$pid = htmlentities(trim($_POST['pid']));

if(empty($pid))
{
   echo json_encode(array('status'=>400,'msg'=>"Require parameter missing!"));
   exit;
}

require_once('../Database.class.php');
require_once('../Business.class.php');
$db = new Database();
$business = new Business($db);


$res = $business->getGradCapstoneStatus($pid);

if($res===false)
{
    echo json_encode(array('status'=>400,'msg'=>'We were not able to find any data for the pid '.$pid));
    exit;
}

$rows = array();
while($r = $res->fetchAll(PDO::FETCH_ASSOC)) {
    $rows["item"] = $r;
}

//output the full data in json
echo json_encode(array('status'=>200,'msg'=>json_encode($rows)));
exit;
