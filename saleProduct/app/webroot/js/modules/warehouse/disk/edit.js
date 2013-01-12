	$(function(){
			var isAdd = false ;
			
			
			//结束盘点
			$(".btn-end").click(function(){
				if(window.confirm("确认结束盘点，并提交审批?")){
					/*$.dataservice("model:Warehouse.Disk.doEnd",{diskId:diskId},function(result){
						//window.location.reload();
					});*/
					
					var length = $(".data-row").length ;
					if(length > 0){
						$(".data-row").each(function(index){
							var id = $(this).find("[name='id']").val() ;
							var realNum = $(this).find("[name='realNum']").val() ;
							var memo = $(this).find("[name='memo']").val() ;
							var gainNum = $.trim( $(this).find("[key='gainNum']").text() ) ;
							var lossNum = $.trim( $(this).find("[key='lossNum']").text() ) ;
							var row = {id:id,realNum:realNum,memo:memo,gainNum:gainNum,lossNum:lossNum}  ;
						
							$.dataservice("model:Warehouse.Disk.doEditDetails",row,function(result){
								if( length == index+1 ){
									$.dataservice("model:Warehouse.Disk.doCommit",{diskId:diskId},function(result){
										window.location.reload();
									});
								}
							});
						}) ;
					}else{
						alert("未选择任何货品进行盘点") ;
					}
				}
			}) ;
			
			//保存审批结果
			$(".btn-audit-save").click(function(){
				if(window.confirm("确认保存审批结果？")){
					
					var json =$(".edit-table").toJson() ;
					
					var isPass = json.isPass||"" ;
					
					$.dataservice("model:Warehouse.Disk.doAudit",{diskId:diskId,passProductIds:isPass,status:1},function(result){
						try{ window.opener.openCallback('edit') ; }catch(e){}
						window.location.reload() ;
					});
				}
				
			});
			
			//保存审批结果
			$(".btn-audit-complete").click(function(){
				if(window.confirm("确认审批完成？")){
					var json =$(".edit-table").toJson() ;
					
					var isPass = json.isPass||"" ;
					$.dataservice("model:Warehouse.Disk.doAudit",{diskId:diskId,passProductIds:isPass,status:3},function(result){
						try{ window.opener.openCallback('edit') ; }catch(e){}
						window.location.reload() ;
					});
				}
			});

			$(".btn-save").click(function(){
				if(isAdd)return ;
				var length = $(".data-row").length ;
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					if(window.confirm("确认保存吗?")){
						isAdd = true ;
						
						var json = $("#personForm").toJson() ;
						
						json.planId=planId;
						
						//保存基本信息
						$.dataservice("model:Warehouse.Disk.doSave",json,function(result){
							if( !diskId ){
								window.opener.openCallback('edit') ;
								window.close();
							}
							
							if(length <=0) window.location.reload();
						});
					}
				};
				
				//保存明细信息
				//var rows = [] ;
				
				$(".data-row").each(function(index){
					var id = $(this).find("[name='id']").val() ;
					var realNum = $(this).find("[name='realNum']").val() ;
					var memo = $(this).find("[name='memo']").val() ;
					var gainNum = $.trim( $(this).find("[key='gainNum']").text() ) ;
					var lossNum = $.trim( $(this).find("[key='lossNum']").text() ) ;
					var row = {id:id,realNum:realNum,memo:memo,gainNum:gainNum,lossNum:lossNum}  ;
				
					$.dataservice("model:Warehouse.Disk.doEditDetails",row,function(result){
						if( length == index+1 ){
							window.location.reload();
						}
					});
				}) ;
				
				return false ;
			}) ;
			
   		var chargeGridSelect = {
				title:'用户选择页面',
				defaults:[],//默认值
				key:{value:'ID',label:'NAME'},//对应value和label的key
				multi:false,
				grid:{
					title:"用户选择",
					params:{
						sqlId:"sql_user_list_forwarehouse"
					},
					ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					pagesize:10,
					columns:[//显示列
						{align:"center",key:"ID",label:"编号",width:"100"},
						{align:"center",key:"LOGIN_ID",label:"登录ID",sort:true,width:"100"},
						{align:"center",key:"NAME",label:"用户姓名",sort:true,width:"100"}
					]
				}
		   } ;
		   
		$(".btn-charger").listselectdialog( chargeGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			$("#charger").val(value) ;
			$("#chargerName").val(label) ;
			return false;
		}) ;
		
		var warehouseGridSelect = {
				title:'仓库选择',
				defaults:[],//默认值
				key:{value:'ID',label:'NAME'},//对应value和label的key
				multi:false,
				grid:{
					title:"仓库选择",
					params:{
						sqlId:"sql_warehouse_lists"
					},
					ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					pagesize:10,
					columns:[//显示列
						{align:"center",key:"ID",label:"编号",width:"100"},
						{align:"center",key:"CODE",label:"仓库代码",sort:true,width:"100"},
						{align:"center",key:"NAME",label:"仓库名称",sort:true,width:"100"},
						{align:"center",key:"ADDRESS",label:"地址",sort:true,width:"100"}
					]
				}
		   } ;
		   
		$(".btn-warehouse").listselectdialog( warehouseGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			$("#warehouseId").val(value) ;
			$("#warehouseName").val(label) ;
			return false;
		}) ;
		
		var productGridSelect = {
				title:'货品选择',
				defaults:[],//默认值
				key:{value:'ID',label:'NAME'},//对应value和label的key
				multi:true,
				grid:{
					title:"货品选择",
					params:{
						sqlId:"sql_warehouse_disk_products",
						planId:planId
					},
					ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					pagesize:10,
					columns:[//显示列
			           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left" },
			           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%"},
			           	{align:"center",key:"QUANTITY",label:"库存" ,width:"5%" },
			           	
			           	{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",
			           		content:{'base':"基本类型",'package':"打包货品"}}},
			           	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:{type:'func',funcName:"window.opener.renderProductImg"}},
			           	{align:"center",key:"MEMO",label:"备注",width:"20%"}
			           	
					]
				}
		   } ;
		   
		
		   
		$(".btn-select-product").listselectdialog( productGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			args.diskId = diskId ;
			args.value = (args.value||[]).join(",") ;
			var value = args.value ;
			var label = args.label ;
			//保存货品到盘点计划明细列表
			$.dataservice("model:Warehouse.Disk.doSelectProduct",args,function(result){
				window.location.reload();
			});
			return false;
		}) ;
		
		//实际库存编辑
		$("[name='realNum']").live("keyup",function(){
			var val = $(this).val() ;
			var row = $(this).parents("tr:first") ;
			var paperNum = row.find("[name='paperNum']").val() ;
			if( val - paperNum > 0 ){
				row.removeClass("audit-nosame audit-same").addClass("audit-nosame") ;
				row.find("[key='gainNum']").html(val - paperNum) ;
				row.find("[key='lossNum']").html("") ;
			}else if( paperNum - val > 0 ){
				row.removeClass("audit-nosame audit-same").addClass("audit-nosame") ;
				row.find("[key='lossNum']").html( paperNum - val) ;
				row.find("[key='gainNum']").html("") ;
			}else if(val == paperNum){
				row.removeClass("audit-nosame audit-same").addClass("audit-same") ;//"audit-same":"audit-nosame"
				row.find("[key='lossNum']").html("") ;
				row.find("[key='gainNum']").html("") ;
			}
		});
   }) ;