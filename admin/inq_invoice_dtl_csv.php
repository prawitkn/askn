<?php
	include 'inc_helper.php'; 
	include 'session.php';

	header("Content-Type: text/csv");
	header("Content-Disposition: attachment; filename=invoice_prod.csv");
	
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
	$statusCode = (isset($_GET['statusCode'])?$_GET['statusCode']:'');
	$search_word = (isset($_GET['search_word'])?$_GET['search_word']:'');
	
	$sqlSearch = "";
	$url="inq_invoice.php";
	if($search_word<>""){ $sqlSearch = "and (prodName like '%".$search_word."%' OR prodNameNew like '%".$search_word."%') "; }
	if($smCode<>""){ $sqlSearch .= " AND sh.smCode='$smCode' ";	}
	if($custCode<>""){ $sqlSearch .= " AND sh.custCode='$custCode' ";	}
	if($statusCode<>""){ $sqlSearch .= " AND sh.statusCode='$statusCode' ";	}
	if($dateFrom<>""){ $sqlSearch .= " AND sh.invoiceDate>='$dateFrom' ";	}
	if($dateTo<>""){ $sqlSearch .= " AND sh.invoiceDate<='$dateTo' ";	}
	
	$sql = "SELECT ih.invNo, ih.doNo, sh.`soNo`, sh.`poNo`, ih.`invoiceDate`, ih.`custCode`
	, ct.custName
	, ih.`smCode`
	, sm.name as smName 
	, ih.`totalExcVat`, ih.`statusCode` 
	, id.prodCode
	, pd.prodName 
	, id.salesPrice, id.qty, id.discAmount, id.netTotal as prodNetTotal
	FROM `invoice_header` ih
	LEFT JOIN `delivery_header` dh on dh.doNo=ih.doNo 
	LEFT JOIN `sale_header` sh on sh.soNo=dh.soNo 
	LEFT JOIN customer ct on ct.code=ih.custCode ".$sqlRole."
	LEFT JOIN salesman sm on sm.code=ih.smCode ".$sqlRoleSm."
	LEFT JOIN invoice_detail id on ih.invNo=id.invNo 
	LEFT JOIN product pd on pd.code=id.prodCode 
	WHERE 1 "
	.$sqlSearch."
	ORDER BY invNo desc
	"; //echo $sql;
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