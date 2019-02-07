<?php
  //include '../db/database_sqlsrv.php';
  include_once '../db/db_sqlsrv.php';
  include 'inc_helper.php';  
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>

</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	

<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->	
    <section class="content-header">
		<?php
			
		?>
      <h1>
       Sending to Warehouse
        <small>Sending to Warehouse</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Sending to Warehouse</a></li>
        <li class="active">Sending to Warehouse</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">		
        <div class="box-header with-border">
        <h3 class="box-title">Product Item Sync</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="<?=basename($_SERVER['PHP_SELF']);?>" method="get" class="form-inline" novalidate>				
			<div class="col-md-12">	
				<label for="sendDate">Send Date</label>
				<input type="text" id="sendDate" name="sendDate" class="form-control datepicker" data-smk-msg="Require Order Date." required >
				
				<input type="submit" class="btn btn-primary" value="Submit" />
			</div>
			<!--col-md-->
				</form>
				<!--from1-->
			</div>
			<!--/.row-->
			<div class="row col-md-12">
			<?php	
				if(isset($_GET['sendDate'])){
						$sendDate = $_GET['sendDate'];
						//$fromCode = $_POST['fromCode'];
						//$toCode = $_POST['toCode'];
						//$prodId = $_POST['prodId'];
						//$sendDate = to_mysql_date($sendDate);
						$sendDate = str_replace('/', '-', $sendDate);
						$sendDate = date("Y-m-d",strtotime($sendDate));
						//echo date("Y-m-d",strtotime($sendDate));
						//echo $sendDate;
						
						
					//TRUNCATE temp 
						echo "Clear temp header...";
						$sql = "TRUNCATE TABLE send_mssql_tmp";			
						$stmt = $pdo->prepare($sql);
						if($stmt->execute()){
							echo "success!<br/>";
						}
						
						echo "Clear temp detail...";
						$sql = "TRUNCATE TABLE send_detail_mssql_tmp";			
						$stmt = $pdo->prepare($sql);
						if($stmt->execute()){
							echo "success!<br/>";
						}
						
						echo "Clear temp product item...";
						$sql = "TRUNCATE TABLE product_item_temp";			
						$stmt = $pdo->prepare($sql);
						if($stmt->execute()){
							echo "success!<br/>";
						}
						
						echo "Get Production data ";
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
							case 'whOff' :  case 'whSup' : case 'whMgr' :  
									$sql .= "AND left(itm.[ItemCode],1) IN ('0','1','7','8','9','E') ";
								break;
							case 'pdOff' :  case 'pdSup' :
									$sql .= "AND left(itm.[ItemCode],1) = '".$s_userDeptCode."' ";
								break;
							case 'pdMgr' : 
									$sql .= "AND left(itm.[ItemCode],1) IN ('3','4','5','6') ";
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
										
						
						$sql = "  SELECT DISTINCT  itm.[ProductItemID]
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
					  FROM [send_detail] dtl, [product_item] itm 
					  WHERE dtl.[ProductItemID]=itm.[ProductItemID]
					  AND dtl.[SendID] IN (  SELECT DISTINCT  hdr.[SendID] 
										  FROM [send] hdr, [send_detail] dtl, [product_item] itm
										  WHERE hdr.SendID=dtl.SendID 
										  AND dtl.[ProductItemID]=itm.[ProductItemID]
										  AND hdr.[IssueDate] = '$sendDate' )
						  ";
					  switch($s_userGroupCode){ 
						case 'whOff' :  case 'whSup' : case 'whMgr' :
								$sql .= "AND left(itm.[ItemCode],1) IN ('0','1','7','8','9') ";
							break;
						case 'pdOff' :  case 'pdSup' :
								$sql .= "AND left(itm.[ItemCode],1) = '".$s_userDeptCode."' ";
							break;
						case 'pdMgr' : 
								$sql .= "AND left(itm.[ItemCode],1) IN ('3','4','5','6') ";
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
							, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`, `gradeTypeId`, `remarkWh`) 
							VALUES
							(:ProductItemID,:ProductID,:ItemCode,:IssueDate,:MachineID,:SeqNo,:NW,:GW
							,:Length,null,:Grade,:IssueGrade,:RefItemID,:ItemStatus,:Remark,:ProblemID
							,1 ,''
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

							$msRowCount+=1;
						}
						//end while mssql
						}else{
							echo sqlsrv_error();
						}
						//if
						
						
						
						$sql = "  SELECT DISTINCT itm.[ProductItemID]
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
						case 'whOff' :  case 'whSup' :  case 'whMgr' : 
								$sql .= "AND left(itm.[ItemCode],1) IN ('0','1','7','8','9') ";
							break;
						case 'pdOff' :  case 'pdSup' :
								$sql .= "AND left(itm.[ItemCode],1) = '".$s_userDeptCode."' ";
							break;
						case 'pdMgr' : 
								$sql .= "AND left(itm.[ItemCode],1) IN ('3','4','5','6') ";
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
						



						//Update KG Product from NW to Qty
						$sql = "UPDATE product_item_temp tmp 
						SET tmp.qty=tmp.NW 
						WHERE tmp.qty=0 
						";			
						$stmt = $pdo->prepare($sql);
						$stmt->execute();
						//Update KG Product from NW to Qty

						//Update KG Product from NW to Qty
						$sql = "UPDATE product_item_temp tmp 
						SET tmp.qty=tmp.NW 
						WHERE tmp.qty=0 
						";			
						$stmt = $pdo->prepare($sql);
						$stmt->execute();
						//Update KG Product from NW to Qty

						//Update Qty=NW If UOM = KG. / KG
						$sql = "UPDATE product_item_temp SET qty=NW WHERE prodCodeId IN (SELECT id FROM `product` WHERE uomCode IN ('KG','KG.') )
						";			
						$stmt = $pdo->prepare($sql);
						$stmt->execute();
						//Update Qty=NW If UOM = KG. / KG

						//Update Qty=1 If UOM = ROLL
						$sql = "UPDATE product_item_temp SET qty=1 WHERE prodCodeId IN (SELECT id FROM `product` WHERE uomCode='ROLL')
						";			
						$stmt = $pdo->prepare($sql);
						$stmt->execute();
						//Update Qty=1 If UOM = ROLL

						//P7577M-TN-B-01650 : 2000524
						//Update Qty=NW If UOM = KG. / KG
						$sql = "UPDATE product_item_temp SET qty=GW WHERE prodCodeId = 2000524 
						";			
						$stmt = $pdo->prepare($sql);
						$stmt->execute();
						//Update Qty=NW If UOM = KG. / KG


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
						
						//Update un sent item.
						$sql = "UPDATE  product_item map
						INNER JOIN product_item_temp tmp ON tmp.prodItemId=map.prodItemId
							AND tmp.prodItemId NOT IN (SELECT x.prodItemId FROM send_detail x)  
						SET map.NW=tmp.NW 
						, map.GW=tmp.GW
						, map.qty=tmp.qty
						, map.grade=tmp.grade
						, map.gradeDate=tmp.gradeDate 
						";			
						$stmt = $pdo->prepare($sql);
						$stmt->execute();
						
						
						$sql = "UPDATE  product_item map
						INNER JOIN product_item_temp tmp ON tmp.prodItemId=map.prodItemId
						INNER JOIN send_detail sd ON sd.prodItemId=map.prodItemId
						INNER JOIN send sh ON sh.sdNo=sd.sdNo AND sh.statusCode NOT IN ('P','X') 
						SET map.NW=tmp.NW 
						, map.GW=tmp.GW
						, map.qty=tmp.qty
						, map.grade=tmp.grade
						, map.gradeDate=tmp.gradeDate 
						";			
						$stmt = $pdo->prepare($sql);
						$stmt->execute();	

							
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
						251	R&D 	*** Technic **
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
							WHEN 251 THEN 'T'
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
					
					
					

				}
				//end if isset fromDate and toDate 
			?>
			
				<?php if(isset($_GET['sendDate'])) echo 'Updated '.$msRowCount.' items'; ?>
			</div>
		</div>   
		<!--/.row body-->	
		<div class="box-footer">
		</div>
		<!--/.box-footer-->
	</div>
	<!--/.box-->
            
					
			
		

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

$("html,body").scrollTop(0);
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
		}); 
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




<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>