<?php
// $Id: EbatNs_FacetType.php,v 1.1 2007/05/31 11:38:00 michael Exp $
/* $Log: EbatNs_FacetType.php,v $
/* Revision 1.1  2007/05/31 11:38:00  michael
/* - initial checkin
/* - version < 513
/*
 * 
 * 2     3.02.06 10:44 Mcoslar
 * 
 * 1     30.01.06 12:11 Charnisch
 */
	require_once 'EbatNs_SimpleType.php';
	
	class EbatNs_FacetType extends EbatNs_SimpleType
	{
		function EbatNs_FacetType($name, $nsURI)
		{
			$this->EbatNs_SimpleType($name, $nsURI);
		}
	}
?>