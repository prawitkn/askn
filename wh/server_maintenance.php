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
<?php 
	include 'head.php'; 
?>

<div class="wrapper">

  <!-- Main Header -->
  <?php // include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php //include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Server Maintenance
      </h1>
      <!--<ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Page</a></li>
        <li class="active">Server Maintenance</li>
      </ol>-->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
	<div class="error-page">
		<h2 class="headline text-yellow">Server Maintenance</h2>
		<h2 class="headline text-yellow">Now Inprocess</h2>

		<div class="error-content">
		  <h3><i class="fa fa-warning text-yellow"></i> Please Check Back Later.</h3>

		  <p>
			<!--<a href="javascript:history.go(-1)" class="btn btn-primary">Go Back to Previous Page...</a>-->
		  </p>
		  
		</div>
	  </div>
	  <!-- /.error-page -->
	

	</section>
	<!--sec.content-->
	
	</div>
	<!--content-wrapper-->

</div>
<!--warpper-->

</body>
</html>
