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
<?php include 'head.php';  /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$rootPage='prepare';

$ppNo = $_GET['ppNo'];
	$sql = "
	SELECT hdr.`ppNo`, hdr.`pickNo`, hdr.`prepareDate`, hdr.`remark`, hdr.`statusCode`
	, pk.soNo 
	, cust.name as custName 
	, st.code as shipToCode, st.name as shipToName 
	, hdr.`createTime`, hdr.`createById`, hdr.`updateTime`, hdr.`updateById`
	, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`
						, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
	FROM prepare hdr
	INNER JOIN picking pk on pk.pickNo=hdr.pickNo 
	LEFT JOIN sale_header sh on sh.soNo=pk.soNo 
	LEFT JOIN customer cust on cust.id=sh.custId  
	LEFT JOIN shipto st on st.id=sh.shipToId  
	LEFT JOIN wh_user uca on uca.userId=hdr.createById					
	LEFT JOIN wh_user ucf on ucf.userId=hdr.confirmById
	LEFT JOIN wh_user uap on uap.userId=hdr.approveById
	WHERE 1
	AND hdr.ppNo=:ppNo
	";
	$stmt = $pdo->prepare($sql);			
	$stmt->bindParam(':ppNo', $ppNo);	
	$stmt->execute();
	$hdr = $stmt->fetch();	

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
      <h1><i class="glyphicon glyphicon-th-large"></i>
       Prepare
        <small>Prepare management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Prepare List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?ppNo=<?=$ppNo;?>"><i class="glyphicon glyphicon-edit"></i>Prepare No.<?=$ppNo;?></a></li>
		<li><a href="#"><i class="glyphicon glyphicon-list"></i>View</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<input type="hidden" name="ppNo" id="ppNo" value="<?=$ppNo;?>" />
			<h3 class="box-title">Prepare No : <b><?= $ppNo; ?></b></h3>
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
            <div class="row">				
					<div class="col-md-3">
						
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Prepare Date : <b><?= $hdr['prepareDate']; ?></b><br/>
						Picking No : <b><?= $hdr['pickNo']; ?></b><br/>		
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
						$sql = "SELECT COUNT(id) as rowCount FROM prepare_detail
								WHERE ppNo=:ppNo 
									";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':ppNo', $hdr['ppNo']);
						$stmt->execute();	
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
				  ?>
				  <span class="label label-primary">Total <?php echo $row['rowCount']; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
					$sql = "
					SELECT dtl.`id`, itm.prodCodeId as prodCode, itm.`barcode`, itm.`issueDate`, itm.`grade`, itm.`qty`, dtl.`ppNo` 
					, prd.code as prodCode 
					,IFNULL((SELECT COUNT(*) FROM picking_detail pickDtl 			
							WHERE pickDtl.pickNo=hdr.pickNo				
							AND itm.prodCodeId=pickDtl.prodId
							AND itm.issueDate=pickDtl.issueDate 
							AND itm.grade=pickDtl.grade 						
							),0) AS inPick 
					FROM `prepare_detail` dtl
					INNER JOIN prepare hdr ON hdr.ppNo=dtl.ppNo 
					LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					LEFT JOIN product prd ON prd.id=itm.prodCodeId 
					WHERE 1
					AND dtl.`ppNo`=:ppNo 
					
					ORDER BY prd.code 
					";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':ppNo', $hdr['ppNo']);
					$stmt->execute();
						
				   ?>
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Barcode</th>
							<th>Issue Date</th>
							<th>Grade</th>
							<th>Qty</th>
						</tr>
						<?php $wrongProduct=0; $row_no=1; while ($row = $stmt->fetch()) { 
						$gradeName = '<b style="color: red;">N/A</b>'; 
						switch($row['grade']){
							case 0 : $gradeName = 'A'; break;
							case 1 : $gradeName = '<b style="color: red;">B</b>'; break;
							case 2 : $gradeName = '<b style="color: red;">N</b>'; break;
							default : 
						} 
						?>
							<tr <?php if($row['inPick']==0 ) echo ' style="color: red;" '; $wrongProduct+=1; ?> >
								<td style="text-align: center;"><?= $row_no; ?></td>
								<td><?= $row['barcode']; ?></td>
								<td><?= $row['issueDate']; ?></td>
								<td><?= $gradeName; ?></td>
								<td><?= $row['qty']; ?></td>
							</tr>							
						<?php $row_no+=1; } ?>
					</table>
					
					<?php
						$sql = "
						SELECT prd.code as prodCode, itm.`issueDate`, itm.`grade`
						,IFNULL((SELECT SUM(pickDtl.qty) 
								FROM picking_detail pickDtl 
								WHERE pickDtl.pickNo=hdr.pickNo
								AND pickDtl.prodId=itm.prodCodeId 
								AND pickDtl.issueDate=itm.issueDate 
								AND pickDtl.grade=itm.grade 						
						),0) AS sumPickQty 
						, SUM(itm.`qty`) AS sumPackQty
						FROM `prepare_detail` dtl
						INNER JOIN prepare hdr ON hdr.ppNo=dtl.ppNo 
						LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
						LEFT JOIN product prd ON prd.id=itm.prodCodeId 
						WHERE 1
						AND dtl.`ppNo`=:ppNo 
						GROUP BY prd.`code`, itm.`issueDate`, itm.`grade`
												
						UNION 
						
						SELECT * FROM (
						SELECT prd.code as prodCode, pick.`issueDate`, pick.`grade`, IFNULL(SUM(pick.qty),0) as sumPickQty, 0 as sumPackQty 
						FROM picking_detail pick
						LEFT JOIN product prd ON prd.id=pick.prodId  
						WHERE pick.pickNo=(SELECT pickNo FROM prepare WHERE ppNo=:ppNo2)
						AND pick.prodId NOT IN (SELECT itm.prodCodeId FROM `prepare_detail` dtl
													INNER JOIN prepare hdr ON hdr.ppNo=dtl.ppNo 
													LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
													WHERE 1
													AND dtl.`ppNo`=:ppNo3  )		
										) as tmp 
						WHERE tmp.sumPackQty<>0 AND tmp.sumPackQty<>0 
						
						";
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':ppNo', $hdr['ppNo']);
						$stmt->bindParam(':ppNo2', $hdr['ppNo']);
						$stmt->bindParam(':ppNo3', $hdr['ppNo']);
						$stmt->execute();
						
				   ?>
				   <h3 class="box-title">Product Quantity Difference</h3>
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Product</th>
							<th>Issue Date</th>
							<th>Grade</th>
							<th>Picking Qty</th>
							<th>Packing Qty</th>
						</tr>
						<?php $diffQty=0; $row_no=1; while ($row = $stmt->fetch()) { 
						$gradeName = '<b style="color: red;">N/A</b>'; 
						switch($row['grade']){
							case 0 : $gradeName = 'A'; break;
							case 1 : $gradeName = '<b style="color: red;">B</b>'; break;
							case 2 : $gradeName = '<b style="color: red;">N</b>'; break;
							default : 
						} 
						?>
							<?php if($row['sumPickQty']<>$row['sumPackQty']) { ?> 
							<tr  style="color: red;" >
								<td style="text-align: center;"><?= $row_no; ?></td>
								<td><?= $row['prodCode']; ?></td>
								<td><?= $row['issueDate']; ?></td>
								<td><?= $gradeName; ?></td>
								<td><?= $row['sumPickQty']; ?></td>
								<td><?= $row['sumPackQty']; ?></td>
							</tr>							
						<?php $row_no+=1; $diffQty+=1; } //end if  ?>
						<?php } //end loop ?>
					</table>
				</div>
				<!-- /.box-body -->
				
	</div>
	<!-- /.row add items -->
		
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
			  <a target="_blank" href="<?=$rootPage;?>_view_pdf.php?ppNo=<?=$hdr['ppNo'];?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</a>
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

$('#btn_delete').click (function(e) {				 
	var params = {	
	action: 'delete',				
	ppNo: $('#ppNo').val()				
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
	ppNo: $('#ppNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Confirm ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
		$wrongProduct = <?=$wrongProduct;?>;
		$diffQty = <?=$diffQty;?>;
		if($wrongProduct>0){
			alert('Cannot Confirm because there is WRONG PRODUCT in packing list');
			//return false;
		}
		if($diffQty>0){
			alert('Cannot Confirm because there is DIFF QUANTITY in packing list');
			//return false;
		}
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
	ppNo: $('#ppNo').val()					
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
	ppNo: $('#ppNo').val()
	};
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
				window.location.href = "<?=$rootPage;?>_view.php?ppNo=" + data.ppNo;
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
