<?php 
// $Id: EbatNs_RequesterCredentialType.php,v 1.1 2007/05/31 11:38:00 michael Exp $
/* $Log: EbatNs_RequesterCredentialType.php,v $
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

	class EbatNs_RequesterCredentialType extends EbatNs_ComplexType
	{
		// @var string $eBayAuthToken
		var $eBayAuthToken;
		// @var CredentialType $Credentials
		var $Credentials;
		var $_attributeValues;
		
		function EbatNs_RequesterCredentialType()
		{
			$this->_attributeValues['soap:actor'] = '';
			$this->_attributeValues['soap:mustUnderstand'] = '0';
			$this->_attributeValues['xmlns'] = 'urn:ebay:apis:eBLBaseComponents';		
			
			$this->EbatNs_ComplexType('EbatNs_RequesterCredentialType', 'urn:ebay:apis:eBLBaseComponents');
			$this->_elements = array_merge($this->_elements,
				array(
					
					'eBayAuthToken' =>
					array(
						'required' => false,
						'type' => 'string',
						'nsURI' => 'http://www.w3.org/2001/XMLSchema'
					),
					'Credentials' =>
					array(
						'required' => false,
						'type' => 'CredentialType',
						'nsURI' => 'http://www.w3.org/2001/XMLSchema'
					)
				));
		}
	}
?>