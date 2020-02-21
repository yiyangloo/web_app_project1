<?php
require_once ("common/db_conn.php");  //connection to database
session_start();

if (isset ($_POST['login'])){
  $username = !empty($_POST['username']) ? trim($_POST['username']) : null;
  $passwordAttempt = !empty($_POST['password']) ? trim($_POST['password']) : null;

  $sql = "SELECT id, username, password FROM user WHERE username = :username";
  $stmt = $conn->prepare($sql);

  //Bind value.
  $stmt->bindValue(':username', $username);

  //Execute.
  $stmt->execute();

  //Fetch row.
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  //If $row is FALSE.
  if($user === false){
    $_SESSION['login_username'] = 'Username is incorrect. Try again.';
  } else{

    //User account found. Check to see if the given password matches the
    //password hash that we stored in our users table.
    //Compare the passwords.
    $validPassword = (md5($passwordAttempt, FALSE) == $user['password']);
    //If $validPassword is TRUE, the login has been successful.

    if($validPassword == TRUE){

      //Provide the user with a login session.
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['logged_in'] = time();

      //Redirect to our protected page, which we called home.php
      header('Location: index.php');
      exit;

    } else{
      //$validPassword was FALSE. Passwords do not match.
      $_SESSION['login_password'] = 'Password is incorrect. Try again.';
    }
  }
}// end of if isset login

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <?php include_once('common/bootstrap.php') ; ?>
  <title></title>
</head>
<body>

  <?php include_once('common/header.php');?>
  <div class="container" style="margin: auto;width: 50%;padding: 10px;">
    <h1>Log In</h1>
    <hr>
    <?php
    if ( isset($_SESSION['login_username']) ) {
      echo '<div class="alert alert-danger" role="alert">'.$_SESSION['login_username'].'</div>';
      unset($_SESSION['login_username']);
    }
    if ( isset($_SESSION['login_password']) ) {
      echo '<div class="alert alert-danger" role="alert">'.$_SESSION['login_password'].'</div>';
      unset($_SESSION['login_password']);
    }
    ?>
    <form class="" action="login.php" method="post">
      <div class="form-group">
        <label for="Username">Username</label>
        <input type="text" class="form-control" name="username" value="">
      </div>
      <div class="form-group">
        <label for="Password">Password</label>
        <input type="password" class="form-control" name="password" value="">
      </div>

      <div class="form-group">
        <a href="#">Forget password?</a><br><br>
        <input type="submit" class="btn btn-primary" name="login" value="Login">
      </div>

    </form>


  </div>
</body>
</html>
