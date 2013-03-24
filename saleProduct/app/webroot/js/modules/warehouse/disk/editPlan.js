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
										//window.location.reload();
									});
								}
							});
						}) ;
					}else{
						alert("未选择任何货品进行盘点") ;
					}
				}
			}) ;
			
			

			$(".btn-save").click(function(){
				if(isAdd)return ;
				var length = $(".data-row").length ;
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					if(window.confirm("确认保存吗?")){
						isAdd = true ;
						
						var json = $("#personForm").toJson() ;
						
						//保存基本信息
						$.dataservice("model:Warehouse.Disk.doSavePlan",json,function(result){
							if( !diskId ){
								window.opener.openCallback('editPlan') ;
								window.close();
							}
							
							if(length <=0) window.location.reload();
						});
					}
				};
				
				
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