<?php
class PublishEbayController extends AppController {
    public $helpers = array( 'Html' , 'Form');
    var $uses = array('Utils');
  
    /**
     * 保存模板
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
		//format parymethod
		$data['PAYMENTMETHODS1'] = $pms ;
		
		$pms1 = "" ;
		foreach( $data['listingenhancement'] as $pm){
			if( $pms1 == "" ){
				$pms1 = $pm ;
			}else{
				$pms1 .= ",".$pm ;
			}
		}
		//format parymethod
		$data['LISTINGENHANCEMENT'] = $pms1 ;
		
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
		
		
		
		$data['RP_RETURNSACCEPTEDOPTION'] = $data['return_policy']['ReturnsAcceptedOption'] ;
		$data['RP_REUNDOPTION'] = $data['return_policy']['RefundOption'] ;
		$data['RP_RETURNSWITHINOPTION'] = $data['return_policy']['ReturnsWithinOption'] ;
		$data['RP_SHIPPINGCOSTPAIDBYOPTION'] = $data['return_policy']['ShippingCostPaidByOption'] ;
		$data['RP_DESCRIPTION'] = $data['return_policy']['Description'] ;
		
		$data['BRD_LPPA'] = "" ;
		
		if( isset( $data['buyerrequirementdetails']['LinkedPayPalAccount'] ) ){
			$data['BRD_LPPA'] = $data['buyerrequirementdetails']['LinkedPayPalAccount'];
			//LinkedPayPalAccount
		}
		
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
		
		//debug($data) ;
		//return ;
		
		debug($data) ;
		
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
		
		$this->response->type("text/javascript") ;
		$this->response->body("<script>window.top.location.reload()</script>")   ;
		
		return $this->response ;
	}
	
	/**
	 * 刊登产品
	 * @param unknown $templateId
	 */
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
		
		
		if( $listingType == 'Chinese' ){//拍马
			$xml .= "
			    </ShippingDetails>
			    <StartPrice><![CDATA[".$data['STARTPRICE']."]]></StartPrice>";
			if( !empty($data['BUYITNOWPRICE']) && $data['BUYITNOWPRICE'] !="0.0" ){
				$xml .= "    <BuyItNowPrice><![CDATA[".$data['BUYITNOWPRICE']."]]></BuyItNowPrice>";
			}
			
			if( !empty($data['RESERVEPRICE']) && $data['RESERVEPRICE'] !="0.0" ){
				$xml .= "     <ReservePrice>".$data['RESERVEPRICE']."</ReservePrice>";
			}
			
			if( !empty($data['LOTSIZE']) && $data['LOTSIZE'] !="0" ){
				$xml .= "     <LotSize>".$data['LOTSIZE']."</LotSize>";
			}
		}else{
			$xml .= "
			    </ShippingDetails>
			    <StartPrice><![CDATA[".$data['BUYITNOWPRICE']."]]></StartPrice>";
		}
		
		$array = explode(",", $data['PAYMENTMETHODS1']) ;
		foreach($array as $a) {
			if( !empty($a) )
			$xml .= "	   <PaymentMethods><![CDATA[".$a."]]></PaymentMethods>" ;
		}
		//<PaymentMethods><![CDATA[".$data['PAYMENTMETHODS1']."]]></PaymentMethods>
		
		
		
		$xml .= "<Title><![CDATA[".$data['ITEMTITLE']."]]></Title>";
		
		if( $data['CONDITIONID'] ){
			$xml .= "	   <ConditionID><![CDATA[".$data['CONDITIONID']."]]></ConditionID>";
		}
		
		if( $data['AUTOPAY'] ){
			$xml .= "	   <AutoPay>".$data['AUTOPAY']."</AutoPay>";
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
					<Name><![CDATA[$key]]></Name>
					<Value><![CDATA[$value]]></Value>
					</NameValueList>" ;
				}
				}
				$xml .= "		</ItemSpecifics>" ;
		}
		
		if( !empty( $data['LISTINGENHANCEMENT'] ) ){
			$xml .= "<ListingEnhancement><![CDATA[".$data['LISTINGENHANCEMENT']."]]></ListingEnhancement>" ;
		}
		
		if( !empty( $data['PRIVATELISTING'] ) ){
			$xml .= "<PrivateListing>".$data['PRIVATELISTING']."</PrivateListing>" ;
		}
		
		$brd = "" ;
		if( !empty( $data['BRD_LPPA'] ) ){
			$brd .= "<LinkedPayPalAccount>".$data['BRD_LPPA']."</LinkedPayPalAccount>" ;
		}
		
		if( !empty( $data['BRD_MBPV_COUNT'] ) &&  !empty( $data['BRD_MBPV_PERIOD'] ) ){
			$brd .= "<MaximumBuyerPolicyViolations>
				<Count>".$data['BRD_MBPV_COUNT']."</Count>
				<Period>".$data['BRD_MBPV_PERIOD']."</Period>
			</MaximumBuyerPolicyViolations>" ;
		}
		
		if( !empty( $data['BRD_MUIS_COUNT'] ) &&  !empty( $data['BRD_MUIS_PERIOD'] ) ){
			$brd .= "<MaximumUnpaidItemStrikesInfo>
				<Count>".$data['BRD_MUIS_COUNT']."</Count>
				<Period>".$data['BRD_MUIS_PERIOD']."</Period>
			</MaximumUnpaidItemStrikesInfo>" ;
		}
		
		if( !empty( $data['BRD_MIR_MIC'] )  ){
			$brd .= "<MaximumItemRequirements>
				<MaximumItemCount>".$data['BRD_MIR_MIC']."</MaximumItemCount>
			</MaximumItemRequirements>" ;
		}
		
		if( !empty($brd) ){
			$xml .= "<BuyerRequirementDetails>
				$brd
				</BuyerRequirementDetails>" ;
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

		//echo $xml;
		
		$baseUrl = $this->Utils->buildUrlByAccountId($data['ACCOUNT_ID'], "eBay/doItem") ;
		
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