	$(function(){
			var sqlId = "sql_sc_order_doneList" ;//"sql_order_done_list" ;
		
		    $(".btn-startRma").live("click",function(){
		    	var orderId = $(this).attr("orderId") ;
		    	openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Ram.editEvent/"+orderId,830,480) ;
		    }) ;
		 
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ORDER_ID",label:"操作",width:"5%",render:function(record){
							if(record.IS_PACKAGE || record.C > 1){
								$(this).find("td").css("background","#EEBBFF") ;
							}
						}
						,format:function(val,record){
						return "<button class='btn btn-danger btn-startRma' orderId='"+val+"'>RMA</button>" ;
					}},
					{align:"center",key:"ORDER_ID",label:"Order Id", width:"15%"},
			      	{align:"left",key:"ORDER_NUMBER",label:"内部订单号", width:"8%"},
			    	{align:"left",key:"ORDER_PRODUCTS",label:"订单货品", width:"10%",format:function(val,record){
			      		val = val||"" ;
			      		var html = [] ;
			      		$( val.split(";") ).each(function(index,item){
			      			var array = item.split("|") ;
			      			item&& html.push("<img src='/saleProduct"+array[0]+"' style='width:25px;height:25px;'>") ;
			      		})  ;
			      		return html.join("") ;
			      	}},
					{align:"center",key:"SHIPPED_NUM",label:"发货数量", width:"6%"},
					{align:"center",key:"UNSHIPPED_NUM",label:"未发货数量", width:"6%"},
					{align:"center",key:"AMOUNT",label:"金额", width:"4%" ,align:'right'},
					{align:"center",key:"PURCHASE_DATE",label:"Purchase Date",sort:true, width:"15%"},
			    	{align:"center",key:"LAST_UPDATE_DATE",label:"Last Update Date",sort:true, width:"15%"},
					{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
					{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -155 ;
				 },
				 title:"订单信息列表",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId,status:'',unRedoStatus:1},
				 loadMsg:"数据加载中，请稍候......"
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
					{label:'完成订单',content:"tab-content"},
					{label:'RMA处理订单',content:"tab-content"}/*,
					{label:'待审批重发货订单',content:"tab-content"},
					{label:'待退货订单',content:"tab-content"},
					{label:'待退款订单',content:"tab-content"},
					{label:'待重发货订单',content:"tab-content"},
					{label:'售后订单',content:"tab-content"}*/
				] ,
				//height:'500px',
				select:function(event,ui){
					var index = ui.index ;
					renderAction(index);
					
				}
			} ) ;
		}) ;
 
var actionType = 0 ;		
function renderAction(index){
	actionType = index ;
	$(".save-btn").show() ;
	if(index == 0){//拣货中
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{rmaStatus:''}) ;
	}else if(index == 1){//RMA订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{rmaStatus:'1'}) ;
	}/*else if(index == 2){//待审批重发货订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:3,status:''}) ;
	}else if(index == 3){//退货订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:1,status:''}) ;
	}else if(index == 4){//退款订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:201,status:''}) ;
	}else if(index == 5){//重发货
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:301,status:''}) ;
	}else if(index == 6){//售后订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:"",status:'',serviceStatus:"doing"}) ;
	}*/
}