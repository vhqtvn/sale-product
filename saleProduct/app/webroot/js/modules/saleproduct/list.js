	$(function(){
			$(".action").live("click",function(){
				var record = $(this).parents("tr:first").data("record")||{} ;
				var id = record.ID;
				if( $(this).hasClass("view") ){
					openCenterWindow(contextPath+"/saleProduct/details/"+record.REAL_SKU+"/sku",1000,650) ;
				}else if( $(this).hasClass("update") ){
					openCenterWindow(contextPath+"/saleProduct/details/"+id,1000,650,function(){
						$(".grid-content").llygrid("reload",{},true) ;
					}) ;
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
					openCenterWindow(contextPath+"/saleProduct/forward/edit_product/",900,620,function(){
						$(".grid-content").llygrid("reload",{},true) ;
					}) ;
				}else if( $(this).hasClass("inout") ){//货品出入库明细
					openCenterWindow(contextPath+"/page/forward/Warehouse.In.storageDetails/"+id,850,600) ;
				}else if( $(this).hasClass("assign") ){//货品出入库明细
					openCenterWindow(contextPath+"/page/forward/Warehouse.In.assign/"+id,850,600) ;
				}
				return false ;
			});
		
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;
			
			$(".category-set").live("click",function(){
				var record = $(this).parents("tr:first").data("record");
				var categoryTreeSelect = {
						title:'产品分类选择页面',
						//valueField:"#categoryId",
						//labelField:"#categoryName",
						key:{value:'ID',label:'NAME'},//对应value和label的key
						multi:true ,
						tree:{
							title:"产品分类选择页面",
							method : 'post',
							nodeFormat:function(node){
								node.complete = false ;
							},
							asyn : true, //异步
							rootId  : 'root',
							expandLevel:3,
							rootText : '产品分类',
							CommandName : 'sqlId:sql_saleproduct_categorytree',/*sql_saleproduct_account_categorytree*/
							recordFormat:true,
							params : {
								productId:record.ID
							}
						}
				   } ;
				$.listselectdialog( categoryTreeSelect,function(win,ret){
					if( ret && ret.value ){
						var categoryId = ret.value.join(",") ;
						//保存产品分类
						var productId = record.ID ;
						json = {
								categoryId:categoryId,
								productId:productId
						} ;
						$.dataservice("model:SaleProduct.saveProductCategoryByObj",json,function(result){
							//刷新树
							$('#default-tree').remove() ;
							$("#tree-wrap").append('<div id="default-tree" class="tree" style="padding: 5px; "></div>') ;
							loadTree( ) ;
						});
					}
				}) ;
			}) ;

			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作", width:"8%",format:function(val,record){
						var html = [] ;
						html.push(  getImage('icon-grid.gif','查看','action view ') +"&nbsp;") ;
						html.push('<a href="#" class="category-set" val="'+val+'">'+getImage("collapse-all.gif","设置分类")+'</a>&nbsp;') ;
							if( $product_edit ){
								html.push(  getImage('edit.png','编辑','action update ') +"&nbsp;") ;
							}
				
						return html.join("") ;
					}},
				 	{align:"center",key:"IMG_URL",label:"图片",width:"5%",format:{type:'img'}},
				 	{align:"center",key:"IS_ONSALE",label:"销售状态",width:"5%",format:function(val,record){
				 		if(val == 1){
				 			return   getImage('checked.gif','在售中','onsale-status ');
				 		}
				 		
				 		return   getImage('unchecked.gif','未销售','unsale-status');
				 	}},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%",sort:true},
		           	{align:"center",key:"LAST_IN_TIME",label:"",group:"库存",width:"3%",format:function(val,record){
		           		if( record.LAST_ASSIGN_TIME &&( val >= record.LAST_ASSIGN_TIME ) ){
							return '<img title="已经分配" src="/'+fileContextPath+'/app/webroot/img/success.gif">';
				
		           		}
		           		return "<img title='未分配' src='/"+fileContextPath+"/app/webroot/img/error.gif'>"  ;
		           	}},
		           	{align:"center",key:"QUANTITY",label:"总",group:"库存",width:"5%",sort:true },
		        	{align:"center",key:"COMMON_QUANTITY",label:"普通",group:"库存",width:"5%" ,sort:true},
		        	{align:"center",key:"FBA_QUANTITY",label:"FBA",group:"库存",width:"5%" ,sort:true},
		           	{align:"center",key:"SECURITY_QUANTITY",label:"安全",group:"库存",width:"5%",sort:true },
		           	{align:"center",key:"LOCK_QUANTITY",label:"锁定",group:"库存",width:"5%",sort:true },
		           	{align:"center",key:"ASSIGN_QUANTITY",sort:true,label:"可分配",group:"库存",width:"5%",format:function(val, record){
		           		var quantity = record.QUANTITY - record.SECURITY_QUANTITY - record.LOCK_QUANTITY ;
		           		return quantity ;
		           	},render:function(record){
		           		var quantity = record.QUANTITY - record.SECURITY_QUANTITY - record.LOCK_QUANTITY ;
		           		if( parseInt(record.WARNING_QUANTITY||0) >= quantity ){
		           			var title = "当前可分配库存已经小于或等于预警库存【"+record.WARNING_QUANTITY+"】" ;
		           			$(this).find("td[key='ASSIGN_QUANTITY']").css({"background":"red",'font-size':'15px',color:'#FFF'}).find("span").attr("title",title);
		           		}
		           	}},
		           	
		           	{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
		          
		           	{align:"center",key:"MEMO",label:"备注",width:"25%"},
		           	{align:"center",key:"ID",label:"操作", width:"6%",format:function(val,record){
						//if(tabIndex < 2){
							var html = [] ;
							html.push("<a href='#' class='action giveup btn' val='"+val+"' type=1>作废</a>") ;
							return html.join("") ;
							/*}else{
							var html = [] ;
							html.push(deleteHtml) ;
							html.push("<a href='#' class='action giveup btn'  val='"+val+"' type=2>恢复</a>") ;
							return html.join("") ;
						//}*/
					},permission:function(){ return $product_giveup ; }}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[20,10,20,30,40],
				 height:function(){
				 	return $(window).height() - 200 ;
				 },
				 title:"",
				// autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sql_saleproduct_list",status:1,categoryId:''},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
					if( $product_onsale ){
						 $(".onsale-status").css("cursor","pointer").click(function(){
							 var row =  $(this).parents("tr:first").data("record") ;
							 if(window.confirm("确认该货品为未销售状态？")){
								 $.dataservice("model:SaleProduct.onSale",{id:row.ID,isOnsale:'0'},function(){
							 			$(".grid-content").llygrid("reload",{},true) ;
								}) ;
							 }
						 }) ;
						 
						 $(".unsale-status").css("cursor","pointer").click(function(){
							 var row =  $(this).parents("tr:first").data("record") ;
							 if(window.confirm("确认该货品为在售状态？")){
								 $.dataservice("model:SaleProduct.onSale",{id:row.ID,isOnsale:'1'},function(){
							 			$(".grid-content").llygrid("reload",{},true) ;
								}) ;
							 }
						 }) ;
					 }
					}
					
			}) ;
   	 });
   	 