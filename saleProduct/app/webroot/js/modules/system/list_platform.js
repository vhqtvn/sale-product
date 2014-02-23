$(function() {
	$(".grid-content").llygrid({
		columns : [ 
			{align:"center",key:"ID",label:"操作",width:"5%",format:function(val,record){
				var html = [] ;
				html.push("<a href='#' class='edit-account' val='"+val+"'>修改</a>&nbsp;&nbsp;") ;
				return html.join("") ;
			}},
			{
				align : "center",
				key : "ID",
				label : "平台编号",
				width : "8%"
			}, {
				align : "center",
				key : "CODE",
				label : "平台编码",
				width : "10%"
			}, {
				align : "left",
				key : "NAME",
				label : "平台名称",
				width : "15%"
			} ,
			{align:"center",key:"EXCHANGTE_NAME",label:"货币",width:"4%"},
	    	{align:"center",key:"SUPPLY_CYCLE",label:"供应周期",width:"5%"},
	    	{align:"center",key:"FEE_RATIO",label:"区域税率",width:"8%"},
	    	{align:"center",key:"REQ_ADJUST",label:"需求调整系数",width:"8%"},
	    	{align:"center",key:"TRANSFER_WH_PRICE",label:"转仓成本单价",width:"8%"}
    	],
		ds : {
			type : "url",
			content : contextPath + "/grid/query/"
		},
		limit : 30,
		pageSizes : [ 10, 20, 30, 40 ],
		height : function() {
			return $(window).height() - 110
		},
		title : "平台列表",
		indexColumn : false,
		querys : {
			sqlId : "sql_platform_list"
		},
		loadMsg : "数据加载中，请稍候......"
	});
	
	$(".edit-account").live("click",function(){
		var val = $(this).attr("val") ;
		openCenterWindow(contextPath+"/page/forward/System.editPlatform/"+val,750,530,function(win,re){
			if(re){
				$(".grid-content").llygrid("reload",{},true) ;
			}
		}) ;
	}) ;

});