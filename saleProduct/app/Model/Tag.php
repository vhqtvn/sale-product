<?php

class Tag extends AppModel {
	var $useTable = "sc_election_rule" ;
	
	function addTagDetails($params){
		$id = $this->create_guid() ;
		$params['id'] = $id ;
		
		$this->exeSql("sql_insert_tag_details", $params) ;
		
		$tagEntity = $this->getObject("select ste.*  FROM   sc_tag_entity ste
				WHERE  ste.id = '{@#tagEntityId#}'", $params) ;
		
		$params['tagId'] = $tagEntity['TAG_ID'] ;
		$params['entityType'] = $tagEntity['ENTITY_TYPE'] ;
		$params['entityId'] = $tagEntity['ENTITY_ID'] ;
		$params['creator'] = $tagEntity['CREATOR'] ;
		$params['createDate'] = $tagEntity['CREATE_DATE'] ;
		$params['action'] = "备注" ;
		
		$this->tagActionLog($params) ;
	}
	
	function addTag($params){
		$id = $this->create_guid() ;
		$params['id'] = $id ;
		$this->exeSql("sql_insert_tag", $params) ;
		$params['action'] = "添加" ;
		$params['creator'] = $params['loginId'] ;
		$params['createDate'] =date("Y-m-d H:i:s") ;
		$this->tagActionLog($params) ;
	}
	
	function deleteTag($params){
		$tagEntity = $this->getObject("select ste.*  FROM   sc_tag_entity ste  
				WHERE  ste.id = '{@#tagEntityId#}'", $params) ;
		$sql = " DELETE FROM  sc_tag_entity  WHERE ID = '{@#tagEntityId#}'" ;
		$this->exeSql($sql, $params) ;
		
		$params['tagId'] = $tagEntity['TAG_ID'] ;
		$params['entityType'] = $tagEntity['ENTITY_TYPE'] ;
		$params['entityId'] = $tagEntity['ENTITY_ID'] ;
		$params['creator'] = $tagEntity['CREATOR'] ;
		$params['createDate'] = $tagEntity['CREATE_DATE'] ;
		$params['action'] = "删除" ;
		$params['memo'] = $tagEntity['MEMO'] ;
		
		$this->tagActionLog($params) ;
	}
	
	function tagActionLog($params){
		$id = $this->create_guid() ;
		$sql = "INSERT INTO sc_tag_log 
							(ID, 
							TAG_ID, 
							ENTITY_TYPE, 
							ENTITY_ID, 
							CREATOR, 
							CREATE_DATE, 
							ACTION, 
							MEMO,
							LOGOR,
							LOG_DATE
							)
							VALUES
							('$id', 
							'{@#tagId#}', 
							'{@#entityType#}', 
							'{@#entityId#}', 
							'{@#creator#}', 
							'{@#createDate#}', 
							'{@#action#}', 
							'{@#memo#}',
							'{@#loginId#}',
							NOW()
							)" ;
		$this->exeSql($sql, $params) ;
	}
	
	function listByType($params){
		$ss = "stt.code " ;
		if( isset($params['subEntityType']) ){
			$ss = "=concat(stt.code,'".$params['subEntityType']."') " ; ;
		}else{
			$ss = " like concat(stt.code,'%')" ;
		}
		
		return $this->exeSqlWithFormat("select st.*,
				(select count(*) from sc_tag_entity ste where ste.entity_type  $ss   and ste.tag_id = st.id ) as COUNT
				from sc_tag st,sc_tag_type stt
				where st.type_id = stt.id and  stt.code = '{@#entityType#}'", $params) ;
	}
	
	function listByEntity($params){
		if(!isset($params['subEntityType'])){
			$params['subEntityType'] = "" ;
		}
		
		$entitys = $this->exeSqlWithFormat("sql_tag_listbyEntity", $params) ;
		$result = array() ;
		foreach( $entitys as $entity ){ //获取访问日志
			$tagEntityId = $entity['TAG_ENTITY_ID'] ;
			if( !empty($tagEntityId) ){
				$memos = $this->exeSqlWithFormat("sql_tag_listMemosbyEntity", array('tagEntityId'=>$tagEntityId)) ;
				$entity['MEMOS'] = $memos ;
			}
			$result[] = $entity ;
		}
		return $result ;
	}


	function saveTag($params  ) {
		if( empty($params['id']) ){
			$id = $this->create_guid() ;
			$sql = "INSERT INTO sc_tag
					(ID,
					TYPE_ID,
					NAME,
					DESCRIPTION
					)
					VALUES
					('$id',
					'{@#typeId#}',
					'{@#name#}',
					'{@#description#}'
					)" ;
			$this->exeSql($sql, $params) ;
		}else{
			$sql = "UPDATE sale_product1.sc_tag 
				SET
				NAME = '{@#name#}' , 
				DESCRIPTION = '{@#description#}' 
				
				WHERE
				ID = '{@#id#}' " ;
			$this->exeSql($sql, $params) ;
		}
	}
	
}

