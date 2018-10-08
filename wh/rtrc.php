<?php
	include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
		
		switch($s_userGroupCode){ 
			case 'whOff' :
			case 'whSup' :
			case 'whMgr' : 
				header("Location: access_denied.php"); exit();
				break;
			default :	// it, admin 
		}	
		
$rootPage="rtrc";		
?>
 
</head>
<body class="hold-transition skin-green sidebar-mini">


	
  
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1><i class="glyphicon glyphicon-retweet"></i>
       Return Receive
        <small>Return Receive management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Return Receive List</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
		<div class="form-inline">
			<label class="box-title">Return Receive List</label>
			<a href="<?=$rootPage;?>_add.php?rcNo=" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> Add Return Receive</a>
		</div>
		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php								
				$search_word = (isset($_GET['search_word'])?$_GET['search_word']:'');
                $sql = "SELECT COUNT(hdr.rcNo) AS countTotal
				FROM `receive` hdr
				LEFT JOIN sloc fsl on hdr.fromCode=fsl.code 
				LEFT JOIN sloc tsl on hdr.toCode=tsl.code
				left join user d on hdr.createByID=d.userID
				WHERE 1 
				AND hdr.type='R' 
				AND hdr.statusCode<>'X' ";							
				switch($s_userGroupCode){ 
					case 'pdOff' :
					case 'pdSup' :
						$sql .= "AND hdr.toCode=:s_userDeptCode ";
						break;
					default :	// it, admin 
				}	
				if(isset($_GET['search_word']) and $_GET['search_word']<>""){
					$sql .= "AND hdr.rcNo like :search_word ";		
				}		
				$stmt = $pdo->prepare($sql);
				switch($s_userGroupCode){ 	
					case 'pdOff' :
					case 'pdSup' :
						$stmt->bindParam(':s_userDeptCode', $s_userDeptCode);
						break;
					default :	// it, admin 
				}
				if( isset($_GET['search_word']) and $_GET['search_word']<>"" ){
					$tmp='%'.$search_word.'%';
					$stmt->bindParam(':search_word', $tmp);
				}
				$stmt->execute();
				$row = $stmt->fetch();
				$countTotal = $row['countTotal'];
				
				$rows=100;
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
								<label for="search_word">Receive No. search key word.</label>
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
                $sql = "SELECT hdr.`rcNo`, hdr.`refNo`, hdr.`receiveDate`, hdr.`fromCode`, hdr.`toCode`, hdr.`remark`, hdr.`sdNo`, hdr.`statusCode`, hdr.`createTime`, hdr.`createByID`
				, fsl.name as fromName, tsl.name as toName
				, d.userFullname as createByName
				FROM `receive` hdr
				LEFT JOIN sloc fsl on hdr.fromCode=fsl.code 
				LEFT JOIN sloc tsl on hdr.toCode=tsl.code
				left join user d on hdr.createByID=d.userID
				WHERE 1 
				AND hdr.type='R' 
				AND hdr.statusCode<>'X' ";
				switch($s_userGroupCode){ 
					case 'pdOff' :
					case 'pdSup' :
						$sql .= "AND hdr.toCode=:s_userDeptCode ";
						break;
					default :	// it, admin 
				}	
				if(isset($_GET['search_word']) and $_GET['search_word']<>""){
					$sql .= "AND hdr.rcNo like :search_word ";		
				}	
				$sql .="ORDER BY hdr.createTime DESC ";
				$sql .= "LIMIT $start, $rows ";
				
				$stmt = $pdo->prepare($sql);
				switch($s_userGroupCode){ 
					case 'pdOff' :
					case 'pdSup' :
						$stmt->bindParam(':s_userDeptCode', $s_userDeptCode);
						break;
					default :	// it, admin 
				}
				if( isset($_GET['search_word']) and $_GET['search_word']<>"" ){
					$tmp='%'.$search_word.'%';
					$stmt->bindParam(':search_word', $tmp);
				}
				$stmt->execute();
				
           ?> 
            <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>Receive No.</th>
					<th>Receive Date</th>
					<th>Ref. No.</th>
					<th>Sender</th>
					<th>Receiver</th>					
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
					<td>
                         <?= $row['rcNo']; ?>
                    </td>
					<td><?= date('d M Y',strtotime( $row['receiveDate'] )); ?></td>
					
					<td>
                         <?= $row['refNo']; ?>
                    </td>
                    <td>
                         <?= $row['fromName']; ?>
                    </td>
					<td>
                         <?= $row['toName']; ?>
                    </td>
					<td>
                         <?= $statusName; ?>
                    </td>	
					<td>					
						<a class="btn btn-info " name="btn_row_search" 
							href="<?=$rootPage;?>_view.php?rcNo=<?=$row['rcNo'];?>" 
							data-toggle="tooltip" title="Search"><i class="glyphicon glyphicon-search"></i></a>
						<a class="btn btn-success" name="btn_row_edit" 
							<?php echo ($row['statusCode']=='B'?'href="'.$rootPage.'_add.php?rcNo='.$row['rcNo'].'"':' disabled '); ?> 
							data-toggle="tooltip" title="Edit" ><i class="glyphicon glyphicon-edit"></i></a>							
						<!--<a class="btn btn-danger fa fa-trash" name="btn_row_remove" 
							<?php echo ($row['statusCode']=='P'?'data-id="" disabled ':'data-id="'.$row['rcNo'].'" '); ?>
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
});
</script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
