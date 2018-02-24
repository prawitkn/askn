<?php
include 'inc_helper.php'; 
include 'session.php';

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=sales_prod.csv");

$sqlRole = "";
$sqlRoleSm = "";
switch($s_userGroupCode){
	case 'sales' :
		$sqlRole = " AND ct.smCode='$s_smCode' ";
		$sqlRoleSm = " AND sm.code='$s_smCode' ";
		break;
	case 'salesAdmin' :
		$sqlRole = " AND ct.smAdmCode='$s_smCode' ";
		break;
	default :
}

$dateFrom = (isset($_GET['dateFrom'])?to_mysql_date($_GET['dateFrom']):'');
$dateTo = (isset($_GET['dateTo'])?to_mysql_date($_GET['dateTo']):'');
$custCode = (isset($_GET['custCode'])?$_GET['custCode']:'');
$smCode = (isset($_GET['smCode'])?$_GET['smCode']:'');
$search_word = (isset($_GET['search_word'])?$_GET['search_word']:'');

$sqlSearch = "";
$url="inq_sales.php";
if($search_word<>""){ $sqlSearch = " AND (prodName like '%".$search_word."%' OR prodNameNew like '%".$search_word."%') "; }
if($smCode<>""){ $sqlSearch .= " AND sh.smCode='$smCode' ";	}
if($custCode<>""){ $sqlSearch .= " AND sh.custCode='$custCode' ";	}
if($dateFrom<>""){ $sqlSearch .= " AND sh.saleDate>='$dateFrom' ";	}
if($dateTo<>""){ $sqlSearch .= " AND sh.saleDate<='$dateTo' ";	}

$sql = "SELECT sh.`soNo`, sh.`poNo`, sh.`saleDate`, sh.`custCode`
, ct.custName
, sh.`smCode`
, sm.name as smName 
, sh.`netTotal`, sh.`statusCode`, sh.`isClose`
, sd.prodCode
, pd.prodName 
, sd.salesPrice, sd.qty, sd.discAmount, sd.netTotal as prodNetTotal
FROM `sale_header` sh
LEFT JOIN customer ct on ct.code=sh.custCode ".$sqlRole."
LEFT JOIN salesman sm on sm.code=sh.smCode ".$sqlRoleSm."
LEFT JOIN sale_detail sd on sh.soNo=sd.soNo 
LEFT JOIN product pd on pd.code=sd.prodCode 
WHERE 1 "
.$sqlSearch."
ORDER BY soNo DESC, prodCode ASC
";
$stmt = $pdo->prepare($sql);
//$stmt->bindParam(':doNo', $doNo);
$stmt->execute();
//$row_count = $stmt->rowCount();	

$fp = fopen('php://output', 'w');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    fputcsv($fp,array_keys($row));
    while ($row) {
        fputcsv($fp,array_values($row));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>