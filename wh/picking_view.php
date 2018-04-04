<?php
  //  include '../db/database.php';
  include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$rootPage='picking';
$pickNo = $_GET['pickNo'];
$sql = "
SELECT hdr.`pickNo`, hdr.`soNo`, hdr.`pickDate`, hdr.`remark`, hdr.`statusCode`
, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`
, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`
					, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
FROM picking hdr
LEFT JOIN user uca on uca.userID=hdr.createByID					
LEFT JOIN user ucf on ucf.userID=hdr.confirmById
LEFT JOIN user uap on uap.userID=hdr.approveById
WHERE 1
AND hdr.pickNo=:pickNo
";
$stmt = $pdo->prepare($sql);			
$stmt->bindParam(':pickNo', $pickNo);	
$stmt->execute();
$hdr = $stmt->fetch();			
?>

<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="plugins/iCheck/all.css">

<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="glyphicon glyphicon-shopping-cart"></i>
       Picking
        <small>Picking management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Picking List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?pickNo=<?=$pickNo;?>"><i class="glyphicon glyphicon-edit"></i>Pick No.<?=$pickNo;?></a></li>
		<li><a href="#"><i class="glyphicon glyphicon-list"></i>View</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<input type="hidden" name="pickNo" id="pickNo" value="<?=$pickNo;?>" />
			<h3 class="box-title">Picking No : <b><?= $pickNo; ?></b></h3>
			<div class="box-tools pull-right">
				<?php $statusName = '<b style="color: red;">Unknown</b>'; switch($hdr['statusCode']){
					case 'B' : $statusName = '<b style="color: blue;">Begin</b>'; break;
					case 'C' : $statusName = '<b style="color: blue;">Confirmed</b>'; break;
					case 'P' : $statusName = '<b style="color: green;">Approved</b>'; break;
					default : 
				} ?>
				<h3 class="box-title" id="statusName">Status : <?= $statusName; ?></h3>
			</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<input type="hidden" id="doNo" value="<?= $doNo; ?>" />
            <div class="row">				
					<div class="col-md-3">
						
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Pick Date : <b><?= date('d M Y',strtotime( $hdr['pickDate'] )); ?></b><br/>
						SO No : <b><?= $hdr['soNo']; ?></b><br/><input type="hidden" id="soNo" value="<?=$hdr['soNo'];?>" />		
					</div>	<!-- /.col-md-3-->	
					<div class="col-md-3">
						Remark : 
						<b><?= $hdr['remark']; ?></b>
					</div>	<!-- /.col-md-3-->	
			</div> <!-- row add items -->
		
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <?php
						$sql = "SELECT COUNT(id) as rowCount FROM picking_detail
								WHERE pickNo=:pickNo 
									";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':pickNo', $hdr['pickNo']);
						$stmt->execute();	
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
				  ?>
				  <span class="label label-primary">Total <?php echo $row['rowCount']; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
						$sql = "
						SELECT dtl.`id`, dtl.prodId, dtl.`issueDate`, dtl.`grade`, dtl.`qty`, dtl.`pickNo` 
						, prd.code as prodCode 
						FROM `picking_detail` dtl 
						LEFT JOIN product prd ON prd.id=dtl.prodId 
						WHERE 1
						AND dtl.`pickNo`=:pickNo 
						
						ORDER BY prd.code 
						";
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':pickNo', $hdr['pickNo']);
						$stmt->execute();	
						
						$sql = "
						SELECT dtl.`prodId`, dtl.`issueDate`, dtl.`grade`, ws.code as shelfCode, ws.name as shelfName
						, prd.code as prodCode 
						FROM `picking_detail` dtl 		
						INNER JOIN product_item itm ON itm.prodCodeId=dtl.prodId AND itm.issueDate=dtl.issueDate AND itm.grade=dtl.grade 						
						INNER JOIN receive_detail rDtl on  itm.prodItemId=rDtl.prodItemId  AND rDtl.statusCode='A'  
						INNER JOIN wh_shelf_map_item wmi on wmi.recvProdId=rDtl.id 
						INNER JOIN wh_shelf ws ON wmi.shelfId=ws.id 
						LEFT JOIN product prd ON prd.id=itm.prodCodeId 
						WHERE 1 
						AND dtl.`pickNo`=:pickNo 
						
						ORDER BY dtl.id 
						";
						$stmt2 = $pdo->prepare($sql);	
						$stmt2->bindParam(':pickNo', $hdr['pickNo']);
						$stmt2->execute();	
				   ?>	
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Product Name</th>
							<th>Issue Date</th>
							<th>Grade</th>
							<th>Qty</th>
							<th>Shelf</th>
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) { 
						?>
							<tr>
								<td style="text-align: center;"><?= $row_no; ?></td>
								<td><?= $row['prodCode']; ?></td>
								<td><?= $row['issueDate']; ?></td>
								<td><?= $row['grade']; ?></td>
								<td><?= $row['qty']; ?></td>
								<td colspan="3"><small>
								<?php $shelfCount=0; while ($row2 = $stmt2->fetch()) { 
									if($row['prodId']==$row2['prodId']){
										echo $row2['shelfName'].', ';
										$shelfCount+=1;
									} 
									if($shelfCount >= 10 ) exit;?>
								<?php }// end while ?>
								</small></td>
							</tr>
						<?php $row_no+=1; } ?>
					</table>
					<!-- for automatic close SO No. -->
					<input type="hidden" name="isClose" id="isClose" value="<?=($remainTotal<=0?'Yes':'No'); ?>" />
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->
		
	<div class="row">
		<div class="col-md-4">
					
		</div>
		<div class="col-md-4">
					
		</div>
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-4">
					Create By : </br>
					Create Time : </br>
					Confirm By : </br>
					Confirm Time : </br>
					Approve By : </br>
					Approve Time : 		
				</div>
				<div class="col-md-8">
					<label class=""><?php echo $hdr['createByName']; ?></label></br>
					<label class=""><?php echo date('d M Y H:m',strtotime( $hdr['createTime'] )); ?></label></br>
					<label class=""><?php echo $hdr['confirmByName']; ?></label></br>
					<label class=""><?php echo date('d M Y H:m',strtotime( $hdr['confirmTime'] )); ?></label></br>
					<label class=""><?php echo $hdr['approveByName']; ?></label></br>
					<label class=""><?php echo date('d M Y H:m',strtotime( $hdr['approveTime'] )); ?></label>	
				</div>				
			</div>			
		</div>
	</div>
	<!-- /.row -->
	
	
    </div><!-- /.box-body -->
  <div class="box-footer">
    <div class="col-md-12">
			<?php if($hdr['statusCode']=='P'){ ?>
			  <a href="<?=$rootPage;?>_view_pdf.php?pickNo=<?=$hdr['pickNo'];?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</a>
			<?php } ?>
			
			
			
		  <?php switch($s_userGroupCode){ case 'it' : case 'admin' : case 'whSup' : ?>
			  <button type="button" id="btn_approve" class="btn btn-success pull-right" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?>>
			 <i class="glyphicon glyphicon-check">
				</i> Approve
			  </button>
		  <button type="button" id="btn_reject" class="btn btn-warning pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?>>
		  <i class="glyphicon glyphicon-remove">
			</i> Reject
          </button>
		  <?php break; default : } ?>
		  
          <button type="button" id="btn_verify" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-ok"></i> Confirm
          </button>   
			<button type="button" id="btn_delete" class="btn btn-danger pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']<>'P'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-trash"></i> Delete
          </button>
	</div><!-- /.col-md-12 -->
  </div><!-- box-footer -->
</div><!-- /.box -->

<div id="spin"></div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>
  
  <!--AUDIO-->
  <audio id="audioSuccess" src="..\asset\sound\game-sound-effects-success-cute.wav" type="audio/wav"></audio>   

</div>
<!-- ./wrapper -->

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
// to start and stop spiner.  
$( document ).ajaxStart(function() {
	$("#spin").show();
}).ajaxStop(function() {
	$("#spin").hide();
});
//   

$(document).ready(function() {
// Append and Hide spinner.          
var spinner = new Spinner().spin();
$("#spin").append(spinner.el);
$("#spin").hide();
//           
$('#btn_verify').click (function(e) {				 
	var params = {					
	pickNo: $('#pickNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Confirm ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_verify_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});		
				location.reload();
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

$('#btn_reject').click (function(e) {				 
	var params = {					
	pickNo: $('#pickNo').val()					
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Reject ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_reject_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});		
				location.reload();
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

$('#btn_approve').click (function(e) {				 
	var params = {					
	pickNo: $('#pickNo').val()
	};
	$.smkConfirm({text:'Are you sure to Approve ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_approve_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});
				$('#audioSuccess').get(0).play();
				alert('Success.');
				window.location.href = "<?=$rootPage;?>_view.php?pickNo=" + data.pickNo;
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

$('#btn_delete').click (function(e) {				 
	var params = {					
	pickNo: $('#pickNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Delete ?', accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_delete_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				alert(data.message);
				window.location.href = 'picking.php';
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

	$("html,body").scrollTop(0);
	$("#statusName").fadeOut('slow').fadeIn('slow').fadeOut('slow').fadeIn('slow');
});
</script>



<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
