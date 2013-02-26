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
				{align:"center",key:"IN_NUMBER",label:"出库号",width:"12%",forzen:false,align:"left"},
	           	{align:"center",key:"CHARGER_NAME",label:"负责人",width:"6%",forzen:false,align:"left"},
	           	{align:"center",key:"WAREHOUSE_NAME",label:"出库目的地",width:"15%",format:function(val,record){
	           		return val || record.WAREHOUSE_ID;
	           	}},
	           	{align:"center",key:"SHIP_COMPANY",label:"运输公司",width:"15%"},
	           	{align:"center",key:"SHIP_TYPE",label:"运输方式",width:"10%"},
	           	{align:"center",key:"SHIP_NO",label:"运单号",width:"10%"},
	           	{align:"center",key:"SHIP_TRACKNUMBER",label:"物流跟踪号",width:"10%"},
	           	{align:"center",key:"SHIP_DATE",label:"出库时间",width:"15%"},
	           	{align:"center",key:"PLAN_ARRIVAL_DATE",label:"预计到达日期",width:"10%"},
	           	{align:"center",key:"REAL_ARRIVAL_DATE",label:"实际到达日期",width:"10%"},
	           	{align:"center",key:"MEMO",label:"备注",width:"10%"}
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
			 limit:10,
			 pageSizes:[10,20,30,40],
			 height:function(){
			 	return $(window).height() - 200 ;
			 },
			 title:"入库计划列表",
			 indexColumn:false,
			 querys:{sqlId:"sql_warehouse_out_lists",status:"0"},
			 loadMsg:"数据加载中，请稍候......"
		}) ;
		
		$(".query-btn").click(function(){
			var json = $(".query-table").toJson() ;
			//if(currentQueryKey)json.sqlId = currentQueryKey ;
			$(".grid-content").llygrid("reload",json,true) ;
		}) ;
		
		$(".add-btn").click(function(){
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Out.edit",990,640) ;
		}) ;
		
		$(".action").live("click",function(){
			var record = $.llygrid.getRecord(this) ;
			var id = record.ID ;
			var status = $(this).attr("status")||"";
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.In.process/"+id+"/"+status,860,630) ;
			return false;
			
		});
		
		$(".edit").live("click",function(){
			var val = $(this).attr("val") ;
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Out.editTab/"+val,990,640) ;
			return false;
		}) ;
		 
		var tab = $('#details_tab').tabs( {
			tabs:[
				{label:'编辑中',content:"tab-content",custom:"0"},
				{label:'待审批',content:"tab-content",custom:"100"},
				{label:'待出库',content:"tab-content",custom:"200"},
				{label:'已出库',content:"tab-content",custom:"300"},
				{label:'对方收货',content:"tab-content",custom:"400"}
			] ,
			//height:'500px',
			select:function(event,ui){
				var index = ui.index ;
				tabIndex = index ;
				renderAction(index);
			}
		} ) ; 
		
		function loadCount(){
			$.dataservice("model:Warehouse.In.loadStatusCount4Out",{},function(result){
			
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
				$(".grid-content").llygrid("reload",{status:100},true) ;
			}else if(index == 2){//待发货
				$(".grid-content").llygrid("reload",{status:'200'},true) ;
			}else if(index == 3){//已发货
				$(".grid-content").llygrid("reload",{status:300},true) ;
			}else if(index == 4){//到达海关
				$(".grid-content").llygrid("reload",{status:'400'},true) ;
			}
		}
	
 });
 
 function openCallback(){
 	$(".grid-content").llygrid("reload") ;
 }

