$(function(){
		/*$(".action").live("click",function(){
			var id = $(this).attr("val") ;
			if( $(this).hasClass("update") ){
				openCenterWindow("/saleProduct/index.php/users/editGroup/"+id,400,300) ;
			}else if( $(this).hasClass("del") ){
				if(window.confirm("确认删除吗")){
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/product/deleteScript/"+id,
						data:{id:id},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							$(".grid-content").llygrid("reload") ;
						}
					}); 
				}
			}else if( $(this).hasClass("add") ){
				openCenterWindow("/saleProduct/index.php/users/editGroup",400,300) ;
			}else if( $(this).hasClass("assign") ){//分配权限
				openCenterWindow("/saleProduct/index.php/users/assignFunctions/"+id,400,400) ;
			} 
			return false ;
		})*/

		$(".grid-content").llygrid({
			columns:[
	           	{align:"center",key:"ID",label:"Actions", width:"20%",format:function(val,record){
						var html = [] ;
						var val = record["CODE"] ;
						html.push("<a href='#' class='action update' val='"+val+"'>修改</a>&nbsp;") ;
						html.push("<a href='#' class='action assign' val='"+val+"'>功能权限</a>&nbsp;") ;
						//html.push("<a href='#' class='action del' val='"+val+"'>删除</a>") ;

						return html.join("") ;
				}},
				{align:"center",key:"CODE",label:"代码", width:"15%"},
	           	{align:"center",key:"NAME",label:"用户组名称",width:"20%",forzen:false,align:"left"},
	           	{align:"center",key:"MEMO",label:"备注",width:"40%",forzen:false,align:"left"}
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
			 limit:20,
			 pageSizes:[10,20,30,40],
			 height:function(){
			 	return $(window).height() - 200 ;
			 },
			 title:"用户组列表",
			 indexColumn:false,
			  querys:{sqlId:"sql_groups_list"},
			 loadMsg:"数据加载中，请稍候......"
		}) ;
		
		$(".update").live("click",function(){
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow("/saleProduct/index.php/page/forward/Users.editGroup/"+record.ID,580,300) ;
		}) ;
		
		$(".assign").live("click",function(){
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow("/saleProduct/index.php/users/assignFunctions/"+record.CODE,400,400) ;
		}) ;
		
		$(".add-btn").click(function(){
			openCenterWindow("/saleProduct/index.php/page/forward/Users.editGroup",580,300) ;
		}) ;
		
		$(".query").click(function(){
			var json = $(".query-table").toJson() ;
			$(".grid-content").llygrid("reload",json) ;
		}) ;
 });
 
 function openCallback(){
 	$(".grid-content").llygrid("reload");
 }