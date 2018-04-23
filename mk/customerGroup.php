<?php
    include '../db/database.php';
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
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php';  
		include 'inc_helper.php';  
	?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Customer Group
        <small>Customer Group management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Menu</a></li>
        <li class="active">Customer Group Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

<!-- To allow only admin to access the content -->        
        <?php 
            if ($s_userGroupCode != 'admin') {
                echo 'You are not permitted to access this level.';
       
            } else {
 // Closing Bracket of Else is on line 143                
?>
        
        
      <!-- Your Page Content Here -->
      <a href="#" class="btn btn-google">Add Customer Group</a>
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Customer Group List</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php				
				$search_word="";
				$sqlSearch = "";
				$url="customerGroup.php";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sqlSearch = "and (name like '%".$_GET['search_word']."%' ) ";
				}
                $sql = "
							SELECT COUNT(*) AS countTotal 							
							FROM `customer_group` 
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
          <span class="label label-primary">Total <?php echo $countTotal['countTotal']; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<div class="row">
				<div class="col-md-6">					
					<form id="form1" action="<?=$url;?>" method="get" class="form" novalidate>
						<div class="form-group">
							<label for="search_word">Name search key word.</label>
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
				<!--/.col-md-->
			</div>
			<!--/.row-->
           <?php
				$sql = "
						SELECT `id`, `code`, `name`, `statusCode`, `createTime`, `createById`, `updateTime`, `updateById`, `deleteTime`, `deleteById` 
						FROM `customer_group`
						WHERE 1 "
						.$sqlSearch." 
						
						ORDER BY id ASC
						LIMIT $start, $rows 
				";		
                //$result = mysqli_query($link, $sql);
				$stmt = $pdo->prepare($sql);	
				$stmt->execute();	
                
           ?> 
            
            <table class="table table-striped">
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>#</th>
                </tr>
                <?php while ($row = $stmt->fetch()) { 
					$statusName = '<label class="label label-info">Unknow</label>';
						switch($row['statusCode']){
							case 'A' : $statusName = '<label class="label label-success">Active</label>'; break;
							case 'X' : $statusName = '<label class="label label-danger">Inactive</label>'; break;
							default : 						
						}
						?>
                <tr>
                    <td>
                         <?= $row['id']; ?>
                    </td>
                    <td>
                         <?= $row['code']; ?>
                    </td>
                    <td>
                         <?= $row['name']; ?>
                    </td>
                    <td>
                         <?=$statusName; ?>
                    </td>
					<td>					
						<a class="btn btn-success fa fa-edit" name="btn_row_edit" <?php echo ($row['statusCode']=='A'?' href="user_edit.php?id='.$row['id'].'" ':' disabled '); ?> ></a>						
						<a class="btn btn-danger fa fa-remove" name="btn_row_remove" <?php echo ($row['statusCode']=='A'?' data-id="'.$row['id'].'" ':' disabled '); ?> ></a>	
						<a class="btn btn-danger fa fa-trash" name="btn_row_delete" <?php echo ($row['statusCode']=='X'?' data-id="'.$row['id'].'" ':' disabled '); ?> ></a>	
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
			<?=to_mysql_date('01/02/2561');?>
    </div><!-- /.box-body -->
  <div class="box-footer">
      
      
    <!--The footer of the box -->
  </div><!-- box-footer -->
</div><!-- /.box -->

  <!-- Closing of above If/Else to access the content about line # 62-65.   -->
          <?php } ?>

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

<script src="bootstrap/js/smoke.min.js"></script>
<script>
$(document).ready(function() {
	$("a[name=btn_row_delete]").click(function(e) {
	  var row_id = $(this).attr('data-id');
	  $.smkConfirm({text:'Are you sure you want to delete?',accept:'OK Sure.', cancel:'Do not Delete.'}, function (e){if(e){
			  window.location.replace('user_del.php?id='+row_id);
	  }});
	  e.preventDefault();
	});
	
	$("a[name=btn_row_remove]").click(function(e) {
	  var row_id = $(this).attr('data-id');
	  $.smkConfirm({text:'Are you sure you want to remove?',accept:'OK Sure.', cancel:'Do not remove.'}, function (e){if(e){
			  window.location.replace('user_remove.php?id='+row_id);
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

