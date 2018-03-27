<?php include 'inc_helper.php'; ?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>


<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
		
$rootPage="sending";		
$tb="send";

?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->	
    <section class="content-header">
		<?php
			$sdNo = $_GET['sdNo'];
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
    <a href="<?=$rootPage;?>_add.php?sdNo=<?=$sdNo;?>" class="btn btn-google">Back</a>
    <div class="box box-primary">
		<?php			
			$sql = "SELECT hdr.* 
			FROM send_prod hdr 
			WHERE hdr.statusCode='P' AND hdr.sdNo=:refNo 
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':refNo', $refNo);	
			$stmt->execute();
			$hdr = $stmt->fetch();
		?>
        <div class="box-header with-border">
        <h3 class="box-title">Sending No. : <?=$rtNo;?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
       		
			<?php
					$sql = "SELECT count(*) as countTotal 
					FROM send_prod_detail dtl 
					WHERE hdrNo=:hdrNo 
					";						
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':hdrNo', $refNo);
					$stmt->execute();	
					$row = $stmt->fetch();
					$countTotal = $row['countTotal'];
			  ?>
			<div class="row" <?php echo ($countTotal==0?' style="display: none;" ':''); ?>  >
				<div class="box-header with-border">
				<h3 class="box-title">Product List From Production Sending No. : <?=$refNo;?></h3>
				
				
				
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  
				  <span class="label label-primary">Total <?php echo $countTotal; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<form id="form2" action="" method="post" class="form" novalidate>
						<input type="hidden" name="rtNo" value="<?=$rtNo;?>" />
					<?php
						$sql = "SELECT dtl.`id`, dtl.`prodItemId`, itm.`barcode`, itm.`issueDate`
						, itm.`machineId`, itm.`seqNo`, itm.`NW`, itm.`GW`, itm.`qty`, itm.`packQty`, itm.`grade`, itm.`gradeDate`
						, itm.`refItemId`, itm.`itemStatus`, itm.`remark`, itm.`problemId`, dtl.`hdrNo`, dtl.`sdNo`						
						, itm.prodCodeId, prd.code as prodCode 
						FROM send_prod_detail dtl 
						LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
						LEFT JOIN product prd ON prd.id=itm.prodCodeId 
						WHERE dtl.sdNo=:sdNo 
								";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':sdNo', $refNo);		
						$stmt->execute();
					?>
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<tr>
							<th><input type="checkbox" id="checkAll"  />Select All</th>
							<th>No.</th>
							<th>Product Code</th>
							<th>Barcode</th>
							<th>Grade</th>
							<th>Qty</th>
							<th>Issue Date</th>
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) { 
						$gradeName = '<b style="color: red;">N/A</b>'; 
						switch($row['grade']){
							case 0 : $gradeName = 'A'; break;
							case 1 : $gradeName = '<b style="color: red;">B</b>'; break;
							case 2 : $gradeName = '<b style="color: red;">N</b>'; break;
							default : 
								$gradeName = '<b style="color: red;">N/a</b>'; 
						} 
						?>
						<tr>
							<td><input type="checkbox" name="itmId[]" value="<?=$row['id'];?>"  /></td>
							<td><?= $row_no; ?></td>
							<td><?= $row['prodCode']; ?></td>
							<td><?= $row['barcode']; ?></td>
							<td><?= $gradeName; ?></td>
							<td><?= $row['qty']; ?></td>
							<td><?= date('d M Y',strtotime( $row['gradeDate'] )); ?></td>							
						</tr>
						<?php $row_no+=1; } ?>
					</table>
					</div>
					<!--/.table-responsive-->
					<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>
					</form>
					
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
					url: 'rt_add_search_prod_submit_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "rt_add.php?rtNo=<?=$rtNo;?>";
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
  
  <link href="bootstrap-datepicker-custom-thai/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
    <script src="bootstrap-datepicker-custom-thai/dist/js/bootstrap-datepicker-custom.js"></script>
    <script src="bootstrap-datepicker-custom-thai/dist/locales/bootstrap-datepicker.th.min.js" charset="UTF-8"></script>
  
<script>
	$(document).ready(function () {
		$('.datepicker').datepicker({
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			format: 'dd/mm/yyyy',
			todayBtn: true,
			language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: true              //Set เป็นปี พ.ศ.
		});  
				
		<?php if(isset($searchFromDate)){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?= $searchFromDate;?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#searchFromDate').datepicker('setDate', realDate);
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		<?php } ?>
		
		<?php if(isset($searchToDate)){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?= $searchToDate;?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#searchToDate').datepicker('setDate', realDate);
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		<?php } ?>
		
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