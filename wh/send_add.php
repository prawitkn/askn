<?php
  //  include '../db/database.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 
//Check Type

//Check Right

	$rootPage = 'send';

	$tb="send";	

	$sdNo=$fromCode=$toCode=$refCode='';
	switch($s_userGroupCode){ 	
		case 'pdOff' :
		case 'pdSup' :
			$fromCode=$s_userDeptCode;			
			break;
		default :	// it, admin 
	}
	
	$sql = "SELECT hdr.`sdNo`, hdr.`refNo`, hdr.`sendDate`, hdr.`fromCode`, hdr.`toCode`, hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createById`
	, fsl.name as fromName, tsl.name as toName
	, d.userFullname as createByName
	FROM `".$tb."` hdr
	LEFT JOIN sloc fsl on hdr.fromCode=fsl.code 
	LEFT JOIN sloc tsl on hdr.toCode=tsl.code
	left join user d on hdr.createById=d.userId
	WHERE 1 
	AND hdr.statusCode='B' AND hdr.createById=:s_userId 
	";

	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':s_userId', $s_userId);	
	$stmt->execute();
	$hdr = $stmt->fetch();	
	if($stmt->rowCount() >= 1){
		$sdNo = $hdr['sdNo'];
		$refNo = $hdr['refNo'];
		$fromCode = $hdr['fromCode'];
		$toCode = $hdr['toCode'];
	}

?>
    
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->	
	<section class="content-header"  style="color: red;">	  
	  <h1><i class="glyphicon glyphicon-eject"></i>
       Send
        <small>Send management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Send List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?sdNo=<?=$sdNo;?>"><i class="glyphicon glyphicon-edit"></i><?=$sdNo;?></a></li>
      </ol>
    </section>
	

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Sending No. : <?=$sdNo;?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="#" method="post" class="form" novalidate>	
					<input type="hidden" name="action" value="add" />
                <div class="col-md-12">   
					<div class="row">
						<div class="col-md-3">	
							<label>From : </label>
							<select name="fromCode" id="fromCode" class="form-control" <?php echo ($fromCode==""?'':' disabled '); ?>  >
								<option value="" <?php echo ($fromCode==""?'selected':''); ?> >--All--</option>
								<?php
								$sql = "SELECT `code`, `name` FROM sloc WHERE statusCode='A' ORDER BY code ASC ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();					
								while ($row = $stmt->fetch()){
									$selected=($fromCode==$row['code']?'selected':'');						
									echo '<option value="'.$row['code'].'" '.$selected.'>'.$row['code'].' : '.$row['name'].'</option>';
								}
								?>
							</select>	
						</div>
						<!-- col-md-->
						
						<div class="col-md-3">	
							<label>To : </label>
							<select name="toCode" class="form-control"  <?php echo ($sdNo==''?'':' disabled '); ?>  >
								<option value="" <?php echo ($toCode==""?'selected':''); ?> >--All--</option>
								<?php
								$sql = "SELECT `code`, `name` FROM sloc WHERE statusCode='A' ORDER BY code ASC ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();					
								while ($row = $stmt->fetch()){
									$selected=($toCode==$row['code']?'selected':'');						
									echo '<option value="'.$row['code'].'" '.$selected.'>'.$row['code'].' : '.$row['name'].'</option>';
								}
								?>
							</select>	
						</div>
						<!-- col-md-->
				
						<div class="col-md-3">	
							<label>Ref. No. : </label>
							<input type="text" name="refNo" class="form-control" <?php echo ($sdNo==''?'':' value="'.$refNo.'" disabled '); ?>  />  
						</div>
						<!-- col-md-->
						
				
					</div>	
					<!--row-->
					
		<div class="row">
			<div class="col-md-3">		
				<div class="from-group">
				<label for="sendDate">Send Date</label>
				<input type="text" id="sendDate" name="sendDate" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($sdNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->				
			</div>
			<!--col-md-->
			<div class="col-md-6">	
				<div class="from-group">
					<label for="remark">Remark</label>
					<input type="text" id="remark" name="remark" value="<?=$hdr['remark'];?>" class="form-control" <?php echo ($sdNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
			<div class="col-md-3">	
				
			</div>
			<!--col-md-->
		</div>
		<!--row-->
		<div class="row" <?php echo ($sdNo==''?'':' style="display: none;" '); ?>>
			<div class="col-md-12">					
				<a name="btn_create" href="#" class="btn btn-default"><i class="glyphicon glyphicon-plus" ></i> Create</a>
			</div>
		</div>
		<!--row-->
					
				</div>
				<!-- col-md-6 --> 
						
				
				
				

			
			</form>			
            </div>   
			<!--/.row hdr-->
			
			
			
			
			
	<?php
		$sql = "SELECT dtl.`refNo`, dtl.`id`, dtl.`prodItemId`,itm.`barcode`, itm.`issueDate`, itm.`machineId`, itm.`NW`, itm.`GW`
		, itm.`qty`, itm.`packQty`, itm.`grade`, itm.`gradeDate`, itm.`refItemId`, itm.`itemStatus`, itm.`remark`, itm.`problemId`
		, itm.`gradeTypeId`, itm.`remarkWh`
		,prd.id as prodId, prd.code as prodCode 
		, dtl.`sdNo` 
		FROM `".$tb."_detail` dtl	
		LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
		LEFT JOIN product prd ON prd.id=itm.prodCodeId  
		WHERE 1
		AND dtl.sdNo=:sdNo  
		ORDER BY dtl.refNo, itm.barcode
		";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':sdNo', $sdNo);		
		$stmt->execute();
		$rowCount = $stmt->rowCount();
	?>
			<div class="row" <?php echo ($sdNo==''?' style="display: none;" ':''); ?> >
				<div class="box-header with-border">
				<div class="form-inline">
				<label class="box-title">Item List > Barcode : </label>
				<input type="text" id="barcode" name="barcode" class="form-control" />
				</div>
				<!--<h3 class="box-title">Item List > Barcode : </h3>
				
				form-->
				
				
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  
				  <span class="label label-primary">Total <?=$rowCount; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				
				<div class="box-body">					
					<div class="row">
						<div class="col-md-12" id="divBarcodeFail" style="color: red;">
			
						</div>
						<!--col-md6-error-->
					</div>
					
					<form id="form2" action="picking_add_item_submit_ajax.php" method="post" class="form" novalidate  <?php echo ($rowCount==0?' style="display: none;" ':''); ?>  >
						<input type="hidden" name="sdNo" id="sdNo" value="<?=$sdNo;?>" />
										
					
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<tr>
						<th>No.</th>
						<th>Product Code</th>
						<th>Barcode</th>
						<th>Grade</th>						
						<th>Qty</th>
						<th>Issue Date</th>
						<th>#</th>
					</tr>
					<?php $row_no=1;  $prevNo=""; $rowColor='lightBlue';  $sumQty=0;  $sumGradeNotOk=0; while ($row = $stmt->fetch()) { 
						$gradeName = '<b style="color: red;">N/A</b>'; 
						switch($row['grade']){
							case 0 : $gradeName = 'A'; break;
							case 1 : $gradeName = '<b style="color: red;">B</b>'; break;
							case 2 : $gradeName = '<b style="color: red;">N</b>'; $sumGradeNotOk+=1; break;
							default : 
								$sumGradeNotOk+=1;
						} //$sumGradeNotOk=0;
						if($prevNo<>"" AND $prevNo<>$row['refNo']){
							if($rowColor=="lightBlue"){$rowColor="lightGreen";}else{$rowColor="lightBlue";}
						}
						$prevNo=$row['refNo'];
					?>
					<tr style="background-color: <?=$rowColor;?>;" >
						<td>
							<?= $row_no; ?>
							<input type="hidden" name="prodItemId[]" value="<?=$row['prodItemId'];?>" />
						</td>
						<td><?= $row['prodCode']; ?></td>	
						<td><?= $row['barcode']; ?></td>	
						<td><?= $gradeName; ?></td>	
						<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
						<td><?= date('d M Y',strtotime( $row['issueDate'] )); ?></td>		
						<td><a class="btn btn-danger fa fa-trash" name="btn_row_delete" <?php echo ($hdr['statusCode']=='B'?' data-id="'.$row['id'].'" ':' disabled '); ?> > Delete</a></td>
						
					</tr>
					<?php $row_no+=1; 
						$sumQty+=$row['qty']; 
						
					} ?>
					<tr>
						<td></td>
						<td>Total</td>	
						<td></td>
						<td></td>
						<td style="text-align: right;"><?= number_format($sumQty,0,'.',','); ?></td>
						<td></td>
						<td></td>
					</tr>
					</table>
					</div>
					<!--/.table-responsive-->
					<!--<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>-->
					
					</form>
					<button type="button" id="btn_verify" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
						<i class="glyphicon glyphicon-ok"></i> Confirm
					  </button>  

					<button type="button" id="btn_delete" class="btn btn-danger pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']<>'P'?'':'disabled'); ?> >
						<i class="glyphicon glyphicon-trash"></i> Delete
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
        <h4 class="modal-title">Search Picking No.</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">Picking No.</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_fullname" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td>Picking No.</td>
					<td>Pick Date</td>
					<td>Sales Order No.</td>
					<td>Customer Name</td>
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







  
  <!--AUDIO-->
  <audio id="beep" src="..\asset\sound\beep-05.wav" type="audio/wav"></audio>    
  
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
			  url: "search_picking_ajax.php",
			  type: "post",
			  data: params,
			datatype: 'json',
			  success: function(data){	
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
									'<td>'+ value.pickNo +'</td>' +
									'<td>'+ value.pickDate +'</td>' +
									'<td>'+ value.soNo +'</td>' +
									'<td>'+ value.custName +'</td>' +
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
			$('#fromCode').prop('disabled','');
			$.smkConfirm({text:'Are you sure to Create? ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: '<?=$rootPage;?>_ajax.php',
					data: $("#form1").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "<?=$rootPage;?>_add.php?sdNo=" + data.sdNo;
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

	
	$('a[name=btn_row_delete]').click(function(){
		var params = {
			action: 'item_delete',
			sdNo: '<?=$sdNo;?>',
			id: $(this).attr('data-id')
		};
		//alert(params.id);
		$.smkConfirm({text:'Are you sure to Delete ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
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
	
	$('input[name=barcode]').keyup(function(e){
		if(e.keyCode == 13)
		{
			var params = {
				action: 'add_item_add',
				sdNo: '<?=$sdNo;?>',
				barcode: $(this).val()
			};			
			/* Send the data using post and put the results in a div */
			  $.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function (data) {					
				if (data.success){ 
					/*$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});*/
					$('input[name=barcode]').select();
					 
					location.reload();
				} else {
					$tmp = $('#divBarcodeFail').html();
					$('#divBarcodeFail').html($tmp+'<label class="-error">'+params.barcode+' => '+data.message+'</label>');
					$('input[name=barcode]').select();
					$('#beep').get(0).play();
					//alert(data.message);
					//location.reload();
				}
			}).error(function (response) {
				alert(response.responseText);
			}); 
		}/* e.keycode=13 */	
	});
	
	$('#btn_delete').click (function(e) {				 
		var params = {
		action: 'delete',
		sdNo: $('#sdNo').val()				
		};
		alert(params.sdNo);
		$.smkConfirm({text:'Are you sure to Delete ?', accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: 'send2_ajax.php',
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
		sdNo: $('#sdNo').val()			
		};
		//alert(params.hdrID);
		$.smkConfirm({text:'Are you sure to Confirm ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: 'send2_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function(data) {
				if (data.success){  
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});		
					setTimeout(function(){ window.location.href = '<?=$rootPage;?>.php'; }, 2000);
					//location.reload();
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
	
	$('#barcode').select();
	
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
			language: 'en',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: false              //Set เป็นปี พ.ศ.
		});  //กำหนดเป็นวันปัจุบัน
		//กำหนดเป็น วันที่จากฐานข้อมูล
		<?php if($sdNo<>''){ ?>
		var queryDate = '<?=$hdr['sendDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('.datepicker').datepicker('setDate', realDate);
		<?php }else{ ?> $('.datepicker').datepicker('setDate', '0'); <?php } ?>
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