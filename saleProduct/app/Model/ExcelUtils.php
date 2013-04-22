<?php
class ExcelUtils extends AppModel {
	var $useTable = 'sc_user';
	
	private  $_index = array() ;
	
	public function export($tempName,$params,$exportName,$callback = null){
		$inputFileType = 'Excel5';
		
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$PHPExcel = $objReader->load($tempName);
		
		$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumm = $sheet->getHighestColumn(); // 取得总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($highestColumm); //字母列转换为数字列 如:AA变为27
		
		/** 循环读取每个单元格的数据 */
		for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
			//echo $row.'<br/>' ;
			//getRow Column Value Map
			$map = array() ;
			for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);
				$columnValue = $sheet->getCellByColumnAndRow($column, $row)->getValue() ;
				if( !empty($columnValue) ){
					$map[ $columnName ] = $columnValue ;
				}
			}
			//	debug( $map ) ;
			//判断是否循环行
			$isLoopRow = false ;
			$loopCount = 0 ;
			for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);
				$columnValue = $sheet->getCellByColumnAndRow($column, $row)->getValue() ;
				if( !empty($columnValue) ){
					$_ = $this->isLoopRow($columnValue ,$params,$row ) ;
					$isLoopRow = $_['isLoop'] ;
					if( $isLoopRow ){//loopCount
						$loopCount = $_['loopCount'] ;
						break;
					}
				}
			}
		
			//echo $loopCount ;
			//return ;
		
			if( $isLoopRow ){//循环行，获取循环行的变量与只对应关系
				for ($i=0; $i<$loopCount ; $i++) {
					if( $i >=1 ){
						$row++ ;
						$highestRow++;
						$PHPExcel->getActiveSheet()->insertNewRowBefore($row,1) ;
						
						if( !empty($callback ) ){
							$callback($PHPExcel->getActiveSheet() , $row , true ) ;
						} ;
					}
		
					foreach( $map as $m=>$v ){
						$parseValue = $this->parseVariable($v ,$params,$row ) ;
						$PHPExcel->getActiveSheet()->setCellValue($m.$row , $parseValue );
						//echo $m.$row.'===>'.$parseValue.'<br/>' ;
					}
		
				}
			}else{
				foreach( $map as $m=>$v ){
					if( empty($v)) continue ;
					$parseValue = $this->parseVariable($map[$m] ,$params,$row ) ;
					$PHPExcel->getActiveSheet()->setCellValue($m.$row , $parseValue );
					//echo $m.$row.'===>'.$parseValue.'<br/>' ;
				}
			}
		}
		//return ;
		$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5'); //$objPHPExcel是上文中读的资源
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$exportName.'.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter->save('php://output');
		
	}
	
	public function isLoopRow($vari , $obj,$row){
		$vari = $this->getVariable($vari) ;
		//debug($vari) ;
		foreach( $vari as  $vr ){
			$array = explode('.' ,$vr) ;
			$temp = $obj ;
			$loopVariable = '' ;
			$loopCount = 0 ;
			$pre = null ;
			$isLoopRow= false ;
	
			foreach( $array as $a ){
				if( $a  == 'currentDate' ) continue ;
				if( $a == 'i' ){//数组处理
					$isLoopRow = true ;
					$loopVariable = $pre ;
					//debug($temp) ;
					$loopCount = count($temp) ;
					break ;
				}else{
					$temp = $temp[$a] ;
				}
				$pre = $a ;
			}
			if($isLoopRow){
				return array('isLoop'=>$isLoopRow, 'loopCount'=>$loopCount ) ;
			}
	
		} ;
		return array('isLoop'=>false) ;
	}
	
	public function parseValue($vari , $obj,$row){
		if( $vari == 'currentDate' ){
			return date('Y-m-d');
		}
	
		$array = explode('.' ,$vari) ;
		$temp = $obj ;
		$pre = '' ;
		$isLoopRow = false ;
		$loopVariable = null ;
	
		foreach( $array as $a ){
			if( $a == 'i' ){//数组处理
				//循环数组
				$isLoopRow = true ;
	
				if(! isset($this->_index[$pre] ) ){
					$this->_index[$pre] = $row ;//开始计数
					$loopVariable = $pre ;
				}
	
				$temp = $temp[ $row - $this->_index[$pre]  ] ;
			}else{
				$temp = $temp[$a] ;
			}
			$pre = $a ;
		}
		return $temp ;
	}
	
	public function  getVariable($value , $obj = null,$row=null){
		$array = explode('${', $value) ;
		$rs = '' ;
		$vs = array() ;
		foreach( $array as $a ){
			if( strpos($a, "}") >=1 ){
				$_array = explode("}", $a) ;
				$variable = $_array[0] ;
				$vs[] = $variable ;
			}
		} ;
		return $vs ;
	}
	
	public function  parseVariable($value , $obj = null,$row){
		$array = explode('${', $value) ;
		$rs = '' ;
		foreach( $array as $a ){
			if( strpos($a, "}") >=1 ){
				$_array = explode("}", $a) ;
				$variable = $_array[0] ;
				$rs .= $this->parseValue($variable,$obj,$row) ;
				$rs .= $_array[1] ;
			}else{
				$rs .= $a ;
			}
		} ;
		return $rs ;
	}
	
}