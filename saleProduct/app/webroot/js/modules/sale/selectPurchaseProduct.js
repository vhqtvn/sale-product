$(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'货品SKU输入',content:"sku-input-content"},//9
					{label:'货品选择',content:"product-select-content"},//12
					{label:'ASIN输入',content:"asin-input-content"},//10
					{label:'开发产品选择',content:"dev-select-content"}//10
				] ,
				height:function(){
					return $(window).height() - 300 ;
				},
				select:function(event,ui){
					var index = ui.index ;
					//renderAction(index);.
					setTimeout(function(){
						if( index == 1){
							selectProduct.initProductSelectTab() ;
						}else if(index == 3){
							selectProduct.initDevSelect() ;
						}
					},100) ;
				}
			} ) ;
			
			$(".btn-vali").click(function(){
				var result = {} ;
				if( $(this).hasClass("btn-vali-sku") ){
					var val = $(".skus").val() ;
					result.skus = val ;
				}else if( $(this).hasClass("btn-vali-asin") ){
					var val = $(".asins").val() ;
					result.asins = val ;
				}
				
				$.dataservice("model:Sale.checkValidSkus",result,function(res){
					var correct = res.correct ;
					var incorrect = res.incorrect ;
					
					$(correct).each(function(index,record){
						buildSelectProduct(record.IMAGE_URL , record.REAL_SKU , record.NAME ) ;
					}) ;
					
					if( incorrect&&incorrect.length> 0 ){
						alert( incorrect.join(",") +"输入有误或者对应的货品不存在！") ;
						if( result.skus ){
							$(".skus").val( incorrect.join(",")  ) ;
						}
						
						if( result.asins ){
							$(".asins").val( incorrect.join(",")  ) ;
						}
					}
					
				}) ;
			}) ;
			
			$(".submit-select").click(function(){
				var skus = [] ;
				$(".select-container li[sku]").each(function(){
					var sku = $(this).attr("sku") ;
					skus.push(sku) ;
				}) ;
				$.dataservice("model:Sale.saveSelectedProduct",{ sku:skus.join(",") , planId:planId },function(){
					//jQuery.dialogReturnValue(true) ;
					//window.close() ;
				})
			}) ;
}) ;

var selectProduct = {
		initProductSelectTab : function(){
			     if( window.__isInit1 ) return ;
			     window.__isInit1 = true ;
				$(".product-select-grid").llygrid({
						columns:[
						    {align:"center",key:"ID",label:"名称",width:"25",forzen:false,format:{type:'checkbox',callback:function(record){
								var checked = $(this).attr("checked");
								if(checked){
									if( !record.REAL_SKU ){
										alert("该产品还未关联货品，不能选择！") ;
										$(this).attr("checked",false);
										return ;
									}
									buildSelectProduct(record.IMAGE_URL , record.REAL_SKU , record.NAME ) ;
								}else{
									//alert("该产品还未关联货品，不能选择！") ;
									//$(this).attr("checked",false);
								}
							}}},
						    {align:"center",key:"NAME",label:"名称",width:"180",forzen:false,align:"left"},
					        {align:"center",key:"REAL_SKU",label:"SKU",width:"100"},
				           	{align:"center",key:"TYPE",label:"货品类型",width:"100",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
				           	{align:"center",key:"IMAGE_URL",label:"图片",width:"100",format:{type:'img'}},
				           	{align:"center",key:"MEMO",label:"备注",width:"300"}
				         ],
				         ds:{type:"url",content:contextPath+"/grid/query"},
						 limit:20,
						 pageSizes:[10,20,30,40],
						 height:function(){
						 	return $(window).height() - 425 ;
						 },
						 title:"",
						// autoWidth:true,
						 indexColumn:false,
						  querys:{sqlId:"sql_saleproduct_list",type:"base",status:1,categoryId:''},
						 loadMsg:"数据加载中，请稍候......"
					}) ;
		},
		initDevSelect : function(){
			  if( window.__isInit2 ) return ;
			     window.__isInit2 = true ;
			$(".dev-product-filter-grid").llygrid({
				columns:[
		           	{align:"center",key:"NAME",label:"筛选名称",width:"130",forzen:false,align:"left"},
		           	{align:"center",key:"CREATE_TIME",label:"筛选时间",width:"120"},
		           	{align:"center",key:"STATUS57",label:"审批完成",width:"55" }
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query" },//salegrid/filterTask4
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
					 	return $(window).height() - 400 ;
				 },
				 title:"筛选列表",
				 indexColumn:false,
				 querys:{sqlId:'sql_getProductDevFilter'},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	var taskId = rowData.ID  ;
				 	$(".dev-product-grid").llygrid("reload",{taskId:taskId}) ;
				 },loadAfter:function(){
					 
					 $(".dev-product-filter-grid").find(".grid-toolbar-div").find("td:lt(4)").hide();
				 }
			}) ;
			
			$(".dev-product-grid").llygrid({
				columns:[
							{align:"center",key:"ASIN",label:"",width:"10%",format:{type:"checkbox",callback:function(record){
								var checked = $(this).attr("checked");
								if(checked){
									if( !record.REAL_SKU ){
										alert("该产品还未关联货品，不能选择！") ;
										$(this).attr("checked",false);
										return ;
									}
									buildSelectProduct(record.IMAGE_URL , record.REAL_SKU , record.NAME ) ;
								}else{
									//alert("该产品还未关联货品，不能选择！") ;
									//$(this).attr("checked",false);
								}
							}}},
				        	{align:"center",key:"NAME",label:"货品名称",width:"100",forzen:false,align:"left"},
				        	{align:"center",key:"IMAGE_URL",label:"",width:"30",forzen:false,format:{type:'img'}},
				        	{align:"center",key:"ASIN",label:"ASIN", width:"90",format:{type:'asin'}},
				           	{align:"center",key:"TITLE",label:"AMAZON名称",width:"20%",align:"left",format:{type:'titleListing'}},
				           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:{type:'img'}}
				         ],
				         ds:{type:"url",content: contextPath+'/grid/query'},//"/salegrid/filterApply/"},
						 limit:30,
						 pageSizes:[10,20,30,40],
						 height:function(){
							 	return $(window).height() - 400 ;
						 },
						 title:"产品列表",
						 indexColumn:true,
						 querys:{status:$("[name='status']").val(),sqlId:'sql_getProductDevFilter_Pass_Product'},
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
		}
}

function buildSelectProduct(imgUrl,sku,title){
	if( $("li[sku='"+sku+"']").length ) return ;
	var imgStr = "" ;
	if(imgUrl){
		imgUrl = imgUrl.replace(/%/g,'%25') ;
		imgStr =  "<img src='/"+fileContextPath+"/"+imgUrl+"' style='width:25px;height:25px;margin-right:5px;'>" ;
		}
	$(".select-container ul").append("<li class='alert alert-success' sku='"+sku+"' style='padding:5px 10px;'><div class='row-fluid' style='width:150px;'><span class='span3'>"
				+imgStr+"</span><span class='span9'>"+sku+"<br/>"+title+"</span></div></li>") ;
}
   	 