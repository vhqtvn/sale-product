<?php
// autogenerated file 30.08.2007 09:37
// $Id$
// $Log$
//
require_once 'EbatNs_FacetType.php';

class EndReasonCodeType extends EbatNs_FacetType
{
	// start props
	// @var string $LostOrBroken
	var $LostOrBroken = 'LostOrBroken';
	// @var string $NotAvailable
	var $NotAvailable = 'NotAvailable';
	// @var string $Incorrect
	var $Incorrect = 'Incorrect';
	// @var string $OtherListingError
	var $OtherListingError = 'OtherListingError';
	// @var string $CustomCode
	var $CustomCode = 'CustomCode';
	// @var string $SellToHighBidder
	var $SellToHighBidder = 'SellToHighBidder';
	// end props

/**
 *

 * @return 
 */
	function EndReasonCodeType()
	{
		$this->EbatNs_FacetType('EndReasonCodeType', 'urn:ebay:apis:eBLBaseComponents');

	}
}

$Facet_EndReasonCodeType = new EndReasonCodeType();

?>
