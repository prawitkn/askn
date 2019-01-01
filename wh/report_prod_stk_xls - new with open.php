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
		
$dateFrom=$dateTo="";
$dateFromYmd=$dateToYmd="";
if(isset($_GET['dateFrom'])){
	$dateFrom=$_GET['dateFrom'];
	$dateArr = explode('/', $dateFrom);
    $dateY = (int)$dateArr[2];
    $dateM = $dateArr[1];
    $dateD = $dateArr[0];
    $dateFromYmd = $dateY . '-' . $dateM . '-' . $dateD;
}else{
	$dateFrom=date('d/m/Y');
	$dateFromYmd=date('Y-m-d');
}
if(isset($_GET['dateTo'])){
	$dateTo=$_GET['dateTo'];
	$dateArr = explode('/', $dateTo);
    $dateY = (int)$dateArr[2];
    $dateM = $dateArr[1];
    $dateD = $dateArr[0];
    $dateToYmd = $dateY . '-' . $dateM . '-' . $dateD;
}else{
	$dateTo=date('d/m/Y');
	$dateToYmd=date('Y-m-d');
}

$search_word = (isset($_GET['search_word'])?trim($_GET['search_word']):'');
$sloc = (isset($_GET['sloc'])?$_GET['sloc']:'8');
$catCode = (isset($_GET['catCode'])?$_GET['catCode']:'');
//$prodId = (isset($_GET['prodId']) ?$_GET['prodId']:'');
$prodCode = (isset($_GET['prodCode'])?trim($_GET['prodCode']):'');

							
$pdo->beginTransaction();

	$sql = "
	CREATE TEMPORARY TABLE tmpStock (
		`prodId` int(11) NOT NULL,
	  `sloc` varchar(10) NOT NULL,
	  `openAcc` decimal(10,2) NOT NULL,
	  `openTrans` decimal(10,2) NOT NULL,
	  `onway` decimal(10,2) NOT NULL,
	  `receive` decimal(10,2) NOT NULL,
	  `sent` decimal(10,2) NOT NULL,
	  `return` decimal(10,2) NOT NULL,
	  `delivery` decimal(10,2) NOT NULL,
	  `balance` decimal(10,2) NOT NULL,
	  `book` decimal(10,2) NOT NULL,
  	PRIMARY KEY (`prodId`,`sloc`)
)";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

$sql = "
  INSERT INTO tmpStock (prodId, sloc)
  SELECT prd.id, sl.code 
  FROM product prd
  CROSS JOIN sloc sl ON sl.code IN ('8','E')
	";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

//Open
$sql = "UPDATE tmpStock hdr
 ,(SELECT td.prodId, td.sloc, td.balance as sumQty FROM stk_closing_detail td 
  				WHERE td.hdrId=(SELECT th.id FROM stk_closing th 
  								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1) 
  				) as tmp 
  SET hdr.openAcc=tmp.sumQty 
  WHERE hdr.prodId=tmp.prodId AND hdr.sloc=tmp.sloc 
	";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

//Onway
$sql = "UPDATE tmpStock hdr
 ,(SELECT itm.prodCodeId, sh.toCode, SUM(itm.qty) as sumQty FROM product_item itm 
  				INNER JOIN send_detail sd ON sd.prodItemId=itm.prodItemId  
 				INNER JOIN send sh ON sh.sdNo=sd.sdNo AND sh.statusCode='P' AND sh.rcNo IS NULL 
  				GROUP BY itm.prodCodeId, sh.toCode
  				) as tmp 
  SET hdr.onway=tmp.sumQty 
  WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.toCode 
	";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

//Receive
$sql = "UPDATE tmpStock hdr
 ,(SELECT itm.prodCodeId, th.toCode as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
  				INNER JOIN receive_detail td ON td.prodItemId=itm.prodItemId  
 				INNER JOIN receive th ON th.rcNo=td.rcNo AND th.statusCode='P' 
 					AND th.receiveDate > (SELECT th.closingDate FROM stk_closing th 
	          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
  				GROUP BY itm.prodCodeId, th.toCode
  				) as tmp 
  SET hdr.receive=tmp.sumQty 
  WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
	";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

//Sent
$sql = "UPDATE tmpStock hdr
 ,(SELECT itm.prodCodeId, th.fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
  				INNER JOIN send_detail td ON td.prodItemId=itm.prodItemId  
 				INNER JOIN send th ON th.sdNo=td.sdNo AND th.statusCode='P' 
 					AND th.sendDate > (SELECT th.closingDate FROM stk_closing th 
	          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
  				GROUP BY itm.prodCodeId, th.fromCode
  				) as tmp 
  SET hdr.sent=tmp.sumQty 
  WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
	";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

//return
$sql = "UPDATE tmpStock hdr
 ,(SELECT itm.prodCodeId, th.fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
  				INNER JOIN rt_detail td ON td.prodItemId=itm.prodItemId  
 				INNER JOIN rt th ON th.rtNo=td.rtNo AND th.statusCode='P' 
 					AND th.returnDate > (SELECT th.closingDate FROM stk_closing th 
	          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
  				GROUP BY itm.prodCodeId, th.fromCode
  				) as tmp 
  SET hdr.return=tmp.sumQty 
  WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
	";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

//delivery
$sql = "UPDATE tmpStock hdr
 ,(SELECT itm.prodCodeId, cust.locationCode as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
  				INNER JOIN delivery_detail td ON td.prodItemId=itm.prodItemId  
 				INNER JOIN delivery_header th ON th.doNo=td.doNo AND th.statusCode='P' 
 					AND th.deliveryDate > (SELECT th.closingDate FROM stk_closing th 
	          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
 				INNER JOIN sale_header shd ON shd.soNo=th.soNo 
 				INNER JOIN customer cust ON cust.id=shd.custId 
  				GROUP BY itm.prodCodeId, cust.locationCode 
  				) as tmp 
  SET hdr.delivery=tmp.sumQty 
  WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
	";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

//delete
$sql = "UPDATE tmpStock 
SET `balance`=`openAcc`+`openTrans`+`receive`-`sent`-`return`-`delivery`
	";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

//delete
$sql = "DELETE FROM tmpStock 
WHERE `openAcc`=0 AND `openTrans`=0 AND `onway`=0
AND `receive`=0 AND `sent`=0 AND `return`=0 AND `delivery`=0 
AND `balance`=0 AND `book`=0 
	";
	$stmt = $pdo->prepare($sql);		
$stmt->execute();

//We've got this far without an exception, so commit the changes.
$pdo->commit();	

$sql = "SELECT  
sb.`prodId`, sb.`sloc`, sb.`openAcc`, sb.`openTrans`, sb.`onway`, sb.`receive` ,sb.`sent`,sb.`return` ,sb.`delivery` ,sb.`balance` ,sb.`book` 	
, prd.code as prodCode 		
FROM tmpStock sb 
INNER JOIN product prd on prd.id=sb.prodId  ";
if($prodCode<>""){ $sql .= " AND prd.code like '%".$prodCode."%' ";	}	
$sql.="
WHERE 1 ";
if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}else{ $sql .= " AND sb.sloc IN ('8','E') "; }
if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}		
$sql.="ORDER BY prd.code  ";
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
		->setCellValue('C2', 'Location')
		->setCellValue('D2', 'Available')
		->setCellValue('E2', 'Balance')
		->setCellValue('F2', 'Onway');

	$iRow=3; while($row = $stmt->fetch() ){
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['prodId'])
		->setCellValue('B'.$iRow, $row['prodCode'])
		->setCellValue('C'.$iRow, $row['sloc'])
		->setCellValue('D'.$iRow, $row['balance']-$row['book'] )
		->setCellValue('E'.$iRow, $row['balance'])
		->setCellValue('F'.$iRow, $row['onway']);
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