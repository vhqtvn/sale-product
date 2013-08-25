$(function(){

	$(".niche-grid").llygrid({
		columns:[
				{align:"center",key:"keyword_id",label:"操作", width:"10%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='action niche-update' val='"+val+"'>设置</a>&nbsp;") ;
				
					return html.join("") ;
				}},
				{align:"left",key:"keyword",label:"关键字名称", width:"15%"},
				{align:"left",key:"status",label:"状态", width:"8%",format:function(val , record){
				if( !val ) return "开发中" ;
				if( val==1 ) return "等待审批" ;
				if( val==2 ) return "审批通过" ;
				if( val==3 ) return "废弃" ;
				}},
				{align:"left",key:"dev_charger_name",label:"开发负责人", width:"15%"},
				{align:"left",key:"keyword_type",label:"关键字类型", width:"10%"},
				{align:"center",key:"search_volume",label:"搜索量", width:"5%"},
				
				{align:"left",key:"cpc",label:"CPC",width:"5%",forzen:false,align:"left"},
				{align:"left",key:"competition",label:"竞争",width:"5%"},
				{align:"left",key:"result_num",label:"结果数",width:"8%"},
				{align:"left",key:"trends",label:"趋势",width:"25%"} 
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 110 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{_data :"d_list_my_niche_keyword",status:'2'},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	$(".niche-update").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Keyword.nicheDevForAudit/"+record.keyword_id,800,550,function(win,ret){
			if(ret)$(".niche-grid").llygrid("reload",{},true) ;
		}) ;
	}) ;
}) ;