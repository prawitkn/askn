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
$prodId = (isset($_GET['prodId'])?$_GET['prodId']:'');
$prodCode = (isset($_GET['prodCode'])?$_GET['prodCode']:'');

							
$sql = "SELECT count(*) as countTotal
FROM stk_bal sb 
INNER JOIN product prd on prd.id=sb.prodId  ";
if($prodCode<>""){ $sql .= " AND prd.code like '%".$prodCode."%' ";	}
$sql.="
WHERE 1 ";
if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}
if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}	
if($prodId<>""){ $sql .= " AND prodId=$prodId ";	}	

$result = mysqli_query($link, $sql);
$countTotal = mysqli_fetch_assoc($result);

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'Update Date : '.date('Y-m-d H:m:s'));
		
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A2', 'Barcode')
		->setCellValue('B2', 'MFD')
		->setCellValue('C2', 'Grade')
		->setCellValue('D2', 'Grade Type')
		->setCellValue('E2', 'Qty')
		->setCellValue('F2', 'WH Remark')
		->setCellValue('G2', 'Shelf')
		->setCellValue('H2', 'Receive No.');
		
	$sql = "
	SELECT itm.prodItemId, itm.prodCodeId, itm.barcode, itm.issueDate, itm.qty, itm.grade, itm.gradeTypeId, itm.remarkWh
	,REPLACE(itm.`barcode`, '-', '') as barcodeId 
	,prd.id as prodId, prd.code as prodCode
	,pigt.Name as gradeTypeName 
	,ws.name as shelfName 
	,hdr.rcNo  
	FROM `receive` hdr 
	INNER JOIN receive_detail dtl on dtl.rcNo=hdr.rcNo  
	INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
	INNER JOIN product prd ON prd.id=itm.prodCodeId ";
	if($prodCode<>""){ $sql .= " AND prd.code like '%".$prodCode."%' ";	}	
	$sql .= "
	LEFT JOIN product_item_grade_type pigt ON pigt.Id=itm.gradeTypeId 
	LEFT JOIN wh_shelf_map_item smi ON smi.recvProdId=dtl.Id
	LEFT JOIN wh_shelf ws ON ws.Id=smi.shelfId 
	WHERE 1=1
	AND hdr.statusCode='P' 	
	AND dtl.statusCode='A' 
	AND hdr.toCode IN ('8','E') 
	";
	if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
	if($sloc<>""){ $sql .= " AND hdr.toCode='$sloc' ";	}
	if($catCode<>""){ $sql .= " AND prd.catCode='$catCode' ";	}	
	if($prodId<>""){ $sql .= " AND prd.id='$prodId' ";	}		
	$sql.="ORDER BY prd.code, itm.issueDate, itm.barcode  ";
	$result = mysqli_query($link, $sql);    
	
	$iRow=3; while($row = mysqli_fetch_assoc($result) ){
		$gradeName = '<b style="color: red;">N/A</b>'; 
		switch($row['grade']){
			case 0 : $gradeName = 'A'; break;
			case 1 : $gradeName = '<b style="color: red;">B</b>'; $sumGradeNotOk+=1; break;
			case 2 : $gradeName = '<b style="color: red;">N</b>'; $sumGradeNotOk+=1; break;
			default : 
				$gradeName = '<b style="color: red;">N/a</b>'; $sumGradeNotOk+=1;
		} 
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['barcode'])
		->setCellValue('B'.$iRow, $row['issueDate'])
		->setCellValue('C'.$iRow, $gradeName)
		->setCellValue('D'.$iRow, $row['gradeTypeName'])
		->setCellValue('E'.$iRow, $row['qty'])
		->setCellValue('F'.$iRow, $row['remarkWh'])
		->setCellValue('G'.$iRow, $row['shelfName'])
		->setCellValue('H'.$iRow, $row['rcNo']);
		$iRow+=1;
	}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Data');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="availableItem.xlsx"');
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