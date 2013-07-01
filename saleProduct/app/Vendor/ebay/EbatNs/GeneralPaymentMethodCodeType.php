<?php
// autogenerated file 30.08.2007 09:37
// $Id$
// $Log$
//
require_once 'EbatNs_FacetType.php';

class GeneralPaymentMethodCodeType extends EbatNs_FacetType
{
	// start props
	// @var string $Other
	var $Other = 'Other';
	// @var string $Echeck
	var $Echeck = 'Echeck';
	// @var string $ACH
	var $ACH = 'ACH';
	// @var string $Creditcard
	var $Creditcard = 'Creditcard';
	// @var string $PayPalBalance
	var $PayPalBalance = 'PayPalBalance';
	// @var string $CustomCode
	var $CustomCode = 'CustomCode';
	// end props

/**
 *

 * @return 
 */
	function GeneralPaymentMethodCodeType()
	{
		$this->EbatNs_FacetType('GeneralPaymentMethodCodeType', 'urn:ebay:apis:eBLBaseComponents');

	}
}

$Facet_GeneralPaymentMethodCodeType = new GeneralPaymentMethodCodeType();

?>
