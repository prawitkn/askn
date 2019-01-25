<?php
include 'session.php';
include 'inc_helper.php'; 

require_once '../phpexcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();


		
$dateFrom = (isset($_GET['dateFrom'])?$_GET['dateFrom']: date('d-m-Y') );

$dateFrom = str_replace('/', '-', $dateFrom);
$dateFromYmd=$dateToYmd="";
if($dateFrom<>""){ $dateFromYmd = date('Y-m-d', strtotime($dateFrom));	}

$sql = "
	SELECT hdr.`soNo`, hdr.`approveTime`,
	cust.name as custName,
	prd.code as prodCode
	 ,dtl.id as saleItemId, dtl.deliveryDate 
	, sum(dtl.qty) as sumQty 
	,IFNULL((SELECT sum(xd.qty) FROM picking xh 
			LEFT JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
			WHERE xh.statusCode='P' 
			AND xh.isFinish='N' 
			AND xh.soNo=hdr.soNo 
			AND xd.saleItemId=dtl.id
			GROUP BY xd.saleItemId),0) as sumPickedQty  
	,IFNULL((SELECT sum(xd.qty) FROM picking xh 
			LEFT JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
			WHERE xh.statusCode='P' 
			AND xh.isFinish='Y' 
			AND xh.soNo=hdr.soNo 
			AND xd.saleItemId=dtl.id
			GROUP BY xd.saleItemId),0) as sumSentQty 
	FROM `sale_header` hdr 
	INNER JOIN sale_detail dtl on dtl.soNo=hdr.soNo AND dtl.deliveryDate='$dateFromYmd' 
	INNER JOIN customer cust ON cust.id=hdr.custId 
	INNER JOIN product prd ON prd.id=dtl.prodId ";
	switch($s_userGroupCode){
		case 'pdOff' : case 'pdSup' :
			$sql .= " AND prd.catCode= CASE :toCode WHEN '4' THEN '70' WHEN '5' THEN '71' WHEN '6' THEN '72' END ";
			break;
		default : // it, admin
	}
	$sql.="
	WHERE 1=1
	AND hdr.statusCode='P' 
	";		
	//AND hdr.isClose='N' 
	$sql.="GROUP BY hdr.`soNo`, hdr.`approveTime`, dtl.`id`, dtl.`deliveryDate`, cust.name,prd.code ";
	$sql.="ORDER BY hdr.soNo, dtl.deliveryDate, prd.code ";	
	//$sql.="LIMIT $start, $rows ";
	$stmt = $pdo->prepare($sql);
	switch($s_userGroupCode){
		case 'pdOff' : case 'pdSup' :
			$stmt->bindParam(':toCode', $s_userDeptCode);
			break;
		default : // it, admin
	}						
	$stmt->execute();	


$countTotal = $stmt->rowCount();

if($countTotal>0){
	$iRow=1;
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$iRow, 'Update Date : ')
		->setCellValue('B'.$iRow, date('d.m.Y H:i:s') );
	$iRow+=1;

	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$iRow, 'SO No.')
		->setCellValue('B'.$iRow, 'Update Date')
		->setCellValue('C'.$iRow, 'Customer')
		->setCellValue('D'.$iRow, 'Picked')
		->setCellValue('E'.$iRow, 'Sent')
		->setCellValue('F'.$iRow, 'Order');
	$iRow+=1;
	
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
	WHERE 10
	AND sh.statusCode='P' 
	AND sh.isClose<>'Y' 	";
	if($dateFromYmd<>""){ $sql .= " AND sd.deliveryDate='$dateFromYmd' ";	}
	$sql .= "ORDER BY soNo desc 
	";
	$result = mysqli_query($link, $sql);             

	$soPrev=""; $itemStr=""; while ($row = $stmt->fetch()) { 
			$deliveryDate=date('d/M', strtotime($row['deliveryDate']) );

   			if($soPrev<>$row['soNo']){ 
	   			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$iRow, $row['soNo'])
					->setCellValue('B'.$iRow, date('d/M H:i', strtotime($row['approveTime'])) )
					->setCellValue('C'.$iRow, $row['custName']);
					
					$iRow+=1;

				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$iRow, '')
					->setCellValue('B'.$iRow, $deliveryDate )
					->setCellValue('C'.$iRow, $row['prodCode'])
					->setCellValue('D'.$iRow, $row['sumPickedQty'])
					->setCellValue('E'.$iRow, $row['sumSentQty'])
					->setCellValue('F'.$iRow, $row['sumQty']);
					
					$iRow+=1;			
	   		 }else{ 
	   			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$iRow, '')
					->setCellValue('B'.$iRow, $deliveryDate )
					->setCellValue('C'.$iRow, $row['prodCode'])
					->setCellValue('D'.$iRow, $row['sumPickedQty'])
					->setCellValue('E'.$iRow, $row['sumSentQty'])
					->setCellValue('F'.$iRow, $row['sumQty']);
					
					$iRow+=1;
	   		} // end if so header.             
	 
		$soPrev=$row['soNo'];
	}  //end while
} //end if row count ;


$filename="SO by Delivery Date on ".date('d.m.Y', strtotime($dateFrom));
// Set document properties
$objPHPExcel->getProperties()->setCreator($s_userFullname)
        ->setTitle("Sales Order by Delivery Date")
        ->setSubject("Sales Order by Delivery Date")
        ->setDescription("Sales Order by Delivery Date")
        ->setKeywords("Sales Order by Delivery Date")
        ->setCategory("Sales Order by Delivery Date");


// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Sales Order by Delivery Date');	


 
	
	

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
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