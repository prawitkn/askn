<?php
  include '../db/database_sqlsrv.php';
  include 'inc_helper.php';  
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>

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
		<?php	
			if(isset($_GET['searchFromDate'])){
				//TRUNCATE temp 
				$sql = "TRUNCATE TABLE send_production_temp
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				//TRUNCATE new 
				$sql = "TRUNCATE TABLE send_production_new
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				$sql = "SELECT TOP 100 [SendID]
			  ,[SendNo]
			  ,[SendNum]
			  ,[IssueDate]
			  ,[Quantity]
			  ,[IsCustomer]
			  ,[CustomerID] FROM send  
				  ";
				//echo $sql;
				$msResult2 = sqlsrv_query($ssConn, $sql);
				$msRowCount = 0;
				
				set_time_limit(0);
				if($msResult2){
				while ($msRow = sqlsrv_fetch_array($msResult2, SQLSRV_FETCH_ASSOC))  {	
					//Insert mysql from mssql
					$sql = "INSERT INTO  `send_production_temp` 
					(`sendID`, `sendNo`, `issueDate`, `qty`, `isCustomer`, `customerID`) 
					VALUES
					(:sendID,:sendNo,null,:qty,:isCustomer,:customerID)
					";		
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':sendID', $msRow['sendID']);	
					$stmt->bindParam(':sendNo', $msRow['sendNo']);		
					//$stmt->bindParam(':issueDate', '2017-12-19');	
					$stmt->bindParam(':qty', $msRow['qty']);	
					$stmt->bindParam(':isCustomer', $msRow['isCustomer']);	
					$stmt->bindParam(':customerID', $msRow['customerID']);		
					$stmt->execute();

					$msRowCount+=1;
				}
				//end while mssql
				}else{
					echo sqlsrv_error();
				}
				//if
				
				
				////Product_item
				//TRUNCATE temp 
				$sql = "TRUNCATE TABLE product_item_temp
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				//TRUNCATE new 
				$sql = "TRUNCATE TABLE product_item_new
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				$searchFromDate = $_GET['searchFromDate'];
				
				$searchFromDate = to_mysql_date($searchFromDate);
					$sql = "SELECT TOP 1[ProductItemID]
				  ,[ProductID]
				  ,[ItemCode]
				  ,CONVERT(varchar(10),[IssueDate],126) AS IssueDate 
				  ,[MachineID]
				  ,[SeqNo]
				  ,[NW]
				  ,[GW]
				  ,[Length]
				  ,[Grade]
				  ,CONVERT(varchar(10),[IssueGrade],126) AS IssueGrade 
				  ,[UserID]
				  ,[RefItemID]
				  ,[ItemStatus]
				  ,[Remark]
				  ,[RecordDate]
				  ,[ProblemID] FROM product_item 
				   WHERE IssueDate = '$searchFromDate' 
				  ";
				//echo $sql;
				$msResult = sqlsrv_query($ssConn, $sql);
				$msRowCount = 0;
				
				set_time_limit(0);
				while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {
					//Insert mysql from mssql
					$sql = "INSERT INTO  `product_item_temp` 
					(`prodItemId`, `prodId`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`, `qty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`) 
					VALUES
					(:prodItemId,:prodId,:barcode,:issueDate,:machineId,:seqNo,:NW,:GW,:qty,:grade,:gradeDate,:refItemId,:itemStatus,:remark,:problemId)
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':prodItemId', $msRow['ProductItemID']);	
					$stmt->bindParam(':prodId', $msRow['ProductID']);	
					$stmt->bindParam(':barcode', $msRow['ItemCode']);	
					$stmt->bindParam(':issueDate', date($msRow['IssueDate']) );	
					$stmt->bindParam(':machineId', $msRow['MachineID']);	
					$stmt->bindParam(':seqNo', $msRow['SeqNo']);	
					$stmt->bindParam(':NW', $msRow['NW']);	
					$stmt->bindParam(':GW', $msRow['GW']);	
					$stmt->bindParam(':qty', $msRow['Length']);	
					$stmt->bindParam(':grade', $msRow['Grade']);	
					$stmt->bindParam(':gradeDate', date($msRow['IssueGrade']) );	
					$stmt->bindParam(':refItemId', $msRow['RefItemID']);	
					$stmt->bindParam(':itemStatus', $msRow['ItemStatus']);	
					$stmt->bindParam(':remark', $msRow['Remark']);	
					$stmt->bindParam(':problemId', $msRow['ProblemID']);		
					$stmt->execute();
					
					$msRowCount+=1;
				}
				//end while mssql
				
				//Update prodCode
				$sql = "UPDATE product_item_temp itm
				INNER JOIN product_mapping pm on itm.prodId=pm.invProdId
				SET itm.prodCode=pm.prodCode
					";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				
				//Update prod with temp
				$sql = "UPDATE product_item prod 
				INNER JOIN product_item_temp tmp ON tmp.prodItemId=prod.prodItemId
				SET prod.`prodId`=tmp.`prodId`, prod.`prodCode`=tmp.`prodCode`, prod.`barcode`=tmp.`barcode`, prod.`issueDate`=tmp.`issueDate`, prod.`machineId`=tmp.`machineId`
				 , prod.`seqNo`=tmp.`seqNo`, prod.`NW`=tmp.`NW`, prod.`GW`=tmp.`GW`, prod.`qty`=tmp.`qty`, prod.`grade`=tmp.`grade`, prod.`gradeDate`=tmp.`gradeDate`
				 , prod.`refItemId`=tmp.`refItemId`, prod.`itemStatus`=tmp.`itemStatus`, prod.`remark`=tmp.`remark`, prod.`problemId`=tmp.`problemId`
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				//Get only new item
				$sql = "INSERT INTO  `product_item_new` 
				(`prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`, `qty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`) 
				SELECT 
				prodItemId,prodId,prodCode,barcode,issueDate,machineId,seqNo,NW,GW,qty,grade,gradeDate,refItemId,itemStatus,remark,problemId 
				FROM product_item_temp 
				WHERE prodItemId NOT IN (SELECT prodItemId FROM product_item) 
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
								
				//Update prod with temp
				$sql = "INSERT INTO  `product_item` 
				(`prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`, `qty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`) 
				SELECT 
				prodItemId,prodId,prodCode,barcode,issueDate,machineId,seqNo,NW,GW,qty,grade,gradeDate,refItemId,itemStatus,remark,problemId 
				FROM product_item_new 
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				
				
				//sqlsrv_free_stmt($result);
				
				
				
				
				
				
				
				
				
				
				
				
				
				//Update prod with temp
				$sql = "UPDATE send_production prod 
				INNER JOIN send_production_temp tmp ON tmp.sendID=prod.sendID
				SET prod.`sendNo`=tmp.`sendNo`
				, prod.`issueDate`=tmp.`issueDate`
				, prod.`qty`=tmp.`qty`
				, prod.`isCustomer`=tmp.`isCustomer`
				, prod.`customerID`=tmp.`customerID`				
				";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				//Get only new item
				$sql = "INSERT INTO  `send_production_new` 
				(`sendID`, `sendNo`, `issueDate`, `qty`, `isCustomer`, `customerID`) 
				SELECT 
				sendID,sendNo,issueDate,qty,isCustomer,customerID 
				FROM send_production_temp 
				WHERE NOT EXISTS (SELECT * FROM send_production WHERE send_production.sendID=send_production_temp.sendID
								) 
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				//Get only new item
				$sql = "INSERT INTO  `send_production` 
				(`sendID`, `sendNo`, `issueDate`, `qty`, `isCustomer`, `customerID`) 
				SELECT 
				sendID,sendNo,issueDate,qty,isCustomer,customerID 
				FROM send_production_new	
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				
				
				
				
				
				
				
				
				////Detail
				//TRUNCATE temp 
				$sql = "TRUNCATE TABLE send_production_detail_temp
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				//TRUNCATE new 
				$sql = "TRUNCATE TABLE send_production_detail_new
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
								
				$sql = "SELECT [SendID]
			  ,[ProductItemID]
			  ,[Remark] FROM [send_detail]  
				WHERE [SendID] IN (SELECT [SendID] FROM [send] WHERE IssueDate = '$searchFromDate') 
				  ";
				//echo $sql;
				$msResult = sqlsrv_query($ssConn, $sql);
				$msRowCount = 0;
				
				set_time_limit(0);
				while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {										
					//Insert mysql from mssql
					$sql = "INSERT INTO  `send_production_detail_temp` 
					(`sendID`, `ProdItemID`) 
					VALUES
					(:sendID,:ProductItemID)
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':sendID', $msRow['sendID']);	
					$stmt->bindParam(':ProductItemID', $msRow['ProductItemID']);
					$stmt->execute();

					$msRowCount+=1;
				}
				//end while mssql
				
				//update send_production_detail_temp
				//Update prod with temp
				$sql = "UPDATE send_production_detail_temp prod 
				INNER JOIN product_item tmp ON tmp.prodItemId=prod.prodItemId
				SET prod.`prodId`=tmp.`prodId`, prod.`prodCode`=tmp.`prodCode`, prod.`barcode`=tmp.`barcode`, prod.`issueDate`=tmp.`issueDate`, prod.`machineId`=tmp.`machineId`
				 , prod.`seqNo`=tmp.`seqNo`, prod.`NW`=tmp.`NW`, prod.`GW`=tmp.`GW`, prod.`qty`=tmp.`qty`, prod.`grade`=tmp.`grade`, prod.`gradeDate`=tmp.`gradeDate`
				 , prod.`refItemId`=tmp.`refItemId`, prod.`itemStatus`=tmp.`itemStatus`, prod.`remark`=tmp.`remark`, prod.`problemId`=tmp.`problemId`
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();				
				
				//Update prod with temp
				$sql = "UPDATE send_production_detail prod 
				INNER JOIN send_production_detail_temp tmp ON tmp.prodItemId=prod.prodItemId
				SET prod.`prodId`=tmp.`prodId`, prod.`prodCode`=tmp.`prodCode`, prod.`barcode`=tmp.`barcode`, prod.`issueDate`=tmp.`issueDate`, prod.`machineId`=tmp.`machineId`
				 , prod.`seqNo`=tmp.`seqNo`, prod.`NW`=tmp.`NW`, prod.`GW`=tmp.`GW`, prod.`qty`=tmp.`qty`, prod.`grade`=tmp.`grade`, prod.`gradeDate`=tmp.`gradeDate`
				 , prod.`refItemId`=tmp.`refItemId`, prod.`itemStatus`=tmp.`itemStatus`, prod.`remark`=tmp.`remark`, prod.`problemId`=tmp.`problemId`
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				//Get only new item
				$sql = "INSERT INTO  `send_production_detail` 
				(`prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`, `qty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`) 
				SELECT 
				prodItemId,prodId,prodCode,barcode,issueDate,machineId,seqNo,NW,GW,qty,grade,gradeDate,refItemId,itemStatus,remark,problemId 
				FROM send_production_detail_temp 
				WHERE NOT EXISTS (SELECT * FROM send_production_detail WHERE send_production_detail.sendID=send_production_detail_temp.sendID
									AND send_production_detail.prodItemId=send_production_detail_temp.prodItemId ) 
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
								
				
				
				
				
							
				
				
				
				
				
				
				
				
				
				
				
				//STOCK
				//Update Stock balance
				$sql = "UPDATE stk_bal sb 
						INNER JOIN (SELECT left(barcode,1) as sloc,
									prodCode,
									IFNULL(SUM(qty),0) as qty 
									FROM `product_item_new` 
									group by left(barcode,1), prodCode) as temp on temp.prodCode=sb.prodCode AND temp.sloc=sb.sloc
					SET sb.produce=sb.produce+temp.qty
					, sb.balance=sb.balance+temp.qty 
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				//Insert Stock balance
				$sql = "INSERT INTO stk_bal (prodCode,sloc,produce,balance)
				SELECT temp.prodCode, temp.sloc, temp.qty, temp.qty
				FROM (SELECT left(barcode,1) as sloc,
										prodCode,
										IFNULL(SUM(qty),0) as qty 
										FROM `product_item_new` 
										group by left(barcode,1), prodCode) as temp 
				WHERE NOT EXISTS (SELECT * FROM stk_bal sb 
									WHERE sb.prodCode=temp.prodCode
									AND sb.sloc=temp.sloc) 
				";			
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				
				
				

			}
			//end if isset fromDate and toDate 
		?>
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
				<label for="searchFromDate">From Date</label>
				<input type="text" id="searchFromDate" name="searchFromDate" class="form-control datepicker" data-smk-msg="Require Order Date." required >
				
				<input type="submit" class="btn btn-primary" value="Submit" />
				<?php if(isset($_GET['searchFromDate'])) echo 'Updated '.$msRowCount.' items'; ?>
			</div>
			<!--col-md-->
				</form>
				<!--from1-->
			</div>
			<!--/.row-->
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
			language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: true              //Set เป็นปี พ.ศ.
		});  
				
		<?php if(isset($searchFromDate)){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?= $searchFromDate;?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#searchFromDate').datepicker('setDate', realDate);		
		<?php }else{ ?> $('#searchFromDate').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
	});
</script>




<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>