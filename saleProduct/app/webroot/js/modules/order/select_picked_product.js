
$(function(){
	
	var sqlId = "sql_order_list_picked_forselect" ;
	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"ORDER_ID",label:"操作",width:"6%",render:function(record){
					if(record.IS_PACKAGE || record.C > 1){
						$(this).find("td").css("background","#EEBBFF") ;
					}
				}
				,format:{type:"checkbox",render:function(record){
					if(record.checked >=1){
						$(this).attr("checked",true) ;
					}
			}}},
			 //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
			/*{align:"center",key:"AUDIT_STATUS",label:"状态",sort:true, width:"8%",format:function(val,record){
				var pickStatus = record.PICK_STATUS ;
				if(pickStatus == 9){
					return "拣货中" ;
				}else if(pickStatus == 10){
					return "完成出仓" ;
				}else if(pickStatus == 11){
					return "拣货单异常" ;
				}else if(pickStatus == 12){
					return "完成拣货" ;
				}
				
				var map = {0:"未审核",5:"合格订单",2:"风险订单",3:"待退单",4:"外购订单",6:"加急单",7:"特殊单"} ;
				return map[val] ;
			}},*/
			{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
	           		var memo = record.MEMO||"" ;
	           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
	        }},
	        {align:"left",key:"ORDER_NUMBER",label:"内部订单号", width:"10%"},
           	{align:"left",key:"REAL_SKU",label:"货品SKU", width:"10%"},
	        {align:"left",key:"NAME",label:"货品名称", width:"10%"},
           	{align:"center",key:"IMAGE_URL",label:"货品图片",width:"4%",format:function(val,record){
           		if(val){
           			val = val.replace(/%/g,'%25') ;
           		}else{
           			return "" ;
           		}
           		return "<img src='/"+fileContextPath+"/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
           	}},
           	{align:"center",key:"ORDER_ID",label:"ORDER_ID", width:"15%"},
           	{align:"center",key:"ORDER_ITEM_ID",label:"ORDER_ITEM_ID", width:"12%"},
           	{align:"center",key:"SKU",label:"SKU",sort:true, width:"10%"},
           	{align:"center",key:"PRODUCT_NAME",label:"PRODUCT_NAME", width:"20%"},
           	{align:"center",key:"PURCHASE_DATE",label:"PURCHASE_DATE",sort:true, width:"20%"},
           	{align:"center",key:"PAYMENTS_DATE",label:"PAYMENTS_DATE",sort:true, width:"20%"},
           	{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
           	{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"},
           	{align:"center",key:"BUYER_PHONE_NUMBER",label:"BUYER_PHONE_NUMBER", width:"10%"},
           	{align:"center",key:"QUANTITY_PURCHASED",label:"QUANTITY_PURCHASED", width:"10%"},
           	{align:"center",key:"CURRENCY",label:"CURRENCY", width:"10%"},
           	{align:"center",key:"ITEM_PRICE",label:"ITEM_PRICE", width:"10%"},
           	{align:"center",key:"ITEM_TAX",label:"ITEM_TAX", width:"10%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query/"+accountId},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - $(".toolbar-auto").height() -155 ;
		 },
		 title:"订单信息列表",
		 indexColumn:false,
		 querys:{sqlId:sqlId,accountId:accountId,status:'',pickStatus:'9',pickId:pickedId},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	
	$(".action-btn").click(function(){
		var action = $(this).attr("action");
		var checkedRecords = $(".grid-content").llygrid("getSelectedRecords",{key:"ORDER_ID",checked:true},true) ;
		var status = $(this).attr("status");
		
		if( action == 4 ){//打印拣货单
			alert("合并打印拣货单");
			openCenterWindow(contextPath+"/order/printPicked/"+pickedId,950,650) ;
			return ;
		}
	
		var orders = [] ;
		$(checkedRecords).each(function(index,item){
			orders.push(item.ORDER_ID+"|"+item.ORDER_ITEM_ID) ;
		}) ;
			
		if( orders.length <=0 ){
			alert("未选中任意订单！");
			return ;
		}	
		
		var text = $.trim( $(this).text() ) ;
		
		var msgs = {
			1:"确认将选择订单添加到拣货单中吗？",
			2:"确认将选择订单移除出拣货单吗？",
			3:"确认完成拣货吗？"
		} ;
		
		var msg = msgs[action] ;
		
		if( window.confirm(msg) ){
			$.ajax({
				type:"post",
				url:contextPath+"/order/savePickedOrder/"+pickedId ,
				data:{status:status,orders:orders.join(","),memo:$("#memo").val(),action:action},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					alert("保存成功!");
					window.location.reload();
				}
			});
		}
		
		
	}) ;
	
	$(".query").click(function(){
		var json = $(".query-table").toJson() ;
		$(".grid-content").llygrid("reload",json,true) ;
		}) ;
 });
 
 var currentQueryKey = "" ;
 //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
 $(function(){
		var tab = $('#details_tab').tabs( {
		tabs:[
			{label:'拣货单订单',content:"tab-content"},
			{label:'合格订单',content:"tab-content"},
			{label:'加急单',content:"tab-content"},
			{label:'特殊单',content:"tab-content"},
			{label:'多品订单',content:"tab-content"},
			{label:'RMA重发单<font color="red">*</font>',content:"tab-content"}
		] ,
		//height:'500px',
		select:function(event,ui){
			var index = ui.index ;
			$(".save-btn").show().html("添加到拣货单").removeClass("btn-danger").addClass("btn-success").attr("action","1");
			$(".pick-btn").hide() ;
			if(index == 0){//拣货单订单
				$(".pick-btn").show() ;
				$(".save-btn").html("移除出拣货单").removeClass("btn-success").addClass("btn-danger").attr("action","2");
				$(".grid-content").llygrid("reload",{pickStatus:9,status:'',pickId:pickedId,rmaValue:'',unRmaValue:''},true) ;
			}else if(index == 1){//合格订单
				$(".grid-content").llygrid("reload",{pickStatus:'',status:5,pickId:'',rmaValue:'',unRmaValue:'1'
						,sqlId:"sql_order_list_picked_forselect"},true) ;
			}else if(index == 2){//加急单
				$(".grid-content").llygrid("reload",{pickStatus:'',status:6,pickId:'',rmaValue:'',unRmaValue:'1'
						,sqlId:"sql_order_list_picked_forselect"},true) ;
			}else if(index == 3){//特殊但
				$(".grid-content").llygrid("reload",{pickStatus:'',status:7,pickId:'',rmaValue:'',unRmaValue:'1'
						,sqlId:"sql_order_list_picked_forselect"},true) ;
			}else if(index == 4){//特殊但
				$(".grid-content").llygrid("reload",{pickStatus:'',status:7,pickId:'',rmaValue:'',unRmaValue:'1'
						,sqlId:"sql_order_list_picked_forselect.many"},true) ;
			}else if(index == 5){//特殊但
				$(".grid-content").llygrid("reload",{pickStatus:'',status:'',pickId:'',rmaValue:'10',unRmaValue:''
						,sqlId:"sql_order_list_picked_forselect"},true) ;
			}
		}
	} ) ;
}) ;
   	 
