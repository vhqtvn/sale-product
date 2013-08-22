$(function(){
	$(".save-plan").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			
			$.dataservice("model:Keyword.savePlan",json,function(result){
					$(document.body).dialogReturnValue(true) ;
					window.close();
			});
		}
	}) ;
}) ;