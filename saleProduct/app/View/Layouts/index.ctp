<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>Sale Product</title>
    <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../ligerUI/lib/ligerUI/skins/Aqua/css/ligerui-all');
		echo $this->Html->script('jquery');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('../ligerUI/lib/ligerUI/js/ligerui.min');
		
		$amazonAccount  = ClassRegistry::init("Amazonaccount") ;
		$accounts = $amazonAccount->getAccounts();  
	?>

        <script type="text/javascript">
          var accounts = [] ;
          var accountsCategory = [] ;
        
      <?php  
		foreach( $accounts as $Record ){
			$sfs = $Record['sc_amazon_account']  ;
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME']  ;
			$pid = null ;
			echo " accounts.push({id:'a_$id',accountId:'$id',text:'$name',isExpand:true,url:'/saleProduct/index.php/amazonaccount/productLists/$id'}) ;" ;
			echo " accountsCategory.push({id:'c_$id',accountId:'$id',text:'$name',isExpand:true,url:'/saleProduct/index.php/amazonaccount/category/$id'}) ;" ;
		} ;
	?>
        var indexdata = [] ;
        var map = {} ;
        var scripts = null ;
        $.ajax({
			type:"get",
			url:"/saleProduct/index.php/grid/functionItems",
			data:{start:0,limit:100,curPage:0,end:0},
			cache:false,
			dataType:"text",
			success:function(result,status,xhr){
				
				if(typeof result == 'string'){
					eval("result = " +result) ;
				}
				var formatDate = window.formatGridData||function(data){ return data } ;
				result = formatDate(result) ;

				var cacheItem = [] ;
				$(result.records).each(function(){
					var item = {isexpand:false,text:this.NAME,pid:this.PARENT_ID} ;
					if(this.URL) item.url = this.URL ;
					map["P"+this.ID] = item ;
					if(this.CODE == 'filter_rule'){
						item.children = [] ;
						scripts = item ;
					}
					
					if(this.CODE == "amazon_account_lists"){
						item.children = accounts ;
					}
					
					if(this.CODE == "amazon_product_categorys"){
						item.children = accountsCategory ;
					}
					
					cacheItem.push(item) ;
					
					/*
					if( !this.PARENT_ID ){
						indexdata.push(item) ;
					}else{
						var pItem = map["P"+this.PARENT_ID] ;
						pItem.children = pItem.children||[] ;
						pItem.children.push(item) ;
					}*/
					
				}) ;
				
				$(cacheItem).each(function(){
					if( !this.pid ){
						indexdata.push(this) ;
					}else{
						var pItem = map["P"+this.pid] ;
						pItem.children = pItem.children||[] ;
						pItem.children.push(this) ;
					}
				}) ;
				
				$.ajax({
					type:"get",
					url:"/saleProduct/index.php/grid/scriptitem",
					data:{start:0,limit:100},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						
						if(typeof result == 'string'){
							eval("result = " +result) ;
						}
						var formatDate = window.formatGridData||function(data){ return data } ;
						result = formatDate(result) ;
		
						if(scripts){
							$(result.records).each(function(){
								scripts.children.push({isexpand:false,text:this.NAME,url:"/saleProduct/index.php/product/rule/"+this.ID}) ;
							}) ;
						}
		
						 //树
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
							if (!tabid)
							{
							    tabid = new Date().getTime();
							    $(node.target).attr("tabid", tabid)
							} 
							f_addTab(tabid, node.data.text, node.data.url);
						    }
						});
					}
				}); 
				
			}
        }) ;
	
		
            var tab = null;
            var accordion = null;
            var tree = null;
            $(function ()
            {

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
        <div position="left"  title="主要菜单" id="accordion1"> 
                     <div title="功能列表" class="l-scroll">
                         <ul id="tree1" style="margin-top:3px;">
                    </div>
		    <!--
                    <div title="应用场景">
                    <div style=" height:7px;"></div>
                         <a class="l-link" href="javascript:f_addTab('listpage','列表页面','demos/case/listpage.htm')">列表页面</a> 
                         <a class="l-link" href="demos/dialog/win7.htm" target="_blank">模拟Window桌面</a> 
                    </div>    
                     <div title="实验室">
                    <div style=" height:7px;"></div>
                          <a class="l-link" href="lab/generate/index.htm" target="_blank">表格表单设计器</a> 
                    </div> 
		    -->
        </div>
        <div position="center" id="framecenter"> 
	   
            <div tabid="home" title="我的主页" style="height:300px" >
                <iframe frameborder="0" name="home" id="home" src="/saleProduct/index.php/product/index"></iframe>
            </div> 
        </div> 
        
    </div>
    <div  style="height:32px; line-height:32px; text-align:center;">
           双橙科技
    </div>
    <div style="display:none"></div>
</body>
</html>
