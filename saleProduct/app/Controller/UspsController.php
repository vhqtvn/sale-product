<?php

App :: import('Vendor', 'Usps');

class UspsController extends AppController {
	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $uses = array('Amazonaccount', 'Config');
    
    public function validateService(){
    	$usps = new Usps() ;
    	
    	$xml = $this->getEmrsRequest() ;
    	$result = $usps->connectToUSPS("https://secure.shippingapis.com/ShippingAPI.dll","API=MerchReturnCertifyV4&XML=",$xml) ;
    	print_r($result) ;
    }
    
    /**
     * http://testing.shippingapis.com/ShippingAPITest.dll
     * API=Verify&XML=
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
    
    /**
     * 80040b1a API Disabled: MerchReturnCertifyV4. 
     * Use https://secure.shippingapis.com/ShippingAPI.dll?API=MerchReturnCertifyV4 for testing. UspsCom::DoAuth
     * API=MerchandiseReturnV4&XML=
     */
    public function getEmrsRequest(){
	    	$xml = '<EMRSV4.0Request USERID="282LIXIA0523" PASSWORD="309NM26RF888">
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
						
			return $xml ;
	    	
	    }
}