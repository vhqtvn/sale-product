<?php
// $Id: EbatNs_Client.php,v 1.6 2007/08/30 07:35:14 michael Exp $
/* $Log: EbatNs_Client.php,v $
/* Revision 1.6  2007/08/30 07:35:14  michael
/* added replacing HTTP 1.1 header
/*
/* Revision 1.5  2007/08/03 12:17:59  michael
/* *** empty log message ***
/*
/* Revision 1.4  2007/08/01 11:22:22  michael
/* *** empty log message ***
/*
/* Revision 1.3  2007/06/05 10:12:23  michael
/* fix bug
/*
/* Revision 1.2  2007/05/31 12:03:37  michael
/* added XML-style client
/*
/* Revision 1.1  2007/05/31 11:38:00  michael
/* - initial checkin
/* - version < 513
/*
 * 
 * 9     12.02.07 10:23 Charnisch
 * - changed logging wire request
 * 
 * 8     12.02.07 10:05 Charnisch
 * - added subject on request-logging
 * 
 * 7     9.02.07 16:44 Charnisch
 * minor bugfixing:
 * - removed doubled $
 * - renamed variable ($ignoreWarning -> $parseMode)
 * 
 * 6     9.02.07 14:32 Charnisch
 * 
 * 5     29.05.06 9:59 Charnisch
 * 
 * 4     11.02.06 16:58 Charnisch
/* Revision 1.1  2006/02/03 10:52:01  michael
/* initial checkin
/*
*/
require_once 'UserIdPasswordType.php';

require_once 'EbatNs_RequesterCredentialType.php';
require_once 'EbatNs_RequestHeaderType.php';
require_once 'EbatNs_ResponseError.php';
require_once 'EbatNs_ResponseParser.php';

require_once 'EbatNs_DataConverter.php';

class EbatNs_Client
{ 
	// endpoint for call
	var $_ep;
	var $_session;
	var $_currentResult;
	var $_parser = null; 
	// callback-methods/functions for data-handling
	var $_hasCallbacks = false;
	var $_callbacks = null; 
	// EbatNs_DataConverter object
	var $_dataConverter = null;
	
	var $_logger = null;
	var $_parserOptions = null;
	
	var $_paginationElementCounter = 0;
	var $_paginationMaxElements = -1;
	
	var $_transportOptions = array();
	var $_loggingOptions   = array();
	var $_callUsage = array();
	
	function getVersion()
	{
		return EBAY_WSDL_VERSION;
	}	

	function EbatNs_Client( $session, $converter = 'EbatNs_DataConverterIso' )
	{
		$this->_session = $session;
		if ($converter)
			$this->_dataConverter = new $converter();
		$this->_parser = null;
		
		$timeout = $session->getRequestTimeout();
		if (!$timeout)
			$timeout = 300;
		$httpCompress = $session->getUseHttpCompression();	
		
		$this->setTransportOptions(
				array(
					'HTTP_TIMEOUT'  => $timeout, 
					'HTTP_COMPRESS' => $httpCompress));
	} 
	
	function resetPaginationCounter($maxElements = -1)
	{
		$this->_paginationElementCounter = 0;
		if ($maxElements > 0)
			$this->_paginationMaxElements = $maxElements;
		else
			$this->_paginationMaxElements = -1;
	}
	
	function incrementPaginationCounter()
	{
		$this->_paginationElementCounter++;
		
		if ($this->_paginationMaxElements > 0 && ($this->_paginationElementCounter > $this->_paginationMaxElements))
			return false;
		else
			return true;
	}
	
	function getPaginationCounter()
	{
		return $this->_paginationElementCounter;
	}
	
	function setParserOption($name, $value = true)
	{
		$this->_parserOptions[$name] = $value;
	}
	
	function log( $msg, $subject = null )
	{
		if ( $this->_logger )
			$this->_logger->log( $msg, $subject );
	} 
	
	function logXml( $xmlMsg, $subject = null )
	{
		if ( $this->_logger )
			$this->_logger->logXml( $xmlMsg, $subject );
	} 
	
	function attachLogger(& $logger)
	{
		$this->_logger = $logger;
	}
	
	// HTTP_TIMEOUT: default 300 s
	// HTTP_COMPRESS: default true
	function setTransportOptions($options)
	{
		$this->_transportOptions = array_merge($this->_transportOptions, $options);
	}
	
	// LOG_TIMEPOINTS: true/false
	// LOG_API_USAGE: true/false
	function setLoggingOptions($options)
	{
		$this->_loggingOptions = array_merge($this->_loggingOptions, $options);
	}
	
	//
	// timepoint-tracing
	//
	var $_timePoints = null;
	var $_timePointsSEQ = null;
	
	function _getMicroseconds()
	{
		list( $ms, $s ) = explode( ' ', microtime() );
		return floor( $ms * 1000 ) + 1000 * $s;
	} 
	
	function _getElapsed( $start )
	{
		return $this->_getMicroseconds() - $start;
	} 
	
	function _startTp( $key )
	{
		if (!$this->_loggingOptions['LOG_TIMEPOINTS'])
			return;
		
		if ( isset( $this->_timePoints[$key] ) )
			$tp = $this->_timePoints[$key];
		
		$tp['start_tp'] = time();
		
		$tp['start'] = $this->_getMicroseconds();
		$this->_timePoints[$key] = $tp;
	} 
	
	function _stopTp( $key )
	{
		if (!$this->_loggingOptions['LOG_TIMEPOINTS'])
			return;
		
		if ( isset( $this->_timePoints[$key]['start'] ) )
		{
			$tp = $this->_timePoints[$key];
			$tp['duration'] = $this->_getElapsed( $tp['start'] ) . 'ms';
			unset( $tp['start'] );
			$this->_timePoints[$key] = $tp;
		} 
	} 
	
	function _logTp()
	{
		if (!$this->_loggingOptions['LOG_TIMEPOINTS'])
			return;
		
		// log the timepoint-information
		ob_start();
		echo "<pre><br>\n";
		print_r($this->_timePoints);
		print_r("</pre><br>\n");
		$msg = ob_get_clean();
		$this->log($msg, '_EBATNS_TIMEPOINTS');
	}
	
	//
	// end timepoint-tracing
	//
	
	// callusage
	function _incrementApiUsage($apiCall)
	{
		if (!$this->_loggingOptions['LOG_API_USAGE'])	
			return;
		
		$this->_callUsage[$apiCall] = $this->_callUsage[$apiCall] + 1;
	}
	
	function getApiUsage()
	{
		return $this->_callUsage;
	}
	
	function & getParser($tns = 'urn:ebay:apis:eBLBaseComponents', $parserOptions = null, $recreate = true)
	{
		if ($recreate)
			$this->_parser = null;
		
		if (!$this->_parser)
		{
			if ($parserOptions)
				$this->_parserOptions = $parserOptions;
			$this->_parser = &new EbatNs_ResponseParser( &$this, $tns, $this->_parserOptions );
		}
		return ($t = &$this->_parser);
	}
	
	// should return true if the data should NOT be included to the
	// response-object !
	function _handleDataType( $typeName, &$value, $mapName )
	{
		if ( $this->_hasCallbacks )
		{
			if (isset($this->_callbacks[strtolower( $typeName )]))
				$callback = $this->_callbacks[strtolower( $typeName )];
			else
				$callback = null;
			if ( $callback )
			{
				if ( is_object( $callback['object'] ) )
				{
					return call_user_method( $callback['method'], $callback['object'], $typeName, & $value, $mapName, & $this );
				} 
				else
				{
					return call_user_func( $callback['method'], $typeName, & $value, $mapName, & $this );
				} 
			} 
		} 
		return false;
	} 
	
	// $typeName as defined in Schema
	// $method (callback, either string or array with object/method)
	function setHandler( $typeName, $method )
	{
		$this->_hasCallbacks = true;
		if ( is_array( $method ) )
		{
			$callback['object'] = &$method[0];
			$callback['method'] = $method[1];
		} 
		else
		{
			$callback['object'] = null;
			$callback['method'] = $method;
		} 
		
		$this->_callbacks[strtolower( $typeName )] = $callback;
	} 
	
	function _makeSessionHeader()
	{
		$cred = new UserIdPasswordType();
		$cred->AppId = $this->_session->getAppId();
		$cred->DevId = $this->_session->getDevId();
		$cred->AuthCert = $this->_session->getCertId();
		if ( $this->_session->getTokenMode() == 0 )
		{
			$cred->Username = $this->_session->getRequestUser();
			$cred->Password = $this->_session->getRequestPassword();
		} 
		$reqCred = new EbatNs_RequesterCredentialType();
		$reqCred->Credentials = $cred;
		
		if ( $this->_session->getTokenMode() == 1 )
		{
			$this->_session->ReadTokenFile();
			$reqCred->eBayAuthToken = $this->_session->getRequestToken();
		} 
		
		$header = new EbatNs_RequestHeaderType();
		$header->RequesterCredentials = $reqCred;
		
		return $header;
	} 
	
	function call( $method, $request, $parseMode = EBATNS_PARSEMODE_CALL )
	{
		$this->_startTp('API call ' . $method);
		$this->_incrementApiUsage($method);
		
		$this->_startTp('Encoding SOAP Message');
		
		$body = $this->encodeMessage( $method, $request );
		$header = $this->_makeSessionHeader();
		
		$message = $this->buildMessage( $body, $header );
		
		$ep = $this->_session->getApiUrl();
		$ep .= '?callname=' . $method;
		$ep .= '&siteid=' . $this->_session->getSiteId();
		$ep .= '&appid=' . $this->_session->getAppId();
		$ep .= '&version=' . $this->getVersion();
		$ep .= '&routing=default';
		$this->_ep = $ep;
		
		$this->_stopTp('Encoding SOAP Message');
		$this->_startTp('Sending SOAP Message');
		
		$responseMsg = $this->sendMessage( $message );
		
		$this->_stopTp('Sending SOAP Message');
		
		if ( $responseMsg )
		{
			$this->_startTp('Decoding SOAP Message');
			$ret = & $this->decodeMessage( $method, $responseMsg, $parseMode );
			$this->_stopTp('Decoding SOAP Message');
		}
		else
		{
			$ret = & $this->_currentResult;
		}
		
		$this->_stopTp('API call ' . $method);
		$this->_logTp();
		
		return $ret;
	} 
	
	// should return a serialized XML of the outgoing message
	function encodeMessage( $method, $request )
	{
		return $request->serialize( $method . 'Request', $request, null, true, null, $this->_dataConverter );
	} 
	// should transform the response (body) to a PHP object structure
	function &decodeMessage( $method, &$msg, $parseMode )
	{
		$this->_parser = &new EbatNs_ResponseParser( &$this, 'urn:ebay:apis:eBLBaseComponents', $this->_parserOptions );
		return $this->_parser->decode( $method . 'Response', $msg, $parseMode );
	} 
	// should generate a complete SOAP-envelope for the request
	function buildMessage( $body, $header )
	{
		$soap = '<?xml version="1.0" encoding="utf-8"?>';
		$soap .= '<soap:Envelope';
		$soap .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
		$soap .= ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"';
		$soap .= ' xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"';
		$soap .= ' encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"';
		$soap .= ' xmlns="urn:ebay:apis:eBLBaseComponents"';
		$soap .= ' >';
		
		if ( $header )
			$soap .= $header->serialize( 'soap:Header', $header, null, true, null, $t = null );
		
		$soap .= '<soap:Body>';
		$soap .= $body;
		$soap .= '</soap:Body>';
		$soap .= '</soap:Envelope>';
		return $soap;
	}
	
	// this method will generate a notification-style message body
	// out of a response from a call
	function _buildNotificationMessage($response, $simulatedMessageName, $tns, $addData = null)
	{
		if ($addData)
		{
			foreach($addData as $key => $value)
			{
				$response->{$key} = $value;
			}
		}		
		$response->setTypeAttribute('xmlns', $tns);
		$msgBody = $response->serialize( $simulatedMessageName, $response, isset($response->attributeValues) ? $response->attributeValues : null, true, null, $this->_dataConverter );
		
		$soap = '<?xml version="1.0" encoding="utf-8"?>';
		$soap .= '<soapenv:Envelope';
		$soap .= ' xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"';
		$soap .= ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"';
		$soap .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
		$soap .= '>';
		$soap .= '<soapenv:Header>';
		$soap .= '<ebl:RequesterCredentials soapenv:mustUnderstand="0" xmlns:ns="urn:ebay:apis:eBLBaseComponents" xmlns:ebl="urn:ebay:apis:eBLBaseComponents">';
		$soap .= '<ebl:NotificationSignature xmlns:ebl="urn:ebay:apis:eBLBaseComponents">invalid_simulation</ebl:NotificationSignature>';
		$soap .= '</ebl:RequesterCredentials>';
		$soap .= '</soapenv:Header>';
		$soap .= '<soapenv:Body>';
		$soap .= $msgBody;
		$soap .= '</soapenv:Body>';
		$soap .= '</soapenv:Envelope>';
		
		return $soap;
	}
	
	// should send the message to the endpoint
	// the result should be parsed out of the envelope and return as the plain
	// response-body.
	function sendMessage( $message )
	{
		$this->_currentResult = null;
		
		$this->log( $this->_ep, 'RequestUrl' );
		$this->logXml( $message, 'Request' );
		
		$timeout = $this->_transportOptions['HTTP_TIMEOUT'];
		if (!$timeout || $timeout <= 0)
			$timeout = 300;
		
		$soapaction = 'dummy';
		
		$ch = curl_init();
		$reqHeaders[] = 'Content-Type: text/xml;charset=utf-8';
		if ($this->_transportOptions['HTTP_COMPRESS'])
		{
			$reqHeaders[] = 'Accept-Encoding: gzip, deflate';
			curl_setopt( $ch, CURLOPT_ENCODING, "gzip");
			curl_setopt( $ch, CURLOPT_ENCODING, "deflate");
		}
		$reqHeaders[] = 'SOAPAction: "' . $soapaction . '"';
		
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $reqHeaders );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'ebatns;1.0' );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $message );
		curl_setopt( $ch, CURLOPT_URL, $this->_ep );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_FAILONERROR, 0 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_HTTP_VERSION, 1 );
		
		// added support for multi-threaded clients
		if (isset($this->_transportOptions['HTTP_CURL_MULTITHREADED']))
		{
			curl_setopt( $ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0 );
			// be aware of the following:
			// - CURLOPT_NOSIGNAL is NOT defined in the standard-PHP cURL ext	(PHP 4.x)
			// so the usage need a patch and rebuild of the curl.so or "inline" PHP Version
			// Not using CURLOPT_NOSIGNAL might break if any signal-handlers are installed. So
			// the usage might be recommend but it is not must.
			// curl_setopt( $ch, CURLOPT_NOSIGNAL, true);			
			
			// - using cURL together with OpenSSL absolutely needs the multi-threading
			// looking functions for OpenSSL (see http://curl.haxx.se/libcurl/c/libcurl-tutorial.html#Multi-threading)
			// As these callbacks are NOT implemented in PHP 4.x BUT in PHP 5.x you have to do the implementation
			// for your own in PHP 4.x or switch to PHP 5.x 
		}
		
		if ($this->_transportOptions['HTTP_VERBOSE'])
		{
			curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
			ob_start();
		}
		
		$responseRaw = curl_exec( $ch );
		
		if ( !$responseRaw )
		{
			$this->_currentResult = new EbatNs_ResponseError();
			$this->_currentResult->raise( 'curl_error ' . curl_errno( $ch ) . ' ' . curl_error( $ch ), 80000 + 1, EBAT_SEVERITY_ERROR );
			curl_close( $ch );
			
			return null;
		} 
		else
		{
			curl_close( $ch );
			
			$responseBody = null;
			if ( preg_match( "/^(.*?)\r?\n\r?\n(.*)/s", $responseRaw, $match ) )
			{
				$responseBody = $match[2];
				$headerLines = split( "\r?\n", $match[1] );
				foreach ( $headerLines as $line )
				{
					if ( strpos( $line, ':' ) === false )
					{
						$responseHeaders[0] = $line;
						continue;
					} 
					list( $key, $value ) = split( ':', $line );
					$responseHeaders[strtolower( $key )] = trim( $value );
				} 
			} 
			
			if ($responseBody)
				$this->logXml( $responseBody, 'Response' );
			else
				$this->logXml( $responseRaw, 'ResponseRaw' );
		} 
		
		return $responseBody;
	} 
	
	function callShoppingApiStyle($method, $request, $parseMode = EBATNS_PARSEMODE_CALL)
	{
		// we support Production here ! Do we have Sandbox Support for the Shopping API ?!?
		if ($this->_session->getAppMode() == 1)
			$ep = 'http://open.api.ebay.com/shopping';
		else
			$ep = 'http://open.api.ebay.com/shopping';
		
		// place all data into theirs header
		$reqHeaders[] = 'X-EBAY-API-VERSION: ' . $this->getVersion();
		$reqHeaders[] = 'X-EBAY-API-APP-ID: ' . $this->_session->getAppId();
		$reqHeaders[] = 'X-EBAY-API-CALL-NAME: ' . $method;
		$siteId = $this->_session->getSiteId();
		if (empty($siteId))
			$reqHeaders[] = 'X-EBAY-API-SITE-ID: 0';
		else
			$reqHeaders[] = 'X-EBAY-API-SITE-ID: ' . $siteId;
		$reqHeaders[] = 'X-EBAY-API-REQUEST-ENCODING: XML';
		
		$body = $this->encodeMessageXmlStyle( $method, $request );
		
		$message = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$message .= $body;
		
		$this->_ep = $ep;
		
		$responseMsg = $this->sendMessageShoppingApiStyle( $message, $reqHeaders );
		
		if ( $responseMsg )
		{
			$this->_startTp('Decoding SOAP Message');
			$ret = & $this->decodeMessage( $method, $responseMsg, $parseMode );
			$this->_stopTp('Decoding SOAP Message');
		}
		else
		{
			$ret = & $this->_currentResult;
		}
		
		return $ret;
	}
	
	function sendMessageShoppingApiStyleNonCurl( $message, $extraXmlHeaders )
	{
		// this is the part for systems that are not have cURL installed 
		$transport = new EbatNs_HttpTransport();
		if (is_array($extraXmlHeaders))
			$reqHeaders = array_merge($reqHeaders, $extraXmlHeaders);
		
		$responseRaw = $transport->Post($this->_ep, $message, $reqHeaders);
		if (!$responseRaw)
		{
			$this->_currentResult = new EbatNs_ResponseError();
			$this->_currentResult->raise( 'transport error (none curl) ', 90000 + 1, EBAT_SEVERITY_ERROR );
			return null;
		}
		else
		{
			if (isset($responseRaw['errors']))
			{
				$this->_currentResult = new EbatNs_ResponseError();
				$this->_currentResult->raise( 'transport error (none curl) ' . $responseRaw['errors'], 90000 + 2, EBAT_SEVERITY_ERROR );
				return null;
			}
			
			$responseBody = $responseRaw['data'];
			if ($responseBody)
				$this->logXml( $responseBody, 'Response' );
			else
				$this->logXml( $responseRaw, 'ResponseRaw' );
			
			return $responseBody;
		}
	}

	function sendMessageShoppingApiStyle( $message, $extraXmlHeaders )
	{
		$this->_currentResult = null;
		
		$this->log( $this->_ep, 'RequestUrl' );
		$this->logXml( $message, 'Request' );
		
		$timeout = $this->_transportOptions['HTTP_TIMEOUT'];
		if (!$timeout || $timeout <= 0)
			$timeout = 300;
		
		// if we have a special HttpTransport-class defined use it !
		if (class_exists('EbatNs_HttpTransport'))
			return $this->sendMessageShoppingApiStyleNonCurl($message, $extraXmlHeaders);
		
		// continue with curl support !				
		$ch = curl_init();
		
		$reqHeaders[] = 'Content-Type: text/xml;charset=utf-8';
		
		if ($this->_transportOptions['HTTP_COMPRESS'])
		{
			$reqHeaders[] = 'Accept-Encoding: gzip, deflate';
			curl_setopt( $ch, CURLOPT_ENCODING, "gzip");
			curl_setopt( $ch, CURLOPT_ENCODING, "deflate");
		}
		
		if (is_array($extraXmlHeaders))
			$reqHeaders = array_merge($reqHeaders, $extraXmlHeaders);
		
		ob_start();
		print_r($reqHeaders);
		$this->log(ob_get_clean(), 'Request headers');
		
		curl_setopt( $ch, CURLOPT_URL, $this->_ep );
		
		// curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
		// curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
		
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $reqHeaders );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'ebatns;shapi;1.0' );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $message );
		
		curl_setopt( $ch, CURLOPT_FAILONERROR, 0 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_HTTP_VERSION, 1 );
		
		// added support for multi-threaded clients
		if (isset($this->_transportOptions['HTTP_CURL_MULTITHREADED']))
		{
			curl_setopt( $ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0 );
		}
		
		$responseRaw = curl_exec( $ch );
		
		if ( !$responseRaw )
		{
			$this->_currentResult = new EbatNs_ResponseError();
			$this->_currentResult->raise( 'curl_error ' . curl_errno( $ch ) . ' ' . curl_error( $ch ), 80000 + 1, EBAT_SEVERITY_ERROR );
			curl_close( $ch );
			
			return null;
		} 
		else
		{
			curl_close( $ch );
			
			$responseBody = null;
			if ( preg_match( "/^(.*?)\r?\n\r?\n(.*)/s", $responseRaw, $match ) )
			{
				$responseBody = $match[2];
				$headerLines = split( "\r?\n", $match[1] );
				foreach ( $headerLines as $line )
				{
					if ( strpos( $line, ':' ) === false )
					{
						$responseHeaders[0] = $line;
						continue;
					} 
					list( $key, $value ) = split( ':', $line );
					$responseHeaders[strtolower( $key )] = trim( $value );
				} 
			} 
			
			if ($responseBody)
				$this->logXml( $responseBody, 'Response' );
			else
				$this->logXml( $responseRaw, 'ResponseRaw' );
		} 
		
		return $responseBody;
	} 

	function callXmlStyle( $method, $request, $parseMode = EBATNS_PARSEMODE_CALL )
	{
		// Inject the Credentials into the request here !
		$request->_elements['RequesterCredentials'] = array(
				'required' => false,
				'type' => 'EbatNs_RequesterCredentialType',
				'nsURI' => 'urn:ebay:apis:eBLBaseComponents',
				'array' => false,
				'cardinality' => '0..1');
		
		$reqCred = new EbatNs_RequesterCredentialType();
		if ( $this->_session->getTokenMode() == 0 )
		{
			$cred = new UserIdPasswordType();
			$cred->Username = $this->_session->getRequestUser();
			$cred->Password = $this->_session->getRequestPassword();
			$reqCred->Credentials = $cred;
		} 
		
		if ( $this->_session->getTokenMode() == 1 )
		{
			$this->_session->ReadTokenFile();
			$reqCred->eBayAuthToken = $this->_session->getRequestToken();
		} 
		
		$request->RequesterCredentials = $reqCred;
		
		// we support only Sandbox and Production here !
		if ($this->_session->getAppMode() == 1)
			$ep = "https://api.sandbox.ebay.com/ws/api.dll";
		else
			$ep = 'https://api.ebay.com/ws/api.dll';
		
		// place all data into theirs header
		$reqHeaders[] = 'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->getVersion();
		$reqHeaders[] = 'X-EBAY-API-DEV-NAME: ' . $this->_session->getDevId();
		$reqHeaders[] = 'X-EBAY-API-APP-NAME: ' . $this->_session->getAppId();
		$reqHeaders[] = 'X-EBAY-API-CERT-NAME: ' . $this->_session->getCertId();
		$reqHeaders[] = 'X-EBAY-API-CALL-NAME: ' . $method;
		$reqHeaders[] = 'X-EBAY-API-SITEID: ' . $this->_session->getSiteId();
		
		$multiPartData = null;
		if ($method == 'UploadSiteHostedPictures')
		{
			// assuming to have the picture-binary data 
			// in $request->PictureData
			$multiPartData = $request->PictureData;
			$request->PictureData = null;
		}
		
		$body = $this->encodeMessageXmlStyle( $method, $request );
		
		$message = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$message .= $body;
		
		$this->_ep = $ep;
		
		$responseMsg = $this->sendMessageXmlStyle( $message, $reqHeaders, $multiPartData );
		
		if ( $responseMsg )
		{
			$this->_startTp('Decoding SOAP Message');
			$ret = & $this->decodeMessage( $method, $responseMsg, $parseMode );
			$this->_stopTp('Decoding SOAP Message');
		}
		else
		{
			$ret = & $this->_currentResult;
		}
		
		return $ret;
	}
	
	// sendMessage in XmlStyle,
	// the only difference is the extra headers we use here
	function sendMessageXmlStyle( $message, $extraXmlHeaders, $multiPartImageData = null )
	{
		$this->_currentResult = null;
		
		$this->log( $this->_ep, 'RequestUrl' );
		$this->logXml( $message, 'Request' );
		
		$timeout = $this->_transportOptions['HTTP_TIMEOUT'];
		if (!$timeout || $timeout <= 0)
			$timeout = 300;
		
		$ch = curl_init();
		
		if ($multiPartImageData !== null)
		{
			$boundary = "MIME_boundary";
			
			$CRLF = "\r\n";
			
			$mp_message .= "--" . $boundary . $CRLF;
			$mp_message .= 'Content-Disposition: form-data; name="XML Payload"' . $CRLF;
			$mp_message .= 'Content-Type: text/xml;charset=utf-8' . $CRLF . $CRLF;
			$mp_message .= $message;
			$mp_message .= $CRLF;
			
			$mp_message .= "--" . $boundary . $CRLF;
			$mp_message .= 'Content-Disposition: form-data; name="dumy"; filename="dummy"' . $CRLF;
			$mp_message .= "Content-Transfer-Encoding: binary" . $CRLF;
			$mp_message .= "Content-Type: application/octet-stream" . $CRLF . $CRLF;
			$mp_message .= $multiPartImageData;
			
			$mp_message .= $CRLF;
			$mp_message .= "--" . $boundary . "--" . $CRLF;
			
			$message = $mp_message;
			
			$reqHeaders[] = 'Content-Type: multipart/form-data; boundary=' . $boundary;
			$reqHeaders[] = 'Content-Length: ' . strlen($message);
		}
		else
		{
			$reqHeaders[] = 'Content-Type: text/xml;charset=utf-8';
		}
		
		
		if ($this->_transportOptions['HTTP_COMPRESS'])
		{
			$reqHeaders[] = 'Accept-Encoding: gzip, deflate';
			curl_setopt( $ch, CURLOPT_ENCODING, "gzip");
			curl_setopt( $ch, CURLOPT_ENCODING, "deflate");
		}
		
		if (is_array($extraXmlHeaders))
			$reqHeaders = array_merge($reqHeaders, $extraXmlHeaders);
		
		curl_setopt( $ch, CURLOPT_URL, $this->_ep );
		
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
		
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $reqHeaders );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'ebatns;xmlstyle;1.0' );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $message );
		
		curl_setopt( $ch, CURLOPT_FAILONERROR, 0 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_HTTP_VERSION, 1 );
		
		// added support for multi-threaded clients
		if (isset($this->_transportOptions['HTTP_CURL_MULTITHREADED']))
		{
			curl_setopt( $ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0 );
		}
		
		$responseRaw = curl_exec( $ch );
		
		if ( !$responseRaw )
		{
			$this->_currentResult = new EbatNs_ResponseError();
			$this->_currentResult->raise( 'curl_error ' . curl_errno( $ch ) . ' ' . curl_error( $ch ), 80000 + 1, EBAT_SEVERITY_ERROR );
			curl_close( $ch );
			
			return null;
		} 
		else
		{
			curl_close( $ch );
			
			$responseRaw = str_replace
			(
				array
				(
					"HTTP/1.1 100 Continue\r\n\r\nHTTP/1.1 200 OK\r\n",
					"HTTP/1.1 100 Continue\n\nHTTP/1.1 200 OK\n"
				),
				array
				(
					"HTTP/1.1 200 OK\r\n",
					"HTTP/1.1 200 OK\n"
				),
				$responseRaw
			);

			$responseBody = null;
			if ( preg_match( "/^(.*?)\r?\n\r?\n(.*)/s", $responseRaw, $match ) )
			{
				$responseBody = $match[2];
				$headerLines = split( "\r?\n", $match[1] );
				foreach ( $headerLines as $line )
				{
					if ( strpos( $line, ':' ) === false )
					{
						$responseHeaders[0] = $line;
						continue;
					} 
					list( $key, $value ) = split( ':', $line );
					$responseHeaders[strtolower( $key )] = trim( $value );
				} 
			} 
			
			if ($responseBody)
				$this->logXml( $responseBody, 'Response' );
			else
				$this->logXml( $responseRaw, 'ResponseRaw' );
		} 
		
		return $responseBody;
	} 
	
	function encodeMessageXmlStyle( $method, $request )
	{
		return $request->serialize( $method . 'Request', $request, array('xmlns' => 'urn:ebay:apis:eBLBaseComponents'), true, null, $this->_dataConverter );
	}
} 
?>
