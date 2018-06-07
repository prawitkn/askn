<?php
  //  include '../db/database.php';
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
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
		
$rootPage="send2";	
$tb="send";	
		
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
		$sdNo = $hdr['sdNo'];
		$refNo = $hdr['refNo'];
		if($stmt->rowCount() >= 1){
			switch($s_userGroupCode){ 					
				case 'whOff' :
				case 'whSup' :
				case 'pdOff' :
				case 'pdSup' :
					//if($hdr['fromCode']!=$s_userDeptCode) { header("Location: access_denied.php"); exit();}			
					break;
				default :	// it, admin 
			}
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
    <section class="content-header">	  
	  <h1><i class="glyphicon glyphicon-arrow-up"></i>
       Send
        <small>Send management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Send List</a></li>		
		<li><a href="<?=$rootPage;?>_hdr.php?sdNo=<?=$sdNo;?>"><i class="glyphicon glyphicon-edit"></i><?=$sdNo;?></a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Send No. : <?=$sdNo;?></h3>
		
		
		<input type="hidden" id="sdNo" value="<?=$sdNo;?>" />
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="#" method="post" class="form" novalidate>	
				<input type="hidden" name="action" value="add" />
				<input type="hidden" name="userDeptCode" id="userDeptCode" value="<?=$s_userDeptCode;?>" />
                <div class="col-md-12">   
					<div class="row">
						<div class="col-md-3">
							<div class="from-group">
							<label for="sendDate">Send Date</label>
							<div class="input-group">
								<input type="text" id="sendDate" name="sendDate" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($sdNo==''?'':' disabled '); ?> >								
							</div><!--input group-->
							</div>
							<!--from group-->															
                        </div>		
						<!--col-md-6-->			
						<div class="col-md-3">					  
					  <div class="from-group">
						<label for="fromCode">From</label>						
						<?php $fromCode=$hdr['fromCode']; 
						switch($s_userGroupCode){
							case 'pdOff' : case 'pdSup' :
								$fromCode=$s_userDeptCode; ?>
								<select name="fromCode" id="fromCode" class="form-control" data-smk-msg="Require from code." required disabled >
							<?php
								break;
							default :
								?>
								<select name="fromCode" id="fromCode" class="form-control"  data-smk-msg="Require from code." required   <?php echo ($sdNo==''?'':' disabled '); ?>  >
								<?php
						}
						?>
						
							<option value="" <?php echo ($fromCode==""?'selected':''); ?> >--All--</option>
							<?php
							$sql = "SELECT `code`, `name` FROM sloc WHERE statusCode='A'	ORDER BY code ASC ";
							$stmt = $pdo->prepare($sql);
							$stmt->execute();					
							while ($itm = $stmt->fetch()){
								$selected=($fromCode==$itm['code']?'selected':'');						
								echo '<option value="'.$itm['code'].'" '.$selected.'>'.$itm['code'].' : '.$itm['name'].'</option>';
							}
							?>
						</select>	
					</div>
					<!--from group-->
				</div>
				<!-- col-md-->
				
				<div class="col-md-3">					  
				  <!-- checkbox -->
					<div class="from-group">
						<label for="toCode">To</label>
						<?php $toCode=$hdr['toCode']; ?>
						<select name="toCode" class="form-control"  data-smk-msg="Require to code." required  <?php echo ($sdNo==''?'':' disabled '); ?> >
							<option value="" <?php echo ($toCode==""?'selected':''); ?> >--All--</option>
							<?php
							$sql = "SELECT `code`, `name` FROM sloc WHERE statusCode='A'	ORDER BY code ASC ";
							$stmt = $pdo->prepare($sql);
							$stmt->execute();					
							while ($itm = $stmt->fetch()){
								$selected=($toCode==$itm['code']?'selected':'');						
								echo '<option value="'.$itm['code'].'" '.$selected.'>'.$itm['code'].' : '.$itm['name'].'</option>';
							}
							?>
						</select>	
					</div>
					<!--from group-->		  
				</div>
				<!-- col-md-->
				
					</div>	
					<!--row-->
					
		<div class="row">
			<div class="col-md-6">		
				<div class="from-group">
					<label for="remark">Remark</label>
					<input type="text" id="remark" name="remark" value="<?=$hdr['remark'];?>" class="form-control" <?php echo ($sdNo==''?'':' disabled '); ?>  >
				</div>
				<!--from group-->			
			</div>
			<!--col-md-->
			<div class="col-md-6">	
				
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

		<div class="row col-md-12"  <?php echo ($sdNo==''?' style="display: none;" ':''); ?>  >
			<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>				
				<a name="btn_search_prod" href="#" class="btn btn-default"><i class="glyphicon glyphicon-plus" ></i> Add item</a>
				
				<div class="box-tools pull-right">
				  <span class="label label-primary">Total <?=$rowCount; ?> items</span>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
				
			<form id="form2" action="delivery_add_item_submit_ajax.php" method="post" class="form" novalidate>
				<input type="hidden" name="action" value="item_update" />
				<input type="hidden" name="sdNo" value="<?=$hdr['sdNo'];?>" />
				
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
						<th>Ref.No.</th>
						<th>Grade Type</th>
						<th>Send Remark</th>
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
						<td><?= $row['refNo']; ?></td>
						<td>
							<select name="gradeTypeId[]" class="form-control"  data-smk-msg="Require Grade Type" >
								<?php
								$sql = "SELECT `id`, `name` FROM `product_item_grade_type` WHERE statusCode='A' ";							
								$stmtOpt = $pdo->prepare($sql);		
								$stmtOpt->execute();
								while($rOption = $stmtOpt->fetch()){
									$selected = ($rOption['id']==$row['gradeTypeId']?' selected ':'');									
									echo '<option value="'.$rOption['id'].'" '
										.$selected
										 .'>'.$rOption['name'].'</option>';
								}
								?>
							</select>
						</td>
						<td>
							<input type="text" name="remarkWh[]" value="<?= $row['remarkWh']; ?>" />
						</td>
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
				
				<!--<a name="btn_view" href="<?=$rootPage;?>_view.php?sdNo=<?=$sdNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>-->
				<button type="button" id="btn_verify" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
				<i class="glyphicon glyphicon-ok"></i> Confirm
			  </button>   
			  
			  <button type="button" id="btn_item_update" class="btn btn-warning pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
				<i class="glyphicon glyphicon-ok"></i> Update Item and Confirm
			  </button>   

			<button type="button" id="btn_delete" class="btn btn-danger pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']<>'P'?'':'disabled'); ?> >
				<i class="glyphicon glyphicon-trash"></i> Delete
			</button>
		  
				</form>
			</div>
			<!--/.row dtl-->
		
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
	
	$('a[name=btn_search_prod]').click(function(e){
		var queryDate = $('#sendDate').val(); 
		window.location.href = "<?=$rootPage;?>_hdr_item.php?sdNo=<?=$sdNo;?>&sendDate="+queryDate+"&fromCode=<?=$fromCode;?>&toCode=<?=$toCode;?>";
	});
	
// Append and Hide spinner.          
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
  //           

	
	$('#form1 a[name=btn_create]').click (function(e) {		
		if($('#userDeptCode').val() != "") { $('#fromCode').prop('disabled',''); }
		if ($('#form1').smkValidate()){
			$.smkConfirm({text:'Are you sure to Create ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
				$('#fromCode').prop('disabled','');
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
						window.location.href = '<?=$rootPage;?>_hdr.php?sdNo='+data.sdNo;
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
		if($('#userDeptCode').val() != "") { $('#fromCode').prop('disabled','disabled'); }
	});
	//.btn_click
	
	$('#btn_item_update').click (function(e) {	
		//alert(params.hdrID);
		$.smkConfirm({text:'Are you sure to Update All Items and Confirm ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: $("#form2").serialize(),
				dataType: 'json'
			}).done(function(data) {
				if (data.success){  
					$.smkAlert({
						text: data.message,
						type: 'warning',
						position:'top-center'
					});		
					setTimeout(function(){ window.location.href = '<?=$rootPage;?>_view.php?sdNo=<?=$sdNo;?>'; }, 1000);
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
	
	$('a[name=btn_row_delete]').click(function(){
		var params = {
			action: 'item_delete',
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
	//
	
	$('#btn_delete').click (function(e) {				 
		var params = {		
		action: 'delete',
		sdNo: $('#sdNo').val()				
		};
		//alert(params.sdNo);
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
		<?php if($sumGradeNotOk>0){
				echo "alert('Please check GRADE before sending.'); return false; ";
		}?>	 
		var params = {
		action: 'confirm',					
		sdNo: $('#sdNo').val()			
		};
		if(params.sdNo==""){
			alert('SO No. not found.');
			return false;
		}
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
					setTimeout(function(){ window.location.href = '<?=$rootPage;?>_view.php?sdNo=<?=$sdNo;?>'; }, 1000);
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
		<?php if(isset($hdr['sendDate'])){ ?>
		var queryDate = '<?=$hdr['sendDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#sendDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#sendDate').datepicker('setDate', '0'); <?php } ?>
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