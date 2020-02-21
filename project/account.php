<?php
session_start();
require_once('common/db_conn.php'); //connection to database

$stmt = $conn->prepare('SELECT * FROM account WHERE user_id = :user_id');
$stmt->bindParam(":user_id", $_SESSION['user_id']);
$stmt->execute();
$accounts = $stmt->fetchAll();

if ( (isset($_POST['account_name']))  && (isset($_POST['user_id'])) ){
  $account_name = $_POST['account_name'];
  $user_id = $_POST['user_id'];

  $sql = "INSERT INTO account (name, user_id) VALUES (:name , :user_id)";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":name", $account_name);
  $stmt->bindParam(":user_id", $user_id);
  $stmt->execute();
  header('Location: account.php');
}

$stmt = $conn->prepare("SELECT acc_id, SUM(a) balance from (SELECT acc_id, SUM(amount) a FROM transaksi WHERE user_id = :user_id and type = 'income' GROUP BY acc_id UNION SELECT acc_id, SUM(amount*-1) a FROM transaksi WHERE user_id = :user_id and type = 'expense' GROUP BY acc_id) x GROUP BY acc_id");
$stmt->bindParam(":user_id", $_SESSION['user_id']);
$stmt->execute();
$balance = $stmt->fetchAll();

if ( isset($_POST['acc_update']) ) {
  $account_name = $_POST['account_name'];
  $account_id = $_POST['account_id'];

  $sql = "UPDATE account SET name = :name where id = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":name", $account_name);
  $stmt->bindParam(":id", $account_id);
  $stmt->execute();
  header('Location: account.php');
}

if ( isset($_POST['acc_delete']) ) {
  $account_id = $_POST['account_id'];

  $sql = "DELETE FROM account where id = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":id", $account_id);
  $stmt->execute();
  header('Location: account.php');
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
        <h1>Accounts and Wallets</h1>
        <hr>
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#add_account">
          Add Account
        </button>

        <table class="table">
          <thead>
            <tr>
              <th scope="col">Account Name</th>
              <th scope="col">Balance (RM)</th>
              <th scope="col">Options</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($accounts as $row){
              echo "<tr><td>".$row['name']."</td><td>";
              foreach ($balance as $acc_balance) {
                if ($row['id'] == $acc_balance['acc_id']){
                  echo $acc_balance['balance']."</td>";
                }
              }
              echo '<td><a data-toggle="modal" data-name="'.$row['name'].'" data-id="'.$row['id'].'" class="edit_account_modal btn btn-info mb-1 mr-3" href="#edit_account">Edit</a>';
              echo '<a data-toggle="modal" data-id="'.$row['id'].'" class="delete_account_modal btn btn-danger mb-1" href="#delete_account">Delete</a></td>';
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="modal fade" id="add_account" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Add Account</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="" action="account.php" method="post">
              <div class="modal-body">
                <div class="form-group">
                  <label for="expense_amount">Account Name</label>
                  <input type="text" class="form-control" name="account_name" value="">
                  <input type="hidden" class="form-control" name="user_id" value="<?php echo $_SESSION['user_id'] ?>">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Account</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal fade" id="edit_account" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Edit Account Information</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="" action="account.php" method="post">
              <div class="modal-body">
                <div class="form-group">
                  <label for="account_name">Account Name</label>
                  <input type="text" class="form-control" name="account_name" id="account_name" value="">
                  <input type="hidden" class="form-control" name="account_id" id="account_id" value="">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="acc_update" class="btn btn-primary">Update Account</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal fade" id="delete_account" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-danger" id="exampleModalLongTitle">Delete Account</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="" action="account.php" method="post">
              <div class="modal-body">
                <p>Are you sure you want to delete this finance account?</p>
                <div class="form-group">
                  <input type="hidden" class="form-control" name="account_id" id="account_id" value="">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="acc_delete" class="btn btn-primary">Delete Account</button>
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

$(document).on("click", ".edit_account_modal", function () {
  var acc_id = $(this).data('id');
  var acc_name = $(this).data('name');
  $(".modal-body #account_id").val( acc_id );
  $(".modal-body #account_name").val( acc_name );
});

$(document).on("click", ".delete_account_modal", function () {
  var acc_id = $(this).data('id');
  $(".modal-body #account_id").val( acc_id );
});
</script>


</body>
</html>
