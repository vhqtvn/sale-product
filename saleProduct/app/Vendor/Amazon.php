<?php
require_once("amazon/MarketplaceWebService/Client.php");
require_once("amazon/MarketplaceWebService/FeedClient.php");
require_once("amazon/MarketplaceWebService/Model/GetReportRequest.php");
require_once("amazon/MarketplaceWebService/Model/GetReportListRequest.php");
require_once("amazon/MarketplaceWebService/Model/RequestReportRequest.php");
require_once("amazon/MarketplaceWebService/Model/SubmitFeedRequest.php");
require_once("amazon/MarketplaceWebService/Model/GetFeedSubmissionResultRequest.php");
require_once("amazon/MarketplaceWebService/Exception.php");

 include('config/config_mock.php');

class Amazon {
	var $AWS_ACCESS_KEY_ID ; 
	var $AWS_SECRET_ACCESS_KEY ;
	var $APPLICATION_NAME;
	var $APPLICATION_VERSION;
	var $MERCHANT_ID ;
	var $MARKETPLACE_ID ;
	var $MerchantIdentifier ;
	var $APPLICATION_ID ;
	
	public function Amazon( 
		$AWS_ACCESS_KEY_ID, 
		$AWS_SECRET_ACCESS_KEY, 
		$APPLICATION_NAME, 
		$APPLICATION_VERSION, 
		$MERCHANT_ID, 
		$MARKETPLACE_ID,
		$MerchantIdentifier , 
		$APPLICATION_ID = '' 
	){
		$this->AWS_ACCESS_KEY_ID 	= $AWS_ACCESS_KEY_ID ;
		$this->AWS_SECRET_ACCESS_KEY= $AWS_SECRET_ACCESS_KEY ;
		$this->APPLICATION_NAME 	= $APPLICATION_NAME ;
		$this->APPLICATION_VERSION 	= $APPLICATION_VERSION ;
		$this->MERCHANT_ID 			= $MERCHANT_ID ;
		$this->MARKETPLACE_ID 		= $MARKETPLACE_ID ;
		$this->MerchantIdentifier 	= $MerchantIdentifier ;
		$this->APPLICATION_ID       = $APPLICATION_ID;
	}
	
	public function getFeedReport1($accountId ,$reportType,$querys =array()){
		$config = array (
				'ServiceURL' =>  "https://mws.amazonservices.com",
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
		
		$service = new MarketplaceWebService_Client(
				$this->AWS_ACCESS_KEY_ID,
				$this->AWS_SECRET_ACCESS_KEY,
				$config,
				$this->APPLICATION_NAME,
				$this->APPLICATION_VERSION );
		
		$marketplaceIdArray = array("Id" => array($this->MARKETPLACE_ID));
		
		
		//if( ! isset( $querys['LastUpdatedAfter']  ) ){
			//$querys['LastUpdatedAfter'] = date("Y-m-d H:i:s", time()-12*60*60) ;
		//}
		
		$StartDate = new DateTime('now', new DateTimeZone('UTC')) ;
		$StartDate->modify( '-16 hour' );
		
		$parameters = array (
				'Merchant' => $this->MERCHANT_ID,
				'MarketplaceIdList' => $marketplaceIdArray,
				'ReportType' => $reportType,
				'ReportOptions' => 'ShowSalesChannel=true',
				'StartDate'=>$StartDate
		);
		
		$request = new MarketplaceWebService_Model_RequestReportRequest($parameters);
		
		if(!empty( $querys['LastUpdatedAfter']  )){
			//$request->setLastUpdatedAfter(new DateTime(  $querys['LastUpdatedAfter']  , new DateTimeZone('UTC')));
		}
		
		$return = null ;
		
		try {
			$response = $service->requestReport($request);
		
			if ($response->isSetRequestReportResult()) {
				$requestReportResult = $response->getRequestReportResult();
		
				if ($requestReportResult->isSetReportRequestInfo()) {
					$reportRequestInfo = $requestReportResult->getReportRequestInfo();
					$reportRequestId = "" ;
					$reportType = "" ;
					if ($reportRequestInfo->isSetReportRequestId()) {
						$reportRequestId =  $reportRequestInfo->getReportRequestId() ;
					}
					if ($reportRequestInfo->isSetReportType()) {
						$reportType =  $reportRequestInfo->getReportType() ;
					}
		
					if( $reportRequestId != "" ){
						$return = array('reportRequestId'=>$reportRequestId,'reportType'=>$reportType) ;
					}
				}
			}
		} catch (MarketplaceWebService_Exception $ex) {
		}
		
		return $return ;
		
	}
	
	public function getFeedReport2($accountId ,$reportType,$reportRequestId){
		$config = array (
				'ServiceURL' =>  "https://mws.amazonservices.com",
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
		
		$service = new MarketplaceWebService_Client(
				$this->AWS_ACCESS_KEY_ID,
				$this->AWS_SECRET_ACCESS_KEY,
				$config,
				$this->APPLICATION_NAME,
				$this->APPLICATION_VERSION);
		
		$ReportRequestIdList = array("Id" => array($reportRequestId));
		
		$parameters = array (
				'Merchant' => $this->MERCHANT_ID,
				'ReportRequestIdList' => $ReportRequestIdList,
				'Acknowledged' => false,
		);
		
		$request = new MarketplaceWebService_Model_GetReportListRequest($parameters);
		
		$return = null ;
		
		try {
			$response = $service->getReportList($request);
		
			if ($response->isSetGetReportListResult()) {
				$getReportListResult = $response->getGetReportListResult();
				$reportInfoList = $getReportListResult->getReportInfoList();
				foreach ($reportInfoList as $reportInfo) {
					if ($reportInfo->isSetReportId()){
						$reportId = $reportInfo->getReportId() ;
						$return = array("reportId"=>$reportId,'reportType'=>$reportType) ;
					}
				}
			}
		} catch (MarketplaceWebService_Exception $ex) {
		}
		
		return $return ;
	}
	
	public function getFeedReport3($accountId ,$reportType,$reportId){
		$config = array (
				'ServiceURL' => "https://mws.amazonservices.com",
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
		
		$service = new MarketplaceWebService_Client(
				$this->AWS_ACCESS_KEY_ID,
				$this->AWS_SECRET_ACCESS_KEY,
				$config,
				$this->APPLICATION_NAME,
				$this->APPLICATION_VERSION);
		
		$parameters = array (
				'Merchant' => $this->MERCHANT_ID,
				'Report' => @fopen('php://memory', 'rw+'),
				'ReportId' => $reportId //"7383292363"//$reportId,
		);
		$request = new MarketplaceWebService_Model_GetReportRequest($parameters);
		
		try {
			$response = $service->getFeedReport($request,$accountId,$reportType);
		} catch (MarketplaceWebService_Exception $ex) {
		}
	}
	
	
	
		/**
	 * step1
	 * 发送report请求,获取产品信息
	 */
	public function getProductReport1( $accountId,$log = null ){
		 $config = array (
			  'ServiceURL' =>  "https://mws.amazonservices.com",
			  'ProxyHost' => null,
			  'ProxyPort' => -1,
			  'MaxErrorRetry' => 3,
			);
		
		 $service = new MarketplaceWebService_Client(
		     $this->AWS_ACCESS_KEY_ID, 
		     $this->AWS_SECRET_ACCESS_KEY, 
		     $config,
		     $this->APPLICATION_NAME,
		     $this->APPLICATION_VERSION);
		     
		$marketplaceIdArray = array("Id" => array($this->MARKETPLACE_ID));  
		
		 $parameters = array (
		   'Merchant' => $this->MERCHANT_ID,
		   'MarketplaceIdList' => $marketplaceIdArray,
		   'ReportType' => '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_',
		   'ReportOptions' => 'ShowSalesChannel=true'
		 );
		 
		 $request = new MarketplaceWebService_Model_RequestReportRequest($parameters); 
		 
		 $return = null ;
		 
	     try {
	          $response = $service->requestReport($request);
	          
	            if ($response->isSetRequestReportResult()) { 
	                $requestReportResult = $response->getRequestReportResult();
	                
	                if ($requestReportResult->isSetReportRequestInfo()) {
	                      $reportRequestInfo = $requestReportResult->getReportRequestInfo();
	                      $reportRequestId = "" ;
	                      $reportType = "" ;
	                      if ($reportRequestInfo->isSetReportRequestId()) {
	                          $reportRequestId =  $reportRequestInfo->getReportRequestId() ;
	                      }
	                      if ($reportRequestInfo->isSetReportType()) {
	                      	  $reportType =  $reportRequestInfo->getReportType() ;
	                      }
	                      
	                      if( $reportRequestId != "" ){
	                      	 $return = array('reportRequestId'=>$reportRequestId,'reportType'=>'_GET_FLAT_FILE_OPEN_LISTINGS_DATA_') ;
	                      }
	                  }
	            } 
	     } catch (MarketplaceWebService_Exception $ex) {
	     	$log->saveException("getProductReport1__$accountId",$ex) ;
	     }
	     
	     return $return ;
	}
	
	public function getProductReport2( $accountId , $reportRequestId,$log = null ){
		 $config = array (
			  'ServiceURL' =>  "https://mws.amazonservices.com",
			  'ProxyHost' => null,
			  'ProxyPort' => -1,
			  'MaxErrorRetry' => 3,
			);
		
		 $service = new MarketplaceWebService_Client(
		     $this->AWS_ACCESS_KEY_ID, 
		     $this->AWS_SECRET_ACCESS_KEY, 
		     $config,
		     $this->APPLICATION_NAME,
		     $this->APPLICATION_VERSION);
		  
		 $ReportRequestIdList = array("Id" => array($reportRequestId));
 
		 $parameters = array (
		   'Merchant' => $this->MERCHANT_ID,
		   'ReportRequestIdList' => $ReportRequestIdList,
		   'Acknowledged' => false, 
		 );

		 $request = new MarketplaceWebService_Model_GetReportListRequest($parameters); 
		 
		 $return = null ;
		 
	     try {
	          $response = $service->getReportList($request);
	    
	            if ($response->isSetGetReportListResult()) { 
                    $getReportListResult = $response->getGetReportListResult();
                    $reportInfoList = $getReportListResult->getReportInfoList();
                    foreach ($reportInfoList as $reportInfo) {
                        if ($reportInfo->isSetReportId()){
                            $reportId = $reportInfo->getReportId() ;
                            $return = array("reportId"=>$reportId,'reportType'=>'_GET_FLAT_FILE_OPEN_LISTINGS_DATA_') ;
                        }
                    }
                } 
	     } catch (MarketplaceWebService_Exception $ex) {
	     	$log->saveException("getProductReport2__$accountId",$ex) ;
	     }
	     
	     return $return ;
	}
	
	public function getProductReport3( $accountId ,$reportId,$log=null){
		 $config = array (
		  'ServiceURL' => "https://mws.amazonservices.com",
		  'ProxyHost' => null,
		  'ProxyPort' => -1,
		  'MaxErrorRetry' => 3,
		 );
		
		 $service = new MarketplaceWebService_Client(
		     $this->AWS_ACCESS_KEY_ID, 
		     $this->AWS_SECRET_ACCESS_KEY, 
		     $config,
		     $this->APPLICATION_NAME,
		     $this->APPLICATION_VERSION);
		     
		 $parameters = array (
		   'Merchant' => $this->MERCHANT_ID,
		   'Report' => @fopen('php://memory', 'rw+'),
		   'ReportId' => $reportId //"7383292363"//$reportId,
		 );
		 $request = new MarketplaceWebService_Model_GetReportRequest($parameters);
		 
		 try {
	           $response = $service->getProductReport($request,$accountId,"_GET_FLAT_FILE_OPEN_LISTINGS_DATA_");
	     } catch (MarketplaceWebService_Exception $ex) {
	     	$log->saveException("getProductReport3__$accountId",$ex) ;
	     }
	}
	
	public function getProductActiveReport1( $accountId,$log = null  ){

		 $config = array (
			  'ServiceURL' =>  "https://mws.amazonservices.com",
			  'ProxyHost' => null,
			  'ProxyPort' => -1,
			  'MaxErrorRetry' => 3,
			);
		
		 $service = new MarketplaceWebService_Client(
		     $this->AWS_ACCESS_KEY_ID, 
		     $this->AWS_SECRET_ACCESS_KEY, 
		     $config,
		     $this->APPLICATION_NAME,
		     $this->APPLICATION_VERSION);
		     
		$marketplaceIdArray = array("Id" => array($this->MARKETPLACE_ID));  
		
		 $parameters = array (
		   'Merchant' => $this->MERCHANT_ID,
		   'MarketplaceIdList' => $marketplaceIdArray,
		   'ReportType' => '_GET_MERCHANT_LISTINGS_DATA_',
		   'ReportOptions' => 'ShowSalesChannel=true'
		 );
		 
		 $request = new MarketplaceWebService_Model_RequestReportRequest($parameters); 
		 
		 $return = null ;
		 
	     try {
	          $response = $service->requestReport($request);
	          
	            if ($response->isSetRequestReportResult()) { 
	                $requestReportResult = $response->getRequestReportResult();
	                
	                if ($requestReportResult->isSetReportRequestInfo()) {
	                      $reportRequestInfo = $requestReportResult->getReportRequestInfo();
	                      $reportRequestId = "" ;
	                      $reportType = "" ;
	                      if ($reportRequestInfo->isSetReportRequestId()) {
	                          $reportRequestId =  $reportRequestInfo->getReportRequestId() ;
	                      }
	                      if ($reportRequestInfo->isSetReportType()) {
	                      	  $reportType =  $reportRequestInfo->getReportType() ;
	                      }
	                      
	                      if( $reportRequestId != "" ){
	                      	 $return = array('reportRequestId'=>$reportRequestId,'reportType'=>'_GET_MERCHANT_LISTINGS_DATA_') ;
	                      }
	                  }
	            } 
	     } catch (MarketplaceWebService_Exception $ex) {
	     	$log->saveException("getProductActiveReport1__$accountId",$ex) ;
	     }
	     
	     return $return ;
	}
	
	public function getProductActiveReport2( $accountId , $reportRequestId,$log = null ){
		 $config = array (
			  'ServiceURL' =>  "https://mws.amazonservices.com",
			  'ProxyHost' => null,
			  'ProxyPort' => -1,
			  'MaxErrorRetry' => 3,
			);
		
		 $service = new MarketplaceWebService_Client(
		     $this->AWS_ACCESS_KEY_ID, 
		     $this->AWS_SECRET_ACCESS_KEY, 
		     $config,
		     $this->APPLICATION_NAME,
		     $this->APPLICATION_VERSION);
		  
		 $ReportRequestIdList = array("Id" => array($reportRequestId));
 
		 $parameters = array (
		   'Merchant' => $this->MERCHANT_ID,
		   'ReportRequestIdList' => $ReportRequestIdList,
		   'Acknowledged' => false, 
		 );

		 $request = new MarketplaceWebService_Model_GetReportListRequest($parameters); 
		 
		 $return = null ;
		 
	     try {
	          $response = $service->getReportList($request);
	    
	            if ($response->isSetGetReportListResult()) { 
                    $getReportListResult = $response->getGetReportListResult();
                    $reportInfoList = $getReportListResult->getReportInfoList();
                    foreach ($reportInfoList as $reportInfo) {
                        if ($reportInfo->isSetReportId()){
                            $reportId = $reportInfo->getReportId() ;
                            $return = array("reportId"=>$reportId,'reportType'=>'_GET_MERCHANT_LISTINGS_DATA_') ;
                        }
                    }
                } 
	     } catch (MarketplaceWebService_Exception $ex) {
	     	$log->saveException("getProductActiveReport2__$accountId",$ex) ;
	     }
	     
	     return $return ;
	}
	
	public function getProductActiveReport3( $accountId ,$reportId,$log=null){
		 $config = array (
		  'ServiceURL' => "https://mws.amazonservices.com",
		  'ProxyHost' => null,
		  'ProxyPort' => -1,
		  'MaxErrorRetry' => 3,
		 );
		
		 $service = new MarketplaceWebService_Client(
		     $this->AWS_ACCESS_KEY_ID, 
		     $this->AWS_SECRET_ACCESS_KEY, 
		     $config,
		     $this->APPLICATION_NAME,
		     $this->APPLICATION_VERSION);
		     
		 $parameters = array (
		   'Merchant' => $this->MERCHANT_ID,
		   'Report' => @fopen('php://memory', 'rw+'),
		   'ReportId' => $reportId //"7383292363"//$reportId,
		 );
		 $request = new MarketplaceWebService_Model_GetReportRequest($parameters);
		 
		 try {
	           $response = $service->getProductReport($request,$accountId,"_GET_MERCHANT_LISTINGS_DATA_");
	     } catch (MarketplaceWebService_Exception $ex) {
	     	$log->saveException("getProductActiveReport3__$accountId",$ex) ;
	     }
	}
	
	
	public function updatePrice( $accountId ,$feed , $loginId ){
	     
	     $result = array() ;
	     if( mock_updatePrice ){
	     	$result  = array(
	     			'feedsubmissionId'=>'xxxx_mock',
	     			'loginId' =>'mock',
	     			'type'=>'_POST_PRODUCT_PRICING_DATA_',
	     			'accountId'=>$accountId,
	     			'status'=>'Complete',
	     			'message'=>'',
	     			'feed'=>$feed
	     	) ;
	     }else{
	     	$result = $this->postFeedSubmission($accountId ,$feed , $loginId ,"_POST_PRODUCT_PRICING_DATA_");
	     }
	     // return $this->postFeedSubmission($accountId ,$feed , $loginId ,"_POST_INVENTORY_AVAILABILITY_DATA_");
	     return $result ;
	}
	
	public function updateInventory($accountId,$feed,$loginId){
		$result = array() ;
		if( mock_updateInventory ){
			$result  = array(
					'feedsubmissionId'=>'xxxx_mock',
					'loginId' =>'mock',
					'type'=>'_POST_INVENTORY_AVAILABILITY_DATA_',
					'accountId'=>$accountId,
					'status'=>'Complete',
					'message'=>'',
					'feed'=>$feed
			) ;
		}else{
			$result = $this->postFeedSubmission($accountId ,$feed , $loginId ,"_POST_INVENTORY_AVAILABILITY_DATA_");
		}
	    // return $this->postFeedSubmission($accountId ,$feed , $loginId ,"_POST_INVENTORY_AVAILABILITY_DATA_");
	    return $result ;
	}
	
	public function updateOrderTrackNumber( $accountId ,$feed , $loginId ){
		$result = array() ;
		if( mock_updateOrderTrackNumber ){
			$result  = array(
					'feedsubmissionId'=>'xxxx_mock',
					'loginId' =>'mock',
					'type'=>'_POST_ORDER_FULFILLMENT_DATA_',
					'accountId'=>$accountId,
					'status'=>'Complete',
					'message'=>'',
					'feed'=>$feed
			) ;
		}else{
			$result = $this->postFeedSubmission($accountId ,$feed , $loginId ,"_POST_ORDER_FULFILLMENT_DATA_");
		}
		// return $this->postFeedSubmission($accountId ,$feed , $loginId ,"_POST_INVENTORY_AVAILABILITY_DATA_");
		return $result ;
	}
	
	public function postFeedSubmission( $accountId ,$feed , $loginId ,$feedType){
		$config = array (
		  'ServiceURL' => "https://mws.amazonservices.com",
		  'ProxyHost' => null,
		  'ProxyPort' => -1,
		  'MaxErrorRetry' => 3,
		);
		
		 $service = new MarketplaceWebService_FeedClient(
		     $this->AWS_ACCESS_KEY_ID, 
		     $this->AWS_SECRET_ACCESS_KEY, 
		     $config,
		     $this->APPLICATION_NAME,
		     $this->APPLICATION_VERSION);
		     
		$marketplaceIdArray = array("Id" => array($this->MARKETPLACE_ID));
		
		$feedHandle = @fopen('php://temp', 'rw+');
		fwrite($feedHandle, $feed);
		rewind($feedHandle);
		$parameters = array (
		  'Merchant' => $this->MERCHANT_ID,
		  'MarketplaceIdList' => $marketplaceIdArray,
		  'FeedType' => $feedType,
		  'FeedContent' => $feedHandle,
		  'PurgeAndReplace' => false,
		  'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
		);

		$request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
		$array = null ;
		try {
                $response = $service->submitFeed($request);

                if ($response->isSetSubmitFeedResult()) { 
                    $submitFeedResult = $response->getSubmitFeedResult();
                    if ($submitFeedResult->isSetFeedSubmissionInfo()) { 
                        $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                        
                        $feedsubmissionId = "" ;
                        $feedType  = "" ;
                        $feedStatus = "" ;
                        
                        if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                        {
                            $feedsubmissionId = $feedSubmissionInfo->getFeedSubmissionId()  ;
                        }
                        if ($feedSubmissionInfo->isSetFeedType()) 
                        {
                            $feedType = $feedSubmissionInfo->getFeedType()  ;
                        }
                        
                        if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                        {
                            $feedStatus = $feedSubmissionInfo->getFeedProcessingStatus() ;
                        }
                        
                        $array = array(
								"feedsubmissionId"=>$feedsubmissionId,
								"loginId"=>$loginId,
								"type"=>$feedType,
								"accountId"=>$accountId,
								"status"=>$feedStatus,
								"message"=>"",
								"feed"=>""
                        );
                        
                      //  $amazon->saveAccountFeed($array) ;
		
                    } 
                } 
	     } catch (MarketplaceWebService_Exception $ex) {
	     	print_r( $ex );
	     }
	     
	     @fclose($feedHandle);
	     
	     return $array;
	}
	
	public function getFeedSubmissionResult($accountId,$feedSubmissionId){
		$config = array (
		  'ServiceURL' => "https://mws.amazonservices.com",
		  'ProxyHost' => null,
		  'ProxyPort' => -1,
		  'MaxErrorRetry' => 3,
		);
		
		 $service = new MarketplaceWebService_FeedClient(
		    $this->AWS_ACCESS_KEY_ID, 
		    $this->AWS_SECRET_ACCESS_KEY, 
		    $config,
		    $this->APPLICATION_NAME,
		    $this->APPLICATION_VERSION);
		    
		$parameters = array (
		  'Merchant' => $this->MERCHANT_ID,
		  'FeedSubmissionId' => $feedSubmissionId,//'5628762486',
		  'FeedSubmissionResult' => @fopen('php://memory', 'rw+'),
		); 
		
		$request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);
		
		try {
              $response = $service->getFeedSubmissionResult($request);

			  print_r($response) ;
              /* 
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        GetFeedSubmissionResultResponse\n");
                if ($response->isSetGetFeedSubmissionResultResult()) {
                  $getFeedSubmissionResultResult = $response->getGetFeedSubmissionResultResult(); 
                  echo ("            GetFeedSubmissionResult");
                  
                  if ($getFeedSubmissionResultResult->isSetContentMd5()) {
                    echo ("                ContentMd5");
                    echo ("                " . $getFeedSubmissionResultResult->getContentMd5() . "\n");
                  }
                }
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

                echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");*/
	     } catch (MarketplaceWebService_Exception $ex) {
	         echo("Caught Exception: " . $ex->getMessage() . "\n");
	         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
	         echo("Error Code: " . $ex->getErrorCode() . "\n");
	         echo("Error Type: " . $ex->getErrorType() . "\n");
	         echo("Request ID: " . $ex->getRequestId() . "\n");
	         echo("XML: " . $ex->getXML() . "\n");
	         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
	     }
	}

	
	public function getFBAInventory1($accountId){ //_GET_AFN_INVENTORY_DATA_
		$config = array (
			  'ServiceURL' =>  "https://mws.amazonservices.com",
			  'ProxyHost' => null,
			  'ProxyPort' => -1,
			  'MaxErrorRetry' => 3,
			);
		
		 $service = new MarketplaceWebService_Client(
		     $this->AWS_ACCESS_KEY_ID, 
		     $this->AWS_SECRET_ACCESS_KEY, 
		     $config,
		     $this->APPLICATION_NAME,
		     $this->APPLICATION_VERSION);
		     
		$marketplaceIdArray = array("Id" => array($this->MARKETPLACE_ID));  
		
		 $parameters = array (
		   'Merchant' => $this->MERCHANT_ID,
		   'MarketplaceIdList' => $marketplaceIdArray,
		   'ReportType' => '_GET_AFN_INVENTORY_DATA_',
		   'ReportOptions' => 'ShowSalesChannel=true'
		 );
		 
		 $request = new MarketplaceWebService_Model_RequestReportRequest($parameters); 
		 
		 $return = null ;
		 
	     try {
	          $response = $service->requestReport($request);
	          
	            if ($response->isSetRequestReportResult()) { 
	                $requestReportResult = $response->getRequestReportResult();
	                
	                if ($requestReportResult->isSetReportRequestInfo()) {
	                      $reportRequestInfo = $requestReportResult->getReportRequestInfo();
	                      $reportRequestId = "" ;
	                      $reportType = "" ;
	                      if ($reportRequestInfo->isSetReportRequestId()) {
	                          $reportRequestId =  $reportRequestInfo->getReportRequestId() ;
	                      }
	                      if ($reportRequestInfo->isSetReportType()) {
	                      	  $reportType =  $reportRequestInfo->getReportType() ;
	                      }
	                      
	                      if( $reportRequestId != "" ){
	                      	 $return = array('reportRequestId'=>$reportRequestId,'reportType'=>'_GET_AFN_INVENTORY_DATA_') ;
	                      }
	                  }
	            } 
	     } catch (MarketplaceWebService_Exception $ex) {
	     }
	     
	     return $return ;
	}
	
	public function getFBAInventory2( $accountId , $reportRequestId ){
		$config = array (
				'ServiceURL' =>  "https://mws.amazonservices.com",
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
	
		$service = new MarketplaceWebService_Client(
				$this->AWS_ACCESS_KEY_ID,
				$this->AWS_SECRET_ACCESS_KEY,
				$config,
				$this->APPLICATION_NAME,
				$this->APPLICATION_VERSION);
	
		$ReportRequestIdList = array("Id" => array($reportRequestId));
	
		$parameters = array (
				'Merchant' => $this->MERCHANT_ID,
				'ReportRequestIdList' => $ReportRequestIdList,
				'Acknowledged' => false,
		);
	
		$request = new MarketplaceWebService_Model_GetReportListRequest($parameters);
	
		$return = null ;
	
		try {
			$response = $service->getReportList($request);
	
			if ($response->isSetGetReportListResult()) {
				$getReportListResult = $response->getGetReportListResult();
				$reportInfoList = $getReportListResult->getReportInfoList();
				foreach ($reportInfoList as $reportInfo) {
					if ($reportInfo->isSetReportId()){
						$reportId = $reportInfo->getReportId() ;
						$return = array("reportId"=>$reportId,'reportType'=>'_GET_AFN_INVENTORY_DATA_') ;
					}
				}
			}
		} catch (MarketplaceWebService_Exception $ex) {
		}
	
		return $return ;
	}
	
	public function getFBAInventory3( $accountId ,$reportId){
		$config = array (
				'ServiceURL' => "https://mws.amazonservices.com",
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
		);
	
		$service = new MarketplaceWebService_Client(
				$this->AWS_ACCESS_KEY_ID,
				$this->AWS_SECRET_ACCESS_KEY,
				$config,
				$this->APPLICATION_NAME,
				$this->APPLICATION_VERSION);
	
		$parameters = array (
				'Merchant' => $this->MERCHANT_ID,
				'Report' => @fopen('php://memory', 'rw+'),
				'ReportId' => $reportId //"7383292363"//$reportId,
		);
		$request = new MarketplaceWebService_Model_GetReportRequest($parameters);
	
		try {
			$response = $service->getFbaInventryReport($request,$accountId,"_GET_AFN_INVENTORY_DATA_");
		} catch (MarketplaceWebService_Exception $ex) {
		}
	}
}
?>