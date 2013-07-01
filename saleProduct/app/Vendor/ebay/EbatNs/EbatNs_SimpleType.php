<?php
// $Id: EbatNs_SimpleType.php,v 1.1 2007/05/31 11:38:00 michael Exp $
/* $Log: EbatNs_SimpleType.php,v $
/* Revision 1.1  2007/05/31 11:38:00  michael
/* - initial checkin
/* - version < 513
/*
 * 
 * 2     3.02.06 10:44 Mcoslar
 * 
 * 1     30.01.06 12:11 Charnisch
 */
class EbatNs_SimpleType
{ 
	// type-name
	var $_typeName; 
	// namespace (prefix)
	var $_ns; 
	// namespace (fullname)
	var $_nsURI; 
	// array or not
	var $_isArrayType = false; 
	// associative array of attribute-names
	var $_attributes = array(); 
	// values of attributes
	var $attributeValues = null; 
	// a plain value (in case of a simple-type)
	var $value = null;

	function EbatNs_SimpleType( $typeName = 'string', $nsURI = 'http://www.w3.org/2001/XMLSchema' )
	{
		$this->_typeName = $typeName;
		$this->_nsURI = $nsURI;
	} 

	function _getAttributeString( $attributeValues )
	{
		$ret = '';
		if ( $attributeValues )
			foreach ( array_keys( $attributeValues ) as $key )
			$ret .= $key . '="' . $attributeValues[$key] . '" ';
		if ( !$ret )
			return '';
		else
			return ' ' . $ret;
	} 

	/**
	 * Set the value of an attribute on this object.
	 * as we got a name-clash with the attribute-class we choose this name
	 */
	function setTypeAttribute( $key, $value )
	{
		$this->attributeValues[$key] = $value;
	} 

	/**
	 * Get the value of an attribute on this object.
	 * as we got a name-clash with the attribute-class we choose this name
	 */
	function getTypeAttribute( $key )
	{
		return isset( $this->attributeValues[$key] ) ?
		$this->attributeValues[$key] :
		null;
	} 

	// set the value, as there is a name clash with the attribute-value class
	// we choose this name !
	function setTypeValue( $value )
	{
		$this->value = $value;
	} 

	// get the value, as there is a name clash with the attribute-value class
	// we choose this name !
	function getTypeValue()
	{
		return $this->value;
	} 
	
	// will serialize the given value
	// and return XML-data.
	// give obmitNull = true to obmit a value if the value if not set (null or 0 or '')
	function serialize( $elementName, $value, $attributeValues, $obmitNull, $typeName, &$dataConverter )
	{
		if ( $value && $obmitNull )
		{
			if ( $value )
			{
				$ret = '<' . $elementName . $this->_getAttributeString( $attributeValues ) . '>';
				if ( $dataConverter )
					$value = $dataConverter->encodeData( $value, $typeName, $elementName );
				$ret .= $value;

				$ret .= '</' . $elementName . '>';

				return $ret;
			} 
			else
				return '<' . $elementName . '/>';
		} 
		else
		{
			return '';
		} 
	} 
} 

?>