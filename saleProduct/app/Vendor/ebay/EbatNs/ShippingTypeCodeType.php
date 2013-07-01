<?php
// autogenerated file 30.08.2007 09:37
// $Id$
// $Log$
//
require_once 'EbatNs_FacetType.php';

class ShippingTypeCodeType extends EbatNs_FacetType
{
	// start props
	// @var string $Flat
	var $Flat = 'Flat';
	// @var string $Calculated
	var $Calculated = 'Calculated';
	// @var string $Freight
	var $Freight = 'Freight';
	// @var string $Free
	var $Free = 'Free';
	// @var string $NotSpecified
	var $NotSpecified = 'NotSpecified';
	// @var string $FlatDomesticCalculatedInternational
	var $FlatDomesticCalculatedInternational = 'FlatDomesticCalculatedInternational';
	// @var string $CalculatedDomesticFlatInternational
	var $CalculatedDomesticFlatInternational = 'CalculatedDomesticFlatInternational';
	// @var string $CustomCode
	var $CustomCode = 'CustomCode';
	// end props

/**
 *

 * @return 
 */
	function ShippingTypeCodeType()
	{
		$this->EbatNs_FacetType('ShippingTypeCodeType', 'urn:ebay:apis:eBLBaseComponents');

	}
}

$Facet_ShippingTypeCodeType = new ShippingTypeCodeType();

?>
