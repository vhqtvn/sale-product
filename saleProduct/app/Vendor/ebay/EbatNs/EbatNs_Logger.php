<?php
// $Id: EbatNs_Logger.php,v 1.1 2007/05/31 11:38:00 michael Exp $
/* $Log: EbatNs_Logger.php,v $
/* Revision 1.1  2007/05/31 11:38:00  michael
/* - initial checkin
/* - version < 513
/*
 * 
 * 5     12.02.07 10:35 Charnisch
 * 
 * 4     12.02.07 10:15 Charnisch
 * - added EbatNs_LoggerWire logger, which logs full data to strings
 * 
 * 3     3.02.06 10:44 Mcoslar
 * 
 * 2     30.01.06 16:44 Mcoslar
 * �nderungen eingef�gt
 */
class EbatNs_Logger
{
	// debugging options
	var $_debugXmlBeautify = true;
	var $_debugLogDestination = 'stdout';
	var $_debugSecureLogging = true;
	var $_debugHtml = true;
	
	function EbatNs_Logger($beautfyXml = false, $destination = 'stdout', $asHtml = true, $SecureLogging = true)
	{
		$this->_debugLogDestination = $destination;
		$this->_debugXmlBeautify = $beautfyXml;
		$this->_debugHtml = $asHtml;
		$this->_debugSecureLogging = $SecureLogging;
	}
	
	function log($msg, $subject = null)
	{
		if ($this->_debugLogDestination)
		{
			if ($this->_debugLogDestination == 'stdout')
			{
				if ($this->_debugHtml)
				{
					print_r("<pre>");
					if ($subject)
						print_r("$subject :<br>");				
					print_r(htmlentities($msg) . "\r\n");
					print_r("</pre>");
				}
				else
				{
					if ($subject)
						print_r($subject . ' : ' . "\r\n"); 
					print_r($msg . "\r\n");
				}
			}
			else
			{
				ob_start();
				echo date('r') . "\t" . $subject . "\t";
				print_r($msg);
				echo "\r\n";
				error_log(ob_get_clean(), 3, $this->_debugLogDestination);
			}			
		}
	}
	
	function logXml($xmlMsg, $subject = null)
	{
		if ($this->_debugSecureLogging)
		{
			$xmlMsg = preg_replace("/<eBayAuthToken>.*<\/eBayAuthToken>/", "<eBayAuthToken>...</eBayAuthToken>", $xmlMsg);
			$xmlMsg = preg_replace("/<AuthCert>.*<\/AuthCert>/", "<AuthCert>...</AuthCert>", $xmlMsg);
		}
		
		if ($this->_debugXmlBeautify)
		{
			if (is_object($xmlMsg))
				$this->log($xmlMsg);
			else
			{
				require_once 'XML/Beautifier.php';
				$xmlb = new XML_Beautifier();
				$this->log($xmlb->formatString($xmlMsg), $subject);
			}
			
			return;
		}
		
		$this->log($xmlMsg, $subject);
	}
}

class EbatNs_LoggerWire extends EbatNs_Logger
{
	var $_Request = '';
	var $_Response = '';
	function EbatNs_LoggerWire()
	{
		$this->EbatNs_Logger(false, '', false, false);
	}
	
	function log($msg, $subject = null)
	{
		if ($subject == 'Request')
			$this->_Request = $msg;
		else
			if ($subject == 'Response' || $subject == 'ResponseRaw')
				$this->_Response = $msg;
	}
	
	function getFullWireLog($beautifyRequest = true)
	{
		if ($beautifyRequest === true)
		{
			require_once 'XML/Beautifier.php';
			$xmlb = new XML_Beautifier();
			$this->_Request = $xmlb->formatString($this->_Request);
		}
		
		return $this->_RequestUrl .
			($asHtml ? "<br>" : "\n") .
			($asHtml ? htmlentities($this->_Request) :  $this->_Request) .
			($asHtml ? "<br>" : "\n") .
			($asHtml ? htmlentities($this->_Response) : $this->_Response);
	}
}
?>