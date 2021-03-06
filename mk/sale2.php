<?php
	//include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 
?>
 
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	
  


<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   <?php
		$rootPage="sale2";
   ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">	  
	  <h1><i class="glyphicon glyphicon-shopping-cart"></i>
       Sales Order
        <small>Sales Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Sales Order List</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<div class="form-inline">
				<label class="box-title">Sales Order List</label>
				<a href="<?=$rootPage;?>_add.php?soNo=" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Add Sales Order</a>
			</div>
		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
				$sqlRole = "";
				switch($s_userGroupCode){
					case 'sales' : //$sqlRole = " and b.smId=$s_smId "; break;
					case 'salesAdmin' : //$sqlRole = " and b.smAdmId=$s_smId "; break;
					default : 
				}
				$search_word="";
				$sqlSearch = "";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sqlSearch = "and (a.soNo like '%".$_GET['search_word']."%' OR  b.name like '%".$_GET['search_word']."%' OR  c.name like '%".$_GET['search_word']."%') ";
				}
				$sqlCond = "";
				
                $sql_so = "
							SELECT COUNT(*) AS countTotal 
							FROM `sale_header` a
							left join customer b on a.custId=b.id
							left join salesman c on a.smId=c.id
							left join user d on a.createById=d.userId
							WHERE 1 
							".$sqlSearch." 
							".$sqlCond." 
							".$sqlRole." 
							AND a.statusCode<>'X' 
							";
                $result = mysqli_query($link, $sql_so);
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
						<form id="form1" action="<?=$rootPage;?>.php" method="get" class="form" novalidate>
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
						SELECT a.`soNo`, a.`saleDate`, a.`custId`, a.`smId`, a.`createTime`, a.`createById`, a.isClose, a.statusCode , a.revCount
						,b.code as custCode, b.name as custName, b.tel as custTel, b.fax as custFax
						,c.name as smName
						,d.userFullname as createByName
						,(SELECT IFNULL(count(*),0) FROM sale_detail b WHERE b.soNo=a.soNo) as countItem
						FROM `sale_header` a
						left join customer b on a.custId=b.id
						left join salesman c on a.smId=c.id
						left join user d on a.createById=d.userId
						WHERE 1 
							".$sqlSearch." 
							".$sqlCond." 
							".$sqlRole." 
						AND a.statusCode<>'X' 
						
						ORDER BY  FIELD(a.statusCode,'B','C','P'), a.createTime DESC
						LIMIT $start, $rows 
				";
				//echo $sql;
                $result = mysqli_query($link, $sql);
           ?> 
            
            <table class="table table-striped">
                <tr>
                    <th>SO No.</th>
					<th>SO DATE</th>
                    <th>Customer Name</th>
					<th>Salesman Name</th>
					<th>Closed</th>
					<th>Status</th>
					<th>#</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { 
					$statusName = '<label class="label label-danger">Unknown</label>';
					switch($row['statusCode']){
						case 'A' : $statusName = '<label class="label label-danger">Incomplete</label>'; break;
						case 'B' : $statusName = '<label class="label label-info">Begin</label>'; break;
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						default : 						
					}
					$isCloseName = '<label class="label label-danger">No</label>';
					switch($row['isClose']){
						case 'Y' : $isCloseName = '<label class="label label-success">Yes</label>'; break;
						default : 						
					}
					$revisedName = ($row['revCount']<>0?'<a href="'.$rootPage.'Revised.php?soNo='.$row['soNo'].'" /><small style="color: red;"> rev.'.$row['revCount'].'</small></a>':'');
					?>
					
                <tr>
                    <td>
                         <?= $row['soNo'].$revisedName; ?>
                    </td>
                    <td>
                         <?= date('d M Y',strtotime($row['saleDate'])); ?>
                    </td>
                    <td>
                         <?= $row['custName']; ?>
                    </td>
					<td>
                         <?= $row['smName']; ?>
                    </td>
					<td>
                         <?= $isCloseName; ?>
                    </td>
					<td>
                         <?= $statusName; ?>
                    </td>		
					<td>					
						<a class="btn btn-default" name="btn_row_search" 
							href="<?=$rootPage;?>_view.php?soNo=<?=$row['soNo'];?>" 
							data-toggle="tooltip" title="View"><i class="glyphicon glyphicon-search"></i> View</a>						
						
					<?php
						switch($row['statusCode']) {
							case 'C' : case 'P' : ?>
								<a class="btn btn-default" name="btn_row_edit" href="#" disabled=disabled ><i class="fa fa-edit"></i> Edit</a>

						<?php 
								break;
							default :  ?>
								<a class="btn btn-default" name="btn_row_edit" href="<?=$rootPage;?>_add.php?soNo=<?=$row['soNo'];?>" title="Edit Sales Order" ><i class="fa fa-edit"></i> Edit</a> 
						<?php 
								break;
						} ?>
												
                    </td>
                </tr>
                <?php } ?>
            </table>
			
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


	
});
</script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
