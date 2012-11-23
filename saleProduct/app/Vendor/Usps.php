<?php 
	class Usps{
		
		public function connectToUSPS($url, $api, $xml) {
	        $ch = curl_init($url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $api . $xml);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	 
	        $result = curl_exec($ch);
	        $error = curl_error($ch);
	        if (empty($error)) {
	            return $result;
	        } else {
	            return false;
	        }
	    }
	    
	    /**
	     * 
	     * $usps = new USPS();
			$result = $usps->connectToUSPS( 'http://production.shippingapis.com/ShippingAPI.dll', 'API=Verify&XML=', $xml);
	        <?xml version="1.0"?>
			<AddressValidateResponse><Address ID="1">
			<Address2>6406 IVY LN</Address2>
			<City>GREENBELT</City>
			<State>MD</State>
			<Zip5>20770</Zip5>
			<Zip4>1441</Zip4>
			</Address></AddressValidateResponse>
	     */
	    public function getAddressValidateXml(){
	    	$xml = '<AddressValidateRequest USERID="282LIXIA0523">
					<Address ID="1">
					<Address1></Address1>
					<Address2>6406 Ivy Lane</Address2>
					<City>Greenbelt</City>
					<State>MD</State>
					<Zip5></Zip5>
					<Zip4></Zip4>
					</Address>
					</AddressValidateRequest>';
			return $xml ;
	    }
	    
	    public function getEmrsRequest(){
	    	$xml = '<EMRSV4.0Request USERID="XXXXXX" PASSWORD="XXXXXX">
						<Option>RIGHTWINDOW</Option>
						<CustomerName>Garrison Johns</CustomerName>
						<CustomerAddress1>TEST 40</CustomerAddress1>
						<CustomerAddress2>6406 Ivy Lane</CustomerAddress2>
						<CustomerCity>Greenbelt</CustomerCity>
						<CustomerState>MD</CustomerState>
						<CustomerZip5>20770</CustomerZip5>
						<CustomerZip4 />
						<RetailerName>Reza Dianat</RetailerName>
						<RetailerAddress>6406 Ivy Lane</RetailerAddress>
						<PermitNumber>293829</PermitNumber>
						<PermitIssuingPOCity>Greenbelt</PermitIssuingPOCity>
						<PermitIssuingPOState>MD</PermitIssuingPOState>
						<PermitIssuingPOZip5>20770</PermitIssuingPOZip5>
						<PDUPOBox>6406 Ivy Lane</PDUPOBox>
						<PDUCity>Greenbelt</PDUCity>
						<PDUState>MD</PDUState>
						<PDUZip5>20770</PDUZip5>
						<PDUZip4>1234</PDUZip4>
						<ServiceType>Bound Printed Matter</ServiceType>
						<DeliveryConfirmation>False</DeliveryConfirmation>
						<InsuranceValue />
						<MailingAckPackageID>ID00001</MailingAckPackageID>
						<WeightInPounds>0</WeightInPounds>
						<WeightInOunces>10</WeightInOunces>
						<RMA>RMA 123456</RMA>
						<RMAPICFlag>False</RMAPICFlag>
						<ImageType>TIF</ImageType>
						<RMABarcode>False</RMABarcode>
						<AllowNonCleansedDestAddr>False</AllowNonCleansedDestAddr>
						</EMRSV4.0Request>';
	    	
	    }
	    
	}
?>