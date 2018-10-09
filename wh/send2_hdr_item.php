<?php include 'inc_helper.php'; ?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>
 
</head>
<body class="hold-transition skin-green sidebar-mini">


	
  

<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
		
$rootPage="send2";		
$tb="send";

?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
	<?php
	$sdNo = $_GET['sdNo'];
	$sendDate=(isset($_GET['sendDate'])? $_GET['sendDate'] : '01/01/1900' );
	$sendDate=str_replace('/', '-', $sendDate);
	$sendDate=date('Y-m-d', strtotime($sendDate));
	
	if(isset($_GET['issueDate'])){
		$issueDate=(isset($_GET['issueDate'])? $_GET['issueDate'] : '' );
		$issueDate=str_replace('/', '-', $issueDate);
		$issueDate=date('Y-m-d', strtotime($issueDate));
	}
	
	
	$fromCode=(isset($_GET['fromCode'])?$_GET['fromCode']:'');
	$toCode=(isset($_GET['toCode'])?$_GET['toCode']:'');
	$prodId=(isset($_GET['prodId'])?$_GET['prodId']:'');
	
	$prodCode="";
	if($prodId<>""){
		$sql = "SELECT code FROM product WHERE id=:id ";			
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':id', $prodId);			
		$stmt->execute();
		$row=$stmt->fetch();
		$prodCode=$row['code'];
	}
	
	
	
	
	?>
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
		<li><a href="#"><i class="glyphicon glyphicon-list"></i> Item Select</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<div class="form-inline">
				<label class="box-title">Sending No. : <?=$sdNo;?> / Item Search </label>
				<a href="<?=$rootPage;?>_hdr.php?sdNo=" class="btn btn-primary"><i class="glyphicon glyphicon-arrow-left"></i> Back</a>
			</div>
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
			<span id="lblTotal" class="label label-primary">Total items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
			<div class="row" >		
					<div class="col-md-12 ">					
						<form id="form1" action="#" method="get" class="form-inline" style="background-color: gray; padding: 5px;" novalidate>							
								<input type="hidden" name="sdNo" value="<?=$sdNo;?>" />
								
								<label for="sendDate">Sending Date</label> 
								<input type="text" id="sendDate" name="sendDate" class="form-control datepicker" />
								
								<label for="issueDate">Issue Date</label> 
								<input type="text" id="issueDate" name="issueDate" class="form-control datepicker" />
							
								<label for="prodCode">Product Code</label> 
								<input type="hidden" name="prodId" id="prodId" class="form-control" value="<?=$prodId;?>"  />
								<input type="text" name="prodCode" id="prodCode" class="form-control" value="<?=$prodCode;?>"  />
								<a href="#" name="btnSdNo" class="btn btn-primary" ><i class="glyphicon glyphicon-search" ></i> </a>	
								
							<br/><br/>
						</form> 	
						<a name="btnSubmit" id="btnSubmit" href="#" class="btn btn-danger"><i class="glyphicon glyphicon-search"></i> Search</a>
					</div>   
					
					<div class="row">
						<div class="col-md-12">
					<form id="form2" action="" method="post" class="form" novalidate>
						<input type="hidden" name="sdNo" value="<?=$sdNo;?>" />
						<input type="hidden" name="action" value="item_add" />		
						
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-3">
								<select id="selItmId" class="form-control"></select> 
							</div><!--col-md-3-->
							<div class="col-md-3">
								<input type="checkbox" id="chkPending" /> Pending Item Only
							</div>
						</div><!--col-md-12-->
					</div><!--row-->
					
					<div class="col-md-12">
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<thead>
						<tr>
<!--							<th><input type="checkbox" id="checkAll"  />Select All</th>-->
							<th>No. </th>
							<th>Product Code</th>
							<th>Barcode</th>
							<th>Grade</th>
							<th>Qty</th>
							<th>Issue Date</th>							
							<th>Ref.ID</th>
						</tr>
						</thead>
						<tbody>
						
						</tbody>
					</table>
					</div>
					<!--/.table-responsive-->
					</div><!--col-md-12-->
					
						<div class="col-md-12">
							<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>
						</div>
						<!--col-md-12-->
					
					</form>
						</div><!--col-md-12-->
					</div><!--row-->
			
			</div>
			<!--/.row-->
			
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





 <!-- Modal -->
<div id="modal_search_person" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search Product</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="txt_search_word" class="control-label col-md-2">Product Code </label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_word" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td style="display: none;">ID</td>
					<td>Product Code.</td>
					<td>Product Name</td>
					<td style="display: none;">UOM</td>
					<td>Product Category</td>
					<td>App ID</td>
					<td style="display: none;">Balance</td>
					<td style="display: none;">Sales</td>
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
	
// Append and Hide spinner.          
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
  //   
  
	function getList(){
		var sendDate = $('#sendDate').val(); 	
		var issueDate = $('#issueDate').val(); 	
		//queryDate = queryDate.replace(/\//g, '%2F');
		var prodId="";
		if($('#prodCode').val()!=""){ prodId=$('#prodId').val(); }			
						
		var params = {
			action: 'searchItem',
			sdNo: '<?=$sdNo;?>',
			sendDate: sendDate,
			issueDate: issueDate,
			prodId: prodId,
			fromCode: '<?=$fromCode;?>',
			toCode: '<?=$toCode;?>'
		}; //alert(params.action);
		/* Send the data using post and put the results in a div */
		  $.ajax({
			  url: '<?=$rootPage;?>_ajax.php',
			  type: 'post',
			  data: params,
			datatype: 'json',
			  success: function(data){	//alert(data);
					data=$.parseJSON(data);
					//alert(data);
					$('#lblTotal').text('Total '+data.rowCount+' items');
					
					$('#tbl_items tbody').empty();
					$('#selItmId').empty();
					switch(data.rowCount){
						case 0 : alert('Data not found.');
							return false; break;
						default : 
							var prevSendId="";
							var optItmHtml="";
							var rowColor="lightBlue";	
							var tmpNo="";
							
							
							var rowNo=1;
							$.each($.parseJSON(data.data), function(key,value){
								if( prevSendId == ""){
									$('#selItmId').append('<option value="">Clear All</option>'+
									'<option value="0">Select All</option>');
									$('#selItmId').append('<option value="'+value.sendId+'">'+value.sendId+'</option>');
								}
								if( (prevSendId != "") && (prevSendId != value.sendId) ){
									if(rowColor=="lightBlue"){rowColor="lightGreen";}else{rowColor="lightBlue";}
									$('#selItmId').append('<option value="'+value.sendId+'">'+value.sendId+'</option>');
								}
								prevSendId=value.sendId;								

								if(value.sentNo==""){
									var isSelected=value.isSelected;
									if(isSelected==1){ isSelected=' checked="checked" '; }
									tmpNo=rowNo+':'+'<input type="checkbox" name="itmId[]" class="itmId" value="'+value.sendId+','+value.productItemId+'" '+isSelected+'  />';
								}else{
									if(value.recvNo==""){
										var isSelected=value.isSelected;
										if(isSelected==1){ isSelected=' checked="checked" '; }
										tmpNo=rowNo+':'+'<input type="checkbox" name="itmId[]" class="itmId" value="'+value.sendId+','+value.productItemId+'" '+isSelected+'  />';
									}else{
										tmpNo=rowNo+':'+'<label class="label label-danger" >'+value.sentNo+'</label>';
									}									
								} //alert(tmpNo);
								$('#tbl_items tbody').append(
								'<tr style="background-color: '+rowColor+'" >' +	
									'<td>'+ tmpNo +'</td>' + 
									'<td>'+ value.prodCode +'</td>' +
									'<td>'+ value.barcode +'</td>' + 
									'<td>'+ value.gradeName +'</td>' +
									'<td>'+ value.qty +'</td>' +
									'<td>'+ value.issueDate +'</td>' +
									'<td>'+ value.sendId +'</td>' +
								'</tr>'
								);		
								rowNo+=1;
							});
						//('#modal_search_person').modal('show');	
					}	
			  }   
			}).error(function (response) {
				alert(response.responseText);
			}); 
	}
  
  
  
  
  	//SEARCH Begin
	$('a[name="btnSdNo"]').click(function(){
		curName = $(this).prev().attr('name');
		curId = $(this).prev().prev().attr('name');
		if(!$('#'+curName).prop('disabled')){
			$('#modal_search_person').modal('show');
		}
	});	
	$('#txt_search_word').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#txt_search_word').val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_product_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								return false; break;
							default : 
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data.data), function(key,value){
									$('#tbl_search_person_main tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' + 
										'<td style="display: none;">'+ value.prodId +'</td>' +
										'<td>'+ value.prodCode +'</td>' +
										'<td>'+ value.prodName +'</td>' +
										'<td style="display: none;">'+ value.prodUomCode +'</td>' +
										'<td>'+ value.prodCatName +'</td>' +
										'<td>'+ value.prodAppName+'</td>' +									
										'<td style="display: none;">'+ value.balance+'</td>' +	
										'<td style="display: none;">'+ value.sales+'</td>' +	
									'</tr>'
									);		
								});
								$('#modal_search_person').modal('show');	
						}	
						
								
							
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'a[data-name="search_person_btn_checked"]',function() {
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('input[name='+curName+']').val($(this).closest("tr").find('td:eq(2)').text());
						
		$('#modal_search_person').modal('hide');
		getList();
	});
	//Search End

	$('#prodCode').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#prodCode').val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			}
			curName = $(this).attr('name');
			curId = $(this).prev().attr('name');
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_product_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								$('#tbl_items tbody').empty();
								return false; break;
							case 1 :
								$.each($.parseJSON(data.data), function(key,value){
									$('input[name='+curName+']').val(value.prodCode);
									$('input[name='+curId+']').val(value.prodId);
								});
								getList();
								break;
							default : 
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data.data), function(key,value){
									$('#tbl_search_person_main tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' + 
										'<td style="display: none;">'+ value.prodId +'</td>' +
										'<td>'+ value.prodCode +'</td>' +
										'<td>'+ value.prodName +'</td>' +
										'<td style="display: none;">'+ value.prodUomCode +'</td>' +
										'<td>'+ value.prodCatName +'</td>' +
										'<td>'+ value.prodAppName+'</td>' +									
										'<td style="display: none;">'+ value.balance+'</td>' +	
										'<td style="display: none;">'+ value.sales+'</td>' +	
									'</tr>'
									);		
								});								
								$('#modal_search_person').modal('show');	
						}	
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});
 
 
 
 
 
 
 
 
  
	$('#chkPending').on('change', function() {
		if($(this).prop('checked')){
			$('#tbl_items > tbody  > tr').each(function() {
				//alert($(this).find('td:eq(1) input[name=itmId]'));
				var tmp = $(this).find('td:eq(0) input.itmId');
				//alert(tmp.val());
				if(tmp.length <= 0){
					//alert(tmp.val());
					$(this).fadeOut('slow');
				} 
			});
		}else{
			$('#tbl_items > tbody  > tr').each(function() {
				$(this).fadeIn('slow');
			});
		}
		
	});
		
	
	$('#form2 a[name=btn_submit]').click (function(e) {
		if ($('#form2').smkValidate()){
			$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: '<?=$rootPage;?>_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) { //alert(data.sdNo);
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "<?=$rootPage;?>_hdr.php?sdNo=<?=$sdNo;?>";
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
	
	/*$("#checkAll").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});*/
	$(document).on("change",'#selItmId',function() { 
		switch($(this).val()){
			case "" :	$("input:checkbox").prop('checked',''); break;
			case "0" : $("input:checkbox").prop('checked','checked'); break;
			default : 
				$("input:checkbox").prop('checked',''); 
				$("input:checkbox[value^='"+$(this).val()+"']").prop('checked','checked');
		}
	});
	
	
	$("#btnSubmit").click(function(){ 
		getList();
	});
	
	$("#btnSyncSubmit").click(function(){ 
		getList();
	});

	//getList();
	
	$("html,body").scrollTop(0);
		
});
        
        
   
  </script>
  
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>



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
		<?php if(isset($sendDate)){ ?>
		var queryDate = '<?=$sendDate;?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1,dateParts[2]); 
		$('#sendDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#sendDate').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		//กำหนดเป็น วันที่จากฐานข้อมูล
		<?php if(isset($issueDate)){ ?>
		var queryDate = '<?=$issueDate;?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1,dateParts[2]); 
		$('#issueDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#issueDate').val(''); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
	});
</script>
