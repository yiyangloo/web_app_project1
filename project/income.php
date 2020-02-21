<?php
session_start();
require_once('common/db_conn.php');  //connection to database

$stmt = $conn->prepare('SELECT * FROM account WHERE user_id = :user_id');
$stmt->bindParam(":user_id", $_SESSION['user_id']);
$stmt->execute();
$accounts = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT * FROM account INNER JOIN transaksi ON account.id=transaksi.acc_id WHERE account.user_id = :user_id and transaksi.type = 'income'");
$stmt->bindParam(":user_id", $_SESSION['user_id']);
$stmt->execute();
$transactions = $stmt->fetchAll();

if((isset($_POST['income_amount'])) && (isset($_POST['income_category'])) && (isset($_POST['account'])) && (isset($_POST['type'])) && (isset($_POST['transdate'])) ){
  $amount = $_POST['income_amount'];
  $category = $_POST['income_category'];
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
  header('Location: income.php');
}

if ( isset($_POST['edit_income_form']) ) {
  $income_amount = $_POST['income_amount'];
  $transdate = $_POST['transdate'];
  $income_category = $_POST['income_category'];
  $account = $_POST['account'];
  $income_id = $_POST['income_id'];

  $sql = "UPDATE transaksi SET amount = :amount, transdate = :transdate, category = :category, acc_id = :acc_id where id = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":amount", $income_amount);
  $stmt->bindParam(":transdate", $transdate);
  $stmt->bindParam(":category", $income_category);
  $stmt->bindParam(":acc_id", $account);
  $stmt->bindParam(":id", $income_id);
  $stmt->execute();
  header('Location: income.php');
}

if ( isset($_POST['delete_income_form']) ) {
  $income_id = $_POST['income_id'];

  $sql = "DELETE FROM transaksi where id = :id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":id", $income_id);
  $stmt->execute();
  header('Location: income.php');
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
  <title>Document</title>
</head>
<body>
  <div class="d-flex" id="wrapper">
    <?php include_once"common/sidebar.php"; ?>

    <div id="page-content-wrapper">
      <?php include_once"common/nav_index.php"; ?>

      <div class="container-fluid">
        <h1>Income</h1>
        <hr>
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#exampleModalCenter">
          Add Income
        </button>

        <table class="table">
          <thead>
            <tr>
              <th scope="col">Bank</th>
              <th scope="col">Date</th>
              <th scope="col">Category</th>
              <th scope="col">Amount</th>
              <th scope="col">Option</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($transactions as $row){
              switch ($row['category']) {
                case '7':
                $row['category'] = "Active/Earned Income";
                break;
                case '8':
                $row['category'] = "Passive Income";
                break;
                case '9':
                $row['category'] = "Portfolio Income";
                break;
              }
              echo "<tr><td>".$row['name']."</td><td>".$row['transdate']."</td><td>".$row['category']."</td><td>".$row['amount']."</td>";
              switch ($row['category']) {
                case "Active/Earned Income":
                $row['category'] = "7";
                break;
                case "Passive Income":
                $row['category'] = '8';
                break;
                case "Portfolio Income":
                $row['category'] = '9';
                break;
              }
              echo '<td><a data-toggle="modal" data-id="'.$row['id'].'" data-acc_id="'.$row['acc_id'].'" data-transdate="'.$row['transdate'].'" data-category="'.$row['category'].'" data-amount="'.$row['amount'].'" class="edit_income_modal btn btn-info mb-1 mr-3" href="#edit_income">Edit</a>';
              echo '<a data-toggle="modal" data-id="'.$row['id'].'" class="delete_income_modal btn btn-danger mb-1" href="#delete_income">Delete</a></td>';
            }
            ?>
          </tbody>
        </table>


        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Income</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form class="" action="income.php" method="post">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="income_amount">Income Amount</label>
                    <input type="text" class="form-control" name="income_amount" value="">
                  </div>
                  <div class="form-group">
                    <label for="income_amount">Date</label>
                    <input type="date" class="form-control" name="transdate" value="">
                  </div>
                  <div class="form-group">
                    <input type="hidden" class="form-control" name="type" value="income">
                  </div>
                  <div class="form-group">
                    <label for="income_category">Category</label>
                    <select class="form-control" name="income_category" id="">
                      <option value="7">Active/Earned Income</option>
                      <option value="8">Passive Income</option>
                      <option value="9">Portfolio Income</option>
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
                  <button type="submit" class="btn btn-primary">Add Income</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="edit_income" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit Income Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form class="" action="income.php" method="post">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="income_amount">Income Amount</label>
                    <input type="text" class="form-control" name="income_amount" id="income_amount" value="">
                  </div>
                  <div class="form-group">
                    <label for="income_amount">Date</label>
                    <input type="date" class="form-control" name="transdate" id="transdate" value="">
                  </div>
                  <div class="form-group">
                    <input type="hidden" class="form-control" name="income_id" id="income_id" value="">
                  </div>
                  <div class="form-group">
                    <label for="income_category">Category</label>
                    <select class="form-control" name="income_category" id="income_category">
                      <option value="7">Active/Earned Income</option>
                      <option value="8">Passive Income</option>
                      <option value="9">Portfolio Income</option>
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
                  <button type="submit" name="edit_income_form" class="btn btn-primary">Edit</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="delete_income" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLongTitle">Delete Income</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form class="" action="income.php" method="post">
                <div class="modal-body">
                  <p>Are you sure you want to delete this income transaction?</p>
                  <div class="form-group">
                    <input type="hidden" class="form-control" name="income_id" id="income_id" value="">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="delete_income_form" class="btn btn-danger">Delete</button>
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

  $(document).on("click", ".edit_income_modal", function () {
    var income_id = $(this).data('id');
    var amount = $(this).data('amount');
    var transdate = $(this).data('transdate');
    var category = $(this).data('category');
    var acc_id = $(this).data('acc_id');
    $(".modal-body #income_amount").val( amount );
    $(".modal-body #transdate").val( transdate );
    $(".modal-body #income_id").val( income_id );
    $(".modal-body #income_category").val( category );
    $(".modal-body #account").val( acc_id );
  });

  $(document).on("click", ".delete_income_modal", function () {
    var income_id = $(this).data('id');
    $(".modal-body #income_id").val( income_id );
  });
  </script>


</body>
</html>
