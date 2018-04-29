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
	FROM `receive` hdr
	LEFT JOIN sloc fsl on hdr.fromCode=fsl.code 
	LEFT JOIN sloc tsl on hdr.toCode=tsl.code
	left join user d on hdr.createByID=d.userID
	WHERE 1 
	AND hdr.statusCode='P' ";
	switch($s_userGroupCode){ 
		case 'whOff' :
		case 'whSup' :
			$sql .= "AND hdr.toCode='8' ";
			break;
		case 'pdOff' :
		case 'pdSup' :
			$sql .= "AND hdr.toCode=:s_userDeptCode ";
			break;
		default :	// it, admin 
	}	
	if($dateFrom<>""){ $sql .= " AND hdr.receiveDate>='$dateFromYmd' ";	}
	if($dateTo<>""){ $sql .= " AND hdr.receiveDate<='$dateToYmd' ";	}

$result = mysqli_query($link, $sql);
$countTotal = mysqli_fetch_assoc($result);

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'Receving No.')
		->setCellValue('B1', 'Date')
		->setCellValue('C1', 'Sender')
		->setCellValue('D1', 'Receiver')
		->setCellValue('E1', 'Ref. No.')
		->setCellValue('F1', 'Type');
		
	$sql = "SELECT hdr.`rcNo`, hdr.`refNo`, hdr.`receiveDate`, hdr.`fromCode`, hdr.`toCode`, hdr.`remark`, hdr.`sdNo`, hdr.`type`, hdr.`statusCode`, hdr.`createTime`, hdr.`createByID`
	, fsl.name as fromName, tsl.name as toName
	, d.userFullname as createByName
	FROM `receive` hdr
	LEFT JOIN sloc fsl on hdr.fromCode=fsl.code 
	LEFT JOIN sloc tsl on hdr.toCode=tsl.code
	left join user d on hdr.createByID=d.userID
	WHERE 1 
	AND hdr.statusCode='P' ";
	switch($s_userGroupCode){ 
		case 'whOff' :
		case 'whSup' :
			$sql .= "AND hdr.toCode='8' ";
			break;
		case 'pdOff' :
		case 'pdSup' :
			$sql .= "AND hdr.toCode=:s_userDeptCode ";
			break;
		default :	// it, admin 
	}	
	if($dateFrom<>""){ $sql .= " AND hdr.receiveDate>='$dateFromYmd' ";	}
	if($dateTo<>""){ $sql .= " AND hdr.receiveDate<='$dateToYmd' ";	}
	$sql .="ORDER BY hdr.createTime DESC ";
	$result = mysqli_query($link, $sql);     
	
	$iRow=2; while($row = mysqli_fetch_assoc($result) ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['rcNo'])
		->setCellValue('B'.$iRow, $row['receiveDate'])
		->setCellValue('C'.$iRow, $row['fromName'])
		->setCellValue('D'.$iRow, $row['toName'])
		->setCellValue('E'.$iRow, $row['refNo'])
		->setCellValue('F'.$iRow, $row['Type']);
		
		$iRow+=1;
	}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Data');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="receiving.xlsx"');
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