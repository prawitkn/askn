<?php
  include '../db/database_sqlsrv.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>
<?php
	/*$sqlRole = "";
	switch($s_userGroupCode){
		case 'sales' :
			$sqlRole = " AND ct.smCode='$s_smCode' ";
			break;
		case 'salesAdmin' :
			$sqlRole = " AND ct.smAdmCode='$s_smCode' ";
			break;
		default :
	}*/
?>
    
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
--> 
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
    <a href="2wh.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
		<?php	
			$sql = "SELECT dh.* 
			FROM receive dh 
			WHERE dh.statusCode='B' AND dh.createByID=:s_userID 
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':s_userID', $s_userID);	
			$stmt->execute();
			$hdr = $stmt->fetch();
			$rcNo = $hdr['rcNo'];
			
			

		?>
        <div class="box-header with-border">
        <h3 class="box-title">Add Receive No. : <?=$rcNo;?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="delivery_add_insert.php" method="post" class="form" novalidate>				
                <div class="col-md-12"> 
					
		<div class="row">
			<div class="col-md-3">		
				<div class="from-group">
				<label for="receiveDate">Sending Date</label>
				<input type="text" id="receiveDate" name="receiveDate" value="<?=$hdr['receiveDate'];?>" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($rcNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->				
			</div>
			<!--col-md-->
			<div class="col-md-3">	
				<div class="from-group">
				<label for="refNo">Ref No.</label>
				<input type="text" id="refNo" name="refNo" value="<?=$hdr['refNo'];?>" class="form-control" <?php echo ($rcNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
			<div class="col-md-3">	
				<div class="from-group">
					<label for="fromCode">From Code</label>
					<select id="custCode" name="custCode" class="form-control" data-smk-msg="Require Customer." required >
						<option value=""> -- Select -- </option>
						<?php
						$sql = "SELECT [SourceID],[SourceCode],[SourceName],[IsDisable] FROM source ";
						$result = sqlsrv_query($ssConn, $sql);
						while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
							echo '<option value="'.$row['SourceCode'].'" 
								 >'.$row['SourceCode'].' : '.$row['SourceName'].'</option>';
						}
						?>
					</select> 
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
			<div class="col-md-3">	
				<div class="from-group">
					<label for="remark">Remark</label>
					<input type="text" id="remark" name="remark" value="<?=$hdr['remark'];?>" class="form-control" <?php echo ($rcNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
		</div>
		<!--row-->
		<div class="row" <?php echo ($rcNo==''?'':' style="display: none;" '); ?>>
			<div class="col-md-12">					
				<a name="btn_create" href="#" class="btn btn-default"><i class="glyphicon glyphicon-plus" ></i> Create</a>
			</div>
		</div>
		<!--row-->
		

		
		</div>
		<!-- col-md- --> 
						
				
				
				

			
			</form>			
            </div>   
			<!--/.row hdr-->
			
			<div class="row" <?php echo ($rcNo==''?' style="display: none;" ':''); ?> >
			
			</div>
			<!--row-->
			
			
			<?php
					$sql = "SELECT count(*) as countTotal FROM product_item
								";						
					//$stmt = $pdo->prepare($sql);	
					//$stmt->bindParam(':rcNo', $hdr['rcNo']);
					//$stmt->execute();	
					//$rowCount = $stmt->rowCount();
					$result = sqlsrv_query($ssConn, $sql);
					$tmpCount = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
					$countTotal = $tmpCount['countTotal'];
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
					<?php
						$sql = "SELECT [ProductItemID]
					  ,[ProductID]
					  ,[ItemCode]
					  ,[IssueDate]
					  ,[MachineID]
					  ,[SeqNo]
					  ,[NW]
					  ,[GW]
					  ,[Length]
					  ,[Grade]
					  ,[IssueGrade]
					  ,[UserID]
					  ,[RefItemID]
					  ,[ItemStatus]
					  ,[Remark]
					  ,[RecordDate]
					  ,[ProblemID]
						FROM product_item pi
								";
						//$stmt = $pdo->prepare($sql);
						//$stmt->bindParam(':rcNo', $rcNo);		
						//$stmt->execute();
						$result = sqlsrv_query($ssConn, $sql);
						//$countTotal = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
					?>
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<tr>
							<th><input type="checkbox" name="chkSelected" checked />Select All</th>
							<th>No.</th>
							<th>Barcode</th>
							<th>Qty</th>
						</tr>
						<?php $row_no=1; while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) { 
						?>
						<tr>
							<td><input type="checkbox" name="chkSelected" checked /></td>
							<td><?= $row_no; ?></td>
							<td><?= $row['ItemCode']; ?></br>
							<td><?= $row['Length']; ?></br>
						</tr>
						<?php $row_no+=1; } ?>
					</table>
					</div>
					<!--/.table-responsive-->
					<!--<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>-->
					<a name="btn_view" href="receive_view.php?rcNo=<?=$rcNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>
					
				</div>
				<!--/box-body-->
			</div>
			<!--/.row table-responsive-->
		
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

	$('#form1 a[name=btn_create]').click (function(e) {
		if ($('#form1').smkValidate()){
			$.smkConfirm({text:'Are you sure to Create? ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'receive_add_hdr_insert_ajax.php',
					data: $("#form1").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "receive_add.php?rcNo=" + data.rcNo;
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
	
	$('#form5 a[name=btn_create]').click (function(e) {
		if ($('#form1').smkValidate()){
			$.post({
				url: 'receive_add_item_add_ajax.php',
				data: $("#form5").serialize(),
				dataType: 'json'
			}).done(function(data) {
				if (data.success){  
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});
					//window.location.href = "receive_add.php?rcNo=" + data.rcNo;
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
		e.preventDefault();
		}//.if end
	});
	//.btn_click
	
	$('a[name=btn_row_delete]').click(function(){
		var params = {
			id: $(this).attr('data-id')
		};
		//alert(params.id);
		$.smkConfirm({text:'Are you sure to Delete ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: 'receive_add_item_delete_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function (data) {					
				if (data.success){ 
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});
					location.reload();
				} else {
					alert(data.message);
					location.reload();
				}
			}).error(function (response) {
				alert(response.responseText);
			}); 
		}});
		e.preventDefault();
	});
	//btn_click
	
	$('#form2 a[name=btn_submit]').click (function(e) {
		if ($('#form2').smkValidate()){
			$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'delivery_add_item_submit_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "delivery_view.php?doNo=" + data.doNo;
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
		
		<?php if($rcNo<>""){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?=$hdr['receiveDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('.datepicker').datepicker('setDate', realDate);
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