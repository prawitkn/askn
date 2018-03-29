<?php
	include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php';	/*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];		*/
	//$is_local = true;
	if($is_local){
		//include '../db/database_sqlsrv_localhost.php';
	}else{
		include '../db/database_sqlsrv.php';
	}

$rootPage = 'send';

if(isset($_GET['sync']) AND isset($_GET['sendDate'])  ){
	$sendDate = to_mysql_date($_GET['sendDate']);
	//$sendDate = '2017-11-01';
	
	//TRUNCATE temp 
	$sql = "TRUNCATE TABLE send_production";			
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	
	$sql = "TRUNCATE TABLE send_production_detail";			
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
		
	$sql = "TRUNCATE TABLE product_item_temp";			
	$stmt = $pdo->prepare($sql);
	$stmt->execute();

	$sql = "SELECT DISTINCT  hdr.[SendID], hdr.[SendNo], CONVERT(VARCHAR, hdr.[IssueDate], 121) as IssueDate, hdr.[Quantity] as qty
	  , left(itm.[ItemCode],1) as fromCode 
	  , [isCustomer] , [CustomerID]
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
		//Insert mysql from mssql
		$sql = "INSERT INTO  `send_production` 
		(`sendID`, `sendNo`, `issueDate`, `qty`, `fromCode`, `isCustomer`, `customerID`) 
		VALUES
		(:sendID,:sendNo,:issueDate,:qty,:fromCode,:isCustomer,:customerID)
		";		
		
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':sendID', $msRow['SendID']);	
		$stmt->bindParam(':sendNo', $msRow['SendNo']);	
		$stmt->bindParam(':issueDate', $msRow['IssueDate']);	
		$stmt->bindParam(':qty', $msRow['qty']);	
		$stmt->bindParam(':fromCode', $msRow['fromCode']);	
		$stmt->bindParam(':isCustomer', $msRow['isCustomer']);	
		$stmt->bindParam(':customerID', $msRow['CustomerID']);			
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
	  ,dtl.[SendID] 
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
		
		$sql = "INSERT INTO  `send_production_detail` 
		(`prodItemId`, `sendID`) 
		VALUES
		(:ProductItemID, :SendID)
		";		
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':ProductItemID', $msRow['ProductItemID']);	
		$stmt->bindParam(':SendID', $msRow['SendID']);	
		$stmt->execute();

		$msRowCount+=1;
	}
	//end while mssql
	}else{
		echo sqlsrv_error();
	}
	//if
	
	sqlsrv_free_stmt($msResult);
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
	
	
	//Update prodCodeId in product item.
	$sql = "UPDATE product_item_temp tmp 
	INNER JOIN product_mapping map ON map.invProdId=tmp.prodId 
	SET tmp.prodCodeId=map.wmsProdId 
	";			
	$stmt = $pdo->prepare($sql);
	$stmt->execute();	
	
	//Delete production only not approve sending.
	$sql = "DELETE FROM product_item 
	WHERE prodItemId IN (SELECT tmp.prodItemId FROM product_item_temp tmp 
							INNER JOIN send_detail dtl ON dtl.prodItemId=tmp.prodItemId 
							INNER JOIN send hdr ON hdr.sdNo=dtl.sdNo AND hdr.statusCode<>'P')	
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
	
	
	
	//Begin Sync Sending data.
	$sql = "UPDATE send_production prod 
	SET prod.`toCode`= CASE customerID
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
		WHEN 238 THEN '8'
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
	
	//Delete temp if Approved.
	$sql = "DELETE FROM send_production WHERE sendId IN (SELECT refNo FROM send WHERE statusCode='P') 
	";			
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	
	//Delete temp if Approved.
	$sql = "DELETE FROM send_detail WHERE sdNo IN (SELECT sdNo FROM send 
													WHERE refNo IN (SELECT sendId FROM send_production) 
													AND statusCode='B')
	";			
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	
	$sql = "DELETE FROM send WHERE refNo IN (SELECT sendId FROM send_production) AND statusCode='B'
	";			
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	
	//Insert productoin header.
	$sql = "INSERT INTO send 
	(`sdNo`, `refNo`, `sendDate`, `fromCode`, `toCode`, `remark`, `statusCode`, `createTime`, `createById`)
	SELECT `sendNo`, `sendID`, `issueDate`, `fromCode`, `toCode`, `sendNo`, 'B', NOW(), :s_userId
	FROM send_production 
	";	
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':s_userId', $s_userId );	
	$stmt->execute();
	
	//Insert productoin detail .
	$sql = "INSERT INTO send_detail 
	(`prodItemId`, `sdNo`)
	SELECT dtl.`prodItemId`, hdr.sendNo 
	FROM send_production_detail dtl
	INNER JOIN send_production hdr ON hdr.sendID=dtl.sendID  
	";					
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	
	header("Location: ".$rootPage.".php?sendDate=".$_GET['sendDate']);
	
	exit();
}//if sync

?>
    
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">
      <h1><i class="glyphicon glyphicon-arrow-up"></i>
       Send
        <small>Send management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Sending List</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<form id="fromSync" action="<?=$rootPage;?>.php?sync=1" method="get" class="form form-inline" novalidate>
		
			<label class="box-title">Send List</label>
			<label for="sendDate">Sync Date</label>
			<input type="hidden" name="sync" value="1" />
			<input type="text" id="sendDate" name="sendDate" class="form-control datepicker" data-smk-msg="Require Order Date." required  >			
			<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-refresh"></i> Sync Sending Data From Production.</button>
			</form>
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placedhere! -->
          <!-- Here is a label for example -->
          <?php				
                $sql = "
				SELECT COUNT(hdr.sdNo) AS countTotal
				FROM `send` hdr 
				WHERE 1 ";
				switch($s_userGroupCode){ 
					case 'whOff' :  case 'whSup' : 
					case 'pdOff' :  case 'pdSup' :
							$sql .= "AND hdr.fromCode='".$s_userDeptCode."' ";
						break;
					default : //case 'it' : case 'admin' : 
				  }
				$sql .= "AND hdr.statusCode<>'X' 
				";
                $result = mysqli_query($link, $sql);
                $countTotal = mysqli_fetch_assoc($result);
				
				$rows=20;
				$page=0;
				if( !empty($_GET["page"]) and isset($_GET["page"]) ) $page=$_GET["page"];
				if($page<=0) $page=1;
				$total_data=$countTotal['countTotal'];
				$total_page=ceil($total_data/$rows);
				if($page>=$total_page) $page=$total_page;
				$start=($page-1)*$rows;
				if($start<0) $start=0;
          ?>
          <span class="label label-primary">Total <?php echo $countTotal['countTotal']; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<div class="row">
				<div class="col-md-6">					
						<form id="form1" action="<?=$url;?>" method="get" class="form" novalidate>
							<div class="form-group">
								<label for="search_word">Ref. No Or Remark search key word.</label>
								<div class="input-group">
									<input id="search_word" type="text" class="form-control" name="search_word" data-smk-msg="Require userFullname."required>
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-search"></span>
									</span>
								</div>
							</div>						
							<input type="submit" class="btn btn-default" value="ค้นหา">
						</form>
					</div>    
				</div>
           <?php
                $sql = "SELECT hdr.`sdNo`, hdr.`refNo`, hdr.`sendDate`, hdr.`fromCode`, hdr.`toCode`, hdr.`remark`, hdr.`statusCode`
				, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`
				, fsl.name as fromName, tsl.name as toName 
				, cu.userFullname as createByName, fu.userFullname as confirmByName, pu.userFullname as approveByName 
				FROM `send` hdr
				LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
				LEFT JOIN sloc tsl on hdr.toCode=tsl.code
				LEFT JOIN user cu on hdr.createByID=cu.userId 
				LEFT JOIN user fu on hdr.confirmById=fu.userId
				LEFT JOIN user pu on hdr.approveById=pu.userId  
				WHERE 1 ";
				switch($s_userGroupCode){ 
					case 'whOff' :  case 'whSup' : 
					case 'pdOff' :  case 'pdSup' :
							$sql .= "AND hdr.fromCode='".$s_userDeptCode."' ";
						break;
					default : //case 'it' : case 'admin' : 
				  }
				$sql .= "AND hdr.statusCode<>'X' 
				
				ORDER BY hdr.createTime DESC 
				LIMIT $start, $rows 
				";
				//echo $sql;
                $result = mysqli_query($link, $sql);
           ?> 
            <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>Send No.</th>
					<th>Issue date</th>
					<th>From</th>
					<th>To</th>					
					<th>Ref.Send No.</th>			
					<th>Status</th>
					<th>#</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) {
 
					$statusName = '<label class="label label-danger">Unknown</label>';
					switch($row['statusCode']){
						case 'B' : $statusName = '<label class="label label-info">Begin</label>'; break;
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						default : 						
					}
					?>
                <tr>
					<td><?= $row['sdNo']; ?></td>
                    <td><?= to_thai_date_fdt($row['sendDate']); ?></td>
					<td><?= $row['fromCode'].':'.$row['fromName']; ?></td>
					<td><?= $row['toCode'].':'.$row['toName']; ?></td>
					<td><?= $row['remark']; ?></td>
					<td><?= $statusName; ?></td>	
					<td>					
						<a class="btn btn-info " name="btn_row_search" 
							href="send_view.php?sdNo=<?=$row['sdNo'];?>" 
							data-toggle="tooltip" title="Search"><i class="glyphicon glyphicon-search"></i></a>
						<?php
						switch($s_userGroupCode){
						case 'it' : ?>
						<a class="btn btn-danger" name="btn_row_delete" 
							<?php echo ($row['statusCode']=='P'?'data-id="" disabled ':'data-id="'.$row['sdNo'].'" '); ?>
							data-toggle="tooltip" title="Delete" ><i class="glyphicon glyphicon-trash"></i></a>
						<?php 
						break;
						default : 						
						}
						?>						
                    </td>
                </tr>
                <?php } ?>
            </table>
			</div>
			<!--tabl-response-->
			
			<nav>
			<ul class="pagination">
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage;?>.php?search_word=<?= $search_word;?>&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="<?=$rootPage;?>.php?search_word=<?= $search_word;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage;?>.php?search_word=<?= $search_word;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
				</li>
			</ul>
			</nav>
    
    </div><!-- /.box-body -->
  <div class="box-footer">
      
      
    <!--The footer of the box -->
  </div><!-- box-footer -->
</div><!-- /.box -->

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
<!-- smoke validate -->
<script src="bootstrap/js/smoke.min.js"></script>

<script>
$(document).ready(function() {  
	$('a[name=btn_row_delete]').click(function(){
		var params = {
			id: $(this).attr('data-id')
		};
		if(params.id==''){
			$.smkAlert({
				text: 'ข้อมูลรายการนี้ ไม่สามารถลบได้',
				type: 'danger',
				position:'top-center'
			});
			return false;
		}
		//alert(params.id);
		$.smkConfirm({text:'คุณแน่ใจที่จะลบรายการนี้ใช่หรือไม่ ?',accept:'ลบรายการ', cancel:'ไม่ลบรายการ'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_delete_ajax.php',
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
					$.smkAlert({
						text: data.message,
						type: 'danger'//,
					//                        position:'top-center'
					});
				}
			}).error(function (response) {
				alert(response.responseText);
			}); 
		}});
		e.preventDefault();
	});
	
	
	
	
                
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
		
		<?php if(isset($_GET['sendDate'])){ ?>
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?= to_mysql_date($_GET['sendDate']);?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#sendDate').datepicker('setDate', realDate);
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		<?php }else{ ?> $('#sendDate').datepicker('setDate', "0"); <?php } ?>
			
	});
</script>