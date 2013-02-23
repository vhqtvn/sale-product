<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../ligerUI/lib/ligerUI/skins/Aqua/css/ligerui-all');
		echo $this->Html->script('jquery');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('../ligerUI/lib/ligerUI/js/ligerui.min');
		
		$userModel  = ClassRegistry::init("User") ;
		$funs = $userModel->getSecurityFunctions( $User["GROUP_CODE"] ); 
		//print_r($funs) ;
		
		$amazonAccount  = ClassRegistry::init("Amazonaccount") ;
		$accounts = $amazonAccount->getAccounts();  
		
		$Functions = $funs['functions'] ;
		$accounts  = $funs['accounts'] ;
		$accountSecuritys = $funs['accountSecuritys'] ;
		$filterRules = $funs['filterRules'] ;

	?>

        <script type="text/javascript">
          var accounts = [] ;
          var indexdata = [] ;
          var map = {} ;
          var itemCache = [] ;
          var treeMap = {} ;
          
          <?php
          $index = 0 ;
          foreach( $Functions as $Record ){
				$sfs = $Record['sc_security_function']  ;
				
				$id   = $sfs['ID'] ;
				$code = $sfs['CODE'] ;
				$name = $sfs['NAME'] ;
				$pid  = $sfs['PARENT_ID'] ;
				$url  = $sfs['URL'] ;
				echo " var item$index = {id:'$id',text:'$name',pid:'$pid',url:'$url',isexpand:false,code:'$code'} ;" ;
				
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
				$url  = "/saleProduct/index.php/product/rule/$id" ;
				echo " var item$index = {id:'$id',text:'$name',pid:'$pid',url:'$url',isexpand:false,code:'$code'} ;" ;
				
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
				
				echo " var account_$aid = {id:'a___$aid',accountId:'$aid',text:'$name',children:[],isexpand:false} ;" ;
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
					
					echo " account_$aid.children.push({id:'a___$aid"."_"."$code',url:'$url',text:'$name',pid:'a___$aid'});" ;
					
				} ;
			} ;
          ?>
          
         $(itemCache).each(function(){
			if( this.pid && treeMap['id_'+this.pid]){
				treeMap['id_'+this.pid].children = treeMap['id_'+this.pid].children||[] ;
				treeMap['id_'+this.pid].children.push(this) ;
			}
			
			if(this.code == "order_manage"){
				this.children = this.children||[] ;
				var me = this ;
				$(accounts).each(function(){
					me.children.push(this) ;
				}) ;
			}
		}) ;
		
		$(function(){
			$("#tree1").ligerTree({
			    data : indexdata,
			    checkbox: false,
			    slide: false,
			    nodeWidth: 120,
			    attribute: ['nodename', 'url'],
			    onSelect: function (node)
			    {
					if (!node.data.url) return;
					var tabid = $(node.target).attr("tabid");
					if (!tabid){
					    tabid = new Date().getTime();
					    $(node.target).attr("tabid", tabid)
					} 
					
					var prevText = $(node.target).parents("ul:first").prev().text() ;
					if( prevText && $.trim(prevText) ){
						prevText = prevText+" 》" ;
					}

					f_addTab(tabid,prevText+ node.data.text, node.data.url);
			    }
			});
		})
		
		///saleProduct/index.php/grid/scriptitem
		///saleProduct/index.php/product/rule/"+this.ID

            var tab = null;
            var accordion = null;
            var tree = null;
            $(function (){
                //布局
                $("#layout1").ligerLayout({ leftWidth: 190, height: '100%',heightDiff:-34,space:4, onHeightChanged: f_heightChanged });

                var height = $(".l-layout-center").height();
                //Tab
                $("#framecenter").ligerTab({ 
                	height: height/*,
                	onAfterSelectTabItem:function(tabId){
                		//alert( tabId );
                		$("#"+tabId).attr( "src" , $("#"+tabId).attr("src") ) ;
                		//document.getElementById(tabId).window.location.reload() ;
                		return true ;
                	}*/
                 });

                //面板
                $("#accordion1").ligerAccordion({ height: height - 24, speed: null });

                $(".l-link").hover(function ()
                {
                    $(this).addClass("l-link-over");
                }, function ()
                {
                    $(this).removeClass("l-link-over");
                });
               

                tab = $("#framecenter").ligerGetTabManager();
                accordion = $("#accordion1").ligerGetAccordionManager();
                tree = $("#tree1").ligerGetTreeManager();
                $("#pageloading").hide();

            });
            function f_heightChanged(options)
            {
                if (tab)
                    tab.addHeight(options.diff);
                if (accordion && options.middleHeight - 24 > 0)
                    accordion.setHeight(options.middleHeight - 24);
            }
            function f_addTab(tabid,text, url)
            { 
                tab.addTabItem({ tabid : tabid,text: text, url: url });
            } 
             
            
     </script> 
<style type="text/css"> 
    body,html{height:100%;}
    body{ padding:0px; margin:0;   overflow:hidden;}  
    .l-link{ display:block; height:26px; line-height:26px; padding-left:10px; text-decoration:underline; color:#333;}
    .l-link2{text-decoration:underline; color:white; margin-left:2px;margin-right:2px;}
    .l-layout-top{background:#102A49; color:White;}
    .l-layout-bottom{ background:#E5EDEF; text-align:center;}
    #pageloading{position:absolute; left:0px; top:0px; background:white url('loading.gif') no-repeat center; width:100%; height:100%;z-index:99999;}
    .l-link{ display:block; line-height:22px; height:22px; padding-left:16px;border:1px solid white; margin:4px;}
    .l-link-over{ background:#FFEEAC; border:1px solid #DB9F00;} 
    .l-winbar{ background:#2B5A76; height:30px; position:absolute; left:0px; bottom:0px; width:100%; z-index:99999;}
    .space{ color:#E7E7E7;}
    /* 顶部 */ 
    .l-topmenu{ margin:0; padding:0; height:31px; line-height:31px; background:url('/saleProduct/app/webroot/ligerUI/lib/images/top.jpg') repeat-x bottom;  position:relative; border-top:1px solid #1D438B;  }
    .l-topmenu-logo{ color:#E7E7E7; padding-left:35px; line-height:26px;background:url('/saleProduct/app/webroot/ligerUI/lib/images/topicon.gif') no-repeat 10px 5px;}
    .l-topmenu-welcome{  position:absolute; height:24px; line-height:24px;  right:30px; top:2px;color:#070A0C;}
    .l-topmenu-welcome a{ color:#E7E7E7; text-decoration:underline} 

	.logout , .logout a{
		float:right;
		margin-right:20px;
		color:#FFF;
		font-weight:bold;
		font-size:15px;
	}
 </style>
</head>
<body style="padding:0px;background:#EAEEF5;">  
<div id="pageloading"></div>  
<div id="topmenu" class="l-topmenu">
    <div class="l-topmenu-logo">
    	产品营销系统
    	 <div class="logout" >
    	 	<a href="/saleProduct/index.php/users/logout">退出</a>
    	 	<a><?php 
    	 	if( isset($User) ){
    	 		echo $User["NAME"] ;
    	 	} ?></a>
    	 </div>	
    </div>
   
</div>
  <div id="layout1" style="width:99.2%; margin:0 auto; margin-top:4px; "> 
        <div position="left"  title="" id="accordion1"> 
             <div title="功能列表" class="l-scroll">
                 <ul id="tree1" style="margin-top:3px;">
            </div>
        </div>
        <div position="center" id="framecenter"> 
	   
            <div tabid="home" title="我的主页" style="height:300px" >
                <iframe frameborder="0" name="home" id="home" src="/saleProduct/index.php/home/widgets"></iframe>
            </div> 
        </div> 
        
    </div>
    <div  style="height:32px; line-height:32px; text-align:center;">
           双橙科技
    </div>
    <div style="display:none"></div>
</body>
</html>
