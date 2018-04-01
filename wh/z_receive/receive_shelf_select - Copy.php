<?php include 'inc_helper.php'; ?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  

<?php 
	$id = $_GET['id'];
	
	$sqlCond = "";
	switch($s_userGroupCode){ 
		case 'it' : 
		case 'admin' :  
		case 'whOff' : 
		case 'whSup' : 			
			break;
		default : 
			header('Location: access_denied.php');
			exit();
	} 
	?>

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
		Set Shelf
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Set Shelf</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
<!-- Your Page Content Here -->
<div class="row">
	<div class="col-md-12">
		<div class="box">
			 <?php
			$sql = "SELECT dtl.id, dtl.rcNo, itm.prodCodeId, itm.barcode 
			, prd.code as prodCode 
			FROM receive_detail dtl
			LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
			LEFT JOIN product prd ON prd.id=itm.prodCodeId 
			WHERE dtl.id=:id
						";						
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':id', $id);
			$stmt->execute();	
			$hdr = $stmt->fetch();
			?>
			<div class="box-header with-border">              
				<h3 class="box-title">Receive No : <?= $hdr['rcNo']; ?> <span class="glyphicon glyphicon-chevron-right"/> <b><?=$hdr['barcode'];?></br></h3>

				<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
				<div class="btn-group">
				  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-wrench"></i></button>
				  <ul class="dropdown-menu" role="menu">
					<li><a href="#">Action</a></li>
					<li><a href="#">Another action</a></li>
					<li><a href="#">Something else here</a></li>
					<li class="divider"></li>
					<li><a href="#">Separated link</a></li>
				  </ul>
				</div>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
				<!--pull-right-->
				
			</div>
			<!-- /.box-header -->
			
			 <?php
			$sql = "SELECT ws.`sloc`, ws.`X`, ws.`Y`, ws.`Z`, ws.`code`, ws.`name`, ws.`statusCode`,
			(SELECT count(*) from wh_sloc_map_item smi WHERE smi.slocCode=ws.code) AS itemCount
			FROM wh_sloc ws
			GROUP BY ws.`sloc`, ws.`X`, ws.`Y`, ws.`Z`, ws.`code`, ws.`name`, ws.`statusCode`
			ORDER BY sloc, x, cast(y as int), cast(z as int)
						";						
			$stmt = $pdo->prepare($sql);	
			//$stmt->bindParam(':rcNo', $hdr['rcNo']);
			$stmt->execute();	
			$rowCount = $stmt->rowCount();
			?>
			<div class="box-body">
				<div class="row col-md-12">
				<input type="hidden" id="hid_rcNo" value="<?=$hdr['rcNo'];?>" />
				<input type="hidden" id="hid_recvProdId" value="<?=$hdr['id'];?>" />
				
				<?php $row_no=1; $x=''; $y=''; $z=''; while ($row = $stmt->fetch()) { 
				if($x<>'' and $x<>$row['X']){ ?> <br/><br/><?php } 
					$aColor = '';
					switch($row['itemCount']){
						case 0 : ?><a class="btn btn-success btn_set_shelf" data-code="<?=$row['code'];?>" ><?=$row['name'].' ['.$row['itemCount'].']';?></a><?php break;
						default : ?><a class="btn btn-danger btn_set_shelf" data-code="<?=$row['code'];?>" ><?=$row['name'].' ['.$row['itemCount'].']';?></a><?php break;
					}
				?>
						
				<?php $row_no+=1; $x=$row['X']; } ?>
				</div>
				<!--row-->
			</div>
			<!--box-body-->
		</div>
		<!--box-->
	</div>
	<!--col-md-12-->
</div>
<!--row-->
	  
	  
	
	
  
	<div id="spin"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>  
  
</div>
<!-- ./wrapper -->
</body>

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>
<!-- Add smoke dialog -->
<script src="bootstrap/js/smoke.min.js"></script>
<!-- Add _.$ jquery coding -->
<script src="../asset/js/underscore-min.js"></script>

<script>
$(document).ready(function() {
	$('.btn_set_shelf').click (function(e) {				 
		var params = {				
		rcNo: $('#hid_rcNo').val(),
		recvProdId: $('#hid_recvProdId').val(),
		slocCode: $(this).attr('data-code')
		};
		//alert(params.hdrID);
		$.smkConfirm({text:'Are you sure to Set ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: 'receive_set_shelf_update_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function(data) {
				if (data.success){  
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});
					window.location.href = "receive_set_shelf.php?rcNo=" + params.rcNo;
				}else{
					$.smkAlert({
						text: data.message,
						type: 'danger',
						position:'top-center'
					});
				}
				//e.preventDefault();		
			}).error(function (response) {
				alert(response.responseText);
			});
			//.post
		}});
		//smkConfirm
	});
	//.btn_click
});
//docutmen.ready
</script>

</html>
