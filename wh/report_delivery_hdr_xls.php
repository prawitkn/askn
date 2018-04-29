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
		
$dateFrom = (isset($_GET['dateFrom'])?$_GET['dateFrom']: date('d-m-Y') );
$dateTo = (isset($_GET['dateTo'])?$_GET['dateTo']: date('d-m-Y') );

$dateFrom = str_replace('/', '-', $dateFrom);
$dateTo = str_replace('/', '-', $dateTo);
$dateFromYmd=$dateToYmd="";
if($dateFrom<>""){ $dateFromYmd = date('Y-m-d', strtotime($dateFrom));	}
if($dateTo<>""){ $dateToYmd =  date('Y-m-d', strtotime($dateTo));	}
							
$sql = "SELECT count(*) as countTotal
	FROM `delivery_header` hdr
	left join customer cm on cm.id=hdr.custId 
	left join salesman sm on sm.id=hdr.smId
	left join wh_user d on hdr.createById=d.userId
	WHERE 1 
	AND hdr.statusCode='P' ";
	if($dateFrom<>""){ $sql .= " AND hdr.deliveryDate>='$dateFromYmd' ";	}
	if($dateTo<>""){ $sql .= " AND hdr.deliveryDate<='$dateToYmd' ";	}

$result = mysqli_query($link, $sql);
$countTotal = mysqli_fetch_assoc($result);

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'Delivery No.')
		->setCellValue('B1', 'Date')
		->setCellValue('C1', 'Customer Code')
		->setCellValue('D1', 'Customer Name')
		->setCellValue('E1', 'So. No.')
		->setCellValue('F1', 'Salesman Code')
		->setCellValue('G1', 'Salesman Name');
	$sql = "
	SELECT hdr.`doNo`, hdr.`soNo`, hdr.`ppNo`, hdr.`deliveryDate`, hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createById`
	,cm.code as custCode , cm.name as custName, cm.addr1 , cm.addr2 , cm.addr3 , cm.zipcode, cm.tel, cm.fax
	,sm.code as smCode, sm.name as smName
	,d.userFullname as createByName
	FROM `delivery_header` hdr
	left join customer cm on cm.id=hdr.custId 
	left join salesman sm on sm.id=hdr.smId
	left join wh_user d on hdr.createById=d.userId
	WHERE 1 
	AND hdr.statusCode='P' ";
	if($dateFrom<>""){ $sql .= " AND hdr.deliveryDate>='$dateFromYmd' ";	}
	if($dateTo<>""){ $sql .= " AND hdr.deliveryDate<='$dateToYmd' ";	}
	$sql .="ORDER BY hdr.createTime DESC ";
	$result = mysqli_query($link, $sql);     
	
	$iRow=2; while($row = mysqli_fetch_assoc($result) ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['doNo'])
		->setCellValue('B'.$iRow, $row['deliveryDate'])
		->setCellValue('C'.$iRow, $row['custCode'])
		->setCellValue('D'.$iRow, $row['custName'])
		->setCellValue('E'.$iRow, $row['soNo'])
		->setCellValue('F'.$iRow, $row['smCode'])
		->setCellValue('G'.$iRow, $row['smName']);
		
		$iRow+=1;
	}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Data');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="deivery.xlsx"');
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