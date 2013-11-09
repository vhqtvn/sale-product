$(function(){
	
	var sqlId = "sql_sc_order_list_picked_forselect" ;
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
			{align:"center",key:"ORDER_ID",label:"Order Id", width:"15%"},
	      	{align:"left",key:"ORDER_NUMBER",label:"内部订单号", width:"8%"},
			{align:"center",key:"SHIPPED_NUM",label:"发货数量", width:"6%"},
			{align:"center",key:"UNSHIPPED_NUM",label:"未发货数量", width:"6%"},
			{align:"center",key:"RMA_RESHIP",label:"待重发", width:"6%"},
			{align:"center",key:"AMOUNT",label:"金额", width:"4%" ,align:'right'},
			{align:"center",key:"PURCHASE_DATE",label:"Purchase Date",sort:true, width:"15%"},
	    	{align:"center",key:"LAST_UPDATE_DATE",label:"Last Update Date",sort:true, width:"15%"},
			{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
			{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"}
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
			 jQuery(document.body).block() ;
			$.ajax({
				type:"post",
				url:contextPath+"/order/savePickedOrder/"+pickedId ,
				data:{status:status,orders:orders.join(","),memo:$("#memo").val(),action:action},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					 jQuery(document.body).unblock() ;
					alert("保存成功!");
					window.location.reload();
				},error:function(){
					 jQuery(document.body).unblock() ;
					alert("保存异常!");
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
			{label:'RMA重发单',content:"tab-content"}
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
						,sqlId:"sql_sc_order_list_picked_forselect"},true) ;
			}else if(index == 2){//加急单
				$(".grid-content").llygrid("reload",{pickStatus:'',status:6,pickId:'',rmaValue:'',unRmaValue:'1'
						,sqlId:"sql_sc_order_list_picked_forselect"},true) ;
			}else if(index == 3){//特殊但
				$(".grid-content").llygrid("reload",{pickStatus:'',status:7,pickId:'',rmaValue:'',unRmaValue:'1'
						,sqlId:"sql_sc_order_list_picked_forselect"},true) ;
			}else if(index == 4){//特殊但
				$(".grid-content").llygrid("reload",{pickStatus:'',status:7,pickId:'',rmaValue:'',unRmaValue:'1'
						,sqlId:"sql_sc_order_list_picked_forselect.many"},true) ;
			}else if(index == 5){//特殊但
				$(".grid-content").llygrid("reload",{pickStatus:'',status:'',pickId:'',rmaValue:'10',unRmaValue:''
						,sqlId:"sql_sc_order_list_picked_forselect"},true) ;
			}
		}
	} ) ;
}) ;
   	 
