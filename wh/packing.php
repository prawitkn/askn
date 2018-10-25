<?php
    include '../db/database.php';
	include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>
    
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
      Packing
        <small>Packing management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Menu</a></li>
        <li class="active">Packing Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here
      <a href="order_add.php" class="btn btn-google">Add Sales Orders</a> -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Sales Order List</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
				$search_word="";
				$sqlSearch = "";
				$url="order.php";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sqlSearch = "and (b.custName like '%".$_GET['search_word']."%' OR  c.name like '%".$_GET['search_word']."%') ";
				}
				$sqlCond = "";
				switch($s_userGroupCode){
					case 'salesAdmin' : 
						break;
					case 'sales' : 
						
						break;
					default :
				}
                $sql_so = "
							SELECT COUNT(*) AS countTotal 
							FROM `order_header` a
							left join customer b on a.custCode=b.code
							left join salesman c on a.smCode=c.code
							left join user d on a.createByID=d.userID
							WHERE 1 
							".$sqlSearch." 
							".$sqlCond." 
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
						SELECT a.`id`, a.`orderNo`, a.`orderDate`, a.`custCode`, a.`smCode`, a.`createTime`, a.`createByID`, a.statusCode ,
						b.custName, b.custAddr, b.custTel, b.custFax,
						c.name as smName,
						d.userFullname as createByName,
						(SELECT IFNULL(count(*),0) FROM order_detail b WHERE b.orderNo=a.orderNo) as countItem
						FROM `order_header` a
						left join customer b on a.custCode=b.code
						left join salesman c on a.smCode=c.code
						left join user d on a.createByID=d.userID
						WHERE 1 
							".$sqlSearch." 
							".$sqlCond." 
						AND a.statusCode<>'X' 
						
						ORDER BY a.createTime DESC
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
					<th>Status</th>
                    <th>Create Time</th>
                    <th>Create By</th>
					<th>Items Count</th>
					<th>#</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) {
 
					$statusName = '<label class="label label-info">Being</label>';
					switch($row['statusCode']){
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						default : 						
					}
					?>
                <tr>
                    <td>
                         <?= $row['orderNo']; ?>
                    </td>
                    <td>
                         <?= to_thai_date_fdt($row['orderDate']); ?>
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
                         <?= to_thai_datetime_fdt($row['createTime']); ?>
                    </td>
					<td>
                         <?= $row['createByName']; ?>
                    </td>
					<td>
                         <?= $row['countItem']; ?>
                    </td>			
					<td>					
						<a class="btn btn-info fa fa-search" name="btn_row_search" href="order_view.php?id=<?=$row['id'];?>&orderNo=<?=$row['orderNo'];?>" target="_blank" ></a>
						<a class="btn btn-success fa fa-edit" name="btn_row_edit" <?php echo ($row['statusCode']=='B'?'href="order_edit.php?id='.$row['id'].'&orderNo='.$row['orderNo'].'"':"disabled"); ?> ></a>						
						<a class="btn btn-success fa fa-plus" name="btn_row_item" <?php echo ($row['statusCode']=='B'?'href="order_item.php?id='.$row['id'].'&orderNo='.$row['orderNo'].'"':"disabled"); ?> ></a>
						<a class="btn btn-danger fa fa-trash" name="btn_row_remove" data-id="<?php echo ($row['statusCode']=='P'?'':$row['id']); ?>" <?php echo ($row['statusCode']=='P'?'disabled':''); ?> ></a>	
                    </td>
                </tr>
                <?php } ?>
            </table>
			
			<nav>
			<ul class="pagination">
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="<?=$url.php;?>?search_word=<?= $search_word;?>&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="<?=$url.php;?>?search_word=<?= $search_word;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="<?=$url.php;?>?search_word=<?= $search_word;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
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
	$('a[name=btn_row_remove]').click(function(){
		var params = {
			id: $(this).attr('data-id')
		};
		if(params.id==''){
			$.smkAlert({
				text: 'ข้อมูลรายการนี้ ไม่สามารถลบได้',
				type: 'danger',
				position:'top-center'
			});
			return false;
		}
		//alert(params.id);
		$.smkConfirm({text:'คุณแน่ใจที่จะยกเลิกรายการนี้ใช่หรือไม่ ?',accept:'ยกเลิกรายการ', cancel:'ไม่ยกเลิกรายการ'}, function (e){if(e){
			$.post({
				url: 'order_remove_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function (data) {					
				if (data.success){ 
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});
					location.reload();
				} else {
					alert(data.message);
					$.smkAlert({
						text: data.message,
						type: 'danger'//,
					//                        position:'top-center'
					});
				}
			}).error(function (response) {
				alert(response.responseText);
			}); 
		}});
		e.preventDefault();
	});
});
</script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
