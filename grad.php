<?php
include_once("assets/header.php");
    $db = new Database();
    $bus = new Business($db);
    protect_page();

    //where did the login for grad occured?

    //Grad Student's Profile (Info)
    $name = $_SESSION["name"];
    $type = $_SESSION["type"];
    $uid = $_SESSION["uid"];
    $email = $_SESSION["email"];
    $pid = $_SESSION["uid"];

    //Determine if the Grad student has created the Capstone Project
    $result = $bus->isCapstoneExist($pid);
    if($result !== FALSE){
        $disabled = " disabled";
    }

    // When the submit a capstone proposal
    if(isset($_POST["submit"])) {
        $type = $_POST["projectType"];
        $projectName = $_POST["projectName"];
        $description = $_POST["description"];
        $startTerm = $_POST["startTerm"];
        $expectedDate = $_POST["expectedDate"];
        $selectFaculty = $_POST["selectFaculty"];
        
        $result = $bus->add($uid, $type, $projectName, $description, $startTerm, $expectedDate, $selectFaculty);
        
        /**
        * Success: Redirecting to the home.php
        * Fail: Load the index.php repeat
        **/
        if($result) {
            $msg = "<div class='alert alert-success'><strong>Success!</strong> Your capstone has been added to the database.</div>";
            //disable the create button after Grad Student creates a new capstone.
            $disabled = " disabled"; 
        } 
        else {
            $msg = "<div class='alert alert-danger'><strong>Failed!</strong> Your capstone is unable to added to the database. There are some problems in the form. Please try again.</div>";   
        }   // $result
    }   // end submit if

?>

    <body class="fixed-nav sticky-footer bg-dark sidenav-toggled" id="page-top">

        <?php include_once("assets/nav.php"); ?>

        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Breadcrumbs-->
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Capstone</a>
                    </li>
                    <li class="breadcrumb-item active">Welcome,
                        <?php echo $name?>
                    </li>
                </ol>

                <!-- Insert the Capstone [Message] -->
                <?php if(!empty($msg)) echo "<div class = 'card mb-5 '>" . $msg . "</div>"; ?>

                <!-- Icon Cards-->
                <div class="row">
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-primary o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fa fa-fw fa-list"></i>
                                </div>
                                <div class="mr-5">Create a New Capstone</div>
                            </div>
                            <a href="#" class="btn btn-lg btn-success <?php if(!empty($disabled)) echo $disabled ?>" data-toggle="modal" data-target="#createCapstone">
              <span class="float-left">Click here</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-warning o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fa fa-fw fa-info"></i>
                                </div>
                                <div class="mr-5">See Status Change</div>
                            </div>
                            <a href="#" class="btn btn-lg btn-success" data-toggle="modal" data-target="#statusCapstone">
              <span class="float-left">Click here</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
                        </div>
                    </div>
                </div>
                
                <!--Grad Create Capstone -->
                <?php include_once("createCapstone.php"); ?>

                <!--Grad Capstone Status -->
                <?php
                $result = $bus->getGradCapstoneStatus($uid);
                if($result !== FALSE){
                    echo "<div class='modal fade' id='statusCapstone' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'><div class='modal-dialog'><div class='modal-content'><div class='modal-header'><h4 class='modal-title'>Capstone Status</h4><button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button></div><div class='modal-body'><div class='table-responsive'><table class='table table-bordered'><thead><tr><th>Status</th><th>Status Description</th><th>Last Modified</th></thead><tbody>";
                    $data = $result->fetchAll();

                    foreach($data as $gradStatus){
                        echo "<tr><td>" . $gradStatus["name"] . "</td>"
                            . "<td>" . $gradStatus["description"] . "</td>"
                            . "<td>" . $gradStatus["last_modified"] . "</td></tr>";
                    }
                    echo "</tbody></table></div></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Close</button></div></div></div></div>";
                }
                ?>

                    <!-- Grad Student's Profile -->
                    <div class="card mb-5">
                        <ul class="list-group">
                            <div class="card-header">
                                <i class="fa fa-user"></i> Profile
                            </div>
                            <li class="list-group-item list-group-item-primary">Type:
                                <?php if(!empty($type))echo $type ?>
                            </li>
                            <li class="list-group-item list-group-item-secondary">UID:
                                <?php if(!empty($uid)) echo $uid ?>
                            </li>
                            <li class="list-group-item list-group-item-success">Email:
                                <?php if(!empty($email)) echo $email ?>
                            </li>
                        </ul>
                    </div>

                    <!-- Retrieving the Grad Capstone Info -->
                    <?php
                    $result = $bus->getGradCapstone($pid);

                    if($result !== FALSE) {
                        //DataTables Card
                        echo "<div class = 'card mb-3'>
                        <div class = 'class-header'><h3><i class = 
                        'fa fa-table'></i> Capstone Info </h3>
                        </div><div class = 'card-body'>
                        <div class = 'table-responsive'>
                        <table class = 'table table-bordered'
                        id = 'dataTable' width = '100%' cellspacing = '0'>
                        <thead><tr><th>PID</th><th>Type</th><th>Project Name
                        </th><th>Description</th><th>Start Term</th>
                        <th>Expected-End-Date</th><th>Plagiarism Score</th>
                        <th>Grade</th><th>Faculty</th><th>Current Status</th></tr></thead><tbody>";

                        $data = $result->fetchAll();

                        foreach ($data as $gradCapstone) {
                            echo "<tr><td>" . $gradCapstone["pid"] . "</td>"
                                . "<td>" . $gradCapstone["type"] . "</td>"
                                . "<td>" . $gradCapstone["name"] . "</td>"
                                . "<td>" . $gradCapstone["description"] . "</td>"
                                . "<td>" . $gradCapstone["start_term"] . "</td>"
                                . "<td>" . $gradCapstone["expected_end_date"] . "</td>"
                                . "<td>" . $gradCapstone["plagiarism_score"] . "</td>"
                                . "<td>" . $gradCapstone["grade"] . "</td>";

                            //Retrieving the faculty and latest status
                            $result = $bus->getFacultyCapstone($pid);
                            if ($result !== FALSE) {
                                $data = $result->fetchAll();

                                echo "<td>";
                                foreach ($data as $gradFaculty) {
                                    echo "-" . $gradFaculty["faculty"] . "<br/><br/>";
                                }
                                echo "</td>";

                                $result = $bus->getCurrentStatus($pid);
                                if($result !== FALSE){
                                    $data = $result->fetch();
                                    echo "<td>" . $data["status"] . "</td></tr>";
                                }
                            }
                        }
                        echo "</tbody></table></div></div></div>";
                    }
                ?>
            </div>

            <?php include_once ("assets/footer.php"); ?>
