<?php include 'inc_helper.php'; ?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>


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
	
	
	if(isset($_GET['isSync']) AND isset($_GET['sendDate'])){
		//$sendDate = to_mysql_date($_GET['sendDate']);
		//$sendDate = '2017-11-01';
		
		//TRUNCATE temp 
		$sql = "TRUNCATE TABLE send_mssql_tmp";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		
		$sql = "TRUNCATE TABLE send_detail_mssql_tmp";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
			
		$sql = "TRUNCATE TABLE product_item_temp";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();

		$sql = "SELECT DISTINCT  hdr.[SendID], hdr.[SendNo], CONVERT(VARCHAR, hdr.[IssueDate], 121) as IssueDate
		  , left(itm.[ItemCode],1) as fromCode 
		  , [CustomerID]
		  FROM [send] hdr, [askn].[dbo].[send_detail] dtl, [product_item] itm
		  WHERE hdr.SendID=dtl.SendID 
		  AND dtl.[ProductItemID]=itm.[ProductItemID]
		  AND hdr.[isCustomer]='N' 
		  AND hdr.[IssueDate] = '$sendDate'
		  ";
		  switch($s_userGroupCode){ 
			case 'whOff' :  case 'whSup' : 
					$sql .= "AND left(itm.[ItemCode],1) IN (0,7,8) ";
				break;
			case 'pdOff' :  case 'pdSup' :
					$sql .= "AND left(itm.[ItemCode],1) = '".$s_userDeptCode."' ";
				break;
			default : //case 'it' : case 'admin' : 
		  }
		//echo $sql;
		$msResult = sqlsrv_query($ssConn, $sql);
		$msRowCount = 0;
		$c = 1;
		set_time_limit(0);
		if($msResult){
			while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {	
				//Insert Header mysql from mssql
				$sql = "INSERT INTO  `send_mssql_tmp` 
				(`sendId`, `issueDate`, `customerId`, `fromCode`) 
				VALUES
				(:SendID,:IssueDate,:CustomerID,:fromCode)
				";		
				
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':SendID', $msRow['SendID']);	
				$stmt->bindParam(':IssueDate', $msRow['IssueDate']);
				$stmt->bindParam(':CustomerID', $msRow['CustomerID']);	
				$stmt->bindParam(':fromCode', $msRow['fromCode']);			
				$stmt->execute();

				$msRowCount+=1;
			}
			//end while mssql
		}else{
			echo sqlsrv_error();
		}
		//if
		
		sqlsrv_free_stmt($msResult);
		
		
		
		$sql = "  SELECT itm.[ProductItemID]
		  ,itm.[ProductID]
		  ,itm.[ItemCode]
		  , CONVERT(VARCHAR, itm.[IssueDate], 121) as IssueDate
		  ,itm.[MachineID]
		  ,itm.[SeqNo]
		  ,itm.[NW]
		  ,itm.[GW]
		  ,itm.[Length]
		  ,itm.[Grade]
		  , CONVERT(VARCHAR, itm.[IssueGrade], 121) as IssueGrade
		  ,itm.[UserID]
		  ,itm.[RefItemID]
		  ,itm.[ItemStatus]
		  ,itm.[Remark]
		  ,itm.[RecordDate]
		  ,itm.[ProblemID]
		  ,dtl.[SendID], dtl.[Remark] 
	  FROM [send_detail] dtl, [product_item] itm 
	  WHERE dtl.[ProductItemID]=itm.[ProductItemID]
	  AND dtl.[SendID] IN (  SELECT DISTINCT  hdr.[SendID] 
						  FROM [send] hdr, [send_detail] dtl, [product_item] itm
						  WHERE hdr.SendID=dtl.SendID 
						  AND dtl.[ProductItemID]=itm.[ProductItemID]
						  AND hdr.[IssueDate] = '$sendDate' )
		  ";
	  switch($s_userGroupCode){ 
		case 'whOff' :  case 'whSup' : 
				//$sql .= "AND left(itm.[ItemCode],1) IN ('0','7','8','9') ";
			break;
		case 'pdOff' :  case 'pdSup' :
				$sql .= "AND left(itm.[ItemCode],1) = '".$s_userDeptCode."' ";
			break;
		default : //case 'it' : case 'admin' : 
	  }
		//echo $sql;
		$msResult = sqlsrv_query($ssConn, $sql);
		$msRowCount = 0;
		$c = 1;
		set_time_limit(0);
		if($msResult){
		while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {	
			//Insert mysql from mssql
			$sql = "INSERT INTO  `product_item_temp` 
			(`prodItemId`, `prodId`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`
			, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`) 
			VALUES
			(:ProductItemID,:ProductID,:ItemCode,:IssueDate,:MachineID,:SeqNo,:NW,:GW
			,:Length,null,:Grade,:IssueGrade,:RefItemID,:ItemStatus,:Remark,:ProblemID
			)
			";		
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':ProductItemID', $msRow['ProductItemID']);	
			$stmt->bindParam(':ProductID', $msRow['ProductID']);	
			$stmt->bindParam(':ItemCode', $msRow['ItemCode']);	
			$stmt->bindParam(':IssueDate', $msRow['IssueDate']);	
			$stmt->bindParam(':MachineID', $msRow['MachineID']);	
			$stmt->bindParam(':SeqNo', $msRow['SeqNo']);	
			$stmt->bindParam(':NW', $msRow['NW']);			
			$stmt->bindParam(':GW', $msRow['GW']);	
			
			$stmt->bindParam(':Length', $msRow['Length']);	
			$stmt->bindParam(':Grade', $msRow['Grade']);	
			$stmt->bindParam(':IssueGrade', $msRow['IssueGrade']);	
			$stmt->bindParam(':RefItemID', $msRow['RefItemID']);	
			$stmt->bindParam(':ItemStatus', $msRow['ItemStatus']);	
			$stmt->bindParam(':Remark', $msRow['Remark']);	
			$stmt->bindParam(':ProblemID', $msRow['ProblemID']);		
			
			$stmt->execute();
			
			$sql = "INSERT INTO  `send_detail_mssql_tmp` 
			(`productItemId`, `sendId`, `remark`) 
			VALUES
			(:ProductItemID, :SendID, :Remark)
			";		
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':ProductItemID', $msRow['ProductItemID']);	
			$stmt->bindParam(':SendID', $msRow['SendID']);	
			$stmt->bindParam(':Remark', $msRow['Remark']);	
			$stmt->execute();

			$msRowCount+=1;
		}
		//end while mssql
		}else{
			echo sqlsrv_error();
		}
		//if
		
		sqlsrv_free_stmt($msResult);
		//END MSSQL 
		
		
		
		
		
		
		//PRODUCT ITEM (INSERT ONLY) 
		//Update prodCodeId in product item.////////////////////////////////////////////
		$sql = "UPDATE product_item_temp tmp 
		INNER JOIN product_mapping map ON map.invProdId=tmp.prodId 
		SET tmp.prodCodeId=map.wmsProdId 
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();	
		//Update prodCodeId in product item.////////////////////////////////////////////
		
		
		//Delete production only not approve sending.
		/*$sql = "DELETE FROM product_item 
		WHERE prodItemId IN (SELECT tmp.prodItemId FROM product_item_temp tmp 
								INNER JOIN send_detail dtl ON dtl.prodItemId=tmp.prodItemId 
								INNER JOIN send hdr ON hdr.sdNo=dtl.sdNo AND hdr.statusCode<>'P')	
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();	*/
			
		//Insert prod with temp
		$sql = "INSERT INTO product_item
		SELECT * FROM product_item_temp 
		WHERE prodItemId NOT IN (SELECT prodItemId FROM product_item)	
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();	
		
		
		
		
		
		
		
		
		
		
		/*22	COATING(5)
		23	CUTTING(6)
		57	Inspection(7)
		181	Determinate 
		191	Trash
		209	Weaving(4)
		212	Twisting(2)
		213	Warping(3)
		221	C/O=>In
		222	Warehouse
		223	Scrap
		226	Extra stock
		236	Packing
		238	WH(Export)
		239	ERP
		240	160958 TW
		241	160958 WP
		242	160958 WV
		243	160958 CO
		244	160958 CT
		245	160958 In.
		251	R&D 
		252	ล้างสต็อก 2017*/
		//Begin Sync Sending data.
		$sql = "UPDATE send_mssql_tmp prod 
		SET prod.`toCode`= CASE customerId
			WHEN 22 THEN '5'
			WHEN 23 THEN '6'
			WHEN 57 THEN '8'
			WHEN 181 THEN 'U'
			WHEN 191 THEN 'U' 		
			WHEN 209 THEN '4'
			WHEN 212 THEN '2'
			WHEN 213 THEN '3'
			WHEN 221 THEN 'U'
			WHEN 222 THEN '8'
			WHEN 223 THEN 'U'
			WHEN 226 THEN '8'
			WHEN 236 THEN '8'
			WHEN 238 THEN 'E'
			WHEN 239 THEN 'U' 
			WHEN 240 THEN '2'
			WHEN 241 THEN '3'
			WHEN 242 THEN '4'
			WHEN 243 THEN '5'
			WHEN 244 THEN '6'
			WHEN 245 THEN '8'
			WHEN 251 THEN 'U'
			WHEN 252 THEN 'U'
			ELSE 'U' 
		END
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
			
		//Update prod with temp
		//$sql = "UPDATE send prod 
		//INNER JOIN send_production tmp ON tmp.SendNo=prod.refNo AND prod.statusCode<>'P' 
		//SET prod.`issueDate`=tmp.`issueDate`
		//, prod.`qty`=tmp.`qty`
		//, prod.`fromCode`=tmp.`fromCode`
		//, prod.`isCustomer`=tmp.`isCustomer`
		//, prod.`customerID`=tmp.`customerID`
		//";			
		//$stmt = $pdo->prepare($sql);
		//$stmt->execute();
		
		//Insert prod with temp
		/*$sql = "SELECT `sendID`, `sendNo`, `issueDate`, `qty`, `fromCode`, `toCode` 
		FROM send_production 
		WHERE SendID NOT IN (SELECT refNo FROM send) 
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();*/
				
		//Update productoin header.
		/*$sql = "INSERT INTO send_prod 
		(`sdNo`, `refNo`, `sendDate`, `fromCode`, `toCode`, `remark`, `statusCode`, `createTime`, `createById`)
		SELECT tmp.`sendNo`, tmp.`sendID`, tmp.`issueDate`, tmp.`fromCode`, tmp.`toCode`, tmp.`sendNo`, 'B', NOW(), :s_userId
		FROM send_production tmp
		WHERE NOT EXISTS (SELECT * FROM send_prod hdr WHERE hdr.sdNo=tmp.sdNo 
		";	
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':s_userId', $s_userId );	
		$stmt->execute();*/
		
		
		//Insert new productoin header.
		$sql = "INSERT INTO send_mssql
		(`sendId`, `issueDate`, `customerId`, `fromCode`, `toCode`, `createTime`, `createById`)
		SELECT tmp.`sendId`, tmp.`issueDate`, tmp.customerId, tmp.`fromCode`, tmp.`toCode`, NOW(), :s_userId
		FROM send_mssql_tmp tmp
		WHERE NOT EXISTS (SELECT * FROM send_mssql hdr WHERE hdr.sendId=tmp.sendId )
		";	
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':s_userId', $s_userId );	
		$stmt->execute();
		
		
		
		//Delete temp if Approved.
		/*$sql = "DELETE FROM send_production_detail 
		WHERE prodItemId IN (SELECT dtl.prodItemId FROM send hdr
							INNER JOIN send_detail dtl ON dtl.sdNo=hdr.sdNo 
							WHERE hdr.statusCode='P' )
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();*/
		
		//Insert productoin detail .
		$sql = "INSERT INTO send_detail_mssql 
		(`sendId`, `productItemId`, `remark`)
		SELECT dtl.sendId, dtl.`productItemId`, dtl.`remark`
		FROM send_detail_mssql_tmp dtl
		WHERE NOT EXISTS (SELECT x.* FROM send_detail_mssql x WHERE dtl.productItemId=x.productItemId AND dtl.sendId=x.sendId) 
		";					
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		
		/*header("Location: ".$rootPage.".php?sendDate=".$_GET['sendDate']);
		
		exit();*/
		
		$isSync=0;
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
		<?php						
			$sql = "SELECT hdr.sendId, dtl.`productItemId`, itm.`barcode`, itm.`issueDate`
			, itm.`machineId`, itm.`seqNo`, itm.`NW`, itm.`GW`, itm.`qty`, itm.`packQty`, itm.`grade`, itm.`gradeDate`
			, itm.`refItemId`, itm.`itemStatus`, itm.`remark`, itm.`problemId`					
			, itm.prodCodeId as prodId, prd.code as prodCode
			, (SELECT IFNULL(sHdr.sdNo,'') FROM send sHdr
											INNER JOIN send_detail sDtl ON sDtl.sdNo=sHdr.sdNo
											WHERE sHdr.statusCode IN ('C','P') AND sDtl.prodItemId=itm.prodItemId LIMIT 1) as sentNo 
			FROM send_mssql hdr  
			INNER JOIN send_detail_mssql dtl ON dtl.sendId=hdr.sendId 
			INNER JOIN product_item itm ON itm.prodItemId=dtl.productItemId 
			LEFT JOIN product prd ON prd.id=itm.prodCodeId  	
			WHERE 1=1 ";
			if($sendDate<>"") $sql.="AND hdr.issueDate=:sendDate ";
			if($fromCode<>"") $sql.="AND hdr.fromCode=:fromCode ";
			if($toCode<>"") $sql.="AND hdr.toCode=:toCode ";
			if($prodId<>"") $sql.="AND itm.prodCodeId=:prodId ";
			$stmt = $pdo->prepare($sql);
			if($sendDate<>"") $stmt->bindParam(':sendDate', $sendDate );
			if($fromCode<>"") $stmt->bindParam(':fromCode', $fromCode);
			if($toCode<>"") $stmt->bindParam(':toCode', $toCode);
			if($prodId<>"") $stmt->bindParam(':prodId', $prodId);
			$sql.="ORDER BY hdr.sendId  "; 
			$stmt->execute();
		?>
        <div class="box-header with-border">
			<div class="form-inline">
				<label class="box-title">Sending No. : <?=$sdNo;?></label>
				<a href="<?=$rootPage;?>_hdr.php?sdNo=" class="btn btn-primary"><i class="glyphicon glyphicon-arrow-left"></i> Back</a>
			</div>
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
			<div class="row" >
				<div class="box-header with-border">
				<h3 class="box-title">Item List From Production</h3>
				
				
				
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  
				  <span class="label label-primary">Total <?php echo $stmt->rowCount(); ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">					
							<form id="form1" action="#" method="get" class="form-inline" novalidate>
							
									<label for="sendDate">Sending Date</label> 
									<input type="text" id="sendDate" name="sendDate" class="form-control datepicker" />
								
									<label for="prodCode">Product Code</label> 
									<?php 
									?>
									<input type="hidden" name="prodId" id="prodId" class="form-control" value="<?=$prodId;?>"  />
									<input type="text" name="prodCode" id="prodCode" class="form-control" value="<?=$prodCode;?>"  />
									<a href="#" name="btnSdNo" class="btn btn-primary" ><i class="glyphicon glyphicon-search" ></i> Search Product</a>	
																
								<a name="btnSubmit" href="#" class="btn btn-danger"><i class="glyphicon glyphicon-search"></i> Search</a>
								<a name="btnSyncSubmit" href="#" class="btn btn-danger"><i class="glyphicon glyphicon-retweet"></i> Sync & Search</a>
							
							</form>  
						</div>    
					</div>	
					
					<form id="form2" action="" method="post" class="form" novalidate>
						<input type="hidden" name="sdNo" value="<?=$sdNo;?>" />
						<input type="hidden" name="action" value="item_add" />		
					
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<thead>
						<tr>
<!--							<th><input type="checkbox" id="checkAll"  />Select All</th>-->
							<th><select id="selItmId" class="form-control"></select> No. <input type="checkbox" id="chkPending" /> Pending</th>
							<th>Product Code</th>
							<th>Barcode</th>
							<th>Grade</th>
							<th>Qty</th>
							<th>Issue Date</th>							
							<th>Ref.ID</th>
						</tr>
						</thead>
						<tbody>
						<?php $row_no=1; $prevSendId=""; $rowColor='lightBlue'; $optItmHtml=""; while ($row = $stmt->fetch()) { 
						$gradeName = '<b style="color: red;">N/A</b>'; 
						switch($row['grade']){
							case 0 : $gradeName = 'A'; break;
							case 1 : $gradeName = '<b style="color: red;">B</b>'; break;
							case 2 : $gradeName = '<b style="color: red;">N</b>'; break;
							default : 
						} 
						if($prevSendId=="") {
							$optItmHtml='<option value="">Clear All</option>'
							.'<option value="0">Select All</option>'
							.'<option value="'.$row['sendId'].'" >'.$row['sendId'].'</option>';
						}
						if($prevSendId<>"" AND $prevSendId<>$row['sendId']){
							if($rowColor=="lightBlue"){$rowColor="lightGreen";}else{$rowColor="lightBlue";}
							$optItmHtml.='<option value="'.$row['sendId'].'">'.$row['sendId'].'</option>';
						}
						$prevSendId=$row['sendId'];
						?>
						<tr style="background-color: <?=$rowColor;?>;"  >
							<td>
							<?php if($row['sentNo']==''){ ?>
								<input type="checkbox" name="itmId[]" value="<?=$row['sendId'].','.$row['productItemId'];?>"  />
							<?php }else{ ?>
								<label class="label label-danger" ><?=$row['sentNo'];?></label>
							<?php } ?>							
							<?= $row_no; ?></td>
							<td><?= $row['prodCode']; ?></td>
							<td><?= $row['barcode']; ?></td>
							<td><?= $gradeName; ?></td>
							<td><?= $row['qty']; ?></td>
							<td><?= date('d M Y',strtotime( $row['gradeDate'] )); ?></td>			
							<td><?= $row['sendId']; ?></td>				
						</tr>
						<?php $row_no+=1;
						} 
						?>
						</tbody>
					</table>
					</div>
					<!--/.table-responsive-->
					<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>
					</form>
					
				</div>
				<!--/box-body-->
			</div>
			<!--/.row table-responsive-->
			
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
							case 1 :
								$.each($.parseJSON(data.data), function(key,value){
									$('#prodCode').val(value.sdNo).prop('disabled','disabled');
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
	});
	//Search End
  
  
  
  
	$('#selItmId').append('<?=$optItmHtml;?>');
	
	$('#chkPending').on('change', function() {
		if($(this).prop('checked')){
			$('#tbl_items > tbody  > tr').each(function() {
				//alert($(this).find('td:eq(1) input[name=itmId]'));
				var $tmp = $(this).find('td:eq(0)').find('input[name=itmId]');
				//alert($tmp);
				if($tmp){					
					$(this).fadeOut('slow');
				} 
			});
		}else{
			$('#tbl_items > tbody  > tr').each(function() {
				$(this).fadeIn('slow');
			});
		}
		
	});
	
	
			
	$('a[name=btnSubmit]').click(function(e){		
		var queryDate = $('#sendDate').val(); 	
		queryDate = queryDate.replace(/\//g, '%2F');
		var prodId="";
		if($('#prodCode').val()!=""){ prodId=$('#prodId').val(); }			
		window.location.href = "<?=$rootPage;?>_hdr_item.php?sdNo=<?=$sdNo;?>&sendDate="+queryDate+"&prodId="+prodId+"&fromCode=<?=$fromCode;?>&toCode=<?=$toCode;?>";
	});
	$('a[name=btnSyncSubmit]').click(function(e){
		var queryDate = $('#sendDate').val(); 	
		queryDate = queryDate.replace(/\//g, '%2F');  
		var prodId="";
		if($('#prodCode').val()!=""){ prodId=$('#prodId').val(); }	
		window.location.href = "<?=$rootPage;?>_hdr_item.php?sdNo=<?=$sdNo;?>&sendDate="+queryDate+"&prodId="+prodId+"&fromCode=<?=$fromCode;?>&toCode=<?=$toCode;?>&isSync=1";
	});
	$('#form2 a[name=btn_submit]').click (function(e) {
		if ($('#form2').smkValidate()){
			$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: '<?=$rootPage;?>_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
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
	});
</script>
