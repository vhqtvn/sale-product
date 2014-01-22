	$(function(){
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;
			
			var currentRealId = "" ;
			var currentPlanProduct = {} ;
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"动态", width:"6%",format:function(val,record){
						return "<span class='pi-status hide'  realId='"+val+"'  title=''>查看</span>" ;
					}},
					{align:"center",key:"P_STATUS",label:"状态", width:"10%",format:{type:'json',content:{'0':'待审批','1':'审批通过','2':'审批不通过','3':'采购中','4':'采购完成','5':'入库中','6':'需求完成'}}},
				 	{align:"center",key:"IMG_URL",label:"图片",width:"5%",format:{type:'img'}},
					{align:"center",key:"REAL_SKU",label:"SKU",width:"10%",sort:true},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left"},
		        	{align:"center",key:"FIX_QUANTITY",label:"周期需求量",width:"8%",forzen:false,align:"left"},
		           	{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
		          
		           	{align:"center",key:"MEMO",label:"备注",width:"25%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[20,10,20,30,40],
				 height:function(){
				 	return 200 ;
				 },
				 title:"需求货品列表",
				// autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sql_supplychain_requirement_plan_product_list",planId:planId},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
					 enableActionPanel(record.P_STATUS);
					 currentRealId = record.ID ;
					 currentPlanProduct = record ;
					 $(".grid-content-details").llygrid("reload",{realId:record.ID}) ;
					 $(".current-product").html("#"+record.REAL_SKU+"#") ;
				 },
				 loadAfter:function(records){
					 //重新点击行
					 if(currentRealId){
						 $(".grid-content").find(".lly-grid-row").each(function(){
							 var _record = $(this).data("record") ;
							 if(_record.ID == currentRealId){
								 currentPlanProduct = _record ;
								 $(this).click() ;
							 }
						 }) ;
					 }
					 
					 $(".grid-content").find('.audit').click(function(){
						 var record = $(this).closest("tr").data("record") ;
						 var fixQuatity = $(this).val() ;
						 var id = record.ID ;
						 $.dataservice("model:ScRequirement.saveItemFixQuantity" , {fixQuantity:fixQuatity,id:id} , function(){
							// $(".grid-content-details").llygrid("reload",{},true) ;
						 })
					 }) ;
					 
					 
					$realIds = [] ;
					$(records).each(function(){
						$realIds.push(this.ID) ;
					}) ;
					
					Business.getProductStatus($realIds.join(",") , function( map ){
						$(".pi-status").each(function(){
							var realId = $(this).attr("realId") ;
							var $title = map[realId] ;
							if( $title ){
								$(this).show().popover({trigger:'click',content: $title,delay:{hide:50},width:500}) ; 
								$(this).mouseenter(function(){
									var me = $(this) ;
									$(".pi-status").popover("hide") ;
									$(this).popover("show") ;
									$(".popover-inner").mouseleave(function(){
										me.popover("hide") ;
									}) ;
								}) ;
							}else{
								$(this).hide() ;
							}
						});
					}) ;
				}
			}) ;
			
			$(".grid-content-details").llygrid({
				columns:[
				    {align:"center",key:"IN_FLAG",label:"状态",width:"7%",format:function(val,record){
				    	if(val>=1)return "入库中";
				    	return "" ;
				    }},
				 	{align:"center",key:"ACCOUNT_NAME",label:"账号",width:"10%"},
		           	{align:"center",key:"LISTING_SKU",label:"Listing SKU",width:"20%",forzen:false,align:"left"},
		        	{align:"center",key:"FULFILLMENT_CHANNEL",label:"渠道",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"QUANTITY",label:"周期需求量",width:"10%",sort:true},
		           	{align:"center",key:"FIX_QUANTITY",label:"需求数量（修正）",width:"15%",format:function(val,record){
		           		if(currentPlanProduct.P_STATUS == 1 || currentPlanProduct.P_STATUS ==0 ){
		           			return "<input type='text' class='edit-fix-quantity'  value='"+(val||record.QUANTITY||"0")+"' style='width:100%;height:100%;padding:0px;border:none;'/>" ;
		           		}else{
		           			return val ;
		           		}
		           	}},
		           	{align:"center",key:"PURCHASE_QUANTITY",label:"待采购数量",width:"8%",format:function(val,record){
		           		if(currentPlanProduct.P_STATUS == 1 || currentPlanProduct.P_STATUS ==0 ){
		           			return "<input type='text' class='edit-purchase-quantity'  value='"+(val||"0")+"' style='width:100%;height:100%;padding:0px;border:none;'/>" ;
		           		}else{
		           			return val ;
		           		}
		           	}},
		           	{align:"center",key:"REAL_PURCHASE_QUANTITY",label:"实际入库",width:"8%"},
		           	{align:"center",key:"REQ_TYPE",label:"生成类别",width:"8%",format:{type:"json",content:{'A':"销量",'B':"流量",'C':"其他"}}},
		           	{align:"center",key:"URGENCY",label:"紧急程度",width:"8%",format:function(val,record){
		           		if(currentPlanProduct.P_STATUS == 1 || currentPlanProduct.P_STATUS ==0 ){
		           			return $.llygrid.format.editor.body(val,record,{align:"center",key:"URGENCY",label:"紧急程度",width:"10%",
				           		format:{type:'editor',renderType:'select',data:[{value:'A',text:'A'},{value:'B',text:'B'}]}})  ;
		           		}else{
		           			return val ;
		           		}
		           	}}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[20,10,20,30,40],
				 height:function(){
				 	return 190 ;
				 },
				 title:"需求货品明细",
				// autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sql_supplychain_requirement_plan_product_details_list",planId:planId,realId:'-'},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(records){ 
					 //修正数量
					 /*
					 $(".grid-content-details").find(".lly-grid-row").each(function(){
						 var _record = $(this).data("record") ;
						 var quantity = _record.QUANTITY ;
						 if( _record )
					 }) ;*/
					
					 $(".grid-content-details").find('.edit-fix-quantity').blur(function(){
						 var record = $(this).closest("tr").data("record") ;
						 var fixQuatity = $(this).val() ;
						 var id = record.ID ;
					 }) ;
				 }
					
			}) ;
			
			function getGridEditorData(){
				var data = [] ;
				$(".grid-content-details").find(".lly-grid-2-body  tr").each(function(){
					 var record = $(this).data("record");
					 var fixQuantity = $(this).find(".edit-fix-quantity").val() ;
					 var urgency      = $(this).find("select").val() ;
					 var purchaseQuantity = $(this).find(".edit-purchase-quantity").val() ;
					 data.push({id:record.ID,fixQuantity:fixQuantity,urgency:urgency,purchaseQuantity:purchaseQuantity}) ;
				 }) ;
				
				return data ;
			}
			
			$(".save").click(function(){
				var data = getGridEditorData();
				var memo = $(".audit-memo").val() ;
				$.dataservice("model:ScRequirement.saveItemAuditInfo" , {auditData:data,memo:memo,entityType:"planProduct",entityId:planId+"_"+currentRealId} , function(){
					//
				})
			}) ;
			
			$(".save-pass").click(function(){
				if( window.confirm("确认审批通过？") ){
					var data = getGridEditorData();
					var memo = $(".audit-memo").val() ;
					$.dataservice("model:ScRequirement.saveItemAuditInfo" , {auditData:data,memo:memo,entityType:"planProduct",entityId:planId+"_"+currentRealId,status:1} , function(){
						$(".grid-content").llygrid("reload",{},true) ;
					});
				}
			}) ;
			
			$(".save-nopass").click(function(){
					if( window.confirm("确认审批不通过？") ){
					var data = getGridEditorData();
					var memo = $(".audit-memo").val() ;
					$.dataservice("model:ScRequirement.saveItemAuditInfo" , {auditData:data,memo:memo,entityType:"planProduct",entityId:planId+"_"+currentRealId,status:2} , function(){
						$(".grid-content").llygrid("reload",{},true) ;
					})
				}
			}) ;
			
			$(".add-purchaseplan").click(function(){
				var data = getGridEditorData();
				var  plan = $(".purchase-plan").val();
				if(!plan){
					alert("请选择需求计划！");
				}else{
					var  totalPurchaseNum = 0 ;
					$(data).each(function(){
						totalPurchaseNum += this.purchaseQuantity||0 ;
					});
					if(window.confirm("确认加入该采购计划进行采购？")){
						$.dataservice("model:ScRequirement.add2PurchasePlan" , {
							purchasePlanId:plan,
							reqPlanId:planId,
							realId:currentRealId,
							purchaseQuantity:totalPurchaseNum
						} , function(){
							window.location.reload() ;
						});
					}
				}
			}) ;
			
			
			$(".track-img").click(function(){
				if( $(".track-list").is(":visible") ){
					$(".track-list").empty().hide() ;
					return ;
				}
				$(".track-list").empty().show().append("加载中......") ;
				var track = {entityType:"planProduct",entityId:planId+"_"+currentRealId} ;
				$.dataservice("model:SupplyChain.listTrack" , track , function(result){
					$(".track-list").empty().show() ;
					$(".track-list").append("<ul></ul>") ;
					$(result).each(function(){
						$(".track-list").find("ul").append("<li>"+this.MEMO+"("+this.CREATOR+"   "+this.CREATE_DATE+")</li>")
					}) ;
				},{noblock:true})
			}) ;
			
			function disabledActionPanel(){
				$(".action-panel").find(".btn").attr("disabled","disabled").hide() ;
				$(".action-panel").find("textarea").attr("readonly","readOnly").hide() ;
				$(".track-img").hide() ;
				$(".track-list").hide();
				$(".purchase-plan").hide();
			}
			
			function enableActionPanel(status){
				$(".action-panel").find(".btn").hide() ;
				$(".action-panel").find("textarea").hide() ;
				$(".add-purchaseplan").hide();
				$(".track-img").hide() ;
				$(".track-list").hide();
				
				if(status == 0 ||  status <1){
					$(".action-panel").find(".btn").removeAttr("disabled").show() ;
					$(".action-panel").find("textarea").removeAttr("readonly").show() ;
					$(".add-purchaseplan").hide();
					$(".track-img").show() ;
					$(".track-list").hide();
				}else  if(status == 1){//审批通过
					$(".add-purchaseplan").removeAttr("disabled").show() ;
					$(".save").removeAttr("disabled").show() ;
					$(".action-panel").find("textarea").removeAttr("readonly").show() ;
					$(".purchase-plan").show();
					$(".track-img").show() ;
					$(".track-list").hide();
				} else if( status == 2 ){//审批不通过
					disabledActionPanel() ;
					$(".track-img").show() ;
					$(".track-list").hide();
					return ;
				} else if( status == 3 ){//采购中
					$(".save").removeAttr("disabled").show() ;
					$(".action-panel").find("textarea").removeAttr("readonly").show() ;
					$(".track-img").show() ;
					$(".track-list").hide();
					return ;
				}else if( status == 4 ){//采购完成
					disabledActionPanel() ;
					$(".track-img").show() ;
					$(".track-list").hide();
					return ;
				}
				
			}
			
			disabledActionPanel() ;
   	 });
   	 