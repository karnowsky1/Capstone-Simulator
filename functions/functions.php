<?php
include_once ("functions/Business.class.php");
$db = new Database();
$bus = new Business($db);

/**
 * This function will redirect to the login page
 */
function redirectToLogin()
{
    header("Location:index.php");
}

/**
 * This function will check the every required sessions
 */
function protect_page(){

    $allowed = array("Grad","Faculty","Staff");
    $required_session = array("type","uid","name","email");

    //no session
    if(!isset($_SESSION))
    {
        redirectToLogin();
    }

    //have all required session?
    foreach($required_session as $session)
    {
        //not set or empty
        if(!isset($_SESSION[$session]) || empty(trim($_SESSION[$session])))
        {
            redirectToLogin();
        }
    }

    //allowed user?
    if(!in_array(trim($_SESSION['type']),$allowed))
    {
        redirectToLogin();
    }
}


/**
 * This function will redirect to the user's authorization page
 */
function authorization_page(){
    
    if($_SESSION["type"] == "Grad"){
        header("Location: grad.php"); //Redirecting to the UI for Grad
    }
    else if($_SESSION["type"] == "Faculty"){
        header("Location: faculty.php"); //Redirecting to the UI for Faculty
    }
    else if($_SESSION["type"] == "Staff"){
        header("Location: staff.php"); //Redirecting to the UI for Staff
    }
}


/**
 * This function will call the login function from Business class to validate the username and password
 * @param $user - the username
 * @param $pass - the password
 * @return boolean
 */
function capstone_login($user, $pass){
    global $bus;

    $result = $bus->login($user, $pass);

    if($result !==false)
    {
        $user = $result;

        $_SESSION["type"] = $user["type"];
        $_SESSION["uid"] = $user["uid"];
        $_SESSION["name"] = $user["fName"] . " " . $user["lName"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["phone"] = $user["phone"];
        $_SESSION["office"] = $user["officelocation"];

        return true;
    }
    return false;
    }
    
?>