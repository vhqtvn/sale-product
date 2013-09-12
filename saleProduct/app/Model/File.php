<?php
class File extends AppModel {
	var $useTable = "sc_product_flow" ;
	
	public function loadImage($params){
		return $this->exeSqlWithFormat("select * from sc_utils_image where entity_id =
				 '{@#entityId#}' and entity_type='{@#entityType#}'", $params) ;
	}
}