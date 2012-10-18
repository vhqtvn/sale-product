<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
		function _clearDBCache() {
		  	
		}
	
		function getScriptRecords($query=null) {
			$sql = 'SELECT * FROM sc_election_rule' ;
			$array = $this->query($sql);
			return $array ;
		}
		
		function formatSqlParams($param = null){
			if( $param == null || empty($param)  || $param == "") return $param ;
			
			return str_replace("'","‘",$param) ;
			
		}
		
		function getAgent($index){
			$agents = array() ;
			$agents[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11" ;
			$agents[] = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)" ;
			return $agents[ $index % 2 ]  ;		
		}
		
		public function creatdir($path)
	{
		if(!is_dir($path))
		{
			if($this->creatdir(dirname($path)))
			{
				mkdir($path,0777);
				return true;
			}
		}
		else
		{
			return true;
		}
	}
}
