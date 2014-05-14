$(function(){
	var  amazonSiteMap = {
			us:"www.amazon.com",
			uk:"www.amazon.co.uk",
			ca:"www.amazon.ca",
			ru:"www.amazon.ru",
			de:"www.amazon.de",
			fr:"www.amazon.fr",
			es:"www.amazon.es",
			it:"www.amazon.it",
			br:"www.amazon.br",
			au:"www.amazon.com.au",
			"us.bing":"www.amazon.com"
	};
	
	function loadStatics(){
		$.dataservice("sqlId:sql_keyword_nicheKey_statics",{},function(result){
			 //$(".grid-content-details").llygrid("reload",{},true) ;
			$(".flow-node").find(".count").html("(0)") ;
			var count = 0 ;
			var map = {} ;
			$(result).each(function(index,item){
				item = item.t ;
				map[item.STATUS+"_"] = parseInt(item.COUNT) ;
				count +=parseInt(item.COUNT) ;
			}) ;
			//alert( $.json.encode(map) ) ;
			$(".flow-node").each(function(){
				var status = $(this).attr("status") ;
				var ss = (status+"").split(",") ;
				var c = 0 ;
				$(ss).each(function(index,item){
					if(!item)return ;
					c = (map[item+"_"]||0) +c;
				}) ;
				$(".flow-node[status='"+status+"']").find(".count").html("("+c+")") ;
			}) ;
			
			$(".total").find(".count").html("("+count+")") ;

			setTimeout(function(){
				loadStatics() ;
			},5000) ;
			
		 },{noblock:true});
		
	}
	
	loadStatics() ;
	
	function loadTree(){
		$('#default-tree').tree({//tree为容器ID
			//source:'array',
			//data:treeData ,
			rootId  : 'root',
			rootText : '产品分类',
			expandLevel:2,
			asyn:false,
			CommandName : 'sqlId:sql_productDev_new_categorytree',
			recordFormat:true,
			dataFormat:function(data){
				//data.push({id:'uncategory',text:'未分类产品',memo:'',isExpand:true});
				return data;
			},
			nodeFormat:function(record){
				if(record.id=='root' ||record.id == 'uncategory') return record ;
				record.text = record.text+"("+record.TOTAL+")";
				return record ;
			},
			params : {
				//accountId: accountId
			},
			onNodeClick:function(id,text,record){
				var uncategory = "" ;
				if(id == 'uncategory'){
					id="" ;
					uncategory = 1 ;
				}else{
					uncategory = "" ;
				}
				
				if( id == 'root' ){
					$(".grid-content-details").llygrid("reload",{categoryId:"",uncategory:uncategory,"untag":1}) ;
				}else{
					$(".grid-content-details").llygrid("reload",{categoryId:id,uncategory:uncategory,"untag":1}) ;
				}
			}
       }) ;
	}
	
	loadTree() ;
	
	$(".flow-node").click(function(){
		var status = $(this).attr("status");
		$(".flow-node").removeClass("active").addClass("disabled");
		$(this).removeClass("disabled").addClass("active");
		$(".niche-grid").llygrid("reload",{status:status,"untag":1});
	}) ;
	
	$(".niche-grid").llygrid({
		columns:[
				{align:"center",key:"keyword_id",label:"操作", width:"10%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='action niche-update' val='"+val+"'>设置</a>&nbsp;") ;
					return html.join("") ;
				}},
				{align:"left",key:"keyword",label:"关键字名称", width:"20%",format:function(val,record){
					var site = record.site||"us" ;
					var amazonUrl = amazonSiteMap[site] ;
					return "<a href='http://"+amazonUrl+"/s/ref=nb_sb_noss?field-keywords="+val+"' target='_blank'>"+val+"</a>" ;
				}},
				{align:"left",key:"status",label:"状态", width:"8%",format:function(val , record){
	
					if( !val ) return "开发中" ;
					if( val==10 ) return "开发中" ;
					if( val==20 ) return "待审批" ;
					if( val==30 ) return "产品开发中" ;
					if( val==50 ) return "结束" ;
					if( val==15 ) return "废弃" ;
				}},
				{align:"left",key:"dev_charger_name",label:"开发负责人", width:"15%"},
				{align:"left",key:"keyword_type",label:"关键字类型", width:"10%"},
				{align:"center",key:"search_volume",label:"搜索量", width:"5%"},
				
				{align:"left",key:"cpc",label:"CPC",width:"5%",forzen:false,align:"left"},
				{align:"left",key:"competition",label:"竞争",width:"5%"},
				{align:"left",key:"site",label:"国家",width:"5%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 170 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{_data :"d_list_niche_keyword",status:''},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	$(".niche-update").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Keyword.nicheDev/"+record.keyword_id,1000,650,function(win,ret){
			if(ret)$(".niche-grid").llygrid("reload",{},true) ;
		}) ;
	}) ;
}) ;