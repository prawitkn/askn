
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 

//Check user roll.
switch($s_userGroupCode){
	case 'admin' : 
		break;
	case 'pdSup' :
		if ( $s_userDeptCode == 'T' ){
			//break;
		}else{			
			header('Location: access_denied.php');
			exit();
		}
		break;
	default : 
		header('Location: access_denied.php');
		exit();
}
?>	<!-- head.php included session.php! -->    
 
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	
  
<div class="wrapper">

  <!-- Main Header -->
  <?php 
include 'header.php'; 

$rootPage = 'product_roll_length';
$tb = 'product_roll_length';

//Check user roll.
$isRollOk=false;
switch($s_userGroupCode){
	case 'admin' : $isRollOk=true;
		break;
	case 'pdSup' : 
		if ( $s_userDeptCode == 'T' ) { $isRollOk=true; }
	default :
}
if ( !$isRollOk ) {  
	include 'access_denied2.php';
	exit();
}
//End check user roll.

?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">
		<h1><i class="fa fa-archive"></i>
       Product Roll Length
        <small>Product Roll Length management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Product Roll Length List</a></li>
		<!--<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Product Roll Length</a></li>-->
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

<!-- To allow only admin to access the content -->      
    <div class="box box-primary">
        <div class="box-header with-border">
		<label class="box-title">Product Roll Length List</label>
			<a href="<?=$rootPage;?>_add.php?id=" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Add Product Roll Length</a>
		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
			$search_word="";
			if(isset($_GET['search_word']) and isset($_GET['search_word'])){
				$search_word=trim($_GET['search_word']);
			}	
			$sql = "
			SELECT COUNT(h.id) AS countTotal 
			FROM ".$tb." h 
			INNER JOIN product prod ON prod.id=h.prodId  
			WHERE 1 ";
			if($search_word<>""){				
				$sql .= "and (prod.code like '%".$search_word."%') ";
			}	
			//echo $sql;
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
          <span class="label label-primary">Total <?php echo $total_data; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<div class="row">
				<div class="col-md-6">					
					<form id="form1" action="<?=$rootPage;?>.php" method="get" class="form" novalidate>
						<div class="form-group">
							<label for="search_word">Product Code search key word.</label>
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
			SELECT h.`prodId`, h.`id`, h.`name`
			, h.`statusCode`, h.`createTime`, h.`createById`
			, prod.code as prodCode  
			FROM ".$tb." h 
			INNER JOIN product prod ON prod.id=h.prodId  
			WHERE 1 ";
			if($search_word<>""){				
				$sql .= "and (prod.code like '%".$search_word."%') ";
			}	
			$sql .= "ORDER BY h.createTime DESC  ";
			$sql.="LIMIT $start, $rows ";		
			//$result = mysqli_query($link, $sql);
			$stmt = $pdo->prepare($sql);	
			$stmt->execute();	
                
           ?> 
            <div class="table-responsive">
            <table class="table table-striped">
               <tr>
                    <th>No.</th>
					<th>Product Code</th>
					<th>Roll Length</th>
					<th>Status</th>
                    <th>#</th>
                </tr>
                <?php $c_row=($start+1); while ($row = $stmt->fetch()) { 					
						?>
                <tr>
                    <td>
                         <?= $c_row; ?>
                    </td>
					<td>
                         <?= $row['prodCode']; ?>
                    </td>  	 		
					<td>
                         <?= $row['name']; ?>
                    </td> 
					<td>
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
                    <td>
						
						<?php if($row['statusCode']=='A' OR ($s_userGroupCode=='it' OR $s_userGroupCode=='prog')){ ?>
							<a class="btn btn-primary" name="btn_row_edit" href="<?=$rootPage;?>_edit.php?act=edit&id=<?= $row['id']; ?>" >
								<i class="glyphicon glyphicon-edit"></i> Edit</a>	
						<?php }else{ ?>	
							<a class="btn btn-primary"  disabled  > 
								<i class="glyphicon glyphicon-edit"></i> Edit</a>	
						<?php } ?>
						
						<?php if($row['statusCode']=='I'){ ?>
							<a class="btn btn-danger" name="btn_row_remove"  data-id="<?=$row['id'];?>" > 
								<i class="glyphicon glyphicon-remove"></i> Remove</a>	
						<?php }else{ ?>	
							<a class="btn btn-danger"  disabled  >
								<i class="glyphicon glyphicon-remove"></i> Remove</a>	
						<?php } ?>
						
						<?php if($row['statusCode']=='X' AND ($s_userGroupCode=='admin' OR $s_userGroupCode=='it' OR $s_userGroupCode=='prog')){ ?>
							<a class="btn btn-danger" name="btn_row_delete"  data-id="<?=$row['id'];?>" > 
								<i class="glyphicon glyphicon-trash"></i> Delete</a>	
						<?php } ?>
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
});
</script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
