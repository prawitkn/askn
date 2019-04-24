<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  
<?php 

$rootPage = "picking";

$locationCode=$_GET['locCode'];
$pickNo=$_GET['pickNo'];
$id=$_GET['id'];
$custId=$_GET['custId'];
$saleItemId=$_GET['saleItemId'];

?>      
    
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
      
	  <h1><i class="glyphicon glyphicon-shopping-cart"></i>
       Product Stock Info
        <small>Picking management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Picking List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?pickNo=<?=$pickNo;?>" ><i class="glyphicon glyphicon-edit"></i>Picking No.<?=$pickNo;?></a></li>
		<li><a href="#"><i class="glyphicon glyphicon-list"></i>Product Stock Info</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
	  <?php
	  $sql="SELECT name FROM customer_location_type WHERE code=:code ";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':code', $locationCode);
		$stmt->execute();
		$slocName=$stmt->fetch()['name'];

		if($locationCode=='L') $locationCode='8';

		$sql="SELECT code FROM product WHERE id=:id ";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$prodCode=$stmt->fetch()['code'];	
		  ?>

	
	
	<!-- Main row -->
      <div class="row">
		<div class="col-md-12">
			
			<!-- TABLE: LATEST ORDERS -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Available Item Stock : <span style="color: blue; font-weight: bold;"><?=$prodCode;?></span> <span style="color: red; font-weight: bold;">[<?=$slocName;?>]</span></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
			<form id="form2" action="#" method="post" class="form" novalidate>
					<input type="hidden" name="pickNo" value="<?=$pickNo;?>" />
					<input type="hidden" name="action" value="item_add" />			
					<input type="hidden" name="prodId" value="<?=$id;?>" />	
					<input type="hidden" name="saleItemId" value="<?=$saleItemId;?>" />	
            <div class="box-body">
					
					
			<?php

			//No pick cust n prod condition setting.
			$sql="SELECT catCode FROM product WHERE id=:id ";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':id', $id);
			$stmt->execute();	
			$row=$stmt->fetch();
			$catCode=$row['catCode'];

			// Get Customer Condition.
			$sql="SELECT maxDays FROM wh_pick_cond WHERE custId=:custId AND prodId=:id ";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':custId', $custId);
			$stmt->bindParam(':id', $id);
			$stmt->execute();	
			$row=$stmt->fetch();

			$isCustomerPickCondition=false;
			$maxDays=$row['maxDays'];

			if($stmt->rowCount()>0){
				$isCustomerPickCondition=true;
			}



			$pdo->beginTransaction();

          	$sql = "
          	CREATE TEMPORARY TABLE tmpStock (
          		`sloc` varchar(1) NOT NULL,
          		`prodId` int(11) NOT NULL,
				  `prodCode` varchar(100) NOT NULL,
				  `issueDate` date NOT NULL,
				  `prodLife` int(11),
				  `grade` int(11),
				  `qty` decimal(10,2) NOT NULL,
				  `meters` decimal(10,2) NOT NULL,
				  `gradeTypeId` int(11),
				  `gradeTypeName` varchar(100) NOT NULL,
				  `remarkWh` varchar(100) NOT NULL,
				  `total` decimal(10,2) NOT NULL,
				  `bookedQty` decimal(10,2) NOT NULL,
				  `pickQty` decimal(10,2) NOT NULL,
          		  `isShelfed` int(11) NOT NULL
		    )";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();
				
		//switch coz
		//Local order by issueDate, Export order by sendDate
		//Local receive to 8 , Export receive to E
		switch($locationCode){
			case '8' : // local
				$sql = "
				INSERT INTO tmpStock (`sloc`, `prodId`, `prodCode`, `issueDate`, `prodLife`,
				  `grade`, `meters`, `gradeTypeId`, `gradeTypeName`, `remarkWh`, `qty`, `total`, `bookedQty`, `pickQty`,`isShelfed`)
				SELECT hdr.`toCode`, itm.`prodCodeId` as prodId, prd.code as prodCode, itm.`issueDate` as issueDate, DATEDIFF(NOW(), itm.`issueDate`) as prodLife, itm.`grade`
				, itm.`qty` as meters
				, itm.`gradeTypeId`, pgt.name as `gradeTypeName` 
				, itm.`remarkWh` 
				, COUNT(*) as qty
				,  IFNULL(SUM(itm.`qty`),0) as total			
				, 0 as bookedQty
				, 0 as pickQty
				, (SELECT COUNT(x.shelfId) FROM wh_shelf_map_item x WHERE x.recvProdId=dtl.id) as isShelfed
				FROM `receive` hdr 
				INNER JOIN receive_detail dtl on dtl.rcNo=hdr.rcNo  		
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
				LEFT JOIN wh_shelf_map_item smi ON smi.recvProdId=dtl.id 
				LEFT JOIN product prd ON prd.id=itm.prodCodeId 		
				LEFT JOIN product_item_grade_type pgt ON pgt.id=itm.gradeTypeId  				
				WHERE 1=1
				AND hdr.statusCode='P' 	
				AND dtl.statusCode='A' 
				AND itm.prodCodeId=:id ";					
				if($locationCode<>'') $sql .= "AND hdr.toCode=:toCode  ";			
				$sql.="
				GROUP BY itm.`prodCodeId`, itm.`issueDate`, itm.`grade`, prd.code , itm.`qty`, itm.`gradeTypeId`, itm.remarkWh , isShelfed 
				"; 		
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				if($locationCode<>'') $stmt->bindParam(':toCode', $locationCode);
				$stmt->execute();
				break;
			case 'E' : // Export.
				$sql = "
				INSERT INTO tmpStock (`sloc`, `prodId`, `prodCode`, `issueDate`, `prodLife`,
				  `grade`, `meters`, `gradeTypeId`, `gradeTypeName`, `remarkWh`, `qty`, `total`, `bookedQty`, `pickQty`,`isShelfed`)
				SELECT hdr.`toCode`, itm.`prodCodeId` as prodId, prd.code as prodCode, itm.`sendDate` as issueDate, DATEDIFF(NOW(), itm.`sendDate`) as prodLife, itm.`grade`
				, itm.`qty` as meters
				, itm.`gradeTypeId`, pgt.name as `gradeTypeName` 
				, itm.`remarkWh` 
				, COUNT(*) as qty
				,  IFNULL(SUM(itm.`qty`),0) as total			
				, 0 as bookedQty
				, 0 as pickQty
				, (SELECT COUNT(x.shelfId) FROM wh_shelf_map_item x WHERE x.recvProdId=dtl.id) as isShelfed
				FROM `receive` hdr 
				INNER JOIN receive_detail dtl on dtl.rcNo=hdr.rcNo  		
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
				LEFT JOIN wh_shelf_map_item smi ON smi.recvProdId=dtl.id 
				LEFT JOIN product prd ON prd.id=itm.prodCodeId 		
				LEFT JOIN product_item_grade_type pgt ON pgt.id=itm.gradeTypeId  				
				WHERE 1=1
				AND hdr.statusCode='P' 	
				AND dtl.statusCode='A' 
				AND itm.prodCodeId=:id ";					
				if($locationCode<>'') $sql .= "AND hdr.toCode=:toCode  ";			
				$sql.="
				GROUP BY itm.`prodCodeId`, itm.`sendDate`, itm.`grade`, prd.code , itm.`qty`, itm.`gradeTypeId`, itm.remarkWh , isShelfed 
				"; 		
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				if($locationCode<>'') $stmt->bindParam(':toCode', $locationCode);
				$stmt->execute();
				break;
			default : 
				$sql = "
				INSERT INTO tmpStock (`sloc`, `prodId`, `prodCode`, `issueDate`, `prodLife`,
				  `grade`, `meters`, `gradeTypeId`, `gradeTypeName`, `remarkWh`, `qty`, `total`, `bookedQty`, `pickQty`,`isShelfed`)
				SELECT hdr.`toCode`, itm.`prodCodeId` as prodId, prd.code as prodCode, IF(hdr.`toCode`='E',s.sendDate,itm.`issueDate`) as issueDate, DATEDIFF(NOW(), IF( hdr.`toCode`='E', s.sendDate, itm.`issueDate`) ) as prodLife, itm.`grade`
				, itm.`qty` as meters
				, itm.`gradeTypeId`, pgt.name as `gradeTypeName` 
				, itm.`remarkWh` r
				, COUNT(*) as qty
				,  IFNULL(SUM(itm.`qty`),0) as total				
				, 0 as bookedQty
				, 0 as pickQty
				, (SELECT COUNT(x.shelfId) FROM wh_shelf_map_item x WHERE x.recvProdId=dtl.id) as isShelfed
				FROM `receive` hdr 
				INNER JOIN receive_detail dtl on dtl.rcNo=hdr.rcNo  		
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
				INNER JOIN send s ON s.sdNo=hdr.refNo 
				LEFT JOIN wh_shelf_map_item smi ON smi.recvProdId=dtl.id 
				LEFT JOIN product prd ON prd.id=itm.prodCodeId 	
				LEFT JOIN product_item_grade_type pgt ON pgt.id=itm.gradeTypeId  			
				WHERE 1=1
				AND hdr.statusCode='P' 	
				AND dtl.statusCode='A' 
				AND itm.prodCodeId=:id ";
				if($locationCode<>'') $sql .= "AND hdr.toCode=:toCode  ";	
				$sql.="
				GROUP BY itm.`prodCodeId`, 4, itm.`grade`, prd.code , itm.`qty`, itm.`gradeTypeId`, itm.remarkWh , isShelfed 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				if($locationCode<>'') $stmt->bindParam(':toCode', $locationCode);
				$stmt->execute();
		}// End switch.

	// Update booked qty
	$sql = "UPDATE tmpStock hdr SET 
	hdr.bookedQty = (SELECT IFNULL(SUM(pickd.qty),0) FROM picking pickh INNER JOIN picking_detail pickd 
			ON pickh.pickNo=pickd.pickNo
			WHERE pickd.prodId=hdr.prodId AND pickd.issueDate=hdr.issueDate AND pickd.grade=hdr.grade AND pickd.gradeTypeId= hdr.gradeTypeId AND pickd.remarkWh=hdr.remarkWh  AND pickd.meter=hdr.meters  
			AND pickh.isFinish='N'
			AND pickh.statusCode<>'X' 
			AND pickh.pickNo<>:pickNo)";
  	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':pickNo', $pickNo);	
	$stmt->execute();

	// Update picked qty
	$sql = "UPDATE tmpStock hdr SET 
	hdr.pickQty = (SELECT IFNULL(SUM(pickd.qty),0) FROM picking pickh INNER JOIN picking_detail pickd 
			ON pickh.pickNo=pickd.pickNo
			WHERE pickd.saleItemId=:saleItemId AND pickd.issueDate=hdr.issueDate AND pickd.grade=hdr.grade AND pickd.gradeTypeId= hdr.gradeTypeId AND pickd.remarkWh=hdr.remarkWh AND pickd.meter=hdr.meters  
			AND pickh.pickNo=:pickNo )";
	$stmt = $pdo->prepare($sql);		
	$stmt->bindParam(':pickNo', $pickNo);	
	$stmt->bindParam(':saleItemId', $saleItemId);
	$stmt->execute();


	//We've got this far without an exception, so commit the changes.
	$pdo->commit();	

	// Get List.
	$sql = "SELECT `prodId`, `prodCode`, `issueDate`, `prodLife`, `grade`, `qty`, `meters`, `gradeTypeId`, `gradeTypeName`, `remarkWh`, `total`, `bookedQty`, `pickQty`,`isShelfed` 
	FROM tmpStock ";
	$sql .= "ORDER BY issueDate ASC";
	$stmt = $pdo->prepare($sql);		
	$stmt->execute();


	?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
					<th style="text-align: center;">No.</th>
					<!--<th>Product Code</th>-->
					<th style="text-align: center;">MFD.</th>
					<th style="text-align: center;">Prod.Life(Days)</th>
					<th style="text-align: center;">Meter</th>
					<th style="text-align: center;">Grade</th>	
					<th style="text-align: center;">Grade Type</th>				
					<th style="text-align: center;">WH Remark</th>
                    <th style="text-align: center;">Qty</th>
					<th style="text-align: center;">Total</th>
					<th style="text-align: center; color: red;">Booked</th>
					<th style="text-align: center; color: blue;">Balance</th>					
                    <th style="text-align: center;">Pick</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php $row_no = 1; while ($row = $stmt->fetch()) { 

				  // Grade Name.
				  $gradeName = '<b style="color: red;">N/A</b>'; 
				switch($row['grade']){
					case 0 : $gradeName = 'A'; break;
					case 1 : $gradeName = '<b style="color: red;">B</b>'; break;
					case 2 : $gradeName = '<b style="color: red;">N</b>'; break;
					default : 
				} 

				// Product life display.				
				$prodLifeDays = $row['prodLife'];
				if($isCustomerPickCondition){
					if ( $row['prodLife'] > $maxDays ){
						$prodLifeDays = '<b style="color: red;">'.$row['prodLife'].'</b>';
					}
				}else{
					switch($catCode){
						case '72' : //Cutting
							if ( $row['prodLife'] > 90 ){
								$prodLifeDays = '<b style="color: red;">'.number_format($row['prodLife'],0,'.',',').'</b>';
							}
							break;
						default : //80					
					}//end switch.
				}// enc if customer pick condition.
				
				?>
                  <tr>
					<td style="text-align: center;"><?= $row_no; ?></td>
					<!--<td><?= $row['prodCode']; ?></td>-->				
					<td style="text-align: center;"><?= date('d M Y',strtotime( $row['issueDate'] )); ?></td>
					<td style="text-align: center;"><?= $prodLifeDays; ?></td>
					<td style="text-align: right;"><?= number_format($row['meters'],2,'.',','); ?></td>
					<td style="text-align: center;"><?= $gradeName; ?></td>
					<td style="text-align: center;"><?= $row['gradeTypeName']; ?></td>
					<td style="text-align: center;"><?= $row['remarkWh']; ?></td>
					<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
					<td style="text-align: right;"><?= number_format($row['total'],2,'.',','); ?></td>
					<td style="text-align: right; color: red;"><?= number_format($row['bookedQty'],2,'.',','); ?></td>
					<td style="text-align: right; color: blue;"><?= number_format($row['total']-$row['bookedQty'],2,'.',','); ?></td>
					
					<td >
						
						
						<?php if($row['isShelfed']==0){ ?>
							<span style="color: red;">Not Shelfed.</span>
						<?php }else{ ?>
						<input type="hidden" name="issueDate[]" value="<?=$row['issueDate'];?>" />
						<input type="hidden" name="grade[]" value="<?=$row['grade'];?>" />
						<input type="hidden" name="meter[]" value="<?=$row['meters'];?>" />		
						<input type="hidden" name="remarkWh[]" value="<?=$row['remarkWh'];?>" />					
						<input type="hidden" name="gradeTypeId[]" value="<?=$row['gradeTypeId'];?>" />
						<input type="hidden" name="balanceQty[]" value="<?=$row['total']-$row['bookedQty'];?>" />
						<!-- <input type="textbox" name="pickQty[]" class="form-control" style="width: 100px; text-align: right;" value="<?=$row['pickQty'];?>"  
						data-isShelfed="1" 
						onkeypress="return numbersOnly(this, event);" 
						onpaste="return false;"
						style="text-align: right;"
						/> -->
						<input type="textbox" name="pickQty[]" class="form-control" style="width: 100px; text-align: right;" value="<?=$row['pickQty'];?>"  
						data-isShelfed="1" 
						min="0" step="0.01"
						onkeyup="return decimalKeyUp(this);"
						onchange="return decimalOnChange(this);" 
						style="text-align: right;"
						/>
						<?php } ?>
					</td>					
                </tr>
                <?php $row_no+=1; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
				
				<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>
            </div>
            <!-- /.box-footer -->
			
			</form>
			<!--form-->
          </div>
          <!-- /.box -->
		  
		  </div>
		  <!-- col-md-12 -->
		  
      </div>
      <!-- /.row  -->
	   	  
	<div id="spin"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include 'footer.php'; ?>  
  
</div>
<!-- ./wrapper -->
</body>

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>
<!-- Add smoke dialog -->
<script src="bootstrap/js/smoke.min.js"></script>

<script> 
// to start and stop spiner.  
$( document ).ajaxStart(function() {
	$("#spin").show();
}).ajaxStop(function() {
	$("#spin").hide();
});
		
		
$(document).ready(function() { 
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
				
		
	$('#form2 a[name=btn_submit]').click (function(e) {  
		$('#form2 input[data-isShelfed=0]').prop('disabled', false).val(0);
		if ($('#form2').smkValidate()){
			//$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
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
						window.location.href = "<?=$rootPage;?>_add.php?pickNo=<?=$pickNo;?>";
					}else{
						$.smkAlert({
							text: data.message,
							type: 'danger',
							position:'top-center'
						});
						$('#form2 input[data-isShelfed=0]').val("No Shelf.").prop('disabled', true);
					}
					//e.preventDefault();		
				}).error(function (response) {
					alert(response.responseText);
				});
				//.post
			//}});
			//smkConfirm
		e.preventDefault();
		}//.if end
	});
	//.btn_click
});
</script>

</html>


<!--Integers (non-negative)-->
<script>
  function numbersOnly(oToCheckField, oKeyEvent) {
    return oKeyEvent.charCode === 0 ||
        /\d/.test(String.fromCharCode(oKeyEvent.charCode));
  }
  
	
   	function decimalKeyUp(el){
	 var ex = /^[0-9]+\.?[0-9]*$/;
	 if(ex.test(el.value)==false){
	   el.value = el.value.substring(0,el.value.length - 1);
	  }
	}
	function decimalOnChange(el) {
	    var v = parseFloat(el.value);
	    el.value = (isNaN(v)) ? '0.00' : v.toFixed(2);
	}
</script>