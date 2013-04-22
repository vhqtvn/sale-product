<?php

App :: import('Vendor', 'PHPExcel/IOFactory');

class ExcelController extends AppController {
	var $uses = array('ExcelUtils');
	
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
		$inProducts = $SqlUtils->exeSql("sql_warehouse_in_products",array('inId'=>$id)) ;
		
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