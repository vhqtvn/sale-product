<?php
// $Id: EbatNs_DataConverter.php,v 1.1 2007/05/31 11:38:00 michael Exp $
/* $Log: EbatNs_DataConverter.php,v $
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
	class EbatNs_DataConverter
	{
		var $_options = array();
		function EbatNs_DataConverter()
		{
		}
		
		function decodeData($data, $type = 'string')
		{
			switch ($type)
			{
				case 'boolean':
					if ($data == 'true')
						return true;
					else
						return null;
			}
			return $data;
		}
		
		function encodeData($data, $type = 'string', $elementName = null)
		{
			switch ($type)
			{
				case 'string':
					if ($elementName == 'Description')
						return "<![CDATA[" . $data . "]]>";
			}
			return $data;
		}
		
		function encryptData($data, $type = null)
		{
			return $data;
		}
		
		function decryptData($data, $type = null)
		{
			return $data;
		}
	}
	
	class EbatNs_DataConverterUtf8 extends EbatNs_DataConverter
	{
		function EbatNs_DataConverterUtf8()
		{
			$this->EbatNs_DataConverter();
		}
	}
	
	class EbatNs_DataConverterIso extends EbatNs_DataConverter
	{
		function EbatNs_DataConverterIso()
		{
			$this->EbatNs_DataConverter();
		}

		function decodeData($data, $type = 'string')
		{
			switch ($type)
			{
				case 'string':
					return utf8_decode($data);
				case 'dateTime':
					{
						$dPieces = split('T', $data);
						$tPieces = split("\.", $dPieces[1]);
						$data = $dPieces[0] . ' ' . $tPieces[0];
						
						// return date('Y-m-d H:i:s', strtotime($data) + date('Z'));
						return $data;
					}
				case 'boolean':
					if ($data == 'true')
						return true;
					else
						return null;
				default:
					return $data;	
			}
		}
		
		function encodeData($data, $type = 'string', $elementName = null)
		{
			switch ($type)
			{
				case 'string':
					if ($elementName == 'Description')
						return "<![CDATA[" . utf8_encode($data) . "]]>";
					else
						return utf8_encode($data);
				case 'dateTime':
				{
					$time = strtotime($data);
					return date("Y-m-d\\TH:i:s.000\\Z", $time);
				}
			}
			
			return $data;
		}
	}
?>