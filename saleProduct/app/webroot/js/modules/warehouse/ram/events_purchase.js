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
				//loadCount() ;
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
			{key:"IMAGE_URL",label:"",width:"4%",forzen:false,align:"center",format:{type:"img"}},
			{key:"STATUS",label:"状态",width:"5%",forzen:false,align:"left",format:{type:"json",content:{'10':"编辑中",20:"待审批",30:"退货标签确认",40:"退货确认",50:"退货入库",60:"退款",70:"重发配置",75:"重发确认",78:"重发收货确认",79:"Feedback",80:"结束"}}},
           	{key:"CAUSE_NAME",label:"原因",width:"13%",align:"left"},
           	{key:"POLICY_NAME",label:"决策",width:"10%",align:"left"},
			{key:"CODE",label:"RMA编号",width:"14%",forzen:false,align:"center"},
			//http://localhost/saleProduct/index.php/page/forward/Purchase.edit_purchase_product/4EFCF5E9-AFB6-F5BB-AF5B-A5A5022C8643
			{key:"PURCHASE_CODE",label:"采购编号",width:"14%",forzen:false,align:"center",format:function(val,record){
           		return "<a href='#' purchase-product='"+record.PURCHASE_ID+"'>"+(val||"")+"</a>" ;
           	}},
			{key:"REAL_NAME",label:"货品名称",width:"14%",forzen:false,align:"center"},
			{align:"center",key:"REAL_SKU",label:"货品SKU",width:"8%",forzen:false,align:"left",format:function(val){
           		return "<a href='#' product-realsku='"+val+"'>"+(val||"")+"</a>" ;
           	}},
           	{key:"TRACK_MEMO",label:"最新轨迹",width:"10%",forzen:false,align:"left",format:function(val,record){
				if(!val) return "" ;
				if( val.indexOf(")") >0 ) return val.split(")")[1] ;
				return val ;
			}},
			{key:"TRACK_TIME",label:"更新时间",width:"10%",forzen:false,align:"left"},
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
		 	return $(window).height()-180 ;
		 },
		 title:"采购RAM事件列表",
		 indexColumn:false,
		 querys:{sqlId:"sql-rma-purchaes-list",status:'',status1:"1"},
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
		openCenterWindow(contextPath+"/page/forward/Warehouse.Ram.editPurchaseRma",900,330) ;
	}) ;
	
	$(".edit").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Warehouse.Ram.editPurchaseRma/"+record.ID,950,650) ;
		return false;
	}) ;
		
 });
 
 function openCallback(type){
 	$(".grid-content").llygrid("reload") ;
 }