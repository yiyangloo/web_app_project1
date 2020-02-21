<?php
session_start();
require_once('common/db_conn.php');  //connection to database

$stmt = $conn->prepare('SELECT * FROM account WHERE user_id = :user_id');
$stmt->bindParam(":user_id", $_SESSION['user_id']);
$stmt->execute();
$accounts = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT * FROM account INNER JOIN transaksi ON account.id=transaksi.acc_id WHERE account.user_id = :user_id and transaksi.type = 'expense'");
$stmt->bindParam(":user_id", $_SESSION['user_id']);
$stmt->execute();
$transactions = $stmt->fetchAll();

if((isset($_POST['expense_amount'])) && (isset($_POST['expense_category'])) && (isset($_POST['account'])) && (isset($_POST['type'])) && (isset($_POST['transdate'])) ){
  $amount = $_POST['expense_amount'];
  $category = $_POST['expense_category'];
  $account_select = $_POST['account'];
  $transtype = $_POST['type'];
  $transdate = $_POST['transdate'];

  $sql = "INSERT INTO transaksi (amount, category, acc_id, type, transdate, user_id) VALUES (:amount, :category, :acc_id, :type, :transdate, :user_id)";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":amount", $amount);
  $stmt->bindParam(":category", $category);
  $stmt->bindParam(":acc_id", $account_select);
  $stmt->bindParam(":type", $transtype);
  $stmt->bindParam(":transdate", $transdate);
  $stmt->bindParam(":user_id", $_SESSION['user_id']);
  $stmt->execute();
  header('Location: expense.php');
}

if ( isset($_POST['edit_expense_form']) ) {
  $expense_amount = $_POST['expense_amount'];
  $transdate = $_POST['transdate'];
  $expense_category = $_POST['expense_category'];
  $account = $_POST['account'];
  $expense_id = $_POST['expense_id'];

  $sql = "UPDATE transaksi SET amount = :amount, transdate = :transdate, category = :category, acc_id = :acc_id where id = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":amount", $expense_amount);
  $stmt->bindParam(":transdate", $transdate);
  $stmt->bindParam(":category", $expense_category);
  $stmt->bindParam(":acc_id", $account);
  $stmt->bindParam(":id", $expense_id);
  $stmt->execute();
  header('Location: expense.php');
}

if ( isset($_POST['delete_expense_form']) ) {
  $expense_id = $_POST['expense_id'];

  $sql = "DELETE FROM transaksi where id = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":id", $expense_id);
  $stmt->execute();
  header('Location: expense.php');
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
        <h1 >Expenses</h1>
        <hr>
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#exampleModalCenter">
          Add Expenses
        </button>

        <table class="table">
          <thead>
            <tr>
              <th scope="col">Bank</th>
              <th scope="col">Date</th>
              <th scope="col">Category</th>
              <th scope="col">Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($transactions as $row){
              switch ($row['category']) {
                case '1':
                $row['category'] = "Food";
                break;
                case '2':
                $row['category'] = "Transport";
                break;
                case '3':
                $row['category'] = "Utilities";
                break;
                case '4':
                $row['category'] = "Entertainment";
                break;
                case '5':
                $row['category'] = "Groceries";
                break;
                case '6':
                $row['category'] = "Other";
                break;
              }
              echo "<tr><td>".$row['name']."</td><td>".$row['transdate']."</td><td>".$row['category']."</td><td>".$row['amount']."</td><td>";
              switch ($row['category']) {
                case "Food":
                $row['category'] = '1';
                break;
                case "Transport":
                $row['category'] = '2' ;
                break;
                case "Utilities":
                $row['category'] = '3';
                break;
                case "Entertainment":
                $row['category'] = '4';
                break;
                case "Groceries":
                $row['category'] = '5';
                break;
                case "Other":
                $row['category'] = '6';
                break;
              }
              echo '<td><a data-toggle="modal" data-id="'.$row['id'].'" data-acc_id="'.$row['acc_id'].'" data-transdate="'.$row['transdate'].'" data-category="'.$row['category'].'" data-amount="'.$row['amount'].'" class="edit_expense_modal btn btn-info mb-1 mr-3" href="#edit_expense">Edit</a>';
              echo '<a data-toggle="modal" data-id="'.$row['id'].'" class="delete_expense_modal btn btn-danger mb-1" href="#delete_expense">Delete</a></td>';
            }
            ?>
          </tbody>
        </table>


        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Expenses</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form class="" action="expense.php" method="post">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="expense_amount">Expenses Amount</label>
                    <input type="text" class="form-control" name="expense_amount" value="">
                  </div>
                  <div class="form-group">
                    <label for="expense_amount">Date</label>
                    <input type="date" class="form-control" name="transdate" value="">
                  </div>
                  <div class="form-group">
                    <input type="hidden" class="form-control" name="type" value="expense">
                  </div>
                  <div class="form-group">
                    <label for="expense_category">Category</label>
                    <select class="form-control" name="expense_category" id="">
                      <option value="1">Food</option>
                      <option value="2">Transport</option>
                      <option value="3">Utilities</option>
                      <option value="4">Entertainment</option>
                      <option value="5">Groceries</option>
                      <option value="6">Other</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="account">Account</label>
                    <select class="form-control" name="account" id="">
                      <?php
                      foreach ($accounts as $row) {
                        echo "<option value='".$row['id']."'>".$row['name']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Add Expenses</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="edit_expense" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit expense Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form class="" action="expense.php" method="post">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="expense_amount">expense Amount</label>
                    <input type="text" class="form-control" name="expense_amount" id="expense_amount" value="">
                  </div>
                  <div class="form-group">
                    <label for="expense_amount">Date</label>
                    <input type="date" class="form-control" name="transdate" id="transdate" value="">
                  </div>
                  <div class="form-group">
                    <input type="hidden" class="form-control" name="expense_id" id="expense_id" value="">
                  </div>
                  <div class="form-group">
                    <label for="expense_category">Category</label>
                    <select class="form-control" name="expense_category" id="expense_category">
                      <option value="1">Food</option>
                      <option value="2">Transport</option>
                      <option value="3">Utilities</option>
                      <option value="4">Entertainment</option>
                      <option value="5">Groceries</option>
                      <option value="6">Other</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="account">Account</label>
                    <select class="form-control" name="account" id="account">
                      <?php
                      foreach ($accounts as $row) {
                        echo "<option value='".$row['id']."'>".$row['name']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="edit_expense_form" class="btn btn-primary">Edit</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="delete_expense" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLongTitle">Delete expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form class="" action="expense.php" method="post">
                <div class="modal-body">
                  <p>Are you sure you want to delete this expense transaction?</p>
                  <div class="form-group">
                    <input type="hidden" class="form-control" name="expense_id" id="expense_id" value="">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="delete_expense_form" class="btn btn-danger">Delete</button>
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

  $(document).on("click", ".edit_expense_modal", function () {
    var expense_id = $(this).data('id');
    var amount = $(this).data('amount');
    var transdate = $(this).data('transdate');
    var category = $(this).data('category');
    var acc_id = $(this).data('acc_id');
    $(".modal-body #expense_amount").val( amount );
    $(".modal-body #transdate").val( transdate );
    $(".modal-body #expense_id").val( expense_id );
    $(".modal-body #expense_category").val( category );
    $(".modal-body #account").val( acc_id );
  });

  $(document).on("click", ".delete_expense_modal", function () {
    var expense_id = $(this).data('id');
    $(".modal-body #expense_id").val( expense_id );
  });
  </script>

</body>
</html>
