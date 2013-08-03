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
	public function saveTemplate(){
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
		
		
		$itemSpecials = "" ;
		if(isset($data['itemspecials'])){
			$itemSpecials = $data['itemspecials'] ;
			$vals = array() ;
			foreach ( $itemSpecials as $name ){
				$selectKey = "itemspecial_".str_replace(" ", "_", $name) ;
				$inputKey  = "itemspecial_".str_replace(" ", "_", $name)."_input" ;
				$val = "" ;
				if( isset( $data[$inputKey] ) && !empty( $data[$inputKey] ) ){
					$val = $data[$inputKey] ;
				}else if( isset( $data[$selectKey] ) ){
					$val = $data[$selectKey] ;
				}
				$vals[$name] = $val ;
			}
			$itemSpecials = json_encode($vals) ;
		}
		$data['itemspecials'] = $itemSpecials ;
		
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
		$data['SD_ISSO1_SHIPPINGSERVICECOST'] 						= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingServiceCost'] ;
		$data['SD_ISSO1_SHIPPINGSERVICEADDITIONALCOST'] 	= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingServiceAdditionalCost'] ;
		$data['SD_ISSO1_SHIPTOLOCASTION'] 								= $this->formatLocation( $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShipToLocation'] );
		
		$data['SD_ISSO2_SHIPPINGSERVICE'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingService'] ;
		$data['SD_ISSO2_SHIPPINGSERVICECOST'] 						= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingServiceCost'] ;
		$data['SD_ISSO2_SHIPPINGSERVICEADDITIONALCOST'] 	= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingServiceAdditionalCost'] ;
		$data['SD_ISSO2_SHIPTOLOCATION'] 								= $this->formatLocation(  $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShipToLocation'] ) ;
	
		$data['SD_ISSO3_SHIPPINGSERVICE'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingService'] ;
		$data['SD_ISSO3_SHIPPINGSERVICECOST'] 						= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingServiceCost'] ;
		$data['SD_ISSO3_SHIPPINGSERVICEADDITIONALCOST'] 	= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingServiceAdditionalCost'] ;
		$data['SD_ISSO3_SHIPTOLOCATION'] 								=  $this->formatLocation(  $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShipToLocation'] );
		$data['SD_SALESTAXSTATE'] = $data['shippingdetails']['SalesTax']['SalesTaxState'] ;
		$data['SD_SALESTAXPERCENT'] = $data['shippingdetails']['SalesTax']['SalesTaxPercent'] ;
		$data['SD_SHIPPINGTYPE'] = $data['shippingdetails']['ShippingType'] ;
		
		$data['itemdescription'] =  $data['itemdescription'] ;
		
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
		
		$this->response->type("text") ;
		$this->response->body("success")   ;
		
		return $this->response ;
	}
	
	public  function doPublish($templateId){
		
		$data = $this->Utils->getObject("select * from sc_ebay_template where id='{@#id#}'",array("id"=>$templateId))  ;
		
		$listingType = $data['LISTINGTYPE'] ;
		
		$tagName = "" ;
		if( $listingType == 'Chinese' ){
			$tagName ="AddItemRequest" ;
		}else{
			$tagName ="AddFixedPriceItemRequest" ;
		}
		
		$xml = "
		<".$tagName.">
			<Version><![CDATA[815]]></Version>
			<Item>
				<Country>".$data['COUNTRY']."</Country>
				<Currency>".$data['CURRENCY']."</Currency>
				<Description><![CDATA[".$data['ITEMDESCRIPTION']."]]></Description>
				<ListingDuration><![CDATA[".$data['LISTINGDURATION']."]]></ListingDuration>
			    <ListingType>".$data['LISTINGTYPE']."</ListingType>
			    <Location><![CDATA[".$data['LOCATION']."]]></Location>
			    <PaymentMethods>".$data['PAYMENTMETHODS1']."</PaymentMethods>
			    <PayPalEmailAddress><![CDATA[".$data['PAYPAL']."]]></PayPalEmailAddress>
			    <PrimaryCategory>
			    	<CategoryID><![CDATA[".$data['PRIMARYCATEGORY']."]]></CategoryID>
			    </PrimaryCategory>
			    <Quantity>".$data['QUANTITY']."</Quantity>
			    <ShippingDetails>
				     <ShippingType><![CDATA[".$data['SD_SHIPPINGTYPE']."]]></ShippingType>
				    <ShippingServiceOptions>
				    	<ShippingServicePriority>1</ShippingServicePriority>
					    <ShippingService><![CDATA[".$data['SD_SSO1_SHIPPINGSERVICE']."]]></ShippingService>
					    <ShippingServiceCost>".$data['SD_SSO1_SHIPPINGSERVICECOST']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['SD_SSO1_SHIPPINGSERVICEADDITIONALCOST']."</ShippingServiceAdditionalCost>
					</ShippingServiceOptions>" ;
		if( !empty($data['SD_SSO2_SHIPPINGSERVICE']) ){
			$xml .= "		<ShippingServiceOptions>
				<ShippingServicePriority>1</ShippingServicePriority>
				<ShippingService><![CDATA[".$data['SD_SSO2_SHIPPINGSERVICE']."]]></ShippingService>
				<ShippingServiceCost>".$data['SD_SSO2_SHIPPINGSERVICECOST']."</ShippingServiceCost>
				<ShippingServiceAdditionalCost>".$data['SD_SSO2_SHIPPINGSERVICEADDITIONALCOST']."</ShippingServiceAdditionalCost>
				</ShippingServiceOptions>" ;
		}
		

		if( !empty($data['SD_SSO3_SHIPPINGSERVICE']) ){
			$xml .= "
					<ShippingServiceOptions>
						<ShippingServicePriority>1</ShippingServicePriority>
					    <ShippingService><![CDATA[".$data['SD_SSO3_SHIPPINGSERVICE']."]]></ShippingService>
					    <ShippingServiceCost>".$data['SD_SSO3_SHIPPINGSERVICECOST']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['SD_SSO3_SHIPPINGSERVICEADDITIONALCOST']."</ShippingServiceAdditionalCost>
					  </ShippingServiceOptions>" ;
		}
		
		if( !empty($data['SD_ISSO1_SHIPPINGSERVICE']) ){
			$xml .= "
					<InternationalShippingServiceOption>
						<ShippingServicePriority>1</ShippingServicePriority>
						<ShippingService><![CDATA[".$data['SD_ISSO1_SHIPPINGSERVICE']."]]></ShippingService>
					    <ShippingServiceCost>".$data['SD_ISSO1_SHIPPINGSERVICECOST']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['SD_ISSO1_SHIPPINGSERVICEADDITIONALCOST']."</ShippingServiceAdditionalCost>
					    <ShipToLocation><![CDATA[".$data['SD_ISSO1_SHIPTOLOCASTION']."]]></ShipToLocation>
					</InternationalShippingServiceOption>" ;
		}
		
		if( !empty($data['SD_ISSO2_SHIPPINGSERVICE']) ){
			$xml .= "
					<InternationalShippingServiceOption>
						<ShippingServicePriority>1</ShippingServicePriority>
						<ShippingService><![CDATA[".$data['SD_ISSO2_SHIPPINGSERVICE']."]]></ShippingService>
					    <ShippingServiceCost>".$data['SD_ISSO2_SHIPPINGSERVICECOST']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['SD_ISSO2_SHIPPINGSERVICEADDITIONALCOST']."</ShippingServiceAdditionalCost>
					    <ShipToLocation><![CDATA[".$data['SD_ISSO2_SHIPTOLOCATION']."]]></ShipToLocation>
					</InternationalShippingServiceOption>" ;
		}
		
		if( !empty($data['SD_ISSO3_SHIPPINGSERVICE']) ){
			$xml .= "
					<InternationalShippingServiceOption>
						<ShippingServicePriority>1</ShippingServicePriority>
						<ShippingService><![CDATA[".$data['SD_ISSO3_SHIPPINGSERVICE']."]]></ShippingService>
					    <ShippingServiceCost>".$data['SD_ISSO3_SHIPPINGSERVICECOST']."</ShippingServiceCost>
					    <ShippingServiceAdditionalCost>".$data['SD_ISSO3_SHIPPINGSERVICEADDITIONALCOST']."</ShippingServiceAdditionalCost>
					    <ShipToLocation><![CDATA[".$data['SD_ISSO3_SHIPTOLOCATION']."]]></ShipToLocation>
					</InternationalShippingServiceOption>" ;
		}
		
		
		if( $listingType == 'Chinese' ){
			$xml .= "
			    </ShippingDetails>
			    <StartPrice><![CDATA[".$data['STARTPRICE']."]]></StartPrice>
			    <BuyItNowPrice><![CDATA[".$data['BUYITNOWPRICE']."]]></BuyItNowPrice>
			    <Title><![CDATA[".$data['ITEMTITLE']."]]></Title>";
		}else{
			$xml .= "
			    </ShippingDetails>
			    <StartPrice><![CDATA[".$data['BUYITNOWPRICE']."]]></StartPrice>
			    <Title><![CDATA[".$data['ITEMTITLE']."]]></Title>";
		}
		
		if( $data['CONDITIONID'] ){
			$xml .= "	   <ConditionID><![CDATA[".$data['CONDITIONID']."]]></ConditionID>";
		}
			
		if( isset($data['SUBTITLE']) && !empty($data['SUBTITLE']) ){
			$xml .= "	   <SubTitle><![CDATA[".$data['SUBTITLE']."]]></SubTitle>";
		}
		
		if( !empty($data['ITEM_SPECIALS']) ){
			$xml .= "
					<ItemSpecifics>" ;
			$itemspecials = json_decode( $data['ITEM_SPECIALS'] ) ;
			$objectVars = get_object_vars($itemspecials);
			foreach( $objectVars as $key=>$value ){
				if( !empty($value) ){
					$xml .= "
					<NameValueList>
					<Name>$key</Name>
					<Value>$value</Value>
					</NameValueList>" ;
				}
				}
				$xml .= "		</ItemSpecifics>" ;
		}
		
		$xml .= "
			  <DispatchTimeMax>".$data['DISPATCHTIME']."</DispatchTimeMax>
			    <ReturnPolicy>
			    	<ReturnsAcceptedOption><![CDATA[".$data['RP_RETURNSACCEPTEDOPTION']."]]></ReturnsAcceptedOption>
			    	<RefundOption><![CDATA[".$data['RP_REUNDOPTION']."]]></RefundOption>
			    	<ReturnsWithinOption><![CDATA[".$data['RP_RETURNSWITHINOPTION']."]]></ReturnsWithinOption>
			    	<ShippingCostPaidByOption><![CDATA[".$data['RP_SHIPPINGCOSTPAIDBYOPTION']."]]></ShippingCostPaidByOption>
			    	<Description><![CDATA[".$data['RP_DESCRIPTION']."]]></Description>
			    </ReturnPolicy>
			</Item>
		</".$tagName.">" ;
	
		$params = http_build_query(  array("xml"=>$xml) ) ;
			
		$baseUrl = $this->Utils->buildUrlByAccountId($data['ACCOUNT_ID'], "ebay/doItem") ;
		//debug( $xml ) ;
		$return = $this->Post($baseUrl."/".$data['LISTINGTYPE'] , $params);
		
		//保存发布历史
		$user =  $this->getCookUser() ;
		$loginId = $user["LOGIN_ID"] ;
		
		$params = array() ;
		$params['guid'] = $this->create_guid() ;
		$params['result'] = $return ;
		$params['detail'] = json_encode($data) ;
		$params['loginId'] = $loginId ;
		$params['templateId'] = $templateId ;
		
		$this->Utils->exeSql("sql_ebay_publish_history_insert", $params) ;
	
		$this->response->type("json") ;
		$this->response->body($return)   ;
		
		return $this->response ;
	}
	
	function formatLocation( $locations ){
		if( empty( $locations ) ) return "" ;
		$ls = "" ;
		foreach( $locations as $l ){
			if( empty($ls) ){
				$ls = $l ;
			} else{
				$ls = ",".$l ;
			}
		}
		return $ls ;
	}

}