<?php
// $Id: EbatNs_NotificationClient.php,v 1.1 2007/05/31 11:38:00 michael Exp $
/* $Log: EbatNs_NotificationClient.php,v $
/* Revision 1.1  2007/05/31 11:38:00  michael
/* - initial checkin
/* - version < 513
/*
 * 
 * 3     3.02.06 10:44 Mcoslar
 * 
 * 2     30.01.06 16:44 Mcoslar
 * �nderungen eingef�gt
 */
	require_once 'EbatNs_ResponseParser.php';
	require_once 'EbatNs_DataConverter.php';

	class EbatNs_NotificationClient
	{
		var $_parser = null; 
		// callback-methods/functions for data-handling
		var $_hasCallbacks = false;
		var $_callbacks = null; 
		// EbatNs_DataConverter object
		var $_dataConverter = null;

		var $_logger = null;

		function EbatNs_NotificationClient($converter = 'EbatNs_DataConverterIso')
		{
			if ($converter)
				$this->_dataConverter = new $converter();
			$this->_parser = null;
		}
		
		// should return true if the data should NOT be included to the
		// response-object !
		function _handleDataType( $typeName, &$value )
		{
			if ( $this->_hasCallbacks )
			{
				$callback = $this->_callbacks[strtolower( $typeName )];
				if ( $callback )
				{
					if ( is_object( $callback['object'] ) )
					{
						return call_user_method( $callback['method'], $callback['object'], $typeName, & $value );
					} 
					else
					{
						return call_user_func( $callback['method'], $typeName, & $value );
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

		function getResponse($msg)
		{
			$this->_parser = &new EbatNs_ResponseParser( &$this, 'urn:ebay:apis:eBLBaseComponents' );
			$this->_parser->setMode(EBATNS_PARSEMODE_NOTIFICATION);
			
			return $this->_parser->decode( $method . 'Response', $msg, true );
		}
	}

?>