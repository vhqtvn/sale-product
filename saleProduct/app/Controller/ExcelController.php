<?php

App :: import('Vendor', 'PHPExcel/IOFactory');
App :: import('Vendor', 'PHPExcel');

class ExcelController extends AppController {
	var $uses = array('ExcelUtils');
	
	public function importEbayCategory(){
		
		ignore_user_abort(1);
		set_time_limit(0);
		
		ini_set("memory_limit", "256M");
		ini_set("post_max_size", "78M");
		
		$filePath = 'app/Vendor/phpExcelReader/ebayCategories_us.xls';
		$PHPExcel = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel5();
		$PHPExcel = $PHPReader->load($filePath);
		$currentSheet = $PHPExcel->getSheet(0);
		/**取得最大的列号*/
		$allColumn = $currentSheet->getHighestColumn();
		
		/**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();
		/**从第二行开始输出，因为excel表中第一行为列名*/
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
			$categoryId = null ; //分类ID
			$parantId = null ; 	//父ID
			$categoryName = null ; //分类名称
			$categoryFullName = null ;//分类全名
			
			/**从第A列开始输出*/
			for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
				$val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
				
				if($currentColumn == 'B'){
					if( !empty($val) ){
						$categoryName = $val ;
						$categoryFullName = $val ;
					}
				} else if($currentColumn == 'C'){
					if( !empty($val) ){
						$categoryName = $val ;
						$categoryFullName .= ">".$val ;
					}
				} else if($currentColumn == 'D'){
					if( !empty($val) ){
						$categoryName = $val ;
						$categoryFullName .= ">".$val ;
					}
				} else if($currentColumn == 'E'){
					if( !empty($val) ){
						$categoryName = $val ;
						$categoryFullName .= ">".$val ;
					}
				} else if($currentColumn == 'F'){
					if( !empty($val) ){
						$categoryName = $val ;
						$categoryFullName .= ">".$val ;
					}
				} else if($currentColumn == 'G'){
					if( !empty($val) ){
						$categoryName = $val ;
						$categoryFullName .= ">".$val ;
					}
				} else if($currentColumn == 'H'){
					if( !empty($val) ){
						$categoryName = $val ;
						$categoryFullName .= ">".$val ;
					}
				} else if( $currentColumn == 'I'  ){//CategoryId
					$categoryId = $val ;
				}else if( $currentColumn == 'J'  ){//ParentId
					$parantId = $val ;
				}else{
					//echo $val;
					/**如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出*/
					//echo iconv('utf-8','gb2312', $val)."\t";
				}
			}
			
			if( $categoryId == $parantId  ){
				$parantId = null ;
			}
			
			if( !empty($categoryId) ){
				$SqlUtils  = ClassRegistry::init("SqlUtils") ;
				$SqlUtils->exeSql("INSERT INTO sc_ebay_category 
							(CATEGORY_ID, 
							PARENT_ID, 
							NAME, 
							FULLNAME, 
							TRANSACTION, 
							COUNTRY
							)
							VALUES
							('{@#categoryId#}', 
							'{@#parentId#}', 
							'{@#name#}', 
							'{@#fullname#}', 
							'{@#transaction#}', 
							'US'
							)",array(
									'categoryId'=> $categoryId,
									'parentId'=> $parantId,
									'name'=>$categoryName,
									'fullname'=>$categoryFullName
									)
						) ;
			}
			
		}
	}
	
	public function exportEndiciaOrder(){
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$items = $SqlUtils->exeSqlWithFormat("select * from sc_view_endicia",array()) ;
		
		$inputFileName = 'app/template/endicia-order-template.xls';
		
		$params = array( 'p'=>$items) ;
		
		$this->ExcelUtils->export( $inputFileName , $params , 'endicia-orders' ,function($sheet , $row , $isNew){
			if( $isNew ){
				//$sheet->mergeCells("B$row:E$row") ;
			}
		}) ;
		exit;
	}
	
	public function box($id){
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$in = $SqlUtils->getObject("sql_warehouse_in_getById",array('id'=> $id)) ;
		$inProducts = $SqlUtils->exeSql("sql_warehouse_in_products",array('inId'=>$id)) ;
		
		$products = array() ;
		foreach($inProducts as $p){
			$products[] = $SqlUtils->formatObject($p) ;
		} ;
		
		//debug($products);
		
		$params = array('in'=>$in , 'p'=>$products) ;
		
		$inputFileName = 'app/template/box-template.xls';
		//return ;
		$this->ExcelUtils->export( $inputFileName , $params , 'package-list-'.$in['IN_NUMBER'] ,function($sheet , $row , $isNew){
			if( $isNew ){
				$sheet->mergeCells("B$row:E$row") ;
			}
		}) ;
		exit;
	}
	
	public function read($id){
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$in = $SqlUtils->getObject("sql_warehouse_in_getById",array('id'=> $id)) ;
		$inProducts = $SqlUtils->exeSql("sql_warehouse_in_products4Invoice",array('inId'=>$id)) ;
		
		$indate = $ppo=date('Y/m/d',strtotime($in['SHIP_DATE']));
		$in['SHIP_DATE'] = $indate ;
		
		$products = array() ;
		$total = 0 ;
		foreach($inProducts as $p){
			$p = $SqlUtils->formatObject($p) ;
			$total = $total + $p['DECLARATION_PRICE']  * $p['QUANTITY'] ;
			$p['TOTAL'] = $total ;
			$products[] = $p ;
		} ;
		
		$params = array('in'=>$in , 'p'=>$products,'total'=>$total) ;
		
		$inputFileName = 'app/template/in_invoice.xls';
		
		$this->ExcelUtils->export( $inputFileName , $params , 'commerical-invoice-'.$in['IN_NUMBER'] ,function($sheet , $row , $isNew){
			if( $isNew ){
				$sheet->mergeCells("A$row:B$row") ;
				$sheet->mergeCells("C$row:F$row") ;
				$sheet->mergeCells("H$row:I$row") ;
				$sheet->mergeCells("J$row:K$row") ;
			}
		} ) ;
	} 
}