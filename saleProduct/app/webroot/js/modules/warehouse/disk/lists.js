$(function(){
		$(".grid-content").llygrid({
			columns:[
				{align:"center",key:"STATUS",label:"状态",width:"6%",format:function(val,record){
					if(!val || val=='0') return "初始化" ;
					if(!val || val=='2') return "盘点结束" ;
					return val;
				}},
				{align:"center",key:"ID",label:"编辑",width:"8%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='edit btn' val='"+val+"'>盘点明细</a>&nbsp;&nbsp;") ;
					return html.join("") ;
				}},
				{align:"center",key:"DISK_NO",label:"单号",width:"10%",forzen:false,align:"left"},
	           	{align:"center",key:"DISK_TIME",label:"时间",width:"13%",forzen:false,align:"left"},
	           	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"13%"},
	           	{align:"center",key:"PROCESSOR",label:"经办人",width:"8%"},
	           	{align:"center",key:"SHIP_COMPANY",label:"盘盈货品",width:"15%"},
	           	{align:"center",key:"SHIP_COMPANY",label:"盘亏货品",width:"15%"},
	           	{align:"center",key:"MEMO",label:"备注",width:"10%"}
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
			 limit:10,
			 pageSizes:[5,10,20,30],
			 height:function(){
			 	return $(window).height() - 200 ;
			 },
			 title:"盘仓计划列表",
			 indexColumn:false,
			 querys:{sqlId:"sql_warehouse_disk_lists"},
			 loadMsg:"数据加载中，请稍候......"
		}) ;
		
		$(".add-btn").click(function(){
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Disk.edit",760,530) ;
		}) ;
		
		$(".edit").live("click",function(){
			var val = $(this).attr("val") ;
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Disk.edit/"+val,860,650) ;
			return false;
		}) ;
	
 });
 
 function openCallback(){
 	$(".grid-content").llygrid("reload") ;
 }

