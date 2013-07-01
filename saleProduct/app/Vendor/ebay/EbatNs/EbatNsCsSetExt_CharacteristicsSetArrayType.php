<?php
// autogenerated file 23.02.2007 11:57
// $Id$
// $Log$
//
require_once 'EbatNsCsSetExt_CharacteristicsSetType.php';
require_once 'EbatNs_ComplexType.php';

class EbatNsCsSetExt_CharacteristicsSetArrayType extends EbatNs_ComplexType
{
	// start props
	// @var EbatNsCsSetExt_CharacteristicsSetType $CharacteristicsSet
	var $CharacteristicsSet;
	// end props

/**
 *

 * @return EbatNsCsSetExt_CharacteristicsSetType
 * @param  $index 
 */
	function getCharacteristicsSet($index = null)
	{
		if ($index) {
		return $this->CharacteristicsSet[$index];
	} else {
		return $this->CharacteristicsSet;
	}

	}
/**
 *

 * @return void
 * @param  $value 
 * @param  $index 
 */
	function setCharacteristicsSet($value, $index = null)
	{
		if ($index) {
	$this->CharacteristicsSet[$index] = $value;
	} else {
	$this->CharacteristicsSet = $value;
	}

	}
/**
 *

 * @return 
 */
	function EbatNsCsSetExt_CharacteristicsSetArrayType()
	{
		$this->EbatNs_ComplexType('EbatNsCsSetExt_CharacteristicsSetArrayType', 'http://www.intradesys.com/Schemas/ebay/AttributeData_Extension.xsd');
		$this->_elements = array_merge($this->_elements,
			array(
				'CharacteristicsSet' =>
				array(
					'required' => false,
					'type' => 'EbatNsCsSetExt_CharacteristicsSetType',
					'nsURI' => 'http://www.intradesys.com/Schemas/ebay/AttributeData_Extension.xsd',
					'array' => true,
					'cardinality' => '0..*'
				)
			));

	}
}
?>
