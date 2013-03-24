
	$(function(){
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				if( $(this).hasClass("update") ){
					openCenterWindow(contextPath+"/saleProduct/details/"+id,900,600) ;
				}else if( $(this).hasClass("giveup") ){
					var type = $(this).attr("type");
					var message = type == 1?"确认将该货品作废吗":"确认恢复该货品吗？"
					message = type == 3?"确认删除该货品吗（相关货品数据都将删除，如与SKU关系，组合产品关系等）？":message;
					if(window.confirm(message)){
						$.ajax({
							type:"post",
							url:contextPath+"/saleProduct/giveup/"+id+"/"+type,
							data:{id:id},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								$(".grid-content").llygrid("reload",{},true) ;
							}
						}); 
					}
				}else if( $(this).hasClass("add") ){
					openCenterWindow(contextPath+"/saleProduct/forward/edit_product/",700,500) ;
				}
				return false ;
			});
		
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;

			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left",format:function(val,reocrd){
		           		return "<a href='"+reocrd.URL+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%"},
		           	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/"+fileContextPath+"/"+val+"' style='width:30px;height:30px;'>" ;
		           		}
		           		return "" ;
		           	}}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return 200 ;
				 },
				 title:"",
				 autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sql_saleproduct_list",type:"base",status:1},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
   	 });
   	