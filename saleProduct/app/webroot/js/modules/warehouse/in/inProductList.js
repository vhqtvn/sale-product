

	var currentId = '' ;
	$(function(){
		$(".print-btn").live("click",function(){
			var tr = $(this).closest("tr") ;
			var record = $(this).closest("tr").data("record") ;
			var printNum = tr.find(".print-num").val() ;//$(this).prev().val() ;
			var accountId = record.ACCOUNT_ID ;//record.ACCOUNT_ID ;
			var listingSku =  record.LISTING_SKU ;//record.SKU ;
			openCenterWindow(contextPath+"/page/forward/Barcode.barcode/"+listingSku+"/"+accountId+"/"+printNum ,850,700) ;
	 });
		
			$(".grid-content-details").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",width:"3%",permission:function(){
		           		return   !$isRead ;
		           	},format:function(val,record){
		           		var html = [] ;
		           		html.push( getImage("delete.gif","删除","delete-in-product") ) ;
						return html.join("") ;
					}},
					{align:"center",key:"ID",label:"打印标签",width:"5%",format:function(val,record){
						var quntity = record.QUANTITY||0 ;
						var html = [] ;
		           		html.push("<input type='text'  class='print-num no-disabled' style='width:35px;height:20px;margin-top:2px;padding:0px;' value='"+quntity+"'  title='输入打印数量'>") ;
		           		html.push( "&nbsp;<button class='btn print-btn  no-disabled'>打印</button>") ;
						return html.join("") ;
					}},
					{align:"center",key:"IMAGE_URL",label:"",width:"2%",format:{type:'img'}},
		           	{align:"center",key:"REAL_NAME",label:"货品名称",width:"5%"},
	           		{align:"center",key:"REAL_SKU",label:"货品SKU",width:"5%",format:function(val,reocrd){
	           			return "<a href='#' product-realsku='"+val+"'>"+val+"</a>" ;
	           		}},
	           		{align:"center",key:"FN_SKU",label:"FNSKU",width:"5%"},
	           		{align:"center",key:"LISTING_SKU",label:"Listing SKU",width:"5%"},
	           		{align:"center",key:"QUANTITY",label:"数量",width:"3%",format:function(val,record){
	           			var dis = $isRead?"disabled='disabled'":'' ;
	           			return "<input type='text' "+dis+"  class='in-quantity' style='width:40px;height:20px;margin-top:2px;padding:0px;padding-left:2px;' value='"+(val||0)+"'>" ;
	           		}},
	           		{align:"center",key:"IN_QUANTITY",label:"实际入库数量",width:"3%",format:function(val,record){
	           			var dis = $isInRead?"disabled='disabled'":'' ;
	           			return "<input type='text' "+dis+"  class='real-in-quantity' style='width:40px;height:20px;margin-top:2px;padding:0px;padding-left:2px;' value='"+(val||'')+"'>" ;
	           		}},
	           		{align:"center",key:"DELIVERY_TIME",label:"供货时间",width:"6%"},
	           		{align:"center",key:"PRODUCT_TRACKCODE",label:"产品跟踪码",width:"6%"},
	           		{align:"center",key:"MEMO",label:"备注",width:"6%" }
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:200,
				 pageSizes:[200],
				 height:function(){
					 return $(window).height() -120 ;
				 },
				 title:"货品列表",
				 autoWidth:true,
				 querys:{sqlId:"sql_warehouse_new_in_products",inId:inId},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(records){
					 var q = 0 ;
					 var t = 0 ;
					 $(records).each(function(){
						 q = q+parseInt(this.QUANTITY) ;
						 t++;
					 }) ;
					 $(".total").html("货品种类（"+t+"）,数量总计（"+q+"）") ;
					 $(".delete-in-product").bind("click",function(event){
							event.stopPropagation() ;
							if(window.confirm("确认删除？")){
								var ipId = $(this).parents("tr").data("record")['ID'] ;
								$.dataservice("model:Warehouse.In.deleteInProduct",{ipId:ipId},function(result){
									if(result){
										alert(result) ;
									}else{
										$(".grid-content-details").llygrid("reload",{},true) ;
									}
								});
							}
							return false ;
					 }) ;
					 
					$(".in-quantity").blur(function(){
						var record = $(this).parents("tr").data("record") ;
						var quantity = $(this).val() ;
						$.dataservice("model:Warehouse.In.updateInProduct",{quantity:quantity,id:record.ID},function(result){
							if(result){
								alert(result) ;
							}
						});
					}) ;
					
					$(".real-in-quantity").blur(function(){
						var record = $(this).parents("tr").data("record") ;
						var quantity = $(this).val() ;
						$.dataservice("model:Warehouse.In.updateInProduct",{inQuantity:quantity,id:record.ID},function(result){
							if(result){
								alert(result) ;
							}
						});
					}) ;
				 }
			}) ;
			

			$(".add-in-product").live("click",function(){
				openCenterWindow(contextPath+"/page/forward/Warehouse.In.editInProductPage/"+inId+"/",850,650,function(){
					$(".grid-content-details").llygrid("reload",{},true);
				}) ;
			});
   	 });
   	 
   	 function openCallback(type){
   	 	if(type == 'box'){
   	 		$(".grid-content").llygrid("reload",{},true);
   	 	}else{
   	 		$(".grid-content-details").llygrid("reload",{},true);
   	 	}
   	 }