<?php
 		include_once ('config/config.php');
  	
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$accountId = $params['arg1'] ;
		$shipmentId = $params['arg2'] ;
		
		$plan = $SqlUtils->getObject("select * from sc_fba_inbound_plan
					where shipment_id= '{@#shipmentId#}' and account_id = '{@#accountId#}' ",array("shipmentId"=>$shipmentId,"accountId"=>$accountId)) ;

		$label = $plan['LABEL'] ;
		
		$pdf_document = base64_decode($label);
		header("Content-type: application/zip");
		header("Content-Length: " . strlen($pdf_document));
		header("Content-Disposition: attachment; filename=pachage_label_$shipmentId.zip");
		echo $pdf_document;
?>	