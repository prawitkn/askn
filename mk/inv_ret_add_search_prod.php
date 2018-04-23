<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 
	include 'inc_helper.php'; 

$rootPage="inv_ret";
	
?>

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
		<?php
			$docNo = $_GET['docNo'];
			$refNo = $_GET['refNo'];
		?>
      <h1>
       Return
        <small>Return management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Menu</a></li>
        <li class="active">Return</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <a href="<?=$rootPage;?>_add.php?docNo=<?=$docNo;?>" class="btn btn-google">Back</a>
    <div class="box box-primary">
		<?php	
			
			$reason = mysqli_query($link,"SELECT code, name FROM wh_inv_return_reason_type WHERE statusCode='A' ");	
			
		
			//$sql = "SELECT hdr.* 
			//FROM invoice hdr 
			//WHERE hdr.statusCode='P' AND hdr.invNo=:refNo 
			//";
			//$stmt = $pdo->prepare($sql);
			//$stmt->bindParam(':refNo', $refNo);	
			//$stmt->execute();
			//$hdr = $stmt->fetch();
			
		
			
			

		?>
        <div class="box-header with-border">
        <h3 class="box-title">Customer Return No. : <?=$docNo;?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
       		
			<?php
					/*$sql = "SELECT count(*) as countTotal 
					FROM invoice_detail dtl 
					WHERE invNo=:refNo 
					";						
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':refNo', $refNo);
					$stmt->execute();	
					$row = $stmt->fetch();
					$countTotal = $row['countTotal'];*/
					
					$sql = "SELECT dtl.`id`, dtl.`prodItemId`, dtl.`prodCode` 
					, itm.barcode, itm.qty 
					FROM `invoice_detail` dtl	
					LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 
					WHERE 1
					AND dtl.invNo=:refNo  
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':refNo', $refNo);		
					$stmt->execute();
					$countTotal = $stmt->rowCount();
			  ?>
			<div class="row" <?php echo ($countTotal==0?' style="display: none;" ':''); ?>  >
				<div class="box-header with-border">
				<h3 class="box-title">Product List From Invoice No. : <?=$refNo;?></h3>
				
				
				
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  
				  <span class="label label-primary">Total <?php echo $countTotal; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<form id="form2" action="" method="post" class="form" novalidate>
						<input type="hidden" name="docNo" value="<?=$docNo;?>" />
					<?php
						$sql = "SELECT dtl.`id`, dtl.`prodItemId`, dtl.`prodId`, dtl.`prodCode`, dtl.`barcode`, dtl.`issueDate`
						, dtl.`machineId`, dtl.`seqNo`, dtl.`NW`, dtl.`GW`, dtl.`qty`, dtl.`packQty`, dtl.`grade`, dtl.`gradeDate`
						, dtl.`refItemId`, dtl.`itemStatus`, dtl.`remark`, dtl.`problemId`, dtl.`shelfCode`, dtl.`rcNo`
						FROM invoice_detail dtl 
						LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
						WHERE dtl.invNo=:refNo 
								";
						//$stmt = $pdo->prepare($sql);
						//$stmt->bindParam(':refNo', $refNo);		
						//$stmt->execute();
						//$result = sqlsrv_query($ssConn, $sql);
						//$countTotal = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
					?>
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<tr>
							<th><input type="checkbox" id="checkAll"  />Select All</th>
							<th>No.</th>
							<th>Barcode</th>
							<th>Qty</th>
							<th>Reason</th>
							<th>Reason Remark</th>
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) { mysqli_data_seek($reason, 0);
						?>
						<tr>
							<td><input type="checkbox" name="id[]" value="<?=$row['id'];?>"  />
								<input type="hidden" name="prodItemId[]" value="<?=$row['prodItemId'];?>"  />
							</td>
							<td><?= $row_no; ?></td>
							<td><?= $row['barcode']; ?></td>
							<td><?= $row['qty']; ?></td>
							<td>
								<select name="returnReasonCode[]" class="form-control" name="division_code">
									<option value="">- - ระบุ --</option>
									<?php 
									   $rank_code = "";	
									   while($r = mysqli_fetch_array($reason)) {
										   $selected = '';
										   if( ($rank_code == $r['code']) ) {
											   $selected = "selected";
										   }
										  echo '<option value="'.$r['code'].'" data-name="'.$r['name'].'" '.$selected.' >'.$r['code'].' : '.$r['name'].'</option>';									
										}
									?>
									</select>
							</td>
							<td><input type="text"  class="form-control" name="returnReasonRemark[]" /></td>
						</tr>
						<?php $row_no+=1; } ?>
					</table>
					</div>
					<!--/.table-responsive-->
					<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>
					
					
				</div>
				<!--/box-body-->
			</div>
			<!--/.row table-responsive-->
			
		</div>
		<!-- form-->
		
    </div><!-- /.box-body -->
  <div class="box-footer">
      
      
    <!--The footer of the box -->
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
//       alert("jquery ok");
	$("#custName").focus();
	
// Append and Hide spinner.          
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
  //           

	$(document).on("change",'select[name="returnReasonCode[]"]',function() {
		$(this).closest('tr').find('input[name="returnReasonRemark[]"]')
			.val($(this).find(':selected').attr('data-name'))
			.select()
	});
		
			
	$('#form2 a[name=btn_submit]').click (function(e) {
		if ($('#form2').smkValidate()){
			$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: '<?=$rootPage;?>_add_search_prod_submit_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "<?=$rootPage;?>_add.php?docNo=<?=$docNo;?>";
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
		e.preventDefault();
		}//.if end
	});
	//.btn_click
	
	$("#checkAll").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

	
	$("html,body").scrollTop(0);
	$("#statusName").fadeOut('slow').fadeIn('slow').fadeOut('slow').fadeIn('slow');
	
	$('#txt_row_first').select();
	
});
        
        
   
  </script>
  
 




<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>



<!--Integers (non-negative)-->
<script>
  function numbersOnly(oToCheckField, oKeyEvent) {
    return oKeyEvent.charCode === 0 ||
        /\d/.test(String.fromCharCode(oKeyEvent.charCode));
  }
</script>

<!--Decimal points (non-negative)-->
<script>
  function decimalOnly(oToCheckField, oKeyEvent) {        
    var s = String.fromCharCode(oKeyEvent.charCode);
    var containsDecimalPoint = /\./.test(oToCheckField.value);
    return oKeyEvent.charCode === 0 || /\d/.test(s) || 
        /\./.test(s) && !containsDecimalPoint;
  }
</script>