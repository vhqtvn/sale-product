<?php

require_once 'AmountType.php';
require_once 'CategoryType.php';

class EbatNs_Convert
{
	function EbatNs_Convert()
	{
	}
	
	function ToAmount($amountValue, $currency)
	{
		$a = new AmountType();
		$a->setTypeValue($amountValue);
		$a->setTypeAttribute('currencyID', $currency);
		
		return $a;
	}
	
	function ToCategory($categoryId)
	{
		$category = new CategoryType();
		$category->CategoryID = $categoryId;
		return $category;
	}
}

?>