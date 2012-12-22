	$(function(){

			$(".btn-save").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					$.dataservice("model:Warehouse.In.doSave",json,function(result){
						window.opener.openCallback('edit') ;
						window.close();
					});

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
   }) ;