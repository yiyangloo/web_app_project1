<?php
session_start();
require_once('common/db_conn.php'); //connection to database

if( (isset($_POST['username'])) && (isset($_POST['password'])) && (isset($_POST['confirm_password'])) && (isset($_POST['email'])) && (isset($_POST['phone_num'])) ){
if (($_POST['username'] != '') && ($_POST['password'] != '') && ($_POST['confirm_password'] != '') && ($_POST['email'] != '') && ($_POST['phone_num'] != '')) {
  if ( $_POST['password'] == $_POST['confirm_password']){
    //Retrieve the field values from our registration form.
    $username = !empty($_POST['username']) ? trim($_POST['username']) : null;
    $password = !empty($_POST['password']) ? trim($_POST['password']) : null;
    $confirm_password = !empty($_POST['confirm_password']) ? trim($_POST['confirm_password']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $phone_num = !empty($_POST['phone_num']) ? trim($_POST['phone_num']) : null;
    //TO ADD: Error checking (username characters, password length, etc).
    //Basically, you will need to add your own error checking BEFORE
    //the prepared statement is built and executed.

    //Now, we need to check if the supplied username already exists.

    //Construct the SQL statement and prepare it.
    $sql = "SELECT COUNT(username) AS num FROM user WHERE username = :username";
    $stmt = $conn->prepare($sql);

    //Bind the provided username to our prepared statement.
    $stmt->bindValue(':username', $username);

    //Execute.
    $stmt->execute();

    //Fetch the row.
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    //If the provided username already exists - display error.
    //TO ADD - Your own method of handling this error. For example purposes,
    //I'm just going to kill the script completely, as error handling is outside
    //the scope of this tutorial.
    if($row['num'] > 0){
      die('That username already exists!');
    }

    //Hash the password as we do NOT want to store our passwords in plain text.
    $passwordHash = md5($password, FALSE);

    //Prepare our INSERT statement.
    //Remember: We are inserting a new row into our users table.
    $sql = "INSERT INTO user (username, password, email, phone_num) VALUES (:username, :password, :email, :phone_num)";
    $stmt = $conn->prepare($sql);

    //Bind our variables.
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', $passwordHash);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':phone_num', $phone_num);

    //Execute the statement and insert the new account.
    $result = $stmt->execute();
    ?>
    <script type="text/javascript">alert('Thank you for registering with our website.'); </script>
    <?php
    //If the signup process is successful.
    if($result){
      //What you do here is up to you!

      header('Location: login.php');
    }

  } else {
    $_SESSION['register_password_different'] = 'The password and confirmation password is different. Try again.';

}
}else {
  $_SESSION['register_empty'] = 'Please enter all the information required. Try again.';
}
}

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
    <h1>Create New Account</h1>
    <hr>
    <?php
    if ( isset($_SESSION['register_empty']) ) {
      echo '<div class="alert alert-danger" role="alert">'.$_SESSION['register_empty'].'</div>';
      unset($_SESSION['register_empty']);
    }
    if ( isset($_SESSION['register_password_different']) ) {
      echo '<div class="alert alert-danger" role="alert">'.$_SESSION['register_password_different'].'</div>';
      unset($_SESSION['register_password_different']);
    }
    ?>
    <form method="post">
      <div class="form-group">
        <label for="inputUsername">Username</label>
        <input type="text" name="username" class="form-control" id="inputUsername">
      </div>
      <div class="form-group">
        <label for="inputPassword">Password</label>
        <input type="password" name="password" class="form-control" id="inputPassword">
      </div>
      <div class="form-group">
        <label for="inputPassword">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" id="inputcPassword">
      </div>
      <div class="form-group">
        <label for="inputEmail">E-Mail</label>
        <input type="text" name="email" class="form-control" id="inputEmail">
      </div>
      <div class="form-group">
        <label for="inputNum">Phone Number</label>
        <input type="text" name="phone_num" class="form-control" id="inputNum">
      </div>
      <div class="form-group">
        <button type="submit" name="register" class="btn btn-primary">Sign Up</button>
      </div>
    </form>
  </div>
</body>
</html>
