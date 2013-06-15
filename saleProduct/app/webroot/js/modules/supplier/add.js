
		$(function(){
			
			$(".action").live("click",function(){
				var record = $(this).parents("tr:first").data("record")||{} ;
				var id = record.ID;
				if( $(this).hasClass("view") ){
					openCenterWindow(contextPath+"/saleProduct/details/"+record.REAL_SKU+"/sku",900,650) ;
				}
				return false ;
			});
			
			

			var tab = $('#tabs-default').tabs( {//$this->layout="index";
				tabs:[
					{label:'基本信息',content:"base-info"}
					,{label:'评价',content:"evaluate"}
					,{label:'供应产品',iframe:true,url:contextPath+"/page/forward/Supplier.supplierProductList/"+supplierId}
					,{label:'采购记录',iframe:true,url:contextPath+"/page/forward/Supplier.purchaseProductList/"+supplierId}
				] ,
				height:function(){
					return $(window).height() - 65 ;
				}
			} ) ;

			$(".commit").click(function(){
				if(window.confirm("确认保存？")){
					if( !$.validation.validate('#personForm').errorInfo ) {
						var json = $("#personForm").toJson() ;
						//var vals = $('#default-tree').tree().getSelectedIds()  ;
						
						json.products = "";//vals.join(",") ;

						$.dataservice("model:Supplier.saveSupplier",json,function(result){
							jQuery.dialogReturnValue(result) ;
							window.close();
						}) ;
					};
				}
			})
		})