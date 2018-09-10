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
<?php include 'head.php'; 

$rootPage="send";

$sdNo = $_GET['sdNo'];
$sql = "SELECT hdr.`sdNo`, hdr.`refNo`, hdr.`sendDate`, hdr.`fromCode`, hdr.toCode, hdr.rcNo, hdr.remark, hdr.`statusCode`
, hdr.`createTime`, hdr.`createById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
, fsl.name as fromName, tsl.name as toName
, d.userFullname as createByName
, hdr.confirmTime, cu.userFullname as confirmByName
, hdr.approveTime, au.userFullname as approveByName
FROM `send` hdr
LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
LEFT JOIN sloc tsl on hdr.toCode=tsl.code
left join wh_user d on hdr.createById=d.userId
left join wh_user cu on hdr.confirmById=cu.userId
left join wh_user au on hdr.approveById=au.userId
WHERE 1
AND hdr.sdNo=:sdNo 					
ORDER BY hdr.createTime DESC
LIMIT 1
		
";
$stmt = $pdo->prepare($sql);			
$stmt->bindParam(':sdNo', $sdNo);	
$stmt->execute();
if($stmt->rowCount()==0){
	header("Location: access_denied.php"); exit();
}
$hdr = $stmt->fetch();			
$sdNo = $hdr['sdNo'];
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
    <section class="content-header"  style="color: red;">	  
	  <h1><i class="glyphicon glyphicon-eject"></i>
       Send
        <small>Send management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Send List</a></li>		
		<li><a href="<?=$rootPage;?>_add.php?sdNo=<?=$sdNo;?>"><i class="glyphicon glyphicon-edit"></i><?=$sdNo;?></a></li>
		<li><a href="<?=$rootPage;?>_view.php?sdNo=<?=$sdNo;?>"><i class="glyphicon glyphicon-search"></i> View</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">View Sending No : <b><?= $sdNo; ?><?php if($hdr['rcNo']<>'') { ?> / <small style="color: red;"><?=$hdr['rcNo'];?></small> <?php } ?></b></h3>
			<div class="box-tools pull-right">
				<?php $statusName = '<b style="color: red;">Unknown</b>'; switch($hdr['statusCode']){
					case 'A' : $statusName = '<b style="color: red;">Incompleate</b>'; break;
					case 'B' : $statusName = '<b style="color: blue;">Begin</b>'; break;
					case 'C' : $statusName = '<b style="color: blue;">Confirmed</b>'; break;
					case 'P' : $statusName = '<b style="color: green;">Approved</b>'; break;
					default : 
				} ?>
				<h3 class="box-title" id="statusName">Status : <?= $statusName; ?></h3>
			</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<input type="hidden" id="sdNo" value="<?= $sdNo; ?>" />
            <div class="row">				
					<div class="col-md-3">
						From : <br/>
						<b><?= $hdr['fromCode'].' : '.$hdr['fromName']; ?></b>						
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						To : <br/>
						<b><?= $hdr['toCode'].' : '.$hdr['toName']; ?></b><br/>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Issue Date : <br/>
						<b><?= date('d M Y',strtotime( $hdr['sendDate'] )); ?></b><br/>
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
						$sql = "SELECT count(*) as countTotal FROM send_detail
								WHERE sdNo=:sdNo 
									";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':sdNo', $hdr['sdNo']);
						$stmt->execute();	
						$rc = $stmt->fetch();						
				  ?>
				  <span class="label label-primary">Total <?php echo $rc['countTotal']; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
						$sql = "SELECT dtl.refNo, dtl.id, dtl.prodItemId 
						, itm.barcode, itm.grade, itm.qty, itm.issueDate , itm.`gradeTypeId`, itm.`remarkWh`
						, prd.code as prodCode 
						, igt.name as gradeTypeName 
						FROM send_detail dtl 
						LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 
						LEFT JOIN product prd ON prd.id=itm.prodCodeId 
						LEFT JOIN product_item_grade_type igt ON igt.id=itm.gradeTypeId 
						WHERE sdNo=:sdNo  
						ORDER BY dtl.refNo, itm.barcode
						";			
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':sdNo', $hdr['sdNo']);
						$stmt->execute();	
				   ?>	
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Product Code</th>
							<th>Barcode</th>
							<th>Grade</th>
							<th>Qty</th>
							<th>Produce Date</th>
							<th>Grade Type</th>
							<th>Send Remark</th>
						</tr>
						<?php $row_no=1; $sumQty=0; $sumGradeNotOk=0; while ($row = $stmt->fetch()) { $sumQty+=$row['qty']; 
							$gradeName = '<b style="color: red;">N/A</b>'; 
							switch($row['grade']){
								case 0 : $gradeName = 'A'; break;
								case 1 : $gradeName = '<b style="color: red;">B</b>'; break;
								case 2 : $gradeName = '<b style="color: red;">N</b>'; $sumGradeNotOk+=1; break;
								default : 
									$sumGradeNotOk+=1;
							} //$sumGradeNotOk=0;
						?>
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>
							<td><?= $row['prodCode']; ?></td>
							<td><?= $row['barcode']; ?></td>
							<td><?= $gradeName; ?></td>
							<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
							<td><?= date('d M Y',strtotime( $row['issueDate'] )); ?></td>	
							<td><?= $row['gradeTypeName']; ?></td>
							<td><?= $row['remarkWh']; ?></td>							
						</tr>
						<?php $row_no+=1; } ?>
						<tr style="font-weight: bold;">
							<td style="text-align: center;"></td>
							<td colspan="3">Total</td>
							<td style="text-align: right;"><?= number_format($sumQty,0,'.',','); ?></td>
							<td></td>							
						</tr>
					</table>
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->			
			
    
    <div class="row">
		<div class="col-md-6">
				Create By : <label class=""><?= $hdr['createByName']; ?></label></br>
				Create Time : <label class=""><?= date('d M Y H:i',strtotime( $hdr['createTime'] )); ?></label></br>
				Confirm By : <label class=""><?= $hdr['confirmByName']; ?></label></br>
				Confirm Time : <label class=""><?= date('d M Y H:i',strtotime( $hdr['confirmTime'] )); ?></label>
		</div>
		<div class="col-md-4">
					
		</div>
		<div class="col-md-6">
			Approve By : <label class=""><?= $hdr['approveByName']; ?></label></br>
			Approve Time : <label class=""><?= date('d M Y H:i',strtotime( $hdr['approveTime'] )); ?></label>	
		</div>
	</div>
	<!-- /.row -->		      
    
    </div><!-- /.box-body -->
  <div class="box-footer">
    <div class="col-md-12">
		<?php if($hdr['statusCode']=='P' OR $hdr['statusCode']=='C'){ ?>
          <a target="_blank" href="<?=$rootPage;?>_view_pdf.php?sdNo=<?=$sdNo;?>" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> Print</a>		  
		  <button type="button" id="btn_approve_special" class="btn btn-danger" style="margin-right: 5px;" <?php echo ($hdr['rcNo']==''?'':'disabled'); ?> >
			<i class="glyphicon glyphicon-star"></i> Approve (Special)
		  </button>
		<?php } ?>
		<?php if($hdr['statusCode']=='P' AND $hdr['rcNo']==""){ ?>         
		  <button type="button" id="btn_remove" class="btn btn-default" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='P'?'':'disabled'); ?> >
		 <i class="glyphicon glyphicon-trash">
			</i> Remove Approved
          </button>
		<?php } ?>
	
		
		  
		  <?php switch($s_userGroupCode){ case 'admin' : case 'whSup' :  case 'pdSup' : case 'whMgr' : case 'pdMgr' : ?>
          <button type="button" id="btn_approve" class="btn btn-success pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?> >
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
		  </button>   
			<button type="button" id="btn_delete" class="btn btn-danger pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']<>'P'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-trash"></i> Delete
          </button>
		  
		  <!--Unused for auto mapping on sync. -->
		  <!--<button type="button" id="btn_mapping" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-link"></i> Prod.Mapping
          </button>-->
		  
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
  
  
  
  <!-- Modal -->
<div id="modal_pin" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">PIN to Approve</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="txt_reason" class="control-label col-md-4">PIN : </label>
				<div class="col-md-6">					
					<input type="text" class="form-control" id="txt_pin" />
				</div>
			</div>
		
		</form>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-danger" id="btn_pin_ok" >OK</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>
<!-- End Modal -->


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

//Super Approve Begin
$('#btn_approve_special').click(function(){
	$('#modal_pin').modal('show');
});			
$('#btn_pin_ok').click(function(){
	var params = {			
		action: 'approve_special',
		sdNo: $('#sdNo').val(),
		pin: $('#txt_pin').val()
	};	
	if(params.pin.trim()==""){
		alert('PIN is required.');
		$('#txt_pin').select();
		return false;
	}
	if (confirm('Are you sure to Special Approve ?')) {
		// Save it!
		$.post({
			url: 'send2_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});
				window.location.href = "<?=$rootPage;?>_view.php?sdNo=" + data.sdNo;
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
	} else {
		// Do nothing!
	}
});	
//Super Approve End


$('#btn_verify').click (function(e) {			
	<?php if($sumGradeNotOk>0){
			echo "alert('Please check GRADE before sending.'); return false; ";
	}?>	 
	var params = {
	action: 'confirm',					
	sdNo: $('#sdNo').val()			
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Confirm ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'send2_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});		
				setTimeout(function(){ window.location.href = '<?=$rootPage;?>.php'; }, 3000);
				//location.reload();
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
	action: 'reject',					
	sdNo: $('#sdNo').val()					
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Reject ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'send2_ajax.php',
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
	<?php if($sumGradeNotOk>0){
			echo "alert('Please check GRADE before sending.'); return false; ";
	}?>
	var params = {
	action: 'approve',					
	sdNo: $('#sdNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Approve ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'send2_ajax.php',
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
				window.location.href = "<?=$rootPage;?>_view.php?sdNo=" + data.sdNo;
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
	action: 'delete',
	sdNo: $('#sdNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Delete ?', accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'send2_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				alert(data.message);
				window.location.href = '<?=$rootPage;?>.php';
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

$('#btn_remove').click (function(e) {				 
	var params = {
	action: 'remove',					
	sdNo: $('#sdNo').val()					
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Remove Approved ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'send2_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});		
				alert(data.message);
				window.location.href = '<?=$rootPage;?>.php';
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
