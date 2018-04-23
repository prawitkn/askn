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
		
$dateFrom = (isset($_GET['dateFrom'])?$_GET['dateFrom']:'');
$dateTo = (isset($_GET['dateTo'])?$_GET['dateTo']:'');
$isClose = (isset($_GET['isClose'])?$_GET['isClose']:'');

$dateFromYmd=$dateToYmd="";
if($dateFrom<>""){ $dateFromYmd = to_mysql_date($_GET['dateFrom']);	}
if($dateFrom<>""){ $dateToYmd = to_mysql_date($_GET['dateTo']);	}


							
$sql = "SELECT count(*) as countTotal
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
WHERE 1 
AND sh.statusCode='P' ";				
if($isClose<>""){ $sql .= " AND sh.isClose='$isClose' ";	}
if($dateFrom<>""){ $sql .= " AND sh.saleDate>='$dateFromYmd' ";	}
if($dateTo<>""){ $sql .= " AND sh.saleDate<='$dateToYmd' ";	}

$result = mysqli_query($link, $sql);
$countTotal = mysqli_fetch_assoc($result);

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'SO No.')
		->setCellValue('B1', 'Sales Date')
		->setCellValue('C1', 'Customer')
		->setCellValue('D1', 'Salesman')
		->setCellValue('E1', 'Is Closed');
		
	$sql = "SELECT sh.`soNo`, sh.`poNo`, sh.`saleDate`, sh.`custId`
	, ct.name as custName
	, sh.`smId`
	, sm.name as smName 
	, sh.`statusCode`, sh.`isClose` 
	FROM `sale_header` sh
	INNER JOIN customer ct on ct.id=sh.custId ";
	switch($s_userGroupCode){
		case 'sales' : 
			 $sql .= " AND ct.smId=$s_smId ";
			break;
		case 'salesAdmin' :
			//$sql .= " AND ct.smId=$s_smId ";
			break;
		default :
	}
	$sql .= "LEFT JOIN salesman sm on sm.id=sh.smId 
	WHERE 1 
	AND sh.statusCode='P' ";
	if($isClose<>""){ $sql .= " AND sh.isClose='$isClose' ";	}
	if($dateFrom<>""){ $sql .= " AND sh.saleDate>='$dateFromYmd' ";	}
	if($dateTo<>""){ $sql .= " AND sh.saleDate<='$dateToYmd' ";	}				
	$sql .= "ORDER BY soNo desc
	
	";
	$result = mysqli_query($link, $sql);     
	
	$iRow=2; while($row = mysqli_fetch_assoc($result) ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['soNo'])
		->setCellValue('B'.$iRow, $row['saleDate'])
		->setCellValue('C'.$iRow, $row['custName'])
		->setCellValue('D'.$iRow, $row['smName'])
		->setCellValue('E'.$iRow, $row['isClose']);
		
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