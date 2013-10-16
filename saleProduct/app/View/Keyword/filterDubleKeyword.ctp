<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
   <?php
 /*  ini_set("memory_limit", "62M");
   ini_set("post_max_size", "24M");
   ob_end_flush();//关闭缓存
   set_time_limit(0);
   */
	?>
  
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

</head>

<body class="container-popup">
	
   <?php
   $keyword  = ClassRegistry::init("Keyword") ;
   
   ob_end_flush();//关闭缓存
   /*
   $sql= "select * from (
		select task_id,keyword,site,cpc,count(1) as c  from sc_keyword group by task_id,keyword,site,cpc
		)  t where c>1
		" ;
   $i = 0 ;
   $result = $keyword->exeSqlWithFormat($sql,array()) ;

   foreach( $result as $item ){
   		$sql = "select * from sc_keyword where task_id = '{@#task_id#}' and keyword = '{@#keyword#}'
   		and site = '{@#site#}' and cpc = '{@#cpc#}'  limit 0 ,1 " ;
   		$r = $keyword->getObject($sql,$item) ;
   		
   		if(!empty($r)){
   			$i++;
   			echo $i.'--' ;
   			flush();
   			 
   			$deleteSql = "delete from sc_keyword where task_id = '{@#task_id#}' and keyword = '{@#keyword#}'
			   		and site = '{@#site#}' and cpc = '{@#cpc#}'
			   		and keyword_id <> '{@#keyword_id#}'  and is_main_keyword = 0
			   		" ;
			   			$keyword->exeSql($deleteSql,$r) ;
   			 
   		}
   		
   }*/
   
   for( $i=470784 ;$i>470000  ;$i-- ){
   	$sql = "select * from sc_keyword limit $i ,1 " ;
   	$result = $keyword->getObject($sql,array()) ;
   	
   	//查找前面是否存在对应的数据，如果存在，则删除目前这条数据
   	
   	if(!empty($result)){

   		 $getSql = "select count(1) as c from sc_keyword where task_id = '{@#task_id#}' and keyword = '{@#keyword#}'
   		and site = '{@#site#}' and cpc = '{@#cpc#}'
   		" ;
   		 
   		 $item = $keyword->getObject($getSql, $result ) ;
   		 
   		 if(!empty( $item ) && $item['c'] > 1 && $result['is_main_keyword'] != 1  ){
			echo '**'.$i.'**' ;
			$deleteSql = "delete from sc_keyword where keyword_id = '{@#keyword_id#}' " ;
   		 	$keyword->exeSql($deleteSql,$result) ;
   		 }
   		 
   		 echo $i.'--' ;
   		 flush();
   		
   	}
   		
   }
   ?>
   
</body>
</html>