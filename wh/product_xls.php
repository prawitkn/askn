<?php
include 'session.php';
include 'inc_helper.php'; 

require_once '../phpexcel/Classes/PHPExcel.php';

date_default_timezone_set("Asia/Bangkok");

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Prawit Khamnet")
        ->setTitle("WMS")
        ->setSubject("Sales Order Report")
        ->setDescription("Excel File")
        ->setKeywords("Sales Order")
        ->setCategory("Sales Order");
		
$sql = "  
SELECT `id`, `code`, `catCode`, `name`, `uomCode`, `sourceTypeCode`, `appCode`, `photo`, `specFile`, `description`, `statusCode` FROM `product`   ";
//$sql.="LIMIT $start, $rows ";
//$result = mysqli_query($link, $sql);   
$stmt = $pdo->prepare($sql);		
$stmt->execute();
$countTotal = $stmt->rowCount();

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B1', 'Update Date : '.date('Y-m-d H:i:s'));
		
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A2', 'Product ID')
		->setCellValue('B2', 'Product Code')
		->setCellValue('C2', 'Category Code')
		->setCellValue('D2', 'Product Name')
		->setCellValue('E2', 'Unit of Measure')
		->setCellValue('F2', 'Source Type Code')
		->setCellValue('G2', 'MKT GROUP')
		->setCellValue('H2', 'Picture File')
		->setCellValue('I2', 'spec File')
		->setCellValue('J2', 'Description')
		->setCellValue('K2', 'Status')
		;
		//->setCellValue('D2', 'Available')

	$iRow=3; while($row = $stmt->fetch() ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['id'])
		->setCellValue('B'.$iRow, $row['code'])
		->setCellValue('C'.$iRow, $row['catCode'])
		->setCellValue('D'.$iRow, $row['name'])
		->setCellValue('E'.$iRow, $row['uomCode'])
		->setCellValue('F'.$iRow, $row['sourceTypeCode'])
		->setCellValue('G'.$iRow, $row['appCode'])
		->setCellValue('H'.$iRow, $row['photo'])
		->setCellValue('I'.$iRow, $row['specFile'])
		->setCellValue('J'.$iRow, $row['description'])
		->setCellValue('K'.$iRow, $row['statusCode']);
		//		->setCellValue('D'.$iRow, $row['balance']-$row['book'] )
		$iRow+=1;
	}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Data');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="product.xlsx"');
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