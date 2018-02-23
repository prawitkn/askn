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

$sql = "SELECT count(*) as countTotal
FROM `sale_header` sh
INNER JOIN sale_detail sd ON sd.soNo=sh.soNo 
INNER JOIN customer ct on ct.id=sh.custId 
LEFT JOIN salesman sm on sm.id=sh.smId 
WHERE 1 ";
if($dateFrom<>""){ $sql .= " AND sd.deliveryDate='$dateFrom' ";	}
$sql .= "ORDER BY soNo desc
";
$result = mysqli_query($link, $sql);      
$countTotal = mysqli_fetch_assoc($result);

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'SO No.')
		->setCellValue('C1', 'Sales Date')
		->setCellValue('D1', 'Customer')
		->setCellValue('E1', 'Salesman')
		->setCellValue('F1', 'Status')
		->setCellValue('G1', 'Is Closed')
		->setCellValue('H1', 'Delivery Date');
	
	 $sql = "SELECT DISTINCT sh.`soNo`, sh.`poNo`, sh.`saleDate`, sh.`custId`
	, ct.name as custName
	, sh.`smId`
	, sm.name as smName 
	, sh.`netTotal`, sh.`statusCode`, sh.`isClose` 
	, sd.deliveryDate 
	FROM `sale_header` sh
	INNER JOIN sale_detail sd ON sd.soNo=sh.soNo 
	INNER JOIN customer ct on ct.id=sh.custId 
	LEFT JOIN salesman sm on sm.id=sh.smId 
	WHERE 1 ";
	if($dateFrom<>""){ $sql .= " AND sd.deliveryDate='$dateFrom' ";	}
	$sql .= "ORDER BY soNo desc 
	";
	$result = mysqli_query($link, $sql);             
	
	$iRow=2; while($row = mysqli_fetch_assoc($result) ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$iRow, $row['soNo'])
		->setCellValue('G'.$iRow, $row['deliveryDate']);
		
		$iRow+=1;
	}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('SObyDeli');	


 
	
	

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