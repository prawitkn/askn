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
<?php include 'head.php';/*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
$rootPage='receive';

$rcNo = $_GET['rcNo'];
$sql = "SELECT rc.`rcNo`, rc.`refNo`, rc.`receiveDate`, rc.`fromCode`, rc.`remark`, rc.`statusCode`
, rc.`createTime`, rc.`createByID`, rc.`confirmTime`, rc.`confirmById`, rc.`approveTime`, rc.`approveById` 
, fsl.name as fromName, tsl.name as toName
, d.userFullname as createByName
, rc.confirmTime, cu.userFullname as confirmByName
, rc.approveTime, au.userFullname as approveByName
FROM `receive` rc 
LEFT JOIN sloc fsl on rc.fromCode=fsl.code 
LEFT JOIN sloc tsl on rc.toCode=tsl.code 
left join wh_user d on rc.createById=d.userId
left join wh_user cu on rc.confirmById=cu.userId
left join wh_user au on rc.approveById=au.userId
WHERE 1
AND rc.rcNo=:rcNo 					
ORDER BY rc.createTime DESC
LIMIT 1
		
";
$stmt = $pdo->prepare($sql);			
$stmt->bindParam(':rcNo', $rcNo);	
$stmt->execute();
if($stmt->rowCount()==0){
	header("Location: access_denied.php"); exit();
}
$hdr = $stmt->fetch();			
$rcNo = $hdr['rcNo'];
?>
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="plugins/iCheck/all.css">
 
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	
  
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="glyphicon glyphicon-arrow-down"></i>
       Receive
        <small>Receive management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Receive List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?rcNo=<?=$rcNo;?>"><i class="glyphicon glyphicon-edit"></i>RC No.<?=$rcNo;?></a></li>
		<li><a href="#"><i class="glyphicon glyphicon-list"></i>View</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">Receive No : <b><?= $rcNo; ?></b></h3>
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
			<input type="hidden" id="rcNo" value="<?= $rcNo; ?>" />
            <div class="row">				
					<div class="col-md-3">
						From : <br/>
						<b><?= $hdr['fromName']; ?></b><br/>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						To : <br/>
						<b><?= $hdr['toName']; ?></b><br/>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Receive Date : <br/>
						<b><?= date('d M Y',strtotime( $hdr['receiveDate'] )); ?></b><br/>
						Sending No. : <br/>
						<b><?= $hdr['refNo']; ?></b><br/>
					</div>	<!-- /.col-md-3-->	
					<div class="col-md-3">
						Remark : 
						<b><?= $hdr['remark']; ?></b>
					</div>	<!-- /.col-md-3-->	
			</div> <!-- row add items -->
		
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Product List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <?php
						$sql = "SELECT id FROM receive_detail
								WHERE rcNo=:rcNo 
									";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':rcNo', $hdr['rcNo']);
						$stmt->execute();	
						$rowCount = $stmt->rowCount();
				  ?>
				  <span class="label label-primary">Total <?php echo $rowCount; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
						$sql = "
						SELECT dtl.`id`, dtl.`prodItemId`, itm.`prodId`, itm.`barcode`, itm.`issueDate`
						, itm.`NW`, itm.`GW`, itm.`qty`, itm.`grade`, itm.`refItemId`, itm.`itemStatus`
						, itm.`gradeTypeId`, itm.`remarkWh`
						,prd.code as prodCode 
						, igt.name as gradeTypeName 
						, dtl.`statusCode`, dtl.`shelfCode`, dtl.`rcNo` 
						, ws.name as shelfName 
						FROM `receive_detail` dtl
						LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
						LEFT JOIN product prd ON prd.id=itm.prodCodeId
						LEFT JOIN wh_sloc ws on ws.code=dtl.shelfCode 
						LEFT JOIN product_item_grade_type igt ON igt.id=itm.gradeTypeId 
						WHERE 1=1 
						AND dtl.`rcNo`=:rcNo 
						ORDER BY  itm.`barcode`
						";
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':rcNo', $hdr['rcNo']);
						$stmt->execute();	
				   ?>	
					<table class="table table-striped">
						<tr>
							<th>No.</th>							
							<th>Product Code</th>
							<th>barcode</th>
							<th>Grade</th>
							<th>Net<br/>Weight(kg.)</th>
							<th>Gross<br/>Weight(kg.)</th>
							<th>Qty</th>
							<th>Issue Date</th>							
							<th>Grade Type</th>
							<th>Send Remark</th>
							<th>Is Return</th>
						</tr>
						<?php $row_no=1;  $sumQty=$sumNW=$sumGW=$sumGradeNotOk=0;  while ($row = $stmt->fetch()) { 
							$isReturn = "";
							if($row['statusCode']=='R') { $isReturn = '<label class="label label-danger">Yes</label>'; }
							
							$gradeName = '<b style="color: red;">N/A</b>'; 
							switch($row['grade']){
								case 0 : $gradeName = 'A'; break;
								case 1 : $gradeName = '<b style="color: red;">B</b>'; $sumGradeNotOk+=1; break;
								case 2 : $gradeName = '<b style="color: red;">N</b>'; $sumGradeNotOk+=1; break;
								default : 
									$gradeName = '<b style="color: red;">N/a</b>'; $sumGradeNotOk+=1;
							} 
						?>
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>							
							<td><?= $row['prodCode']; ?></td>
							<td><?= $row['barcode']; ?></td>
							<td style="text-align: center;"><?= $gradeName; ?></td>	
							<td style="text-align: right;"><?= number_format($row['NW'],2,'.',','); ?></td>	
							<td style="text-align: right;"><?= number_format($row['GW'],2,'.',','); ?></td>	
							<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
							<td><?= date('d M Y',strtotime( $row['issueDate'] )); ?></td>	
							<td><?= $row['gradeTypeName']; ?></td>
							<td><?= $row['remarkWh']; ?></td>		
							<td><?= $isReturn; ?></td>
						</tr>
						<?php $row_no+=1;  $sumQty+=$row['qty'] ; $sumNW+=$row['NW']; $sumGW+=$row['GW'] ;  } ?>
						<tr style="font-weight: bold;">
							<td></td>
							<td colspan="3">Total</td>	
							<td style="text-align: right;"><?= number_format($sumNW,2,'.',','); ?></td>
							<td style="text-align: right;"><?= number_format($sumGW,2,'.',','); ?></td>
							<td style="text-align: right;"><?= number_format($sumQty,0,'.',','); ?></td>
							<td></td>	
							<td></td>	
							<td></td>	
							<td>
								
							</td>
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
		<?php if($hdr['statusCode']=='P'){ ?>
          <a target="_blank" href="<?=$rootPage;?>_view_pdf.php?rcNo=<?=$hdr['rcNo'];?>" class="btn btn-primary"><i class="glyphicon glyphicon-print"></i> Print</a>
		  <?php switch($s_userGroupCode){ case 'admin' : case 'whSup' : case 'whMgr' : ?>
				<a href="<?=$rootPage;?>_set_shelf.php?rcNo=<?=$hdr['rcNo'];?>" class="btn btn-default"><i class="glyphicon glyphicon-object-align-bottom"></i> Shelf</a>

				<a target="" href="<?=$rootPage;?>_edit.php?rcNo=<?=$hdr['rcNo'];?>" class="btn btn-default"><i class="glyphicon glyphicon-edit"></i> Edit Item Grade Type</a>
			<?php break; default : } ?>
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

$('#btn_delete').click (function(e) {				 
	var params = {
	action: 'delete',
	rcNo: $('#rcNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Delete ?', accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
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
       
$('#btn_verify').click (function(e) {				 
	var params = {	
	action: 'confirm',				
	rcNo: $('#rcNo').val()			
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Confirm ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
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
	action: 'reject',				
	rcNo: $('#rcNo').val()					
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Reject ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
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
	action: 'approve',		
	rcNo: $('#rcNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Approve ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
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
				window.location.href = '<?=$rootPage;?>_view.php?rcNo=' + data.rcNo;
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
