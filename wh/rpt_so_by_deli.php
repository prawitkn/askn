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
?>   
 
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	
   
<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php';   
	
	$rootPage="rpt_so_by_deli";
  ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		Report
        <small>Report</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Report</a></li>
        <li class="active">Sales Order by Delivery Date Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Sales Order by Delivery Date Report</h3>		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
				$dateFrom = (isset($_GET['dateFrom'])?$_GET['dateFrom']: date('d-m-Y') );

				$dateFrom = str_replace('/', '-', $dateFrom);
				$dateFromYmd=$dateToYmd="";
				if($dateFrom<>""){ $dateFromYmd = date('Y-m-d', strtotime($dateFrom));	}
				
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
					INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo AND dtl.deliveryDate='$dateFromYmd' 
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
            
				$countTotal = $stmt->rowCount();
				
				$rows=100;
				$page=0;
				if( !empty($_GET["page"]) and isset($_GET["page"]) ) $page=$_GET["page"];
				if($page<=0) $page=1;
				$total_data=$countTotal['countTotal'];
				$total_page=ceil($total_data/$rows);
				if($page>=$total_page) $page=$total_page;
				$start=($page-1)*$rows;
				if($page==0) $start=0;
          ?>
		  
          <span class="label label-primary">Total <?php echo $countTotal; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<div class="row" style="margin-bottom: 5px;">
			<!--<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active" ><a href="#divSearch" toggle="tab">Search</a></li>
					<li class="" ><a href="#divResult" toggle="tab">Result</a></li>
				</ul>
				
				<div class="tab-content clearfix">
				<div class="tab-pane active" id="divSearch">a
				</div>
				<div class="tab-pane" id="divResult">b
				</div>
				</div>
				
			</div>-->
			<div class="col-md-12">					
                    <form id="form1" action="<?=$rootPage;?>.php" method="get" class="form-inline" novalidate>
						<label for="dateFrom">Delivery Date : </label>
						<input type="text" id="dateFrom" name="dateFrom" value="" class="form-control datepicker" data-smk-msg="Require From Date." required >										
						<input type="submit" class="btn btn-default" value="ค้นหา">
                    </form>
                </div>    
			</div>
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
					INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo AND dtl.deliveryDate='$dateFromYmd' 
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
					$sql.="ORDER BY hdr.soNo, dtl.deliveryDate, prd.code ";	
					$sql.="LIMIT $start, $rows ";
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
				<!--table-resposive-->
		
		<div class="col-md-12">
			<?php $pagingString = "?dateFrom=".$dateFrom;
			?>
				
			<a target="_blank" href="<?="rpt_so_by_deli_hdr_xls.php".$pagingString;?>" class="btn btn-default pull-right" aria-label=".CSV"><span aria-hidden="true">
				<i class="glyphicon glyphicon-save-file"></i> Excel</span></a>
			
			<nav>
			<ul class="pagination">
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage.'.php.'.$pagingString;?>&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="<?=$rootPage.'.php.'.$pagingString;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage.'.php.'.$pagingString;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
				</li>
			</ul>
			</nav>
			
		<div>
			
        </div><!-- /.box-body -->
  <div class="box-footer">
      
      
    <!--The footer of the box -->
  </div><!-- box-footer -->
</div><!-- /.box -->

	<div id="spin"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>  
  
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<!--Deprecation Notice: The jqXHR.success(), jqXHR.error(), and jqXHR.complete() callbacks are removed as of jQuery 3.0. 
    You can use jqXHR.done(), jqXHR.fail(), and jqXHR.always() instead.-->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- smoke validate -->
<script src="bootstrap/js/smoke.min.js"></script>
<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>


<script> 		
$(document).ready(function() {    
	//.ajaxStart inside $(document).ready to start and stop spiner.  
	$( document ).ajaxStart(function() {
		$("#spin").show();
	}).ajaxStop(function() {
		$("#spin").hide();
	});
	//.ajaxStart inside $(document).ready END
	
	$("#title").focus();
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
	
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
		});  //กำหนดเป็นวันปัจุบัน
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($dateFromYmd<>"") { ?>
			var queryDate = '<?=$dateFromYmd?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#dateFrom').datepicker('setDate', realDate);
		<?php }else{ ?> $('#dateFrom').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
	});
</script>





</body>
</html>
