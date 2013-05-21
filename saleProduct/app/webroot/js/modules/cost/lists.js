
   	var taskId = '' ;
	var currentAsin = "" ;

	$(function(){
			
			$(".grid-content-details").llygrid({
				 columns: columns,
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					 return $(window).height()-150 ;
				 },
				 title:"",
				 indexColumn:true,
				 querys:{sqlId:"sql_cost_product_details_list"},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
				 	$(".grid-checkbox").each(function(){
						var val = $(this).attr("value") ;
						if( $(".product-list ul li[asin='"+val+"']").length ){
							$(this).attr("checked",true) ;
						}
					}) ;
				 }
			}) ;
			
			$(".edit-action").live("click",function(){
				var id = $(this).attr("val") ;
				openCenterWindow(contextPath+"/cost/add/"+currentAsin+"/"+id,830,680) ;
			})
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow(contextPath+"/product/details/"+asin,950,650) ;
			})
			
			$(".query-btn").click(function(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				if(asin){
					querys.asin = asin ;
				}else{
					querys.unAsin = 1 ;
				}
				if(title){
					querys.title = title ;
				}
				
				$(".grid-content-details").llygrid("reload",querys) ;
			}) ;
			
			$(".add-cost").click(function(){
				if(currentAsin){
				 	openCenterWindow(contextPath+"/cost/add/"+currentAsin,680,650) ;
				}else{
					alert("请选择某个产品！");
				}
			}) ;
   	 });