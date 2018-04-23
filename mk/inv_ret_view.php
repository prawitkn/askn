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
<?php include 'head.php'; /*$s_userID=$_SESSION['userID'];
		$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$rootPage='inv_ret';		

$docNo = $_GET['docNo'];

switch($s_userGroupCode){ 
	case 'it' : 
	case 'admin' : 
	case 'warehouse' :
		break;
	case 'production' :
		 header("Location: access_denied.php"); exit();	
		break;
	default :
}			
	$sql = "SELECT hdr.`docNo`, hdr.`refNo`, hdr.`docDate`, hdr.`custCode`, hdr.`smCode`, hdr.`totalExcVat`, hdr.`vatAmount`, hdr.`totalIncVat`
	, hdr.`remark`, hdr.`statusCode`
	, hdr.`createTime`, hdr.`createByID`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
	, ct.custName, ct.custAddr, ct.taxId, ct.creditDay 
	, concat(sm.name, '  ', sm.surname) as smFullname 
	, sh.soNo, sh.poNo 
	, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
	FROM `inv_ret` hdr
	LEFT JOIN invoice_header inv on inv.invNo=hdr.refNo 
	LEFT JOIN  delivery_header dh on dh.doNo=inv.doNo 			
	LEFT JOIN  prepare pa on pa.ppNo=dh.ppNo 				
	LEFT JOIN  picking pi on pi.pickNo=pa.pickNo
	LEFT JOIN sale_header sh on sh.soNo=pi.soNo 
	LEFT JOIN customer ct on ct.code=hdr.custCode ";
	switch($s_userGroupCode){
		case 'it' : case 'admin' : 
			break;
		case 'sales' : $sql .= " AND ct.smCode=:s_smCode "; break;
		case 'salesAdmin' : 	$sql .= " AND ct.smAdmCode=:s_smCode "; break;
		default : 
			//return JSON
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
			exit();
	}		
	$sql .= "
	LEFT JOIN salesman sm on sm.code=hdr.smCode 
	LEFT JOIN user uca on hdr.createByID=uca.userID
	LEFT JOIN user ucf on hdr.confirmByID=ucf.userID
	LEFT JOIN user uap on hdr.approveByID=uap.userID

	WHERE 1 
	AND hdr.docNo=:docNo ";
			
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':docNo', $docNo);	
	switch($s_userGroupCode){
		case 'it' : case 'admin' : 
			break;
		case 'sales' : $stmt->bindParam(':s_smCode', $s_smCode);
			break;
		case 'salesAdmin' : $stmt->bindParam(':s_smCode', $s_smCode);
			break;
		default : 
	}	
	$stmt->execute();
	$hdr = $stmt->fetch();
	$docNo = $hdr['docNo'];
	//$refNo = $hdr['refNo'];	

?>
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="plugins/iCheck/all.css">

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
	  <h1><i class="glyphicon glyphicon-arrow-left"></i>
       Customer Return
        <small>Customer Return management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Customer Return List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?docNo=<?=$docNo;?>"><i class="glyphicon glyphicon-edit"></i>No.<?=$docNo;?></a></li>
		<li><a href="#"><i class="glyphicon glyphicon-list"></i>View</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">View Customer Return No : <b><?= $docNo; ?></b></h3>
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
			<input type="hidden" id="docNo" value="<?= $docNo; ?>" />
            <div class="row">				
					<div class="col-md-3">
						Customer : <br/>
						<b><?= $hdr['custCode'].' : '.$hdr['custName']; ?></b><br/>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Salesman : <br/>
						<b><?= $hdr['smCode'].' : '.$hdr['smFullname']; ?></b><br/>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Return Date : <br/>
						<b><?= $hdr['docDate']; ?></b><br/>
						Ref. Invoice No. : <br/>
						<b><?= $hdr['refNo']; ?></b><br/>
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
						$sql = "SELECT dtl.`id`, dtl.`prodItemId`, dtl.`prodCode`, dtl.`returnReasonCode`, dtl.`returnReasonRemark`
						, itm.barcode, itm.qty 
						, rrt.name as returnReasonName 
						FROM `inv_ret_detail` dtl	
						LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 
						LEFT JOIN wh_inv_return_reason_type rrt on rrt.code=dtl.returnReasonCode 
						WHERE 1
						AND dtl.docNo=:docNo  
						";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':docNo', $docNo);		
						$stmt->execute();
						$rowCount = $stmt->rowCount();					
				  ?>
				  <span class="label label-primary">Total <?php echo $rowCount; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
			/*$sql = "SELECT dtl.`id`, dtl.`prodItemId`, dtl.`prodId`, dtl.`prodCode`, dtl.`barcode`, dtl.`issueDate`, dtl.`machineId`, dtl.`seqNo`, dtl.`NW`, dtl.`GW`
			, dtl.`qty`, dtl.`packQty`, dtl.`grade`, dtl.`gradeDate`, dtl.`refItemId`, dtl.`itemStatus`, dtl.`remark`, dtl.`problemId`
			, dtl.`returnReasonCode`, dtl.`returnReasonRemark`, dtl.`rtNo` 
			, rrt.name as returnReasonName 
			FROM `rt_detail` dtl	
			LEFT JOIN wh_return_reason_type rrt on rrt.code=dtl.returnReasonCode 
			WHERE 1
			AND dtl.rtNo=:rtNo  
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':rtNo', $rtNo);		
			$stmt->execute();
			$rowCount = $stmt->rowCount()*/
			
			
				   ?>	
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Product Code</th>
							<th>Barcode</th>
							<th>Qty</th>
							<th>Return Type</th>
							<th>Return Remark</th>
							<th>#</th>
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) { ?>
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>
							<td><?= $row['prodCode']; ?></td>	
						<td><?= $row['barcode']; ?></td>	
						<td style="text-align: right;"><?=number_format($row['qty'],0,'.',','); ?></td>
						<td><?= $row['returnReasonName']; ?></td>	
						<td><?= $row['returnReasonRemark']; ?></td>	
						</tr>
						<?php $row_no+=1; } ?>
					</table>
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->

			
			
          
    
    </div><!-- /.box-body -->
  <div class="box-footer">
    <div class="col-md-12">
		<?php if($hdr['statusCode']=='P'){ ?>
          <a href="<?=$rootPage;?>_view_pdf.php?docNo=<?=$docNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</a>
		<?php } ?>
	
		
		  
		  <?php //switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : ?>
          <button type="button" id="btn_approve" class="btn btn-success pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?> >
		 <i class="glyphicon glyphicon-check">
			</i> Approve
          </button>
		  
		  <button type="button" id="btn_reject" class="btn btn-warning pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?>>
		  <i class="glyphicon glyphicon-remove">
			</i> Reject
          </button>
		  <?php //break; default : } ?>
		  
          <button type="button" id="btn_confirm" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-ok"></i> Confirm
          </button>      
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
$('#btn_confirm').click (function(e) {				 
	var params = {					
	docNo: $('#docNo').val()			
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Confirm ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_confirm_ajax.php',
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
	docNo: $('#docNo').val()					
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
	docNo: $('#docNo').val()				
	};
	//alert(params.hdrID);
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
				window.location.href = "<?=$rootPage;?>_view.php?docNo=" + data.docNo;
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
	docNo: $('#docNo').val()				
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
