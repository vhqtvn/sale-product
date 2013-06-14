
		$(function(){
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				//showCheck:true,
				cascadeCheck:false,
				onNodeClick:function(id,text,record){
					if( id == 'root' ){
						$(".grid-content").llygrid("reload",{categoryId:""}) ;
					}else{
						$(".grid-content").llygrid("reload",{categoryId:id}) ;
					}
				}
           }) ;

			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作", width:"6%",format:function(val,record){
						var html = [] ;
						html.push(  getImage('icon-grid.gif','查看','action view ') +"&nbsp;") ;
						return html.join("") ;
					}},
				 	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:{type:'img'}},
				 	{align:"center",key:"IS_ONSALE",label:"销售状态",width:"5%",format:function(val,record){
				 		if(val == 1){
				 			return   getImage('checked.gif','在售中','onsale-status ');
				 		}
				 		
				 		return   getImage('unchecked.gif','未销售','unsale-status');
				 	}},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%",sort:true},
		           	{align:"center",key:"QUANTITY",label:"总",group:"库存",width:"5%",sort:true },
		        	{align:"center",key:"COMMON_QUANTITY",label:"普通",group:"库存",width:"5%" ,sort:true},
		        	{align:"center",key:"FBA_QUANTITY",label:"FBA",group:"库存",width:"5%" ,sort:true},
		           	{align:"center",key:"SECURITY_QUANTITY",label:"安全",group:"库存",width:"5%",sort:true },
		           	{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
		          
		           	{align:"center",key:"MEMO",label:"备注",width:"25%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 200 ;
				 },
				 title:"",
				// autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sql_saleproduct_listBySupllierId",supplierId:supplierId,categoryId:''},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){}
					
			}) ;

			var tab = $('#tabs-default').tabs( {//$this->layout="index";
				tabs:[
					{label:'基本信息',content:"base-info"}
					,{label:'评价',content:"evaluate"}
					,{label:'供应产品',content:"supllie-product"}
				] ,
				height:'588x'
			} ) ;

			$(".commit").click(function(){
				if(window.confirm("确认保存？")){
					if( !$.validation.validate('#personForm').errorInfo ) {
						var json = $("#personForm").toJson() ;
						var vals = $('#default-tree').tree().getSelectedIds()  ;
						
						json.products = vals.join(",") ;

						$.dataservice("model:Supplier.saveSupplier",json,function(result){
							jQuery.dialogReturnValue(result) ;
							window.close();
						}) ;
					};
				}
			})
		})