$(function(){
	$(".action").live("click",function(){
		var id = $(this).attr("val") ;
	
		if( $(this).hasClass("update") ){
			
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow(contextPath+"/users/editUser/"+id,600,430) ;
			
		}else if( $(this).hasClass("del") ){
			
			var record = $.llygrid.getRecord(this) ;
			if(window.confirm("确认删除该用户吗")){
				$.dataservice("model:User.disableUser",{id:record.ID},function(result){
						$(".grid-content").llygrid("reload") ;
				});
			}
		}else if( $(this).hasClass("add") ){
			openCenterWindow(contextPath+"/users/editUser",600,430) ;
		} 
		return false ;
	})

	$(".grid-content").llygrid({
		columns:[
           	{align:"center",key:"SOURCE_NAME",label:"货币名称",width:"20%",forzen:false},
           	{align:"center",key:"SOURCE",label:"货币代码",width:"20%"},
           	{align:"right",key:"EXCHANGE_RATE",label:"中间价",width:"20%",format:function(val,record){
           		return (val*100).toFixed(2) ;
           	}}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 150 ;
		 },
		 title:"汇率列表",
		 indexColumn:false,
		  querys:{sqlId:"data_exchange_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
});

 
 function openCallback(){
 	$(".grid-content").llygrid("reload");
 }