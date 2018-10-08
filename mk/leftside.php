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
	<input type="hidden" name="id" value="<?= $_GET['id']; ?>" />
	<table>
		<tr>		
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
			</td>
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
      <ul class="sidebar-menu" >	
		
		<?php switch($s_userGroupCode){ case 'admin' :   case 'it' :   ?>
			<li class="header"> Admin Master Menu</li>	
			<li><a href="userGroup.php"><i class="fa fa-users"></i> <span>User Group</span></a></li>
			<li><a href="custGroup.php"><i class="fa fa-object-group"></i> <span>Customer Group</span></a></li>
			<li><a href="custLocation.php"><i class="fa fa-map-pin"></i> <span>Customer Location</span></a></li>
			<li><a href="market.php"><i class="fa fa-puzzle-piece"></i> <span>Market</span></a></li>
			<li><a href="prodCat.php"><i class="fa fa-industry"></i> <span> Product Catagory</span></a></li>
			<li><a href="prdSuppType.php"><i class="fa fa-object-ungroup"></i> <span> Product Supplier Group</span></a></li>
			<li><a href="saleStkType.php"><i class="fa fa-battery-3"></i> <span> Sale Stock Type</span></a></li>
			<li><a href="salePackType.php"><i class="fa fa-cube"></i> <span> Sale Package Type</span></a></li>
			<li><a href="salePriceType.php"><i class="fa fa-money"></i> <span> Sale Price Type</span></a></li>
			<li><a href="saleDeliveryType.php"><i class="fa fa-truck"></i> <span> Sale Delivery Type</span></a></li>
			<li><a href="saleContLoadType.php"><i class="fa fa-ship"></i> <span> Sale Container Load Type</span></a></li>
			<li><a href="saleCreditType.php"><i class="fa fa-credit-card"></i> <span> Sale Credit Type</span></a></li>
			<!--<li><a href="saleOptionType.php"><i class="fa fa-sticky-note"></i> <span> Sale Option</span></a></li>	-->

			<li class="header"> Master Menu</li>			
			<!-- Optionally, you can add icons to the links -->
			<li><a href="user.php"><i class="fa fa-user"></i> <span>User</span></a></li>
			<li><a href="pick_cust_n_prod_condition.php"><i class="fa fa-filter"></i> <span>Picking Condition by Cust.and Prod.</span></a></li>
		<?php } ?>
		
		<?php //switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' :  ?>		
			<li class="header">Master Management</li>
		<?php switch($s_userGroupCode){ case 'admin' : case 'it' :  case 'sales' : case 'salesAdmin' : ?>
			<!-- Optionally, you can add icons to the links -->
			<li><a href="customer.php"><i class="glyphicon glyphicon-user"></i> <span>Customer Data</span></a></li>
			<!--<li><a href="shipto.php"><i class="glyphicon glyphicon-download-alt"></i> <span>Ship to Customer Data</span></a></li>-->
			<li><a href="shipping_marks.php"><i class="fa fa-table"></i> <span>Shipping Marks Data</span></a></li>
			<li><a href="product.php"><i class="fa fa-barcode"></i> <span>Product Data</span></a></li>

			
			<li><a href="product_roll_length.php"><i class="glyphicon glyphicon-compressed"></i> <span>Product Roll Length</span></a></li>
			<li><a href="salesman.php"><i class="glyphicon glyphicon-briefcase"></i> <span>Salesman Data</span></a></li>
		<?php } ?>
			<?php switch($s_userGroupCode){ case 'admin' : case 'it' :  ?>
			
			<?php } ?>
		<?php switch($s_userGroupCode){ case 'admin' :  ?>
				
		<?php } ?>
		
		<?php switch($s_userGroupCode){ case 'admin' : case 'sales' : case 'salesAdmin' : ?>
			<li class="header">Master List</li>
			<!-- Optionally, you can add icons to the links 
			<li><a href="customer.php"><i class="fa fa-male"></i> <span>Customer List (SS)</span></a></li>-->
			
			<li><a href="product_list.php"><i class="glyphicon glyphicon-th-list"></i> <span>Product List</span></a></li>
			<!--<li><a href=""><i class="fa fa-table"></i> <span>Customer Target List (SS)</span></a></li>	
			<li><a href=""><i class="fa fa-table"></i> <span>Salesman Target List (SS)</span></a></li>	-->		
		<?php } ?>
		
		<?php switch($s_userGroupCode){ case 'it' : case 'admin' : case 'salesAdmin' : case 'sales' : ?>
			<li class="header">Transaction Menu</li>
			<li><a href="sale2.php"><i class="fa fa-cart-plus"></i> <span>Sales Order</span></a></li>	
			<!--<li><a href="sale2.php"><i class="fa fa-cart-plus"></i> <span>Sales Order [Beta]</span></a></li>		
			<li><a href="delivery.php"><i class="fa fa-truck"></i> <span>Delivery</span></a></li>
			<li><a href="invoice.php"><i class="glyphicon glyphicon-usd"></i> <span>Invoice</span></a></li>
			<li><a href="inv_ret.php"><i class="glyphicon glyphicon-repeat"></i> <span>Customer Return</span></a></li>-->
		<?php break; default : } ?>
		
		<?php switch($s_userGroupCode){ case 'admin' : ?>
			<li><a href="order_pending.php"><i class="fa fa-truck"></i> <span>Order Pending</span></a></li>
		<?php break; default : } ?>	

		<li class="header">Search</li>
		<?php switch($s_userGroupCode){ case 'admin' : case 'sales' : case 'salesAdmin' : ?>	
			<li><a href="inq_sales.php"><i class="fa fa-search"></i> <span> Sales Order Search</span></a></li>
		<?php break; default : } ?>
		<?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : ?>	
			<!--<li><a href="inq_invoice.php"><i class="fa fa-bars"></i> <span> Invoice</span></a></li>-->
		<?php break; default : } ?>
		
			<li class="header">Report</li>
		<?php switch($s_userGroupCode){  case 'it' :  case 'admin' : case 'sales' : case 'salesAdmin' : ?>			
			<li><a href="report_sale.php"><i class="fa fa-bars"></i> <span>Sales Order </span></a></li>			
			<li><a href="report_sale_pending_by_prod.php"><i class="fa fa-bars"></i> <span>Sales Order Pending by Product </span></a></li>	
			<li><a href="report_prod_stk.php"><i class="fa fa-list-alt"></i> <span>Stock Report</span></a></li>
			<li><a href="report_prod_stk_n_pending.php"><i class="fa fa-list-alt"></i> <span>Stock n Pending Report</span></a></li>
		<?php break; default : } ?>
		
		<?php switch($s_userGroupCode){ case 'admin' : ?>	
			<!--<li><a href="#"><i class="fa fa-bars"></i> <span>Visit Customer </span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>New Product Development</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>Customer Complain </span></a></li>				
			<li><a href=""><i class="fa fa-bars"></i> <span>Forecast&Actual by Salesman</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>Forecast&Actual Customer</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>Forecast&Actual by Market</span></a></li>
			<li><a href="#"><i class="fa fa-bars"></i> <span>Forecast&Actual by Product</span></a></li>-->			
		<?php break; default : } ?>  
 		
		<li class="header">Setting</li>	
		<li><a href="user_change_pw.php"><i class="fa fa-bars"></i> <span> Change Password </span></a></li>	    
		
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

