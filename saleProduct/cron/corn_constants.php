<?php
	require '../app/Config/database.php';
	
	$databaseConfig = new DATABASE_CONFIG() ;
	$host = $databaseConfig->gbk['host']  ;
	$username = $databaseConfig->gbk['login']  ;
	$password = $databaseConfig->gbk['password']  ;
	$database =  $databaseConfig->gbk['database']  ;
	
	$conn=@mysql_connect($host,$username,$password) or die("连接错误");
	
	mysql_select_db($database,$conn);
	mysql_query("set names 'GBK'");
	
	$accounts = array() ;
	$ebayAccounts = array() ;
	try{
		$sql="select (
				select code from  sc_platform sp where sp.id = saa.platform_id
				)  as platformCode ,saa.*   
				from sc_amazon_account saa where status = 1";
		$result = mysql_query($sql)  or die("Invalid query: " . mysql_error());
		while ($row=mysql_fetch_row($result))
		{
			$platform = $row[0] ;
			$accountId = $row[1] ;
			$domain = $row[5] ;
			$context = $row[6] ;
			//9 amazon 25 ebay
			$platform = strtolower($platform) ;
			echo $platform ;
			if( strpos( $platform ,"amazon" ) === 0 ){
				$accounts [] = array('accountId'=>$accountId,'domain'=>$domain,'context'=>$context) ;
			}else if( strpos( $platform ,"ebay" ) === 0 ){
				$ebayAccounts [] = array('accountId'=>$accountId,'domain'=>$domain,'context'=>$context) ;
			}
		}
		mysql_close($conn) ;
	}catch(Exception $e){
		print_r($e) ;
		mysql_close($conn) ;
	}
	
	////////////////////common method/////////////////////
	function sock_get($url)
	{
		$info = parse_url($url);
		$fp = fsockopen($info["host"], 80, $errno, $errstr, 3);
		$head = "GET ".$info['path']."?".$info["query"]." HTTP/1.0\r\n";
		$head .= "Host: ".$info['host']."\r\n";
		$header .= "Connection:Close\r\n";
		$head .= "\r\n";

		fwrite($fp, $head);
		fclose($fp);
		
	}

		function triggerRequest($url, $post_data = array(), $cookie = array()){
			$method = "GET";  //通过POST或者GET传递一些参数给要触发的脚本
			$url_array = parse_url($url); //获取URL信息
			$port = isset($url_array['port'])? $url_array['port'] : 80;
			$fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30);
			if (!$fp) {
				return FALSE;
			}
			$getPath = $url_array['path'] ."?". $url_array['query'];
			if(!empty($post_data)){
				$method = "POST";
			}
			
			$header = "$method $getPath HTTP/1.1\r\n";
			$header .= "Host: ". $url_array['host'] . "\r\n";
			$header .= "Connection:Close\r\n";
			
			//echo $header ;
			
			if(!empty($cookie)){
				$_cookie = strval(NULL);
				foreach($cookie as $k => $v){
					$_cookie .= $k."=".$v."; ";
				}
				$cookie_str =  "Cookie: " . base64_encode($_cookie) ." \r\n"; //传递Cookie
				$header .= $cookie_str;
			}
			if(!empty($post_data)){
				$_post = strval(NULL);
				foreach($post_data as $k => $v){
					$_post .= $k."=".urlencode($v)."&";
				}
				
				$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$header .= "Content-Length: " . strlen($_post) . "\r\n\r\n";
				$header .= $_post;
			}
			
			echo $header ;
			
			fwrite($fp, $header);
			echo fread($fp, 1024); //服务器返回
			fclose($fp);
			return true;									
		}