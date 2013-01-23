$(function(){
	$(".action").live("click",function(){
		var id = $(this).attr("val") ;
	
		if( $(this).hasClass("update") ){
			
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow("/saleProduct/index.php/users/editUser/"+id,600,430) ;
			
		}else if( $(this).hasClass("del") ){
			
			var record = $.llygrid.getRecord(this) ;
			if(window.confirm("确认删除该用户吗")){
				$.dataservice("model:User.disableUser",{id:record.ID},function(result){
						$(".grid-content").llygrid("reload") ;
				});
			}
		}else if( $(this).hasClass("add") ){
			openCenterWindow("/saleProduct/index.php/users/editUser",600,430) ;
		} 
		return false ;
	})

	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"ID",label:"操作", width:"10%",format:function(val,record){
					var html = [] ;
					var val = record["LOGIN_ID"] ;
					html.push("<a href='#' class='action update' val='"+val+"'>修改</a>&nbsp;") ;
					html.push("<a href='#' class='action del' val='"+val+"'>删除</a>") ;

					return html.join("") ;
			}},
           	//{align:"center",key:"ID",label:"ID", width:"5%" },
			
           	{align:"center",key:"NAME",label:"用户姓名",width:"10%",forzen:false,align:"left"},
           	{align:"center",key:"LOGIN_ID",label:"登录ID",width:"10%"},
           	{align:"center",key:"PHONE",label:"电话",width:"20%"},
           	{align:"left",key:"EMAIL",label:"邮箱",width:"20%"},
           	{align:"center",key:"GROUP_NAME",label:"用户组",width:"10%"}
         ],
         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 150 ;
		 },
		 title:"用户列表",
		 indexColumn:false,
		  querys:{sqlId:"sql_user_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
});

 
 function openCallback(){
 	$(".grid-content").llygrid("reload");
 }