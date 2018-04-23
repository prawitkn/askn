
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>	<!-- head.php included session.php! -->

<div class="wrapper">

  <!-- Main Header -->
  <?php 
include 'header.php'; 

$rootPage = 'claim';
$tb = 'claim_hdr';

//Check user roll.
switch($s_userGroupCode){
	case 'admin' : case 'sales' : case 'salesAdmin' :
		break;
	default : 
		include 'access_denied2.php';
		exit();
}
  
  ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
   <!-- Content Header (Page header) -->
	<section class="content-header">	  
	  <h1><i class="glyphicon glyphicon-alert"></i>
       Claim
        <small>Claim management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i> Claim List</a></li>
		<!--<li><a href="#"><i class="glyphicon glyphicon-edit"></i> Claim</a></li>-->
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

<!-- To allow only admin to access the content -->      
    <div class="box box-primary">
        <div class="box-header with-border">
		<label class="box-title">Claim List</label>
			<a href="<?=$rootPage;?>_dtl.php?act=add&docNo=" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Add Claim</a>
		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
			$search_word="";
			if(isset($_GET['search_word']) and isset($_GET['search_word'])){
				$search_word=$_GET['search_word'];
			}	
			$sql = "
			SELECT COUNT(h.id) AS countTotal 
			FROM ".$tb." h 
			LEFT JOIN customer cust ON cust.id=h.custId 
			WHERE 1 ";
			if($search_word<>""){				
				$sql .= "and (cust.name like '%".$search_word."%' ) ";
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
				<!--/.col-md-->
			</div>
			<!--/.row-->
           <?php
			$sql = "
			SELECT h.`refNo`, h.`docNo`, h.`docDate`, h.`prodId`, h.`probId`, h.`probRem`, h.`custReqId`
			, h.`acceptId`, h.`acceptRem`, h.`acceptById`, h.`acceptTime`
			, h.`concId`, h.`concRem`, h.`concById`, h.`concTime`
			, h.`custId`, h.`shipToId`, h.`smId`
			, h.`statusCode`, h.`createTime`, h.`createById`, h.`updateTime`, h.`updateById`, h.`confirmTime`, h.`confirmById`, h.`approveTime`, h.`approveById` 
			, cust.name as custName 
			FROM ".$tb." h 
			LEFT JOIN customer cust ON cust.id=h.custId 
			WHERE 1 ";
			if($search_word<>""){				
				$sql .= "and (cust.name like '%".$search_word."%' ) ";
			}	
			$sql .= "ORDER BY h.updateTime DESC  ";
			$sql.="LIMIT $start, $rows ";		
			//$result = mysqli_query($link, $sql);
			$stmt = $pdo->prepare($sql);	
			$stmt->execute();	                
           ?> 
            <div class="table-responsive">
            <table class="table table-striped">
               <tr>
                    <th>No.</th>
					<th>Date</th>
					<th>Customer</th>
					<th>Status</th>
                    <th>#</th>
                </tr>
                <?php $c_row=($start+1); while ($row = $stmt->fetch()) { 
					$statusName = '<label class="label label-danger">Unknown</label>';
					switch($row['statusCode']){
						case 'A' : $statusName = '<label class="label label-danger">Incomplete</label>'; break;
						case 'B' : $statusName = '<label class="label label-info">Begin</label>'; break;
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						case 'K' : $statusName = '<label class="label label-success">Tech.Accepted</label>'; break;
						case 'U' : $statusName = '<label class="label label-danger">Tech.Unaccepted</label>'; break;
						case 'N' : $statusName = '<label class="label label-danger">Conc.Not approved</label>'; break;
						case 'Y' : $statusName = '<label class="label label-success">Conc.Approved</label>'; break;
						case 'H' : $statusName = '<label class="label label-warning">Conc.Other</label>'; break;
						default : 						
					}
						?>
                <tr>
                    <td>
                         <?= $c_row; ?>
                    </td> 
					<td>
                         <?= date('d M Y',strtotime($row['docDate'])); ?>
                    </td>
					<td>
                         <?= $row['custName']; ?>
                    </td> 
					<td>
                         <?= $statusName; ?>
                    </td> 		
                    <td>
						<a class="btn btn-success" name="btn_row_edit" href="<?=$rootPage;?>_edit.php?id=<?= $row['id']; ?>" >Edit</a>	
						<?php if($row['statusCode']=='X'){ ?>
							<a class="btn btn-danger fa fa-trash" name="btn_row_delete"  data-id="<?=$row['id'];?>" ></a>  
						<?php }else{ ?>	
							<a class="btn btn-danger fa fa-trash"  disabled  >Delete</a>  
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
	$("a[name=btn_row_delete]").click(function(e) {
	  var row_id = $(this).attr('data-id');
	  $.smkConfirm({text:'Are you sure you want to delete?',accept:'OK Sure.', cancel:'Do not Delete.'}, function (e){if(e){
			  window.location.replace('<?=$rootPage;?>_delete.php?id='+row_id);
	  }});
	  e.preventDefault();
	});
	
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
