<?php 
    ob_implicit_flush(true); 

	include "Snoopy.class.php" ;
	//include_once('simplehtmldom/simple_html_dom.php');

	//http://www.amazon.com/s/qid=1341235187/ref=sr_pg_2?ie=UTF8&me=A1L3WBCG312F8S&page=2&rh=
	//http://www.amazon.com/s/qid=1341235187/ref=sr_pg_2?ie=UTF8&me=A1L3WBCG312F8S&page=2&rh=

    //商店地址格式
    //http://www.amazon.com/gp/browse.html?ie=UTF8&marketplaceID=ATVPDKIKX0DER&me=A1L3WBCG312F8S

	
	//$html = new simple_html_dom();
   

   echo "12222222222222222222222222222";
   ob_flush();

    for( $j= 0 ; $j < 20 ; $j++ ){
				$snoopy = new Snoopy;
	    		echo '************************page'.$j.'*******************************<br/>' ;
				if($snoopy->fetch("http://www.amazon.com/s/qid=1341235187/ref=sr_pg_".$j."?ie=UTF8&me=A1L3WBCG312F8S&page=".$j."&rh=")){
					echo '11'.'<br/>' ;
					$Result = $snoopy->results ;
					echo '22'.'<br/>' ;
					/*
					print '33'.'<br/>' ;
					//从文件中加载
					$html->load( $Result );
					print '44'.'<br/>' ;
				   $products =  $html->find('.result');
				   print '55'.'<br/>' ;

					for ($i=0;$i<count($products);$i++){
						print $products[ $i ]->name.'<br/>' ;
					}*/
					
					unset($Result);
					unset($snoopy);
				}else{
					echo "error fetching document: ".$snoopy->error."\n";
					break ;
				}

				ob_flush();

	}

?> 