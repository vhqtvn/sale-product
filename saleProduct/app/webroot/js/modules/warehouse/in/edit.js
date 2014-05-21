	$(function(){
			var isSaved = false ;
			
			$("[name='inSourceType']").click(function(){
				$(".trans_").hide() ;
				$(".trans_").find(":input").removeAttr("data-validator") ;
				var val = $(this).val() ;
				if(val == 'out'){ //外部采购入库
					$(".trans-rk").show() ;
					$(".trans-rk").find(":input").attr("data-validator","required") ;
					//$(this).parent().attr("colspan",3) ;
				}else if(val=='warehouse'){ //转仓
					$(".trans-wh").show() ;
					$(".trans-wh").find(":input").attr("data-validator","required") ;
					
					$(".trans-rk").show() ;
					$(".trans-rk").find(":input").attr("data-validator","required") ;
					
				}else if(val=='fba'){ //转仓
					$(".trans-wh").show() ;
					$(".trans-wh").find(":input").attr("data-validator","required") ;
					
					$(".trans-rkaccount").show() ;
					$(".trans-rkaccount").find(":input").attr("data-validator","required") ;
				}
			}) ;
			
			$("[name='inSourceType']:checked").click();
		
			$(".btn-save").click(function(){
				if( isSaved ) return ;
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					//alert($.json.encode(json)) ;
					//return ;
					json.status = '0' ;
					//shipDate
					//planArrivalDate
					json.shipDate = json.shipDate||"0000-00-00 00:00:00" ;
					json.planArrivalDate = json.planArrivalDate||"0000-00-00 00:00:00" ;
					isSaved = true ;
					$.dataservice("model:Warehouse.In.doSave",json,function(result){
						if( $("#id").val() ){
							window.location.reload() ;
						}else{
							window.close() ;
						}
						
					});
				};
				return false ;
			}) ;
	
		var logisticsCacheHtml = $(".logistics-tbody").html() ;
		
		function initLogistics(){
			var flowConfigName = $("#flowType").val() ;
			var flow = FlowFactory.get(flowConfigName,inSourceType) ;
			flow.logistics = true ;
			if( flow.logistics ){ //物流
				$(".logistics-tbody").show().html(logisticsCacheHtml).uiwidget() ;
			}else{
				$(".logistics-tbody").empty();
			}
		}
			
		$("#flowType").change(function(){
			initLogistics() ;
		}) ;	
		
		initLogistics();
			
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
					ds:{type:"url",content:contextPath+"/grid/query"},
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
					ds:{type:"url",content:contextPath+"/grid/query"},
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
   }) ;