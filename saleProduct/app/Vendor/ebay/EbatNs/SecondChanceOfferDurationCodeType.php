<?php
// autogenerated file 30.08.2007 09:37
// $Id$
// $Log$
//
require_once 'EbatNs_FacetType.php';

class SecondChanceOfferDurationCodeType extends EbatNs_FacetType
{
	// start props
	// @var string $Days_1
	var $Days_1 = 'Days_1';
	// @var string $Days_3
	var $Days_3 = 'Days_3';
	// @var string $Days_5
	var $Days_5 = 'Days_5';
	// @var string $Days_7
	var $Days_7 = 'Days_7';
	// @var string $CustomCode
	var $CustomCode = 'CustomCode';
	// end props

/**
 *

 * @return 
 */
	function SecondChanceOfferDurationCodeType()
	{
		$this->EbatNs_FacetType('SecondChanceOfferDurationCodeType', 'urn:ebay:apis:eBLBaseComponents');

	}
}

$Facet_SecondChanceOfferDurationCodeType = new SecondChanceOfferDurationCodeType();

?>