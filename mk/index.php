<?php
	include 'inc_helper.php'; 
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php 	
	//$year = date('Y');
	//$month = "0";//date('m');
	//if(isset($_GET['year'])) $year = $_GET['year'];
	//if(isset($_GET['month'])) $month = $_GET['month'];
?>
<?php include 'head.php'; ?>

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

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
	  
	  <?php switch($s_userGroupCode){ 
				case 'admin' : case 'it' : case 'tech' : 
					include 'index_1.php'; 
					break;
				case 'salesManager' : case 'salesAdmin' :
					include 'index_2.php'; 
					break;
				case 'sales' :	include 'index_salesperson.php'; 
					break;
				default : 
			} 
		?>
	
	</section>
	<!--sec.content-->
	
	</div>
	<!--content-wrapper-->

</div>
<!--warpper-->

</body>
</html>
