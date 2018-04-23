<?php
	include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; /*$s_userID=$_SESSION['userID'];
		$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

				
$rootPage="inv_ret";
?>

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
	  <h1><i class="glyphicon glyphicon-arrow-left"></i>
       Customer Return
        <small>Customer Return management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Customer Return List</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
		<div class="form-inline">
			<label class="box-title">Customer Return List</label>
			<a href="<?=$rootPage;?>_add.php?docNo=" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Add Customer Return</a>
		</div>
		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
                $sql = "
				SELECT COUNT(hdr.docNo) AS countTotal
				FROM `inv_ret` hdr
				LEFT JOIN invoice_header inv on inv.invNo=hdr.refNo 
				LEFT JOIN  delivery_header dh on dh.doNo=inv.doNo 			
				LEFT JOIN  prepare pa on pa.ppNo=dh.ppNo 				
				LEFT JOIN  picking pi on pi.pickNo=pa.pickNo
				LEFT JOIN sale_header sh on sh.soNo=pi.soNo 
				LEFT JOIN customer ct on ct.code=hdr.custCode ";
				switch($s_userGroupCode){
					case 'it' : case 'admin' : 
						break;
					case 'sales' : $sql .= " AND ct.smCode=:s_smCode "; break;
					case 'salesAdmin' : 	$sql .= " AND ct.smAdmCode=:s_smCode "; break;
					default : 
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
						exit();
				}		
				$sql .= "
				LEFT JOIN salesman sm on sm.code=hdr.smCode 
				LEFT JOIN user uca on hdr.createByID=uca.userID
				LEFT JOIN user ucf on hdr.confirmByID=ucf.userID
				LEFT JOIN user uap on hdr.approveByID=uap.userID

				WHERE 1 
				AND hdr.statusCode<>'X' ";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$sql .= "AND (hdr.docNo like :search_word "; //" '%".$_GET['search_word']."%') ";
				}		
				$stmt = $pdo->prepare($sql);
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word = '%'.$search_word.'%';
					$stmt->bindParam(':search_word', $search_word);
				}				
				switch($s_userGroupCode){
					case 'it' : case 'admin' : 
						break;
					case 'sales' : $stmt->bindParam(':s_smCode', $s_smCode);
						break;
					case 'salesAdmin' : $stmt->bindParam(':s_smCode', $s_smCode);
						break;
					default : 
				}					
				$stmt->execute();				
               // $result = mysqli_query($link, $sql);
				$row = $stmt->fetch();
                $countTotal = $row['countTotal'];  //mysqli_fetch_assoc($result);
				
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
								<label for="search_word">Customer Return No. search key word.</label>
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
                $sql = "SELECT hdr.`docNo`, hdr.`refNo`, hdr.`docDate`, hdr.`custCode`, hdr.`smCode`, hdr.`totalExcVat`, hdr.`vatAmount`, hdr.`totalIncVat`
				, hdr.`remark`, hdr.`statusCode`
				, hdr.`createTime`, hdr.`createByID`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
				, ct.custName, ct.custAddr, ct.taxId, ct.creditDay 
				, concat(sm.name, '  ', sm.surname) as smFullname 
				, sh.soNo, sh.poNo 
				, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
				FROM `inv_ret` hdr
				LEFT JOIN invoice_header inv on inv.invNo=hdr.refNo 
				LEFT JOIN  delivery_header dh on dh.doNo=inv.doNo 			
				LEFT JOIN  prepare pa on pa.ppNo=dh.ppNo 				
				LEFT JOIN  picking pi on pi.pickNo=pa.pickNo
				LEFT JOIN sale_header sh on sh.soNo=pi.soNo 
				LEFT JOIN customer ct on ct.code=hdr.custCode ";
				switch($s_userGroupCode){
					case 'it' : case 'admin' : 
						break;
					case 'sales' : $sql .= " AND ct.smCode=:s_smCode "; break;
					case 'salesAdmin' : 	$sql .= " AND ct.smAdmCode=:s_smCode "; break;
					default : 
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
						exit();
				}		
				$sql .= "
				LEFT JOIN salesman sm on sm.code=hdr.smCode 
				LEFT JOIN user uca on hdr.createByID=uca.userID
				LEFT JOIN user ucf on hdr.confirmByID=ucf.userID
				LEFT JOIN user uap on hdr.approveByID=uap.userID

				WHERE 1 
				AND hdr.statusCode<>'X' ";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$sql .= "AND (hdr.docNo like :search_word "; //" '%".$_GET['search_word']."%') ";
				}							
				$sql .="			
				ORDER BY hdr.createTime DESC
				LIMIT $start, $rows 
				";
				$stmt = $pdo->prepare($sql);
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word = '%'.$search_word.'%';
					$stmt->bindParam(':search_word', $search_word);
				}
				switch($s_userGroupCode){
					case 'it' : case 'admin' : 
						break;
					case 'sales' : $stmt->bindParam(':s_smCode', $s_smCode);
						break;
					case 'salesAdmin' : $stmt->bindParam(':s_smCode', $s_smCode);
						break;
					default : 
				}					
				$stmt->execute();		
                //$result = mysqli_query($link, $sql);
           ?> 
            <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>Cust. Ret. No.</th>
					<th>Date</th>
					<th>Ref. No.</th>
					<th>Customer</th>
					<th>Salesman</th>
					<th>Status</th>
					<th>#</th>
                </tr>
                <?php while ($row = $stmt->fetch()) {
 
					$statusName = '<label class="label label-danger">Unknown</label>';
					switch($row['statusCode']){
						case 'B' : $statusName = '<label class="label label-info">Begin</label>'; break;
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						default : 						
					}
					?>
                <tr>
					<td><?= $row['docNo']; ?></td>
					<td><?= $row['docDate']; ?></td>
					<td><?= $row['refNo']; ?></td>
					<td><?= $row['custName']; ?></td>
					<td><?= $row['smFullname']; ?></td>
					<td><?= $statusName; ?></td>	
					<td>					
						<a class="btn btn-info " name="btn_row_search" 
							href="<?=$rootPage;?>_view.php?docNo=<?=$row['docNo'];?>" 
							data-toggle="tooltip" title="Search"><i class="glyphicon glyphicon-search"></i></a>
						<a class="btn btn-success" name="btn_row_edit" 
							<?php echo ($row['statusCode']=='B'?'href="'.$rootPage.'_add.php?docNo='.$row['docNo'].'"':' disabled '); ?> 
							data-toggle="tooltip" title="Edit" ><i class="glyphicon glyphicon-edit"></i></a>							
						<!--<a class="btn btn-danger fa fa-trash" name="btn_row_remove" 
							<?php echo ($row['statusCode']=='P'?'data-id="" disabled ':'data-id="'.$row['docNo'].'" '); ?>
							data-toggle="tooltip" title="Delete" ><i class="glyphicon glyphicon-trash"></i></a>-->
                    </td>
                </tr>
                <?php } ?>
            </table>
			</div>
			<!--tabl-response-->
			
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
				url: '<?=$rootPage;?>_remove_ajax.php',
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
