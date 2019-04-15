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
$rootPage = 'prodMapCustProdCode';

//Check user roll.
$isAllow=false;
switch($s_userGroupCode){
	case 'admin' : $isAllow=true; break;
	case 'pdSup' : 
		if ( $s_userDeptCode == 'T' ){
			$isAllow=true;
		}
		break;
	default : 
}//.switch

if ( !$isAllow ){
	header('Location: access_denied.php');
	exit();
}//.if isallow

$id=$_GET['id'];

$sql = "SELECT pbc.`id`, pbc.`custId`, pbc.`prodId`, pbc.`prodCode`, pbc.`prodDesc`, pbc.`statusCode`
,prd.code as akProdCode, cust.name as custName 
FROM `wh_product_code_by_customer` pbc
INNER JOIN product prd ON prd.id=pbc.prodId
LEFT JOIN customer cust ON cust.id=pbc.custId 
WHERE 1=1 ";
$sql .= "AND pbc.id=:id ";
$stmt = $pdo->prepare($sql);	
$stmt->bindParam(':id', $id);	
$stmt->execute();	
$row=$stmt->fetch();	

?>

</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">





<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-link"></i>
       Customer's Product Code Mapping
        <small>Master Management</small>
      </h1>
	  <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Customer's Product Code Mapping List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Edit Customer's Product Code Mapping</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Edit Customer's Product Code Mapping</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">            
            <div class="row">                
                    <form id="form1" action="#" method="post" class="form" novalidate >

					<input type="hidden" name="action" value="edit" />

					<input type="hidden" name="id" value="<?=$id;?>" />

					<div class="col-md-6">
						<div class="form-group">
							<label for="prodCode">Product Code : </label>
							<div class="row">
								<div class="col-md-10">
									<input type="hidden" name="prodId" id="prodId" class="form-control" value="<?=$row['prodId']; ?>" />
									<input type="text" name="prodCode" id="prodCode" class="form-control" value="<?=$row['akProdCode'];?>" data-smk-msg="Require Product" required  />
								</div><!--col-md-12-->
								<div class="col-md-2">
									<a data-toggle="modal" href="#modal_search_product" name="btnSearchProduct" class="btn btn-default" ><i class="glyphicon glyphicon-search" ></i> </a>
								</div><!--col-md-1-->
							</div><!--row-->
						</div><!--form-group-->
					</div>
					<!--/.col-md-->


					<div class="col-md-6">
						<div class="form-group">
							<label for="custName">Customer : </label>
							<div class="row">
								<div class="col-md-10">
									<input type="hidden" name="custId" id="custId" class="form-control" value="<?=$row['custId'];?>" />
									<input type="text" name="custName" id="custName" class="form-control" value="<?=$row['custName'];?>" data-smk-msg="Require Customer" required  />
								</div><!--col-md-12-->
								<div class="col-md-2">
									<a data-toggle="modal" href="#modal_search_customer" name="btnSearchCustomer" class="btn btn-default" ><i class="glyphicon glyphicon-search" ></i> </a>
								</div><!--col-md-1-->
							</div><!--row-->
						</div><!--form-group-->

                        <div class="form-group">			
							<div class="form-group">
								<label for="custProdCode">Customer Product Code</label>
								<input id="custProdCode" type="text" class="form-control" name="custProdCode" value="<?=$row['prodCode'];?>"  data-smk-msg="Require Customer Product Code." required>
							</div>
                        </div>                       
                        
						<label for="custProdDesc" >Customer Product Description</label>
						<div class="form-group row">
							<div class="col-md-9">
								<textarea id="custProdDesc" name="custProdDesc" class="form-control"><?=$row['prodDesc'];?></textarea>
							</div>
						</div>
						<!--from group-->
						<div class="form-group">
                            <label for="statusCode">Status</label>
							<input type="radio" name="statusCode" value="A" <?php echo ($row['statusCode']=='A'?' checked ':'');?> >Active
							<input type="radio" name="statusCode" value="X" <?php echo ($row['statusCode']=='X'?' checked ':'');?> >Non-Active
						</div>
												
						<!--<button id="btn1" type="submit" class="btn btn-default">Submit</button>-->
						<a name="btnSubmit" id="btnSubmit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Submit</a>
					</div>
					<!--/.col-md-->
                    </form>
                </div>
                <!--/.row-->       
            </div>
			<!--.body-->    
    </div>
	<!-- /.box box-primary -->
 

<div id="spin"></div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>
  
  
  
  
  
 
<!-- Modal -->
<div id="modal_search_product" class="modal fade" >
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
		
		<table id="tbl_search_data_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td style="text-align: center;">#Select</td>
					<td style="display: none;">ID</td>
					<td style="text-align: center;">Product Code.</td>
					<td style="text-align: center;">Product Name</td>
					<td style="display: none;">UOM</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<div id="div_search_data_result">
		</div>
	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>






<!-- Modal -->
<div id="modal_search_customer" class="modal fade">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search Customer</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="txt_search_word" class="control-label col-md-2">Customer Name </label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_word" />
				</div>
			</div>
		
		<table id="tbl_search_data_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td style="text-align: center;">#Select</td>
					<td style="display: none;">ID</td>
					<td style="text-align: center;">Customer Name</td>
					<td style="text-align: center;">Customer Code.</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<div id="div_search_data_result">
		</div>
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
	$("#name").focus();

	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
//      


	//SEARCH Product Begin
	function modalProductShow(search_word, data){
		$('#modal_search_product #tbl_search_data_main tbody').empty();
		$.each($.parseJSON(data), function(key,value){
			$('#modal_search_product #tbl_search_data_main tbody').append(
			'<tr>' +
				'<td style="text-align: center;">' +
				'	<div class="btn-group">' +
				'	<a href="javascript:void(0);" data-name="search_btn_checked" ' +
				'	class="btn" title="เลือก"> ' +
				'	<i class="fa fa-circle-o"></i> เลือก</a> ' +
				'	</div>' +
				'</td>' + 
				'<td style="display: none;">'+ value.prodId +'</td>' +
				'<td style="text-align: center;">'+ value.prodCode +'</td>' +
				'<td style="text-align: center;">'+ value.prodName +'</td>' +
				'<td style="display: none;">'+ value.prodUomCode +'</td>' +		
			'</tr>'
			);		
		});
		$('#modal_search_product').modal('show');
		$('#modal_search_product #txt_search_word').val(search_word);	
	}
	
	$('a[name="btnSearchProduct"]').click(function(){ 
		curId = $(this).closest('div').prev().closest('div').find('input:hidden').attr('name');
		curName = $(this).closest('div').prev().closest('div').find('input:text').attr('name');
		if($('#'+curName).prop('disabled')){
			//$('#modal_search_product').modal('show');
			return false;
		}
	});	
	
	$('#modal_search_product #txt_search_word').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $(this).val()
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
								modalProductShow(params.search_word, data.data);	
						}	
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'#modal_search_product a[data-name="search_btn_checked"]',function() {
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('input[name='+curName+']').val($(this).closest("tr").find('td:eq(2)').text());
						
		$('#modal_search_product').modal('hide');
		//getList();
	});
	//Search Product End

	$('#prodCode').keyup(function(e){ 
		if(e.keyCode == 13) 
		{
			var params = {
				search_word: $(this).val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			} 
			curName = $(this).attr('name');
			curId = $(this).prev().attr('name');
			//alert(curId);
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_product_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	//alert(data);
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								//$('#tbl_items tbody').empty();
								return false; break;
							case 1 :
								$.each($.parseJSON(data.data), function(key,value){
									$('input[name='+curName+']').val(value.prodCode);
									$('input[name='+curId+']').val(value.prodId);
								});
								//getList();
								break;
							default : 
								modalProductShow(params.search_word, data.data);
						}	
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});








	//SEARCH Customer Begin
	function modalCustomerShow(search_word, data){
		$('#modal_search_customer #tbl_search_data_main tbody').empty();
		$.each($.parseJSON(data), function(key,value){
			$('#modal_search_customer #tbl_search_data_main tbody').append(
			'<tr>' +
				'<td style="text-align: center;">' +
				'	<div class="btn-group">' +
				'	<a href="javascript:void(0);" data-name="search_btn_checked" ' +
				'	class="btn" title="เลือก"> ' +
				'	<i class="fa fa-circle-o"></i> เลือก</a> ' +
				'	</div>' +
				'</td>' + 
				'<td style="display: none;">'+ value.id +'</td>' +
				'<td style="text-align: center;">'+ value.name +'</td>' +	
				'<td style="text-align: center;">'+ value.code +'</td>' +
			'</tr>'
			);		
		});
		$('#modal_search_customer').modal('show');
		$('#modal_search_customer #txt_search_word').val(search_word);	
	}
	
	$('a[name="btnSearchCustomer"]').click(function(){ 
		curId = $(this).closest('div').prev().closest('div').find('input:hidden').attr('name');
		curName = $(this).closest('div').prev().closest('div').find('input:text').attr('name');
		//alert(curId); alert(curName);
		if($('#'+curName).prop('disabled')){
			//$('#modal_search_customer').modal('show');
			return false;
		}				
	});	
	
	$('#modal_search_customer #txt_search_word').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $(this).val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_customer_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	//alert(data);
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								return false; break;
							default : 
								modalCustomerShow(params.search_word, data.data);	
						}	
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'#modal_search_customer a[data-name="search_btn_checked"]',function() {
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('input[name='+curName+']').val($(this).closest("tr").find('td:eq(2)').text());						
		$('#modal_search_customer').modal('hide');
		//getList();
	});
	//Search End

	$('#custName').keyup(function(e){ 
		if(e.keyCode == 13) 
		{
			var params = {
				search_word: $(this).val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			} 
			curName = $(this).attr('name');
			curId = $(this).prev().attr('name');
			//alert(curId);
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_customer_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	//lert(data);
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								//$('#tbl_items tbody').empty();
								return false; break;
							case 1 :
								$.each($.parseJSON(data.data), function(key,value){
									$('input[name='+curName+']').val(value.prodCode);
									$('input[name='+curId+']').val(value.prodId);
								});
								//getList();
								break;
							default : 
								modalCustomerShow(params.search_word, data.data);
						}	
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});







	$("#btnSubmit").click(function(){ 
		$('#form1').submit();
	});
	$('#form1').on("submit", function(e) { 
		if ($('#form1').smkValidate()) {
			$.ajax({
			url: '<?=$rootPage;?>_ajax.php',
			type: 'POST',
			data: new FormData( this ),
			processData: false,
			contentType: false,
			dataType: 'json'
			}).done(function (data) {
				if (data.success){  
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});
					setTimeout(function(){history.back();}, 2000);
				}else{
					$.smkAlert({
						text: data.message,
						type: 'danger',
						position:'top-center'
					});
				}
				$('#form1')[0].reset();
				$("#title").focus(); 
			})
			.error(function (response) {
				  alert(response.responseText);
			});  
			//.ajax		
			e.preventDefault();
		}   
		//end if 
		e.preventDefault();
	});
	//form.submit
});
//doc ready
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
