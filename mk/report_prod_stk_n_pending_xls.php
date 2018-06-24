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
		
$search_word = (isset($_GET['search_word'])?$_GET['search_word']:'');
$sloc = (isset($_GET['sloc'])?$_GET['sloc']:'');
$catCode = (isset($_GET['catCode'])?$_GET['catCode']:'');

							
$sql = "SELECT count(*) as countTotal
FROM stk_bal sb 
INNER JOIN product prd on prd.id=sb.prodId  
WHERE 1 ";
if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}
if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}	

$result = mysqli_query($link, $sql);
$countTotal = mysqli_fetch_assoc($result);

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'Update Date : '.date('Y-m-d H:m:s'));
		
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A2', 'Product Code')
		->setCellValue('B2', 'SLOC')
		->setCellValue('C2', 'Category')
		->setCellValue('D2', 'Balance')
		->setCellValue('E2', 'Pending');
		//->setCellValue('F2', 'Delivery')
		//->setCellValue('G2', 'Balance');
	
		
	$sql = "SELECT  
	 dtl.prodId as id, prd.code as prodCode, prd.catCode 
	, sum(dtl.qty) as sumQty
	, (SELECT IFNULL(sum(doDtl.qty),0) FROM delivery_header doHdr
		INNER JOIN delivery_detail doDtl ON doDtl.doNo=doHdr.doNo
		INNER JOIN product_item itm ON itm.prodItemId=doDtl.prodItemId 
		WHERE 1=1
		AND doHdr.statusCode='P' 
		AND doHdr.soNo=hdr.soNo
		AND itm.prodId=dtl.prodId) as sumSentDtl
	,sb.sloc , sb.`balance`
	FROM `sale_header` hdr
	INNER JOIN sale_detail dtl ON dtl.soNo=hdr.soNo
	INNER JOIN stk_bal sb ON sb.prodId=dtl.prodId 
	LEFT JOIN product prd ON prd.id=dtl.prodId 
	WHERE 1=1 
	AND hdr.statusCode='P' 
	AND hdr.isClose='N' ";
	if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}
	if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}	
	if($prodId<>""){ $sql .= " AND dtl.prodId='$prodId' ";	}	
	$sql.="GROUP BY dtl.prodId, prd.code, prd.catCode , sb.sloc , sb.`balance`  ";
	$sql.="ORDER BY prd.code  ";
	$result = mysqli_query($link, $sql);    
	
	$iRow=3; while($row = mysqli_fetch_assoc($result) ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['prodCode'])
		->setCellValue('B'.$iRow, $row['sloc'])
		->setCellValue('C'.$iRow, $row['catCode'])
		->setCellValue('D'.$iRow, $row['balance'])
		->setCellValue('E'.$iRow, $row['sumQty']-$row['sumSentDtl']);
		//->setCellValue('F'.$iRow, $row['delivery'])
		//->setCellValue('G'.$iRow, $row['balance']);
		
		$iRow+=1;
	}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Data');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="stock.xlsx"');
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