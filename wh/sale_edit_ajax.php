<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
	
	$s_userID = $_SESSION['userID'];
		
    $saleDate = $_POST['saleDate'];
	$poNo = $_POST['poNo'];
    $smCode = $_POST['smCode'];
    $custCode = $_POST['custCode'];
	$deliveryDate = $_POST['deliveryDate'];
	$deliveryRem = $_POST['deliveryRem'];
	$remark = $_POST['remark'];
	$prodGFC = (isset($_POST['prodGFC'])? 1 : 0 );
	$prodGFM = (isset($_POST['prodGFM'])? 1 : 0 );
	$prodGFT = (isset($_POST['prodGFT'])? 1 : 0 );
	$prodSC = (isset($_POST['prodSC'])? 1 : 0 );
	$prodCFC = (isset($_POST['prodCFC'])? 1 : 0 );
	$prodEGWM = (isset($_POST['prodEGWM'])? 1 : 0 );
	$prodGT = (isset($_POST['prodGT'])? 1 : 0 );
	$prodCSM = (isset($_POST['prodCSM'])? 1 : 0 );
	$prodWR = (isset($_POST['prodWR'])? 1 : 0 );
	$suppTypeFact = (isset($_POST['suppTypeFact'])? 1 : 0 );
	$suppTypeImp = (isset($_POST['suppTypeImp'])? 1 : 0 );
	$prodTypeOld = (isset($_POST['prodTypeOld'])? 1 : 0 );
	$prodTypeNew = (isset($_POST['prodTypeNew'])? 1 : 0 );
	$custTypeOld = (isset($_POST['custTypeOld'])? 1 : 0 );
	$custTypeNew = (isset($_POST['custTypeNew'])? 1 : 0 );
	$prodStkInStk = (isset($_POST['prodStkInStk'])? 1 : 0 );
	$prodStkOrder = (isset($_POST['prodStkOrder'])? 1 : 0 );
	$prodStkOther = (isset($_POST['prodStkOther'])? 1 : 0 );
	$prodStkRem = $_POST['prodStkRem'];
	$packTypeAk = (isset($_POST['packTypeAk'])? 1 : 0 );
	$packTypeNone = (isset($_POST['packTypeNone'])? 1 : 0 );
	$packTypeOther = (isset($_POST['packTypeOther'])? 1 : 0 );
	$packTypeRem = $_POST['packTypeRem'];
	$priceOnOrder = (isset($_POST['priceOnOrder'])? 1 : 0 );
	$priceOnOther = (isset($_POST['priceOnOther'])? 1 : 0 );
	$priceOnRem = $_POST['priceOnRem'];
	$plac2deliCode = (isset($_POST['plac2deliCode'])? $_POST['plac2deliCode'] : '' );
	$plac2deliRem = $_POST['plac2deliRem'];
	$payTypeCode = (isset($_POST['payTypeCode'])? $_POST['payTypeCode'] : '' );
	$payTypeRem = $_POST['payTypeRem'];
	$id = $_POST['id'];
	
	$saleDate = to_mysql_date($saleDate);
	$deliveryDate = to_mysql_date($deliveryDate);

	//$pdo->beginTransaction();
	
	$sql = "
			UPDATE `sale_header` SET `saleDate`=:saleDate, `poNo`=:poNo, `custCode`=:custCode
			, `smCode`=:smCode, `prodGFC`=:prodGFC, `prodGFM`=:prodGFM, `prodGFT`=:prodGFT
			, `prodSC`=:prodSC, `prodCFC`=:prodCFC, `prodEGWM`=:prodEGWM, `prodGT`=:prodGT
			, `prodCSM`=:prodCSM, `prodWR`=:prodWR, `deliveryDate`=:deliveryDate
			, `deliveryRem`=:deliveryRem, `suppTypeFact`=:suppTypeFact, `suppTypeImp`=:suppTypeImp
			, `prodTypeOld`=:prodTypeOld, `prodTypeNew`=:prodTypeNew, `custTypeOld`=:custTypeOld
			, `custTypeNew`=:custTypeNew, `prodStkInStk`=:prodStkInStk, `prodStkOrder`=:prodStkOrder
			, `prodStkOther`=:prodStkOther, `prodStkRem`=:prodStkRem, `packTypeAk`=:packTypeAk
			, `packTypeNone`=:packTypeNone, `packTypeOther`=:packTypeOther, `packTypeRem`=:packTypeRem
			, `priceOnOrder`=:priceOnOrder, `priceOnOther`=:priceOnOther, `priceOnRem`=:priceOnRem
			, `remark`=:remark, `plac2deliCode`=:plac2deliCode, `plac2deliRem`=:plac2deliRem
			, `payTypeCode`=:payTypeCode, `payTypeRem`=:payTypeRem
			, `updateTime`=now(), `updateByID`=:s_userID
			WHERE id=:id 
			";
 
    //$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':saleDate', $saleDate);
	$stmt->bindParam(':poNo', $poNo);
	$stmt->bindParam(':custCode', $custCode);
	$stmt->bindParam(':smCode', $smCode);
	$stmt->bindParam(':prodGFC', $prodGFC);
	$stmt->bindParam(':prodGFM', $prodGFM);
	$stmt->bindParam(':prodGFT', $prodGFT);
	$stmt->bindParam(':prodSC', $prodSC);
	$stmt->bindParam(':prodCFC', $prodCFC);
	$stmt->bindParam(':prodEGWM', $prodEGWM);
	$stmt->bindParam(':prodGT', $prodGT);
	$stmt->bindParam(':prodCSM', $prodCSM);
	$stmt->bindParam(':prodWR', $prodWR);
	$stmt->bindParam(':deliveryDate', $deliveryDate);
	$stmt->bindParam(':deliveryRem', $deliveryRem);
	$stmt->bindParam(':suppTypeFact', $suppTypeFact);
	$stmt->bindParam(':suppTypeImp', $suppTypeImp);
	$stmt->bindParam(':prodTypeOld', $prodTypeOld);
	$stmt->bindParam(':prodTypeNew', $prodTypeNew);
	$stmt->bindParam(':custTypeOld', $custTypeOld);
	$stmt->bindParam(':custTypeNew', $custTypeNew);
	$stmt->bindParam(':prodStkInStk', $prodStkInStk);
	$stmt->bindParam(':prodStkOrder', $prodStkOrder);
	$stmt->bindParam(':prodStkOther', $prodStkOther);
	$stmt->bindParam(':prodStkRem', $prodStkRem);
	$stmt->bindParam(':packTypeAk', $packTypeAk);
	$stmt->bindParam(':packTypeNone', $packTypeNone);
	$stmt->bindParam(':packTypeOther', $packTypeOther);
	$stmt->bindParam(':packTypeRem', $packTypeRem);
	$stmt->bindParam(':priceOnOrder', $priceOnOrder);
	$stmt->bindParam(':priceOnOther', $priceOnOther);
	$stmt->bindParam(':priceOnRem', $priceOnRem);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':plac2deliCode', $plac2deliCode);
	$stmt->bindParam(':plac2deliRem', $plac2deliRem);
	$stmt->bindParam(':payTypeCode', $payTypeCode);
	$stmt->bindParam(':payTypeRem', $payTypeRem);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->bindParam(':id', $id);
	$stmt->execute();
		
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Update Completed.'));
    /*if ($result) {
      header('Content-Type: application/json');
      echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Please try again. " . mysqli_error($link);
      echo json_encode(array('success' => false, 'message' => $errors));
   }*/
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    //$pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Update. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
