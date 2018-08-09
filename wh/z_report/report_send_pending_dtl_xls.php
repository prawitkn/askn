<?php
include 'session.php';
include 'inc_helper.php'; 

require_once '../phpexcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$dateFrom = (isset($_GET['dateFrom'])?$_GET['dateFrom']: date('d-m-Y') );
$dateTo = (isset($_GET['dateTo'])?$_GET['dateTo']: date('d-m-Y') );

$dateFrom = str_replace('/', '-', $dateFrom);
$dateTo = str_replace('/', '-', $dateTo);
$dateFromYmd=$dateToYmd="";
if($dateFrom<>""){ $dateFromYmd = date('Y-m-d', strtotime($dateFrom));	}
if($dateTo<>""){ $dateToYmd =  date('Y-m-d', strtotime($dateTo));	}

	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'SO No.')
		->setCellValue('B1', 'Send Date')
		->setCellValue('C1', 'From Code')
		->setCellValue('D1', 'From Name')
		->setCellValue('E1', 'To Code')
		->setCellValue('F1', 'To Name')
		->setCellValue('G1', 'Barcode')
		->setCellValue('H1', 'Quantity');
		
	$sql = "SELECT hdr.sendId as 'sdNo', hdr.`issueDate` as 'sendDate', hdr.`fromCode`, hdr.`toCode`
	, hdr.`createTime`, hdr.`createByID`
	, itm.prodCodeId, itm.barcode, itm.qty, prd.code as prodCode, prd.uomCode
	, fsl.name as fromName, tsl.name as toName 
	, cu.userFullname as createByName
	FROM `send_mssql` hdr
	INNER JOIN `send_detail_mssql` dtl ON dtl.sendId=hdr.sendId 
		AND dtl.productItemId NOT IN (SELECT x.prodItemId FROM send_detail x) 
	INNER JOIN `product_item` itm ON itm.prodItemId=dtl.productItemId 
	INNER JOIN `product` prd ON prd.id=itm.prodCodeId  
	LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
	LEFT JOIN sloc tsl on hdr.toCode=tsl.code
	LEFT JOIN user cu on hdr.createByID=cu.userId 
	WHERE 1 ";
	switch($s_userGroupCode){ 
		case 'whOff' :  case 'whSup' : 
		case 'pdOff' :  case 'pdSup' :
				$sql .= "AND hdr.fromCode='".$s_userDeptCode."' ";
			break;
		default : //case 'it' : case 'admin' : 
	  }
	if($dateFrom<>""){ $sql .= " AND hdr.issueDate>='$dateFromYmd' ";	}
	if($dateTo<>""){ $sql .= " AND hdr.issueDate<='$dateToYmd' ";	}				  
	$sql .= "GROUP BY hdr.sendId , hdr.`issueDate` , hdr.`fromCode`, hdr.`toCode`
	, hdr.`createTime`, hdr.`createByID`, itm.prodItemId ";
	$sql .= "ORDER BY hdr.sendId , itm.barcode ";
	$stmt = $pdo->prepare($sql);
	//$stmt->bindParam(':doNo', $doNo);
	$stmt->execute();
	 

if($stmt->rowCount()>0){
	$iRow=2; while($row = $stmt->fetch() ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$iRow, $row['sdNo'])
		->setCellValue('B'.$iRow, $row['sendDate'])
		->setCellValue('C'.$iRow, $row['fromCode'])
		->setCellValue('D'.$iRow, $row['fromName'])
		->setCellValue('E'.$iRow, $row['toCode'])
		->setCellValue('F'.$iRow, $row['toName'])
		->setCellValue('G'.$iRow, $row['barcode'])
		->setCellValue('H'.$iRow, $row['qty']);
		
		$iRow+=1;
	}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Data');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Send Pending.xlsx"');
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