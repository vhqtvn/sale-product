<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>Smart Desks</title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
	<?php
   	   include ('config/config.php');
   	   
   		echo $this->Html->meta('icon');
		echo $this->Html->css('../../View/Portal/themes/default/webos');
		echo $this->Html->css('../../View/Portal/themes/default/smartMenu');
		
	?>
	<script type="text/javascript" src="/<?php echo $fileContextPath;?>/app/View/Portal/js/webos_compress_client.js"></script>
	<script type="text/javascript" src="/<?php echo $fileContextPath;?>/app/View/Portal/js/data_interface_demo.js"></script>
	<script type="text/javascript" src="/<?php echo $fileContextPath;?>/app/webroot/js/common.js"></script>

	
	<script type="text/javascript">
	 var accounts = [] ;
     var indexdata = [] ;
     var map = {} ;
     var itemCache = [] ;
     var treeMap = {} ;
     
     <?php
     $User = $this->Session->read("product.sale.user") ;
     
     $userModel  = ClassRegistry::init("User") ;
     $funs = $userModel->getSecurityFunctionsByUserId( $User["ID"] );
     //print_r($funs) ;
     
     $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
     $accounts = $amazonAccount->getAccounts();
     
     $Functions = $funs['functions'] ;
     $accounts  = $funs['accounts'] ;
     $accountSecuritys = $funs['accountSecuritys'] ;
     $filterRules = $funs['filterRules'] ;
     
     
     $index = 0 ;
     foreach( $Functions as $Record ){
			$sfs = $Record['sc_security_function']  ;
			
			$id   = $sfs['ID'] ;
			$code = $sfs['CODE'] ;
			$name = $sfs['NAME'] ;
			$pid  = $sfs['PARENT_ID'] ;
			$url  = $sfs['URL'] ;
			
			//format $url ;
			if( substr($url, 0, 1)=="/" ){
				$url = "/".$fileContextPath.$url;
			}else if( !empty($url) ){
				$url = $contextPath.'/'.$url ;
			}
			
			echo " var item$index = {id:'$id',name:'$name',pid:'$pid',url:'$url',isexpand:false,code:'$code'} ;" ;
			
			echo " itemCache.push( item$index ) ;" ;
			
			echo " treeMap['id_$id'] = item$index  ;" ;
			if(empty($pid)){
				echo "indexdata.push( item$index ) ;" ;
			}
			$index++ ;
		} ;
		
		foreach( $filterRules as $Record ){
			$sfs = $Record['sc_security_function']  ;
			
			$id   = $sfs['ID'] ;
			$code = $sfs['CODE'] ;
			$name = $sfs['NAME'] ;
			$pid  = $sfs['PARENT_ID'] ;
			$url  = $contextPath."/product/rule/$id" ;
			echo " var item$index = {id:'$id',name:'$name',pid:'$pid',url:'$url',isexpand:false,code:'$code'} ;" ;
			
			echo " itemCache.push( item$index ) ;" ;
			
			echo " treeMap['id_r$id'] = item$index  ;" ;
			if(empty($pid)){
				echo "indexdata.push( item$index ) ;" ;
			}
			$index++ ;
		} ;
		
		foreach( $accounts as $Record ){
			$sfs = $Record['sc_amazon_account']  ;
			$aid   = $sfs['ID'] ;
			$name  = $sfs["NAME"] ;
			$aname = $name ;
			$security = $accountSecuritys[$aid] ;
			
			echo " var account_$aid = {id:'a___$aid',accountId:'$aid',name:'$name',childs:[],isexpand:false} ;" ;
			echo " accounts.push(account_$aid ) ;" ;
			
			foreach( $security as $Record1 ){
				$sfs1 = $Record1['sc_security_function']  ;
				
				$id   = $sfs1['ID'] ;
				$code = $sfs1['CODE'] ;
				$name = $sfs1['NAME'] ;
				$pid  = $sfs1['PARENT_ID'] ;
				$url  = $sfs1['URL'] ;
				if(!empty($url)){
					$url = $url."/".$aid ;
				}
				
				echo " account_$aid.childs.push({id:'a___$aid"."_"."$code',url:'$url',name:'$name',pid:'a___$aid'});" ;
				
			} ;
		} ;
     ?>
     
    $(itemCache).each(function(){
		if( this.pid && treeMap['id_'+this.pid]){
			treeMap['id_'+this.pid].childs = treeMap['id_'+this.pid].childs||[] ;
			treeMap['id_'+this.pid].childs.push(this) ;
		}
		
		if(this.code == "marketing_manage"){
			this.childs = this.childs||[] ;
			var me = this ;
			$(accounts).each(function(){
				me.childs.push(this) ;
			}) ;
		}
	}) ;
	</script>
</head>
<body>
	<!-- 漂浮白云 -->
	<div class="scene_cloud_container">
		<div class="scene_cloud"></div>
	</div>
	
	<div class="desktop_header">
		<!-- 顶部隐藏菜单 -->
		<div id="header_user_main"></div>
		<div class="header_user" id="header_user_info" open="1">			
			<p>
				<span class="user_icon"></span>
				<span class="arrow_down_icon"></span>
			</p>
		</div>		
		<div class="header_link">
		</div>
		<div class="header_title">
			<div class="title_name">
				<span class="title_name_icon"></span>
				<strong></strong>
			</div>
			<div class="title_time"></div>
			<div class="title_name" style="float:right;width:auto;margin-right:20px;"><?php echo $User['NAME'];?></div>
		</div>
	</div>
	<!-- 桌面内容动态加载 -->
	<div class="desktop_container">
		<div class="desktop_content">
			<div class="desktop_main">
			</div>
		</div>
	</div>
	<!-- 桌面分屏切换 -->
	<div id="deskpage" style="">
		<div class="deskpage_left"></div>
		<div class="img_l"></div>
		<ul class="deskpage_list"></ul>
		<div class="img_r">	</div>
		<div class="deskpage_right"></div>
	</div>
	<!-- 任务栏 -->
 	<div id="taskbar">
  		<div id="start_logo">
			<a href="###" class="logo_button"></a>
  		</div>
  		<div id="task_opened"></div>
  		<div id="task_tool">
   			<div id="tool_line"></div>
  		</div>
 	</div>
 
 	<div class="start_menu">			
		<div class="start_menu_top"></div>
		<div class="start_menu_main">
			<div class="menu_weicome">
				<img src="/<?php echo $fileContextPath;?>/app/webroot/img/m/smiley-happy.png" alt=""/>
				<div class="menu_weicome_font">
					欢迎您，<?php echo $User['NAME'];?>
				</div>
			</div>

			<div class="menu_line"></div>
			<ul class="menu_list"></ul>
		</div>
		<div class="start_menu_bottom menulogo">
			<a href="###" class="logo_button"></a>
		</div>
	</div>
	
	
		
	
	<!--  
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	
	<script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/json2.js"></script>
	
	
	<script type="text/javascript" src="js/data_interface_demo.js"></script>
	
	
	
	<script type="text/javascript" src="js/webos.services.js"></script>
	<script type="text/javascript" src="js/jquery-smartMenu.js"></script>
	<script type="text/javascript" src="js/webos.js"></script>	
	
	-->
	
	<!--
	<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>
	-->
</body>
</html>