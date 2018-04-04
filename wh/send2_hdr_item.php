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
<<<<<<< HEAD
	$sendDate=(isset($_GET['sendDate'])? $_GET['sendDate'] : '01/01/1900' );	
=======
	$sendDate=(isset($_GET['sendDate'])? $_GET['sendDate'] : '01/01/1900' );
	$sendDate=str_replace('/', '-', $sendDate);
	$sendDate=date('Y-m-d', strtotime($sendDate));
	
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a
	$fromCode=(isset($_GET['fromCode'])?$_GET['fromCode']:'');
	$toCode=(isset($_GET['toCode'])?$_GET['toCode']:'');
	
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
			, itm.prodCodeId, prd.code as prodCode
			FROM send_mssql hdr 
			INNER JOIN send_detail_mssql dtl ON dtl.sendId=hdr.sendId 
			INNER JOIN product_item itm ON itm.prodItemId=dtl.productItemId 
			LEFT JOIN product prd ON prd.id=itm.prodCodeId  
			WHERE 1=1 ";
			if($sendDate<>"") $sql.="AND hdr.issueDate=:sendDate ";
			if($fromCode<>"") $sql.="AND hdr.fromCode=:fromCode ";
			if($toCode<>"") $sql.="AND hdr.toCode=:toCode ";
			$stmt = $pdo->prepare($sql);
<<<<<<< HEAD
			if($sendDate<>"") $stmt->bindParam(':sendDate', date("Y-m-d",strtotime($sendDate)) );
=======
			if($sendDate<>"") $stmt->bindParam(':sendDate', $sendDate );
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a
			if($fromCode<>"") $stmt->bindParam(':fromCode', $fromCode);
			if($toCode<>"") $stmt->bindParam(':toCode', $toCode);
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
<<<<<<< HEAD
       					
=======
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a
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
						<div class="col-md-6">					
<<<<<<< HEAD
							<form id="form1" action="<?=$rootPage;?>.php" method="get" class="form" novalidate>
=======
							<form id="form1" action="#" method="get" class="form" novalidate>
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a
								<div class="form-inline">
								<div class="form-group">
									<label for="sendDate">Sending Date</label> 
									<input type="text" id="sendDate" name="sendDate" class="form-control datepicker" />
								</div>						
<<<<<<< HEAD
								<input type="submit" class="btn btn-default" value="ค้นหา">
								<a name="btnSubmit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Submit</a>
								<a name="btnSyncSubmit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-retweet"></i> Submit</a>
=======
								<a name="btnSubmit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Search</a>
								<a name="btnSyncSubmit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-retweet"></i> Sync & Search</a>
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a
								</div>
							</form>  
						</div>    
					</div>	
					
					<form id="form2" action="" method="post" class="form" novalidate>
						<input type="hidden" name="sdNo" value="<?=$sdNo;?>" />
						<input type="hidden" name="action" value="item_add" />		
					
					<div class="table-responsive">
					<table id="tbl_items" class="table table-striped">
						<tr>
<!--							<th><input type="checkbox" id="checkAll"  />Select All</th>-->
							<th><select id="selItmId" class="form-control"></select> No.</th>
							<th>Product Code</th>
							<th>Barcode</th>
							<th>Grade</th>
							<th>Qty</th>
							<th>Issue Date</th>							
<<<<<<< HEAD
							<th>Ref.No.</th>
						</tr>
						<?php $row_no=1; $prevNo=""; $rowColor='lightBlue'; while ($row = $stmt->fetch()) { 
=======
							<th>Ref.ID</th>
						</tr>
						<?php $row_no=1; $prevSendId=""; $rowColor='lightBlue'; $optItmHtml=""; while ($row = $stmt->fetch()) { 
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a
						$gradeName = '<b style="color: red;">N/A</b>'; 
						switch($row['grade']){
							case 0 : $gradeName = 'A'; break;
							case 1 : $gradeName = '<b style="color: red;">B</b>'; break;
							case 2 : $gradeName = '<b style="color: red;">N</b>'; break;
							default : 
						} 
<<<<<<< HEAD
						if($prevNo<>"" AND $prevNo<>$row['sdNo']){
							if($rowColor=="lightBlue"){$rowColor="lightGreen";}else{$rowColor="lightBlue";}
						}
						$prevNo=$row['sdNo'];
						?>
						<tr style="background-color: <?=$rowColor;?>;"  >
							<input type="hidden" name="refProdSdNo[]" value="<?=$row['sendId'];?>" />
							<td><input type="checkbox" name="itmId[]" value="<?=$row['productItemId'].','.$row['sendId'];?>"  /></td>
							<td><?= $row_no; ?></td>
=======
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
							<td><input type="checkbox" name="itmId[]" value="<?=$row['sendId'].','.$row['productItemId'];?>"  /><?= $row_no; ?></td>
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a
							<td><?= $row['prodCode']; ?></td>
							<td><?= $row['barcode']; ?></td>
							<td><?= $gradeName; ?></td>
							<td><?= $row['qty']; ?></td>
							<td><?= date('d M Y',strtotime( $row['gradeDate'] )); ?></td>			
<<<<<<< HEAD
							<td><?= $row['sdNo']; ?></td>				
						</tr>
						<?php $row_no+=1;
							
						} ?>
=======
							<td><?= $row['sendId']; ?></td>				
						</tr>
						<?php $row_no+=1;
						} 
							?>
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a
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
	$('#selItmId').append('<?=$optItmHtml;?>');
	
	$(document).on("change",'select[name="returnReasonCode[]"]',function() {
		$(this).closest('tr').find('input[name="returnReasonRemark[]"]')
			.val($(this).find(':selected').attr('data-name'))
			.select()
	});
			
	$('a[name=btnSubmit]').click(function(e){		
		var queryDate = $('#sendDate').val(); 
		window.location.href = "<?=$rootPage;?>_hdr_item.php?sdNo=<?=$sdNo;?>&sendDate="+queryDate+"&fromCode=<?=$fromCode;?>&toCode=<?=$toCode;?>";
	});
	$('a[name=btnSyncSubmit]').click(function(e){
		var queryDate = $('#sendDate').val(); 
		window.location.href = "<?=$rootPage;?>_hdr_item.php?sdNo=<?=$sdNo;?>&sendDate="+queryDate+"&fromCode=<?=$fromCode;?>&toCode=<?=$toCode;?>&isSync=1";
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
<<<<<<< HEAD
		<?php if(isset($_GET['sendDate'])){ ?>
		var queryDate = '<?=date("Y-m-d", strtotime($_GET['sendDate']));?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
=======
		<?php if(isset($sendDate)){ ?>
		var queryDate = '<?=$sendDate;?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1,dateParts[2]); 
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a
		$('#sendDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#sendDate').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
	});
</script>
