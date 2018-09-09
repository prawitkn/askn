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
       Adjust Out
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
            	<div class="col-md-12">	
					<form id="form1" action="<?=basename($_SERVER['PHP_SELF']);?>" method="post" enctype="multipart/form-data" class="form-inline" novalidate>	
					 <div class="row">	
						<div class="col-md-3">	
							<label for="file">File</label><br/>							
							<input type="file" name="file1" id="file1" accept=".txt" >
						</div>
						<!--col-md-->
						
					</div>
					<!--row-->
					<div class="row">
						<div class="col-md-3">	
							<label for="submit"></label>
							<input type="submit" name="submit" class="form-control btn btn-primary" value="Submit" />
						</div>
						<!--col-md-->
					</div>
					<!--row-->
					
					</form>
					<!--from1-->
				</div>
				<!--col-md-->
			</div>
			<!--/.row-->
			<div class="row">
				<div class="col-md-12">
			<?php	
				if( empty($_FILES) ){ 
					//echo 'empty'; 
					echo '<table class="table table-hover">
						<thead>
							<tr>
							<td>Date</td>
							<td>Seq.No.</td>
							<td>Sloc</td>
							<td>Item Code</td>
							</tr>							
						</thead>
						<tbody>
							<tr>
							<td>2018-07-01</td>
							<td>1,2,3,...</td>
							<td>8,E</td>
							<td>Barcode Without - </td>
							</tr>							
						</tbody>
					</table>';
				}else{
				    if( !file_exists($_FILES['file1']['tmp_name']) || !is_uploaded_file($_FILES['file1']['tmp_name'])) {
					   	 echo '<h3>There is no uploaded file.</h3>';    
					}else{
						$countTotal=0;						
						sqlsrv_query($ssConn, 'Truncate TABLE ZO_adjust');
						//echo $_FILES['file1']['tmp_name'];
						$delimiter = "\t";
						$fp = fopen($_FILES['file1']['tmp_name'], 'r');

						while ( !feof($fp) )
						{
						    $line = fgets($fp, 2048);

						    if(trim($line)<>""){
							    $data = str_getcsv($line, $delimiter);

							    $columns = "IssueDate, FromCode, ToCode, ItemCode";

							    $sql = "INSERT INTO ZO_adjust (".$columns.") VALUES (
							    '".$data[0]."'
							    ,'".$data[1]."'
							    ,'".$data[2]."'
							    ,'".$data[3]."'
								) ";
								$msResult = sqlsrv_query($ssConn, $sql);
								//print_r($data);
								
							}
						}                              

						fclose($fp);	

						$sql = "UPDATE ZO_adjust 			
						SET ZO_adjust.ProdItemId=product_item.ProductItemID 
						FROM ZO_adjust	
						LEFT JOIN product_item ON REPLACE(product_item.ItemCode,'-','')=ZO_adjust.ItemCode 
						";
						$msResult = sqlsrv_query($ssConn, $sql);
						if($msResult==false){
							print_r(sqlsrv_errors());
						}

						$sql = "SELECT count(*) as countTotal FROM ZO_adjust WHERE ProdItemId IS NULL  ";
						$msResult = sqlsrv_query($ssConn, $sql);
						$countTotal=sqlsrv_fetch_array($msResult)['countTotal'];
						if($countTotal>0){
							echo '<h3>'.$countTotal.' items can not find product item ID.</h3>';
						}else{ ?>
							
						<?php } ?>
							
					<?php 
						if($countTotal>0){
							$sql = "SELECT CONVERT(VARCHAR(10),IssueDate,121) as IssueDate, ToCode, ProdItemId, ItemCode, ShelfName FROM ZO_adjust WHERE ProdItemId IS NULL    ";
							$msResult = sqlsrv_query($ssConn, $sql);
							echo '<table border="1" class="table table-hover">';
							echo '<thead>
								<tr>
									<th>No.</th>
									<th>Date</th>
									<th>Sloc</th>
									<th>Prod Item Id</th>
									<th>Barcode</th>
								</tr>
							</thead
							<tbody>';
							$rowNo=1;
							while ( $row=sqlsrv_fetch_array($msResult) ) {
								echo '<tr>';
								echo '<td>'.$rowNo.'</td>';
								echo '<td>'.$row['IssueDate'].'</td>';
								echo '<td>'.$row['ToCode'].'</td>';								
								echo '<td>'.$row['ProdItemId'].'</td>';
								echo '<td>'.$row['ItemCode'].'</td>';
								echo '</tr>';

								$rowNo+=1;
							}
							echo '</tbody>';
							echo '</table>';
						}else{ ?>
							<div class="row">
								<div class="col-md-6">			
									<h3>Matched All Barcode.</h3>
								</div>
								<!--col-md-12-->

								


								<div class="col-md-6 pull-right">
									<form id="form2" action="<?=basename($_SERVER['PHP_SELF']);?>" method="post" enctype="multipart/form-data" class="form-inline" novalidate>	
										<input type="hidden" name="mapping" value="1" />
									 <div class="row pull-right">			
										<div class="col-md-3">	
											<label for="submit"></label>
											<input type="submit" name="submit" class="form-control btn btn-primary" value="Mapping Product" />
										</div>
										<!--col-md-->
									</div>
									<!--row-->					
									</form>
									<!--from2-->

									</div>
									<!--col--md12-->

								<div class="col-md-12">
							<?php
							$sql = "SELECT CONVERT(VARCHAR(10),IssueDate,121) as IssueDate, ToCode, ProdItemId, ItemCode FROM ZO_adjust  ";
								$msResult = sqlsrv_query($ssConn, $sql);

								$countTotal=sqlsrv_num_rows($msResult);
								echo '<h3>Raw Data : '.$countTotal.' items.</h3>';


							echo '<table border="1" class="table table-hover">';
							echo '<thead>
								<tr>
									<th>No.</th>
									<th>Date</th>
									<th>Sloc</th>
									<th>Prod Item Id</th>
									<th>Barcode</th>
								</tr>
							</thead
							<tbody>';
							$rowNo=1;
							while ( $row=sqlsrv_fetch_array($msResult) ) {
								echo '<tr>';
								echo '<td>'.$rowNo.'</td>';
								echo '<td>'.$row['IssueDate'].'</td>';
								echo '<td>'.$row['ToCode'].'</td>';								
								echo '<td>'.$row['ProdItemId'].'</td>';
								echo '<td>'.$row['ItemCode'].'</td>';
								echo '</tr>';

								$rowNo+=1;
							}
							echo '</tbody>';
							echo '</table>';
							?>

							</div>
							</div>
						<?php } // else if countTotal

						
					//	echo '<h3>Total : '.sqlsrv_fetch_array($msResult)['countTotal'].' items.</h3>';
						//mssql_free_resul t($sql);
					} // 
			} //if FIES
			



			if( !empty($_POST['mapping']) ){

					$pdo->beginTransaction();
				try{

					$sql = "TRUNCATE TABLE product_item_temp ";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();

					$sql = "SELECT DISTINCT 'SA'+RIGHT(CONVERT(VARCHAR(10),IssueDate,112),6)+FromCode as DocNo, ToCode as Sloc FROM ZO_adjust ";
					$msResult = sqlsrv_query($ssConn, $sql);
					$msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC);
					$docNo=$msRow['DocNo'];
					$Sloc=$msRow['Sloc'];

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
					  ,dtl.[ShelfName] 
				  FROM [ZO_adjust] dtl, [product_item] itm 
				  WHERE dtl.[ProdItemId]=itm.[ProductItemID]
				  ";
					//echo $sql;
					$msResult = sqlsrv_query($ssConn, $sql);
					$msRowCount = 0;
					$c = 1;
						if ( $msResult ) {
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

							$msRowCount+=1;
						}
						//end while mssql
						}else{
							print_r(sqlsrv_errors());
						}
						//if
					
					$sql = "UPDATE product_item_temp tmp 
					INNER JOIN product_mapping map ON map.invProdId=tmp.prodId 
					SET tmp.prodCodeId=map.wmsProdId 
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();	


					//Commit the transaction.
					$pdo->commit();
					echo '<h1>Success!!</h1>';

					$sql = "SELECT prd.code as prodCode, COUNT(tmp.prodItemId) as rowCount, SUM(tmp.qty) as sumQty 
					FROM product_item_temp tmp 
					LEFT JOIN product prd ON prd.id=tmp.prodCodeId 
					GROUP BY prd.code 
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();
					?>


					
					<form id="form4" action="<?=basename($_SERVER['PHP_SELF']);?>" method="post" enctype="multipart/form-data" class="form-inline" novalidate>	
						<input type="hidden" name="revise" value="1" />
						<input type="hidden" name="docNo" value="<?=$docNo;?>" />
						<input type="hidden" name="Sloc" value="<?=$Sloc;?>" />

					 <div class="row pull-right">			
						<div class="col-md-3">	
							<label for="submit"></label>
							<input type="submit" typename="submit" class="form-control btn btn-danger" value="Revise" />
						</div>
						<!--col-md-->
					</div>
					<!--row-->					
					</form>
					<!--from3-->


					<form id="form3" action="<?=basename($_SERVER['PHP_SELF']);?>" method="post" enctype="multipart/form-data" class="form-inline" novalidate>	
						<input type="hidden" name="update" value="1" />
						<input type="hidden" name="docNo" value="<?=$docNo;?>" />
						<input type="hidden" name="Sloc" value="<?=$Sloc;?>" />

					 <div class="row pull-right">			
						<div class="col-md-3">	
							<label for="submit"></label>
							<input type="submit" name="submit" class="form-control btn btn-danger" value="Update" />
						</div>
						<!--col-md-->
					</div>
					<!--row-->					
					</form>
					<!--from2-->
				<?php
					echo '<table class="table table-hover">
						<thead>
							<tr>
							<td>No.</td>
							<td>Product Code</td>
							<td>Qty</td>
							<td>Length/Box/KG</td>
							</tr>							
						</thead>
						<tbody>';
					$rowNo=1;
					while ( $row=$stmt->fetch() ) {
						echo '<tr>';
						echo '<td>'.$rowNo.'</td>';
						echo '<td>'.$row['prodCode'].'</td>';	
						echo '<td>'.$row['rowCount'].'</td>';
						echo '<td>'.$row['sumQty'].'</td>';
						echo '</tr>';

						$rowNo+=1;
					}		
					echo '	</tbody>
					</table>';	


					$sql = "SELECT tmp.*, prd.code as prodCode 
					FROM product_item_temp tmp 
					LEFT JOIN product prd ON prd.id=tmp.prodCodeId 
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();

					echo '<table class="table table-hover">
						<thead>
							<tr>
							<td>No.</td>
							<td>Barcode</td>
							<td>Product Code</td>
							</tr>							
						</thead>
						<tbody>';
					$rowNo=1;
					while ( $row=$stmt->fetch() ) {
						echo '<tr>';
						echo '<td>'.$rowNo.'</td>';
						echo '<td>'.$row['barcode'].'</td>';
						echo '<td>'.$row['prodCode'].'</td>';	
						echo '</tr>';

						$rowNo+=1;
					}		
					echo '	</tbody>
					</table>';	
				} catch(Exception $e) {
					//Rollback the transaction.
					$pdo->rollBack();

					print_r($e->getMessage());
				}	//end try
			}//end if post update







			if( !empty($_POST['update']) ){

					
				try{

					$docNo=$_POST['docNo'];
					$Sloc=$_POST['Sloc'];

					$pdo->beginTransaction();		

					$sql = "INSERT INTO send (`sdNo`, `refNo`, `sendDate`, `fromCode`, `toCode`, `revCount`, `rcNo`, `remark`, `statusCode`, `createTime`, `createById`, `confirmTime`, `confirmById`, `approveTime`, `approveById`)
					VALUES ('$docNo', '',  NOW(), '$Sloc', 'A', 0, 'SYS', 'Adjust Out', 'P',NOW(), 0, NOW(), 0, NOW(), 0) 
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();	


					$sql = "INSERT INTO send_detail (`prodItemId`, `sdNo`) 
					SELECT tmp.prodItemId, '$docNo' 
					FROM product_item_temp tmp 
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();


					$sql = "UPDATE receive_detail rd
					INNER JOIN send_detail sd ON sd.prodItemId=rd.prodItemId 
					INNER JOIN send sh ON sh.sdNo=sd.sdNo 
                    INNER JOIN receive rh ON rh.rcNo=rd.rcNo AND rh.toCode=sh.fromCode
					SET rd.statusCode='X' 
					WHERE sh.sdNo='$docNo' 
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();


					//insert shelf 
					$sql = "DELETE wmi
					FROM wh_shelf_map_item wmi
					WHERE wmi.recvProdId IN (SELECT id FROM receive_detail rDtl
						INNER JOIN product_item_temp itm ON itm.prodItemId=rDtl.prodItemId )
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();	

					


					//Query 5: UPDATE STK BAl toCode
					$sql = "		
					UPDATE stk_bal sb,
					( SELECT itm.prodCodeId, SUM(itm.qty)  as sumQty
						   FROM product_item_temp itm
						   GROUP BY itm.prodCodeId) as s
					SET sb.balance=sb.balance-s.sumQty 
					WHERE sb.prodId=s.prodCodeId
					AND sb.sloc='$Sloc' 
					";
					$stmt = $pdo->prepare($sql);
					$stmt->execute();
						
					//Query 6: INSERT STK BAl toCode
					$sql = "INSERT INTO stk_bal (prodId, sloc, balance) 
					SELECT itm.prodCodeId, '$Sloc', -1*SUM(itm.qty) 
					FROM product_item_temp itm 
					WHERE itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc='$Sloc')
					GROUP BY itm.prodCodeId
					";
					$stmt = $pdo->prepare($sql);
					$stmt->execute();

					//Commit the transaction.
					$pdo->commit();
					echo '<h1>Success!!</h1>';
				} catch(Exception $e) {
					//Rollback the transaction.
					$pdo->rollBack();

					print_r($e->getMessage());
				}	//end try
			}//end if post update
			
			?>	


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
		<?php if(isset($issueDate)){ ?>
		var queryDate = '<?=$issueDate;?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1,dateParts[2]); 
		$('#issueDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#issueDate').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
	});
</script>




<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>