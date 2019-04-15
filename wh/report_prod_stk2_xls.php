<?php
include 'session.php';

require_once '../phpexcel/Classes/PHPExcel.php';

date_default_timezone_set("Asia/Bangkok");

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("AK")
        ->setTitle("WMS")
        ->setSubject("Stock Report")
        ->setDescription("Excel File")
        ->setKeywords("Stock")
        ->setCategory("Stock");
		
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
	//if($prodCode=="") $prodId="";

							
$pdo->beginTransaction();

          	$sql = "
          	CREATE TEMPORARY TABLE tmpStock (
          		`prodId` int(11) NOT NULL,
				  `prodCode` varchar(100) NOT NULL,
				  `sloc` varchar(10) NOT NULL,
				  `openAcc` decimal(10,2) NOT NULL,
				  `onway` decimal(10,2) NOT NULL,
				  `receive` decimal(10,2) NOT NULL,
				  `sent` decimal(10,2) NOT NULL,
				  `return` decimal(10,2) NOT NULL,
				  `delivery` decimal(10,2) NOT NULL,
				  `balance` decimal(10,2) NOT NULL,
				  `balanceReCheck` decimal(10,2) NOT NULL,
				  `book` decimal(10,2) NOT NULL,
		      	PRIMARY KEY (`prodId`,`sloc`)
		    )";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			$sql = "
	          INSERT INTO tmpStock (prodId, prodCode, sloc)
	          SELECT prd.id, prd.code, sl.code 
	          FROM product prd ";
	        $sql .= "
	          CROSS JOIN sloc sl ON 1=1 ";
	        if($sloc<>""){ $sql .= " AND sl.code='$sloc' ";	}else{ $sql .= " AND sl.code IN ('8','E') "; }  
	        $sql .= "WHERE 1=1 ";
	        if($prodCode<>""){ $sql .= "AND prd.code like '%".$prodCode."%' ";	}
	        if($catCode<>""){ $sql .= " AND prd.catCode='$catCode' ";	}

          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();		


			//Last Prev Closing Date. = LPCD
			$sql = "SELECT th.id, th.closingDate FROM stk_closing th WHERE th.statusCode='A' AND DATE(th.closingDate)<='$dateFromYmd' ORDER BY th.closingDate DESC LIMIT 1
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();
			$row = $stmt->fetch();
			$lpcDate = $row['closingDate'];
			$lpcdId = $row['id'];

			//Open
			$sql = "UPDATE tmpStock hdr 
	         ,(SELECT td.prodId, td.sloc, td.balance as sumQty FROM stk_closing_detail td 
	          				WHERE td.hdrId=:lpcdId 
	          				) as tmp 
	          SET hdr.openAcc=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodId AND hdr.sloc=tmp.sloc 
          	";
          	$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':lpcdId', $lpcdId);	
			$stmt->execute();

			//Onway
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, sh.toCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN send_detail sd ON sd.prodItemId=itm.prodItemId  
	         				INNER JOIN send sh ON sh.sdNo=sd.sdNo AND sh.statusCode='P' AND sh.rcNo IS NULL AND  DATE(sh.sendDate) <= '$dateFromYmd'
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
	         					AND DATE(th.receiveDate) > '$lpcDate' AND DATE(th.receiveDate) <= '$dateFromYmd'
	          				GROUP BY itm.prodCodeId, th.toCode
	          				) as tmp 
	          SET hdr.receive=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//Sent
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, th.fromCode, SUM(itm.qty) as sumQty 
	         				FROM product_item itm 
	          				INNER JOIN send_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN send th ON th.sdNo=td.sdNo AND th.statusCode='P' 
	         					AND DATE(th.sendDate) > '$lpcDate' AND DATE(th.sendDate) <= '$dateFromYmd'
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
	         				INNER JOIN rt th ON th.rtNo=td.rtNo AND th.statusCode='P' AND DATE(th.returnDate) > '$lpcDate' AND DATE(th.returnDate) <= '$dateFromYmd' 
	          				GROUP BY itm.prodCodeId, th.fromCode
	          				) as tmp 
	          SET hdr.return=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//delivery
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, CASE WHEN cust.locationCode = 'L' THEN '8' ELSE 'E' END as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN delivery_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN delivery_header th ON th.doNo=td.doNo AND th.statusCode='P' 
	         					AND DATE(th.deliveryDate) > '$lpcDate' AND DATE(th.deliveryDate) <= '$dateFromYmd'
	         				INNER JOIN sale_header shd ON shd.soNo=th.soNo 
	         				INNER JOIN customer cust ON cust.id=shd.custId 
	          				GROUP BY itm.prodCodeId, cust.locationCode 
	          				) as tmp 
	          SET hdr.delivery=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();
			
			//balance
			$sql = "UPDATE tmpStock 
			SET `balance`=`openAcc`+`receive`-`sent`-`return`-`delivery`
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();
	
			//delete
			$sql = "DELETE FROM tmpStock 
			WHERE `openAcc`=0 AND `onway`=0
			AND `receive`=0 AND `sent`=0 AND `return`=0 AND `delivery`=0 
			AND `balance`=0 AND `book`=0 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//We've got this far without an exception, so commit the changes.
			$pdo->commit();	





$sql = "SELECT  
sb.`prodId`, sb.`prodCode`, sb.`sloc`, sb.`openAcc`, sb.`onway`, sb.`receive` ,sb.`sent`,sb.`return` ,sb.`delivery` ,sb.`balance` ,sb.`balanceReCheck` ,sb.`book` 	
, sl.name as slocName 
FROM tmpStock sb 
	INNER JOIN sloc sl ON sl.code=sb.sloc ";
	$sql.="ORDER BY sb.prodCode, sb.sloc  ";
	//$sql.="LIMIT $start, $rows ";
	$result = mysqli_query($link, $sql);   
	$stmt = $pdo->prepare($sql);		
	$stmt->execute();
$countTotal = $stmt->rowCount();

if($countTotal>0){
	// Add Header
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B1', 'Stock Date : '.date('d M Y',strtotime( $dateFromYmd )));

	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B2', 'Print Date : '.date('Y-m-d H:i:s'));
		
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A3', 'Product ID')
		->setCellValue('B3', 'Product Code')
		->setCellValue('C3', 'Location')
		->setCellValue('D3', 'Balance')
		->setCellValue('E3', 'Onway');
		//->setCellValue('D2', 'Available')

	$iRow=4; while($row = $stmt->fetch() ){
		// Check incorrect balance.
		$isNotEqual=false;
		$bgColor="";
		if ( $row['balance']<0 ){
			$isNotEqual=true;
			$bgColor="bg-danger";
		}
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)		
		->setCellValue('A'.$iRow, $row['prodId'].($isNotEqual?' *** ':''))
		->setCellValue('B'.$iRow, $row['prodCode'])
		->setCellValue('C'.$iRow, $row['sloc'])
		->setCellValue('D'.$iRow, $row['balance'])
		->setCellValue('E'.$iRow, $row['onway']);
		//		->setCellValue('D'.$iRow, $row['balance']-$row['book'] )
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