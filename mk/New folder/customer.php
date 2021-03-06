<?php include 'inc_helper.php';  ?>
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
  <?php 
	include 'header.php'; 
	
	$rootPage="customer";
  ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
  <?php
   		$search_word=(isset($_GET['search_word'])?$_GET['search_word']:'');
   ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">	  
	<h1><i class="glyphicon glyphicon-user"></i>
       Customer
        <small>Customer management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Customer List</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <div class="form-inline">
			<label class="box-title">Customer List</label>
			<a href="<?=$rootPage;?>_add.php" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Add Customer</a>
		</div>
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php				
                $sql = "SELECT count(*) as countTotal
						FROM customer hdr
						WHERE 1 ";				
				if( $search_word<>"" ){ $sql .= "AND hdr.name like '%".$search_word."%'  "; }	
				$sql .= "ORDER BY hdr.name asc 
						";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();	
                $countTotal = $stmt->fetch();
				
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
                    <form id="form1" action="customer.php" method="get" class="form" novalidate>
						<div class="form-group">
                            <label for="search_word">Customer search key word.</label>
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
                $sql = "SELECT hdr.*
						FROM customer hdr
						WHERE 1 ";				
				if( $search_word<>"" ){ $sql .= "AND hdr.name like '%".$search_word."%'  "; }	
				$sql .= "ORDER BY hdr.name asc 
						";
				$sql .= "
						LIMIT $start, $rows
						";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();				
           ?>             
            <table class="table table-striped">
                <tr>
                    <th>No.</th>
					<th>Code</th>
					<th>Name</th>
					<th>Status</th>
                    <th>#</th>
                </tr>
                <?php $c_row=($start+1); while ($row = $stmt->fetch()) { ?>
                <tr>
                    <td>
                         <?= $c_row; ?>
                    </td>
					<td>
                         <?= $row['code']; ?>
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
                <?php $c_row +=1; } ?>
            </table>
			
			<nav>
			<ul class="pagination">
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="customer.php?search_word=<?= $search_word;?>&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="customer.php?search_word=<?= $search_word;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="customer.php?search_word=<?= $search_word;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
				</li>
			</ul>
			</nav>
			
			
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

</body>
</html>
