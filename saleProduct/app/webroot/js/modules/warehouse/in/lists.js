$(function(){
		
		$(".grid-content").llygrid({
			columns:[
				{align:"center",key:"ID",label:"编辑",width:"5%",format:function(val,record){
					if(record.STATUS < 10 ){
						return "<a href='#' class='edit' val='"+val+"'>编辑</a>&nbsp;&nbsp;" ;
					}else if(record.STATUS <70){
						return "<a href='#' class='edit' val='"+val+"'>处理</a>&nbsp;&nbsp;" ;
					}else{
						return "<a href='#' class='edit' val='"+val+"'>查看</a>&nbsp;&nbsp;" ;
					}
				}},
				{align:"center",key:"IN_NUMBER",label:"入库号",width:"12%",forzen:false,align:"left"},
	           	{align:"center",key:"CHARGER_NAME",label:"负责人",width:"6%",forzen:false,align:"left"},
	           	{align:"center",key:"IN_SOURCE_TYPE",label:"入库类型",width:"9%",forzen:false,align:"left",format:function(val,record){
	           		if(val == 'warehouse') return "转仓" ;
	           		return "外部采购入库" ;
	           	}},
	           	{align:"center",key:"FLOW_TYPE",label:"入库流程",width:"9%",forzen:false,align:"left",format:function(val,record){
	           		return FlowFactory.get(val,record.IN_SOURCE_TYPE).name||"" ;
	           	}},
	           	{align:"center",key:"WAREHOUSE_NAME",label:"目标仓库",width:"12%"},
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
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:10,
			 pageSizes:[10,20,30,40],
			 height:function(){
			 	return $(window).height() - 200 ;
			 },
			 title:"入库计划列表",
			 indexColumn:false,
			 querys:{sqlId:"sql_warehouse_in_lists",status:"0"},
			 loadMsg:"数据加载中，请稍候......"
		}) ;
		
		$(".add-btn").click(function(){
			openCenterWindow(contextPath+"/page/model/Warehouse.In.edit",990,640,function(){
				$(".grid-content").llygrid("reload") ;
			}) ;
		}) ;
		
		$(".action").live("click",function(){
			var record = $.llygrid.getRecord(this) ;
			var id = record.ID ;
			var status = $(this).attr("status")||"";
			openCenterWindow(contextPath+"/page/forward/Warehouse.In.process/"+id+"/"+status,860,630) ;
			return false;
			
		});
		
		$(".query-btn").click(function(){
			var json = $(".query-table").toJson() ;
			//if(currentQueryKey)json.sqlId = currentQueryKey ;
			$(".grid-content").llygrid("reload",json,true) ;
		}) ;
		
		$(".edit").live("click",function(){
			var val = $(this).attr("val") ;
			openCenterWindow(contextPath+"/page/model/Warehouse.In.editTab/"+val,990,640) ;
			return false;
		}) ;
		 
		var tab = $('#details_tab').tabs( {
			tabs:[
				{label:'编辑中',content:"tab-content",custom:"0"},
				{label:'待审批',content:"tab-content",custom:"10"},
				{label:'待发货',content:"tab-content",custom:"20"},
				{label:'已发货',content:"tab-content",custom:"30"},
				{label:'到达海关',content:"tab-content",custom:"40"},
				{label:'验货中',content:"tab-content",custom:"50"},
				{label:'入库中',content:"tab-content",custom:"60"},
				{label:'入库完成',content:"tab-content",custom:"70"}
			] ,
			//height:'500px',
			select:function(event,ui){
				var index = ui.index ;
				tabIndex = index ;
				renderAction(index);
			}
		} ) ; 
		
		function loadCount(){
			$.dataservice("model:Warehouse.In.loadStatusCount",{},function(result){
			
				$(result).each(function(){
					var item = {} ;
					for(var o in this){
						var _ = this[o] ;
						for(var o in _){
							item[o] = _[o] ;
						}
					}
					var el = $("[custom='"+item['STATUS']+"']") ;
					if(el.length){
						var cl = el.attr("customLabel");
						var content = cl+"("+item['C']+")" ;
						el.find("span").html(content) ;
					}
				}) ;
				
				setTimeout(function(){
					loadCount() ;
				},60000) ;
			});
		}
		loadCount() ;
	
		
		var tabIndex = 0 ;   	   	 
		function renderAction(index){
			$(".save-btn").show() ;
			if(index == 0){//编辑中
				$(".grid-content").llygrid("reload",{status:'0'},true) ;
			}else if(index == 1){//待审批
				$(".grid-content").llygrid("reload",{status:10},true) ;
			}else if(index == 2){//待发货
				$(".grid-content").llygrid("reload",{status:'20'},true) ;
			}else if(index == 3){//已发货
				$(".grid-content").llygrid("reload",{status:30},true) ;
			}else if(index == 4){//到达海关
				$(".grid-content").llygrid("reload",{status:'40'},true) ;
			}else if(index == 5){//验货中
				$(".grid-content").llygrid("reload",{status:50},true) ;
			}else if(index == 6){//验货中
				$(".grid-content").llygrid("reload",{status:60},true) ;
			}else if(index == 7){//入库完成
				$(".grid-content").llygrid("reload",{status:'70'},true) ;
			}
		}
	
 });
 
 function openCallback(){
 	$(".grid-content").llygrid("reload") ;
 }

