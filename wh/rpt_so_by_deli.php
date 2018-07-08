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
											
                $sql = "SELECT DISTINCT sh.soNO, sh.createTime 
				FROM `sale_header` sh
				INNER JOIN sale_detail sd ON sd.soNo=sh.soNo 
				INNER JOIN customer ct on ct.id=sh.custId ";
				$sql .= "LEFT JOIN salesman sm on sm.id=sh.smId 
				WHERE 1 
				AND sh.statusCode='P' 
				AND sh.isClose<>'Y' ";
				if($dateFromYmd<>""){ $sql .= " AND sd.deliveryDate='$dateFromYmd' ";	}				
                //$result = mysqli_query($link, $sql);
                //$countTotal = mysqli_fetch_assoc($result);
				 //$countTotal = mysqli_row_count($result);
				$stmt = $pdo->prepare($sql);	
				//$stmt->bindParam(':soNo', $hdr['soNo']);
				$stmt->execute();
				//$row = $stmt->fetch();
				$countTotal = $stmt->rowCount();
				
				$rows=20;
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
                $sql = "SELECT DISTINCT sh.`soNo`, sh.`poNo`, sh.`saleDate`, sh.`custId`
				, ct.name as custName
				, sh.`smId`
				, sm.name as smName 
				, sh.`statusCode`, sh.`isClose` 
				, sd.deliveryDate 
				FROM `sale_header` sh
				INNER JOIN sale_detail sd ON sd.soNo=sh.soNo 
				INNER JOIN customer ct on ct.id=sh.custId ";
				$sql .= "LEFT JOIN salesman sm on sm.id=sh.smId 
				WHERE 1 
				AND sh.statusCode='P' 
				AND sh.isClose<>'Y' ";
				if($dateFromYmd<>""){ $sql .= " AND sd.deliveryDate='$dateFromYmd' ";	}
				$sql .= "ORDER BY soNo desc
				LIMIT $start, $rows 
				";
                $result = mysqli_query($link, $sql);                
           ?>             
			
				<div class="table-responsive">
				<table class="table table-striped">
				<thead>
				<tr>
					<th>No.</th>
					<th>SO No.</th>
					<th>Sales Date</th>
					<th>Customer</th>
					<th>Salesman</th>
					<th>Status</th>
					<th>Is Closed</th>
					<th>#</th>
				</tr>
				</thead>
				<tbody>
                <?php $c_row=($start+1); while ($row = mysqli_fetch_assoc($result)) { 
					$statusName = '<label class="label label-danger">Unknown</label>';
					switch($row['statusCode']){
						case 'B' : $statusName = '<label class="label label-info">Begin</label>'; break;
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						default : 						
					}
					$isCloseName = '<label class="label label-danger">Unknown</label>';
					switch($row['isClose']){
						case 'N' : $isCloseName = '<label class="label label-warning">No</label>'; break;
						case 'Y' : $isCloseName = '<label class="label label-success">Yes</label>'; break;
						default : 						
					}
					?>
					<tr>
						<td><?=$c_row;?></td>
						<td><a target="_blank" href="sale_view.php?soNo=<?=$row['soNo'];?>" ><?=$row['soNo'];?></a></td>
						<td><?=$row['saleDate'];?></td>
						<td><?=$row['custName'];?></td>
						<td><?=$row['smName'];?></td>
						<td><?=$statusName;?></td>
						<td><?=$isCloseName;?></td>
						<td>#</td>
					</tr>
                <?php $c_row +=1; } ?>
				</tbody>
				</table>
				</div>
				<!--table-resposive-->
		
		<div class="col-md-12">
			<?php $pagingString = "?dateFrom=".$dateFrom;
			?>
				
			<a href="<?="rpt_so_by_deli_hdr_xls.php".$pagingString;?>" class="btn btn-default pull-right" aria-label=".CSV"><span aria-hidden="true">
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
