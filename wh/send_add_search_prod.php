<?php
  include '../db/database_sqlsrv.php';
  include 'inc_helper.php';  
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>

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
			
		?>
      <h1>
       Sending to Warehouse
        <small>Sending to Warehouse</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Sending to Warehouse</a></li>
        <li class="active">Sending to Warehouse</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <a href="send_add.php?sdNo=<?=$sdNo;?>" class="btn btn-google">Back</a>
    <div class="box box-primary">
		<?php	
			$sdNo = $_GET['sdNo'];
			//$fromCode = $_GET['fromCode'];
			//$sendDate = to_mysql_date($_GET['sendDate']);
			
		
			$sql = "SELECT sd.* 
			FROM send sd 
			WHERE sd.statusCode='B' AND sd.createByID=:s_userID 
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':s_userID', $s_userID);	
			$stmt->execute();
			$hdr = $stmt->fetch();
			$sdNo = $hdr['sdNo'];
			$fromCode = $hdr['fromCode'];
			$sendDate = $hdr['sendDate'];
			$searchFromDate = (isset($_GET['searchFromDate'])?to_mysql_date($_GET['searchFromDate']):$sendDate);
			$searchToDate = (isset($_GET['searchToDate'])?to_mysql_date($_GET['searchToDate']):$sendDate);
			
			

		?>
        <div class="box-header with-border">
        <h3 class="box-title">Search for Sending No. : <?=$sdNo;?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="" method="post" class="form-inline" novalidate>				
                <div class="col-md-12"> 
					
		<div class="row">
			<div class="col-md-12">						
				<label for="fromCode">From Code</label>
				<select id="fromCode" name="fromCode" class="form-control" data-smk-msg="Require Customer." required  <?=($hdr['fromCode']<>""?' disabled ':''); ?> >
					<option value=""> -- Select -- </option>
					<?php
					//$sql = "SELECT [SourceID],[SourceCode],[SourceName],[IsDisable] FROM source ";
					$sql = "SELECT `code`, `name` FROM `sloc` WHERE statusCode<>'X' ";					
					$result = mysqli_query($link, $sql);				
					while($row = mysqli_fetch_assoc($result)){
						$selected = ($row['code'] == $hdr['fromCode']?' selected disabled ':'');
						echo '<option value="'.$row['code'].'" '.$selected.' 
							 >'.$row['code'].' : '.$row['name'].'</option>';
					}
					?>
				</select> 
				<label for="searchFromDate">From Date</label>
				<input type="text" id="searchFromDate" name="searchFromDate" class="form-control datepicker" data-smk-msg="Require Order Date." required >
				<label for="searchToDate">To Date</label>
				<input type="text" id="searchToDate" name="searchToDate" class="form-control datepicker" data-smk-msg="Require Order Date." required >
				<a name="btn_search_list" href="#" class="btn btn-default"><i class="glyphicon glyphicon-search" ></i> Search</a>
				<!--from group-->				
			</div>
			<!--col-md-->
						
			</div>
			<!--col-md-->
		</div>
		<!--row-->
		
		</div>
		<!-- col-md- --> 
						
				
				
				

			
			</form>			
            </div>   
			<!--/.row hdr-->
			
				
			
			<?php
					$sql = "SELECT count(*) as countTotal 
					FROM `product_item`
					WHERE issueDate BETWEEN '$searchFromDate' AND '$searchToDate'
					AND LEFT(barcode,1)='$fromCode' 
								";						
					$stmt = $pdo->prepare($sql);	
					//$stmt->bindParam(':rcNo', $hdr['rcNo']);
					$stmt->execute();	
					$row = $stmt->fetch();
					$countTotal = $row['countTotal'];
					//$result = sqlsrv_query($ssConn, $sql);
					//$tmpCount = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
					//$countTotal = $tmpCount['countTotal'];
			  ?>
			<div class="row" <?php echo ($countTotal==0?' style="display: none;" ':''); ?>  >
				<div class="box-header with-border">
				<h3 class="box-title">Product List</h3>
				
				
				
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  
				  <span class="label label-primary">Total <?php echo $countTotal; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<form id="form2" action="" method="post" class="form" novalidate>
						<input type="hidden" name="sdNo" value="<?=$sdNo;?>" />
					<?php
						$sql = "SELECT `prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`
						, `seqNo`, `NW`, `GW`, `qty`, `packQty`, `grade`, `gradeDate`
						, `refItemId`, `itemStatus`, `remark`, `problemId` 
						FROM `product_item`
						WHERE issueDate BETWEEN '$searchFromDate' AND '$searchToDate'
						AND LEFT(barcode,1)='$fromCode' 
						";
						//echo $sql;
						$stmt = $pdo->prepare($sql);
						//$stmt->bindParam(':rcNo', $rcNo);		
						$stmt->execute();
						//$result = sqlsrv_query($ssConn, $sql);
						//$countTotal = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
					?>
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<tr>
							<th><input type="checkbox" id="checkAll" checked />Select All</th>
							<th>No.</th>
							<th>Barcode</th>
							<th>Qty</th>
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) { 
						?>
						<tr>
							<td><input type="checkbox" name="prodItemId[]" value="<?=$row['prodItemId'];?>" checked /></td>
							<td><?= $row_no; ?></td>
							<td><?= $row['barcode']; ?></br>
							<td><?= $row['qty']; ?></br>
						</tr>
						<?php $row_no+=1; } ?>
					</table>
					</div>
					<!--/.table-responsive-->
					<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>
					<!--<a name="btn_view" href="receive_view.php?rcNo=<?=$rcNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>-->
					
				</div>
				<!--/box-body-->
			</div>
			<!--/.row table-responsive-->
			
		</div>
		<!-- form2-->
		
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

		
	$('#form1 a[name=btn_search_list]').click (function(e) {
		var sdNo = '<?=$sdNo;?>';
		var searchFromDate = $('#searchFromDate').val();
		var searchToDate = $('#searchToDate').val();
		window.location.href = "send_add_search_prod.php?sdNo=" + sdNo + "&searchFromDate=" + searchFromDate + "&searchToDate=" + searchToDate;
	});
	//.btn_click
		
	$('#form2 a[name=btn_submit]').click (function(e) {
		if ($('#form2').smkValidate()){
			$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'send_add_search_prod_submit_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "send_add.php?sdNo=" + data.sdNo;
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
				$.smkAlert({ text: 'Cancelled.', type: 'info', position:'top-center'});	
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
		<?php }else{ ?> $('#searchFromDate').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		<?php if(isset($searchToDate)){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?= $searchToDate;?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#searchToDate').datepicker('setDate', realDate);		
		<?php }else{ ?> $('#searchToDate').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
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