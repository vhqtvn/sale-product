$(function(){
	
	/*var tab = $('#tabs-default').tabs( {//$this->layout="index";
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
	} ) ;*/
	
	function loadCount(){
		$.dataservice("model:Warehouse.Ram.loadStatusCount",{},function(result){
			var map = {} ;
			$(result).each(function(){
				map[this['STATUS']] = this['C'] ;
			}) ;
			
			$(".flow-node").each(function(){
				var status = $(this).attr("status") ;
				var count = map[status]||'0' ;
				if( $(this).find(".count").length){
					$(this).find(".count").html("("+count+")") ;
				}else{
					$("<span class='count'>("+count+")</span>").appendTo(this) ;
				}
			}) ;

			setTimeout(function(){
				loadCount() ;
			},10000) ;
		});
	}
	loadCount() ;
	
	$(".flow-node").click(function(){
		$(".flow-node").addClass("disabled").removeClass("actived");
		$(this).removeClass("disabled").addClass("actived");
		var status1 = $(this).attr("status")==80?"":"1" ;
		$(".grid-content").llygrid("reload",{status: $(this).attr("status"),status1:status1 },true) ;
	}) ;
	
	$(".grid-content").llygrid({
		columns:[
			{key:"CODE",label:"编辑",width:"5%",format:function(val,record){
				var status = record.STATUS ;
				
				if( status == 80 ){
					return  getImage('icon-grid.gif','查看','edit  ')   ;
				}else{
					return getImage('pkg.gif','处理','edit  ')  ;
				}
				
			}},
			{key:"STATUS",label:"状态",width:"5%",forzen:false,align:"left",format:{type:"json",content:{'10':"编辑中",20:"待审批",30:"退货标签确认",40:"退货确认",50:"退货入库",60:"退款",70:"重发配置",75:"重发确认",78:"重发收货确认",79:"Feedback",80:"结束"}}},
			{key:"TRACK_MEMO",label:"最新轨迹",width:"10%",forzen:false,align:"left",format:function(val,record){
				if(!val) return "" ;
				if( val.indexOf(")") >0 ) return val.split(")")[1] ;
				return val ;
			}},
			{key:"TRACK_TIME",label:"更新时间",width:"10%",forzen:false,align:"left"},
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
           	{key:"USER_NAME",label:"创建用户",width:"6%",align:"left"},
           	{key:"PROPOSED_TIME",label:"提出时间",width:"10%",align:"left"},
           	{key:"END_TIME",label:"结束时间",width:"10%",align:"left",format:function(val,record){
           		return record.STATUS != 80 ?"":val;
           	}}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[5,10,20,30],
		 height:function(){
		 	return $(window).height()-230 ;
		 },
		 title:"RAM事件列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_ram_events_list",status:'',status1:"1"},
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
		openCenterWindow(contextPath+"/page/forward/Warehouse.Ram.editEvent",900,330) ;
	}) ;
	
	$(".edit").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Warehouse.Ram.editEvent/"+record.ID,950,650) ;
		return false;
	}) ;
		
 });
 
 function openCallback(type){
 	$(".grid-content").llygrid("reload") ;
 }

