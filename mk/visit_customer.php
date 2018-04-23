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
      <h1>
      Visit Customers
        <small>Visit customer management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Menu</a></li>
        <li class="active">Visit Customer Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      <a href="visit_customer_add.php" class="btn btn-google">Add Visit Customers</a>
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Visit Customer List</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
		  <?php
				$search_word="";
				$sqlSearch = "";
				$url="visit_customer.php";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sqlSearch = "and (c.custName like '%".$_GET['search_word']."%' ) ";
				}
                $sql = "
					select COUNT(*) AS countTotal
					from visit_customer a
					left join salesman b on a.smCode=b.code
					left join customer c on a.custCode=c.code
					left join user d on a.createByID=d.userID
					WHERE 1 "
					.$sqlSearch." 
					
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
          ?>
          <span class="label label-primary">Total <?= $countTotal['countTotal']; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<div class="row">
				<div class="col-md-6">				
						<form id="form1" action="<?=$url;?>" method="get" class="form" novalidate>
						<div class="form-group">
							<label for="search_word">Customer Name search key word.</label>
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
			<!-- /.row-->
           <?php
                $sql = "
						select a.`id`, a.`visitNo`, a.`smCode`, a.`custCode`, a.`visitDate`, a.`custContactName`, 
						a.`custContactTelNo`, a.`createTime`, a.`createByID`, a.`statusCode`,
						b.name as smName,
						c.custName,
						d.userFullname as createByName
						from visit_customer a
						left join salesman b on a.smCode=b.code
						left join customer c on a.custCode=c.code
						left join user d on a.createByID=d.userID
						WHERE 1 "
						.$sqlSearch." 
						
						ORDER BY a.createTime DESC
						LIMIT $start, $rows 
				";
                $result = mysqli_query($link, $sql);
                
           ?> 
            
            <table class="table table-striped">
                <tr>
                    <th>Visit No.</th>
					<th>Visit Date</th>
                    <th>Customer Name</th>
					<th>Salesman Name</th>
                    <th>Create Time</th>
					<th>Create By</th>
                    <th>#</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td>
                         <?= $row['visitNo']; ?>
                    </td>
					<td>
                         <?= to_thai_date($row['visitDate']); ?>
                    </td>
                    <td>
                         <?= $row['custName']; ?>
                    </td>
                    <td>
                         <?= $row['smName']; ?>
                    </td>
                    <td>
                         <?= to_thai_datetime_fdt($row['createTime']); ?>
                    </td>
					<td>
                         <?= $row['createByName']; ?>
                    </td>
                    <td>
						<a class="btn btn-info fa fa-search" name="btn_row_search" href="visit_customer_view.php?id=<?=$row['id'];?>&visitNo=<?=$row['visitNo'];?>" target="_blank" ></a>
						<a class="btn btn-success fa fa-edit" name="btn_row_edit" <?php echo ($row['statusCode']=='A'?'href="visit_customer_edit.php?id='.$row['id'].'&visitNo='.$row['visitNo'].'"':"disabled"); ?> ></a>						
						<a class="btn btn-danger fa fa-trash" name="btn_row_del" data-id="<?= $row['id']; ?>" ></a>	
                    </td>
                </tr>
                <?php } ?>
            </table>
			
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
	$('a[name=btn_row_del]').click(function(){
		var params = {
			id: $(this).attr('data-id')
		};
		if(params.id==''){
			$.smkAlert({
				text: 'This data cant delete',
				type: 'danger',
				position:'top-center'
			});
			return false;
		}
		//alert(params.id);
		$.smkConfirm({text:'Are you sure to delete ?',accept:'Sure, delete data!', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: 'visit_customer_del_ajax.php',
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

</body>
</html>

