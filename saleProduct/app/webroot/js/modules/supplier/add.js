
		$(function(){
			
			var tabs = [] ;
			tabs.push(	{label:'基本信息',content:"base-info"}) ;
			tabs.push({label:'评价',content:"evaluate"}) ;
			if( supplierId ){
				tabs.push({label:'供应产品',iframe:true,url:contextPath+"/page/forward/Supplier.supplierProductList/"+supplierId}) ;
				tabs.push({label:'采购记录',iframe:true,url:contextPath+"/page/forward/Supplier.purchaseProductList/"+supplierId}) ;
			}

			var tab = $('#tabs-default').tabs( {//$this->layout="index";
				tabs: tabs ,
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