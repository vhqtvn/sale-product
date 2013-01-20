$(function(){
		var isAdd = false ;

		$(".btn-save").click(function(){
			if(isAdd)return ;
			if( !$.validation.validate('#personForm').errorInfo ) {
				if(window.confirm("确认保存吗?")){
					isAdd = true ;
					
					var json = $("#personForm").toJson() ;
					json.status = json.status||0 ;
					
					//保存基本信息
					$.dataservice("model:Warehouse.Ram.doSaveEvent",json,function(result){
							window.opener.openCallback('editPlan') ;
							window.close();
					});
				}
			};
			
			return false ;
		}) ;
		
		$(".btn-save-audit").click(function(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				if(window.confirm("确认保存并提交审批吗?")){
					
					var json = $("#personForm").toJson() ;
					json.status = 1 ;//等待shengpi
					
					//保存基本信息
					$.dataservice("model:Warehouse.Ram.doSaveAndAuditEvent",json,function(result){
							window.opener.openCallback('editPlan') ;
							window.close();
					});
				}
			};
			
			return false ;
		}) ;
		
		//审计通过
		$(".btn-aduitPass").click(function(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				if(window.confirm("确认审批痛过吗?")){
					
					var json = $("#personForm").toJson() ;
					
					//保存基本信息
					$.dataservice("model:Warehouse.Ram.doAuditPass",json,function(result){
							window.opener.openCallback('editPlan') ;
							window.close();
					});
				}
			};
			
			return false ;
		}) ;
		
		$(".btn-aduitNotPass").click(function(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				var json = $("#personForm").toJson() ;
				if(!json.trackMemo){
					alert("请填写审批意见！") ;
					return ;
				}
				
				if(window.confirm("确认审批不通过吗?")){
					
					
					//保存基本信息
					$.dataservice("model:Warehouse.Ram.doAuditNotPass",json,function(result){
							window.opener.openCallback('editPlan') ;
							window.close();
					});
				}
			};
			return false ;
		}) ;
		
		$(".btn-save-track").click(function(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				var json = $("#personForm").toJson() ;
				if(!json.trackMemo){
					alert("请填写跟踪意见！") ;
					return ;
				}
				
				if(window.confirm("确认保存跟踪意见?")){
					//保存基本信息
					$.dataservice("model:Warehouse.Ram.doSaveTrack",json,function(result){
							window.opener.openCallback('editPlan') ;
							window.close();
					});
				}
			};
			return false ;
		}) ;
		
		
		
		$("[name='isReceive']").click(function(){
			
			var val = $(this).val() ;
			if( val==1 ){
				if(window.confirm("确认收到退货,编辑后将不能修改？")){
					$("[name='isReceive']").attr("disabled","disabled") ;
					$(".ram-in").removeClass("disabled").removeAttr("disabled") ;
					
					var json = $("#personForm").toJson() ;
					$.dataservice("model:Warehouse.Ram.doUpdateRecevie",json,function(result){
							//window.opener.openCallback('editPlan') ;
							//window.close();
					});
				}else{
					$("[name='isReceive'][value='0']").attr("checked",'checked');
					$(".ram-in").addClass("disabled").addClass("disabled") ;
				}
			}
		}) ;
		
		$("[name='refundStatus']").click(function(){
			var val = $(this).val() ;
			if( val==1 ){
				$(".refund-action").show();
			}else{
				$(".refund-action").hide();
			}
		}) ;
		
		
		$(".refundConfirm").click(function(){
			var json = $("#personForm").toJson() ;
			if(window.confirm("确认已经退款完成?")){
				$.dataservice("model:Warehouse.Ram.doRefundConfrim",json,function(result){
						window.location.reload();
				});
			}
		}) ;
		
		$(".btn-finish").click(function(){
			var json = $("#personForm").toJson() ;
			if(window.confirm("确认该RMA事件已经处理完成?")){
				$.dataservice("model:Warehouse.Ram.doFinish",json,function(result){
						window.location.reload();
				});
			}
		}) ;
		
		//
		var json = $("#personForm").toJson() ;
		if(json.isReceive == 1){
			$(".ram-in").removeClass("disabled").removeAttr("disabled") ;
		}
		
		$(".ram-in").click(function(){
			var isComplete = $(this).hasClass("complete") ;
			if(isComplete){
				if(window.confirm("确认已经完成入库？")){
					var json = $("#personForm").toJson() ;
					$.dataservice("model:Warehouse.Ram.doCompleteRecevie",json,function(result){
						//	window.location.reload();
					});
				}
				
				return false;
			}
			
			var json = $("#personForm").toJson() ;
			var orderId = json.orderId ;
			var ramId = json.id ;
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Ram.addRma/"+orderId+"/"+ramId,830,600) ;
		}) ;
		
		$(".btn-dangerUser").click(function(){
			var email = $(this).attr("email");
			if( window.confirm("确认该用户加入风险客户?") ){
				$.dataservice("model:Warehouse.Ram.doDangerUser",{email:email},function(result){
					window.location.reload();
				});
			}
		}) ;
		
		var orderGridSelect = {
			title:'订单选择',
			labelField:"#orderNo",
			valueField:"#orderId",
			key:{value:'ORDER_ID',label:'ORDER_NUMBER'},//对应value和label的key
			multi:false,
			grid:{
				title:"订单选择",
				params:{
					sqlId:"sql_order_list"
				},
				ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				pagesize:10,
				columns:[//显示列
					{align:"center",key:"ORDER_ID",label:"订单编号",width:"150",query:true},
					{align:"center",key:"ORDER_NUMBER",label:"内部订单号",sort:true,width:"150",query:true},
					{align:"center",key:"PRODUCT_NAME",label:"产品名称",sort:true,width:"150"},
					{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:{type:'func',funcName:"window.opener.renderProductImg"}},
					{align:"center",key:"REAL_SKU",label:"货品SKU",sort:true,width:"50"}
				]
			}
	    } ;
	    
	    window.renderProductImg = function(val,record){
       		if(val){
       			val = val.replace(/%/g,'%25') ;
       			return "<img src='/saleProduct/"+val+"' style='width:30px;height:30px;'>" ;
       		}
       		return "" ;
       	}
	   
		$(".btn-order").listselectdialog( orderGridSelect ) ;
		if( $("#id").val() ){
			var tab = $('#tabs-default').tabs( {//$this->layout="index";
				tabs:[
					{label:'轨迹',content:"tab-track"},
					{label:'RMA入库',content:"tab-rma"},
					{label:'关联RMA',content:"tab-rma-rel"}
				] ,
				select:function(event,ui){
					var index = ui.index ;
					if(index == 0){
						$(".grid-content-track").llygrid("reload") ;
					}else if(index==1){
						$(".grid-content-rma").llygrid("reload") ;
					}else if(index==2){
						$(".grid-content-rma-rel").llygrid("reload") ;
					}
				}
			} ) ;
			
			$(".grid-content-track").llygrid({
				columns:[
					{key:"MEMO",label:"备注",width:"500",align:"left" },
		           	{key:"CREATE_TIME",label:"时间",width:"150",align:"left"},
		           	{key:"CREATOR_NAME",label:"操作用户",width:"100",align:"left"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:10,
				 pageSizes:[5,10,20,30],
				 height:function(){
				 	return 130 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:"sql_ram_track_list",id:$("#id").val()},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
				 }
			}) ;
			
			$(".grid-content-rma").llygrid({
				columns:[
					{align:"center",key:"QUALITY",label:"货品质量",width:"60",forzen:false
		           		,format:{type:"json",content:{'good':"良品",'bad':"残品"}}},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"100"},
		           	{align:"center",key:"NAME",label:"货品名称",width:"150"},
		           	{align:"center",key:"QUANTITY",label:"数量",width:"50" },
		           	//{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
		           	{align:"center",key:"IMAGE_URL",label:"图片",width:"50",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/saleProduct/"+val+"' style='width:30px;height:30px;'>" ;
		           		}
		           		return "" ;
		           	}},
		           	{align:"center",key:"IMAGE",label:"残品图片",width:"50",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/saleProduct/"+val+"' style='width:30px;height:30px;'>" ;
		           		}
		           		return "" ;
		           	}},
		           	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"130" },
		           	{align:"center",key:"MEMO",label:"备注",width:"250"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:10,
				 pageSizes:[5,10,20,30],
				 height:function(){
				 	return 130 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:"sql_warehouse_rmaEdit_lists",rmaId:$("#id").val()},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
				 }
			}) ;
			
			$(".grid-content-rma-rel").llygrid({
				columns:[
					{key:"CODE",label:"编辑",width:"50",format:function(val,record){
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
					{key:"STATUS",label:"状态",width:"50",forzen:false,align:"center",format:{type:"json",content:{'0':"编辑中",1:"待审批",2:"审批完成",3:"处理完成"}}},
					{key:"CODE",label:"编号",width:"80",forzen:false,align:"center"},
					{key:"ORDER_ID",label:"订单ID",width:"100",forzen:false,align:"center"},
					{key:"ORDER_NO",label:"内部订单号",width:"100",forzen:false,align:"center"},
					{key:"REAL_SKU",label:"货品SKU",group:"货品",width:"80"},
					{key:"IMAGE_URL",label:"图片",group:"货品",width:"40",format:{type:'func',funcName:"renderGridImg"}},
		           	{key:"CAUSE_NAME",label:"原因",width:"100",align:"left"},
		           	{key:"POLICY_NAME",label:"决策",width:"100",align:"left"},
		           	{key:"MEMO",label:"备注",width:"100",align:"left"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:10,
				 pageSizes:[5,10,20,30],
				 height:function(){
				 	return 130 ;
				 },
				 title:"RAM事件列表",
				 indexColumn:false,
				 querys:{sqlId:"sql_ram_events_list_rel",orderId:($("#orderId").val()||"--"),id:$("#id").val()},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
				 	//$(".grid-content-active").llygrid("reload",{planId:record.ID});
				 }
			}) ;
		}
		
		///////////////////////////保存重发货配置
		$(".save-reship").click(function(){
			var result = [] ;
			$("[name='rmaReship']").each(function(){
				var me = $(this) ;
				var rmaReship = $(this).val() ;
				var orderId   = $(this).attr("orderId") ;
				var orderItemId = $(this).attr("orderItemId") ;
				var params = {rmaReship:rmaReship,orderId:orderId,orderItemId:orderItemId} ;
				result.push(params) ;
			}) ;
			$.dataservice("model:Warehouse.Ram.saveReship",{result:$.json.encode(result),resendStatus:0,id:$("#id").val()},function(result){
				window.location.reload();
			});
			return false ;
		}) ;
		
		$(".save-reship-finish").click(function(){
			if(window.confirm("确认重发货货品数量已经设置完成?")){
				var result = [] ;
				$("[name='rmaReship']").each(function(){
					var me = $(this) ;
					var rmaReship = $(this).val() ;
					var orderId   = $(this).attr("orderId") ;
					var orderItemId = $(this).attr("orderItemId") ;
					var params = {rmaReship:rmaReship,orderId:orderId,orderItemId:orderItemId} ;
					result.push(params) ;
				}) ;
				$.dataservice("model:Warehouse.Ram.saveReship",{
					result:$.json.encode(result),
					resendStatus:1,
					id:$("#id").val(),
					orderId:$("#orderId").val()
					},function(result){
					window.location.reload();
				});
				return false ;
			}
		}) ;
			
		window.openCallback = function(){
			$(".grid-content-rma").llygrid("reload");
		}
   }) ;