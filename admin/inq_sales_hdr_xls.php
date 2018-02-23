<?php
include 'session.php';
include 'inc_helper.php'; 

require_once '../phpexcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Prawit Khamnet")
        ->setTitle("WMS")
        ->setSubject("Sales Order Report")
        ->setDescription("Excel File")
        ->setKeywords("Sales Order")
        ->setCategory("Sales Order");
		
$dateFrom = (isset($_GET['dateFrom'])?to_mysql_date($_GET['dateFrom']):'');
$dateTo = (isset($_GET['dateTo'])?to_mysql_date($_GET['dateTo']):'');
$custId = (isset($_GET['custId'])?$_GET['custId']:'');
$smId = (isset($_GET['smId'])?$_GET['smId']:'');
$statusCode = (isset($_GET['statusCode'])?$_GET['statusCode']:'');
$search_word = (isset($_GET['search_word'])?$_GET['search_word']:'');

$sql = "SELECT count(*) as countTotal
FROM `sale_header` sh
INNER JOIN customer ct on ct.id=sh.custId ";
switch($s_userGroupCode){
	case 'sales' :
		 $sql .= " AND ct.smId=$s_smId ";
		break;
	case 'salesAdmin' :
		$sql .= " AND ct.smAdmId=$s_smId ";
		break;
	default :
}
$sql .= "LEFT JOIN salesman sm on sm.id=sh.smId 
WHERE 1 ";
if($search_word<>""){ $sql .= "and (ct.code like '%".$search_word."%' OR ct.name like '%".$search_word."%') "; }
if($smId<>""){ $sql .= " AND sh.smId=$smId ";	}
if($custId<>""){ $sql .= " AND sh.custId=$custId ";	}
if($statusCode<>""){ $sql .= " AND sh.statusCode='$statusCode' ";	}
if($dateFrom<>""){ $sql .= " AND sh.saleDate>='$dateFrom' ";	}
if($dateTo<>""){ $sql .= " AND sh.saleDate<='$dateTo' ";	}
switch($s_userGroupCode){
	case 'sales' :
		 $sql .= " AND sh.smId=$s_smId ";
		break;
	case 'salesAdmin' :
			//$sql .= " AND ct.smAdmId=$s_smId ";
			break;
		default :
	default :
}
$result = mysqli_query($link, $sql);
$countTotal = mysqli_fetch_assoc($result);

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'SO No.')
		->setCellValue('B1', 'PO No.')
		->setCellValue('C1', 'Sales Date')
		->setCellValue('D1', 'Customer')
		->setCellValue('E1', 'Salesman')
		->setCellValue('F1', 'Status')
		->setCellValue('G1', 'Is Closed');
	
	$sql = "SELECT sh.`soNo`, sh.`poNo`, sh.`saleDate`, sh.`custId`
	, ct.name as custName
	, sh.`smId`
	, sm.name as smName 
	, sh.`netTotal`, sh.`statusCode`, sh.`isClose` 
	FROM `sale_header` sh
	INNER JOIN customer ct on ct.id=sh.custId ";
	switch($s_userGroupCode){
		case 'sales' :
			 $sql .= " AND ct.smId=$s_smId ";
			break;
		case 'salesAdmin' :
			//$sql .= " AND ct.smAdmId=$s_smId ";
			break;
		default :
	}
	$sql .= "LEFT JOIN salesman sm on sm.id=sh.smId 
	WHERE 1 ";
	if($search_word<>""){ $sql .= "and (ct.code like '%".$search_word."%' OR ct.name like '%".$search_word."%') "; }
	if($smId<>""){ $sql .= " AND sh.smId=$smId ";	}
	if($custId<>""){ $sql .= " AND sh.custId=$custId ";	}
	if($statusCode<>""){ $sql .= " AND sh.statusCode='$statusCode' ";	}
	if($dateFrom<>""){ $sql .= " AND sh.saleDate>='$dateFrom' ";	}
	if($dateTo<>""){ $sql .= " AND sh.saleDate<='$dateTo' ";	}
	switch($s_userGroupCode){
		case 'sales' :
			 $sql .= " AND sh.smId=$s_smId ";
			break;
		default :
	}
	$sql.="ORDER BY soNo desc
	";
	
	$result = mysqli_query($link, $sql);      
	
	$iRow=2; while($row = mysqli_fetch_assoc($result) ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$iRow, $row['soNo'])
		->setCellValue('B'.$iRow, $row['poNo'])
		->setCellValue('C'.$iRow, $row['saleDate'])
		->setCellValue('D'.$iRow, $row['custName'])
		->setCellValue('E'.$iRow, $row['smName'])
		->setCellValue('F'.$iRow, $row['statusCode'])
		->setCellValue('G'.$iRow, $row['isClose']);
		
		$iRow+=1;
	}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Data');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="salesHdr.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_clean();
$objWriter->save('php://output');
exit;