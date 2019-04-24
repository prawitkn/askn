
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>	<!-- head.php included session.php! -->

</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	

<div class="wrapper">

  <!-- Main Header -->
  <?php 
include 'header.php'; 
$rootPage = 'shelf';
$tb = 'wh_shelf';
//Check user roll.
switch($s_userGroupCode){
	case 'admin' : case 'whOff' : case 'whSup' : 
		break;
	default :  
		header('Location: access_denied.php');
		exit();
}
  
  ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">
		<h1><i class="glyphicon glyphicon-wrench"></i>
       Shelf
        <small>Shelf management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Shelf List</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

<!-- To allow only admin to access the content -->      
    <div class="box box-primary">
        <div class="box-header with-border">
		<label class="box-title">Shelf List</label>
			<a href="<?=$rootPage;?>_add.php?id=" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Add Shelf</a>
		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
			$search_word="";
			if(isset($_GET['search_word']) and isset($_GET['search_word'])){
				$search_word=$_GET['search_word'];
			}	
			$sql = "
			SELECT COUNT(*) AS countTotal 
			FROM `".$tb."`  
			WHERE 1=1 ";
			if(isset($_GET['search_word']) and isset($_GET['search_word'])){
				$search_word=$_GET['search_word'];
				$sql .= "and (code like '%".$_GET['search_word']."%' ) ";
			}	
			//$sql .= "ORDER BY code ASC ";
			$stmt = $pdo->prepare($sql);	
			$stmt->execute();	
			$row=$stmt->fetch();
			$countTotal = $row['countTotal'];
			
			$rows=20;
			$page=0;
			if( !empty($_GET["page"]) and isset($_GET["page"]) ) $page=$_GET["page"];
			if($page<=0) $page=1;
			$total_data=$countTotal;
			$total_page=ceil($total_data/$rows);
			if($page>=$total_page) $page=$total_page;
			$start=($page-1)*$rows;			                
          ?>
          <span class="label label-primary">Total <?php echo $countTotal; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<div class="row">
				<div class="col-md-6">					
					<form id="form1" action="<?=$rootPage;?>.php" method="get" class="form" novalidate>
						<div class="form-group">
							<label for="search_word">Shelf name search key word.</label>
							<div class="input-group">
								<input id="search_word" type="text" class="form-control" name="search_word" data-smk-msg="Require userFullname."required>
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-search"></span>
								</span>
							</div>
						</div>						
						<input type="submit" class="btn btn-default" value="Search">
					</form>
				</div>  
				<!--/.col-md-->
			</div>
			<!--/.row-->
           <?php
			$sql = "
			SELECT a.`id`, a.`code`, a.`name`, a.`statusCode`
			FROM `".$tb."` a 
			LEFT JOIN wh_sloc_x x ON x.id=a.xId 
			LEFT JOIN wh_sloc_y y ON y.id=a.yId 
			LEFT JOIN wh_sloc_z z ON z.id=a.zId  
			WHERE 1=1 ";
			if(isset($_GET['search_word']) and isset($_GET['search_word'])){
				$search_word=$_GET['search_word'];
				$sql .= "and (code like '%".$_GET['search_word']."%' ) ";
			}	
			$sql .= "ORDER BY x.code, CAST(y.code AS UNSIGNED), CAST(z.code AS UNSIGNED) ASC ";
			$sql.="LIMIT $start, $rows ";		
			//$result = mysqli_query($link, $sql);
			$stmt = $pdo->prepare($sql);	
			$stmt->execute();	
                
           ?> 
            <div class="table-responsive">
            <table class="table table-striped">
                <tr>
					<th style="text-align: center;">No.</th>	
					<th style="text-align: center;">Shelf Code</th>
                    <th style="text-align: center;">Shelf Name</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
                <?php $c_row=($start+1); while ($row = $stmt->fetch()) { 
					$statusName = '<label class="label label-info">Unknow</label>';
						switch($row['statusCode']){
							case 'A' : $statusName = '<label class="label label-success">Active</label>'; break;
							case 'I' : $statusName = '<label class="label label-danger">Inactive</label>'; break;
							default : 						
						}
						?>
                <tr>
					<td style="text-align: center;">
                         <?= $c_row; ?>
                    </td>	
                    <td style="text-align: center;">
                         <?= $row['code']; ?>
                    </td>
                    <td style="text-align: center;">
                         <?= $row['name']; ?>
                    </td>				
                    <td style="text-align: center;">
						 <?php
						 switch($row['statusCode']){ 	
							case 'A' :
								echo '<a class="btn btn-success" name="btn_row_setActive" data-statusCode="I" data-id="'.$row['id'].'" >Active</a>';
								break;
							case 'I' :
								echo '<a class="btn btn-default" name="btn_row_setActive" data-statusCode="A" data-id="'.$row['id'].'" >Inactive</a>';
								break;
							case 'X' : 
								echo '<label style="color: red;" >Removed</label>';
								break;
							default :	
								echo '<label style="color: red;" >N/A</label>';
						}
						 ?>
                    </td>					
                    <td style="text-align: center;">
						<?php
						 switch($row['statusCode']){ 	
							case 'A' :
								echo '<a class="btn btn-primary" name="btn_row_edit" href="'.$rootPage.'_edit.php?act=edit&id='.$row['id'].'" >
								<i class="glyphicon glyphicon-edit"></i> Edit</a>';
								break;
							case 'I' :
								echo '<a class="btn btn-danger" name="btn_row_remove"  data-id="'.$row['id'].'" > 
								<i class="glyphicon glyphicon-remove"></i> Remove</a>	';
								break;
							case 'X' : 
								if($s_userGroupCode=="admin"){
									echo '<a class="btn btn-danger" name="btn_row_delete"  data-id="'.$row['id'].'" > 
								<i class="glyphicon glyphicon-trash"></i> Delete</a>';
								}
								break;
							default :	
								echo '';
						} //end switch ?>
                    </td>
                </tr>
                <?php $c_row+=1; } ?>
            </table>
			</div>
				
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

<script src="bootstrap/js/smoke.min.js"></script>
<script>
$(document).ready(function() {
	$('a[name=btn_row_setActive]').click(function(){
		var params = {
			action: 'setActive',
			id: $(this).attr('data-id'),
			statusCode: $(this).attr('data-statusCode')			
		};
		$.smkConfirm({text:'Are you sure ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
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
	//end btn_row_setActive
	
	$('a[name=btn_row_remove]').click(function(){
		var params = {
			action: 'remove',
			id: $(this).attr('data-id')
		};
		$.smkConfirm({text:'Are you sure to Remove ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
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
	//end btn_row_remove
	
	$('a[name=btn_row_delete]').click(function(){
		var params = {
			action: 'delete',
			id: $(this).attr('data-id')
		};
		$.smkConfirm({text:'Are you sure to Delete ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
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
	//end btn_row_delete
});
</script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
