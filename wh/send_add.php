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
<?php include 'head.php';


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
    <a href="send.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
		<?php	
			$sql = "SELECT hdr.* 
			FROM send hdr 
			WHERE hdr.statusCode='B' AND hdr.createByID=:s_userID 
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':s_userID', $s_userID);	
			$stmt->execute();
			$hdr = $stmt->fetch();
			$sdNo = $hdr['sdNo'];
			
			

		?>
        <div class="box-header with-border">
        <h3 class="box-title">Add Sending No. : <?=$sdNo;?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="" method="post" class="form" novalidate>				
                <div class="col-md-12"> 
					<div class="row">
						<div class="col-md-3">
							<label for="refSdNo" >Ref. Sending No.</label>
							<div class="form-group row">
								<div class="col-md-9">
									<input type="text" name="refSdNo" class="form-control" <?php echo ($sdNo==''?'':' value="'.$sdNo.'" disabled '); ?>  />
								</div>
								<div class="col-md-3">
									<a href="#" name="btnSdNo" class="btn btn-primary" <?php echo ($sdNo==''?'':' disabled '); ?> ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>
							</div>
							<!--from group-->
							
						</div>		
						<!--col-md-6-->			
					</div>
					<!--row-->
					
					
					
					
		<div class="row">
			<div class="col-md-3">		
				<div class="from-group">
				<label for="sendDate">Sending Date</label>
				<input type="text" id="sendDate" name="sendDate" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($sdNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->				
			</div>
			<!--col-md-->
			
			<div class="col-md-3">	
				<div class="from-group">
					<label for="fromCode">From Code</label>
					<select id="fromCode" name="fromCode" class="form-control" data-smk-msg="Require Customer." required  <?=($hdr['fromCode']<>""?' disabled ':''); ?> >
						<option value=""> -- Select -- </option>
						<?php
						$sql = "SELECT [SourceID],[SourceCode],[SourceName],[IsDisable] FROM source ";
						$result = sqlsrv_query($ssConn, $sql);
						while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
							$selected = ($row['SourceCode'] == $hdr['fromCode']?' selected disabled ':'');
							echo '<option value="'.$row['SourceCode'].'" '.$selected.' 
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
					<label for="toCode">To Code</label>
					<select id="toCode" name="toCode" class="form-control" data-smk-msg="Require Customer." required <?=($hdr['toCode']<>""?' disabled ':''); ?> >
						<option value=""> -- Select -- </option>
						<?php
						$sql = "SELECT [SourceID],[SourceCode],[SourceName],[IsDisable] FROM source ";
						$result = sqlsrv_query($ssConn, $sql);
						while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
							$selected = ($row['SourceCode'] == $hdr['toCode']?' selected ':'');
							echo '<option value="'.$row['SourceCode'].'" '.$selected.' 
								 >'.$row['SourceCode'].' : '.$row['SourceName'].'</option>';
						}
						?>
					</select> 
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
			
			<div class="col-md-3">	
			</div>
			<!--col-md-->
			
			
		</div>
		<!--row-->
		<div class="row" <?php echo ($sdNo==''?'':' style="display: none;" '); ?> >
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
			
			<?php
					$sql = "SELECT count(*) as countTotal FROM send_detail WHERE sdNo=:sdNo 
								";						
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':sdNo', $hdr['sdNo']);
					$stmt->execute();	
					$rc = $stmt->fetch();
			  ?>
			<div class="row" <?php echo ($sdNo==''?' style="display: none;" ':''); ?> >
				<div class="box-header with-border">
				<h3 class="box-title">Product List</h3>
				<a name="btn_search_prod" href="send_add_search_prod.php?sdNo=<?=$sdNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-plus" ></i> Add item</a>
				<!--form-->
				
				
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  
				  <span class="label label-primary">Total <?php echo $rc['countTotal']; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body" <?php echo ($rc['countTotal']==0?' style="display: none;" ':''); ?> >
					<?php
						$sql = "SELECT dtl.id, dtl.prodItemId 
						, itm.barcode, itm.grade, itm.qty, itm.issueDate 
						FROM send_detail dtl 
						LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 
						WHERE sdNo=:sdNo  
						";								
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':sdNo', $hdr['sdNo']);		
						$stmt->execute();
					?>
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Barcode</th>
							<th>Grade</th>
							<th>Qty</th>
							<th>Seding Date</th>
							<th>#</th>
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) { 
						?>
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>
							<td><?= $row['barcode']; ?></td>
							<td><?= $row['grade']; ?></td>
							<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
							<td><?= $row['issueDate']; ?></td>
							<td><a class="btn btn-danger fa fa-trash" name="btn_row_delete" <?php echo ($hdr['statusCode']=='B'?' data-id="'.$row['id'].'" ':' disabled '); ?> > Delete</a></td>
						</tr>
						<?php $row_no+=1; } ?>
					</table>
					</div>
					<!--/.table-responsive-->
					<!--<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>-->
					<a name="btn_view" href="send_view.php?sdNo=<?=$sdNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>
					
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
  
  
  
 
  
<!-- Modal -->
<div id="modal_search_person" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search Sending No.</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">SD NO.</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_fullname" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td>Sending ID</td>
					<td>Sending No.</td>
					<td>Send Date</td>
					<td>Quantity</td>
					<td>Send To ID</td>
					<td>Send To Address</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		</form>
		<div id="div_search_person_result">
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>




  
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
  
  
  
  	//SEARCH Begin
	$('a[name="btnSdNo"]').click(function(){
		//prev() and next() count <br/> too.		
		$btn = $(this).closest("div").prev().find('input');
		curId = $btn.attr('name');
		//curId = $(this).prev().attr('name');
		curTxtFullName = $(this).attr('id');
		if(!$btn.prop('disabled')){
			$('#modal_search_person').modal('show');
		}
		
		//alert(curHidMid+' '+curSlOrgCode+' '+curTxtFullName+' ' +curTxtMobilePhoneNo);
		
	});	
	$('#txt_search_fullname').keyup(function(e){
		if(e.keyCode == 13)
		{
			var params = {
				search_fullname: $('#txt_search_fullname').val()
			};
			if(params.search_fullname.length < 3){
				alert('search name surname must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_sending_old_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	
								alert(data);
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data), function(key,value){
									$('#tbl_search_person_main tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' +
										'<td>'+ value.SendID +'</td>' +
										'<td>'+ value.SendNo +'</td>' +
										'<td>'+ value.IssueDate +'</td>' +
										'<td>'+ value.Quantity +'</td>' +
										'<td>'+ value.CustomerID +'</td>' +
										'<td>'+ value.Address +'</td>' +
									'</tr>'
									);			
								});
				  }, //success
				  error:function(){
					  alert('error');
				  }   
				}); 
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'a[data-name="search_person_btn_checked"]',function() {
		
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		//$('#'+curTxtFullName).val($(this).closest("tr").find('td:eq(2)').text());
		//$('#'+curTxtMobilePhoneNo).val($(this).closest('tr').find('td:eq(3)').text());		
		$('#modal_search_person').modal('hide');
	});
	//Search End
	
	
	

	$('#form1 a[name=btn_create]').click (function(e) {
		if ($('#form1').smkValidate()){
			$.smkConfirm({text:'Are you sure to Create? ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'send_add_hdr_insert_ajax.php',
					data: $("#form1").serialize(),
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
			}});
			//smkConfirm
		e.preventDefault();
		}//.if end
	});
	//.btn_click
	
	$('#form2 a[name=btn_search_list]').click (function(e) {
		var sdNo = '<?=$sdNo;?>';
		var searchFromDate = $('#searchFromDate').val();
		var searchToDate = $('#searchToDate').val();
		window.location.href = "send_add.php?sdNo=" + sdNo + "&searchFromDate=" + searchFromDate + "&searchToDate=" + searchToDate;
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
				url: 'send_add_item_delete_ajax.php',
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
		
		<?php if($sdNo==""){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		$('#sendDate').datepicker('setDate', '0');
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		<?php } ?>
		
		<?php if($sdNo<>""){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?=$hdr['sendDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#sendDate').datepicker('setDate', realDate);
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		<?php } ?>
		
		<?php if(isset($_GET['searchFromDate'])){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?= to_mysql_date($_GET['searchFromDate']);?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#searchFromDate').datepicker('setDate', realDate);
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		<?php } ?>
		
		<?php if(isset($_GET['searchToDate'])){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?= to_mysql_date($_GET['searchToDate']);?>',
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