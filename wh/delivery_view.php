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
$rootPage="delivery";	
$doNo = $_GET['doNo'];

$sql = "
SELECT dh.`doNo`, dh.`soNo`, dh.`ppNo`, oh.`poNo`
, dh.`deliveryDate`, dh.`remark`
, dh.`statusCode`, dh.`createTime`, dh.`createById`, dh.`updateTime`, dh.`updateById`
, dh.`confirmTime`, dh.`confirmById`, dh.`approveTime`, dh.`approveById`
, ct.code as custCode, ct.name as  custName ,ct.addr1 , ct.addr2 , ct.addr3 , ct.zipcode, ct.tel, ct.fax
, concat(sm.name, '  ', sm.surname) as smFullname 
, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
FROM delivery_header dh 
LEFT JOIN prepare pp on pp.ppNo=dh.ppNo 
LEFT JOIN picking pk on pk.pickNo=pp.pickNo 
LEFT JOIN sale_header oh on pk.soNo=oh.soNo 
LEFT JOIN customer ct on ct.id=oh.custId
LEFT JOIN salesman sm on sm.id=oh.smId 
LEFT JOIN wh_user uca on uca.userId=dh.createById					
LEFT JOIN wh_user ucf on ucf.userId=dh.confirmById
LEFT JOIN wh_user uap on uap.userId=dh.approveById
WHERE 1
AND dh.doNo=:doNo
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':doNo', $doNo);	
$stmt->execute();
$hdr = $stmt->fetch();
$doNo = $hdr['doNo'];
$ppNo = $hdr['ppNo'];
$soNo = $hdr['soNo'];


$sql = "SELECT COUNT(id) as rowCount FROM delivery_detail
		WHERE doNo=:doNo 
			";						
$stmt = $pdo->prepare($sql);	
$stmt->bindParam(':doNo', $hdr['doNo']);
$stmt->execute();	
$rowCount = $stmt->fetch(PDO::FETCH_ASSOC);


$sql = "
SELECT dd.`id`, itm.`prodCodeId`, itm.`qty`
,pd.code as prodCode, pd.uomCode,dd.remark 
, IFNULL((SELECT SUM(sd.qty) FROM sale_detail sd
   		INNER JOIN sale_header sh on sh.soNo=sd.soNo
   		WHERE sh.soNo=pk.soNo
   		AND sd.prodId=itm.prodCodeId),0) AS sumSalesQty
, (SELECT IFNULL(SUM(itms.qty),0) FROM delivery_header dhs 
	INNER JOIN delivery_detail dds on dhs.doNo=dds.doNo
	INNER JOIN product_item itms ON itms.prodItemId=dds.prodItemId 
   	INNER JOIN prepare pps on pps.ppNo=dhs.ppNo
    INNER JOIN picking pks on pks.pickNo=pps.pickNo
    WHERE pks.soNo=pk.soNo 
    AND itms.prodCodeId=itm.prodCodeId
    AND dhs.statusCode='P' ) as sumSentQty
, IFNULL(SUM(itm.qty),0) as sumDeliveryQty 
FROM delivery_detail dd
INNER JOIN delivery_header dh on dh.doNo=dd.doNo 
LEFT JOIN product_item itm ON itm.prodItemId=dd.prodItemId 
INNER JOIN prepare pp on pp.ppNo=dh.ppNo
INNER JOIN picking pk on pk.pickNo=pp.pickNo
INNER JOIN sale_header oh on oh.soNo=pk.soNo
LEFT JOIN product pd on pd.id=itm.prodCodeId
WHERE 1
AND dh.doNo=:doNo
GROUP BY dd.`id`, dd.`prodCode`, dd.`qty`, pd.uomCode

ORDER BY dd.`id`, dd.`prodCode`, dd.`qty`, pd.name 
";
$stmt = $pdo->prepare($sql);	
$stmt->bindParam(':doNo', $hdr['doNo']);
$stmt->execute();

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
       <h1><i class="glyphicon glyphicon-send"></i>
       Delivery Order
        <small>Delivery Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Delivery Order List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?ppNo=<?=$ppNo;?>"><i class="glyphicon glyphicon-edit"></i>Delivery Order No.<?=$doNo;?></a></li>
		<li><a href="#"><i class="glyphicon glyphicon-list"></i>View</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">		
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<input type="hidden" name="doNo" id="doNo" value="<?=$doNo;?>" />
			<h3 class="box-title">Delivery Order No : <b><?= $doNo; ?></b></h3>
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
						Salesman : <br/>
						<b><?= $hdr['smFullname']; ?></b>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Customer : <br/>
						<b><?= $hdr['custName']; ?></b><br/>
						<?= $hdr['addr1']; ?><br/>
						<?= $hdr['addr2']; ?><br/>
						<?= $hdr['addr3']; ?>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Delivery Date : <b><?= $hdr['deliveryDate']; ?></b><br/>
						Packing No : <b><?= $hdr['ppNo']; ?></b><br/><input type="hidden" id="ppNo" value="<?=$hdr['ppNo'];?>" />		
						SO No : <b><?= $hdr['soNo']; ?></b><br/><input type="hidden" id="soNo" value="<?=$hdr['soNo'];?>" />		
						PO No : <b><?= $hdr['poNo']; ?></b><br/>		
					</div>	<!-- /.col-md-3-->	
					<div class="col-md-3">
						
					</div>	<!-- /.col-md-3-->	
			</div> <!-- row add items -->
		
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <span class="label label-primary">Total <?php echo $rowCount['rowCount']; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Product Code</th>
							<th style="text-align: right;">Sales Qty</th>
							<th style="text-align: right;">Sent Qty</th>
							<th style="text-align: right;">Delivery Qty</th>
							<th>Remark</th>
							<th style="text-align: right;">Remain Qty</th>
						</tr>
						<?php $remainTotal=0; $row_no=1; while ($row = $stmt->fetch()) { ?>						
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>
							<td><?= $row['prodCode']; ?></td>
							<td style="text-align: right;"><?= number_format($row['sumSalesQty'],0,'.',',').'&nbsp;'.$row['uomCode']; ?></td>
							<td style="text-align: right;"><?= number_format($row['sumSentQty'],0,'.',',').'&nbsp;'.$row['uomCode']; ?></td>
							<td style="text-align: right; color: blue; font-weight: bold;"><?= number_format($row['sumDeliveryQty'],0,'.',',').'&nbsp;'.$row['uomCode']; ?></td>
							<td><?= $row['remark']; ?></td>
							<td style="text-align: right; color: red;"><?= number_format(($row['sumSalesQty']-($row['sumSentQty']+$row['sumDeliveryQty'])),0,'.',',').'&nbsp;'.$row['uomCode']; ?></td>
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
					<label class=""><?php echo to_thai_datetime_fdt($hdr['createTime']); ?></label></br>
					<label class=""><?php echo $hdr['confirmByName']; ?></label></br>
					<label class=""><?php echo to_thai_datetime_fdt($hdr['confirmTime']); ?></label></br>
					<label class=""><?php echo $hdr['approveByName']; ?></label></br>
					<label class=""><?php echo to_thai_datetime_fdt($hdr['approveTime']); ?></label>	
				</div>				
			</div>			
		</div>
	</div>
	<!-- /.row -->
	
	
    </div><!-- /.box-body -->
  <div class="box-footer">
    <div class="col-md-12">
    		<?php if($hdr['statusCode']=='P'){ ?>
			  <a href="<?=$rootPage;?>_view_pdf.php?doNo=<?=$hdr['doNo'];?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</a>
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
            <i class="glyphicon glyphicon-ok"></i> Verify
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
	doNo: $('#doNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Verify ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
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
	}else{ 
		$.smkAlert({ text: 'Cancelled', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

$('#btn_reject').click (function(e) {				 
	var params = {					
	doNo: $('#doNo').val()					
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
	}else{ 
		$.smkAlert({ text: 'Cancelled', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

$('#btn_approve').click (function(e) {				 
	var params = {					
	doNo: $('#doNo').val(),
	soNo: $('#soNo').val(),
	isClose: $('#isClose').val()
	};
	<?php if($remainTotal==0){ ?>
		alert('Sales Order No. <?=$hdr['soNo'];?> will Close automatically.');
	<?php } ?>
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
				window.location.href = "<?=$rootPage;?>_view.php?doNo=" + data.doNo;
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
	doNo: $('#doNo').val()				
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
	}else{ 
		$.smkAlert({ text: 'Cancelled', type: 'info', position:'top-center'});	
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
