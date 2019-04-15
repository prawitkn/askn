 
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  

	<!-- fancybox CSS -->
	<link rel="stylesheet" type="text/css" href="plugins/fancybox-master/dist/jquery.fancybox.min.css">

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
      <h1>
		Product Stock Info
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Product Stock Info</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		
	<?php				
		$id = $_GET['id'];
		$sloc = ( isset($_GET['sloc']) ? $_GET['sloc'] : '' );
		$locationCode = $sloc;
		$pickNo='';

		$sql = "SELECT `code`, `name`, `name2`, `photo`, `uomCode`, `description`, `appCode`, `statusCode` 
				FROM `product` 
				WHERE 1
				AND id=:id 
				";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$row = $stmt->fetch();
	?>
      <!-- Your Page Content Here -->
	  <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">              
			  <form id="frmPeriod" method="get" class="form-inline">
				<label class="box-title">Product Code : <?=$row['code'];?></label>			
			  </form>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                  </ul>
                </div>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
				<div class="col-md-12">
					<div class="col-md-3" style="text-align: center;">
						
						<a href="../images/product/<?php echo (empty($row['photo'])? 'default.jpg' : $row['photo']) ?> " data-fancybox="images" data-caption="<?=$row['code'];?>">
							<image src="../images/product/<?php echo (empty($row['photo'])? 'default.jpg' : $row['photo']) ?> " width="150" height="150" />
						</a>
					</div>				
	                <div class="col-md-9">
						<label>Product Name : </label>
						<?= $row['name']; ?><br/>
						<label>Description : </label>
						<?= $row['description']; ?><br/>	
						<label>Units of Measurement (Sales UOM)  : </label>
						<span style="color: red;"><?= $row['uomCode']; ?></span><br/>						
	                </div><!-- /.col-10 -->
	         	</div>
	         	<!--/.col-md-8-->
	         	</div><!--/.row-->

	         <div class="row">
				<div class="col-md-12">
					<h3>Avalible Item Stock</h3>
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
				SELECT hdr.toCode, itm.`prodCodeId` as prodId, prd.code as prodCode, itm.`issueDate`, DATEDIFF(NOW(), itm.`issueDate`) as prodLife, itm.`grade`
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
				SELECT hdr.toCode, itm.`prodCodeId` as prodId, prd.code as prodCode, s.sendDate as issueDate, DATEDIFF(NOW(), s.sendDate) as prodLife, itm.`grade`
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
				if($locationCode<>'') { $sql .= "AND hdr.toCode=:toCode  "; }else{ $sql .= "AND hdr.toCode IN ('8','E')  "; }	
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
			WHERE pickd.prodId=hdr.prodId AND pickd.issueDate=hdr.issueDate AND pickd.grade=hdr.grade AND pickd.meter=hdr.qty AND pickd.gradeTypeId= hdr.gradeTypeId AND pickd.remarkWh=hdr.remarkWh 
			AND pickh.isFinish='N'
			AND pickh.statusCode<>'X' 
			AND pickh.pickNo<>:pickNo)";
  	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':pickNo', $pickNo);	
	$stmt->execute();

	// // Update picked qty
	// $sql = "UPDATE tmpStock hdr SET 
	// hdr.pickQty = (SELECT IFNULL(SUM(pickd.qty),0) FROM picking pickh INNER JOIN picking_detail pickd 
	// 		ON pickh.pickNo=pickd.pickNo
	// 		WHERE pickd.saleItemId=:saleItemId AND pickd.issueDate=hdr.issueDate AND pickd.grade=hdr.grade AND pickd.meter=hdr.qty  AND pickd.gradeTypeId= hdr.gradeTypeId AND pickd.remarkWh=hdr.remarkWh 
	// 		AND pickh.pickNo=:pickNo )";
	// $stmt = $pdo->prepare($sql);		
	// $stmt->bindParam(':pickNo', $pickNo);	
	// $stmt->bindParam(':saleItemId', $saleItemId);
	// $stmt->execute();


	//We've got this far without an exception, so commit the changes.
	$pdo->commit();	

	// Get List.
	$sql = "SELECT  `sloc`, `prodId`, `prodCode`, `issueDate`, `prodLife`, `grade`, `qty`, `meters`, `gradeTypeId`, `gradeTypeName`, `remarkWh`, `total`, `bookedQty`, `pickQty`,`isShelfed` 
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
					<th style="text-align: center;">Location</th>
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
                  </tr>
                  </thead>
                  <tbody>
				  <?php $sumQtyTotal=0; $row_no = 1; while ($row = $stmt->fetch()) { 

				  // Location Name.
				  $locationName = '<b style="color: red;">N/A</b>'; 
				switch($row['sloc']){
					case 'E' : $locationName = 'Export'; break;
					default : $locationName = 'Local'; 
				} 

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
					<td style="text-align: center;"><?= $locationName; ?></td>
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
							
						<?php } ?>
					</td>					
                </tr>
                <?php $row_no+=1; $sumQtyTotal += $row['total']; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
					
                </div><!-- /.col-4 -->
            </div><!-- /.row -->
            </div>  
<!-- Day8 00:05:45-->            
    
	
	<!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-12">
          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Stock Balance VS Order Pendings</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			 <div id="container" style="width:100%; height:400px;">
                
              </div> 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
          		
		
		
          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Pending Orders</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			
			<?php	
					$sql = "
					SELECT soNo, deliveryDate, prodId, prodCode
					, sum(qty) as sumOrderQty, sum(sentQty) as sumSentQty 
					, customerName 
					FROM (
						SELECT hdr.soNo, dtl.deliveryDate
						, dtl.prodId, prd.code as prodCode
						, dtl.id, dtl.qty
						, (SELECT sum(xd.qty) FROM picking xh 
							INNER JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
							WHERE xh.soNo=hdr.soNo AND xd.saleItemId=dtl.id
							AND xh.isFinish='Y' AND xh.statusCode='P') as sentQty  
						, cust.name as customerName 
						FROM `sale_header` hdr
						INNER JOIN sale_detail dtl ON dtl.soNo=hdr.soNo
						INNER JOIN product prd ON prd.id=dtl.prodId 
						INNER JOIN customer cust ON cust.id=hdr.custId
						";				
						if ($sloc<>"") $sql.="AND cust.locationCode=:sloc ";
						
						$sql .= "WHERE 1 
						AND hdr.statusCode='P' 
						AND hdr.isClose='N' 
						";
						if($id<>""){ $sql .= " AND dtl.prodId=:id ";	}		
						$sql .= "
					) as tmp ";
					$sql.="GROUP BY soNo, deliveryDate, prodId, prodCode ";
					$sql.="
					ORDER BY deliveryDate ASC 
					";
					//$sql .= "LIMIT $start, $rows ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':id', $id);
					switch ($sloc) {
						case '8': $tmp="L"; $stmt->bindParam(':sloc', $tmp ); break;		
						case 'E': $tmp="E"; $stmt->bindParam(':sloc', $tmp ); break;					
						default: break;
					}

					$stmt->execute();       
					
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th style="text-align: center;">Order No.</th>
                    <th style="text-align: center;">Customer</th>
                    <th style="text-align: center;">Delivery Date</th>
                    <th style="text-align: center;">Pending/Order</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php 
				  	$tmpRemainQty=$sumQtyTotal;
					$dateName = array();
					$remainQty = array();
			        $orderQty = array();

        			$row_no = 1; while ($row = $stmt->fetch()) { 
					 $pendingQty=$row['sumOrderQty']-$row['sumSentQty'];

					$dateNameStr="";
					$dt = new DateTime($row['deliveryDate']); 
					$dateNameStr=$dt->format('d M Y');

					 $dateName[] = $dateNameStr;
					 $remainQty[] = ($tmpRemainQty-$pendingQty);
					 $orderQty[] = $pendingQty;
					 $tmpRemainQty-=$pendingQty;
						?>
                  <tr>
                    <td style="text-align: center;"><a href="sale2_view.php?soNo=<?=$row['soNo'];?>" target="_blank"><?= $row['soNo']; ?></a></td>
                    <td style="text-align: center;"><?= $row['customerName']; ?></a></td>
                    <td style="text-align: center;"><?= $dateNameStr; ?></a></td>
					<td style="text-align: center;"><?= number_format($pendingQty,2,'.',',').' / '.number_format($row['sumOrderQty'],2,'.',','); ?></td>
                </tr>
                <?php $row_no+=1; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->



           




		  
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row second box col8 & col 4 -->
	  
	  
	
	
  
	<div id="spin"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>  
  
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
<!-- Add _.$ jquery coding -->
<script src="assets/underscore-min.js"></script>
<!-- Add fancybox JS 
<script src="//code.jquery.com/jquery-3.2.1.min.js"></script>-->
<script src="plugins/fancybox-master/dist/jquery.fancybox.min.js"></script>

<!-- Hightchart -->
<script src="plugins/highcharts-5.0.12/code/highcharts.js"></script>
<script src="plugins/highcharts-5.0.12/code/modules/exporting.js"></script>

<script>
$(function () { 
  Highcharts.setOptions({
    colors: ['red', 'green', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
});

    var myChart = Highcharts.chart('container', {
        chart: {
        type: 'line'
    },
    title: {
        text: 'Stock Balance VS Order Pendings'
    },
    credits: {
        enabled: false
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        allowDecimals: false,
        labels: {
            formatter: function () {
                return this.value; // clean, unformatted number for year
            }
        }
    },
    yAxis: {
        title: {
            text: 'Order Pending'
        },
        labels: {
            formatter: function () {
                return this.value / 1000 + 'k';
            }
        }
    },
    tooltip: {
        pointFormat: 'Quantity <b>{point.y:,.0f}</b>'
    },
        xAxis: {
            
            //categories: ['Apples', 'Bananas', 'Oranges'],
            categories: [<?php echo "'" . implode("','", $dateName) . "'"; ?>]
                        //'prod5','prod6','prod7'
        },
        yAxis: {
            title: {
                text: 'Quantity'
            }
        },
        series: [{
            name: 'Order',
            data: [<?php echo implode(",", $orderQty); ?>],
            //data: [1, 0, 4]
            dataLabels: {
                enabled: true,
                inside: false,
                rotation: 0,
                y: 0,
                style: {
                            fontWeight: 'bold'
                        },
                        format: '{point.y:,.0f}'
                    }
       },{
            name: 'Balance',
            data: [<?php echo implode(",", $remainQty); ?>],
            //data: [1, 0, 4]
            dataLabels: {
                enabled: true,
                inside: false,
                rotation: 0,
                y: 0,
                style: {
                            fontWeight: 'bold'
                        },
                        format: '{point.y:,.0f} '
                    }
       }
        ]
    });
  });
</script>


<script> 
  // to start and stop spiner.  
$( document ).ajaxStart(function() {
        $("#spin").show();
		}).ajaxStop(function() {
            $("#spin").hide();
        });  
		
		
       $(document).ready(function() {    
            $("#title").focus();
            var spinner = new Spinner().spin();
            $("#spin").append(spinner.el);
            $("#spin").hide();
						
				
			$('a[name=btn_submit]').click(function(){				
				var checked='';
				$('input[name=statusCode]:checked').each(function(){
					if(checked.length==0){
						checked=$(this).val();
					}else{
						checked=checked+','+$(this).val();
					}
				});
				var params = {
					id: $('#id').val(),
					name: $('#name').val(),
					surname: $('#surname').val(),
					positionName: $('#positionName').val(),
					mobileNo: $('#mobileNo').val(),
					email: $('#email').val(),
					statusCode: checked
				};								
				//alert(params.status_code);
				$.post({
					url: 'salesman_edit_ajax.php',
					data: params,
					dataType: 'json'
				}).done(function (data) {					
					 if (data.success){ 
						 $.smkAlert({
							 text: data.message,
							 type: 'success',
							 position:'top-center'
						 });
						 } else {
							 $.smkAlert({
								 text: data.message,
								 type: 'danger'//,
	   //                        position:'top-center'
								 });
						 }
						 $('#form1').smkClear();
						 //$("#title").focus(); 
				}).error(function (response) {
					  alert(response.responseText);
				});    				
			});
	});
  </script>
</html>
