<?php
	//include 'inc_helper.php'; 
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>

<?php 	
	//$year = date('Y');
	//$month = "0";//date('m');
	//if(isset($_GET['year'])) $year = $_GET['year'];
	//if(isset($_GET['month'])) $month = $_GET['month'];
?>

<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

   <?php
	$sqlRole = "";
	$sqlRoleSm = "";
	switch($s_userGroupCode){
		case 'sales' :
			$sqlRole = " AND ct.smCode='$s_smCode' ";
			$sqlRoleSm = " AND sm.code='$s_smCode' ";
			break;
		case 'salesAdmin' :
			$sqlRole = " AND ct.smAdmCode='$s_smCode' ";
			break;
		default :
	}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <section class="content-header">
      <h1>
        Welcome to Marketing Department
        <small><?php echo $s_userFullname; ?> [ ID: <?php echo $s_userId; ?>] </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Page</a></li>
        <li class="active">Here</li>
      </ol>
    </section>
    <!--/.content-header-->

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
	  
	  <?php switch($s_userGroupCode){ 
				case 'admin' : case 'it' : case 'tech' : case 'sales' : 
					include 'index_1.php'; 
					break;
				case 'salesManager' : case 'salesAdmin' :
					include 'index_2.php'; 
					break;
				//case 'sales' :	include 'index_salesperson.php'; 
				//	break;
				default : 
			} 
		?>
	</section>
	<!--sec.content-->
	
	</section>
    <!-- /.content -->

</div>
<!--content-wrapper-->

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

</body>
</html>
