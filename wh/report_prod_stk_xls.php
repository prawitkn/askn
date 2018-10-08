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
if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}else{ $sql .= " AND sb.sloc IN ('8','E') "; }
if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}	
if($prodId<>""){ $sql .= " AND prodId=$prodId ";	}	

$result = mysqli_query($link, $sql);
$countTotal = mysqli_fetch_assoc($result);

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'Update Date : '.date('Y-m-d H:m:s'));
		
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A2', 'Product Code')
		->setCellValue('B2', 'Location')
		->setCellValue('C2', 'Category')
		->setCellValue('D2', 'Balance');
	
		
	$sql = "SELECT prd.`id`, prd.`code`, prd.`catCode`, prd.`name`, prd.`name2`, prd.`uomCode`, prd.`packUomCode`
	, prd.`sourceTypeCode`, prd.`appCode`, prd.`description`, prd.`statusCode`
	,sb.sloc, sb.`open`, sb.`produce`, sb.`onway`, sb.`receive`, sb.`send`, sb.`sales`, sb.`delivery`, sb.`balance`
	,IFNULL((SELECT SUM(pDtl.qty) FROM picking pHdr, picking_detail pDtl WHERE pHdr.pickNo=pDtl.pickNo AND pHdr.isFinish='N'  AND pHdr.statusCode<>'X' AND pDtl.prodId=sb.prodId),0) as pick 
	FROM stk_bal sb 
	INNER JOIN product prd on prd.id=sb.prodId  ";
	if($prodCode<>""){ $sql .= " AND prd.code like '%".$prodCode."%' ";	}
	$sql.="
	WHERE 1 ";
	if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
	if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}else{ $sql .= " AND sb.sloc IN ('8','E') "; }		
	if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}	
	if($prodId<>""){ $sql .= " AND prodId=$prodId ";	}	
	$sql.="ORDER BY prd.code ";
	$result = mysqli_query($link, $sql);    
	
	$iRow=3; while($row = mysqli_fetch_assoc($result) ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['code'])
		->setCellValue('B'.$iRow, $row['sloc'])
		->setCellValue('C'.$iRow, $row['catCode'])
		->setCellValue('D'.$iRow, $row['balance']);
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