$(function(){
		$(".grid-content").llygrid({
			columns:[
				{align:"center",key:"ID",label:"编辑",width:"5%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='edit' val='"+val+"'>编辑</a>&nbsp;&nbsp;") ;
					return html.join("") ;
				}},
				{align:"center",key:"ID",label:"状态",width:"10%",format:function(val,record){
					var status = record.STATUS ;
					var html = [] ;
					if(!status){//未收货
						html.push("等待收货&nbsp;<a href='#' class='action sh btn' val='"+val+"' status=1>收货</a>&nbsp;&nbsp;") ;
					}else if(status == 1){//收货完成//为入库
						html.push("收获完成&nbsp;<a href='#' class='action rk btn' val='"+val+"' status=2>入库</a>&nbsp;&nbsp;") ;
					}else if(status == 2){//收货完成//为入库
						html.push("入库完成&nbsp;<a href='#' class='action sj btn' val='"+val+"' status=3>上架</a>&nbsp;&nbsp;") ;
					}else if(status == 3){//收货完成//为入库
						html.push("上架完成") ;
					}
					
					return html.join("") ;
				}},
				{align:"center",key:"IN_NUMBER",label:"入库号",width:"8%",forzen:false,align:"left"},
	           	{align:"center",key:"CHARGER_NAME",label:"负责人",width:"6%",forzen:false,align:"left"},
	           	{align:"center",key:"WAREHOUSE_NAME",label:"目标仓库",width:"15%"},
	           	{align:"center",key:"SHIP_COMPANY",label:"运输公司",width:"15%"},
	           	{align:"center",key:"SHIP_TYPE",label:"运输方式",width:"10%"},
	           	{align:"center",key:"SHIP_NO",label:"运单号",width:"10%"},
	           	{align:"center",key:"SHIP_TRACKNUMBER",label:"物流跟踪号",width:"10%"},
	           	{align:"center",key:"SHIP_DATE",label:"发货时间",width:"15%"},
	           	{align:"center",key:"ARRIVAL_PORT",label:"到达港口",width:"15%"},
	           	{align:"center",key:"PLAN_ARRIVAL_DATE",label:"预计到达日期",width:"10%"},
	           	{align:"center",key:"REAL_ARRIVAL_DATE",label:"实际到达日期",width:"10%"},
	           	{align:"center",key:"MEMO",label:"备注",width:"10%"}
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
			 limit:10,
			 pageSizes:[10,20,30,40],
			 height:function(){
			 	return $(window).height() - 150 ;
			 },
			 title:"入库计划列表",
			 indexColumn:false,
			 querys:{sqlId:"sql_warehouse_in_lists"},
			 loadMsg:"数据加载中，请稍候......"
		}) ;
		
		$(".add-btn").click(function(){
			openCenterWindow("/saleProduct/index.php/page/model/Warehouse.In.edit",760,530) ;
		}) ;
		
		$(".action").live("click",function(){
			var record = $.llygrid.getRecord(this) ;
			var id = record.ID ;
			if( $(this).hasClass("sh") ){//收货
				openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.In.acceptBox/"+id,860,630) ;
			}else if( $(this).hasClass("rk") ){//入库
				openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.In.storage/"+id,860,630) ;
			}else if( $(this).hasClass("sj") ){//上架
				openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.In.shelves/"+id,860,630) ;
			}
		})
		
		
		
		$(".edit").live("click",function(){
			var val = $(this).attr("val") ;
			openCenterWindow("/saleProduct/index.php/page/model/Warehouse.In.editTab/"+val,760,590) ;
			return false;
		}) ;
	
 });
 
 function openCallback(){
 	$(".grid-content").llygrid("reload") ;
 }

