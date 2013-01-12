	$(function(){
			var isAdd = false ;

			$(".btn-save").click(function(){
				if(isAdd)return ;
				var length = $(".data-row").length ;
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					if(window.confirm("确认保存吗?")){
						isAdd = true ;
						
						var json = $("#personForm").toJson() ;
						
						//保存基本信息
						$.dataservice("model:Warehouse.Ram.doSaveOption",json,function(result){
								window.opener.openCallback('editPlan') ;
								window.close();
						});
					}
				};
				
				
				return false ;
			}) ;
			
   		
   }) ;