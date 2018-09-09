<?php
  //  include '../db/database.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>
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
   <?php include 'leftside.php'; ?>

   <?php

	$rootPage="sale2";

   $soNo = (isset($_GET['soNo'])?$_GET['soNo']:'');

	$sql = "
	SELECT a.`soNo`, a.`poNo`, a.`piNo`, a.`saleDate`, a.`custId`, a.`shipToId`, a.`smId`, a.`revCount`, a.`deliveryDate`, a.`suppTypeId`, a.`stkTypeId`, a.`packageTypeId`, a.`priceTypeId`, a.`deliveryTypeId`, a.`shippingMarksId`, a.`deliveryRem`, a.`containerLoadId`, a.`creditTypeId`, a.`shippingMark`, a.`remark`, a.`payTypeCreditDays`, a.`isClose`, a.`statusCode`, a.`createTime`, a.`createById`, a.`updateTime`, a.`updateById`, a.`confirmTime`, a.`confirmById`, a.`approveTime`, a.`approveById`
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
	$hdrStatuscode = $hdr['statusCode'];
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
			<div class="col-md-12">
				
				
					
				<ul class="nav nav-pills">
					<li class="nav-item active"><a class="nav-link" data-toggle="pill" href="#home" >Header <i class="fa fa-caret-right"></i></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="pill" href="#menu1" >Header Option <i class="fa fa-caret-right"></i></a></li>
					<?php if( $soNo<>"" ){ ?>
					<li class="nav-item"><a class="nav-link" data-toggle="pill" href="#menu2">Item <i class="fa fa-caret-right"></i></a></li>
				<?php } ?>
				</ul>

			<form id="form1" action="#" method="post" class="form" novalidate>
					<input type="hidden" name="action" value="add" />
					
					<input type="hidden" name="soNo" id="soNo" value="<?=$_GET['soNo'];?>" />

			  <div class="tab-content">
				<div id="home" class="tab-pane fade in active">
				  <?php 
					?>
				  <div class="row col-md-12">
					<div class="row">
						<div class="col-md-3 form-group">
                            <label for="saleDate">Sales Date</label>
                            <input id="saleDate" type="text" class="form-control datepicker" name="saleDate" data-smk-msg="Require sale Date." required>
                        </div>
						<div class="col-md-3 form-group">
                            <label for="poNo">PO No.</label>
                            <input type="text" name="poNo" id="poNo" class="form-control" data-smk-msg="Require PO No." value="<?=$hdr['poNo'];?>" required>
                        </div>		
						<div class="col-md-3 form-group">
							<label for="piNo">PI No.</label>
							<input type="text" name="piNo" id="piNo" class="form-control" value="<?=$hdr['piNo'];?>" >
						</div>
						<div class="col-md-3 form-group">
							<label for="deliveryDate">Delivery Date/Load Date</label>
							<input type="text" id="deliveryDate" name="deliveryDate" class="form-control datepicker" data-smk-msg="Require Delivery Date / Load Date." required>
						</div>	
					</div>
					<div class="row">
						<div class="col-md-6 form-group">
							<label for="custId" >Customer</label>
							<select id="custId" name="custId" class="form-control" data-smk-msg="Require Customer." required>
								<option value=""> -- Select -- </option>
								<?php
								$sql_sm = "SELECT hdr.id, hdr.`code`,  hdr.`name`,  hdr.`addr1`,  hdr.`addr2`,  hdr.`addr3`,  hdr.`zipcode` 
								FROM `customer` hdr WHERE `statusCode`='A' ";
								switch($s_userGroupCode){ 
									case 'it' : 
									case 'admin' : 
										break;
									case 'sales' :
										$sql .= "AND hdr.smId=$s_smId ";
										break;
									case 'salesAdm' :
										$sql .= "AND hdr.smAdmId=$s_smId ";
										break;
									default :
								}
								$sql_sm.="ORDER BY hdr.name ASC ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = ($hdr['custId']==$row['id']?' selected ':'');
									echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].' [ '.$row['code'].']</option>';
								}
								?>
							</select>   								
						</div>
						<div class="col-md-6 form-group">
							<label for="shipToId">Shipping to Customer</label>							
							<select id="shipToId" name="shipToId" class="form-control" data-smk-msg="Require Salesman." required>
								<option value=""> -- Select -- </option>
								<?php
								$sql_sm = "SELECT id, `code`,  `name`,  `addr1`,  `addr2`,  `addr3`,  `zipcode` FROM `shipto` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = ($hdr['shipToId']==$row['id']?' selected ':'');
									echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['code'].' : '.$row['name'].'</option>';
								}
								?>
							</select>     
						</div>
					</div>
					<!--/.row-->
					<div class="row">
						<div class="col-md-6 form-group">
							<label for="custAddr">Customer Address</label>
							<textarea id="custAddr" class="form-control" name="custAddr" disabled ><?=$hdr['shipToAddr1'].$hdr['shipToAddr2'].$hdr['shipToAddr3'].$hdr['shipToZipcode'];?>
							</textarea>
						</div>
						<div class="col-md-6 form-group">
							<label for="shipToAddr">Ship To Address</label>
							<textarea id="shipToAddr" class="form-control" name="custAddr" disabled ><?=$hdr['shipToAddr1'].$hdr['shipToAddr2'].$hdr['shipToAddr3'].$hdr['shipToZipcode'];?>
							</textarea>  
						</div>
					</div>
					<!--/.row-->
					<div class="row">
						<div class="col-md-6 form-group">
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
						<div class="col-md-6 form-group">
							
						</div>
					</div>
					<!--/.row-->
						
					</div>
					<!--/.col-md-12-->
				</div>
				<!--/.tab-pan-->
				
				<div id="menu1" class="tab-pane fade in">
					
					
					<div class="col-md-6">					  
					
					<div class="row">	
					  <div class="col-md-6 form-group">
						<label for="suppTypeId">กลุ่มสินค้า</label>
						<select id="suppTypeId" name="suppTypeId" class="form-control" >
							<?php
							$sql_sm = "SELECT id,  `name` FROM `sale_supp_type` WHERE `statusCode`='A' ORDER BY seqNo ";
							$result_sm = mysqli_query($link, $sql_sm);
							while($row = mysqli_fetch_assoc($result_sm)){
								$selected = "";// ($hdr['containerLoadId']==$row['id']?' selected ':'');
								echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
							}
							?>
						</select> 
						<!--<input type="text" name="suppTypeRemark" id="suppTypeRemark" class="col-md-2 form-control"  maxlength="40" style="display: <?=($hdr['suppTypeId']==1?'block;':'none;');?>" value="<?=$hdr['suppTypeRemark'];?>" />-->
					  </div>
					  <!--/.col-md-6-->

					  <div class="col-md-6 form-group">
						<label for="stkTypeId">สินค้ามีในสต๊อก</label>
						<select id="stkTypeId" name="stkTypeId" class="form-control" >
							<?php
							$sql_sm = "SELECT id,  `name` FROM `sale_stk_type` WHERE `statusCode`='A'  ORDER BY seqNo  ";
							$result_sm = mysqli_query($link, $sql_sm);
							while($row = mysqli_fetch_assoc($result_sm)){
								$selected = "";// ($hdr['containerLoadId']==$row['id']?' selected ':'');
								echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
							}
							?>
						</select> 
					  </div>
					  <!--/.col-md-6-->

					</div>
					<!--/.row-->
					
					  
					  <div class="row">	
						  <div class="col-md-6 form-group">
							<label for="packageTypeId">บรรจุภัณฑ์ (Package)</label>
							<select id="packageTypeId" name="packageTypeId" class="form-control" >
								<?php
								$sql_sm = "SELECT id,  `name` FROM `sale_package_type` WHERE `statusCode`='A'  ORDER BY seqNo  ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = "";// ($hdr['containerLoadId']==$row['id']?' selected ':'');
									echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
								}
								?>
							</select> 
						  </div>
						  <!--/.col-md-6-->

						  <div class="col-md-6 form-group">
							<label for="priceTypeId">ราคา (Price)</label>
							<select id="priceTypeId" name="priceTypeId" class="form-control" >
								<?php
								$sql_sm = "SELECT id,  `name` FROM `sale_price_type` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = "";// ($hdr['containerLoadId']==$row['id']?' selected ':'');
									echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
								}
								?>
							</select> 
						  </div>
						  <!--/.col-md-6-->
					  </div>
					  <!--/.row-->
					 					  
					  <div class="row">	
						<div class="col-md-6 form-group">
							<label for="deliveryTypeId">Place to Delivery</label>
							<select id="deliveryTypeId" name="deliveryTypeId" class="form-control" >
								<?php
								$sql_sm = "SELECT id,  `name` FROM `sale_delivery_type` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = "";// ($hdr['containerLoadId']==$row['id']?' selected ':'');
									echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
								}
								?>
							</select> 
						</div>	
						  
						<div class="col-md-6">
							<div class="col-md-5">
								<label for="payTypeCode">Credit</label>
							</div>
							<div class="col-md-5">
								<input type="textbox" name="payTypeCreditDays" id="payTypeCreditDays"  class="form-control" value="<?=$hdr['payTypeCreditDays'];?>" 
								onkeypress="return numbersOnly(this, event);" 
								onpaste="return false;" style="text-align: right;"
								/>
							</div>
							<div class="col-md-2">
								Days
							</div>

							<div class="col-md-12">
								<select id="creditTypeId" name="creditTypeId" class="form-control" >
									<?php
									$sql_sm = "SELECT id,  `name` FROM `sale_credit_type` WHERE `statusCode`='A' ";
									$result_sm = mysqli_query($link, $sql_sm);
									while($row = mysqli_fetch_assoc($result_sm)){
										$selected = "";// ($hdr['containerLoadId']==$row['id']?' selected ':'');
										echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
						<!--/.col-md-6-->

					</div>
					<!--/.row-->

					<div class="row">
						<div class="col-md-6 form-group">
							<label for="remark">Remark</label>
							  <textarea id="remark" name="remark" class="form-control"  maxlength="80"><?=$hdr['remark'];?></textarea>
						  </div>
					</div>
					
				</div>
				<!--/.col-md-6-->
					
				<div class="col-md-6" style="background-color: #50dffc;">		
					<h3>กรณีส่งต่างประเทศ</h3>
					<div class="row">
						<div class="col-md-6 form-group">
							<label for="containerLoadId">Container Load</label>
							<select id="containerLoadId" name="containerLoadId" class="form-control" >
								<option value="0"> -- Select -- </option>
								<?php
								$sql_sm = "SELECT id,  `name` FROM `sale_container_load_type` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = "";// ($hdr['containerLoadId']==$row['id']?' selected ':'');
									echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
								}
								?>
							</select> 
						</div>
						<!--/.col-md-6-->

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
						</div>
					</div>
					<!--/.row-->
					
					<div class="row">					
						<div class="col-md-6 form-group">
							  <label for="optTypeId">Optional</label>
							<select id="optTypeId" name="optTypeId" class="form-control" >
								<option value="0"> -- Select -- </option>
								<?php
								$sql_sm = "SELECT id,  `name` FROM `sale_option_type` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = "";// ($hdr['containerLoadId']==$row['id']?' selected ':'');
									echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
								}
								?>
							</select> 
						  </div>
					  </div>
					  <!--/.row-->	
											  
					 
					</div>					
					<!-- col-md-6 --> 
					
										  			
					
					</div>
					<!--/.row-->
		</form>
		<!--/.form-->


		











		


	<?php if( $soNo <> "" ) { ?>
		<div id="menu2" class="tab tab-pane">
			<form id="form2" name="form2" action="#" method="post" class="form" novalidate>				
			<input type="hidden" name="action" value="itemAdd" />

			<input type="hidden" name="soNo" value="<?=$_GET['soNo'];?>" />			
			<input type="hidden" name="refItmId" id="refItmId" value="" />
			
			<div class="row">
				  <div class="col-md-3 form-group">
					<label for="remark">Product : </label>
					  <input type="hidden" name="prodId" id="prodId" class="form-control" value=""   data-smk-msg="Require Product ID" required   />
					<input type="text" name="prodCode" id="prodCode" class="form-control" value=""  data-smk-msg="Require Product" required  />
				  </div>
				  <!--/.col-md-2-->

				  	<div class="col-md-2 form-group">
					<label for="remark">Qty : </label>
						<label class="pull-right" id="lblUom">UOM</label>
					  <input id="qty" type="text" class="form-control" name="qty" value=""  style="text-align: right;" data-smk-msg="Require Quantity."required
								onkeypress="return numbersOnly(this, event);" 
								onpaste="return false;"
									>
				  </div>
				  <!--/.col-md-3-->

				<div class="col-md-3 form-group">
						<label for="itemRemark">Item Remark : </label>
						  <input type="text" id="itemRemark" name="itemRemark" class="form-control" /> 

					  </div>
					  <!--/.col-md-3-->

					  	<div class="col-md-2 form-group">
						<label for="remark">Roll Length : </label>
						  <select id="rollLengthId" name="rollLengthId" class="form-control" >
									</select>

					  </div>
					  <!--/.col-md-3-->


				<div class="col-md-2 form-group">
					<label for="remark">Item Delivery Date : </label>
					  <input id="deliveryDate" type="text" class="form-control datepicker" name="deliveryDate" data-smk-msg="Require Order Date." required>
				  </div>
				  <!--/.col-md-4-->

				  <div class="col-md-12">
					<span id="prodName"></span> : <span id="prodCode"></span> / <label id="prodDesc">...</label>	

					<?php if($hdr['statusCode']=='A' OR $hdr['statusCode']=='B') { ?>
					  <a href="#" name="btnItmSubmit" id="btnItmSubmit" class="btn btn-primary pull-right" ><i class="fa fa-save"></i> Save</a>  

					  <a href="#" name="btnItmClear" id="btnItmClear" class="btn btn-primary pull-right" style="margin-right: 5px;" ><i class="fa fa-refresh"></i> Clear</a>  
					<?php } ?>						  
				  </div>
		  </div>
		  <!--/.row-->

  <div class="row">
  		<div class="col-md-12">
  			<div class="row  col-md-12 table-responsive">
			<table id="tbl_items" class="table table-striped">
				<thead>
					<tr>
						<th>No.</th>
						<th>Product Name</th>							
						<th>Product Code</th>	
						<th>Qty</th>											
						<th>Remark</th>				
						<th>Delivery Date</th>
						<?php if($hdrStatuscode=='A' OR $hdrStatuscode=='B') { ?>
						<th>#</th>
						<?php } ?>							
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>
			</div>
			<!--/.table-responsive-->
  		</div>
  </div>

	</form>
	<!--/.form2-->		

</div>
<!--/.tab-pane-->

<?php } ?>
	

					
	<div class="col-md-12">
		<?php
		if ( $soNo=="" ) {	?>						
			<a class="btn btn-primary pull-right" name="btn_create" title="Create"><i class="fa fa-save"></i> Create SO</a>	
	<?php }else{ ?>
			<a class="btn btn-primary pull-right" name="btn_verify" id="btn_verify" title="Confirm">
				<i class="fa fa-check"></i> Confirm SO
			</a>	

			<a class="btn btn-danger pull-right" name="btn_delete" id="btn_delete" title="Delete"  style="margin-right: 5px;" >
				<?php switch($hdr['statusCode']) {
					case 'P' : case 'X' :
						break;
					default : 
						if ( $hdr['revCount']==0 ) {
							echo '';
						}else{
							echo ' disabled '; 
						}
				}//end switch ?>
				<i class="fa fa-trash"></i> Delete SO</a>	
			
	<?php } ?>
	</div>


    </div>
	<!--/.tab-content-->     
</div>
<!-- /.box-body -->

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
<div id="modal_search_product" class="modal fade" role="dialog">
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
				<label for="txt_search_word_product" class="control-label col-md-2">Product Code </label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_word_product" />
				</div>
			</div>
		
		<table id="tbl_search_product_main" class="table">
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
		
		<div id="div_search_product_result">
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
     
	function getRollLength(prodId){
		//Get Roll Length
		var params = {
			id: prodId //$('option:selected', this).val();
		}; 
		$.ajax({
		  url: "get_prod_roll_length_ajax.php",
		  type: "post",
		  data: params,
		datatype: 'json',
		  success: function(data){
				//alert(data);
				$('#rollLengthId').empty();
				$.each($.parseJSON(data), function(key,value){
					$('#rollLengthId').append('<option value="'+value.id+'" >'+value.name+'</option>' );		
				});		
		  }, //success
		  error:function(){
			  alert('error');
		  }   
		}); 
			
		$('#qty').focus().select();
	}
  
  
	
	
	//SEARCH Begin
	$('a[name="btnProd"]').click(function(){
		curName = $(this).prev().attr('name');
		curId = $(this).prev().prev().attr('name');
		if(!$('#'+curName).prop('disabled')){
			$('#modal_search_product').modal('show');
		}
	});	
	$('#txt_search_word_product').keyup(function(e){ 
		if(e.keyCode == 13)
		{	
			var params = {
				search_word: $('#txt_search_word_product').val()
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
								$('#tbl_search_product_main tbody').empty();
								return false; break;
							default : 
								$('#tbl_search_product_main tbody').empty();
								$.each($.parseJSON(data.data), function(key,value){
									$('#tbl_search_product_main tbody').append(
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
								$('#modal_search_product').modal('show');	
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
		
		$('#prodDesc').html($(this).closest('tr').find('td:eq(3)').text()+' / <span style="color: red;">'+$(this).closest('tr').find('td:eq(7)').text()+'(-'+$(this).closest('tr').find('td:eq(8)').text()+')</span>');	

		$('#lblUom').text($(this).closest('tr').find('td:eq(4)').text());	
		//$('#prodPrice').val($(this).closest('tr').find('td:eq(4)').text());	
		//$('#salesPrice').val($(this).closest('tr').find('td:eq(4)').text());	

		//Get Roll Length
		var prodId=$(this).closest("tr").find('td:eq(1)').text();
		
		getRollLength(prodId);
		
		$('#modal_search_product').modal('hide');
	});
	//Search End

	$('#prodCode').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#prodCode').val()
			};
			curName = $(this).attr('name');
			curId = $(this).prev().attr('name'); //alert(curName); alert(curId);

			if(params.search_word.length < 3){
				$('#modal_search_product').modal('show');
				$('#txt_search_word_product').val(params.search_word).focus().select();

				//alert('search word must more than 3 character.');
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
							case 0 : //alert('Data not found.');
								$('#tbl_search_person_main tbody').empty().fadeIn('slow');
								return false; break;
							case 1 :
								$.each($.parseJSON(data.data), function(key,value){
									$('input[name='+curName+']').val(value.prodCode);
									$('input[name='+curId+']').val(value.prodId);			
									getRollLength(value.prodId);
								});
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
								$('#modal_search_product').modal('show');	
								$('#tbl_search_person_main tbody').fadeIn('slow');
						}	
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});
	
	
	

	function getList(){		
		var params = {
			action: 'getItemList',
			soNo: "<?=$soNo;?>"
		}; //alert(params.soNo);
		/* Send the data using post and put the results in a div */
		  $.ajax({
			  url: "<?=$rootPage;?>_ajax.php",
			  type: "post",
			  data: params,
			datatype: 'json',
			  success: function(data){	//alert(data);
					//data=$.parseJSON(data);
					var sumInviteTotal=0;
					var sumCountTotal=0;
					var sumPendingTotal=0;
					//alert(data);
					//alert(data.join(' '));
					switch(data.rowCount){
						default : 	//alert('default');						
						//$('#tbl_items tbody').empty();
						$('#tbl_items tbody').fadeOut('slow').empty();
						$rowNo=1;
						$.each($.parseJSON(data.data), function(key,value){
							var tmpRemark=value.remark;
							if(value.rollLengthName != ""){
								tmpRemark=tmpRemark+' / RL:'+value.rollLengthName;
							}
							$('#tbl_items tbody').append(
								'<tr>'+
								'<td style="text-align: center;">'+$rowNo+'</td>'+
								'<td style="text-align: left;">'+value.prodName+'</td>'+
								'<td style="text-align: left;">'+value.prodCode+'</td>'+
								'<td style="text-align: right;">'+value.qty+' '+value.uomCode+'</td>'+
								'<td style="text-align: left;">'+tmpRemark+'</td>'+
								'<td style="text-align: left;">'+value.deliveryDate+'</td>'+
								'<td><a href="#" name="divShip" class="btn btn-default" data-ref-id="'+value.id+'" data-prod-id="'+value.prodId+'" ><i class="fa fa-cut"></i> Divided shipping</td>'+
								'<td><a href="#" name="btnItmDelete" class="btn btn-danger" data-id="'+value.id+'"  ><i class="fa fa-trush"></i> Delete</a></td>'+
								'</tr>');
							$rowNo+=1;
							//alert(value);
						});
						$('#tbl_items tbody').fadeIn('slow');

						$('#prodCode').focus().select();							
					}//.switch
			  }   
			}).error(function (response) {
				alert(response.responseText);
			}); 
	}
	
	getList();

	function formItemClear(){
		$('#form2')[0].reset();
		$('#refItmId').val('');
		getList();
		$('#prodCode').focus();
	}

	$('#tbl_items').on("click", "a[name=divShip]", function(e) {
		var params = {
			action: 'getItem',
			id: $(this).attr('data-ref-id'), //$('option:selected', this).val();
			prodId: $(this).attr('data-prod-id')
		}; 
		getRollLength(params.prodId);
		$.post("<?=$rootPage;?>_ajax.php", params )
		.done(function(data) { //alert(data);
			if (data.success){   
				var itm = $.parseJSON(data.data);
				$('#refItmId').val(itm.id);
				$('#prodId').val(itm.prodId);
				$('#prodCode').val(itm.prodCode);
				$('#qty').val(itm.qty);
				$('#uomCode').val(itm.uomCode); 
				$('#itemRemark').val(itm.remark);
				$('#rollLengthId').val(itm.rollLengthId);

				
				//$('#deliveryDate').datepicker('setDate', itm.deliveryDate);
				$('#deliveryDate').val(itm.deliveryDate);

				$('#qty').focus().select();
			} else {
				//alert('a');
				$.smkAlert({
					text: data.message,
					type: 'danger',
					//position:'top-center'
				});
			}
			//$("#visitDate").focus();
		});
	 });

	$('#tbl_items').on("click", "a[name=btnItmDelete]", function(e) {
		var params = {
			action: 'itemDelete',
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
					formItemClear();
				} else {
					alert(data.message);
				}
			}).error(function (response) {
				alert(response.responseText);
			}); 
		}});
		e.preventDefault();
	 });
	

	
	$('a[name=btn_create]').click (function(e) { //alert('big');
		if ($('#form1').smkValidate()){
			$.post("<?=$rootPage;?>_ajax.php", $("#form1").serialize() )
				.done(function(data) {
					if (data.success){   
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						//alert(data.message);
						window.location.href = "<?=$rootPage;?>_add.php?soNo="+data.soNo;
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
				})
				.error(function (response) {
				  alert(response.responseText);
				});  
			e.preventDefault();
		}//.smkValidate()
	});//.btn_click

	$('a[name=btnItmSubmit]').click (function(e) { //alert('big2');
		tmpProductName = $('#product').val();

		if ($('#form2').smkValidate()){
			$.post("<?=$rootPage;?>_ajax.php", $("#form2").serialize() )
				.done(function(data) {
					if (data.success){						
						//$('#form1').smkClear();
						formItemClear();
					} else {
						//alert('a');
						$.smkAlert({
							text: data.message,
							type: 'danger',
							//position:'top-center'
						});
					}
					//$("#visitDate").focus();
				})
				.error(function (response) {
				  alert(response.responseText);
				}); 
			e.preventDefault();
		}//.smkValidate()
	});//.btn_click

	
	$('a[name=btnItmClear]').click (function(e) { //alert('big2');
		formItemClear();
	});//.btn_click

	
	$("#custId").on("change",function(e) {		
		//alert($('option:selected', this).attr('data-addr1'));
		var params = {
			id: $(this).val() //$('option:selected', this).val();
		}; 
		$.ajax({
			  url: "get_customer_ajax.php",
			  type: "post",
			  data: params,
			datatype: 'json',
			  success: function(data){
					//alert(data);
					$('#custAddr').empty();
					$.each($.parseJSON(data), function(key,value){						
						$('#custAddr').text(value.addr1+value.addr2+value.addr3+value.zipcode);
						$('#payTypeCreditDays').val(value.creditDay);
						$('#remark').val(value.soRemark);
						$('#smId').val(value.smId);
					});
					//get shipto
					$.ajax({
					  url: "get_shipto_by_cust_ajax.php",
					  type: "post",
					  data: params,
					datatype: 'json',
					  success: function(data){
						//alert(data);
						$('#shipToId').empty();
						$irow=1;
						$.each($.parseJSON(data), function(key,value){
							$('#shipToId').append('<option value="'+value.id+'" data-creditDay="'+value.creditDay+'" >'+value.code+' : '+value.name+'</option>' );
							if($irow==1){
								$('#shipToAddr').text(value.addr1+value.addr2+value.addr3+value.zipcode);	
							}
							$irow++;
							//$('#payTypeCreditDays').val(value.creditDay);	
						});
								
					  }, //success
					  error:function(){
						  alert('error');
					  }   
					});
					//ajax shipto end.			
			  }, //success
			  error:function(){
				  alert('error');
			  }   
			}); 
		//$('#smId').val($('option:selected', this).attr('data-smId'));
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
					$('#shipToAddr').empty();
					$.each($.parseJSON(data), function(key,value){
						//alert(value.addr1+value.addr2+value.addr3+value.zipcode);
						$('#shipToAddr').html(value.addr1+value.addr2+value.addr3+value.zipcode);
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

	 $('#btn_verify').click (function(e) {				 
		var params = {			
		action: 'confirm',	
		soNo: $('#soNo').val()				
		};
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
					window.location.href = '<?=$rootPage;?>_view.php?soNo='+params.soNo;
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

	$('#btn_delete').click (function(e) {				 
		var params = {
		action: 'delete',					
		soNo: $('#soNo').val()				
		};
		//alert(params.hdrID);
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
		
		$([document.documentElement, document.body]).animate({
			scrollTop: $(".box-title").offset().top
		}, 1000);
	});
</script>


<!--Integers (non-negative)-->
<script>
  function numbersOnly(oToCheckField, oKeyEvent) {
    return oKeyEvent.charCode === 0 ||
        /\d/.test(String.fromCharCode(oKeyEvent.charCode));
  }
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
