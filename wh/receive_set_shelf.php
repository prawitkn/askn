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

$rootPage="receive";


$rcNo = $_GET['rcNo'];
$sql = "SELECT rc.`rcNo`, rc.`refNo`, rc.`receiveDate`, rc.`fromCode`, rc.`remark`, rc.`statusCode`
, rc.`createTime`, rc.`createById`, rc.`confirmTime`, rc.`confirmById`, rc.`approveTime`, rc.`approveById` 
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
$hdr = $stmt->fetch();			
$rcNo = $hdr['rcNo'];

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
      <h1><i class="glyphicon glyphicon-arrow-down"></i>
       Receive
        <small>Receive management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Receive List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?rcNo=<?=$rcNo;?>"><i class="glyphicon glyphicon-edit"></i>RC No.<?=$rcNo;?></a></li>
		<li><a href="<?=$rootPage;?>_view.php?rcNo=<?=$rcNo;?>"><i class="glyphicon glyphicon-list"></i>View</a></li>
		<li><a href="<?=$rootPage;?>_set_shelf.php?rcNo=<?=$rcNo;?>"><i class="glyphicon glyphicon-download-alt"></i>Set Shelf</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">View Receive No : <b><?= $rcNo; ?></b></h3>
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
						<b><?= $hdr['receiveDate']; ?></b><br/>
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
						SELECT dtl.`id`, dtl.`prodItemId`, itm.`prodCodeId`, itm.`barcode`, itm.`issueDate`
						, itm.`NW`, itm.`GW`, itm.`qty`, itm.`packQty`, itm.`grade`, itm.`gradeDate`  
						, dtl.`statusCode`, dtl.`isReturn`, dtl.`shelfCode`, dtl.`rcNo` 
						, ws.name as shelfName 
						,prd.code as prodCode , prd.name as prodName 
						FROM `receive_detail` dtl
						LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
						LEFT JOIN product prd ON prd.id=itm.prodCodeId 
						LEFT JOIN wh_sloc ws on ws.code=dtl.shelfCode 
						WHERE 1=1 
						AND dtl.`rcNo`=:rcNo 
						";
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':rcNo', $hdr['rcNo']);
						$stmt->execute();	
				   ?>	
					<table class="table table-striped">
						<tr>
							<th>No.</th>							
							<th>Product Code</th>
							<th>Barcode</th>
							<th>Grade</th>
							<th>Net<br/>Weight(kg.)</th>
							<th>Gross<br/>Weight(kg.)</th>
							<th>Qty</th>
							<th>Produce Date</th>
							<th>Is Return</th>
							<th>Shelf</th>
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) { 
							$isReturn = "";
							if($row['isReturn']=='Y') { $isReturn = '<label class="label label-danger">Yes</label>'; }
						?>
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>							
							<td><?= $row['prodCode']; ?></td>
							<td><?= $row['barcode']; ?></td>
							<td style="text-align: center;"><?= $row['grade']; ?></td>	
							<td style="text-align: right;"><?= $row['NW']; ?></td>	
							<td style="text-align: right;"><?= $row['GW']; ?></td>	
							<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
							<td><?= $row['issueDate']; ?></td>	
							<td><?= $isReturn; ?></td>
							<td>
								<input type="hidden" id="hid_shelf_code_<?=$row_no;?>" />
								<label id="lbl_shelf_name_<?=$row_no;?>"><?=$row['shelfName'];?></label><a href="receive_shelf_select.php?id=<?=$row['id'];?>" name="" class="btn btn-default btn_set_shelf">...</a>
							</td>						
						</tr>
						<?php $row_no+=1; } ?>
					</table>
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->

			
			
          
    
    </div><!-- /.box-body -->
  <div class="box-footer">
    <div class="col-md-12">
		<?php if($hdr['statusCode']=='P'){ ?>
          <a href="receive_view_shelf_pdf.php?rcNo=<?=$rcNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</a>
		<?php } ?>
	
		
		  
		 	      
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
	rcNo: $('#rcNo').val()			
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Verify ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'receive_confirm_ajax.php',
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
