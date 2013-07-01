<?php 
// $Id: EbatNs_RequestHeaderType.php,v 1.1 2007/05/31 11:38:00 michael Exp $
/* $Log: EbatNs_RequestHeaderType.php,v $
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
	require_once 'EbatNs_ComplexType.php';

	class EbatNs_RequestHeaderType extends EbatNs_ComplexType
	{
		var $RequesterCredentials;
		
		function EbatNs_RequestHeaderType()
		{
			$this->EbatNs_ComplexType('EbatNs_RequestHeaderType', 'urn:ebay:apis:eBLBaseComponents');
			$this->_elements = array_merge($this->_elements,
				array(
					'RequesterCredentials' =>
					array(
						'required' => true,
						'type' => 'EbatNs_RequesterCredentialType',
						'nsURI' => 'http://www.w3.org/2001/XMLSchema'
					)
				));	
		}
	}
?>