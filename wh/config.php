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
<?php 
	include 'head.php'; 
?>

<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   <?php
	$rootPage = 'config';
	$tb="cadet18_person";
	$reCheckIn=0;
	if(isset($_GET['reCheckIn'])){
		$sql = "UPDATE `".$tb."` SET isCount=0 ";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();	
		$reCheckIn=0;
	}//isset reCheckIn
	
	$reInvite=0;
	if(isset($_GET['reInvite'])){
		$sql = "UPDATE `".$tb."` SET isInvite=0 WHERE group2Name LIKE '%เสียชีวิต%' ";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();	
		
		$sql = "UPDATE `".$tb."` SET isInvite=1 WHERE group2Name NOT LIKE '%เสียชีวิต%' ";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();	
		$reInvite=0;
	}//isset reInvite
	
   ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="glyphicon glyphicon-setting"></i>
       Check in Config
        <small>Check in Config management</small>
      </h1>
	  <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Check in Config List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Check in Config</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
	<div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Check in Config</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">            
            <div class="row">                
					<div class="col-md-6">		
						<form id="form1"  onsubmit="return confirm('Do you really want to submit the form?');" >
						<input type="hidden" name="reCheckIn" value="<?=$reCheckIn;?>" />
                        <button id="btn_reset_check_in" type="submit" class="btn btn-primary">Reset Production</button>
						</form>
					</div>
					<div class="col-md-6">		
						<form id="form1"  onsubmit="return confirm('Do you really want to submit the form?');" >
						<input type="hidden" name="reInvite" value="<?=$reInvite;?>" />
                        <button id="btn_reset_invite" type="submit" class="btn btn-primary">Reset Invite</button>
						</form>
					</div>
					<!--/.col-md-->
                </div>
                <!--/.row-->       
            </div>
			<!--.body-->    
    </div>
	<!-- /.box box-primary -->
	

	</section>
	<!--sec.content-->
	
	</div>
	<!--content-wrapper-->

</div>
<!--warpper-->

<!-- REQUIRED JS SCRIPTS -->
<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>

<script src="bootstrap/js/smoke.min.js"></script>

<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>



<script> 
$(document).ready(function() {
	$('#form1').on("submit", function(e) {
		if(!confirm("Are you sure?")){
			return false;
		);
		e.preventDefault();
	});
	
});
//doc ready
</script>





</body>
</html>
