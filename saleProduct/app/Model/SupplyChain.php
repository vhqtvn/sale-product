<?php
class SupplyChain extends AppModel {
	var $useTable = "sc_election_rule" ;
	
	public function listTrack($track){
		$sql = "select * from  sc_supplychain_track where entity_type = '{@#entityType#}' and entity_id = '{@#entityId#}' " ;
		$trackList = $this->exeSqlWithFormat($sql, $track) ;
		return $trackList ;
	}
	
	public  function saveTrack($track){
		$sql = "INSERT INTO sc_supplychain_track 
					(
					MEMO, 
					CREATOR, 
					CREATE_DATE, 
					ENTITY_TYPE, 
					ENTITY_ID
					)
					VALUES
					(
					'{@#memo#}', 
					'{@#loginId#}', 
					NOW(), 
					'{@#entityType#}', 
					'{@#entityId#}'
					)" ;
		$this->exeSql($sql, $track) ;
	}
}
?>