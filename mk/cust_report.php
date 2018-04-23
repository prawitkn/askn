<?php
    include '../db/database.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>
    
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Customer Report
        <small>Customer Report management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Menu</a></li>
        <li class="active">Customer Report Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        
<!-- Day 8 0:09:09  till 27:50-->

      <!-- Your Page Content Here -->
      <a href="cust_pdf.php" class="btn btn-primary">PDF Report 1</a>
      <a href="cust_pdf2.php" class="btn btn-info">PDF Report 2</a>
      <a href="cust_excel.php" class="btn btn-warning">Excel Report</a>
      <br><br>
 
 
      <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Customer List</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
                $sql_cust = "SELECT COUNT(*) AS COUNTCUST FROM customer";
                $result_cust = mysqli_query($link, $sql_cust);
                $count_cust = mysqli_fetch_assoc($result_cust);
          ?>
          <span class="label label-primary">Total <?php echo $count_cust['COUNTCUST']; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
           <?php
                $sql = "SELECT * FROM customer ORDER BY custID ASC";
                $result = mysqli_query($link, $sql);
                
           ?> 
            
            <table class="table table-striped">
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>Delete</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td>
                         <?= $row['custID']; ?>
                    </td>
                    <td>
                         <?= $row['custName']; ?>
                    </td>
                    <td>
                         <?= $row['custEmail']; ?>
                    </td>
                    <td>
                         <?= $row['custTel']; ?>
                    </td>
                    <td>
                        <a href="del_cust.php?id=<?= $row['custID']; ?>"><i class="fa fa-trash"></i></a>
                    
                                           
                   
                    </td>
                </tr>
                <?php } ?>
            </table>
    
    </div><!-- /.box-body -->
  <div class="box-footer">
      
      
    <!--The footer of the box -->
  </div><!-- box-footer -->
</div><!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>
  
  
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
