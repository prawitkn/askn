
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 
	
	include '../db/db_sqlsrv.php';
	
$rootPage = 'productionMappingProduct';

//Check user roll.
$isAllow=false;
switch($s_userGroupCode){
	case 'admin' : $isAllow=true; break;
	case 'pdSup' : 
		if ( $s_userDeptCode == 'T' ){
			$isAllow=true;
		}
		break;
	default : 
}//.switch

if ( !$isAllow ){
	header('Location: access_denied.php');
	exit();
}//.if isallow

	if(isset($_GET['sync'])){		
		//TRUNCATE temp 
		$sql = "SELECT IFNULL(MAX(invProdId),0) as lastInvProdId FROM product_mapping";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$row=$stmt->fetch();
		$lastInvProdId=$row['lastInvProdId'];
		
		//TRUNCATE temp 
		$sql = "TRUNCATE TABLE product_mapping_temp";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();

		$sql = "SELECT [ProductID]
		  ,[ProductName]
		  ,[ProductDesc]
		  ,[SourceID]
		  ,[ProductTypeID]
		  ,[ProcessTypeID]
		  ,[ColorID]
		  ,[ProductSizeID]
		  ,[MinWeightStd]
		  ,[MaxWeightStd]
		  ,[UnitType]
		  ,[IsDisable]
		  ,[IsActive] FROM [product]  
		  WHERE [IsDisable]='N'
		  AND [IsActive]='Y'
		  AND [ProductID] NOT IN (SELECT [ProductID]
									FROM [askn].[dbo].[product] group by ProductID having count(*) > 1)
		  ";
		//echo $sql;
		$msResult = sqlsrv_query($ssConn, $sql);
		$msRowCount = 0;

		set_time_limit(0);
		if($msResult){
		while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {	
			//Insert mysql from mssql
			$sql = "INSERT INTO  `product_mapping_temp` 
			(`invProdId`, `invProdName`) 
			VALUES
			(:invProdId,:invProdName)
			";		
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':invProdId', $msRow['ProductID']);	
			$stmt->bindParam(':invProdName', $msRow['ProductName']);	
			$stmt->execute();

			$msRowCount+=1;
		}
		//end while mssql
		}else{
			echo sqlsrv_errors();
			exit();
		}
		//if
		
		sqlsrv_free_stmt($msResult);
		
		//Delete temp
		$sql = "DELETE FROM `product_mapping` WHERE invProdId IN (SELECT invProdId FROM product_mapping_del) 
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		
		//Delete temp
		$sql = "DELETE FROM `product_mapping_temp` WHERE invProdId IN (SELECT invProdId FROM product_mapping_del) 
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		
		
		//Update prod with temp
		$sql = "UPDATE product_mapping prod 
		INNER JOIN product_mapping_temp tmp ON tmp.invProdId=prod.invProdId
		SET prod.`invProdName`=tmp.`invProdName`
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		
		//Insert prod with temp
		$sql = "INSERT INTO  `product_mapping` 
		(`invProdId`, `invProdName`, `statusCode`) 
		SELECT invProdId,invProdName,'A' 
		FROM product_mapping_temp 
		WHERE invProdId NOT IN (SELECT invProdId FROM product_mapping) 
		";			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();		
		
		header("Location: ".$rootPage.".php");
		
		exit();
	}//if sync
	
	

?>	<!-- head.php included session.php! -->

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
		<h1><i class="glyphicon glyphicon-user"></i>
       Product Mapping Product
        <small>Product Mapping Product management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Product Mapping Product List</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

<!-- To allow only admin to access the content -->      
    <div class="box box-primary">
        <div class="box-header with-border">
		<label class="box-title">Product Mapping Product List</label>
			<a href="<?=$rootPage;?>.php?sync=1" class="btn btn-primary"><i class="glyphicon glyphicon-refresh"></i> Sync Product With Inventory System.</a>
			
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
                //$sql_user = "SELECT COUNT(*) AS COUNTUSER FROM wh_user";
               // $result_user = mysqli_query($link, $sql_user);
               // $count_user = mysqli_fetch_assoc($result_user);
				
				$search_word="";
                $sql = "SELECT COUNT(*) as countTotal 
				FROM `product_mapping`  hdr
				LEFT JOIN product prd ON prd.id=hdr.wmsProdId 
				WHERE 1=1 ";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sql .= "and (hdr.invProdName like '%".$_GET['search_word']."%' OR hdr.wmsProdId like '%".$_GET['search_word']."%' ) ";
				}
				if( isset($_GET['isNotMap']) ){
					$sql .= "and (hdr.wmsProdId='' OR hdr.wmsProdId IS NULL) ";
				}
                $result = mysqli_query($link, $sql);
                $countTotal = mysqli_fetch_assoc($result);			
				
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
							<label for="search_word">Product Mapping Product search key word.</label>
							<div class="input-group">
								<input id="search_word" type="text" class="form-control" name="search_word" data-smk-msg="Require userFullname."required>
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-search"></span>
								</span>
							</div>
							<input type="checkbox" name="isNotMap" <?php echo (isset($_GET['isNotMap'])?' checked ':''); ?> /> No Map
						</div>						
						<input type="submit" class="btn btn-default" value="ค้นหา">
					</form>
				</div>  
				<!--/.col-md-->
			</div>
			<!--/.row-->
           <?php
				$sql = "SELECT hdr.`invProdId`, hdr.`invProdName`, hdr.`wmsProdId`, hdr.`statusCode` 
				, prd.code as wmsProdCode 
				FROM `product_mapping`  hdr 
				LEFT JOIN product prd ON prd.id=hdr.wmsProdId 
				WHERE 1=1 ";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sql .= "and (hdr.invProdName like '%".$_GET['search_word']."%' OR hdr.wmsProdId like '%".$_GET['search_word']."%' ) ";
				}
				if( isset($_GET['isNotMap']) ){
					$sql .= "and (hdr.wmsProdId='' OR hdr.wmsProdId IS NULL) ";
				}
				$sql .= "ORDER BY hdr.wmsProdId, hdr.invProdId ASC
						LIMIT $start, $rows 
				";	
                //$result = mysqli_query($link, $sql);
				$stmt = $pdo->prepare($sql);	
				$stmt->execute();	
                
           ?> 
            
            <table class="table table-striped">
                <tr>
					<th>No.</th>
					<th>Production Name</th>
					<th>WMS Product Code</th>
                    <th>Status</th>
                    <th>#</th>
                </tr>
                <?php $c_row=($start+1); while ($row = $stmt->fetch()) { 
					$statusName = '<label class="label label-info">Unknow</label>';
						switch($row['statusCode']){
							case 'A' : $statusName = '<label class="label label-success">Active</label>'; break;
							case 'X' : $statusName = '<label class="label label-danger">Inactive</label>'; break;
							default : 						
						}
						?>
                <tr>
					<td>
                         <?= $c_row; ?>
                    </td>			
                    <td>
                         <?= $row['invProdName']; ?>
                    </td>
					<td>
                         <?= $row['wmsProdCode']; ?>
                    </td>
                    <td>
                         <?=$statusName; ?>
                    </td>
					<td>					
						<a class="btn btn-success fa fa-edit" name="btn_row_edit" href="<?=$rootPage;?>_edit.php?id=<?=$row['invProdId'];?>"  ></a>												
						<a class="btn btn-danger fa fa-trash" name="btn_row_delete" <?php echo ($row['statusCode']=='X'?' data-id="'.$row['invProdId'].'" ':' disabled '); ?> ></a>	
                    </td>
                </tr>
                <?php $c_row+=1; } ?>
            </table>
			
				
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
});
  
  


</script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
