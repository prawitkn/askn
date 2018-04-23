<?php
	include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 
/*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$rootPage="invoice";		
?>
<?php
	$sqlRole = "";
	switch($s_userGroupCode){
		case 'sales' :
			$sqlRole = " AND ct.smCode='$s_smCode' ";
			break;
		case 'salesAdmin' :
			$sqlRole = " AND ct.smAdmCode='$s_smCode' ";
			break;
		default :
	}
?>

<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">
       <h1><i class="glyphicon glyphicon-usd"></i>
       Invoice
        <small>Invoice management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Invoice List</a></li>		
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
		<div class="form-inline">
			<label class="box-title">Invoice List</label>
			<a href="<?=$rootPage;?>_add.php?invNo=" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Add Invoice</a>
		</div>
		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
				$search_word="";
				$sqlSearch = "";
				$url="invoice.php";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sqlSearch = "and (b.custName like '%".$_GET['search_word']."%' OR  c.name like '%".$_GET['search_word']."%') ";
				}
                $sql = "
							SELECT COUNT(a.invNo) AS countTotal
							FROM `invoice_header` a
							INNER join customer ct on a.custCode=ct.code
								".$sqlRole." 
							left join salesman c on a.smCode=c.code
							WHERE 1 							
							".$sqlSearch." 
							
							AND a.statusCode<>'X' 
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
								<label for="search_word">Customer Name Or Salesman Name search key word.</label>
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
                $sql = "
						SELECT a.`invNo`, a.`doNo`, a.`refNo`, a.`invoiceDate`, a.`custCode`, a.`smCode`, a.`totalExcVat`, a.`vatAmount`, a.`totalIncVat`
						, a.`remark`, a.`statusCode`
						, ct.custName, ct.custAddr, ct.custTel, ct.custFax,
						c.name as smName
						FROM `invoice_header` a
						left join customer ct on a.custCode=ct.code
							".$sqlRole." 
						left join salesman c on a.smCode=c.code
						WHERE 1 
							".$sqlSearch." 
						AND a.statusCode<>'X' 
						
						ORDER BY a.createTime DESC
						LIMIT $start, $rows 
				";
				//echo $sql;
                $result = mysqli_query($link, $sql);
           ?> 
            <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>Inv No.</th>
					<th>Ref No.</th>
					<th>DO No.</th>
					<th>Invoice Date</th>
                    <th>Customer Name</th>
					<th>Salesman Name</th>
					<th>Status</th>
					<th>#</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) {
 
					$statusName = '<label class="label label-danger">Unknown</label>';
					switch($row['statusCode']){
						case 'A' : $statusName = '<label class="label label-warning">Incomplete</label>'; break;
						case 'B' : $statusName = '<label class="label label-primary">Begin</label>'; break;
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						default : 						
					}
					?>
                <tr>
					<td>
                         <?= $row['invNo']; ?>
                    </td>
					<td>
                         <?= $row['refNo']; ?>
                    </td>
                    <td>
                         <?= $row['doNo']; ?>
                    </td>
                    <td>
                         <?= to_thai_date_fdt($row['invoiceDate']); ?>
                    </td>
                    <td>
                         <?= $row['custName']; ?>
                    </td>
					<td>
                         <?= $row['smName']; ?>
                    </td>
					<td>
                         <?= $statusName; ?>
                    </td>	
					<td>					
						<a class="btn btn-info " name="btn_row_search" 
							href="invoice_view.php?invNo=<?=$row['invNo'];?>" 
							data-toggle="tooltip" title="Search"><i class="glyphicon glyphicon-search"></i></a>
						<a class="btn btn-success" name="btn_row_edit" 
							<?php echo ($row['statusCode']=='B'?'href="invoice_add.php?invNo='.$row['invNo'].'"':' disabled '); ?> 
							data-toggle="tooltip" title="Edit" ><i class="glyphicon glyphicon-edit"></i></a>													
                    </td>
                </tr>
                <?php } ?>
            </table>
			</div>
			<!--tabl-response-->
			
			<nav>
			<ul class="pagination">
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="<?=$url;?>?search_word=<?= $search_word;?>&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="<?=$url;?>?search_word=<?= $search_word;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="<?=$url;?>?search_word=<?= $search_word;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
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
	    
});
</script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
