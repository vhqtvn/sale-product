<?php

class Tag extends AppModel {
	var $useTable = "sc_election_rule" ;
	
	function addTag($params){
		$id = $this->create_guid() ;
		$sql = "
					INSERT INTO sc_tag_entity 
						(ID, 
						TAG_ID, 
						ENTITY_TYPE, 
						ENTITY_ID, 
						CREATOR, 
						CREATE_DATE, 
						MEMO
						)
						VALUES
						('$id', 
						'{@#tagId#}', 
						'{@#entityType#}', 
						'{@#entityId#}', 
						'{@#loginId#}', 
						NOW(), 
						'{@#memo#}'
						)" ;
		$this->exeSql($sql, $params) ;
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
		return $this->exeSqlWithFormat("select st.*,
				(select count(*) from sc_tag_entity ste where ste.entity_type = stt.code and ste.tag_id = st.id ) as COUNT
				from sc_tag st,sc_tag_type stt
				where st.type_id = stt.id and  stt.code = '{@#entityType#}'", $params) ;
	}
	
	function listByEntity($params){
		return $this->exeSqlWithFormat("select st.*,
				(select memo from sc_tag_entity ste where ste.entity_type = stt.code and ste.tag_id = st.id
                    and ste.entity_id =  '{@#entityId#}' 
				) as MEMO,
				(select id from sc_tag_entity ste where ste.entity_type = stt.code and ste.tag_id = st.id
                    and ste.entity_id =  '{@#entityId#}' 
				) as TAG_ENTITY_ID,
				(select count(*) from sc_tag_entity ste where ste.entity_type = stt.code and ste.tag_id = st.id
                    and ste.entity_id =  '{@#entityId#}' 
				) as COUNT
				from sc_tag st,sc_tag_type stt
				where st.type_id = stt.id and  stt.code = '{@#entityType#}'", $params) ;

	
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

