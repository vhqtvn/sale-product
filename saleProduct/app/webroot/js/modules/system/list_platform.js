$(function() {
	$(".grid-content").llygrid({
		columns : [ {
			align : "center",
			key : "ID",
			label : "平台编号",
			width : "8%"
		}, {
			align : "center",
			key : "CODE",
			label : "平台编码",
			width : "20%"
		}, {
			align : "left",
			key : "NAME",
			label : "平台名称",
			width : "35%"
		} ],
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

});