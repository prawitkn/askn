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
		<li class="header">Transaction Menu</li>
        <li><a href="order.php"><i class="fa fa-paypal"></i> <span>Order</span></a></li>
		<li><a href="order_pending.php"><i class="fa fa-truck"></i> <span>Delivery Order</span></a></li>
		<li><a href="visit_customer.php"><i class="fa fa-search"></i> <span>Visit Customer</span></a></li>	
		<!--<li><a href=""><i class="fa fa-paypal"></i> <span>New Product Development</span></a></li>
		<li><a href=""><i class="fa fa-paypal"></i> <span>Customer Complain</span></a></li>-->
		
		
        <li class="header">Master Menu</li>
        <!-- Optionally, you can add icons to the links -->
		<li><a href="customer.php"><i class="fa fa-male"></i> <span>Customer Data</span></a></li>
		<li><a href="customerGroup.php"><i class="fa fa-list-ol"></i> <span>Customer Group</span></a></li>
		<li><a href="product.php"><i class="fa fa-barcode"></i> <span>Product Data</span></a></li>
		<li><a href="product_type.php"><i class="fa fa-list-ol"></i> <span>Product Catagory</span></a></li>
		<li><a href="salesman.php"><i class="fa fa-male"></i> <span>Salesman Data</span></a></li>
		<li><a href="user.php"><i class="fa fa-male"></i> <span>Users</span></a></li>
		<li><a href=""><i class="fa fa-table"></i> <span>Customer Target</span></a></li>
		<li><a href=""><i class="fa fa-table"></i> <span>Product Target</span></a></li>
        
        
		
        <li class="header">Reports</li>
        <li><a href=""><i class="fa fa-bars"></i> <span>Forcast & Actual by Product</span></a></li>
        <li><a href="#"><i class="fa fa-bars"></i> <span>Forcast & Actual by Customer</span></a></li>
		<li><a href="#"><i class="fa fa-bars"></i> <span>Forcast & Actual by Salesman</span></a></li>
        <li><a href="#"><i class="fa fa-female"></i> <span>Staff</span></a></li>
        <li><a href="#"><i class="fa fa-adjust"></i> <span>Products</span></a></li>
        <li><a href="#"><i class="fa fa-star"></i> <span>Customers</span></a></li>
        <li><a href="#"><i class="fa fa-check"></i> <span>Stock Balance</span></a></li>
        <li><a href="#"><i class="fa fa-chevron-down"></i> <span>Others</span></a></li>
        <li><a href="#"><i class="fa fa-feed"></i> <span>Miscellaneous</span></a></li>
        
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>