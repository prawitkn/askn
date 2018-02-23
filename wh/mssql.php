
<html>
<!--<form method="post" >
 server : <input type="text" name="serverName" value="localhost\SQLEXPRESS" /><br/>
 user : <input type="text" name="uid" value="sa" /><br/>
 pass : <input type="text" name="pass" value="1234" /><br/>
 dbname : <input type="text" name="dbname" value="askn" /><br/>
 <input type="submit" value="Submit" />
</form>-->

<form method="post" >
 server : <input type="text" name="serverName" value="192.168.0.5" /><br/> <!-- or AD-SERVER -->
 user : <input type="text" name="uid" value="programmer02" /><br/>
 pass : <input type="text" name="pass" value="Wh@2017" /><br/>
 dbname : <input type="text" name="dbname" value="askn" /><br/>
 <input type="submit" value="Submit" />
</form>


</html>


<?php 
if(isset($_POST['serverName'])){
	
$serverName = $_POST['serverName']; 
$uid =  $_POST['uid'];   
$pwd =  $_POST['pass'];  
$databaseName =  $_POST['dbname'];

?>
Server Name : <?=$serverName;?><br/>
UID : <?=$uid;?><br/>
PASS : <?=$pwd;?><br/>
DBNAME : <?=$databaseName;?><br/><br/>

<?php

$connectionInfo = array( "UID"=>$uid,                            
                         "PWD"=>$pwd,                            
                         "Database"=>$databaseName); 
//$connectionInfo = "Data Source=".$serverName.";Initial Catalog=".$databaseName.";Integrated Security=SSPI;";
 //echo $connectionInfo;
/* Connect using SQL Server Authentication. */  
$conn = sqlsrv_connect( $serverName, $connectionInfo);  

if( $conn === false )  
{  
     echo "Unable to connect.</br>";  
     die( print_r( sqlsrv_errors(), true));  
}     


$tsql = "select top 1 * from product_item";  
  
/* Execute the query. */  
  
$stmt = sqlsrv_query( $conn, $tsql);  
  
if ( $stmt )  
{  
     echo "Statement executed.<br>\n";  
}   
else   
{  
     echo "Error in statement execution.\n";  
     die( print_r( sqlsrv_errors(), true));  
}  
  
/* Iterate through the result set printing a row of data upon each iteration.*/  
  
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC))  
{  
     echo "Col1: ".$row[0]."\n";  
     echo "Col2: ".$row[1]."\n";  
     echo "Col3: ".$row[2]."<br>\n";  
     echo "-----------------<br>\n";  
}  
  
/* Free statement and connection resources. */  
sqlsrv_free_stmt( $stmt);  
sqlsrv_close( $conn); 
} 
?>