<?php
session_start();
require_once('common/db_conn.php'); //connection to database

function grabdata ($category,$type){
  include 'common/db_conn.php';
  $sql = "SELECT SUM(amount) FROM transaksi WHERE user_id = :id AND type = :type AND category = :category";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':id', $_SESSION['user_id']);
  $stmt->bindParam(':category', $category);
  $stmt->bindParam(':type', $type);
  $stmt->execute();
  $user1 = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user1['SUM(amount)'] == null){
    return 0;
  }
  else {
    $amount = $user1['SUM(amount)'];
    return $amount;
  }
}

for ( $x = 1; $x <= 6 ; $x++){
  $expensecategory[$x] = grabdata($x,'expense');
}

for ( $x = 7; $x <= 9 ; $x++){
  $incomecategory[$x] = grabdata($x,'income');
}

$catname[1] = "Food";
$catname[2] = "Transport";
$catname[3] = "Utilities";
$catname[4] = "Entertainment";
$catname[5] = "Groceries";
$catname[6] = "Other";
$catname[7] = "Active/Earned Income";
$catname[8] = "Passive Income";
$catname[9] = "Portfolio Income";
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="css/main.css">
  <?php include_once"common/bootstrap.php"; ?>
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <title>MyFinancialPal</title>

  <!--EXPENSE DATA -->
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Task', 'Hours per Day'],
      ['<?php echo $catname[1] ?>',  <?php echo $expensecategory[1] ?>],
      ['<?php echo $catname[2] ?>',  <?php echo $expensecategory[2] ?>],
      ['<?php echo $catname[3] ?>',  <?php echo $expensecategory[3] ?>],
      ['<?php echo $catname[4] ?>',  <?php echo $expensecategory[4] ?>],
      ['<?php echo $catname[5] ?>',  <?php echo $expensecategory[5] ?>],
      ['<?php echo $catname[6] ?>',  <?php echo $expensecategory[6] ?>],
    ]);
    var options = {
      title: 'Expenses Chart'
    };
    var chart = new google.visualization.PieChart(document.getElementById('expensechart_div'));
    chart.draw(data, options);
  }
  </script>

  <!--INCOME DATA -->
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Task', 'Hours per Day'],
      ['<?php echo $catname[7] ?>',  <?php echo $incomecategory[7] ?>],
      ['<?php echo $catname[8] ?>',  <?php echo $incomecategory[8] ?>],
      ['<?php echo $catname[9] ?>',  <?php echo $incomecategory[9] ?>],
    ]);
    var options = {
      title: 'Income Chart'
    };
    var chart = new google.visualization.PieChart(document.getElementById('incomechart_div'));
    chart.draw(data, options);
  }
  </script>
</head>
<body>
  <div class="d-flex" id="wrapper">
    <?php include_once"common/sidebar.php"; ?>
    <div id="page-content-wrapper">
      <?php include_once"common/nav_index.php"; ?>

      <div class="container-fluid">
        <div class="row">
          <div class="card-group">

            <div class="card ">
              <div class="card-body">
                <div id="incomechart_div" style="width: 600px; height: 500px;"></div>
              </div>
              <div class="card-footer ">
                <h5 class="card-title">Total Income: RM<?php echo $incomecategory[7]+$incomecategory[8]+$incomecategory[9]?></h5>
              </div>
            </div>

            <div class="card ">
              <div class="card-body">
                <div id="expensechart_div" style="width: 600px; height: 500px;"></div>
              </div>
              <div class="card-footer ">
                <h5 class="card-title">Total Expenses: RM<?php echo $expensecategory[1]+$expensecategory[2]+$expensecategory[3]+$expensecategory[4]+$expensecategory[5]+$expensecategory[6] ?></h5>
              </div>
            </div>

          </div>
        </div>
      </div><!-- /#end of row -->
    </div><!-- /#container-fluid end -->
  </div><!-- /#page-content-wrapper -->
</div><!-- /#wrapper -->


<!-- Menu Toggle Script -->
<script>
$("#menu-toggle").click(function(e) {
  e.preventDefault();
  $("#wrapper").toggleClass("toggled");
});
</script>
</body>
</html>
