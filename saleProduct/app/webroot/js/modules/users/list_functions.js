$(function(){
		
		$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				isRootExpand:true,
				onNodeClick:function(id, text, record,node){
					$(".grid-content").llygrid("reload",{id:id}) ;
				}
           }) ;
           
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				if( $(this).hasClass("update") ){
					openCenterWindow("/saleProduct/index.php/users/editFunction/"+id,600,450) ;
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
					openCenterWindow("/saleProduct/index.php/users/editFunction",600,450) ;
				} 
				return false ;
			})

			$(".grid-content").llygrid({
				columns:[
		           	
					{align:"center",key:"ID",label:"操作", width:"10%",format:function(val,record){
							var html = [] ;
							html.push("<a href='#' class='action update' val='"+val+"'>修改</a>&nbsp;") ;
							//html.push("<a href='#' class='action del' val='"+val+"'>删除</a>") ;
	
							return html.join("") ;
					}},
					{align:"center",key:"ID",label:"编号", width:"5%"},
		           	{align:"left",key:"PARENT_NAME",label:"父菜单", width:"10%"},
		           	{align:"center",key:"DISPLAY_ORDER",label:"显示顺序", width:"10%"},
					
		           	{align:"left",key:"NAME",label:"功能名称",width:"15%",forzen:false,align:"left"},
		           	{align:"left",key:"URL",label:"URL地址",width:"20%"},
		           	{align:"left",key:"CODE",label:"功能编码",width:"20%"},
		            {align:"left",key:"TYPE",label:"类别",width:"6%"} 
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
					 return $(window).height() - 140 ;
				 },
				 title:"用户列表",
				 indexColumn:false,
				 querys:{sqlId:"sql_functions_list"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json) ;
			}) ;
   	 });
   	 
   	 
   	 
   	 window.openCallback = function(){
   	 	$(".grid-content").llygrid("reload",{},true) ;
   	 }