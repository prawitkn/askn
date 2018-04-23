<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php 
	include 'head.php'; 
	include 'inc_helper.php'; 
?>    

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
		Customer
        <small>Customer management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Customer</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
      <a href="customer_add.php" class="btn btn-google">Add Customer</a>
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Customer List</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
				$search_word="";
				$sqlSearch = "";
				$url = "customer_admin.php";
				if(isset($_GET['search_word']) and isset($_GET['search_word'])){
					$search_word=$_GET['search_word'];
					$sqlSearch = "and custName like '%".$_GET['search_word']."%' ";
				}
                $sql = "SELECT count(*) as countTotal
						FROM customer a
						WHERE 1 "
						.$sqlSearch."
						ORDER BY a.custName asc 
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
          ?>
          <span class="label label-primary">Total <?php echo $countTotal['countTotal']; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
		
		<div class="row"> 
           <?php
				$sql = "SELECT code, name  
				FROM customer_location_type 
				WHERE statusCode='A' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();	
				$objLoc = $stmt->fetchAll(PDO::FETCH_OBJ);
				
				$sql = "SELECT code, name  
				FROM market 
				WHERE statusCode='A' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();	
				$objMk = $stmt->fetchAll(PDO::FETCH_OBJ);
				
				$sql = "SELECT code, name 
				FROM salesman 
				WHERE statusCode='A' AND smType='S'
				";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();	
				$objSm = $stmt->fetchAll(PDO::FETCH_OBJ);
				
				$sql = "SELECT code, name 
				FROM salesman 
				WHERE statusCode='A' AND smType='A'
				";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();	
				$objSa = $stmt->fetchAll(PDO::FETCH_OBJ);
				
                $sql = "SELECT `code`,
				`custName`,
				`locationCode`,
				`marketCode`,
				`custAddr`,
				`custContact`,
				`custContactPosition`,
				`zipcode`,
				`taxId`,
				`accNo`,
				`creditDay`,
				`creditLimit`,
				`accCond`,
				`custUsername`,
				`custPassword`,
				`custEmail`,
				`custTel`,
				`custFax`,
				`smCode`,
				`smAdmCode`,
				`statusCode`
				FROM customer 
				WHERE 1 
				LIMIT $start, $rows 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();				
           ?>             
		   <form id="form2" action="#" method="post" class="form" novalidate>
		   <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>No.</th>
					<th>Code</th>
					<th>custName</th>
					<th>locationCode</th>
					<th>marketCode</th>
					<th>custAddr</th>
					<th>zipcode</th>
					<th>custContact</th>
					<th>custContactPosition</th>
					<th>taxId</th>					
					<th>accNo</th>
					<th>creditDay</th>
					<th>creditLimit</th>
					<th>accCond</th>					
					<th>custEmail</th>
					<th>custTel</th>
					<th>custFax</th>
					<th>smCode</th>
					<th>smAdmCode</th>					
					<th>Status</th>
                    <th>#</th>
                </tr>
                <?php $c_row=($start+1); while ($row = $stmt->fetch()) { ?>
                <tr>
					<td><?= $c_row; ?></td>
					<td><input type="hidden" name="code[]" value="<?= $row['code']; ?>" />
						<input type="text" name="newCode[]" value="<?= $row['code']; ?>" />
					</td>
					<td><input type="text" name="custName[]" value="<?= $row['custName']; ?>" /></td>
					<td>
					<select name="locationCode[]" class="form-control">
						<?php $locationCode = $row['locationCode']; ?>
						<option value="" <?php echo ($locationCode==""?'selected':''); ?> >--All--</option>
						<?php
						 foreach ($objLoc as $r) {
							$selected=($locationCode==$r->code?'selected':'');						
							echo '<option value="'.$r->code.'" '.$selected.'>'.$r->code.' : '.$r->name.'</option>';
						}
						?>
					</select>
					</td>
					<td>
					<select name="marketCode[]" class="form-control">
						<?php $marketCode = $row['marketCode']; ?>
						<option value="" <?php echo ($marketCode==""?'selected':''); ?> >--All--</option>
						<?php
						 foreach ($objMk as $r) {
							$selected=($marketCode==$r->code?'selected':'');						
							echo '<option value="'.$r->code.'" '.$selected.'>'.$r->code.' : '.$r->name.'</option>';
						}
						?>
					</select>
					</td>
					<td><input type="text" name="custAddr[]" value="<?= $row['custAddr']; ?>" /></td>
					<td><input type="text" name="zipcode[]" value="<?= $row['zipcode']; ?>" /></td>
					<td><input type="text" name="custContact[]" value="<?= $row['custContact']; ?>" /></td>
					<td><input type="text" name="custContactPosition[]" value="<?= $row['custContactPosition']; ?>" /></td>
					<td><input type="text" name="taxId[]" value="<?= $row['taxId']; ?>" /></td>					
					<td><input type="text" name="accNo[]" value="<?= $row['accNo']; ?>" /></td>
					<td><input type="text" name="creditDay[]" value="<?= $row['creditDay']; ?>" /></td>
					<td><input type="text" name="creditLimit[]" value="<?= $row['creditLimit']; ?>" /></td>
					<td><input type="text" name="accCond[]" value="<?= $row['accCond']; ?>" /></td>					
					<td><input type="text" name="custEmail[]" value="<?= $row['custEmail']; ?>" /></td>
					<td><input type="text" name="custTel[]" value="<?= $row['custTel']; ?>" /></td>
					<td><input type="text" name="custFax[]" value="<?= $row['custFax']; ?>" /></td>
					<td>
					<select name="smCode[]" class="form-control">
						<?php $smCode = $row['smCode']; ?>
						<option value="" <?php echo ($smCode==""?'selected':''); ?> >--All--</option>
						<?php
						 foreach ($objSm as $r) {
							$selected=($smCode==$r->code?'selected':'');						
							echo '<option value="'.$r->code.'" '.$selected.'>'.$r->code.' : '.$r->name.'</option>';
						}
						?>
					</select>
					</td>
					<td>
					<select name="smAdmCode[]" class="form-control">
						<?php $smAdmCode = $row['smAdmCode']; ?>
						<option value="" <?php echo ($smAdmCode==""?'selected':''); ?> >--All--</option>
						<?php
						 foreach ($objSa as $r) {
							$selected=($smAdmCode==$r->code?'selected':'');						
							echo '<option value="'.$r->code.'" '.$selected.'>'.$r->code.' : '.$r->name.'</option>';
						}
						?>
					</select>
					</td>					
					<td>Status</td>
                </tr>
                <?php $c_row +=1; } ?>
            </table>
			</div>
			<!--table-responsive -->
			<input type="submit" class="btn btn-default" value="บันทึก">
			</form>
		</div>
			
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
	//.ajaxStart inside $(document).ready to start and stop spiner.  
	$( document ).ajaxStart(function() {
		$("#spin").show();
	}).ajaxStop(function() {
		$("#spin").hide();
	});
	//.ajaxStart inside $(document).ready END
	
	$("#title").focus();
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
	   
	$('#form2 a[name=btn_submit]').click (function(e) {
		if ($('#form2').smkValidate()){
			$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'customer_admin_submit_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "delivery_view.php?doNo=" + data.doNo;
					}else{
						$.smkAlert({
							text: data.message,
							type: 'danger',
							position:'top-center'
						});
					}
					//e.preventDefault();		
				}).error(function (response) {
					alert(response.responseText);
				});
				//.post
			}else{ 
				$.smkAlert({ text: 'Cancelled.', type: 'info', position:'top-center'});	
			}});
			//smkConfirm
		e.preventDefault();
		}//.if end
	});
	//.btn_click
});
</script>

</body>
</html>
