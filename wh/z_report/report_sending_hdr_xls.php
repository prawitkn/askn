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
FROM `send` hdr
	LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
	LEFT JOIN sloc tsl on hdr.toCode=tsl.code
	LEFT JOIN user cu on hdr.createByID=cu.userId 
	LEFT JOIN user fu on hdr.confirmById=fu.userId
	LEFT JOIN user pu on hdr.approveById=pu.userId  
	WHERE 1 ";
	switch($s_userGroupCode){ 
		case 'whOff' :  case 'whSup' : 
		case 'pdOff' :  case 'pdSup' :
				$sql .= "AND hdr.fromCode='".$s_userDeptCode."' ";
			break;
		default : //case 'it' : case 'admin' : 
	  }
	if($dateFrom<>""){ $sql .= " AND hdr.sendDate>='$dateFromYmd' ";	}
	if($dateTo<>""){ $sql .= " AND hdr.sendDate<='$dateToYmd' ";	}				  
	$sql .= "AND hdr.statusCode='P' 

	ORDER BY hdr.createTime DESC ";

$result = mysqli_query($link, $sql);
$countTotal = mysqli_fetch_assoc($result);

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'Sending No.')
		->setCellValue('B1', 'Date')
		->setCellValue('C1', 'From')
		->setCellValue('D1', 'To');
		
	$sql = "SELECT hdr.`sdNo`, hdr.`refNo`, hdr.`sendDate`, hdr.`fromCode`, hdr.`toCode`, hdr.`remark`, hdr.`statusCode`
	, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`
	, fsl.name as fromName, tsl.name as toName 
	, cu.userFullname as createByName, fu.userFullname as confirmByName, pu.userFullname as approveByName 
	FROM `send` hdr
	LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
	LEFT JOIN sloc tsl on hdr.toCode=tsl.code
	LEFT JOIN user cu on hdr.createByID=cu.userId 
	LEFT JOIN user fu on hdr.confirmById=fu.userId
	LEFT JOIN user pu on hdr.approveById=pu.userId  
	WHERE 1 ";
	switch($s_userGroupCode){ 
		case 'whOff' :  case 'whSup' : 
		case 'pdOff' :  case 'pdSup' :
				$sql .= "AND hdr.fromCode='".$s_userDeptCode."' ";
			break;
		default : //case 'it' : case 'admin' : 
	  }
	if($dateFrom<>""){ $sql .= " AND hdr.sendDate>='$dateFromYmd' ";	}
	if($dateTo<>""){ $sql .= " AND hdr.sendDate<='$dateToYmd' ";	}				  
	$sql .= "AND hdr.statusCode='P' 

	ORDER BY hdr.createTime DESC ";
	$result = mysqli_query($link, $sql);     
	
	$iRow=2; while($row = mysqli_fetch_assoc($result) ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['sdNo'])
		->setCellValue('B'.$iRow, $row['sendDate'])
		->setCellValue('C'.$iRow, $row['fromName'])
		->setCellValue('D'.$iRow, $row['toName']);
		
		$iRow+=1;
	}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Data');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sending.xlsx"');
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