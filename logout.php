<?php
/**
 * Created by PhpStorm.
 * User: joselopezjr
 * Date: 12/13/17
 * Time: 4:22 PM
 * Description: This script will destory all data registered to a session after the user logs out.
 */

if (session_status() == PHP_SESSION_NONE)
{
    session_start();
    session_unset();
    session_destroy();
}


header("Location: index.php");
