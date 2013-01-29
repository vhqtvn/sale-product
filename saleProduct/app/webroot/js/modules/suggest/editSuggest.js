$(function(){
	
	$("button").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			
			$.dataservice("model:Suggest.saveSuggest",json,function(result){
					window.opener.openCallback('') ;
					window.close();
			});
		}
	})
})