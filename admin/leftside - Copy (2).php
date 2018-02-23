<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="dist/img/<?php echo (empty($s_userPicture)? 'default-50x50.gif' : $s_userPicture) ?> " class="img-circle" alt="<?= $s_userFullname ?>">
        </div>
        <div class="pull-left info">
          <p><?= $s_userFullname ?></p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
	<?php
	$year = date('Y');
	$month = "0";//date('m');
	$monthName = "All";
	if(isset($_GET['year'])) $year = $_GET['year'];
	if(isset($_GET['month'])) $month = $_GET['month'];
?>    
	<form action="index.php" method="get">
	<table>
		<tr>		
			<td style="color: white;">Year :</td>
			<td>
				<select name="year" class="form-control">
					<?php 
						$y = date('Y', strtotime('-2 years'));
						while($y <= date('Y')){
							$selected=($year==$y?'selected':'');
							echo '<option value="'.$y.'" '.$selected.' >'.$y.'</option>';
							$y+=1;
						}
					?>
				</select>
			</td><td></td>
		</tr>
		<tr>
			<td style="color: white;">Month :</td>
			<td>
				<select name="month" class="form-control">
					<option value="0" <?php echo ($month=="0"?'selected':''); ?> >--All--</option>
					<?php
					$sql = "SELECT `id`, `abb`, `name`, `abb_eng`, `name_eng` FROM month";
					$stmt = $pdo->prepare($sql);
					$stmt->execute();					
					while ($row = $stmt->fetch()){
						$selected=($month==$row['id']?'selected':'');
						if($month==$row['id']) $monthName=$row['abb_eng'];							
						echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['abb_eng'].'</option>';
					}
					?>
				</select>
			</td>
			<td><button type="submit"  class="form-control"><i class="fa fa-search"></i></button></td>
		</tr>
	</table>
	</form>
      <!-- search form (Optional) 
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="year" class="form-control" placeholder="<?= $year; ?>">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
		
		
		<?php if($s_userGroupCode=='admin'){ ?>
			<li class="header">Core Master Menu</li>
			<!-- Optionally, you can add icons to the links -->
			<li><a href="user.php"><i class="fa fa-male"></i> <span>User</span></a></li>		
			<li><a href="user.php"><i class="fa fa-list-ol"></i> <span>User Group</span></a></li>
			<li><a href="customerGroup.php"><i class="fa fa-list-ol"></i> <span>Market</span></a></li>
			<li><a href="customerGroup.php"><i class="fa fa-list-ol"></i> <span>Customer Location</span></a></li>
			<li><a href="customerGroup.php"><i class="fa fa-list-ol"></i> <span>Customer Group</span></a></li>
			<li><a href="product_type.php"><i class="fa fa-list-ol"></i> <span>Product Catagory</span></a></li>			
		<?php } ?>
		
		<?php if($s_userGroupCode=='admin' OR $s_userGroupCode=='salesAdmin'){ ?>
			<li class="header">Master Management</li>
			<!-- Optionally, you can add icons to the links -->
			<li><a href="customer.php"><i class="fa fa-male"></i> <span>Customer Data</span></a></li>
			<li><a href="product.php"><i class="fa fa-barcode"></i> <span>Product Data</span></a></li>
			<li><a href="salesman.php"><i class="fa fa-male"></i> <span>Salesman Data</span></a></li>
			<li><a href=""><i class="fa fa-table"></i> <span>Customer Target Data</span></a></li>		
			<li><a href=""><i class="fa fa-table"></i> <span>Product Target Data</span></a></li>
			<li><a href=""><i class="fa fa-table"></i> <span>Salesman Target Data</span></a></li>			
		<?php } ?>
		
		<?php if($s_userGroupCode=='admin' OR $s_userGroupCode=='sales'){ ?>
			<li class="header">Master List</li>
			<!-- Optionally, you can add icons to the links -->
			<li><a href="customer.php"><i class="fa fa-male"></i> <span>Customer List (SS)</span></a></li>
			<li><a href="product.php"><i class="fa fa-barcode"></i> <span>Product List</span></a></li>
			<li><a href=""><i class="fa fa-table"></i> <span>Customer Target List (SS)</span></a></li>	
			<li><a href=""><i class="fa fa-table"></i> <span>Salesman Target List (SS)</span></a></li>			
		<?php } ?>
		
		<?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : case 'sales' : ?>
			<li class="header">Transaction Menu</li>
			<li><a href="order.php"><i class="fa fa-cart-plus"></i> <span>Order (SS)</span></a></li>
			<li><a href="visit_customer.php"><i class="fa fa-map-marker"></i> <span>Visit Customer (SS)</span></a></li>	
			<li><a href=""><i class="fa fa-eyedropper"></i> <span>New Product Development (SS)</span></a></li>
			<li><a href=""><i class="fa fa-comments"></i> <span>Customer Complain (SS)</span></a></li>
		<?php break; default : } ?>
		
		<?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : ?>
			<li><a href="order_pending.php"><i class="fa fa-truck"></i> <span>Delivery Order (SS)</span></a></li>
		<?php break; default : } ?>	

		
			<li class="header">Report</li>
		<?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : case 'sales' : ?>			
			<li><a href=""><i class="fa fa-bars"></i> <span>F. & A. Sales Order (SS)</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>F. & A. Sales Order Summary (SS)</span></a></li>
		<?php break; default : } ?>
		
		<?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : ?>	
			<li><a href="#"><i class="fa fa-bars"></i> <span>Visit Customer (SS)</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>New Product Development (SS)</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>Customer Complain (SS)</span></a></li>
		<?php break; default : } ?>
		
 		<?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : ?>			
			<li><a href=""><i class="fa fa-bars"></i> <span>F. & A. by Salesman</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>F. & A. by Customer</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>F. & A. by Market</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>F. & A. by Product</span></a></li>			
		<?php break; default : } ?>   		
		        
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>