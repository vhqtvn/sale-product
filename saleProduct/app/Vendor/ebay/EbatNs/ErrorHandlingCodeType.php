<?php
// autogenerated file 30.08.2007 09:37
// $Id$
// $Log$
//
require_once 'EbatNs_FacetType.php';

class ErrorHandlingCodeType extends EbatNs_FacetType
{
	// start props
	// @var string $Legacy
	var $Legacy = 'Legacy';
	// @var string $BestEffort
	var $BestEffort = 'BestEffort';
	// @var string $AllOrNothing
	var $AllOrNothing = 'AllOrNothing';
	// @var string $FailOnError
	var $FailOnError = 'FailOnError';
	// end props

/**
 *

 * @return 
 */
	function ErrorHandlingCodeType()
	{
		$this->EbatNs_FacetType('ErrorHandlingCodeType', 'urn:ebay:apis:eBLBaseComponents');

	}
}

$Facet_ErrorHandlingCodeType = new ErrorHandlingCodeType();

?>
