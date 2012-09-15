<?php

/**
Seller account identifiers for MDirect
Merchant ID:	AGTS6F2X3WRVD
Marketplace ID:	ATVPDKIKX0DER
Developer account identifier and credentials for developer account number 8137-2641-4810*
AWS Access Key ID:	AKIAI2TUVMT45FYLVJQQ
Secret Key:	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
 */

/**
Seller account identifiers for Ameripower  
Merchant ID: A1N36FBTGRBD37 
Marketplace ID: ATVPDKIKX0DER 
Developer account identifier and credentials for developer account number 9474-1350-9077*  
AWS Access Key ID: AKIAI54EOFL7VGEJBJ7Q 
Secret Key: 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 
 */
  
	define ('DATE_FORMAT', 'Y-m-d\TH:i:s\Z');


   /************************************************************************ 
    * OPTIONAL ON SOME INSTALLATIONS
    *
    * Set include path to root of library, relative to Samples directory.
    * Only needed when running library from local directory.
    * If library is installed in PHP include path, this is not needed
    ***********************************************************************/   
    set_include_path(get_include_path() . PATH_SEPARATOR . '../../.');    
    
   /************************************************************************ 
    * OPTIONAL ON SOME INSTALLATIONS  
    * 
    * Autoload function is reponsible for loading classes of the library on demand
    * 
    * NOTE: Only one __autoload function is allowed by PHP per each PHP installation,
    * and this function may need to be replaced with individual require_once statements
    * in case where other framework that define an __autoload already loaded.
    * 
    * However, since this library follow common naming convention for PHP classes it
    * may be possible to simply re-use an autoload mechanism defined by other frameworks
    * (provided library is installed in the PHP include path), and so classes may just 
    * be loaded even when this function is removed
    ***********************************************************************/   
     function __autoload($className){
        $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $includePaths = explode(PATH_SEPARATOR, get_include_path());
        foreach($includePaths as $includePath){
            if(file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)){
                require_once $filePath;
                return;
            }
        }
    }