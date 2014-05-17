<?php

/**
 * 执行任务列表
 */
class TestController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array("GatherData","InventoryNew");
	
	public  function purchaseInFix(){
		return ;
		debug(111111);
		$this->InventoryNew->purchaseInFix(  ) ;
	}
	
	public function initAsinCost(){
		$sql = "select  ASIN,ACCOUNT_ID,SKU from sc_amazon_account_product" ;
		$records = $this->InventoryNew->exeSqlWithFormat($sql,array()) ;
		foreach( $records as $record ){
			$ASIN = $record['ASIN'] ;
			//获取成本
			$sql = "SELECT * FROM sc_product_cost_details WHERE ( ASIN = '{@#asin#}'
								OR ( account_id = '{@#accountId#}' AND listing_sku = '{@#listingSku#}' )
					)   " ;
			$product = $this->InventoryNew->getObject($sql , array("asin"=>$ASIN,
					"accountId"=>$record['ACCOUNT_ID']
					,"listingSku"=>$record['SKU'] )
				 ) ;
			if( !empty( $product ) ){
				try{
					$product['ASIN'] = $ASIN ;
					$sql = "INSERT INTO  sc_product_cost_asin
									(ASIN,
									VARIABLE_CLOSING_FEE,
									COMMISSION_LOWLIMIT,
									COMMISSION_RATIO,
									FBA_COST
									)
									VALUES
									('{@#ASIN#}',
									'{@#VARIABLE_CLOSING_FEE#}',
									'{@#COMMISSION_LOWLIMIT#}',
									'{@#COMMISSION_RATIO#}',
									'{@#FBA_COST#}'
									)" ;
					$this->InventoryNew->exeSql($sql,$product) ;
				}catch(Exception $e){
					if( $product['VARIABLE_CLOSING_FEE'] == 0 ){
						$product['VARIABLE_CLOSING_FEE'] = "" ;
					}
					if( $product['COMMISSION_LOWLIMIT'] == 0 ){
						$product['COMMISSION_LOWLIMIT'] = "" ;
					}
					if( $product['COMMISSION_RATIO'] == 0 ){
						$product['COMMISSION_RATIO'] = "" ;
					}
					if( $product['FBA_COST'] == 0 ){
						$product['FBA_COST'] = "" ;
					}
					debug($product) ;
					
					$sql = "UPDATE  sc_product_cost_asin 
									SET
									ASIN = '{@#ASIN#}' 
									{@ ,VARIABLE_CLOSING_FEE = '#VARIABLE_CLOSING_FEE#'}
									{@ ,COMMISSION_LOWLIMIT = '#COMMISSION_LOWLIMIT#' } 
									{@ ,COMMISSION_RATIO = '#COMMISSION_RATIO#' }
									{@ ,FBA_COST = '#FBA_COST#'}
									WHERE
									ASIN = '{@#ASIN#}'" ;
					$this->InventoryNew->exeSql($sql,$product) ;
				}
					
			}
		}
	}

	/**
	 * tab也
	 */
	public function test($asin,$platformId){
		
		$gatherParams = array(
						"asin"=>$asin,
						"platformId"=>$platformId,
						"id"=>"",
						"index"=>"",
						"taskId"=>""
				) ;
		$this->GatherData->asinInfoPlatform( $gatherParams ) ;
	}
	
	
}