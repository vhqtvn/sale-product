$(function(){
	
	var tab = $('#tabs-default').tabs( {//$this->layout="index";
		tabs:[
			{label:'编辑中',content:"tab-container",custom:'0'},
			{label:'待审批',content:"tab-container",custom:'1'},
			{label:'审批完成',content:"tab-container",custom:'2'},
			{label:'处理完成',content:"tab-container",custom:'3'}
		] ,
		select:function(event,ui){
			var index = ui.index ;
			if(index == 0){
				$(".grid-content").llygrid("reload",{status:0}) ;
			}else if(index==1){
				$(".grid-content").llygrid("reload",{status:1}) ;
			}else if(index==2){
				$(".grid-content").llygrid("reload",{status:2}) ;
			}else if(index==3){
				$(".grid-content").llygrid("reload",{status:3}) ;
			}
		}
	} ) ;
	
	function loadCount(){
		$.dataservice("model:Warehouse.Ram.loadStatusCount",{},function(result){
		
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
	
	$(".grid-content").llygrid({
		columns:[
			{key:"CODE",label:"编辑",width:"5%",format:function(val,record){
				var status = record.STATUS ;
				
				if( status == 0 ){
					return "<a href='#' class='edit btn' val='"+val+"'>修改</a>&nbsp;&nbsp;" ;
				}else if(status == 1){
					return "<a href='#' class='edit btn' val='"+val+"'>审批</a>&nbsp;&nbsp;"
				}else if(status == 2){
					return "<a href='#' class='edit btn' val='"+val+"'>处理</a>&nbsp;&nbsp;"
				}else if(status == 3){
					return "<a href='#' class='edit btn' val='"+val+"'>查看</a>&nbsp;&nbsp;"
				}
				
			}},
			{key:"STATUS",label:"状态",width:"5%",forzen:false,align:"center",format:{type:"json",content:{'0':"编辑中",1:"待审批",2:"审批完成",3:"处理完成"}}},
			{key:"CODE",label:"编号",width:"14%",forzen:false,align:"center"},
			{key:"ORDER_ID",label:"订单ID",width:"14%",forzen:false,align:"center"},
			{key:"ORDER_NO",label:"内部订单号",width:"8%",forzen:false,align:"center"},
			{align:"left",key:"ORDER_PRODUCTS",label:"订单货品", width:"10%",format:function(val,record){
	      		val = val||"" ;
	      		var html = [] ;
	      		$( val.split(";") ).each(function(index,item){
	      			var array = item.split("|") ;
	      			item&& html.push("<img src='/"+fileContextPath+""+array[0]+"' style='width:25px;height:25px;'>") ;
	      		})  ;
	      		return html.join("") ;
	      	}},
           	{key:"CAUSE_NAME",label:"原因",width:"13%",align:"left"},
           	{key:"POLICY_NAME",label:"决策",width:"10%",align:"left"},
           	{key:"MEMO",label:"备注",width:"17%",align:"left"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[5,10,20,30],
		 height:function(){
		 	return $(window).height()-230 ;
		 },
		 title:"RAM事件列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_ram_events_list",status:0},
		 loadMsg:"数据加载中，请稍候......",
		 rowClick:function(row,record){
		 	//$(".grid-content-active").llygrid("reload",{planId:record.ID});
		 }
	}) ;
	
	$(".query-btn").click(function(){
		var json = $(".toolbar-auto").toJson() ;
		$(".grid-content").llygrid("reload",json,true) ;
	})

	//添加选项
	$(".add-btn").click(function(){
		openCenterWindow(contextPath+"/page/forward/Warehouse.Ram.editEvent",880,330) ;
	}) ;
	
	$(".edit").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Warehouse.Ram.editEvent/"+record.ID,880,650) ;
		return false;
	}) ;
		
 });
 
 function openCallback(type){
 	$(".grid-content").llygrid("reload") ;
 }

