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

$soNo = "";
?>
<?php
	$sqlCust = "";
	switch($s_userGroupCode){
		case 'sales' :
			//$sqlCust = " AND smId=$s_smId ";
			break;
		case 'salesAdmin' :
			//$sqlCust = " AND smId=$s_smId ";
			break;
		default :
	}
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
<!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/select2.min.css">
  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->	
    <section class="content-header">	  
	  <h1><i class="glyphicon glyphicon-shopping-cart"></i>
       Sales Order
        <small>Sales Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Sales Order List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?soNo=<?=$soNo;?>"><i class="glyphicon glyphicon-edit"></i>No. <?=$soNo;?></a></li>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Sales Order</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
				<form id="form1" action="sale_add_insert.php" method="post" class="form" novalidate>
                <div class="col-md-6">   
					<div class="row">
						<div class="col-md-4 form-group">
                            <label for="saleDate">Sales Date</label>
                            <input id="saleDate" type="text" class="form-control datepicker" name="saleDate" data-smk-msg="Require sale Date." required>
                        </div>
						<div class="col-md-4 form-group">
                            <label for="poNo">PO No.</label>
                            <input type="text" name="poNo" id="poNo" class="form-control" data-smk-msg="Require PO No." required>
                        </div>		
						<div class="col-md-4 form-group">
							<label for="piNo">PI No.</label>
							<input type="text" name="piNo" id="piNo" class="form-control" >
						</div>							
					</div>
						
						<div class="form-group">
							<label for="custId" >Customer Name</label>
							<div class="form-group row">
								<div class="col-md-9">
									<input type="hidden" name="custId" class="form-control" value=""  />
									<input type="text" name="custName" class="form-control" value=""  />
								</div>
								<div class="col-md-3">
									<a href="#" name="btn_search" class="btn btn-primary"  ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>
							</div>
                        </div>
						
                        <div class="form-group">
                            <label for="smId">Salesman Name</label>							
							<select id="smId" name="smId" class="form-control" data-smk-msg="Require Salesman." required>
								<option value=""> -- Select -- </option>
								<?php
								$sql_sm = "SELECT id, `code`,  `name`, `surname`, `mobileNo`, `email` FROM `salesman` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									echo '<option value="'.$row['id'].'">'.$row['code'].' : '.$row['name'].' '.$row['surname'].'</option>';
								}
								?>
							</select>                            
                        </div>						
						
						<div class="form-group">
                            <label for="custAddr">Customer Address</label>
							<textarea id="custAddr" class="form-control" name="custAddr" disabled></textarea>
                        </div>
                        
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="deliveryDate">Delivery Date/Load Date</label>
								<input type="text" id="deliveryDate" name="deliveryDate" class="form-control datepicker" data-smk-msg="Require Delivery Date / Load Date." required>
							</div>
							<div class="col-md-6 form-group">
								<label for="shippingMarksId">Shiping Marks</label>
								<select id="shippingMarksId" name="shippingMarksId" class="form-control" >
									<option value="0"> -- Select -- </option>
									<?php
									$sql_sm = "SELECT id, `code`,  `name`, `filePath` FROM `shipping_marks` WHERE `statusCode`='A' ";
									$result_sm = mysqli_query($link, $sql_sm);
									while($row = mysqli_fetch_assoc($result_sm)){
										echo '<option value="'.$row['id'].'" data-filePath="'.$row['filePath'].'"  >'.$row['code'].' : '.$row['name'].'</option>';
									}
									?>
								</select>  
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="shippingMarks">by</label><br/>
								<input type="checkbox" name="shipByLcl" id="shipByLcl"  >
								  LCL&nbsp;&nbsp;
								  <input type="checkbox" name="shipByFcl1x20" id="shipByFcl1x20"  >
								  FCL 1x20'&nbsp;&nbsp;
								  <input type="checkbox" name="shipByFcl1x40" id="shipByFcl1x40"  >
									FCL 1x40'
							</div>
							<div class="col-md-6 form-group">
								  <img src="" id="shippingMarksImg" />
							</div>
						</div>
						
						
						<div class="row">					
							<div class="col-md-12 form-group">
								  <label for="remOption">Remark Option</label><br/>
									<input type="checkbox" name="remCoa" id="remCoa"  >
									  ขอ COA&nbsp;&nbsp;
									  <input type="checkbox" name="remPalletBand" id="remPalletBand"  >
									  PALLET ตีตรา&nbsp;&nbsp;
									  <input type="checkbox" name="remFumigate" id="remFumigate"  >
										รมยาตู้คอนเทนเนอร์
							  </div>
						  </div>						  			
						
						<div class="row">					
							<div class="col-md-12 form-group">
								<label for="remark">Remark</label>
								  <input type="text" id="remark" name="remark" class="form-control" />
							  </div>
						  </div>
						  						  
						 
						</div>
						
						<!-- col-md-6 --> 
						
						<div class="col-md-6">					  
						
						<div class="row">	
						  <div class="col-md-12 form-group">
							<label for="suppType">Product Supp Type</label><br/>
							  <input type="checkbox" name="suppTypeFact" id="suppTypeFact"  checked>
							  Factory Product&nbsp;&nbsp;
							  <input type="checkbox" name="suppTypeImp" id="suppTypeImp" >
							  Import Product
						  </div>
						</div>

						
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<label for="prodType">Product Type</label><br/>
							<input type="checkbox" name="prodTypeOld" id="prodTypeOld"  checked>
							  Old Product&nbsp;&nbsp;
							  <input type="checkbox" name="prodTypeNew" id="prodTypeNew" >
							  New Product
						  </div>
						  </div>
						  
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<label for="custType">Customer Type</label><br/>
							  <input type="checkbox" name="custTypeOld" id="custTypeOld"  checked>
							  Old Customer&nbsp;&nbsp;
							  <input type="checkbox" name="custTypeNew" id="custTypeNew" >
							  New Customer
						  </div>
						  </div>
						  
						  <div class="row">	
						  <div class="col-md-12 form-group">
								<label for="prodName">Product Name</label><br/>
								<div class="col-md-6">
								  <input type="checkbox" name="prodGFC" id="prodGFC" value="true"  >
								  GLASS FIBER CLOTH</br>
								  <input type="checkbox" name="prodGFM" id="prodGFM"  >
								  GLASS FIBER MESH</br>
								  <input type="checkbox" name="prodGFT" id="prodGFT"  >
								  GLASS FIBER TAPE</br>
								  <input type="checkbox" name="prodSC" id="prodSC"  >
								  SILICA CLOTH</br>
								  <input type="checkbox" name="prodCFC" id="prodCFC"  >
								  CABON FIBER CLOTH</br>
								</div>
								<div class="col-md-6">
								  <input type="checkbox" name="prodEGWM" id="prodEGWM"  >
								  E-GLASS WOOL MAT</br>
								  <input type="checkbox" name="prodGT" id="prodGT"  >
								  GLASS TISSUE</br>
								  <input type="checkbox" name="prodCSM" id="prodCSM"  >
								  CHOPPED STRAND MAT</br>
								  <input type="checkbox" name="prodWR" id="prodWR"  >
								  WOVEN ROVING
								  </div>
								
						  </div>
						  </div>
						  
						  
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<label for="prodStk">Product Stock</label><br/>
								  <input type="checkbox" name="prodStkInStk" id="prodStkInStk"  checked>
								  In Stock&nbsp;&nbsp;
								  <input type="checkbox" name="prodStkOrder" id="prodStkOrder" >
								  Order&nbsp;&nbsp;
								  <input type="checkbox" name="prodStkOther" id="prodStkOther" >
								  Other <input type="text" name="prodStkRem" id="prodStkRem" class="col-md-2 form-control" style="display: none;" >
								<!-- row -->
						  </div>
						  </div>
						  
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<label for="packType">Packing Type</label><br/>
								  <input type="checkbox" name="packTypeAk" id="packTypeAk"  checked>
								  AK Logo&nbsp;&nbsp;
								  <input type="checkbox" name="packTypeNone" id="packTypeNone" >
								  Non AK Logo&nbsp;&nbsp;
								  <input type="checkbox" name="packTypeOther" id="packTypeOther" >
								  Other <input type="text" name="packTypeRem" id="packTypeRem" class="col-md-2 form-control" style="display: none;" >
								<!-- row -->
						  </div>
						  </div>
											  
						  <div class="row">	
						  <div class="col-md-12 form-group">
								<label for="priceOn">Price On</label><br/>
								  <input type="checkbox" name="priceOnOrder" id="priceOnOrder"  checked>
								  on Sales Order&nbsp;&nbsp;
								  <input type="checkbox" name="priceOnOther" id="priceOnOther" >
								  Other 								  
								  <input type="text" name="priceOnRem" id="priceOnRem" class="col-md-2 form-control" style="display: none;" >							 											  
						  </div>
						  </div>
						  
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<div class="col-md-6 form-group">
								<label for="plac2deliCode">Place to Delivery</label><br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeFact" value="FACT"   data-smk-msg="Require Place to Delivery." required checked>
								  AK Factory<br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeSend" value="SEND" >
								  Send by AK Factory
								  <input type="textbox" name="plac2deliCodeSendRem" id="plac2deliCodeSendRem"  class="form-control"  style="display: none;"  /><br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeMaps" value="MAPS" >
								  Map<br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeLogi" value="LOGI" >
								  Logistic
								<input type="textbox" name="plac2deliCodeLogiRem" id="plac2deliCodeLogiRem"  class="form-control"  style="display: none;"  />
							  </div>
							  
							<div class="col-md-6 form-group">
								<div class="row col-md-12">
									<div class="col-md-5">
										<label for="payTypeCode">Credit</label>
									</div>
									<div class="col-md-5">
										<input type="textbox" name="payTypeCreditDays" id="payTypeCreditDays"  class="form-control" />
									</div>
									<div class="col-md-2">
										Days
									</div>
								</div>
								<div class="row col-md-12">
							  <input type="radio" name="payTypeCode" value="CASH"  checked>
							  by Cash<br/>
							  <input type="radio" name="payTypeCode" value="CHEQ" >
							  by Cheque<br/>
							  <input type="radio" name="payTypeCode" value="TRAN" >
							  Transfer
							  </div>
						  </div>
					  
							  </div>
						  </div>
						  <!-- row -->		  
                </div>
                <!-- col-md-6 -->
				
				
<div class="col-md-12">
	<button id="btn1" type="button" class="btn btn-default">Submit</button>		
</div>

		
            </div>          
		</form>
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
<div id="modal_search" class="modal fade" role="dialog">
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
				<label for="txt_search_word" class="control-label col-md-2">Customer Name</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_word" />
				</div>
			</div>
		
		<table id="tbl_search" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td style="display: none;">Id</td>
					<td>Code</td>
					<td>Name</td>
					<td style="display: none;">SM ID</td>
					<td>Salesman</td>
					<td style="display: none;">Addr1</td>
					<td style="display: none;">Addr2</td>
					<td style="display: none;">Addr3</td>
					<td style="display: none;">Zipcode</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		</form>
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
<!-- Select2 -->
<script src="plugins/select2/select2.full.min.js"></script>
<!-- smoke -->
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
	$('a[name="btn_search"]').click(function(){
		//prev() and next() count <br/> too.	
		$txtName = $(this).closest("div").prev().find('input[type="text"]');
		//alert($btn.attr('name'));
		//curId = $btn.attr('name');
		curId = $(this).closest("div").prev().find('input[type="hidden"]').attr('name');
		curName = $(this).closest("div").prev().find('input[type="text"]').attr('name');
		//alert($txtName);
		if(!$txtName.prop('disabled')){
			$('#modal_search').modal('show');
		}
	});	
	$('#txt_search_word').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#txt_search_word').val()
			};
			if(params.search_word.length < 3){
				alert('Search word must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_customer_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){
								//alert(data);
								$('#tbl_search tbody').empty();
								$.each($.parseJSON(data), function(key,value){
									$('#tbl_search tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="btn_search_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' +
										'<td style="display: none;">'+ value.id +'</td>' +	//1
										'<td>'+ value.code +'</td>' +	
										'<td>'+ value.name +'</td>' +	
										'<td style="display: none;">'+ value.smId +'</td>' +
										'<td>'+ value.smName +'</td>' +
										'<td style="display: none;">'+ value.addr1 +'</td>' +
										'<td style="display: none;">'+ value.addr2 +'</td>' +
										'<td style="display: none;">'+ value.addr3 +'</td>' +
										'<td style="display: none;">'+ value.zipcode +'</td>' +
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
	
	$(document).on("click",'a[data-name="btn_search_checked"]',function() {
		
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('input[name='+curName+']').val($(this).closest("tr").find('td:eq(3)').text());
		$('#smId').val($(this).closest("tr").find('td:eq(4)').text());
		$('#custAddr').val($(this).closest("tr").find('td:eq(6)').text()+
			$(this).closest("tr").find('td:eq(7)').text()+
			$(this).closest("tr").find('td:eq(8)').text()+
			$(this).closest("tr").find('td:eq(9)').text());
		
		//$('#'+curName).val($(this).closest("tr").find('td:eq(2)').text());	
		$('#modal_search').modal('hide');
	});
	//Search End

	
	$('#btn1').click (function(e) {
		if ($('#form1').smkValidate()){
			$.post("sale_add_insert.php", $("#form1").serialize() )
				.done(function(data) {
					if (data.success){   
						/*$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});*/
						alert(data.message);
						window.location.href = "sale.php";
					} else {
						alert('a');
						$.smkAlert({
							text: data.message,
							type: 'danger',
							//position:'top-center'
						});
					}
					$('#form1').smkClear();
					//$("#visitDate").focus();
				});
			e.preventDefault();
		}//.smkValidate()
	});//.btn_click
	
	$("#custId").on("change",function(e) {
		$('#custAddr').val($('option:selected', this).attr('data-custAddr'));
		$('#smId').val($('option:selected', this).attr('data-smId'));
		e.preventDefault();
	 });
	 $("#shippingMarksId").on("change",function(e) {
		$('#shippingMarksImg').attr('src','../asset/img/shippingMarks/'+$('option:selected', this).attr('data-filePath'));
		e.preventDefault();
	 });
	$('input[name=prodStkOther]').on("change" ,function() {
		if($(this).is(':checked')){
			$('#prodStkRem').show().focus();
		}else{
			$('#prodStkRem').val('').hide();
		}
	}); 
	$('input[name=packTypeOther]').on("change" ,function() {
		if($(this).is(':checked')){
			$('#packTypeRem').show().focus();
		}else{
			$('#packTypeRem').val('').hide();
		}
	}); 
	$('input[name=priceOnOther]').on("change" ,function() {
		if($(this).is(':checked')){
			$('#priceOnRem').show().focus();
		}else{
			$('#priceOnRem').val('').hide();
		}
	}); 
	$('input[type=radio][name=plac2deliCode]').on("change" ,function() {
		if (this.value == 'SEND') {
            $('#plac2deliCodeSendRem').show().focus();
        }else{
			$('#plac2deliCodeSendRem').val('').hide();
		}
		if (this.value == 'LOGI') {
            $('#plac2deliCodeLogiRem').show().focus();
        }else{
			$('#plac2deliCodeLogiRem').val('').hide();
		}
	}); 
});
        
        
   
  </script>
  
  <link href="bootstrap-datepicker-custom-thai/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
    <script src="bootstrap-datepicker-custom-thai/dist/js/bootstrap-datepicker-custom.js"></script>
    <script src="bootstrap-datepicker-custom-thai/dist/locales/bootstrap-datepicker.th.min.js" charset="UTF-8"></script>
  
<script>
	$(document).ready(function () {
		$('.datepicker').datepicker({
			todayHighlight: true,
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			format: 'dd/mm/yyyy',
			todayBtn: true,
			language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: true              //Set เป็นปี พ.ศ.
		});  //กำหนดเป็นวันปัจุบัน
	});
</script>




<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
