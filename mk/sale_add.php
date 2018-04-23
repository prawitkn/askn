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
$rootPage="sale";
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

<div class="wrapper">
<!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/select2.min.css">
  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; 
	
   $soNo = $_GET['soNo'];
	$sql = "
	SELECT a.`soNo`, a.`saleDate`,a.`poNo`,a.`piNo`, a.`custId`,  a.`shipToId`, a.`smId`, a.`revCount`
	, a.`deliveryDate`, a.`shipByLcl`, a.`shipByFcl`, a.`shipByRem`, a.`shippingMarksId`, a.`suppTypeFact`
	, a.`suppTypeImp`, a.`prodTypeOld`, a.`prodTypeNew`, a.`custTypeOld`, a.`custTypeNew`
	, a.`prodStkInStk`, a.`prodStkOrder`, a.`prodStkOther`, a.`prodStkRem`, a.`packTypeAk`
	, a.`packTypeNone`, a.`packTypeOther`, a.`packTypeRem`, a.`priceOnOrder`, a.`priceOnOther`
	, a.`priceOnRem`, a.`remark`, a.`plac2deliCode`, a.`plac2deliCodeSendRem`, a.`plac2deliCodeLogiRem`, a.`payTypeCode`, a.`payTypeCreditDays`
	, a.`isClose`, a.`statusCode`, a.`createTime`, a.`createByID`, a.`updateTime`, a.`updateById`
	, a.shippingMark, a.`remCoa`, a.`remPalletBand`, a.`remFumigate`
	, b.code as custCode, b.name as custName, b.addr1 as custAddr1, b.addr2 as custAddr2, b.addr3 as custAddr3, b.zipcode as custZipcode, b.tel as custTel, b.fax as custFax
	, st.code as shipToCode, st.name as shipToName, st.addr1 as shipToAddr1, st.addr2 as shipToAddr2, st.addr3 as shipToAddr3, st.zipcode as shipToZipcode, st.tel as shipToTel, st.fax as shipToFax
	, c.code as smCode, c.name as smName, c.surname as smSurname
	, spm.name as shippingMarksName, IFNULL(spm.filePath,'') as shippingMarksFilePath
	
	, d.userFullname as createByName
	, a.confirmTime, cu.userFullname as confirmByName
	, a.approveTime, au.userFullname as approveByName
	FROM `sale_header` a
	left join customer b on b.id=a.custId 
	left join shipto st on st.id=a.shipToId  
	left join salesman c on c.id=a.smId 
	left join shipping_marks spm on spm.id=a.shippingMarksId 
	left join user d on a.createById=d.userId
	left join user cu on a.confirmById=cu.userId
	left join user au on a.approveById=au.userId
	WHERE 1
	AND a.soNo=:soNo 					
	ORDER BY a.createTime DESC
	LIMIT 1
	";
	$stmt = $pdo->prepare($sql);			
	$stmt->bindParam(':soNo', $soNo);	
	$stmt->execute();
	$hdr = $stmt->fetch();			
	$soNo = $hdr['soNo'];
	$custId = $hdr['custId'];
   ?>

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
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>No. <?=$soNo;?></a></li>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">			
		<?php if($soNo==""){ ?>
			<h3 class="box-title">Add Sales Order</h3>
		<?php }else{ ?>
			<h3 class="box-title">Edit Sales Order : <label style="color: red;"><?=$soNo;?></label></h3>
		<?php } ?>        
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
				<form id="form1" action="sale_add_insert.php" method="post" class="form" novalidate>
					<input type="hidden" name="soNo" id="soNo" value="<?=$_GET['soNo'];?>" />
                <div class="col-md-6">   
					<div class="row">
						<div class="col-md-4 form-group">
                            <label for="saleDate">Sales Date</label>
                            <input id="saleDate" type="text" class="form-control datepicker" name="saleDate" data-smk-msg="Require sale Date." required>
                        </div>
						<div class="col-md-4 form-group">
                            <label for="poNo">PO No.</label>
                            <input type="text" name="poNo" id="poNo" class="form-control" data-smk-msg="Require PO No." value="<?=$hdr['poNo'];?>" required>
                        </div>		
						<div class="col-md-4 form-group">
							<label for="piNo">PI No.</label>
							<input type="text" name="piNo" id="piNo" class="form-control" value="<?=$hdr['piNo'];?>" >
						</div>							
					</div>
						
						<div class="form-group">
							<label for="custId" >Customer</label>
							<div class="form-group row">
								<div class="col-md-12">
									<input type="hidden" name="custId" class="form-control" value="<?=$hdr['custId'];?>"  />
									<!--<input type="text" name="custName" class="form-control" value="<?=$hdr['custName'];?>" <?php echo ($soNo<>""?' disabled ':'');?> />-->
									<label name="custName" ><?=$hdr['custName'];?></label>
									<a href="#" name="btn_search" class="btn btn-primary" <?php echo ($soNo<>""?' disabled ':'');?> ><i class="glyphicon glyphicon-search" ></i></a>
								</div>
							</div>
                        </div>
						
						<div class="form-group">
                            <label for="shipToId">Shipping to Customer</label>							
							<select id="shipToId" name="shipToId" class="form-control" data-smk-msg="Require Salesman." required>
								<option value=""> -- Select -- </option>
								<?php
								$sql_sm = "SELECT id, `code`,  `name`,  `addr1`,  `addr2`,  `addr3`,  `zipcode` FROM `shipto` WHERE `statusCode`='A' and custId=$custId ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = ($hdr['shipToId']==$row['id']?' selected ':'');
									echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['code'].' : '.$row['name'].'</option>';
								}
								?>
							</select>                            
                        </div>	
						
                        <div class="form-group">
                            <label for="smId">Salesman</label>							
							<select id="smId" name="smId" class="form-control" data-smk-msg="Require Salesman." required>
								<option value=""> -- Select -- </option>
								<?php
								$sql_sm = "SELECT id, `code`,  `name`, `surname`, `mobileNo`, `email` FROM `salesman` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = ($hdr['smId']==$row['id']?' selected ':'');
									echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['code'].' : '.$row['name'].' '.$row['surname'].'</option>';
								}
								?>
							</select>                            
                        </div>						
						
						<div class="form-group">
                            <label for="custAddr">Customer Address</label>
							<textarea id="custAddr" class="form-control" name="custAddr" disabled ><?=$hdr['shipToAddr1'].$hdr['shipToAddr2'].$hdr['shipToAddr3'].$hdr['shipToZipcode'];?>
							</textarea>
                        </div>
                        
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="deliveryDate">Delivery Date/Load Date</label>
								<input type="text" id="deliveryDate" name="deliveryDate" class="form-control datepicker" data-smk-msg="Require Delivery Date / Load Date." required>
							</div>
							<div class="col-md-6 form-group">
								
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="shippingMarksId">Shiping Marks</label>
								<select id="shippingMarksId" name="shippingMarksId" class="form-control" >
									<option value="0"> -- Select -- </option>
									<?php
									$sql_sm = "SELECT id, `code`,  `name`,  `typeCode`, `filePath` FROM `shipping_marks` WHERE `statusCode`='A' ";
									$result_sm = mysqli_query($link, $sql_sm);
									while($row = mysqli_fetch_assoc($result_sm)){
										$selected = ($hdr['shippingMarksId']==$row['id']?' selected ':'');
										echo '<option value="'.$row['id'].'" data-typeCode="'.$row['typeCode'].'" data-filePath="'.$row['filePath'].'" '.$selected.' >'.$row['code'].' : '.$row['name'].'</option>';
									}
									?>
								</select> 
								<img src="" id="shippingMarksImg" />
								<textarea id="shippingMarksRem" name="shippingMarksRem" class="form-control" ></textarea>
							</div>
							<div class="col-md-6 form-group">
								 <label for="shippingMarks">by</label><br/>
								<input type="checkbox" name="shipByLcl" id="shipByLcl" <?php echo ($hdr['shipByLcl']==1?' checked ':''); ?> >
								  <span class="checkboxtext">LCL</span>&nbsp;&nbsp;
								  <input type="checkbox" name="shipByFcl" id="shipByFcl"  <?php echo ($hdr['shipByFcl']==1?' checked ':''); ?>  >
								   <span class="checkboxtext">FCL</span>
									<input type="text" id="shipByRem" name="shipByRem" class="form-control"  maxlength="40" <?=$hdr['shipByRem'];?> />
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 form-group">
								
							</div>
							<div class="col-md-6 form-group">
								  
							</div>
						</div>
						
						
						<div class="row">					
							<div class="col-md-12 form-group">
								  <label for="remOption">Remark Option</label><br/>
									<input type="checkbox" name="remCoa" id="remCoa" <?php echo ($hdr['remCoa']==1?' checked ':''); ?> >
									 <span class="checkboxtext">ขอ COA&nbsp;&nbsp;</span> 
									  <input type="checkbox" name="remPalletBand" id="remPalletBand" <?php echo ($hdr['remPalletBand']==1?' checked ':''); ?> >
									<span class="checkboxtext">PALLET ตีตรา&nbsp;&nbsp;</span>  
									  <input type="checkbox" name="remFumigate" id="remFumigate" <?php echo ($hdr['remFumigate']==1?' checked ':''); ?> >
									<span class="checkboxtext">รมยาตู้คอนเทนเนอร์</span>	
							  </div>
						  </div>						  			
						
						<div class="row">					
							<div class="col-md-12 form-group">
								<label for="remark">Remark</label>
								  <textarea id="remark" name="remark" class="form-control"  maxlength="80"><?=$hdr['remark'];?></textarea>
							  </div>
						  </div>
						  						  
						 
						</div>
						
						<!-- col-md-6 --> 
						
						<div class="col-md-6">					  
						
						<div class="row">	
						  <div class="col-md-12 form-group">
							<label for="suppType">Product Supp Type</label><br/>
							  <input type="checkbox" name="suppTypeFact" id="suppTypeFact"  <?php echo ($soNo==""?' checked ':($hdr['suppTypeFact']==1?' checked ':'')); ?> >
							  Factory Product&nbsp;&nbsp;
							  <input type="checkbox" name="suppTypeImp" id="suppTypeImp" <?php echo ($hdr['suppTypeImp']==1?' checked ':''); ?> >
							  Import Product
						  </div>
						</div>

						
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<label for="prodType">Product Type</label><br/>
							<input type="checkbox" name="prodTypeOld" id="prodTypeOld"  <?php echo ($soNo==""?' checked ':($hdr['prodTypeOld']==1?' checked ':'')); ?> >
							  Old Product&nbsp;&nbsp;
							  <input type="checkbox" name="prodTypeNew" id="prodTypeNew" <?php echo ($hdr['prodTypeNew']==1?' checked ':''); ?> >
							  New Product
						  </div>
						  </div>
						  
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<label for="custType">Customer Type</label><br/>
								<input type="radio" name="custType" value="custTypeOld"  <?php echo ($soNo==""?' checked ':($hdr['custTypeOld']==1?' checked ':'')); ?> >
							  Old Customer&nbsp;&nbsp;
								<input type="radio" name="custType" value="custTypeNew" <?php echo ($hdr['custTypeNew']==1?' checked ':''); ?> >
							  New Customer
						  </div>
						  </div>	
						  
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<label for="prodStk">Product Stock</label><br/>
								  <input type="checkbox" name="prodStkInStk" id="prodStkInStk" <?php echo ($soNo==""?' checked ':($hdr['prodStkInStk']==1?' checked ':'')); ?> >
								  In Stock&nbsp;&nbsp;
								  <input type="checkbox" name="prodStkOrder" id="prodStkOrder" <?php echo ($hdr['prodStkOrder']==1?' checked ':''); ?> >
								  Order&nbsp;&nbsp;
								  <input type="checkbox" name="prodStkOther" id="prodStkOther" <?php echo ($hdr['prodStkOther']==1?' checked ':''); ?> >
								  Other <input type="text" name="prodStkRem" id="prodStkRem" class="col-md-2 form-control"  maxlength="40" style="display: <?=($hdr['prodStkOther']==1?'block;':'none;');?>" value="<?=$hdr['prodStkRem'];?>" >
								<!-- row -->
						  </div>
						  </div>
						  
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<label for="packType">Packing Type</label><br/>
								  <input type="checkbox" name="packTypeAk" id="packTypeAk" <?php echo ($soNo==""?' checked ':($hdr['packTypeAk']==1?' checked ':'')); ?> >
								  AK Logo&nbsp;&nbsp;
								  <input type="checkbox" name="packTypeNone" id="packTypeNone" <?php echo ($hdr['packTypeNone']==1?' checked ':''); ?> >
								  Non AK Logo&nbsp;&nbsp;
								  <input type="checkbox" name="packTypeOther" id="packTypeOther" <?php echo ($hdr['packTypeOther']==1?' checked ':''); ?> >
								  Other <input type="text" name="packTypeRem" id="packTypeRem" class="col-md-2 form-control"  maxlength="40" style="display:  <?=($hdr['packTypeOther']==1?'block;':'none;');?>" value="<?=$hdr['packTypeRem'];?>"  >
								<!-- row -->
						  </div>
						  </div>
											  
						  <div class="row">	
						  <div class="col-md-12 form-group">
								<label for="priceOn">Price On</label><br/>
								  <input type="radio" name="priceOn" value="priceOnOrder" <?php echo ($soNo==""?' checked ':($hdr['priceOnOrder']==1?' checked ':'')); ?> >
								  on Sales Order&nbsp;&nbsp;
								  <input type="radio" name="priceOn" value="priceOnOther" <?php echo ($soNo==""?'  ':($hdr['priceOnOther']==1?' checked ':'')); ?> >
								  Other 								  
								  <input type="text" name="priceOnRem" id="priceOnRem" class="col-md-2 form-control"  maxlength="40" style="display:  <?=($hdr['priceOnOther']==1?'block;':'none;');?>" value="<?=$hdr['priceOnRem'];?>"  >							 											  
						  </div>
						  </div>
						  
						  <div class="row">	
						  <div class="col-md-12 form-group">
							<div class="col-md-6 form-group">
								<label for="plac2deliCode">Place to Delivery</label><br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeFact" value="FACT"   data-smk-msg="Require Place to Delivery." required <?php echo ($soNo==""?' checked ':($hdr['plac2deliCode']=="FACT"?' checked ':'')); ?> >
								  Pick up by customer at AK<br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeSend" value="SEND" <?php echo ($soNo==""?'  ':($hdr['plac2deliCode']=="SEND"?' checked ':'')); ?> >
								  Send by AK Factory
								  <input type="textbox" name="plac2deliCodeSendRem" id="plac2deliCodeSendRem"  class="form-control"  maxlength="40" style="display: <?=($hdr['plac2deliCode']=='SEND'?'block;':'none;');?>" value="<?=$hdr['plac2deliCodeSendRem'];?>"  /><br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeMaps" value="MAPS" <?php echo ($soNo==""?'  ':($hdr['plac2deliCode']=="MAPS"?' checked ':'')); ?> >
								  Map<br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeLogi" value="LOGI" <?php echo ($soNo==""?'  ':($hdr['plac2deliCode']=="LOGI"?' checked ':'')); ?> >
								  Logistic
								<input type="textbox" name="plac2deliCodeLogiRem" id="plac2deliCodeLogiRem"  class="form-control"  maxlength="40" style="display: <?=($hdr['plac2deliCode']=='LOGI'?'block;':'none;');?>" value="<?=$hdr['plac2deliCodeLogiRem'];?>" />
							  </div>
							  
							<div class="col-md-6 form-group">
								<div class="row col-md-12">
									<div class="col-md-5">
										<label for="payTypeCode">Credit</label>
									</div>
									<div class="col-md-5">
										<input type="textbox" name="payTypeCreditDays" id="payTypeCreditDays"  class="form-control" value="<?=$hdr['payTypeCreditDays'];?>" />
									</div>
									<div class="col-md-2">
										Days
									</div>
								</div>
								<div class="row col-md-12">
							  <input type="radio" name="payTypeCode" value="CASH"  <?php echo ($soNo==""?' checked ':($hdr['payTypeCode']=="CASH"?' checked ':'')); ?>>
							  by Cash<br/>
							  <input type="radio" name="payTypeCode" value="CHEQ" <?php echo ($soNo==""?'  ':($hdr['payTypeCode']=="CHEQ"?' checked ':'')); ?> >
							  by Cheque<br/>
							  <input type="radio" name="payTypeCode" value="TRAN" <?php echo ($soNo==""?'  ':($hdr['payTypeCode']=="TRAN"?' checked ':'')); ?> >
							  Transfer
							  </div>
						  </div>
					  
							  </div>
						  </div>
						  <!-- row -->		  
                </div>
                <!-- col-md-6 -->
				
				
<div class="col-md-12">
	<button id="btn1" type="button" class="btn btn-primary">Submit</button>		
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
	//$("#custName").focus();
	
// Append and Hide spinner.          
var spinner = new Spinner().spin();
$("#spin").append(spinner.el);
$("#spin").hide();
//      
     
	 
	//SEARCH Begin
	$('a[name="btn_search"]').click(function(){
		//prev() and next() count <br/> too.	
		//$txtName = $(this).closest("div").prev().find('input[type="text"]');
		//$txtName = $(this).closest("div").prev().find('label');
		//alert($btn.attr('name'));
		//curId = $btn.attr('name');
		curId = $(this).closest("div").find('input[type="hidden"]').attr('name');
		curName = $(this).closest("div").find('label').attr('name');
		//alert($txtName);
		if(!$(this).attr('disabled')){
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
		$('label[name='+curName+']').text($(this).closest("tr").find('td:eq(2)').text()+' : '+$(this).closest("tr").find('td:eq(3)').text());
		
		$('#smId').val($(this).closest("tr").find('td:eq(4)').text());
		/*$('#custAddr').val($(this).closest("tr").find('td:eq(6)').text()+
			$(this).closest("tr").find('td:eq(7)').text()+
			$(this).closest("tr").find('td:eq(8)').text()+
			$(this).closest("tr").find('td:eq(9)').text());*/
		//ajax shipto begin
		var params = {
			id: $(this).closest("tr").find('td:eq(1)').text()
		}; //alert(params.id);
		$.ajax({
		  url: "get_shipto_by_cust_ajax.php",
		  type: "post",
		  data: params,
		datatype: 'json',
		  success: function(data){
						//alert(data);
						$('#shipToId').empty();
						$.each($.parseJSON(data), function(key,value){
							$('#shipToId').append('<option value="'+value.id+'" >'+value.code+' : '+value.name+'</option>' );
							$('#custAddr').text(value.addr1+value.addr2+value.addr3+value.zipcode);							
						});
					
		  }, //success
		  error:function(){
			  alert('error');
		  }   
		});
		//ajax shipto end.
		
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
						//alert('a');
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
	 $("#shipToId").on("change",function(e) {
		//alert($('option:selected', this).attr('data-addr1'));
		var params = {
			id: $(this).val() //$('option:selected', this).val();
		}; 
		$.ajax({
			  url: "get_shipto_ajax.php",
			  type: "post",
			  data: params,
			datatype: 'json',
			  success: function(data){
					//alert(data);
					$('#custAddr').empty();
					$.each($.parseJSON(data), function(key,value){
						alert(value.addr1+value.addr2+value.addr3+value.zipcode);
						$('#custAddr').text(value.addr1+value.addr2+value.addr3+value.zipcode);
					});				
			  }, //success
			  error:function(){
				  alert('error');
			  }   
			}); 
		//$('#smId').val($('option:selected', this).attr('data-smId'));
		e.preventDefault();
	 });
	 
	 $("#shippingMarksId").on("change",function(e) {
		 if($('option:selected', this).attr('data-typeCode')=="IMG"){
			 $('#shippingMarksRem').attr('disabled','').text("").css('display','none');
			 $('#shippingMarksImg').css('display','block');
			 $('#shippingMarksImg').attr('src','../images/shippingMarks/'+$('option:selected', this).attr('data-filePath'));
		 }else{
			 $('#shippingMarksImg').css('display','none');
			 $('#shippingMarksRem').css('display','block');
			 $('#shippingMarksImg').attr('src','');
			 $('#shippingMarksRem').val($('option:selected', this).text()).attr('disabled','disabled');
		 }
		
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
	$('input[type=radio][name=priceOn]').on("change" ,function() {
		if (this.value == 'priceOnOther') {
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
			language: 'en',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: false              //Set เป็นปี พ.ศ.
		});  //กำหนดเป็นวันปัจุบัน
		
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($hdr['saleDate']<>"") { ?>
			var queryDate = '<?=$hdr['saleDate'];?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#saleDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#saleDate').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($hdr['deliveryDate']<>"") { ?>
			var queryDate = '<?=$hdr['deliveryDate'];?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#deliveryDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#deliveryDate').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
	});
</script>




<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
