<?php 
	include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php 
	include 'head.php'; 
	
	$today=date('Y-m-d');
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
        <small><?php echo $s_userFullname; ?> [ ID: <?php echo $_SESSION['userId']; ?>] </small>
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
								case 'wh' :
								case 'pd' : 
									$sql .= " AND toCode=:toCode ";
									break;
								default : // it, admin
							}
							$stmt = $pdo->prepare($sql);
							switch($s_userGroupCode){
								case 'wh' :
								case 'pd' : 
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
								case 'wh' :
								case 'pd' : 
									$sql .= " AND fromCode=:fromCode ";
									break;
								default : // it, admin
							}
							$stmt = $pdo->prepare($sql);
							switch($s_userGroupCode){
								case 'wh' :
								case 'pd' : 
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
										FROM send
										WHERE statusCode='P'					
										";
							switch($s_userGroupCode){
								case 'wh' :
								case 'pd' : 
									$sql .= " AND fromCode=:fromCode ";
									break;
								default : // it, admin
							}
							$stmt = $pdo->prepare($sql);
							switch($s_userGroupCode){
								case 'wh' :
								case 'pd' : 
									$stmt->bindParam(':fromCode', $s_userDeptCode);
									break;
								default : // it, admin
							}							
							$stmt->execute();
							$row = $stmt->fetch();		
							?>
						   <span class="info-box-text"> Sending </span>
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
										FROM receive
										WHERE statusCode='P'					
										";
							switch($s_userGroupCode){
								case 'wh' :
								case 'pd' : 
									$sql .= " AND toCode=:toCode ";
									break;
								default : // it, admin
							}
							$stmt = $pdo->prepare($sql);
							switch($s_userGroupCode){
								case 'wh' :
								case 'pd' : 
									$stmt->bindParam(':toCode', $s_userDeptCode);
									break;
								default : // it, admin
							}							
							$stmt->execute();
							$row = $stmt->fetch();		
							?>
							<span class="info-box-text"> Receiving </span>
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
				$sql = "
				SELECT COUNT(*) as countTotal 
				FROM `sale_header` hdr 
				INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo AND dtl.deliveryDate='$tomorrow'
				WHERE 1=1
				AND hdr.statusCode='P' 
				AND hdr.isClose='N' 
				";
				$result = mysqli_query($link, $sql);
				$row = mysqli_fetch_assoc($result);
			?>
			
		  <div class="box box-danger">
			<div class="box-header with-border">			  
				<h3 class="box-title">Sales Order Delivery Date in <b style="color: red;"><?=$tomorrowStr;?></b></h3>			  
			  
			  <div class="box-tools pull-right">
				
				<span class="label label-danger"><?= $row['countTotal']; ?> items</span>
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
					SELECT hdr.`soNo`, hdr.`saleDate`
					FROM `sale_header` hdr 
					INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo AND dtl.deliveryDate='$tomorrow'
					WHERE 1=1
					AND hdr.statusCode='P' 
					AND hdr.isClose='N' 
					";
					$sql.="LIMIT 10 ";
					$result = mysqli_query($link, $sql);
					
				?>
			 <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>SO No.</th>
					<th>Sales Date</th>
					<th>#</th>
                  </tr>
                  </thead>
                  <tbody>
				   <?php while ($row = mysqli_fetch_assoc($result)) { 
					?>
                  <tr>
                    <td><a href="sale_view_pdf.php?soNo=<?=$row['soNo'];?>" ><?= $row['soNo']; ?></a></td>
					<td><?= $row['saleDate']; ?></td>
					<td><a href="deliver_add.php?doNo=&soNo=<?=$row['soNo'];?>" >...</a></td>
                </tr>
                <?php  } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
			  <!-- /.users-list -->
			</div>
			<!-- /.box-body -->
			<div class="box-footer text-center">
			  <a href="#" class="uppercase">View All Sales</a>
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
							";
					switch($s_userGroupCode){
						case 'whOff' : case 'whSup' :
						case 'pdOff' : case 'pdSup' :
							$sql .="AND a.toCode=:toCode ";
							break;
						default : // it, admin
					}	
					$sql .="ORDER BY a.`createTime` DESC
							LIMIT 10";
					$stmt = $pdo->prepare($sql);
					switch($s_userGroupCode){
						case 'whOff' : case 'whSup' :
							$stmt->bindParam(':toCode', $s_userDeptCode);
							break;
						case 'pdOff' : case 'pdSup' :
							$stmt->bindParam(':toCode', $s_userDeptCode);
							break;
						default : // it, admin
					}							
					$stmt->execute();
					$row = $stmt->fetch();	
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
                    <td><a href="send_view.php?sdNo=<?=$row['sdNo'];?>" ><?= $row['sdNo']; ?></a></td>
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
						case 'whOff' : case 'whSup' :
						case 'pdOff' : case 'pdSup' :
							$sql .="AND a.fromCode=:fromCode ";
							break;
						default : // it, admin
					}	
					$sql .="ORDER BY a.`createTime` DESC
							LIMIT 10";
					$stmt = $pdo->prepare($sql);
					switch($s_userGroupCode){
						case 'whOff' : case 'whSup' :
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
                    <td><a href="send_view.php?sdNo=<?=$row['sdNo'];?>" ><?= $row['sdNo']; ?></a></td>
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
              <a href="send_add.php" class="btn btn-sm btn-info btn-flat pull-left">Place New Sending</a>
              <a href="send.php" class="btn btn-sm btn-default btn-flat pull-right">View All Sending</a>
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
						case 'whOff' : case 'whSup' :
						case 'pdOff' : case 'pdSup' :
							$sql .="AND a.toCode=:toCode ";
							break;
						default : // it, admin
					}	
					$sql .="ORDER BY a.`createTime` DESC
							LIMIT 10";
					$stmt = $pdo->prepare($sql);
					switch($s_userGroupCode){
						case 'whOff' : case 'whSup' :
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
					
					switch($s_userGroupCode){
						case 'it' :
						case 'admin' :
						case 'whOff' :
						case 'whSup' :
							$sql = "SELECT * FROM sale_header hdr 
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
									$sql = "SELECT * FROM sale_header hdr 
									INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo
										AND dtl.prodCode IN (SELECT prodCode FROM product prd WHERE prodCatCode='70')								
									WHERE hdr.isClose=0 
									AND hdr.statusCode='P' 
									ORDER BY hdr.`createTime` DESC
									LIMIT 10
									";
									break;
								case '5' :
									$sql = "SELECT * FROM sale_header hdr 
									INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo
										AND dtl.prodCode IN (SELECT prodCode FROM product prd WHERE prodCatCode='71')								
									WHERE hdr.isClose=0 
									AND hdr.statusCode='P' 
									ORDER BY hdr.`createTime` DESC
									LIMIT 10
									";
									break;
								case '6' :
									$sql = "SELECT * FROM sale_header hdr 
									INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo
										AND dtl.prodCode IN (SELECT prodCode FROM product prd WHERE prodCatCode='72')								
									WHERE hdr.isClose=0 
									AND hdr.statusCode='P' 
									ORDER BY hdr.`createTime` DESC
									LIMIT 10
									";
									break;
								default : 
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
				  <?php $row_code = 1; while ($row = $stmt->fetch()) { ?>
				  <tr>
					<td>
						 <?= $row_code; ?>
					</td>
					<td>
						 <a target="_blank" href="sale_view_pdf.php?soNo=<?= $row['soNo'];?>" ><?= $row['soNo']; ?></a>
					</td>
					<td>
						 <?= substr($row['deliveryDate'],0,10); ?>
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
              <a href="#" class="uppercase">View All Sales Order</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
		  
		  
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row second box col8 & col 4 -->
	  
	  
	  
	
	</section>
	<!--sec.content-->
	
	</div>
	<!--content-wrapper-->

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
