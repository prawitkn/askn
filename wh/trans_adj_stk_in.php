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
						
						$sql = "SELECT DISTINCT barcode FROM  i_adjust_with_shelf LIMIT 100   ";			
						$stmt1 = $pdo->prepare($sql);
						if($stmt1->execute()){
							$sumFound=0;
							$sumNotFound=0;
							
							while ($row1 = $stmt1->fetch() )  {	
							$barcode=$row1['barcode'];
								$sql = "  SELECT TOP 1 itm.[ProductItemID]
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
							  FROM [product_item] itm 
							  WHERE REPLACE(itm.ItemCode, '-', '')='$barcode' 
								  "; //echo $sql;
								  //echo $sql; 
								$msResult = sqlsrv_query($ssConn, $sql, array(), array( "Scrollable" => 'static' ));
									if(sqlsrv_num_rows($msResult)==1){
									$sumFound+=1;}else{ $sumNotFound+=1; }
									
								 //$msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC);
						
							/*$sql = "INSERT INTO  `i_product_item` 
							(`prodItemId`, `prodId`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`
							, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`) 
							VALUES
							(:ProductItemID,:ProductID,:ItemCode,:IssueDate,:MachineID,:SeqNo,:NW,:GW
							,:Length,null,:Grade,:IssueGrade,:RefItemID,:ItemStatus,:Remark,:ProblemID
							)
							";	
							var_dump($msRow); echo '</br></br/>';
							/*$stmt = $pdo->prepare($sql);
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
							*/
								//$msRowCount+=1;
							}
							//end while mssql
							echo '<h1>'.$sumFound.'</h1>';
							echo '<h1>'.$sumNotFound.'</h1>';
						}	

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