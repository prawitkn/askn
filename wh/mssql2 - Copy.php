<?php  
include '../db/database_sqlsrv.php';
//$pdo = new pdo_dblib_mssql('localhost\SQLEXPRESS','1433','askn','Integrated Security=SSPI','');
//$conn = new PDO( "sqlsrv:Server=(local);Database=askn", NULL, NULL);   
//$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  




/* Query SQL Server for the login of the user accessing the  
database. */  
$tsql = "SELECT CONVERT(varchar(32), SUSER_SNAME())";  
$stmt = sqlsrv_query( $sqlsrvConn, $tsql);  
if( $stmt === false )  
{  
     echo "Error in executing query.</br>";  
     die( print_r( sqlsrv_errors(), true));  
}  

/* Retrieve and display the results of the query. */  
$row = sqlsrv_fetch_array($stmt);  
echo "User login: ".$row[0]."</br>";  

/* Free statement and connection resources. */  
sqlsrv_free_stmt( $stmt);  
sqlsrv_close( $sqlsrvConn);  

?>