<?php
    include_once("assets/header.php");
    $db = new Database();
    $bus = new Business($db);
    protect_page();

    //Faculty's Profile (Info)
    $name = $_SESSION["name"];
    $type = $_SESSION["type"];
    $uid = $_SESSION["uid"];
    $email = $_SESSION["email"];
    $phone = $_SESSION["phone"];
    $office = $_SESSION["office"];

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

                <!-- Icon Cards-->
                <div class="row">
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-primary o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                    <i class="fa fa-fw fa-list"></i>
                                </div>
                                <div class="mr-5">Respond to Requests</div>
                            </div>
                            <a href="#" class="btn btn-lg btn-success" data-toggle="modal" data-target="#requestCapstone">
              <span class="float-left">Click here</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
                        </div>
                    </div>
                </div>


                <!--Respond to Capstone Request -->
                <?php
                $result = $bus->getRequestCapstone($uid);
                if($result !== FALSE){
                    echo "<div class='modal fade' id='requestCapstone' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' 
                    aria-hidden='true'><div class='modal-dialog'><div class='modal-content'>
                    <div class='modal-header'><h4 class='modal-title'>Capstone Requests</h4>
                    <button type='button' data-dismiss='modal' aria-hidden='true'>×</button>
                    </div><div class='modal-body'><div class='table-responsive'>
                    <table class='table table-bordered'><thead><tr><th>PID</th></th><th>Project Name</th><th>Grad Student</th><th>Response</th></thead><tbody>";
                    $data = $result->fetchAll();

                    foreach($data as $requestCapstone){

                        //Check the current status
                        $result1 = $bus->getCurrentStatus($requestCapstone["pid"]);
                        if($result1 !== FALSE){
                            $currentStatus = $result1->fetch()["status"];

                            if($currentStatus == "Pre proposal" || $currentStatus == "Committee formed" || $currentStatus == "Proposal development"){

                                echo "<tr><td>" . $requestCapstone["pid"] . "</td>"
                                    . "<td>". $requestCapstone["name"] . "</td>"
                                    . "<td>" . $requestCapstone["gradName"] . "</td>"
                                    ."<td><button class='approval btn btn-large btn-primary' id = '" . $requestCapstone["pid"] . "' 
                            data-toggle='confirmation'data-btn-ok-label='Continue' data-btn-ok-icon='glyphicon glyphicon-share-alt'>Confirm</button></td></tr>";
                            }
                        }
                    }
                    echo "</tbody></table></div></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Close</button></div></div></div></div>";
                }
                ?>

                <!-- Faculty's Profile-->
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

                <!-- Retrieving the Faculty's pending capstone-->
                <?php
                    $result = $bus->getFacultyAssignCapstone($uid);

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
                        <thead><tr><th>Role</th><th>PID</th>
                        <th>Type</th><th>Project Name
                        </th><th>Description</th><th>Start Term</th>
                        <th>Expected-End-Date</th><th>Plagiarism Score</th>
                        <th>Grade</th><th>Grad Student</th><th>Current Status</th><th>Last Modified</th></tr></thead><tbody>";

                        foreach($data as $facultyCapstone){
                            $pid = $facultyCapstone["pid"]; //Grad Student's Capstone

                            echo "<tr><td>" . $facultyCapstone["role"] . "</td>"
                                . "<td>" . $facultyCapstone["pid"] . "</td>"
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
