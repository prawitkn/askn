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
		
$dateFrom=(isset($_GET['dateFrom'])? $_GET['dateFrom'] : '01/01/1900' );
$dateFrom=str_replace('/', '-', $dateFrom);
$dateFrom=date('Y-m-d', strtotime($dateFrom));

$dateTo=(isset($_GET['dateTo'])? $_GET['dateTo'] : '01/01/1900' );
$dateTo=str_replace('/', '-', $dateTo);
$dateTo=date('Y-m-d', strtotime($dateTo));

$prodCode = (isset($_GET['prodCode'])?$_GET['prodCode']:'');
$prodId = (isset($_GET['prodId']) ?$_GET['prodId']:'');
if($prodCode=="") $prodId="";

							
$sql = "SELECT hdr.soNo, hdr.deliveryDate
, dtl.prodId, prd.code as prodCode
, sum(dtl.qty) as sumQty
, (SELECT IFNULL(sum(doDtl.qty),0) FROM delivery_header doHdr
	INNER JOIN delivery_detail doDtl ON doDtl.doNo=doHdr.doNo
	INNER JOIN product_item itm ON itm.prodItemId=doDtl.prodItemId 
	WHERE 1=1
	AND doHdr.soNo=hdr.soNo
	AND itm.prodId=dtl.prodId) as sumSentDtl
FROM `sale_header` hdr
INNER JOIN sale_detail dtl ON dtl.soNo=hdr.soNo
LEFT JOIN product prd ON prd.id=dtl.prodId ";				

$sql .= "WHERE 1 
AND hdr.statusCode='P' 
AND hdr.isClose='N' ";
if($dateFrom<>""){ $sql .= " AND hdr.saleDate>='$dateFrom' ";	}
if($dateTo<>""){ $sql .= " AND hdr.saleDate<='$dateTo' ";	}	
if($prodId<>""){ $sql .= " AND dtl.prodId=$prodId ";	}						
$sql .= "
group by hdr.soNo, dtl.prodId, prd.code, hdr.deliveryDate ";
$sql.="
ORDER BY soNo desc
";
$stmt = $pdo->prepare($sql);		
$stmt->execute();       
$countTotal = $stmt->rowCount();

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'Product Code.')
		->setCellValue('B1', 'SO No.')
		->setCellValue('C1', 'Delivery Date.')
		->setCellValue('D1', 'Order Qty')
		->setCellValue('E1', 'Sent QTy')
		->setCellValue('F1', 'Pending Qty');
		
	/*$sql = "SELECT hdr.soNo, hdr.deliveryDate
	, dtl.prodId, prd.code as prodCode
	, sum(dtl.qty) as sumQty
	, (SELECT IFNULL(sum(doDtl.qty),0) FROM delivery_header doHdr
		INNER JOIN delivery_detail doDtl ON doDtl.doNo=doHdr.doNo
		INNER JOIN product_item itm ON itm.prodItemId=doDtl.prodItemId 
		WHERE 1=1
		AND doHdr.soNo=hdr.soNo
		AND itm.prodId=dtl.prodId) as sumSentDtl
	FROM `sale_header` hdr
	INNER JOIN sale_detail dtl ON dtl.soNo=hdr.soNo
	LEFT JOIN product prd ON prd.id=dtl.prodId ";				
	
	$sql .= "WHERE 1 
	AND hdr.statusCode='P' 
	AND hdr.isClose='N' ";
	if($dateFrom<>""){ $sql .= " AND hdr.saleDate>='$dateFromYmd' ";	}
	if($dateTo<>""){ $sql .= " AND hdr.saleDate<='$dateToYmd' ";	}				
	$sql .= "
	group by hdr.soNo, dtl.prodId, hdr.deliveryDate ";
	$sql.="
	ORDER BY soNo desc
	";
	$result = mysqli_query($link, $sql); */
	
	$iRow=2; $prodId=0; while($row = $stmt->fetch() ){
	// Add some data
		$pendingQty=$row['sumQty']-$row['sumSentDtl'];
		if($prodId<>$row['prodId']){
			$objPHPExcel->setActiveSheetIndex(0)		
				->setCellValue('A'.$iRow, $row['prodCode'])
				->setCellValue('B'.$iRow, $row['soNo'])
				->setCellValue('C'.$iRow, to_thai_date($row['deliveryDate']))
				->setCellValue('D'.$iRow, $row['sumQty'])
				->setCellValue('E'.$iRow, $row['sumSentDtl'])
				->setCellValue('F'.$iRow, $pendingQty);
				
				$iRow+=1;
			//
			$prodId=$row['prodId'];
		}else{
			$objPHPExcel->setActiveSheetIndex(0)		
				->setCellValue('A'.$iRow, "")
				->setCellValue('B'.$iRow, $row['soNo'])
				->setCellValue('C'.$iRow, to_thai_date($row['deliveryDate']))
				->setCellValue('D'.$iRow, $row['sumQty'])
				->setCellValue('E'.$iRow, $row['sumSentDtl'])
				->setCellValue('F'.$iRow, $pendingQty);
				
				$iRow+=1;
		}
	}//end while
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Data');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="salesPendingByProduct.xlsx"');
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