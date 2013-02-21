 //result.records , result.totalRecord
//result.records , result.totalRecord
	 function formatGridData(data){
		var records = data.record ;
 		var count   = data.count ;
 		
 		count = count[0][0]["count(*)"] ;
 		
		var array = [] ;
		$(records).each(function(){
			var row = {} ;
			for(var o in this){
				var _ = this[o] ;
				for(var o1 in _){
					row[o1] = _[o1] ;
				}
			}
			array.push(row) ;
		}) ;
	
		var ret = {records: array,totalRecord:count } ;
			
		return ret ;
	   }

	$(function(){
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",width:"20%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						html.push("<a href='#' class='delete' val='"+val+"'>删除</a>&nbsp;&nbsp;") ;
						return html.join("") ;
					}},
					{align:"center",key:"NAME",label:"管理员姓名",width:"30%",forzen:false,align:"left"},
		           	{align:"center",key:"PHONE",label:"管理员联系电话",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"EMAIL",label:"邮箱",width:"20%",forzen:false,align:"left"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:10,
				 pageSizes:[10,20,30,40],
				 height:200,
				 title:"管理员列表",
				 indexColumn:false,
				 querys:{sqlId:"sql_warehouse_manage_lists",warehouseId:warehouseId},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".delete").live("click",function(){
				var val = $(this).attr("val") ;
				$.ajax({
				type:"post",
				url:"/saleProduct/index.php/form/ajaxSave/" ,
				data:{warehouseManageId:val,sqlId:"sql_warehouse_manage_delete"},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					alert("保存成功!");
					$(".grid-content").llygrid("reload",{},true) ;
				}
			});
				return false;
			}) ;
		
   	 });

 $(function(){
   		var gridSelect = {
				title:'用户选择页面',
				defaults:[],//默认值
				key:{value:'ID',label:'NAME'},//对应value和label的key
				multi:true,
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
		   
		$(".add-manage").listselectdialog( gridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value ;
			var label = args.label ;
			$.ajax({
				type:"post",
				url:"/saleProduct/index.php/warehouse/saveManage/" ,
				data:{value:value.join(","),warehouseId:warehouseId},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					alert("添加成功!");
					$(".grid-content").llygrid("reload",{},true) ;
				}
			});
		}) ;
   }) ;