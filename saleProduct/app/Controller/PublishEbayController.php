<?php
class PublishEbayController extends AppController {
    public $helpers = array( 'Html' , 'Form');
    var $uses = array('Utils');
    /*
     array(
			'bindproductname_val' => '',
			'bindproductdescription_val' => '',
			'bindproductsku_val' => '',
			'bindproductpicture_val' => '',
			'site' => '0',
			'listingtype' => 'Chinese',
			'productid' => 'ISBN',
			'productidval' => '',
			'primarycategory' => '0',
			'attribute' => '',
			'secondarycategory' => '',
			'attribute2' => '',
			'itemtitle' => '',
			'subtitle' => '',
			'lotsize' => '0',
			'listingduration' => 'Days_1',
			'listingduration_fixedprice' => 'Days_3',
			'listingduration_auction' => 'Days_1',
			'startprice' => '0.0',
			'reserveprice' => '0.0',
			'buyitnowprice' => '0.0',
			'secondoffer' => '0.0',
			'template' => '',
			'basicinfo' => '',
			'imgurl' => array(
				(int) 0 => 'http://'
			),
			'itemdescription' => '',
			'detail_wuliu_profile' => '',
			'shippingdetails' => array(
				'ShippingServiceOptions' => array(
					(int) 0 => array(
						'ShippingService' => '',
						'ShippingServiceCost' => '0.0',
						'ShippingServiceAdditionalCost' => '0.0'
					),
					(int) 1 => array(
						'ShippingService' => '',
						'ShippingServiceCost' => '0.0',
						'ShippingServiceAdditionalCost' => '0.0'
					),
					(int) 2 => array(
						'ShippingService' => '',
						'ShippingServiceCost' => '0.0',
						'ShippingServiceAdditionalCost' => '0.0'
					)
				),
				'InternationalShippingServiceOption' => array(
					(int) 3 => array(
						'ShippingService' => '',
						'ShippingServiceCost' => '0.0',
						'ShippingServiceAdditionalCost' => '0.0',
						'ShipToLocation' => array(
							(int) 0 => 'Worldwide'
						)
					),
					(int) 4 => array(
						'ShippingService' => '',
						'ShippingServiceCost' => '0.0',
						'ShippingServiceAdditionalCost' => '0.0',
						'ShipToLocation' => array(
							(int) 0 => 'Worldwide'
						)
					),
					(int) 5 => array(
						'ShippingService' => '',
						'ShippingServiceCost' => '0.0',
						'ShippingServiceAdditionalCost' => '0.0',
						'ShipToLocation' => array(
							(int) 0 => 'Worldwide'
						)
					)
				),
				'SalesTax' => array(
					'SalesTaxState' => '',
					'SalesTaxPercent' => ''
				),
				'ShippingType' => 'Flat'
			),
			'dispatchtime' => '2',
			'detail_location_profile' => '',
			'location' => 'Hong Kong',
			'postalcode' => '',
			'country' => 'HK',
			'return_policy' => array(
				'ReturnsAcceptedOption' => 'ReturnsAccepted',
				'RefundOption' => 'MoneyBack',
				'ReturnsWithinOption' => 'Days_14',
				'ShippingCostPaidByOption' => 'Buyer',
				'Description' => ''
			),
			'detail_return_profile' => '',
			'paymentmethods' => array(
				(int) 0 => 'PayPal'
			),
			'paypal' => '',
			'gallery' => '0',
			'hitcounter' => 'BasicStyle',
			'privatelisting' => 'false',
			'sku' => '',
			'desc' => '',
			'buyerrequirementdetails' => array(
				'MaximumBuyerPolicyViolations' => array(
					'Count' => '',
					'Period' => ''
				),
				'MaximumUnpaidItemStrikesInfo' => array(
					'Count' => '',
					'Period' => ''
				),
				'MaximumItemRequirements' => array(
					'MaximumItemCount' => ''
				)
			),
			'aktion' => '',
			'muban_id' => '',
			'goods_id' => '0',
			'languageid' => ''
		) 
     */
	public function doPublish(){
		$data =  $this->request->data ;
		
		if(!isset($data['quantity'])){
			$data['quantity'] = 1 ;
		}
		
		$pms = "" ;
		foreach( $data['paymentmethods'] as $pm){
			if( $pms == "" ){
				$pms = $pm ;
			}else{
				$pms .= ",".$pm ;
			}
		}
		
		//format parymethod
		$data['PAYMENTMETHODS1'] = $pms ;
		
		$data['RP_RETURNSACCEPTEDOPTION'] = $data['return_policy']['ReturnsAcceptedOption'] ;
		$data['RP_REUNDOPTION'] = $data['return_policy']['RefundOption'] ;
		$data['RP_RETURNSWITHINOPTION'] = $data['return_policy']['ReturnsWithinOption'] ;
		$data['RP_SHIPPINGCOSTPAIDBYOPTION'] = $data['return_policy']['ShippingCostPaidByOption'] ;
		$data['RP_DESCRIPTION'] = $data['return_policy']['Description'] ;
		
		$data['BRD_MBPV_COUNT'] = $data['buyerrequirementdetails']['MaximumBuyerPolicyViolations']['Count'] ;
		$data['BRD_MBPV_PERIOD'] =  $data['buyerrequirementdetails']['MaximumBuyerPolicyViolations']['Period'] ;
		$data['BRD_MUIS_COUNT'] =  $data['buyerrequirementdetails']['MaximumUnpaidItemStrikesInfo']['Count'] ;
		$data['BRD_MUIS_PERIOD'] =  $data['buyerrequirementdetails']['MaximumUnpaidItemStrikesInfo']['Period'] ;
		$data['BRD_MIR_MIC'] =  $data['buyerrequirementdetails']['MaximumItemRequirements']['MaximumItemCount'] ;
		
		$data['SD_SSO1_SHIPPINGSERVICE'] 								= $data['shippingdetails']['ShippingServiceOptions'][0]['ShippingService'] ;
		$data['SD_SSO1_SHIPPINGSERVICECOST'] 						=$data['shippingdetails']['ShippingServiceOptions'][0]['ShippingServiceCost'] ;
		$data['SD_SSO1_SHIPPINGSERVICEADDITIONALCOST'] = $data['shippingdetails']['ShippingServiceOptions'][0]['ShippingServiceAdditionalCost'] ;
		$data['SD_SSO2_SHIPPINGSERVICE'] 								= $data['shippingdetails']['ShippingServiceOptions'][1]['ShippingService'] ;
		$data['SD_SSO2_SHIPPINGSERVICECOST'] 						=$data['shippingdetails']['ShippingServiceOptions'][1]['ShippingServiceCost'] ;
		$data['SD_SSO2_SHIPPINGSERVICEADDITIONALCOST'] = $data['shippingdetails']['ShippingServiceOptions'][1]['ShippingServiceAdditionalCost'] ;
		$data['SD_SSO3_SHIPPINGSERVICE'] 								= $data['shippingdetails']['ShippingServiceOptions'][2]['ShippingService'] ;
		$data['SD_SSO3_SHIPPINGSERVICECOST'] 						=$data['shippingdetails']['ShippingServiceOptions'][2]['ShippingServiceCost'] ;
		$data['SD_SSO3_SHIPPINGSERVICEADDITIONALCOST'] = $data['shippingdetails']['ShippingServiceOptions'][2]['ShippingServiceAdditionalCost'] ;
		$data['SD_ISSO1_SHIPPINGSERVICE'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingService'] ;
		$data['SD_ISSO1_SHIPPINGSERVICECOST'] 						= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingService'] ;
		$data['SD_ISSO1_SHIPPINGSERVICEADDITIONALCOST'] 	= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingService'] ;
		$data['SD_ISSO1_SHIPTOLOCASTION'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShipToLocation'] ;
		$data['SD_ISSO2_SHIPPINGSERVICE'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingService'] ;
		$data['SD_ISSO2_SHIPPINGSERVICECOST'] 						= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingService'] ;
		$data['SD_ISSO2_SHIPPINGSERVICEADDITIONALCOST'] 	= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingService'] ;
		$data['SD_ISSO2_SHIPTOLOCASTION'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShipToLocation'] ;
		$data['SD_ISSO3_SHIPPINGSERVICE'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingService'] ;
		$data['SD_ISSO3_SHIPPINGSERVICECOST'] 						= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingService'] ;
		$data['SD_ISSO3_SHIPPINGSERVICEADDITIONALCOST'] 	= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingService'] ;
		$data['SD_ISSO3_SHIPTOLOCASTION'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShipToLocation'] ;
		$data['SD_SALESTAXSTATE'] = $data['shippingdetails']['SalesTax']['SalesTaxState'] ;
		$data['SD_SALESTAXPERCENT'] = $data['shippingdetails']['SalesTax']['SalesTaxPercent'] ;
		$data['SD_SHIPPINGTYPE'] = $data['shippingdetails']['ShippingType'] ;
		
		$index = 0 ;
		foreach( $data['imgurl'] as $url ){
			$data['URL'.$index] = $url ;
			$index = $index + 1 ;
		}
		
		$data['guid'] = $this->create_guid() ;
		//insert into db
		if( empty($data['id']) ){
			$this->Utils->exeSql("sql_ebay_template_insert", $data) ;
		}else{
			$this->Utils->exeSql("sql_ebay_template_update", $data) ;
		}
		
		
		return ;
		
		$xml = "
		<AddItemRequest>
			<Version><![CDATA[815]]></Version>
			<Item>
				<Country>".$data['country']."</Country>
				<Currency>".$data['currency']."</Currency>
				<Description><![CDATA[".$data['itemdescription']."]]></Description>
				<ListingDuration><![CDATA[".$data['listingduration']."]]></ListingDuration>
			    <ListingType>".$data['listingtype']."</ListingType>
			    <Location><![CDATA[".$data['location']."]]></Location>
			    <PaymentMethods>".$pms."</PaymentMethods>
			    <PayPalEmailAddress><![CDATA[".$data['paypal']."]]></PayPalEmailAddress>
			    <PrimaryCategory>
			    	<CategoryID><![CDATA[".$data['primarycategory']."]]></CategoryID>
			    </PrimaryCategory>
			    <Quantity>".$data['quantity']."</Quantity>
			    <ShippingDetails>
				     <ShippingType><![CDATA[".$data['shippingdetails']['ShippingType']."]]></ShippingType>
				    <ShippingServiceOptions>
				    	<ShippingServicePriority>1</ShippingServicePriority>
					    <ShippingService><![CDATA[".$data['shippingdetails']['ShippingServiceOptions'][0]['ShippingService']."]]></ShippingService>
					    <ShippingServiceCost currencyID=\"USD\" >".$data['shippingdetails']['ShippingServiceOptions'][0]['ShippingServiceCost']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['shippingdetails']['ShippingServiceOptions'][0]['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
					</ShippingServiceOptions>" ;
		if( !empty($data['shippingdetails']['ShippingServiceOptions'][1]['ShippingService']) ){
			$xml .= "		<ShippingServiceOptions>
				<ShippingServicePriority>1</ShippingServicePriority>
				<ShippingService><![CDATA[".$data['shippingdetails']['ShippingServiceOptions'][1]['ShippingService']."]]></ShippingService>
				<ShippingServiceCost currencyID=\"USD\" >".$data['shippingdetails']['ShippingServiceOptions'][1]['ShippingServiceCost']."</ShippingServiceCost>
				<ShippingServiceAdditionalCost>".$data['shippingdetails']['ShippingServiceOptions'][1]['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
				</ShippingServiceOptions>" ;
		}

		if( !empty($data['shippingdetails']['ShippingServiceOptions'][2]['ShippingService']) ){
			$xml .= "	
					<ShippingServiceOptions>
						<ShippingServicePriority>1</ShippingServicePriority>
					    <ShippingService><![CDATA[".$data['shippingdetails']['ShippingServiceOptions'][2]['ShippingService']."]]></ShippingService>
					    <ShippingServiceCost currencyID=\"USD\" >".$data['shippingdetails']['ShippingServiceOptions'][2]['ShippingServiceCost']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['shippingdetails']['ShippingServiceOptions'][2]['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
					  </ShippingServiceOptions>" ;
		}
		
		if( !empty($data['shippingdetails']['ShippingServiceOptions'][3]['ShippingService']) ){
			$xml .= "	
					<InternationalShippingServiceOption>
						<ShippingServicePriority>1</ShippingServicePriority>
						<ShippingService><![CDATA[".$data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingService']."]]></ShippingService>
					    <ShippingServiceCost currencyID=\"USD\" >".$data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingServiceCost']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
					    <ShipToLocation>".$data['shippingdetails']['InternationalShippingServiceOption'][3]['ShipToLocation']."</ShippingServicePriority>
					</InternationalShippingServiceOption>" ;
		}
		
		if( !empty($data['shippingdetails']['ShippingServiceOptions'][4]['ShippingService']) ){
			$xml .= "	
					<InternationalShippingServiceOption>
						<ShippingServicePriority>1</ShippingServicePriority>
						<ShippingService><![CDATA[".$data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingService']."]]></ShippingService>
					    <ShippingServiceCost currencyID=\"USD\" >".$data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingServiceCost']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
					    <ShipToLocation>".$data['shippingdetails']['InternationalShippingServiceOption'][4]['ShipToLocation']."</ShippingServicePriority>
					</InternationalShippingServiceOption>" ;
		}
		
		if( !empty($data['shippingdetails']['ShippingServiceOptions'][5]['ShippingService']) ){
			$xml .= "	
					<InternationalShippingServiceOption>
						<ShippingServicePriority>1</ShippingServicePriority>
						<ShippingService><![CDATA[".$data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingService']."]]></ShippingService>
					    <ShippingServiceCost currencyID=\"USD\" >".$data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingServiceCost']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
					    <ShipToLocation>".$data['shippingdetails']['InternationalShippingServiceOption'][5]['ShipToLocation']."</ShippingServicePriority>
					</InternationalShippingServiceOption>" ;
		}
			$xml .= "	
			    </ShippingDetails>
			    <StartPrice><![CDATA[".$data['startprice']."]]></StartPrice>
			    <BuyItNowPrice><![CDATA[".$data['buyitnowprice']."]]></BuyItNowPrice>
			    <Title><![CDATA[".$data['itemtitle']."]]></Title>";
			
			if( isset($data['subtitle']) && !empty($data['subtitle']) ){
				$xml .= "	   <SubTitle><![CDATA[".$data['subtitle']."]]></SubTitle>";
			}
			 
			  $xml .= "	   
			  <DispatchTimeMax>".$data['dispatchtime']."</DispatchTimeMax>
			    <ReturnPolicy>
			    	<ReturnsAcceptedOption><![CDATA[".$data['return_policy']['ReturnsAcceptedOption']."]]></ReturnsAcceptedOption>
			    	<RefundOption><![CDATA[".$data['return_policy']['RefundOption']."]]></RefundOption>
			    	<ReturnsWithinOption><![CDATA[".$data['return_policy']['ReturnsWithinOption']."]]></ReturnsWithinOption>
			    	<ShippingCostPaidByOption><![CDATA[".$data['return_policy']['ShippingCostPaidByOption']."]]></ShippingCostPaidByOption>
			    	<Description><![CDATA[".$data['return_policy']['Description']."]]></Description>
			    </ReturnPolicy>
			</Item>
		</AddItemRequest>" ;
			  
			  echo 1111111111;
			$return =  file_get_contents("http://localhost/saleProductService/index.php/eBay/doItem/6/2342342") ;
			
			$params = http_build_query(  array("xml"=>$xml) ) ;
			
			$return = $this->Post("http://localhost/saleProductService/index.php/eBay/doItem/6/2342342", $params);
		
			debug($return) ;
			echo 22222222222;
			debug($xml) ;
		
		/*'startprice' => '0.0',
			'reserveprice' => '0.0',
			'buyitnowprice' => '0.0',
			'secondoffer' => '0.0',*/
	}

}