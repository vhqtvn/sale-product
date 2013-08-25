<?php
class Keyword extends AppModel {
	var $useTable = "sc_keyword_plan" ;
	 
	public function saveNiceDev($params){
		$this->exeSql("sql_keyword_update", $params) ;
		
		if( isset( $params['memo'] ) ){
			$this->exeSql("INSERT INTO sc_keyword_track 
					(
					keyword_id, 
					description,
					creator, 
					create_date
					)
					VALUES
					(
					'{@#keyword_id#}', 
					'{@#memo#}', 
					'{@#loginId#}', 
					NOW()
					)", $params) ;
		}
	}
	
	public function savePlan( $params ){
		
		if( isset( $params['id'] ) && !empty($params['id'] ) ){//update
			$this->exeSql("sql_keyword_plan_update", $params) ;
		}else{//insert
			$params['guid'] = $this->create_guid() ;
			$this->exeSql("sql_keyword_plan_insert", $params) ;
		}
	}
	
	public function saveTask( $params ){
	
		if( isset( $params['id'] ) && !empty($params['id'] ) ){//update
			$this->exeSql("sql_keyword_task_update", $params) ;
		}else{//insert
			$params['guid'] = $this->create_guid() ;
			$this->exeSql("sql_keyword_task_insert", $params) ;
		}
	}
	
	/**
	 * 通过主关键字获取对应的子关键字
	 * 
	 * @param unknown_type $params
	 */
	public function fetchChildKeyWords($params){
		$keywordId = null ;
		if( isset($params['keywordId']) ){
			$keywordId = $params['keywordId'] ;
		}
		
		$mainKeyword = $params['mainKeyword'] ;
		$mainKeyword = urlencode($mainKeyword) ;
		$parseMatchUrl = "http://us.fullsearch-api.semrush.com/?action=report&type=phrase_fullsearch&phrase=$mainKeyword&key=240ada68082b9ad767ef984a0cfde07c&display_limit=10&export=api&export_columns=Ph,Nq,Cp,Co,Nr,Td" ;
		$relationUrl = "http://us.api.semrush.com/?action=report&type=phrase_related&key=240ada68082b9ad767ef984a0cfde07c&display_limit=10&export=api&export_columns=Ph,Nq,Cp,Co,Nr,Td&phrase=$mainKeyword" ;
		$orgUrl = "http://us.api.semrush.com/?action=report&type=phrase_organic&key=240ada68082b9ad767ef984a0cfde07c&display_limit=10&export=api&export_columns=Dn,Ur&phrase=$mainKeyword" ;
		
		//保存主关键字
		$mainGuid = null ;
		if( empty($keywordId) ){
			$mainGuid = $this->create_guid() ;
			
			$record = array() ;
			$record['guid'] = $mainGuid ;
			$record['taskId'] = $params['taskId'] ;
			$record['loginId'] = $params['loginId'] ;
			$record['is_main_keywork'] = '1' ;
			$record['keyword'] = $mainKeyword ;
			$this->exeSql("sql_keyword_insert", $record) ;
		}else{
			$mainGuid = $keywordId ;
		}
		
		
		//保存词组匹配
		$content1 = file_get_contents($parseMatchUrl) ;
		
		$content1 = split("\n", $content1) ;
		$index =0 ;
		foreach($content1 as $c){
			if($index > 0){
				$array = split(";",$c ) ;
				$record = array() ;
				$record['guid'] = $this->create_guid() ;
				$record['taskId'] = $params['taskId'] ;
				$record['loginId'] = $params['loginId'] ;
				$record['is_main_keywork'] = '0' ;
				$record['keyword_type'] ="Pharse";
				
				$record['keyword'] = $array[0] ;
				$record['search_volume'] = $array[1] ;
				$record['cpc'] = $array[2] ;
				$record['competition'] = $array[3] ;
				$record['result_num'] = $array[4] ;
				$record['trends'] = $array[5] ;
				$record['parent_id'] = $mainGuid ;
				$this->exeSql("sql_keyword_insert", $record) ;
			}
			$index++;
		} 
		
		//保存关联关键字
		$content2 = file_get_contents($relationUrl) ;
		$content2 = split("\n", $content2) ;
		$index =0 ;
		foreach($content2 as $c){
			if($index > 0){
				$array = split(";",$c ) ;
				$record = array() ;
				$record['guid'] = $this->create_guid() ;
				$record['taskId'] = $params['taskId'] ;
				$record['loginId'] = $params['loginId'] ;
				$record['is_main_keywork'] = '0' ;
				$record['keyword_type'] ="Related";
				
				$record['keyword'] = $array[0] ;
				$record['search_volume'] = $array[1] ;
				$record['cpc'] = $array[2] ;
				$record['competition'] = $array[3] ;
				$record['result_num'] = $array[4] ;
				$record['trends'] = $array[5] ;
				$record['parent_id'] = $mainGuid ;
				$this->exeSql("sql_keyword_insert", $record) ;
			}
			$index++;
		}
		
		//保存关联网站，关联到主关键字
		$content3 = file_get_contents($orgUrl) ;
		$content3 = split("\n", $content3) ;
		$index =0 ;
		foreach($content3 as $c){
			if($index > 0){
				$array = split(";",$c ) ;
				$record = array() ;
				$record['guid'] = $this->create_guid() ;
				
				$record['domain1'] = $array[0] ;
				$record['url'] = $array[1] ;
				$record['mainGuid'] = $mainGuid ;
				$this->exeSql("sql_keyword_website_insert", $record) ;
			}
			$index++;
		}
	}
	
	public function loadMainKeywords( $params ){
		$sql = "select t.* ,
						(select count(1) from sc_keyword sk where sk.parent_id = t.keyword_id ) as c
					from sc_keyword t 
					where t.task_id='{@#taskId#}' and t.is_main_keywork = 1 order by t.keyword_no" ;
		
		return $this->exeSqlWithFormat( $sql , array('taskId'=>$params['taskId'])) ;
	}
	
	public function loadChildKeywords( $params ){
		$sql = "select t.* ,
						(select count(1) from sc_keyword sk where sk.parent_id = t.keyword_id ) as c
					from sc_keyword t
					where t.parent_id='{@#parentId#}'  order by t.keyword_no" ;
	
		return $this->exeSqlWithFormat( $sql , array('parentId'=>$params['parentId'])) ;
	}
	
	public function setToNiche($params){
		$sql = "update sc_keyword set is_niche = '1' where keyword_id = '".$params['keywordId']."'" ;
		$this->exeSql($sql, array()) ;		
	}
}