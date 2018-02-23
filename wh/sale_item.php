<?php
  //  include '../db/database.php';
	include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>
    
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
   <?php $soNo = $_GET['soNo']; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Sales Order Information
        <small>Sales Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="sale.php"><i class="fa fa-list"></i>Sales List</a></li>
		<li><a href="sale_item.php?soNo=<?=$soNo;?>"><i class="fa fa-edit"></i>SO No.<?=$soNo;?></a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<?php			
			$sql = "
					SELECT a.`soNo`, a.`poNo`, a.`saleDate`, a.`custCode`, a.`smCode`, a.`total`, a.`vatAmount`, a.`netTotal`, a.`prodGFC`, a.`prodGFM`, a.`prodGFT`, a.`prodSC`, a.`prodCFC`, a.`prodEGWM`, a.`prodGT`, a.`prodCSM`, a.`prodWR`, a.`deliveryDate`, a.`deliveryRem`, a.`suppTypeFact`, a.`suppTypeImp`, a.`prodTypeOld`, a.`prodTypeNew`, a.`custTypeOld`, a.`custTypeNew`, a.`prodStkInStk`, a.`prodStkOrder`, a.`prodStkOther`, a.`prodStkRem`, a.`packTypeAk`, a.`packTypeNone`, a.`packTypeOther`, a.`packTypeRem`, a.`priceOnOrder`, a.`priceOnOther`, a.`priceOnRem`, a.`remark`, a.`plac2deliCode`, a.`plac2deliRem`, a.`payTypeCode`, a.`payTypeRem`, a.`statusCode`, a.`createTime`, a.`createByID`, a.`updateTime`, a.`updateById`
					, b.custName, b.custAddr, b.custTel, b.custFax
					, c.name as smName, c.surname as smSurname
					, d.userFullname as createByName
					, a.confirmTime, cu.userFullname as confirmByName
					, a.approveTime, au.userFullname as approveByName
					FROM `sale_header` a
					left join customer b on a.custCode=b.code
					left join salesman c on a.smCode=c.code
					left join user d on a.createByID=d.userID
					left join user cu on a.confirmByID=cu.userID
					left join user au on a.approveByID=au.userID
					WHERE 1
					AND a.soNo=:soNo 					
					ORDER BY a.createTime DESC
			";
			$stmt = $pdo->prepare($sql);			
			$stmt->bindParam(':soNo', $soNo);	
			$stmt->execute();
			$hdr = $stmt->fetch();
	   ?> 
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">Sales Order No : <b><?= $hdr['soNo']; ?></b></h3>
			<div class="box-tools pull-right">
				<?php $statusName = '<b style="color: red;">Unknown</b>'; switch($hdr['statusCode']){
					case 'A' : $statusName = '<b style="color: red;">Incompleate</b>'; break;
					case 'B' : $statusName = '<b style="color: blue;">Begin</b>'; break;
					case 'C' : $statusName = '<b style="color: blue;">Confirmed</b>'; break;
					case 'P' : $statusName = '<b style="color: green;">Approved</b>'; break;
					default : 
				} ?>
				<h3 class="box-title" id="statusName">Status : <?= $statusName; ?></h3>
			</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<input type="hidden" id="hdrID" value="<?= $id; ?>" />
			<div class="row">				
					<div class="col-md-3">
						Salesman : <br/>
						<b><?= $hdr['smName'].'&nbsp;&nbsp;'.$hdr['smSurname']; ?></b>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Customer : <br/>
						<b><?= $hdr['custName']; ?></b><br/>
						<?= $hdr['custAddr']; ?>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Po No : <b><?= $hdr['poNo']; ?></b><br/>
						sales Date : <b><?= $hdr['saleDate']; ?></b><br/>
						Delivery Date : <b><?= $hdr['deliveryDate']; ?></b><br/>
					</div>	<!-- /.col-md-3-->	
					<div class="col-md-3">
						<i class="fa fa-<?php echo ($hdr['suppTypeFact']==0?'square-o':'check-square-o'); ?>"></i> Factory&nbsp;&nbsp;&nbsp;    <i class="fa fa-<?php echo ($hdr['suppTypeImp']==0?'square-o':'check-square-o'); ?>"></i> Import</br>
						<i class="fa fa-<?php echo ($hdr['prodTypeOld']==0?'square-o':'check-square-o'); ?>"></i> Old Product&nbsp;&nbsp;&nbsp; <i class="fa fa-<?php echo ($hdr['prodTypeNew']==0?'square-o':'check-square-o'); ?>"></i> New Product</br>
						<i class="fa fa-<?php echo ($hdr['custTypeOld']==0?'square-o':'check-square-o'); ?>"></i> Old Customer&nbsp;&nbsp;&nbsp; <i class="fa fa-<?php echo ($hdr['custTypeNew']==0?'square-o':'check-square-o'); ?>"></i> New Customer</br>						
					</div>	<!-- /.col-md-3-->	
			</div> <!-- row add items -->
				
		<div class="row"><!-- row add items -->
				<!--<a href="sale_item_add.php" class="btn btn-google">Add sale Orders</a>-->
				<div class="box-header with-border">
				<h3 class="box-title">Add Item</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->				  
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<form id="form1" action="sale_item_insert.php" method="post" class="form" novalidate>
					<input type="hidden" id="soNo" value="<?= $hdr['soNo']; ?>" />
						<div class="row" style="padding-bottom: 3px;">						
							<div class="col-md-2"><div class="pull-right">
							<label for="deliveryDate">Item Delivery Date : </label>
							</div></div>		
							<div class="col-md-2">
							<input id="deliveryDate" type="text" class="form-control datepicker" name="deliveryDate" data-smk-msg="Require Order Date." required>
							</div><!-- /.col-md-10-->	
						</div><!--row-->
						
						<div class="row" style="padding-bottom: 3px;">
						<div class="col-md-2">
						<div class="pull-right">
						<label for="prodCode">Product Name : </label></div>
						</div>
						<div class="col-md-4">
						<select id="prodCode" name="prodCode" class="form-control" >
							<option value=""> -- Select -- </option>
							<?php
							$sql = "SELECT `code`, `prodGroup`, `prodName`, `prodNameNew`, `prodDesc`,  `salesUom`, `prodPrice`, `appID` FROM `product` WHERE 1";							
							$stmt = $pdo->prepare($sql);		
							$stmt->execute();
							while($row = $stmt->fetch()){
								echo '<option value="'.$row['code'].'" 
									 data-prodDesc="'.$row['prodDesc'].'" 									 
									 data-uom="'.$row['salesUom'].'"
									 data-prodPrice="'.$row['prodPrice'].'" 
									 data-appID="'.$row['appID'].'" 	
									 >'.$row['prodName'].' : ['.$row['prodNameNew'].']</option>';
							}
							?>
						</select>
						</div><!-- /.col-md-3-->
						<div class="col-md-2">
						</div>
						<div class="col-md-2"><div class="pull-right">
							<label>Std. Price : </label>
						</div></div><!-- /.col-md-2-->								
						<div class="col-md-2">
							<input id="prodPrice" type="text" class="form-control" name="prodPrice" style="text-align: right;" disabled data-smk-msg="Require Quantity."required>
						</div><!-- /.col-md-4-->	
						
						</div>
						<div class="row" style="padding-bottom: 3px;">						
							<div class="col-md-2"><div class="pull-right">
							<label>Product Desc : </label>
							</div></div>		
							<div class="col-md-10">
							<label id="prodDesc"></label>
							</div><!-- /.col-md-10-->	
						</div><!--row-->
						
						<div class="row" style="padding-bottom: 3px;">						
							<div class="col-md-2"><div class="pull-right">
								<label>Qty : </label>
							</div>							
							</div><!-- /.col-md-2-->								
							<div class="col-md-1">
								<input id="qty" type="text" class="form-control" name="qty" value="0"  style="text-align: right;" data-smk-msg="Require Quantity."required
								onkeypress="return numbersOnly(this, event);" 
								onpaste="return false;"
									>
							</div><!-- /.col-md-1-->	
							<div class="col-md-1"><label id="lblUom">UOM</label></div>
							
							<div class="col-md-2"><div class="pull-right">
								<label>Sales Price : </label>
							</div></div><!-- /.col-md-2-->								
							<div class="col-md-2">
								<input id="salesPrice" type="text" class="form-control" name="salesPrice" style="text-align: right;" data-smk-msg="Require Quantity."required>
							</div><!-- /.col-md-2-->	
							
							<div class="col-md-2">
								<div class="pull-right">
									<label>Total : </label>
								</div>
							</div><!-- /.col-md-2-->								
							<div class="col-md-2">
								<input id="total" type="text" class="form-control" name="total" value="0.00"  style="text-align: right;" disabled data-smk-msg="Require Quantity." required>
							</div><!-- /.col-md-2-->	
						</div><!--row-->
						
						<div class="row" style="padding-bottom: 3px; display:none;" >	
							<div class="col-md-2"><div class="pull-right">
							<label>disc. amount : </label>
							</div></div><!-- /.col-md-2-->								
							<div class="col-md-2">
								<input id="discAmount" type="text" class="form-control" name="discAmount" value="0.00"  style="text-align: right;" data-smk-msg="Require Quantity."required
								onkeypress="return decimalOnly(this, event);" 
								onpaste="return false;"
								>
							</div><!-- /.col-md-2-->
							
							<div class="col-md-2"><div class="pull-right">
							<label>disc. (%) : </label>
							</div></div><!-- /.col-md-2-->								
							<div class="col-md-1">
							<input id="discPercent" type="text" class="form-control" name="discPercent" value="0"  style="text-align: right;" data-smk-msg="Require Quantity."required
							onkeypress="return decimalOnly(this, event);" 
							onpaste="return false;"
							>
							</div><!-- /.col-md-1-->									
							<div class="col-md-1">
								<input id="discPerAmount" type="text" class="form-control" name="discPerAmount"  value="0.00"  style="text-align: right;" >
							</div>
							<div class="col-md-2"><div class="pull-right">
							<label>Net Total : </label>
							</div></div><!-- /.col-md-2-->								
							<div class="col-md-2">
							<input id="netTotal" type="text" class="form-control" name="netTotal"  value="0.00"  style="text-align: right;" disabled data-smk-msg="Require Quantity."required>
							</div><!-- /.col-md-2-->	
						</div><!--row-->						
					</form>
					<?php if($hdr['statusCode']=='A' OR $hdr['statusCode']=='B') { ?>
					<button id="btn_submit" type="button" class="btn btn-warning pull-right" style="display: none;">
						<i class="fa fa-save" ></i> Submit
					  </button>      
					  <button id="btn_calc" type="button" class="btn btn-primary pull-right" style="margin-right: 5px;">
						<i class="fa fa-calculator"></i> Calculate
					  </button>
					<?php } ?>
					<!--if status code in 'a','b'-->
				</div><!--/.box-->
            </div><!-- /.row add items -->
			
			
			
			
			
			
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <?php
						$sql = "SELECT COUNT(*) AS rowCount FROM sale_detail
						WHERE soNo=:soNo 
						";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':soNo', $hdr['soNo']);
						$stmt->execute();	
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
				  ?>
				  <span class="label label-primary">Total <?=$row['rowCount']; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
						$sql = "
								SELECT a.`id`, a.`prodCode`, a.`deliveryDate`, a.`salesPrice`, a.`qty`, a.`total`, 
								a.`discPercent`, a.`discAmount`, a.`netTotal`, a.`soNo`,
								b.prodName
								FROM `sale_detail` a
								LEFT JOIN product b on a.prodCode=b.code
								WHERE 1
								AND a.`soNo`=:soNo 
								ORDER BY a.createTime ASC
						";
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':soNo', $hdr['soNo']);
						$stmt->execute();	
				   ?> 	
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Delivery Date</th>
							<th>Product Name</th>							
							<th>Qty</th>
							<th>Sales Price</th>
							<th>Total</th>
							<th>disc. (%)</th>
							<th>disc.(amount)</th>
							<th>Net Total</th>
							<?php if($hdr['statusCode']=='A' OR $hdr['statusCode']=='B') { ?>
							<th>#</th>
							<?php } ?>							
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) { ?>
						<tr>
							<td><?= $row_no; ?></td>
							<td><?= to_thai_date_fdt($row['deliveryDate']); ?></td>
							<td><?= $row['prodName']; ?></td>
							<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
							<td style="text-align: right;"><?= number_format($row['salesPrice'],2,'.',','); ?></td>														
							<td style="text-align: right;"><?= number_format($row['total'],2,'.',','); ?></td>
							<td style="text-align: right;"><?= $row['discPercent']; ?></td>
							<td style="text-align: right;"><?= number_format($row['discAmount'],2,'.',','); ?></td>
							<td style="text-align: right;"><?= number_format($row['netTotal'],2,'.',','); ?></td>
							<?php if($hdr['statusCode']=='A' OR $hdr['statusCode']=='B') { ?>
							<td><a class="btn btn-danger" name="btn_row_delete" data-id="<?= $row['id']; ?>" ><i class="fa fa-trash"></i> Delete</a></td>
							<?php } ?>
						</tr>
						<?php $row_no+=1; } ?>
					
					<?php
						$sql = "
								SELECT sum(a.`netTotal`) as netTotal
								FROM `sale_detail` a
								WHERE 1
								AND a.`soNo`=:soNo 
						";
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':soNo', $hdr['soNo']);
						$stmt->execute();	
						$row = $stmt->fetch();
				   ?> 
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><b>Total</b></td>
							<td style="text-align: right;"><input type="hidden" id="hdrTotal" value="<?= $row['netTotal']; ?>" />
								<b><?= number_format($row['netTotal'],2,'.',','); ?><b>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><b>Vat 7%</b></td>
							<td style="text-align: right;"><input type="hidden" id="hdrVatAmount" value="<?= $row['netTotal']*0.07; ?>" />
								<b><?= number_format($row['netTotal']*0.07,2,'.',','); ?><b>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><b>Net Total</b></td>
							<td style="text-align: right;"><input type="hidden" id="hdrNetTotal" value="<?= $row['netTotal'] + ($row['netTotal']*0.07); ?>" />
								<b><?= number_format($row['netTotal'] + ($row['netTotal']*0.07),2,'.',','); ?><b>
							</td>
						</tr>
						
					</table>
					</div>
					<!--/.table-responsive-->
				</div><!-- /.box-body -->
	</div>
	<!-- /.row add items -->
	
	<div class="row">
		<div class="col-md-2">
			Product :
		</div>
		<div class="col-md-10">
			<i class="fa fa-<?php echo ($hdr['prodGFC']==0?'square-o':'check-square-o'); ?>"></i> Glass Fiber Cloth&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodGFM']==0?'square-o':'check-square-o'); ?>"></i> Glass Fiber Mesh&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodGFT']==0?'square-o':'check-square-o'); ?>"></i> Glass Fiber Tape&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodSC']==0?'square-o':'check-square-o'); ?>"></i> Silica Cloth&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodCFC']==0?'square-o':'check-square-o'); ?>"></i> Cabon Fiber Cloth&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</br>
			<i class="fa fa-<?php echo ($hdr['prodEGWM']==0?'square-o':'check-square-o'); ?>"></i> E-Glass Wool Mat&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodGT']==0?'square-o':'check-square-o'); ?>"></i> Glass Tissue&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodCSM']==0?'square-o':'check-square-o'); ?>"></i> Chopped Strand Mat&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodWR']==0?'square-o':'check-square-o'); ?>"></i> Woven Roving
		</div>
		
		<div class="col-md-2">
			Stock Status :
		</div>
		<div class="col-md-10">
			<i class="fa fa-<?php echo ($hdr['prodStkInStk']==0?'square-o':'check-square-o'); ?>"></i> In Stock&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['prodStkOrder']==0?'square-o':'check-square-o'); ?>"></i> Order&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['prodStkOther']==0?'square-o':'check-square-o'); ?>"></i> Other 
			<label class="label label-primary"><?php echo $hdr['prodStkRem']; ?></label>
		</div>
		<div class="col-md-2">
			Packing :
		</div>
		<div class="col-md-10">
			<i class="fa fa-<?php echo ($hdr['packTypeAk']==0?'square-o':'check-square-o'); ?>"></i> AK Logo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['packTypeNone']==0?'square-o':'check-square-o'); ?>"></i> None AK Logo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['packTypeOther']==0?'square-o':'check-square-o'); ?>"></i> Other
			<label class="label label-primary"><?php echo $hdr['packTypeRem']; ?></label>
		</div>
		<div class="col-md-2">
			Delivery / Load Date :
		</div>
		<div class="col-md-10">
			<label class="label label-primary"><?php echo to_thai_date_fdt($hdr['deliveryDate']); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Delivery Remark / Shipping Remark :
			<label class="label label-primary"><?php echo $hdr['deliveryRem']; ?></label>
		</div>
		<div class="col-md-2">
			Pricing on :
		</div>
		<div class="col-md-10">
			<i class="fa fa-<?php echo ($hdr['priceOnOrder']==0?'square-o':'check-square-o'); ?>"></i> Sales Order&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['priceOnOther']==0?'square-o':'check-square-o'); ?>"></i> Other
			<label class="label label-primary"><?php echo $hdr['priceOnRem']; ?></label>
		</div>
		<div class="col-md-2">
			Remark :
		</div>
		<div class="col-md-10">
			<label class="label label-primary"><?php echo $hdr['remark']; ?></label>
		</div>		
	</div>
	<!-- /.row -->
	
	<div class="row">
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-4">
					Credit :					
				</div>
				<div class="col-md-8">					
					<label class="label label-primary"><?php echo $hdr['payTypeRem']; ?></label></br>
					<i class="fa fa-<?php echo ($hdr['payTypeCode']=='CASH'?'check-circle-o':'circle-o'); ?>"></i> Cash</br>
					<i class="fa fa-<?php echo ($hdr['payTypeCode']=='CHEQ'?'check-circle-o':'circle-o'); ?>"></i> Cheque</br>
					<i class="fa fa-<?php echo ($hdr['payTypeCode']=='TRAN'?'check-circle-o':'circle-o'); ?>"></i> Transfer					
				</div>
			</div>			
		</div>
		<div class="col-md-4">
			Place to Delivery :
			<div class="row">
				
				<div class="col-md-2">
				</div>
				<div class="col-md-10">					
					<i class="fa fa-<?php echo ($hdr['plac2deliCode']=='FACT'?'check-circle-o':'circle-o'); ?>"></i> AK Factory</br>
					<i class="fa fa-<?php echo ($hdr['plac2deliCode']=='SEND'?'check-circle-o':'circle-o'); ?>"></i> Factory Sent to</br>
					<i class="fa fa-<?php echo ($hdr['plac2deliCode']=='MAP_'?'check-circle-o':'circle-o'); ?>"></i> Map</br>
					<i class="fa fa-<?php echo ($hdr['plac2deliCode']=='LOGI'?'check-circle-o':'circle-o'); ?>"></i> Logistic</br>
					<label class="label label-primary"><?php echo $hdr['plac2deliRem']; ?></label>
				</div>
			</div>			
		</div>
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-4">
					Create By : </br>
					Create Time : </br>
					Confirm By : </br>
					Confirm Time : </br>
					Approve By : </br>
					Approve Time : 		
				</div>
				<div class="col-md-8">
					<label class=""><?php echo $hdr['createByName']; ?></label></br>
					<label class=""><?php echo to_thai_datetime_fdt($hdr['createTime']); ?></label></br>
					<label class=""><?php echo $hdr['confirmByName']; ?></label></br>
					<label class=""><?php echo to_thai_datetime_fdt($hdr['confirmTime']); ?></label></br>
					<label class=""><?php echo $hdr['approveByName']; ?></label></br>
					<label class=""><?php echo to_thai_datetime_fdt($hdr['approveTime']); ?></label>	
				</div>				
			</div>			
		</div>
	</div>
	<!-- /.row -->
				
			
          
    
    </div><!-- /.box-body -->
  <div class="box-footer">
      <div class="col-md-12">
			<!--Left -->
			
			<!--Right-->		  
		  <a class="btn btn-success pull-right" name="btn_row_search" 
				href="sale_view.php?soNo=<?=$hdr['soNo'];?>" target="_blank" 
				data-toggle="tooltip" title="Preview"><i class="glyphicon glyphicon-search"></i> Preview</a>				
	</div><!-- /.col-md-12 -->
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
  

		$(document).on("keyup", ".ctc", function () 
		 {
			alert($(this).val());
			var tmpFloat = parseFloat($(this).val().replace(',', ''));
			alert(tmpFloat);
			
			var RE = /^(\d{0,2})(\.\d{2})$/;
			alert(tmpFloat);
			return (RE.test($(this).val()));
		});
		


       $(document).ready(function() {
    //       alert("jquery ok");
            $("#custName").focus();
            
  // Append and Hide spinner.          
            var spinner = new Spinner().spin();
            $("#spin").append(spinner.el);
            $("#spin").hide();
  //           
             $('#btn_submit').click (function(e) {
				 var params = {					
					soNo: $('#soNo').val(),
					deliveryDate: $('#deliveryDate').val(),
					prodCode: $('#prodCode').val(),
					prodPrice: $('#prodPrice').val().replace(/,/g, ''),
					salesPrice: $('#salesPrice').val().replace(/,/g, ''),
					qty: $('#qty').val().replace(/,/g, ''),
					total: $('#total').val().replace(/,/g, ''),
					discPercent: $('#discPercent').val(),
					discAmount: $('#discAmount').val().replace(/,/g, ''),
					netTotal: $('#netTotal').val().replace(/,/g, '')					
				};
				//alert(params.netTotal);
                if (params.netTotal==0 || params.netTotal==0.00 ){
					$.smkAlert({
					text: 'Net Total Incorrect.',
					type: 'warning',
					position:'top-center'
					});	
				}else{
                  $.post("sale_item_insert.php", params )
                              .done(function(data) {
									   if (data.status){  
										   $.smkAlert({
											text: data.message,
											type: 'success',
											position:'top-center'
											});
											location.reload();
									 } else {
											$.smkAlert({
											text: data.message,
											type: 'danger',
		   //                                 position:'top-center'
											});
									 };
								  });  
								  
				   e.preventDefault();
			  }//.if end
               });//.btn_click end
			   
            $('a[name=btn_row_delete]').click(function(){
				var params = {
					id: $(this).attr('data-id')
				};
				//alert(params.id);
				$.smkConfirm({text:'Are you sure to Delete ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
					$.post({
						url: 'sale_item_delete_ajax.php',
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
            
			$("#prodCode").on("change",function(e) {
				//$('#prodDesc').val($('option:selected', this).attr('data-prodDesc'));
				$("#prodDesc").text($('option:selected', this).attr('data-prodDesc'));
				$("#lblUom").text($('option:selected', this).attr('data-uom'));
				$('#prodPrice').val($('option:selected', this).attr('data-prodPrice'));
				$('#salesPrice').val($('option:selected', this).attr('data-prodPrice'));
				$('#qty').focus().select();
                e.preventDefault();
             });
			 $('#btn_calc').click (function(e) {
				 salesPrice = $('#salesPrice').val().replace(/,/g, '');
				 qty = $('#qty').val().replace(/,/g, '');
				 
				 //calc
				 total = salesPrice * qty;				 				 
				 netTotal = total;
				 				 
				 $('#total').val(total.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 //$('#discAmount').val(discAmount.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 //$('#discPerAmount').val(discPerAmount.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 //$('#salesPrice').val(salesPerUnit.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 $('#netTotal').val(netTotal.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 $('#btn_submit').css('display','block');
				 //$('#btn_calc').css('display','none');
                e.preventDefault();
             });
			 
			 $('#btn_calc_disc').click (function(e) {
				 salesPrice = $('#salesPrice').val().replace(/,/g, '');
				 qty = $('#qty').val().replace(/,/g, '');
				 discAmount = $('#discAmount').val().replace(/,/g, '');
				 discPercent = $('#discPercent').val().replace(/,/g, '');
				 discPerAmount = 0.00;
				 salesPerUnit = 0.00;
				 netTotal = 0.00;
				 //calc
				 total = salesPrice * qty;
				 tmp = total;
				 tmp = tmp-discAmount;
				 discPerAmount = tmp * discPercent / 100;
				 tmp = tmp-discPerAmount;
				 salesPerUnit = tmp/qty;
				 				 
				 netTotal = qty * salesPerUnit;
				 alert('discAmout :'+discAmount);
				 alert('discPercent :'+discPercent);
				 alert('discPerAmount :'+discPerAmount);
				 alert('tmp :'+tmp);
				 alert('salesPerUnit :'+salesPerUnit);
				 
				 $('#total').val(total.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 $('#discAmount').val(discAmount.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 $('#discPerAmount').val(discPerAmount.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 $('#salesPrice').val(salesPerUnit.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 $('#netTotal').val(netTotal.toFixed(2).toString().replace(/\B(?=(?=\d*\.)(\d{3})+(?!\d))/g, ',') );
				 $('#btn_submit').css('display','block');
				 //$('#btn_calc').css('display','none');
                e.preventDefault();
             });
			 			 						
			$('html, body').animate({ scrollTop: 0 }, 'fast');
			$("#statusName").fadeOut('slow').fadeIn('slow').fadeOut('slow').fadeIn('slow');
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
			language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: true              //Set เป็นปี พ.ศ.
		}); 
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?=$hdr['deliveryDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('.datepicker').datepicker('setDate', realDate);
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
	});
</script>





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
