<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 
$rootPage = 'userDept';
//Check user roll.
switch($s_userGroupCode){
	case 'admin' : case 'whSup' : case 'whMgr' : 
		break;
	default : 
		header('Location: access_denied.php');
		exit();
}
?>	<!-- head.php included session.php! -->
 
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">

<?php 

	$rootPage = 'closingStk';
	$tb = 'stk_closing';

?>	

<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">
		<h1><i class="fa fa-users"></i>
       Closing Stock
        <small>Transaction management</small>
      </h1>
	  <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-list"></i>Closing Stock List</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

<!-- To allow only admin to access the content -->      
    <div class="box box-primary">
        <div class="box-header with-border">
        	<label class="box-tittle" style="font-size: 20px;"><i class="fa fa-list"></i> Closing Stock list</label>

			<a href="<?=$rootPage;?>_data.php?id=" class="btn btn-primary"><i class="fa fa-plus"></i> Add Closing Stock</a>

			<a href="#" name="btnSync" class="btn btn-primary"><i class="fa fa-refresh"></i> Closing Stock</a>
		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
                //$sql_user = "SELECT COUNT(*) AS COUNTUSER FROM wh_user";
               // $result_user = mysqli_query($link, $sql_user);
               // $count_user = mysqli_fetch_assoc($result_user);
				
				$search_word="";
                $sql = "SELECT COUNT(*) AS countTotal 
				FROM `".$tb."` hdr 
				LEFT JOIN `wh_user` uc on uc.userId=hdr.createUserId 
				LEFT JOIN `wh_user` uu on uu.userId=hdr.updateUserId 
				";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sql .= "and (hdr.name like '%".$_GET['search_word']."%' ) ";
				}
				$stmt = $pdo->prepare($sql);	
				$stmt->execute();			
				$countTotal=$stmt->fetch()['countTotal'];

				$rows=100;
				$page=0;
				if( !empty($_GET["page"]) and isset($_GET["page"]) ) $page=$_GET["page"];
				if($page<=0) $page=1;
				$total_data=$countTotal;
				$total_page=ceil($total_data/$rows);
				if($page>=$total_page) $page=$total_page;
				$start=($page-1)*$rows;
				if($start<0) $start=0;		
          ?>
          <span class="label label-primary">Total <?php echo $total_data; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
				<form id="form1" action="<?=$rootPage;?>.php" method="get" class="form form-inline" novalidate>
				
					<div class="row">
							<div class="col-md-3">					
								<label for="search_word">search key word.</label>
								<input id="search_word" type="text" name="search_word" class="form-control" data-smk-msg="Require userFullname."required>
								
								
							</div>  
							<!--/.col-md-->
							
							<div class="col-md-1">
								<label for="submit">&nbsp;</label>
								<input type="submit" name="submit" class="btn btn-default" value="ค้นหา">
							</div>  
							<!--/.col-md-->
					</div>
					<!--/.row-->
			
			
				</form>
				<!--/.form1-->
			
           <?php
				$sql = "
				SELECT hdr.id, hdr.closingDate, hdr.statusCode

				, uc.userFullname as createUserName 
				, uu.userFullname as updateUserName 
				FROM `".$tb."` hdr 
				LEFT JOIN `wh_user` uc on uc.userId=hdr.createUserId 
				LEFT JOIN `wh_user` uu on uu.userId=hdr.updateUserId 
				WHERE 1=1 ";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sql .= "and (hdr.userFullname like '%".$_GET['search_word']."%' ) ";
				}	
				$sql .= "ORDER BY hdr.closingDate DESC 
						LIMIT $start, $rows 
				";		
                //$result = mysqli_query($link, $sql);
				$stmt = $pdo->prepare($sql);	
				$stmt->execute();	
                
           ?> 
            <div class="row col-md-12 table-responsive">
            <table class="table table-hover">
                <thead><tr style="background-color: #797979;">
					<th>No.</th>					
                    <th>Closing Date</th>
                    <th>Status</th>
                    <th>#</th>
                    <th></th>
                </tr></thead>
                <?php $rowNo=($start+1); while ($row = $stmt->fetch()) { 
						?>
                <tr>
					<td>
                         <?= $rowNo; ?>
                    </td>
					<td>
						<?= date('d M Y',strtotime( $row['closingDate'] )); ?>
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
						
						<?php if($row['statusCode']=='A'){ ?>
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
                <?php $rowNo+=1; } ?>
            </table>
				
			<nav>
			<ul class="pagination">
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage;?>_list.php?search_word=<?= $search_word;?>&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="<?=$rootPage;?>_list.php?search_word=<?= $search_word;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage;?>_list.php?search_word=<?= $search_word;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
				</li>
			</ul>
			</nav>
			
			</div>
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
			Id: $(this).attr('data-Id'),
			StatusId: $(this).attr('data-StatusId')			
		};
		$.smkConfirm({text:'Are you sure ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function (data) {					
				if (data.status === "success"){ 
					$.smkAlert({
						text: data.message,
						type: data.status,
						position:'top-center'
					});
					location.reload();
				} else {
					alert(data.message);
					$.smkAlert({
						text: data.message,
						type: data.status
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
			Id: $(this).attr('data-Id')
		};
		$.smkConfirm({text:'Are you sure to Remove ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function (data) {					
				if (data.status === "success"){ 
					$.smkAlert({
						text: data.message,
						type: data.status,
						position:'top-center'
					});
					location.reload();
				} else {
					alert(data.message);
					$.smkAlert({
						text: data.message,
						type: data.status
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
			Id: $(this).attr('data-Id')
		};
		$.smkConfirm({text:'Are you sure to Delete ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function (data) {					
				if (data.status === "success"){ 
					$.smkAlert({
						text: data.message,
						type: data.status,
						position:'top-center'
					});
					location.reload();
				} else {
					alert(data.message);
					$.smkAlert({
						text: data.message,
						type: data.status
					});
				}
			}).error(function (response) {
				alert(response.responseText);
			}); 
		}});
		e.preventDefault();
	});
	//end btn_row_delete

	$('a[name=btnSync]').click(function(){
		var params = {
			action: 'sync'
		};
		$.smkConfirm({text:'Are you sure to Sync ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function (data) {					
				if (data.status === "success"){ 
					$.smkAlert({
						text: data.message,
						type: data.status,
						position:'top-center'
					});
					location.reload();
				} else {
					alert(data.message);
					$.smkAlert({
						text: data.message,
						type: data.status
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
