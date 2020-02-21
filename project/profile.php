<?php
session_start();
require_once('common/db_conn.php'); //connection to database

$stmt = $conn->prepare('SELECT * FROM user WHERE id = :id');
$stmt->bindParam(":id", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();

if((isset($_POST['current_password'])) && (isset($_POST['new_password'])) && (isset($_POST['confirm_password'])) ){
  $current_password = $_POST['current_password'];
  $hashed_password = md5($current_password, FALSE);
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];
  $hashed_confirm_password = md5($confirm_password, FALSE);

  if ( ($_POST['current_password'] != '') && ($_POST['new_password'] != '') && ($_POST['confirm_password'] != '')){
    if($hashed_password == $user['password']){
      if ($new_password == $confirm_password) {
        $stmt = $conn->prepare('UPDATE user set password = :password WHERE id = :id');
        $stmt->bindParam(":password", $hashed_confirm_password);
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->execute();
        $_SESSION['confirm_password_success'] = 'Password changed.';
      } else {
        $_SESSION['confirm_password_different'] = 'The new and confirm password is different. Try again.';
      }
    } else {
      $_SESSION['wrong_password'] = 'Password is incorrect. Try again.';
    }

  } else {
    $_SESSION['empty'] = 'Please enter all the information required. Try again.';
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="css/main.css">
  <?php include_once"common/bootstrap.php"; ?>
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <title>MyFinancialPal</title>
</head>
<body>
  <div class="d-flex" id="wrapper">
    <?php include_once"common/sidebar.php"; ?>

    <div id="page-content-wrapper">
      <?php include_once"common/nav_index.php"; ?>


      <div class="container-fluid">
        <h1>Profile</h1>
        <hr>
        <?php
        if ( isset($_SESSION['confirm_password_different']) ) {
          echo '<div class="alert alert-danger" role="alert">'.$_SESSION['confirm_password_different'].'</div>';
          unset($_SESSION['confirm_password_different']);
        }
        if ( isset($_SESSION['wrong_password']) ) {
          echo '<div class="alert alert-danger" role="alert">'.$_SESSION['wrong_password'].'</div>';
          unset($_SESSION['wrong_password']);
        }
        if ( isset($_SESSION['empty']) ) {
          echo '<div class="alert alert-danger" role="alert">'.$_SESSION['empty'].'</div>';
          unset($_SESSION['empty']);
        }
        if ( isset($_SESSION['confirm_password_success']) ) {
          echo '<div class="alert alert-success" role="alert">'.$_SESSION['confirm_password_success'].'</div>';
          unset($_SESSION['confirm_password_success']);
        }

        ?>
        <form class="" action="index.html" method="post">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control col-6" name="username" value="<?php echo $user['username'] ?>"  disabled>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="text" class="form-control col-6" name="email" value="<?php echo $user['email'] ?>" disabled>
          </div>
          <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control col-6" name="phone_number" value="<?php echo $user['phone_num'] ?>" disabled>
          </div>
        </form>

        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
          Change Password
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form class="" action="profile.php" method="post">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" class="form-control" name="current_password" value="">
                  </div>
                  <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-control" name="new_password" value="">
                  </div>
                  <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" value="">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Menu Toggle Script -->
  <script>
  $("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
  });
  </script>


</body>
</html>
