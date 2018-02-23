<?php  
include '../db/database_sqlsrv.php';
//$pdo = new pdo_dblib_mssql('localhost\SQLEXPRESS','1433','askn','Integrated Security=SSPI','');
//$conn = new PDO( "sqlsrv:Server=(local);Database=askn", NULL, NULL);   
//$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  





$sql = "
SELECT count(*) as countTotal FROM product
";
$result = sqlsrv_query($ssConn, $sql);
$countTotal = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

$rows=20;
$page=0;
if( !empty($_GET["page"]) and isset($_GET["page"]) ) $page=$_GET["page"];
if($page<=0) $page=1;
$total_data=$countTotal['countTotal'];
$total_page=ceil($total_data/$rows);
if($page>=$total_page) $page=$total_page;
$start=($page-1)*$rows;
if($start<0) $start=0;




$sql = "SELECT productName, productDesc FROM product
";
//echo $sql;
$result = sqlsrv_query($ssConn, $sql);

?>
<table class="table table-striped">
		<tr>
			<th>SO No.</th>
			<th>SO DATE</th>
		</tr>
		<?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) { 
			?>
			
		<tr>
			<td>
				 <?= $row['productName']; ?>
			</td>
			<td>
				 <?= $row['productDesc']; ?>
			</td>
			<td>
				 <?= $row['productDesc']; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
<?
/* Free statement and connection resources. */  
sqlsrv_free_stmt( $stmt);  
sqlsrv_close( $sqlsrvConn);  

?>