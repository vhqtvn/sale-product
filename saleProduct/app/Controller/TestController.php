<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");
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
	
	
	public function formatDevProduct(){
		$sql = "SELECT * FROM sc_product_developer WHERE account_id = 4 AND listing_sku IS NOT NULL  AND listing_sku!=''" ;
		$records = $this->InventoryNew->exeSqlWithFormat($sql,array()) ;
		foreach( $records as $record ){
			$accountId = $record['ACCOUNT_ID'] ;
			$listingSku = $record['LISTING_SKU'] ;
			echo $accountId.'   '.$listingSku.'<br/>' ;
			
			//自动关联
			//1、自动关联listing
			$realId = $record['REAL_PRODUCT_ID'] ;
			$sql = "select * from sc_real_product where id='{@#realId#}'" ;
			$real = $this->InventoryNew->getObject($sql, array("realId"=>$realId)) ;
			$realSku = $real['REAL_SKU'] ;
			//判断关联是否存在
			$sql ="select * from sc_real_product_rel where real_id='{@#realId#}' and real_sku='{@#realSku#}' and account_id='{@#accountId#}' and sku='{@#sku#}'" ;
			$_params = array("realId"=>$realId,"reslSku"=>$realSku,"accountId"=>$accountId,"sku"=>$listingSku) ;
			$realRel =$this->InventoryNew->getObject($sql, $_params) ;
			if( empty($realRel) ){
				try{
					$Amazonaccount  = ClassRegistry::init("Amazonaccount") ;
					$Amazonaccount->checkProductValid($accountId,$listingSku)  ;
				}catch(Exception $e){}
				
				try{
				$sql = " INSERT INTO sc_real_product_rel
										(REAL_SKU,
										SKU,
										ACCOUNT_ID,
										REAL_ID
										)
										VALUES
										('{@#realSku#}',
										'{@#sku#}',
										'{@#accountId#}',
										'{@#realId#}'
										)";
				$this->InventoryNew->exeSql($sql, $_params) ;
				}catch(Exception $e){}
			}
		}
	}
	
	public function initAsinCost(){
		return ;
		$sql = "select  ASIN,ACCOUNT_ID,SKU from sc_amazon_account_product" ;
		$records = $this->InventoryNew->exeSqlWithFormat($sql,array()) ;
		foreach( $records as $record ){
			$ASIN = $record['ASIN'] ;
			//获取成本
			$sql = "SELECT * FROM sc_product_cost_details WHERE ( ASIN = '{@#asin#}'
								OR ( account_id = '{@#accountId#}' AND listing_sku = '{@#listingSku#}' )
					)   ORDER BY fba_cost DESC LIMIT 0,1 " ;
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