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
	
	$rootPage="report_sale_pending_by_prod";
  ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		Sales Order Pending by Product Report
        <small>Sales Report</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Sales Order Pending by Product Report</h3>		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
				$dateFrom = (isset($_GET['dateFrom'])?$_GET['dateFrom']:'');
				$dateTo = (isset($_GET['dateTo'])?$_GET['dateTo']:'');
				
				$dateFromYmd=$dateToYmd="";
				if($dateFrom<>""){ $dateFromYmd = to_mysql_date($_GET['dateFrom']);	}
				if($dateFrom<>""){ $dateToYmd = to_mysql_date($_GET['dateTo']);	}
				
				
											
                $sql = "SELECT hdr.soNo, hdr.deliveryDate
				, dtl.prodId, prd.code as prodCode
				, sum(dtl.qty) as sumQty
				, (SELECT IFNULL(sum(doDtl.qty),0) FROM delivery_header doHdr
					INNER JOIN delivery_detail doDtl ON doDtl.doNo=doHdr.doNo
					INNER JOIN product_item itm ON itm.prodItemId=doDtl.prodItemId 
					WHERE 1=1
					AND doHdr.soNo=hdr.soNo
					AND itm.prodId=dtl.prodId) as sumSentDtl
				FROM `sale_header` hdr
				INNER JOIN sale_detail dtl ON dtl.soNo=hdr.soNo
				LEFT JOIN product prd ON prd.id=dtl.prodId ";				
				
				$sql .= "WHERE 1 
				AND hdr.statusCode='P' 
				AND hdr.isClose='N' ";
				if($dateFrom<>""){ $sql .= " AND hdr.saleDate>='$dateFromYmd' ";	}
				if($dateTo<>""){ $sql .= " AND hdr.saleDate<='$dateToYmd' ";	}				
				$sql .= "
				group by hdr.soNo, dtl.prodId, dtl.deliveryDate  ";
				
                $result = mysqli_query($link, $sql);
                $countTotal = mysqli_num_rows($result);
				
				$rows=5;
				$page=0;
				if( !empty($_GET["page"]) and isset($_GET["page"]) ) $page=$_GET["page"];
				if($page<=0) $page=1;
				$total_data=$countTotal;
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
						<label for="dateFrom">Date From : </label>
						<input type="text" id="dateFrom" name="dateFrom" value="" class="form-control datepicker" data-smk-msg="Require From Date." required >
						<label for="dateTo">Date To : </label>
						<input type="text" id="dateTo" name="dateTo" value="" class="form-control datepicker" data-smk-msg="Require To Date." required >											
						<input type="submit" class="btn btn-default" value="ค้นหา">
                    </form>
                </div>    
			</div>
           <?php
                $sql = "SELECT hdr.soNo, hdr.deliveryDate
				, dtl.prodId, prd.code as prodCode
				, sum(dtl.qty) as sumQty
				, (SELECT IFNULL(sum(doDtl.qty),0) FROM delivery_header doHdr
					INNER JOIN delivery_detail doDtl ON doDtl.doNo=doHdr.doNo
					INNER JOIN product_item itm ON itm.prodItemId=doDtl.prodItemId 
					WHERE 1=1
					AND doHdr.soNo=hdr.soNo
					AND itm.prodId=dtl.prodId) as sumSentDtl
				FROM `sale_header` hdr
				INNER JOIN sale_detail dtl ON dtl.soNo=hdr.soNo
				LEFT JOIN product prd ON prd.id=dtl.prodId ";				
				
				$sql .= "WHERE 1 
				AND hdr.statusCode='P' 
				AND hdr.isClose='N' ";
				if($dateFrom<>""){ $sql .= " AND hdr.saleDate>='$dateFromYmd' ";	}
				if($dateTo<>""){ $sql .= " AND hdr.saleDate<='$dateToYmd' ";	}				
				$sql .= "
				group by hdr.soNo, dtl.prodId, hdr.deliveryDate ";
				$sql.="
				ORDER BY soNo desc
				LIMIT $start, $rows 
				";
                $result = mysqli_query($link, $sql);                
           ?>             
			
			<div class="table-responsive">
				<table class="table table-striped">
				<thead>
				<tr>
					<th>No.</th>
					<th>Product Code</th>
					<th>SO No.</th>
					<th>Delivery Date</th>
					<th>Order Qty</th>
					<th>Sent Qty</th>
					<th>Pending Qty</th>
				</tr>
				</thead>
				<tbody>
                <?php $c_row=($start+1); while ($row = mysqli_fetch_assoc($result)) { 
					$pendingQty=$row['sumQty']-$row['sumSentDtl'];
					?>
					<tr>
						<td><?=$c_row;?></td>
						<td><?=$row['prodCode'];?></td>
						<td><a href="sale_view.php?soNo=<?=$row['soNo'];?>"><?=$row['soNo'];?></a></td>
						<td><?=to_thai_date($row['deliveryDate']);?></td>-
						<td style="text-align: right;"><?=number_format($row['sumQty'],0,'.',',');?></td>
						<td style="text-align: right;"><?=number_format($row['sumSentDtl'],0,'.',',');?></td>
						<td style="text-align: right; color: red;"><?=number_format($pendingQty,0,'.',',');?></td>
					</tr>
                <?php $c_row +=1; } ?>
				</tbody>
				</table>
				</div>
				<!--table-resposive-->
		
		<div class="col-md-12">
			<?php $pagingString = "?dateFrom=".$dateFrom."&dateTo=".$dateTo;
			?>
		
			<a href="<?=$rootPage."_xls.php".$pagingString;?>" class="btn btn-default pull-right" aria-label=".CSV"><span aria-hidden="true">
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
			language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: true              //Set เป็นปี พ.ศ.
		});  //กำหนดเป็นวันปัจุบัน
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($dateFrom<>"") { ?>
			var queryDate = '<?=$dateFromYmd;?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#dateFrom').datepicker('setDate', realDate);
		<?php }else{ ?> $('#dateFrom').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($dateTo<>"") { ?>
			var queryDate = '<?=$dateToYmd;?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#dateTo').datepicker('setDate', realDate);
		<?php }else{ ?> $('#dateTo').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		
	});
</script>





</body>
</html>
