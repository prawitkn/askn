<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php 
	include 'head.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
	
	$rootPage="closingStk";

	$id = (isset($_GET['id']) ?$_GET['id']:'');
?>    

<style type="text/css">

</style>
<script type="text/javascript">

	
</script>
 
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	
  
<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php'; 
  
  ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-warning"></i>
       Closing Stock
        <small>Transaction management</small>
      </h1>
     

     <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>_list.php"><i class="fa fa-list"></i>Closing Stock List</a></li>
		<li><a href="#"><i class="fa fa-edit"></i>Closing Stock View</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
		<?php
		$sql = "SELECT hdr.id, hdr.closingDate, hdr.createTime, hdr.createUserId, cu.userFullname as createUserFullname 
			FROM `stk_closing` hdr
			LEFT JOIN wh_user cu ON cu.userId=hdr.createUserId 
			WHERE 1=1
			AND hdr.id=:id  ";
			$stmt = $pdo->prepare($sql);				
			$stmt->bindParam(':id', $id);	
			$stmt->execute();
			$hdr=$stmt->fetch();
		?>
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Closing Stock View </h3>		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php

			$sql = "SELECT hdr.id, hdr.closingDate, hdr.createTime
			, dtl.id, dtl.sloc, dtl.prodId, dtl.balance 
			, prd.code as prodCode
			FROM `stk_closing` hdr
			INNER JOIN `stk_closing_detail` dtl ON dtl.hdrId=hdr.id 
			LEFT JOIN  `product` prd ON prd.id=dtl.prodId 
			WHERE 1=1
			AND hdr.id=:id 
			ORDER BY dtl.sloc, prd.code ";
			$stmt = $pdo->prepare($sql);				
			$stmt->bindParam(':id', $id);	
			$stmt->execute();
			$countTotal = $stmt->rowCount();

			//We've got this far without an exception, so commit the changes.
			//$pdo->commit();	
				
			$rows=100;
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
                    <form id="form1" action="#" method="get" class="form-inline"  style="background-color: gray; padding: 5px;"  novalidate>

                    	<label for="dateFrom">Closing Stock Date : </label>
						<?=$hdr['closingDate'];?>
						<br/>	
						<label for="createUserFullname">Create By : </label>
						<?=$hdr['createUserFullname'];?>
                    </form>

                </div>   
				<!--col-md-12-->
				
			</div>
			<!--row-->
			
			<div class="table-responsive">
				<table id="table-1" border=1 class="table table-hover no-margin" style="table-layout: fixed;">
                  <thead>
                  <tr class="header">
					<th style="width: 30px; text-align: center;">No.</th>
                    <th style="width: 200px; text-align: center;">Product Code</th>
					<th style="width: 30px; text-align: center;">Loc.</th>
					<!--<th style="width: 100px; text-align: center; color: green;">Available</th>-->
					<th style="width: 80px; text-align: center; color: blue; ">Balance</th>		
                  </tr>
                  </thead>
                  <tbody>
				<?php 
				if($countTotal==0){ 
				?>
					<tr>
						<td colspan="9" style="text-align: center; color: red; font-weight: bold;" >Data not found.</td>
					</tr>
		       	<?php }else{ 
		       		//there data.
  					$sql = "SELECT hdr.id, hdr.closingDate, hdr.createTime
					, dtl.id, dtl.sloc, dtl.prodId, dtl.balance 
					, prd.code as prodCode
					FROM `stk_closing` hdr
					INNER JOIN `stk_closing_detail` dtl ON dtl.hdrId=hdr.id 
					LEFT JOIN  `product` prd ON prd.id=dtl.prodId 
					WHERE 1=1
					AND hdr.id=:id ";
					$sql.="ORDER BY dtl.sloc, prd.code  ";
					$sql.="LIMIT $start, $rows ";
					$stmt = $pdo->prepare($sql);					
					$stmt->bindParam(':id', $id);	
					$stmt->execute();

		       		$c_row=($start+1); while ($row = $stmt->fetch() ) { 
							//$img = 'dist/img/product/'.(empty($row['photo'])? 'default.jpg' : $row['photo']);
		       			// $isNotEqual=false;
		       			// $bgColor="";
		       			// if ( $row['balance']<>$row['balanceReCheck'] ){
		       			// 	$isNotEqual=true;
		       			// 	$bgColor="bg-danger";
		       			// }
					?>
                  <tr>
					<td style="text-align: center;"><?= $c_row; ?></td>
                    <td style="text-align: center; width: 200px;"><?= $row['prodCode']; ?></td>
					<td style="text-align: center;"><?= $row['sloc']; ?></td>
					<td style="text-align: right; color: blue;"><?= number_format($row['balance'],2,'.',','); ?>					
					</td>
                </tr>
                 <?php 
                 	$c_row +=1; }//end while					
				?>   

		       <?php }//if count total ?   ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
			  
			<?php $condQuery="?id=".$id; ?>

             <?php if($countTotal>0){    ?>
               <div class="col-md-12">
			<!-- <a target="_blank" href="<?=$rootPage;?>_xls.php<?=$condQuery;?>" class="btn btn-default pull-right"><i class="glyphicon glyphicon-print"></i> Export</a>
 -->
			<!-- <a target="_blank" href="<?=$rootPage;?>_stmt_pdf.php<?=$condQuery;?>" class="btn btn-default pull-right"><i class="fa fa-file-pdf-o"></i> Stock movement report</a> -->
			
			<nav>
			<ul class="pagination">				
				
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage;?>_view.php<?=$condQuery;?>.&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="<?=$rootPage;?>_view.php<?=$condQuery;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage;?>_view.php<?=$condQuery;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
				</li>
			</ul>
			</nav>
			</div>
		<?php } ?>

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


	
	
	$("#btnSubmit").click(function(){ 
		$('#form1').submit();
	});

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
			var queryDate = '<?=$dateFromYmd;?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#dateFrom').datepicker('setDate', realDate);
		<?php }else{ ?> $('#dateFrom').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($dateToYmd<>"") { ?>
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
