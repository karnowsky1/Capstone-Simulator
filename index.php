<?php

include_once "assets/header.php";

//skip login page if already login
if(isset($_SESSION['uid']) && isset($_SESSION['type']) && isset($_SESSION['name']) && isset($_SESSION['email']))
{
    authorization_page();
}

    // When the submit (for login) button is clicked
    if(isset($_POST["submit"])) {
        $username = $_POST["user"];
        $password = $_POST["pass"];
        
        $result = capstone_login($username, $password);
        
        /**
        * Success: Redirecting to the home.php
        * Fail: Load the index.php repeat
        **/
        if($result) {
            authorization_page();
        } 
        else {
            echo "<div class='alert alert-danger'><strong>Invalid username or password.</strong></div>";   
        }   // $result
    }   // end submit if
?>

<!DOCTYPE html>
<html lang="en">


<body class="bg-dark">
  <div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header">Capstone Login</div>
      <div class="card-body">
        <form id = "login-form" method = "POST" action = "index.php">
          <div class="form-group">
            <label for="exampleInputEmail1">Username</label>
            <input class="form-control" type="text" aria-describedby="emailHelp" placeholder="Enter Username" name = "user" required>
          </div>
          <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input class="form-control" type="password" placeholder="Enter Password" name = "pass" required>
          </div>
          <div class="form-group">
            <div class="form-check">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox"> Remember Password</label>
            </div>
          </div>
          <input type = "submit" class="btn btn-primary btn-block" value = "Login" name = "submit">
        </form>
      </div>
    </div>
  </div>
  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>

</html>
