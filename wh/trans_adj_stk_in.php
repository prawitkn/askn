<?php
  //include '../db/database_sqlsrv.php';
  include_once '../db/db_sqlsrv.php';
  //include 'inc_helper.php';  
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
       Adjust In
        <small>Config Menu</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Adjust In</a></li>
        <li class="active">Import</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">		
        <div class="box-header with-border">
        <h3 class="box-title">Import File</h3>
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
							set_time_limit(0);
							
						$sendDate = $_GET['sendDate'];
						//$fromCode = $_POST['fromCode'];
						//$toCode = $_POST['toCode'];
						//$prodId = $_POST['prodId'];
						//$sendDate = to_mysql_date($sendDate);
						$sendDate = str_replace('/', '-', $sendDate);
						$sendDate = date("Y-m-d",strtotime($sendDate));
						//echo date("Y-m-d",strtotime($sendDate));
						//echo $sendDate;

						//"DocNo", "IssueDate", "FromCode", "ToCode", "ProdItemId", "ItemCode", "ShelfName" FROM "dbo"."z_adjust_in"
						$sql = "SELECT DISTINCT DocNo, CONVERT(char(10), IssueDate,121) as IssueDate, FromCode, ToCode
						FROM z_adjust_in WHERE IssueDate='$sendDate' ";	
						$msResult = sqlsrv_query($ssConn, $sql);
						
						$arrDocNo=array();
						$msRowCount = 0;
						$c = 1;
						//set_time_limit(0);
						if($msResult){
							while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {	
								//Insert Header mysql from mssql
								$sql = "INSERT INTO  `receive` 
								(`rcNo`, `refNo`, `type`, `ReceiveDate`, `fromCode`, `toCode`, `sdNo`
								, `statusCode`, `createTime`, `createById`) 
								VALUES
								(:rcNo,'INI','S',:receiveDate, :fromCode, :toCode, 'INI'
								,'P',NOW(),0)
								";		
								
								$stmt = $pdo->prepare($sql);
								$stmt->bindParam(':rcNo', $msRow['DocNo']);	
								$stmt->bindParam(':receiveDate', $msRow['IssueDate']);	
								$stmt->bindParam(':fromCode', $msRow['FromCode']);	
								$stmt->bindParam(':toCode', $msRow['toCode']);									
								//$stmt->execute();
								
								$arrDocNo[] = $msRow['DocNo'];
								//$msRowCount+=1;
							}
							//end while mssql
						}else{
							print_r(sqlsrv_errors());
						}
						//if
						
						
						$docNoList='\''.implode('\',\'', $arrDocNo).'\'';
						echo $docNoList;
						
						$sql = "SELECT DocNo, CONVERT(char(10), IssueDate,121) as IssueDate, FromCode, ToCode, ProdItemId, ItemCode, ShelfName 
						FROM z_adjust_in WHERE IssueDate='$sendDate' AND ProdItemId IS NOT NULL ";	
						$msResult = sqlsrv_query($ssConn, $sql);
						$msRowCount = 0;
						$c = 1;
						//set_time_limit(0);
						if($msResult){
							while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {	
								//Insert Detail mysql from mssql
								$sql = "INSERT INTO  `receive_detail` 
								(`prodItemId`, `statusCode`, `rcNo`) 
								VALUES
								(:prodItemId,'A',:rcNo)
								";		
								
								$stmt = $pdo->prepare($sql);								
								$stmt->bindParam(':prodItemId', $msRow['ProdItemId']);
								$stmt->bindParam(':rcNo', $msRow['DocNo']);									
								$stmt->execute();

								//$msRowCount+=1;
							}
							//end while mssql
						}else{
							print_r(sqlsrv_errors());
						}
						//if
						
						sqlsrv_free_stmt($msResult);
						
						
						
						//Insert Detail mysql from mssql
						$sql = "TRUNCATE TABLE `product_item_temp` ";	
						$stmt = $pdo->prepare($sql);												
						$stmt->execute();
						
						
						$sql = "SELECT
						itm.ProductItemID, itm.ProductID, itm.ItemCode, 
						CONVERT(char(10), itm.IssueDate,121) as IssueDate
						, itm.MachineID, itm.SeqNo, itm.NW, itm.GW, itm.Length, itm.Grade
						,CONVERT(char(10), itm.IssueGrade,121) as IssueGrade
						, itm.UserID, itm.RefItemID, itm.ItemStatus, itm.Remark, itm.RecordDate, itm.ProblemID
						FROM z_adjust_in h
						INNER JOIN product_item itm ON itm.ProductItemID=h.ProdItemId 						
						WHERE h.IssueDate='$sendDate'
						AND h.prodItemId IS NOT NULL  ";	
						$msResult = sqlsrv_query($ssConn, $sql);
						$msRowCount = 0;
						$c = 1;
						//set_time_limit(0);
						if($msResult){
							while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {								
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

								//$msRowCount+=1;
							}
							//end while mssql
						}else{
							print_r(sqlsrv_errors());
						}
						//if
						
						sqlsrv_free_stmt($msResult);
												
						
						//Update prodCodeId in product item.////////////////////////////////////////////
						$sql = "UPDATE product_item_temp tmp 
						INNER JOIN product_mapping map ON map.invProdId=tmp.prodId 
						SET tmp.prodCodeId=map.wmsProdId 
						";			
						$stmt = $pdo->prepare($sql);
						$stmt->execute();	
						//Update prodCodeId in product item.////////////////////////////////////////////
						
						
						//Insert Product ITem Production
						$sql = "INSERT INTO product_item
						SELECT *
						, 1, '' 
						FROM product_item_temp 
						";			
						$stmt = $pdo->prepare($sql);
						$stmt->execute();	
												
						//Query 5: UPDATE STK BAl sloc to 
						$sql = "		
						UPDATE stk_bal sb,
						( SELECT itm.prodCodeId, hdr.toCode, sum(itm.qty)  as sumQty
							   FROM receive hdr 
							   INNER JOIN receive_detail dtl ON hdr.rcNo=dtl.rcNo 
							   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
							   WHERE hdr.rcNo IN (".$docNoList.") 
							   GROUP BY itm.prodCodeId, dtl.toCode) as s
						SET sb.onway=sb.onway-s.sumQty
						WHERE sb.prodId=s.prodCodeId
						AND sb.sloc=s.toCode 
						";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();
						
						//Query 6: INSERT STK BAl sloc to 
						$sql = "INSERT INTO stk_bal (prodId, sloc, balance) 
								SELECT itm.prodCodeId, hdr.toCode, SUM(itm.qty) 
								FROM receive hdr 
							   INNER JOIN receive_detail dtl ON hdr.rcNo=dtl.rcNo 
								INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
								WHERE hdr.rcNo IN (".$docNoList.") 
								AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=hdr.toCode)
								GROUP BY itm.prodCodeId
								";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();				

				}
				//end if isset fromDate and toDate 
			?>
			
				<?php //if(isset($_GET['sendDate'])) echo 'Updated '.$msRowCount.' items'; ?>
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