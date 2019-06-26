<?php
    include_once("Database.class.php");
class Business{

    //Attributes for connection
    private $db;
    private $conn;

    /*
    A default constructor that will accept the Database object.
    @param db - A database object
    **/
    function __construct(Database $db){
        $this->db = $db;
        $this->conn = $db->get_connection();


        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }

    }

    /**
     * This function returns the PDO connection
     * @return string - connection
     */
    function getConn(){
        return $this->conn;
    }

    /*
        This function sanitize and return any input provided by the user to
        avoid from corruptting the database.
        @param $data - any input provided by the user.
        @return $data - sanitized input
    */
    function sanitize($data){
        $data = trim($data);
        $data = strip_tags($data);
        $data = htmlentities($data);

        return $data;
    }
    /**
     * This function will validate the username and password from database
     * @param $user - the username provided by the user.
     * @param $pass - the password provided by the user.
     * @return boolean - success (true) / fail (false)
     */
    function login($user, $pass){
        $sql = "SELECT * FROM people WHERE username = ?";

        $result = $this->db->query($sql, array($user));

        if($result!==false)
        {
            $info = $result->fetch();

            $password = $info['username'].trim($pass);

            $hash = $info['password'];

            if(password_verify($password,$hash))
            {
                return $info;
            }

        }
        return false;
    }

    /**
     * This function will insert a new capstone into the database
     * @param $pid - the value of project id.
     * @param $type - the type of capstone provided by the user.
     * @param $name - the name of capstone provided by the user.
     * @param $description - the description of capstone provided by the user.
     * @param $startTerm - the start term (selection) provided by the user.
     * @param $expectedDate - the expected end date provided by the user.
     * @param $selectFaculty - the choice of faculty provided by the user.
     * @return boolean - success (true) / fail (false)
     */
    function add($pid, $type, $name, $description, $startTerm, $expectedDate, $selectFaculty){
        $pid = $this->sanitize($pid);
        $type = $this->sanitize($type);
        $name = $this->sanitize($name);
        $description = $this->sanitize($description);
        $startTerm = $this->sanitize($startTerm);
        $expectedDate = $this->sanitize($expectedDate);
        //change input date format with date()
        $expectedDate = date('Y-m-d', strtotime($expectedDate));
        $selectFaculty = $this->sanitize($selectFaculty);

        // INSERT THE CAPSTONE
        $sql = "INSERT INTO project(pid, type, name, description, start_term, expected_end_date) VALUES(?,?,?,?,?,?)";
        $result = $this->db->query($sql,array($pid,$type,$name,$description,$startTerm,$expectedDate));
        if($result == FALSE){
            $this->db->rollback();
            return false;
        }

        // Assign the Prof (REQUEST PENDING)
        $sql = "INSERT INTO people_project(uid, pid) VALUES(?,?)";
        $result = $this->db->query($sql, array($selectFaculty, $pid));
        if($result == FALSE){
            $this->db->rollback();
            return false;
        }

        // Update the Grad Student's Capstone (pre-proposal)
        $sql = "INSERT INTO project_status(sid, pid) VALUES(?, ?)";
        $result = $this->db->query($sql, array(100, $pid));
        if($result == FALSE){
            $this->db->rollback();

            return false;
        }

        $email = $this->getEmail($selectFaculty);
        if($email!==false)
        {
            $to = $email->fetch()['email'];
            $subject = "You got a new request from " . $name;
            $msg = "Log in your account to get more information from " . $name . "'s proposal capstone";
            mail($to, $subject, $msg);
        }

        return $this->db->commit();
    }

    /*
     * Getting the everything of Grad student's capstone information
     */


    /**
     * This function will retrieve the everything of Grad Student's capstone information
     * @param $pid - the value of project id.
     * @return boolean - success (true) / fail (false)
     */
    function getGradCapstone($pid){
        $sql = "SELECT * FROM project
        WHERE project.pid = ?";
        return $result = $this->db->query($sql, array($pid));
    }

    /**
     * This function will retrieve the multiple committees on Grad Student's project
     * @param $pid - the value of project id.
     * @return boolean - success (true) / fail (false)
     */
    function getFacultyCapstone($pid){
        $sql = "SELECT CONCAT(fname, ' ', lname) as 'faculty' FROM people
        INNER JOIN people_project ON people_project.uid = people.uid
        WHERE people_project.pid = ? AND people.type = 'Faculty';";

        return $result = $this->db->query($sql, array($pid));
    }

    /**
     * This function will retrieve the capstone's current status
     * @param $pid - the value of project id.
     * @return boolean - success (true) / fail (false)
     */
    function getCurrentStatus($pid){
        $sql = "SELECT status.name as 'status' FROM status
        INNER JOIN project_status ON project_status.sid = status.sid
        INNER JOIN project ON project.pid = project_status.pid
        INNER JOIN people_project ON people_project.pid = project.pid
        INNER JOIN people on people.uid = people_project.uid WHERE project.pid = ? ORDER BY
        project_status.last_modified DESC LIMIT 1;";

        return $result = $this->db->query($sql, array($pid));
    }

    /**
     * This function will check if Grad Student has created the capstone.
     * @param $pid - the value of project id.
     * @return boolean - success (true) / fail (false)
     */
    function isCapstoneExist($pid){

        $sql = "SELECT * FROM project INNER JOIN people ON people.uid = project.pid WHERE people.uid = ?";
        return $result = $this->db->query($sql, array($pid));
    }

    /**
     * This function will retrieve the all terms from rit_calendar table
     * @return boolean - success (true) / fail (false)
     */
    function getTerm(){

        $sql = "SELECT * FROM rit_calendar;";
        return $result = $this->db->query($sql);
    }

    /**
     * This function will retrieve the all status from status table
     * @return boolean - success (true) / fail (false)
     */
    function getStatus(){
        $sql = "SELECT * FROM status";
        return $result = $this->db->query($sql);
    }

    /**
     * This function will retrieve the all Grad Student's capstone
     * @return boolean - success (true) / fail (false)
     */
    function getCapstone(){
        $sql = "SELECT * FROM project";
        return $result = $this->db->query($sql);
    }

    /**
     * This function will retrieve the all Faculty or Staff's name.
     * @param $type - the type of user
     * @return boolean - success (true) / fail (false)
     */
    function getFaculty($type){
        $sql = "SELECT uid, CONCAT(fname, ' ', lname) AS name FROM people
        WHERE type = ?";

        return $result = $this->db->query($sql,  array($type));
    }

    /**
     * This function will retrieve the user's email
     * @param $uid - the user's UID
     * @return boolean - success (true) / fail (false)
     */
    function getEmail($uid){
        $sql = "SELECT email FROM people WHERE $uid = ?";
        return $result = $this->db->query($sql, array($uid));
    }

    /**
     * This function will retrieve the Faculty's assigned capstone (including the pre-proposal capstone)
     * @param $uid - the user's UID
     * @return boolean - success (true) / fail (false)
     */
    function getFacultyAssignCapstone($uid){
        $sql = "SELECT people_project.role, project.pid, project.type, project.name, description, start_term, expected_end_date, plagiarism_score,
        grade FROM project INNER JOIN people_project ON people_project.pid = project.pid
        INNER JOIN people ON people.uid = people_project.uid WHERE people.uid = ?";

        return $result = $this->db->query($sql, array($uid));
    }

    /**
     * This function will retrieve the Faculty's Capstone, including the Grad Student's name and
     * the current status of capstone
     * @param $pid - the project id
     * @return boolean - success (true) / fail (false)
     */
    function getFacultyCapstoneInfo($pid){
        $sql = "SELECT DISTINCT CONCAT(fname, ' ', lname) as 'grad', status.name as 'status', project_status.last_modified
        as 'date' FROM status INNER JOIN project_status ON project_status.sid = status.sid INNER JOIN project
        ON project.pid = project_status.pid INNER JOIN people_project
        ON people_project.pid = project.pid INNER JOIN people on people.uid = people_project.pid
        where project.pid= ?
        ORDER BY project_status.last_modified DESC LIMIT 1;";

        return $result = $this->db->query($sql, array($pid));
    }

    /**
     * This function will retrieve the Grad Student's all statuses for his/her capstone
     * @param $pid - the project id
     * @return boolean - success (true) / fail (false)
     */
    function getGradCapstoneStatus($pid){

        $sql = "SELECT status.name, status.description, project_status.last_modified FROM project
                INNER JOIN project_status ON project_status.pid = project.pid
                INNER JOIN status ON status.sid = project_status.sid
                WHERE project.pid = ?";

        return $this->db->query($sql,array($pid));
    }

    /**
     * This function will retrieve the Faculty's pending capstone from a Grad Student (pre-approval)
     * @param $uid - the people_project's uid
     * @return boolean - success (true) / fail (false)
     */
    function getRequestCapstone($uid){
        $sql = "SELECT project.pid, project.name, CONCAT(people.fName, ' ', people.lName) as 'gradName' FROM status
        INNER JOIN project_status on project_status.sid = status.sid
        INNER JOIN project on project.pid = project_status.pid
        INNER JOIN people_project ON people_project.pid = project.pid
        INNER JOIN people ON people.uid = people_project.pid
        WHERE project_status.sid = ? AND people_project.uid = ? and status.name != ?
        ORDER BY project_status.last_modified;";

        $ignore = 'Proposal approved';
        return $this->db->query($sql, array(100, $uid,$ignore));
    }

    /**
     *  This private function will return the object with status and message
     * @param $status - the Integer value
     * @param $msg - the String message
     * @return object
     */
    private function returnStdClass($status,$msg)
    {
        $std = new stdClass();
        $std->status = $status;
        $std->msg = $msg;

        return $std;
    }


    /**
     * This function will insert the new status for capstone
     * @param $sid - the choice of status provided by the user.
     * @param $pid - the project id of capstone
     * @param $comment - the any input provided by the user.
     * @return object - return the obj w/ status and message
     */
    function changeProjectStatus($sid, $pid, $comment = null){

       try
       {
           //duplicate checker
           $sql = "SELECT sid FROM project_status WHERE sid = ? AND pid = ?";
           $res = $this->db->query($sql,array($sid, $pid));

           if($res!==false && $res->rowCount() > 0)
           {

               $msg = "Unable to change project due to status duplicates.";
               return $this->returnStdClass(300,$msg);
           }

           //no duplicate
           $sql = "INSERT INTO project_status(sid,pid,comment) VALUES(?, ?, ?)";
           $result = $this->db->query($sql, array($sid, $pid, $comment));

           if($result == FALSE){
               $this->db->rollback();
               $msg = "There was an issue when attempt to perform your request";
               return $this->returnStdClass(400,$msg);
           }

           if($this->db->commit())
           {
                $msg = "Successful update";
                return $this->returnStdClass(200, $msg);
           }

           $msg = "There was an issue when attempt to perform your request";
           return $this->returnStdClass(400, $msg);

       }
       catch(Exception $e)
       {
           $this->returnStdClass(500,"That should not have happened, please try again");
       }

    }

    /**
     * This function will update the plagiarism score
     * @param $pid - the project id of capstone
     * @param $scoreValue - the value of plagiarism grade provided by the user.
     * @return boolean - success (true) / fail (false)
     */
    function updateScore($scoreValue, $pid){
        $sql = "UPDATE project SET plagiarism_score = ? WHERE PID = ?";
        $result = $this->db->query($sql, array($scoreValue, $pid));
        if($result == FALSE){
            $this->db->rollback();
            return FALSE;
        }
        return $this->db->commit();
    }

    /**
     * This function will delete the project from database
     * @param $pid - the project id of capstone
     * @return boolean - success (true) / fail (false)
     */
    function deleteProject($pid){
        $sql = "DELETE FROM people_project WHERE pid = ?";
        $sql1 = "DELETE FROM project_status WHERE pid = ?";
        $sql2 = "DELETE FROM project WHERE pid = ?";

        $result = $this->db->query($sql, array($pid));
        if($result == FALSE){
            $this->db->rollback();
            return FALSE;
        }
        $result = $this->db->query($sql1, array($pid));
        if($result == FALSE){
            $this->db->rollback();
            return FALSE;
        }
        $result = $this->db->query($sql2, array($pid));
        if($result == FALSE){
            $this->db->rollback();
            return FALSE;
        }
        return $this->db->commit();
    }

}   // End of the business


?>
