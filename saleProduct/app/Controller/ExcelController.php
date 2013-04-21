<?php

App :: import('Vendor', 'PHPExcel/IOFactory');

class ExcelController extends AppController {
	public function read($id){
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$in = $SqlUtils->getObject("sql_warehouse_in_getById",array('id'=> $id)) ;
		
		$inProducts = $SqlUtils->exeSql("sql_warehouse_in_products",array('inId'=>$id)) ;
		
		
		$inputFileName = 'app/template/in_invoice.xls';

		$inputFileType = 'Excel5';
		//	$inputFileType = 'Excel2007';
		//	$inputFileType = 'Excel2003XML';
		//	$inputFileType = 'OOCalc';
		//	$inputFileType = 'Gnumeric';
		//$inputFileName = './sampleData/example1.xls';
		
		//echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		
		
		/**  Read the list of Worksheet Names from the Workbook file  **/
		//echo 'Read the list of Worksheets in the WorkBook<br />';
		$worksheetNames = $objReader->listWorksheetNames($inputFileName);
		
		//echo 'There are ',count($worksheetNames),' worksheet',((count($worksheetNames) == 1) ? '' : 's'),' in the workbook<br /><br />';
		foreach($worksheetNames as $worksheetName) {
			//echo $worksheetName,'<br />';
		}
		
		$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		
		$indate = $ppo=date('Y/m/d',strtotime($in['SHIP_DATE']));
		$shipNo = $in['SHIP_NO'] ;
		
		//处理运单基本信息
		$objPHPExcel->getActiveSheet()->setCellValue('B5' , $in['SHIP_NO'] );
		$objPHPExcel->getActiveSheet()->setCellValue('A8' ,$indate);
		$objPHPExcel->getActiveSheet()->setCellValue('F6' ,$in['SEND_COMPANY']."\n".$in['SEND_COMPANY_ADDRESS'] );
		$objPHPExcel->getActiveSheet()->setCellValue('F12' ,$in['RECEIVE_COMPANY_CONTACTOR']."\n".$in['RECEIVE_COMPANY_ADDRESS'].' '.$in['RECEIVE_COMPANY_POST']."\n".$in['RECEIVE_COMPANY_PHONE']);//收件人 地址 国家  邮编
		
		$total = 0 ;
		$startRow = 19 ;

		//$style = $objPHPExcel->getActiveSheet()->duplicateStyle(null , "A20:K20") ;
		//处理
		foreach( $inProducts as $p ){
			$p = $SqlUtils->formatObject($p) ;
			$name = $p['DECLARATION_NAME'] ;
			$quantity = $p['QUANTITY'] ;
			$price = $p['DECLARATION_PRICE'] ;
			$total = $total +$price * $quantity ;
			
			if( $startRow > 19  ){
				$objPHPExcel->getActiveSheet()->insertNewRowBefore($startRow,1) ;
			}
			
			$objPHPExcel->getActiveSheet()->mergeCells("A$startRow:B$startRow") ;
			$objPHPExcel->getActiveSheet()->mergeCells("C$startRow:F$startRow") ;
			$objPHPExcel->getActiveSheet()->mergeCells("H$startRow:I$startRow") ;
			$objPHPExcel->getActiveSheet()->mergeCells("J$startRow:K$startRow") ;
			
			
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$startRow , $name );
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$startRow ,$quantity);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$startRow ,$price);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$startRow ,$price * $quantity);
			
			
			$startRow = $startRow +1;
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$startRow , "TATOL:USD$total" );
		$objPHPExcel->getActiveSheet()->setCellValue('H'.($startRow+1) , "DATE:".date('Y-m-d') );
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //$objPHPExcel是上文中读的资源
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="commerical-invoice-'.$in['IN_NUMBER'].'.xls"');
		header('Cache-Control: max-age=0');
		
		$filename = microtime() ;
		
		$objWriter->save('php://output');
		//$objWriter->save("app/template/instance/$filename.xls");
		
		/*header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="形式发票.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //$objPHPExcel是上文中读的资源
		$objWriter->save('php://output');*/
		exit;
		
		echo '<hr />';
		
		
	//	var_dump($sheetData);

	}
}