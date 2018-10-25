<?php
  //include '../db/database_sqlsrv.php';
  include_once '../db/database_localhost.php';
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
       Q DATA
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
						;
						
						//TRUNCATE temp 
						//echo "Alter id...";
						$sql = "TRUNCATE TABLE z_product_item_q ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();
						
						//TRUNCATE temp 
						//echo "Alter id...";
						$sql = "TRUNCATE TABLE z_send_detail_q ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();

						$sql = "TRUNCATE TABLE z_send_q ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();

						//TRUNCATE temp 
						echo "Get data...";
						$sql = "SELECT   `fromCode`, `sendDate`, `prodId`, `prodName`, `issueDate`, `length`, `qty`  
						, DATE_FORMAT(`issueDate`,'%d%m%y') as dmy
						FROM z_demo_item";			
						$stmt = $pdo->prepare($sql);
						if($stmt->execute()){
							
							$id=300001;
							$irow=1;
							while ($row = $stmt->fetch() )  {	
								$irow=1;
								for ($i = 1; $i <= $row['qty']; $i++) {
									$barcode=$row['fromCode'].'-'.$row['prodName'].'-'.$row['dmy'].'-DEMO-'.substr('00'.$irow,-2);
									
								    //Insert Header mysubstrsql from mssql
									$sql = "INSERT INTO z_product_item_q 
									(`prodItemId`,`prodCodeId`,`barcode`,`issueDate`,`seqNo`,`NW`,`GW`,`qty`
									,`grade`,`gradeDate`,`gradeTypeId`,`remark`)
									VALUES (:prodItemId, :prodCodeId, :barcode, :issueDate,:seqNo, 9.99, 9.99, :qty
									,0,:gradeDate,1,:remark)
									";														
									$stmt2 = $pdo->prepare($sql);
									$stmt2->bindParam(':prodItemId', $id);	
									$stmt2->bindParam(':prodCodeId', $row['prodId']);
									$stmt2->bindParam(':barcode', $barcode);	
									$stmt2->bindParam(':issueDate', $row['issueDate']);	
									$stmt2->bindParam(':seqNo', $irow);	
									$stmt2->bindParam(':qty', $row['length']);	
									$stmt2->bindParam(':gradeDate', $row['issueDate']);	
									$stmt2->bindParam(':remark', $row['sendDate']);										
									$stmt2->execute();

									$id+=1;
									$irow+=1;
								}//end for
								$i=1;
							}
							//end while mssql
						}

						 $sql = "SELECT  DISTINCT `fromCode`, `sendDate` FROM z_demo_item";	
						$stmt = $pdo->prepare($sql);		
						
						if($stmt->execute()){
							$irow=1;
							while ($row = $stmt->fetch() )  {	
								$fromCode=$row['fromCode'];
								$sendDate=$row['sendDate'];
								$sdNo='SDM'.(string)date("y", strtotime($sendDate)).$fromCode.substr('0000'.$irow,-4);

								$sql = "INSERT INTO z_send_q  
								(sdNo, sendDate, fromCode, toCode, statusCode)
								VALUES (:sdNo, :sendDate, :fromCode, 8, 'P') 
								";														
								$stmt2 = $pdo->prepare($sql);
								$stmt2->bindParam(':sdNo', $sdNo);	
								$stmt2->bindParam(':sendDate', $sendDate);
								$stmt2->bindParam(':fromCode', $fromCode);
								$stmt2->execute();
		
								$sql = "INSERT INTO z_send_detail_q 
								(`prodItemId`,`sdNo`)
								SELECT prodItemId, :sdNo FROM z_product_item_q itm
								WHERE LEFT(itm.barcode,1)=:fromCode
								AND itm.remark=:sendDate 
								";															
								$stmt2 = $pdo->prepare($sql);
								$stmt2->bindParam(':sdNo', $sdNo);	
								$stmt2->bindParam(':fromCode', $fromCode);
								$stmt2->bindParam(':sendDate', $sendDate);
								$stmt2->execute();
								
								$irow+=1;
							}
							//end while mssql
						}//if distinct 
											
						
				}
				//end if isset fromDate and toDate 
			?>
			
				<?php if(isset($_GET['sendDate'])) echo 'Updated '.$irow.' items'; ?>
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

<!--
INSERT INTO SEND 
SELECT * FROM `z_send_q` WHERE 1 ;


insert into send_detail  (prodItemId, sdNo)
SELECT prodItemId, sdNo  FROM `z_send_detail_q` WHERE 1 ;


INSERT INTO product_item SELECT * FROM `z_product_item_q` WHERE 1 ;







DELETE FROM `product_item` WHERE prodItemId<=21350

DELETE FROM `send_detail` WHERE  sdNo like 'SI%'


-->