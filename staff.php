<?php
include_once("assets/header.php");
$db = new Database();
$bus = new Business($db);
protect_page();

//Staff's Profile (Info)
$name = $_SESSION["name"];
$type = $_SESSION["type"];
$uid = $_SESSION["uid"];
$email = $_SESSION["email"];
$phone = $_SESSION["phone"];
$office = $_SESSION["office"];


// When the submit a new score
if(isset($_POST["submit"])) {
    $pid = $_POST["projectName"];

    if(isset($_POST["scoreNumber"])){
        $scoreNumber = $_POST["scoreNumber"];
        $result = $bus->updateScore($scoreNumber, $pid);

        if($result) {
            $msg = "<div class='alert alert-success'><strong>Success!</strong> Grad Student's score has been updated to the database.</div>";

        }
        else {
            $msg = "<div class='alert alert-danger'><strong>Failed!</strong> Grad Student's score is unable to be updated to the database. There are some problems in the form. Please try again.</div>";
        }   // $result


    }
    elseif(isset($_POST["statusChange"])){
        $sid = $_POST["statusChange"];
        $result = $bus->changeProjectStatus($sid, $pid);

        if($result instanceof stdClass)
        {

            if ($result->status==200) {
                $msg = "<div class='alert alert-success'><strong>Success! </strong>$result->msg</div>";

            } else {
                $msg = "<div class='alert alert-danger'><strong>Failed! </strong>$result->msg</div>";
            }   // $result
        }

        else
        {
            $msg = "<div class='alert alert-danger'><strong>Failed! </strong> We were not able to perform your request</div>";
        }

    }
    else{
        $result = $bus->deleteProject($pid); //delete the project

        if($result) {
            $msg = "<div class='alert alert-success'><strong>Success!</strong> Grad Student's capstone has been deleted from the database.</div>";

        }
        else {
            $msg = "<div class='alert alert-danger'><strong>Failed!</strong> Grad Student's capstone is unable to be deleted from the database. There are some problems in the form. Please try again.</div>";
        }   // $result

    }


}   // end submit if


?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script src="js/my.js" type="text/javascript"></script>


    <body class="fixed-nav sticky-footer bg-dark sidenav-toggled" id="page-top">

    <?php include ("assets/nav.php"); ?>

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

            <!-- Insert the TURNITIN SCORE [Message] -->
            <?php if(!empty($msg)) echo "<div class = 'card mb-5 '>" . $msg . "</div>"; ?>


            <!-- Icon Cards-->
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card text-white bg-primary o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fa fa-fw fa-list"></i>
                            </div>
                            <div class="mr-5">Enter TurnItIn.com Score</div>
                        </div>
                        <a href="#" class="btn btn-lg btn-success" data-toggle="modal" data-target="#changeScore">
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
                            <div class="mr-5">Change Status</div>
                        </div>
                        <a href="#" class="btn btn-lg btn-success" data-toggle="modal" data-target="#changeStatus">
                            <span class="float-left">Click here</span>
                            <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
                        </a>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card text-white bg-danger o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fa fa-fw fa-trash"></i>
                            </div>
                            <div class="mr-5">Delete Project</div>
                        </div>
                        <a href="#" class="btn btn-lg btn-success" data-toggle="modal" data-target="#deleteProject">
                            <span class="float-left">Click here</span>
                            <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
                        </a>
                    </div>
                </div>



            </div>



            <!--Staff Change Plagiarism Score -->
            <?php include_once("changeScore.php"); ?>

            <!--Staff Change Status -->
            <?php include_once("changeStatus.php"); ?>

            <!--Staff Delete Project -->
            <?php include_once("deleteProject.php"); ?>


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
                    <?php if(!empty($phone))echo "<li class='list-group-item list-group-item-primary'>Phone: " . $phone . "</li>" ?>

                    <?php if(!empty($office))echo "<li class='list-group-item list-group-item-secondary'>Office Location: " . $office . "</li>" ?>

                </ul>
            </div>

            <?php
            $result = $bus->getCapstone();

            if($result !== FALSE){
                $data = $result->fetchAll();

                //DataTables Card
                echo "<div class = 'card mb-3'>
                        <div class = 'class-header'><h3><i class = 
                        'fa fa-table'></i> Capstone Info </h3>
                        </div><div class = 'card-body'>
                        <div class = 'table-responsive'>
                        <table class = 'table table-bordered'
                        id = 'dataTable' width = '100%' cellspacing = '0'>
                        <thead><tr><th>Faculty</th><th>PID</th>
                        <th>Type</th><th>Project Name
                        </th><th>Description</th><th>Start Term</th>
                        <th>Expected-End-Date</th><th>Plagiarism Score</th>
                        <th>Grade</th><th>Grad Student</th><th>Current Status</th><th>Last Modified</th></tr></thead><tbody>";

                foreach($data as $facultyCapstone){
                    $pid = $facultyCapstone["pid"]; //Grad Student's Capstone

                    $result = $bus->getFacultyCapstone($pid); //Getting the committees on Grad student's capstone
                    if($result !== FALSE){
                        $data = $result->fetchAll();

                        echo "<tr><td>";
                        foreach ($data as $committees) {
                            echo "-" . $committees["faculty"] . "<br/><br/>";
                        }
                        echo "</td>";
                    }

                    echo  "<td>" . $facultyCapstone["pid"] . "</td>"
                        . "<td>" . $facultyCapstone["type"] . "</td>"
                        . "<td>" . $facultyCapstone["name"] . "</td>"
                        . "<td>" . $facultyCapstone["description"] . "</td>"
                        . "<td>" . $facultyCapstone["start_term"] . "</td>"
                        . "<td>" . $facultyCapstone["expected_end_date"] . "</td>"
                        . "<td>" . $facultyCapstone["plagiarism_score"] . "</td>"
                        . "<td>" . $facultyCapstone["grade"] . "</td>";

                    $result1 = $bus->getFacultyCapstoneInfo($pid);
                    if($result1 !== FALSE){
                        $data1 = $result1->fetchAll();
                        foreach($data1 AS $gradInfo){
                            echo "<td>" . $gradInfo["grad"] . "</td>"
                                . "<td>" . $gradInfo["status"] . "</td>" . "<td>"
                                . $gradInfo["date"] . "</td></tr>";
                        }
                    }

                }
                echo "</tbody></table></div></div></div>";
            }
            ?>
        </div>

        <div class = "myGradStatus"></div>

<?php include_once ("assets/footer.php"); ?>