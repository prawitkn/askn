<?php 
	include 'inc_helper.php';
	//include($_SERVER['DOCUMENT_ROOT']."inc_helper.php");
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php 
	include 'head.php'; 
	//include($_SERVER['DOCUMENT_ROOT']."head.php");
	
	$yesterday=date('Y-m-d', strtotime("-1 day"));
	$today=date('Y-m-d');
	$nextDay=date('Y-m-d', strtotime("+1 day"));
	$nextTwoDays=date('Y-m-d', strtotime("+2 day"));
	$nextThreeDays=date('Y-m-d', strtotime("+3 day"));

	$tomorrow="";
	$tomorrowStr="";
	switch(date('D')){
		case 'Sun' : case 'Mon' : case 'Tue' : case 'Wed' : case 'Thu' : 
			$tomorrow=date('Y-m-d', strtotime("+1 day"));
			$tomorrowStr=date("l jS \of F Y", strtotime("+1 day"));
			break;
		case 'Fri' : 
			$tomorrow=date('Y-m-d', strtotime("+3 day"));
			$tomorrowStr=date("l jS \of F Y", strtotime("+3 day"));
			break;
		case 'Sat' : 
			$tomorrow=date('Y-m-d', strtotime("+2 day"));
			$tomorrowStr=date("l jS \of F Y", strtotime("+2 day"));
			break;
	}
	//$tomorrow=date('Y-m-d', strtotime("+1 day"));
?>
 
</head>
<body class="hold-transition skin-green sidebar-mini">


	
  

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
        Welcome to Warehouse Department
        <small><?php echo $s_userFullname; ?> [ ID: <?php echo $s_userId; ?>] </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Page</a></li>
        <li class="active">Here</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
		<div class="row">
			<div class="col-md-3 col-sm-6 col-xs-12">
				   <div class="info-box">
					   <span class="info-box-icon bg-green"><i class="glyphicon glyphicon-arrow-down"></i></span>
					   <div class="info-box-content"> 
						   <?php
							$sql ="SELECT COUNT(*) as countOrder
										FROM send
										WHERE statusCode='P'
										AND rcNo IS NULL						
										";
							switch($s_userGroupCode){
								case 'whOff' : case 'whSup' : case 'whMgr' :
									$sql .= " AND toCode IN ('0','7','8','E') ";
									break;
								case 'pdOff' :  case 'pdSup' : 
									$sql .= " AND toCode=:toCode ";
									break;
								case 'pdMgr' : $sql .= " AND toCode IN ('4','5','6') ";
									break;
								default : // it, admin
							}
							$stmt = $pdo->prepare($sql);
							switch($s_userGroupCode){
								case 'pdOff' :  case 'pdSup' : 
									$stmt->bindParam(':toCode', $s_userDeptCode);
									break;
								default : // it, admin
							}							
							$stmt->execute();
							$row = $stmt->fetch();
							?>
						   <span class="info-box-text"> In Pending</span>
						   <span class="info-box-number"><?= number_format($row['countOrder'], 0, '.', ','); ?> <small> Trans.</small>.</span>
						   
					   </div><!-- /.info-box-content -->
				   </div> <!-- /.info-box -->
				</div> <!-- /.col --> 
				
				<div class="col-md-3 col-sm-6 col-xs-12">
				   <div class="info-box">
					   <span class="info-box-icon bg-green"><i class="glyphicon glyphicon-arrow-up"></i></span>
					   <div class="info-box-content"> 
						   <?php
							$sql ="SELECT COUNT(*) as countOrder
										FROM send
										WHERE statusCode='P'
										AND rcNo IS NULL						
										";
							switch($s_userGroupCode){
								case 'whOff' : case 'whSup' : case 'whMgr' :
									$sql .= " AND fromCode IN ('0','7','8','E') ";
									break;
								case 'pdOff' :  case 'pdSup' : 
									$sql .= " AND fromCode=:fromCode ";
									break;
								case 'pdMgr' : $sql .= " AND fromCode IN ('4','5','6') ";
									break;
								default : // it, admin
							}
							$stmt = $pdo->prepare($sql);
							switch($s_userGroupCode){
								case 'pdOff' :  case 'pdSup' : 
									$stmt->bindParam(':fromCode', $s_userDeptCode);
									break;
								default : // it, admin
							}							
							$stmt->execute();
							$row = $stmt->fetch();							
							?>
						   <span class="info-box-text"> Out Pending</span>
						   <span class="info-box-number"><?= number_format($row['countOrder'], 0, '.', ','); ?> <small> Trans.</small>.</span>
						   
					   </div><!-- /.info-box-content -->
				   </div> <!-- /.info-box -->
				</div> <!-- /.col --> 
								
				<div class="clearfix visible-sm-block"></div>
				
				<div class="col-md-3 col-sm-6 col-xs-12">
				   <div class="info-box">
					   <span class="info-box-icon bg-red"><i class="glyphicon glyphicon-import"></i></span>
					   <div class="info-box-content"> 
						   <?php
							$sql ="SELECT COUNT(*) as countOrder
										FROM receive
										WHERE statusCode='P'					
										";
							switch($s_userGroupCode){
								case 'whOff' : case 'whSup' : case 'whMgr' : 
									$sql .= " AND toCode IN ('0','7','8','E') ";
									break;
								case 'pdOff' :  case 'pdSup' : 
									$sql .= " AND toCode=:toCode ";
									break;
								case 'pdMgr' : $sql .= " AND toCode IN ('4','5','6') ";
									break;
								default : // it, admin
							}
							$stmt = $pdo->prepare($sql);
							switch($s_userGroupCode){
								case 'pdOff' :  case 'pdSup' :  
									$stmt->bindParam(':toCode', $s_userDeptCode);
									break;
								default : // it, admin
							}							
							$stmt->execute();
							$row = $stmt->fetch();		
							?>
						   <span class="info-box-text"> Received </span>
						   <span class="info-box-number"><?= number_format($row['countOrder'], 0, '.', ','); ?> <small> Trans.</small>.</span>
						   
					   </div><!-- /.info-box-content -->
				   </div> <!-- /.info-box -->
				</div> <!-- /.col --> 
				
				<div class="col-md-3 col-sm-6 col-xs-12">
				   <div class="info-box">
					   <span class="info-box-icon bg-red"><i class="glyphicon glyphicon-export"></i></span>
					   <div class="info-box-content"> 
						   <?php
							$sql ="SELECT COUNT(*) as countOrder
										FROM send
										WHERE statusCode='P'					
										";
							switch($s_userGroupCode){
								case 'whOff' : case 'whSup' : case 'whMgr' :
									$sql .= " AND fromCode IN ('0','7','8','E') ";
									break;
								case 'pdOff' :  case 'pdSup' : 
									$sql .= " AND fromCode=:fromCode ";
									break;
								case 'pdMgr' : $sql .= " AND fromCode IN ('4','5','6') ";
									break;
								default : // it, admin
							}
							$stmt = $pdo->prepare($sql);
							switch($s_userGroupCode){
								case 'pdOff' :  case 'pdSup' : 
									$stmt->bindParam(':fromCode', $s_userDeptCode);
									break;
								default : // it, admin
							}							
							$stmt->execute();
							$row = $stmt->fetch();		
							?>
							<span class="info-box-text"> Sent </span>
						   <span class="info-box-number"><?= number_format($row['countOrder'], 0, '.', ','); ?> <small> Trans.</small>.</span>
						   
					   </div><!-- /.info-box-content -->
				   </div> <!-- /.info-box -->
				</div> <!-- /.col --> 
		</div> <!-- /.row -->   
	
	
	
		<!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-8">				
			<!-- Today LIST -->
			<?php
				echo "<h1>aaaaaaaaaaaaaaaa9999</h1>";
			?>
						
		<div class="box box-danger collapsed-box">
			<div class="box-header with-border">			  
				<h3 class="box-title" style="color: red;"> Prev. Delivery Sales Order.</h3>			  
			  
			  <div class="box-tools pull-right">
				
				<!--<span class="label label-danger"> items</span>-->
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
				</button>
			  </div>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<?php
					$sql = "
					SELECT hdr.`soNo`, hdr.`approveTime`,
					cust.name as custName,
					prd.code as prodCode
					 ,dtl.id as saleItemId, dtl.deliveryDate 
					, sum(dtl.qty) as sumQty 
					,IFNULL((SELECT sum(xd.qty) FROM picking xh 
							LEFT JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
							WHERE xh.statusCode='P' 
							AND xh.isFinish='N' 
							AND xh.soNo=hdr.soNo 
							AND xd.saleItemId=dtl.id
							GROUP BY xd.saleItemId),0) as sumPickedQty  
					,IFNULL((SELECT sum(xd.qty) FROM picking xh 
							LEFT JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
							WHERE xh.statusCode='P' 
							AND xh.isFinish='Y' 
							AND xh.soNo=hdr.soNo 
							AND xd.saleItemId=dtl.id
							GROUP BY xd.saleItemId),0) as sumSentQty 
					FROM `sale_header` hdr 
					INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo AND dtl.deliveryDate BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND '$yesterday' 
					INNER JOIN customer cust ON cust.id=hdr.custId 
					INNER JOIN product prd ON prd.id=dtl.prodId ";
					switch($s_userGroupCode){
						case 'pdOff' : case 'pdSup' :
							$sql .= " AND prd.catCode= CASE :toCode WHEN '4' THEN '70' WHEN '5' THEN '71' WHEN '6' THEN '72' END ";
							break;
						default : // it, admin
					}
					$sql.="
					WHERE 1=1
					AND hdr.statusCode='P' 
					";		
					//AND hdr.isClose='N' 
					$sql.="GROUP BY hdr.`soNo`, hdr.`approveTime`, dtl.`id`, dtl.`deliveryDate`, cust.name,prd.code ";
					$sql.="ORDER BY hdr.soNo, dtl.deliveryDate, prd.code";	
					$stmt = $pdo->prepare($sql);
					switch($s_userGroupCode){
						case 'pdOff' : case 'pdSup' :
							$stmt->bindParam(':toCode', $s_userDeptCode);
							break;
						default : // it, admin
					}						
					$stmt->execute();					
				?>
			 <div class="table-responsive">
                <table class="table no-margin" >
                  <thead>
                  <tr>
                    <th>SO No.</th>      
					<th>Update Time</th>                
                    <th>Customer</th>          
                    <th style="text-align: right;">Picked / Sent / Order</th>  
                  </tr>
                  </thead>
                  <tbody>
				   <?php 
				   if ( $stmt->rowCount()>0 ){
				   $soPrev=""; $itemStr=""; while ($row = $stmt->fetch()) { 
				   		$textColor='black';
				   		if ( $row['sumQty'] > $row['sumSentQty'] ) $textColor='red'; 
				   		if($soPrev<>$row['soNo']){ ?>
				   			<tr style="height: 50%; font-weight: bold;">
			                    <td><a href="sale2_view.php?soNo=<?=$row['soNo'];?>"  target="_blank" ><?= $row['soNo']; ?></a></td>
			                    <td style="text-align: center;"><?= date('d/M,H:i', strtotime($row['approveTime']) ); ?></td>
								<td colspan="2"><?= $row['custName']; ?></td>
			                </tr>
			                <tr style="font-size: small; height: 50%; color: <?=$textColor;?>;">	
			                	<td></td>	                		
								<td style="text-align: center;"><?= date('d/M', strtotime($row['deliveryDate']) ); ?></td>
								<td><?= $row['prodCode']; ?></td>
								<td style="text-align: right;"><?=number_format($row['sumPickedQty'],2,'.',','); ?> / <?=number_format($row['sumSentQty'],2,'.',','); ?> / <?=number_format($row['sumQty'],2,'.',','); ?></td>
			                </tr>
				   		<?php }else{ ?>
				   			<tr style="font-size: small; height: 50%; color: <?=$textColor;?>;">		
				   				<td></td>                		
								<td style="text-align: center;"><?= date('d/M', strtotime($row['deliveryDate']) ); ?></td>
								<td><?= $row['prodCode']; ?></td>
								<td style="text-align: right;"><?=number_format($row['sumPickedQty'],2,'.',','); ?> / <?=number_format($row['sumSentQty'],2,'.',','); ?> / <?=number_format($row['sumQty'],2,'.',','); ?></td>
			                </tr>
				   		<?php } ?>                  
                <?php  
                	$soPrev=$row['soNo'];
            	}  //end while
            }else{
            	echo '<tr><td colspan="6"> Not Found</td></tr>'; 
            }
            	?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
			  <!-- /.users-list -->
			</div>
			<!-- /.box-body -->
			<div class="box-footer text-center">
			</div>
			<!-- /.box-footer -->
		  </div>
		  <!--/.box -->


		

		<div class="box box-primary">
			<div class="box-header with-border">			  
				<h3 class="box-title"> Delivery Today Sales Order.</h3>			  
			  
			  <div class="box-tools pull-right">
				
				<!--<span class="label label-primary"> items</span>-->
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
				</button>
			  </div>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<?php
					$sql = "
					SELECT hdr.`soNo`, hdr.`approveTime`,
					cust.name as custName,
					prd.code as prodCode
					 ,dtl.id as saleItemId, dtl.deliveryDate 
					, sum(dtl.qty) as sumQty 
					,IFNULL((SELECT sum(xd.qty) FROM picking xh 
							LEFT JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
							WHERE xh.statusCode='P' 
							AND xh.isFinish='N' 
							AND xh.soNo=hdr.soNo 
							AND xd.saleItemId=dtl.id
							GROUP BY xd.saleItemId),0) as sumPickedQty  
					,IFNULL((SELECT sum(xd.qty) FROM picking xh 
							LEFT JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
							WHERE xh.statusCode='P' 
							AND xh.isFinish='Y' 
							AND xh.soNo=hdr.soNo 
							AND xd.saleItemId=dtl.id
							GROUP BY xd.saleItemId),0) as sumSentQty 
					FROM `sale_header` hdr 
					INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo AND dtl.deliveryDate='$today' 
					INNER JOIN customer cust ON cust.id=hdr.custId 
					INNER JOIN product prd ON prd.id=dtl.prodId ";
					switch($s_userGroupCode){
						case 'pdOff' : case 'pdSup' :
							$sql .= " AND prd.catCode= CASE :toCode WHEN '4' THEN '70' WHEN '5' THEN '71' WHEN '6' THEN '72' END ";
							break;
						default : // it, admin
					}
					$sql.="
					WHERE 1=1
					AND hdr.statusCode='P' 
					";		
					//AND hdr.isClose='N' 
					$sql.="GROUP BY hdr.`soNo`, hdr.`approveTime`, dtl.`id`, dtl.`deliveryDate`, cust.name,prd.code ";
					$sql.="ORDER BY hdr.soNo, dtl.deliveryDate, prd.code";	
					$stmt = $pdo->prepare($sql);
					switch($s_userGroupCode){
						case 'pdOff' : case 'pdSup' :
							$stmt->bindParam(':toCode', $s_userDeptCode);
							break;
						default : // it, admin
					}						
					$stmt->execute();	
				?>
			 <div class="table-responsive">
                <table class="table no-margin" >
                  <thead>
                  <tr>
                    <th>SO No.</th>      
					<th>Update Time</th>                 
                    <th>Customer</th>               
                    <th style="text-align: right;">Picked / Sent / Order</th>  
                  </tr>
                  </thead>
                  <tbody>
				   <?php 
				   if ( $stmt->rowCount()>0 ){
				   $soPrev=""; $itemStr=""; while ($row = $stmt->fetch()) { 
				   		$textColor='black';
				   		if ( $row['sumQty'] > $row['sumSentQty'] ) $textColor='red'; 
				   		if($soPrev<>$row['soNo']){ ?>
				   			<tr style="height: 50%; font-weight: bold;">
			                    <td><a href="sale2_view.php?soNo=<?=$row['soNo'];?>"  target="_blank" ><?= $row['soNo']; ?></a></td>
			                    <td style="text-align: center;"><?= date('d/M,H:i', strtotime($row['approveTime']) ); ?></td>
								<td colspan="2"><?= $row['custName']; ?></td>
			                </tr>
			                <tr style="font-size: small; height: 50%; color: <?=$textColor;?>;">	
			                	<td></td>	                		
								<td style="text-align: center;"><?= date('d/M', strtotime($row['deliveryDate']) ); ?></td>
								<td><?= $row['prodCode']; ?></td>
								<td style="text-align: right;"><?=number_format($row['sumPickedQty'],2,'.',','); ?> / <?=number_format($row['sumSentQty'],2,'.',','); ?> / <?=number_format($row['sumQty'],2,'.',','); ?></td>
			                </tr>
				   		<?php }else{ ?>
				   			<tr style="font-size: small; height: 50%; color: <?=$textColor;?>;">		
				   				<td></td>                		
								<td style="text-align: center;"><?= date('d/M', strtotime($row['deliveryDate']) ); ?></td>
								<td><?= $row['prodCode']; ?></td>
								<td style="text-align: right;"><?=number_format($row['sumPickedQty'],2,'.',','); ?> / <?=number_format($row['sumSentQty'],2,'.',','); ?> / <?=number_format($row['sumQty'],2,'.',','); ?></td>
			                </tr>
				   		<?php } ?>                
                <?php  
                	$soPrev=$row['soNo'];
            	}  //end while
            }else{
            	echo '<tr><td colspan="6"> Not Found</td></tr>'; 
            }
            	?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
			  <!-- /.users-list -->
			</div>
			<!-- /.box-body -->
			<div class="box-footer text-center">
			</div>
			<!-- /.box-footer -->
		  </div>
		  <!--/.box -->



		  <div class="box box-warning">
			<div class="box-header with-border">			  
				<h3 class="box-title"> Delivery In 3 Days Sales Order.</h3>			  
			  
			  <div class="box-tools pull-right">
				
				<!--<span class="label label-warning"> items</span>-->
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
				</button>
			  </div>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<?php
					$sql = "
					SELECT hdr.`soNo`, hdr.`approveTime`,
					cust.name as custName,
					prd.code as prodCode
					,dtl.id as saleItemId, dtl.deliveryDate 
					, sum(dtl.qty) as sumQty 
					,IFNULL((SELECT sum(xd.qty) FROM picking xh 
							LEFT JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
							WHERE xh.statusCode='P' 
							AND xh.isFinish='N' 
							AND xh.soNo=hdr.soNo 
							AND xd.saleItemId=dtl.id
							GROUP BY xd.saleItemId),0) as sumPickedQty  
					,IFNULL((SELECT sum(xd.qty) FROM picking xh 
							LEFT JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
							WHERE xh.statusCode='P' 
							AND xh.isFinish='Y' 
							AND xh.soNo=hdr.soNo 
							AND xd.saleItemId=dtl.id
							GROUP BY xd.saleItemId),0) as sumSentQty 
					FROM `sale_header` hdr 
					INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo AND dtl.deliveryDate BETWEEN '$nextDay' AND '$nextThreeDays' 
					INNER JOIN customer cust ON cust.id=hdr.custId 
					INNER JOIN product prd ON prd.id=dtl.prodId ";
					switch($s_userGroupCode){
						case 'pdOff' : case 'pdSup' :
							$sql .= " AND prd.catCode= CASE :toCode WHEN '4' THEN '70' WHEN '5' THEN '71' WHEN '6' THEN '72' END ";
							break;
						default : // it, admin
					}
					$sql.="
					WHERE 1=1
					AND hdr.statusCode='P' 
					";		
					//AND hdr.isClose='N' 
					$sql.="GROUP BY hdr.`soNo`, hdr.`approveTime`, dtl.`id`, dtl.`deliveryDate`, cust.name,prd.code ";
					$sql.="ORDER BY hdr.soNo, dtl.deliveryDate, prd.code";	
					$stmt = $pdo->prepare($sql);
					switch($s_userGroupCode){
						case 'pdOff' : case 'pdSup' :
							$stmt->bindParam(':toCode', $s_userDeptCode);
							break;
						default : // it, admin
					}						
					$stmt->execute();					
				?>				
			 <div class="table-responsive">
                <table class="table no-margin" >
                  <thead>
                  <tr>
                    <th>SO No.</th>      
					<th>Update Time</th>                 
                    <th>Customer</th>          
                    <th style="text-align: right;">Picked / Sent / Order</th>  
                  </tr>
                  </thead>
                  <tbody>
				   <?php 
				   if ( $stmt->rowCount()>0 ){
				   $soPrev=""; $itemStr=""; while ($row = $stmt->fetch()) { 
				   		$textColor='black';
				   		if ( $row['sumQty'] > $row['sumSentQty'] ) $textColor='red'; 
				   		if($soPrev<>$row['soNo']){ ?>
				   			<tr style="height: 50%; font-weight: bold;">
			                    <td><a href="sale2_view.php?soNo=<?=$row['soNo'];?>"  target="_blank" ><?= $row['soNo']; ?></a></td>
			                    <td style="text-align: center;"><?= date('d/M,H:i', strtotime($row['approveTime']) ); ?></td>
								<td colspan="2"><?= $row['custName']; ?></td>
			                </tr>
			                <tr style="font-size: small; height: 50%; color: <?=$textColor;?>;">	
			                	<td></td>	                		
								<td style="text-align: center;"><?= date('d/M', strtotime($row['deliveryDate']) ); ?></td>
								<td><?= $row['prodCode']; ?></td>
								<td style="text-align: right;"><?=number_format($row['sumPickedQty'],2,'.',','); ?> / <?=number_format($row['sumSentQty'],2,'.',','); ?> / <?=number_format($row['sumQty'],2,'.',','); ?></td>
			                </tr>
				   		<?php }else{ ?>
				   			<tr style="font-size: small; height: 50%; color: <?=$textColor;?>;">		
				   				<td></td>                		
								<td style="text-align: center;"><?= date('d/M', strtotime($row['deliveryDate']) ); ?></td>
								<td><?= $row['prodCode']; ?></td>
								<td style="text-align: right;"><?=number_format($row['sumPickedQty'],2,'.',','); ?> / <?=number_format($row['sumSentQty'],2,'.',','); ?> / <?=number_format($row['sumQty'],2,'.',','); ?></td>
			                </tr>
				   		<?php } ?>                
                <?php  
                	$soPrev=$row['soNo'];
            	}  //end while
            }else{
            	echo '<tr><td colspan="6"> Not Found</td></tr>'; 
            }
            	?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
			  <!-- /.users-list -->
			</div>
			<!-- /.box-body -->
			<div class="box-footer text-center">
			</div>
			<!-- /.box-footer -->
		  </div>
		  <!--/.box -->
         
		  
		  
		  <!-- TABLE: LATEST ORDERS -->		  
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Last Waiting For Receiving</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			<?php
					$sql = "SELECT a.`sdNo`, a.`sendDate`, a.`fromCode`, a.`toCode`, a.`statusCode`, a.`createTime`
							,fsl.name as fromSlocName
							,tsl.name as toSlocName
							FROM `send` a 
							LEFT JOIN `sloc` fsl on fsl.code=a.fromCode
							LEFT JOIN `sloc` tsl on tsl.code=a.toCode
							WHERE 1=1
							AND a.statusCode='P' 
							AND a.rcNo IS NULL 
							";
					switch($s_userGroupCode){
						case 'whOff' : case 'whSup' : case 'whMgr' : 
							$sql .="AND a.toCode IN ('0','7','8','E') ";
							break;
						case 'pdOff' : case 'pdSup' :
							$sql .="AND a.toCode=:toCode ";
							break;
						case 'pdMgr' : 
							$sql .= "AND a.toCode IN ('4','5','6') "; 
							break;
						default : // it, admin
					}	
					$sql .="ORDER BY a.`createTime` DESC
							LIMIT 10";
					$stmt = $pdo->prepare($sql);
					switch($s_userGroupCode){
						case 'pdOff' : case 'pdSup' :
							$stmt->bindParam(':toCode', $s_userDeptCode);
							break;
						default : // it, admin
					}							
					$stmt->execute();
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Send No.</th>
					<th>Send Date</th>
                    <th>From</th>
					<th>To</th>
                    <th>Create Time</th>
					<th>#</th>
                  </tr>
                  </thead>
                  <tbody>
				   <?php while ($row = $stmt->fetch()) { 
					?>
                  <tr>
                    <td><a href="send2_view.php?sdNo=<?=$row['sdNo'];?>" target="_blank" ><?= $row['sdNo']; ?></a></td>
					<td><?= $row['sendDate']; ?></td>
					<td><?= $row['fromSlocName']; ?></td>
					<td><?= $row['toSlocName']; ?></td>
					<td><?= $row['createTime']; ?></td>
					<td>
						<a href="receive_add.php?sdNo=<?=$row['sdNo'];?>" class="btn btn-primary">
							<i class="glyphicon glyphicon-download-alt"></i>
						</a>						
					</td>
                </tr>
                <?php  } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="receive_add.php" class="btn btn-sm btn-info btn-flat pull-left">Place New Receiving</a>
              <a href="receive.php" class="btn btn-sm btn-default btn-flat pull-right">View All Receiving</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
		  
		  	  
		  
		  
          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Latest Send</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			<?php
					$sql = "SELECT a.`sdNo`, a.`sendDate`, a.`fromCode`, a.`toCode`, a.`statusCode`, a.`createTime`
							,fsl.name as fromSlocName
							,tsl.name as toSlocName
							FROM `send` a 
							LEFT JOIN `sloc` fsl on fsl.code=a.fromCode
							LEFT JOIN `sloc` tsl on tsl.code=a.toCode
							WHERE 1=1
							AND a.statusCode='P' 
							";
					switch($s_userGroupCode){
						case 'whOff' : case 'whSup' : case 'whMgr' : 
							$sql .="AND a.fromCode IN ('0','7','8','E') ";
							break;
						case 'pdOff' : case 'pdSup' :
							$sql .="AND a.fromCode=:fromCode ";
							break;
						case 'pdMgr' : 
							$sql .= "AND a.fromCode IN ('4','5','6') "; 
							break;
						default : // it, admin
					}	
					$sql .="ORDER BY a.`createTime` DESC
							LIMIT 10";
					$stmt = $pdo->prepare($sql);
					switch($s_userGroupCode){
						case 'pdOff' : case 'pdSup' :
							$stmt->bindParam(':fromCode', $s_userDeptCode);
							break;
						default : // it, admin
					}							
					$stmt->execute();
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Send No.</th>
					<th>Send Date</th>
                    <th>From</th>
					<th>To</th>
                    <th>Status</th>
                    <th>Create Time</th>
                  </tr>
                  </thead>
                  <tbody>
				   <?php while ($row = $stmt->fetch()) { 
					$statusName = '<label class="label label-info">Being</label>';
					switch($row['statusCode']){
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						default : 						
					}
					?>
                  <tr>
                    <td><a href="send2_view.php?sdNo=<?=$row['sdNo'];?>" ><?= $row['sdNo']; ?></a></td>
					<td><?= $row['sendDate']; ?></td>
					<td><?= $row['fromSlocName']; ?></td>
					<td><?= $row['toSlocName']; ?></td>
					<td><?=$statusName;?></td>
					<td><?= $row['createTime']; ?></td>
                </tr>
                <?php  } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="send2_hdr.php" class="btn btn-sm btn-info btn-flat pull-left">Place New Sending</a>
              <a href="send2.php" class="btn btn-sm btn-default btn-flat pull-right">View All Sending</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->

		
		<!-- TABLE: LATEST ORDERS -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Latest Receiving</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			<?php
					$sql = "SELECT a.`rcNo`, a.`receiveDate`, a.`fromCode`, a.`toCode`, a.`statusCode`, a.`createTime`
							,fsl.name as fromSlocName
							,tsl.name as toSlocName
							FROM `receive` a 
							LEFT JOIN `sloc` fsl on fsl.code=a.fromCode
							LEFT JOIN `sloc` tsl on tsl.code=a.toCode
							WHERE 1=1 
							AND a.statusCode='P' 
							";
					switch($s_userGroupCode){
						case 'whOff' : case 'whSup' : case 'whMgr' : 
							$sql .="AND a.toCode IN ('0','7','8','E') ";
							break;
						case 'pdOff' : case 'pdSup' :
							$sql .="AND a.toCode=:toCode ";
							break;
						case 'pdMgr' : 
							$sql .="AND a.toCode IN ('4','5','6') ";
							break;
						default : // it, admin
					}	
					$sql .="ORDER BY a.`createTime` DESC
							LIMIT 10";
					$stmt = $pdo->prepare($sql);
					switch($s_userGroupCode){
						case 'pdOff' : case 'pdSup' :
							$stmt->bindParam(':toCode', $s_userDeptCode);
							break;
						default : // it, admin
					}							
					$stmt->execute();				
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Receive No.</th>
					<th>Recieve Date</th>
                    <th>From</th>
					<th>To</th>
                    <th>Status</th>
                    <th>Create Time</th>
                  </tr>
                  </thead>
                  <tbody>
				   <?php while ($row = $stmt->fetch()) { 
					$statusName = '<label class="label label-info">Being</label>';
					switch($row['statusCode']){
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						default : 						
					}
					?>
                  <tr>
                    <td><a href="receive_view.php?rcNo=<?=$row['rcNo'];?>" ><?= $row['rcNo']; ?></a></td>
					<td><?= $row['receiveDate']; ?></td>
					<td><?= $row['fromSlocName']; ?></td>
					<td><?= $row['toSlocName']; ?></td>
					<td><?=$statusName;?></td>
					<td><?= $row['createTime']; ?></td>
                </tr>
                <?php  } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Receive</a>
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Receive</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
		
		
		
		
		
		
		
        <div class="col-md-4">		
		
          <!-- TOP 10 PRODUCT LIST -->
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Sales Order Pending</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<?php	
					$sql="";
					switch($s_userGroupCode){
						case 'admin' :
						case 'whOff' :
						case 'whSup' :
						case 'whMgr' :
						case 'pdMgr' :
						case 'acc' : 
							$sql = "SELECT DISTINCT hdr.soNo, hdr.deliveryDate FROM sale_header hdr 
							INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo
							WHERE hdr.isClose='N'  
							AND hdr.statusCode='P' 
							ORDER BY hdr.`createTime` DESC
							LIMIT 10
							";
							break;
						case 'pdOff' : 
						case 'pdSup' : 
							switch($s_userDeptCode){
								case '4' :
									$sql = "SELECT  DISTINCT hdr.soNo, hdr.deliveryDate FROM sale_header hdr 
									INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo
										AND dtl.prodId IN (SELECT id FROM product prd WHERE catCode='70')								
									WHERE hdr.isClose=0 
									AND hdr.statusCode='P' 
									ORDER BY hdr.`createTime` DESC
									LIMIT 10
									";
									break;
								case '5' :
									$sql = "SELECT  DISTINCT hdr.soNo, hdr.deliveryDate FROM sale_header hdr 
									INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo
										AND dtl.prodId IN (SELECT id FROM product prd WHERE catCode='71')								
									WHERE hdr.isClose=0 
									AND hdr.statusCode='P' 
									ORDER BY hdr.`createTime` DESC
									LIMIT 10
									";
									break;
								case '6' :
									$sql = "SELECT  DISTINCT hdr.soNo, hdr.deliveryDate FROM sale_header hdr 
									INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo
										AND dtl.prodId IN (SELECT id FROM product prd WHERE catCode='72')								
									WHERE hdr.isClose=0 
									AND hdr.statusCode='P' 
									ORDER BY hdr.`createTime` DESC
									LIMIT 10
									";
									break;
								default : 
									$sql = "SELECT DISTINCT hdr.soNo, hdr.deliveryDate FROM sale_header hdr 
									INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo
									WHERE hdr.isClose='N'  
									AND hdr.statusCode='P' 
									ORDER BY hdr.`createTime` DESC
									LIMIT 10
									";
							}
							break;
						default : // it, admin
					}	
					$stmt = $pdo->prepare($sql);
					$stmt->execute();						
				?>
		
				<div class="table-responsive">
				<table class="table no-margin">
				  <thead>
				  <tr>
					<th>No.</th>
					<th>SO No.</th>
					<th>Delivery Date</th>
				  </tr>
				  </thead>
				  <tbody>
				  <?php $row_code = 1; while ($row = $stmt->fetch()) { 
				  	$dt = new DateTime($row['deliveryDate']); 
				  	$dtStr = $dt->format('d M Y');
				  	?>
				  <tr>
					<td>
						 <?= $row_code; ?>
					</td>
					<td>
						 <a target="_blank" href="sale2_view.php?soNo=<?= $row['soNo'];?>" ><?= $row['soNo']; ?></a>
					</td>
					<td>
						 <?= $dtStr; ?>
					</td>
				</tr>
				<?php $row_code+=1; } ?>
				  </tbody>
				</table>
				</div>
				<!--/.table-responsive-->
			</div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
              <a href="sales_order_pending.php" class="uppercase">View All Pending Sales Order</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
		  
		  <?php switch($s_userGroupCode){
				case 'admin' :
				case 'whOff' :
				case 'whSup' :
				case 'whMgr' :
				case 'acc' : ?>
			  <!-- Prepare List -->
			  <div class="box box-success">
				<div class="box-header with-border">
				  <h3 class="box-title">Prepare Pending</h3>

				  <div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				  </div>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<?php						
					$sql = "SELECT DISTINCT hdr.ppNo, pick.soNo 
					FROM prepare hdr 
					INNER JOIN prepare_detail dtl on dtl.ppNo=hdr.ppNo
					INNER JOIN picking pick ON pick.pickNo=hdr.pickNo
					WHERE 1=1 
					AND hdr.statusCode='P' 
					ORDER BY hdr.`createTime` DESC 
					LIMIT 10
					";
					$stmt = $pdo->prepare($sql);
					$stmt->execute();						
					?>		
					<div class="table-responsive">
					<table class="table no-margin">
					  <thead>
					  <tr>
						<th>No.</th>
						<th>PP No.</th>
						<th>SO No.</th>
						<th>#</th>
					  </tr>
					  </thead>
					  <tbody>
					  <?php $row_code = 1; while ($row = $stmt->fetch()) { ?>
					  <tr>
						<td>
							 <?= $row_code; ?>
						</td>					
						<td>
							 <a target="_blank" href="prepare_view.php?ppNo=<?= $row['ppNo'];?>" ><?= $row['ppNo']; ?></a>
						</td>
						<td>
							 <a target="_blank" href="sale2_view.php?soNo=<?= $row['soNo'];?>" ><?= $row['soNo']; ?></a>
						</td>
						<td>
							<a href="delivery_add.php?ppNo=<?=$row['ppNo'];?>" class="btn btn-primary">
								<i class="glyphicon glyphicon-download-alt"></i>
							</a>						
						</td>
					</tr>
					<?php $row_code+=1; } ?>
					  </tbody>
					</table>
					</div>
					<!--/.table-responsive-->
				</div>
				<!-- /.box-body -->
				<div class="box-footer text-center">
				  <a href="#" class="uppercase">View All Prepare</a>
				</div>
				<!-- /.box-footer -->
			  </div>
			  <!-- /.box -->
		  <?php break;
				default : // it, admin
			}	?>
		  
		  
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row second box col8 & col 4 -->
	  
	  
	  
	
	</section>
	<!--sec.content-->

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

</body>
</html>
