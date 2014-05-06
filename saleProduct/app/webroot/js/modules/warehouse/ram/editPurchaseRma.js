$(function(){
		var isAdd = false ;
		
		$(".save-score").click(function(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				if(window.confirm("确认保存评分吗，保存后将不能修改？")){
					var json = $("#personForm").toJson() ;
					//保存基本信息
					$.dataservice("model:Warehouse.Ram.doSaveScore",json,function(result){
							window.location.reload();
					});
				}
			}
		}) ;
		
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
		
		$(".btn-delete").click(function(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				if(window.confirm("确认删除RMA事件吗?")){
					
					var json = $("#personForm").toJson() ;
					
					//保存基本信息
					$.dataservice("model:Warehouse.Ram.deleteEvent",json,function(result){
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
		
		
		/*
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
		}) ;*/
		
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
			//检查该RMA是否处理完成
			var refundStatus = $("[name='refundStatus']") ; //退款
			var isReceive = $("[name='isReceive']") ; //退货
			var inStatus = $("#_inStatus") ; //RMA入库状态
			var reSend  = $("#_reSend") ; //重发货状态
			if( isReceive.length  ){
				if( $("[name='isReceive']:checked").val() !=1){
					alert("退货未完成！") ;
					return ;
				}
			}
			
			if( refundStatus.length  ){
				if( $("[name='refundStatus']:checked").val() !=1){
					alert("退款未完成！") ;
					return ;
				}
			}
			
			if( inStatus.length  ){
				if( inStatus.val() !=1){
					alert("退货未入库！") ;
					return ;
				}
				alert(  $("#_inStatus").val() ) ;
			}
			if( reSend.length  ){
				if( reSend.val() !=1){
					alert("重发货未设置！") ;
					return ;
				}
			}

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
			var json = $("#personForm").toJson() ;
			var orderId = json.orderId ;
			var ramId = json.id ;
			openCenterWindow(contextPath+"/page/forward/Warehouse.Ram.addRma/"+orderId+"/"+ramId,830,600) ;
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
					sqlId:"sql_sc_order_list",
					status:"shipped"
				},
				ds:{type:"url",content:contextPath+"/grid/query"},
				pagesize:10,
				columns:[//显示列
					{align:"center",key:"ORDER_ID",label:"订单编号",width:"150",query:true},
					{align:"center",key:"ORDER_NUMBER",label:"内部订单号",sort:true,width:"150",query:true},
					{align:"left",key:"ACCOUNT_NAME",label:"账号", width:"8%"},
					{align:"left",key:"ORDER_PRODUCTS",label:"订单货品", width:"10%",format:function(val,record){
			      		val = val||"" ;
			      		var html = [] ;
			      		$( val.split(";") ).each(function(index,item){
			      			var array = item.split("|") ;
			      			item&& html.push("<img src='/"+fileContextPath+""+array[0]+"' style='width:25px;height:25px;'>") ;
			      		})  ;
			      		return html.join("") ;
			      	}}
				]
			}
	    } ;
	    
	    window.renderProductImg = function(val,record){
       		if(val){
       			val = val.replace(/%/g,'%25') ;
       			return "<img src='/"+fileContextPath+"/"+val+"' style='width:30px;height:30px;'>" ;
       		}
       		return "" ;
       	}
	   
		$(".btn-order").listselectdialog( orderGridSelect ) ;
		if( $("#id").val() ){
			var tab = $('#tabs-default').tabs( {//$this->layout="index";
				tabs:[
					{label:'轨迹',content:"tab-track"},
					{label:'RMA入库',content:"tab-rma"}
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
		         ds:{type:"url",content:contextPath+"/grid/query"},
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
				 },loadAfter:function(){
					 
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
		           			return "<img src='/"+fileContextPath+"/"+val+"' style='width:30px;height:30px;'>" ;
		           		}
		           		return "" ;
		           	}},
		           	{align:"center",key:"IMAGE",label:"残品图片",width:"50",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/"+fileContextPath+"/"+val+"' style='width:30px;height:30px;'>" ;
		           		}
		           		return "" ;
		           	}},
		           	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"130" },
		           	{align:"center",key:"MEMO",label:"备注",width:"250"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
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