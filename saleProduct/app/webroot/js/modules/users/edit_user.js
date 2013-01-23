$(function(){
	if( $("#login_id").val()  ){
		$("#login_id").attr("disabled",true) ;
	}
	
	$("button").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			
			$.dataservice("model:User.saveUser",json,function(result){
					window.opener.openCallback('') ;
					window.close();
			});
		}
	})
})