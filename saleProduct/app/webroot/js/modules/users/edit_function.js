$(function(){
	$("button").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			
			$.dataservice("model:User.saveFunctoin",json,function(result){
					window.opener.openCallback('') ;
					window.close();
			});
		/*
			$.ajax({
				type:"post",
				url:contextPath+"/users/saveFunctoin",
				data:json,
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					window.opener.openCallback() ;
					window.close() ;
				}
			}); */
		}
	})
}) ;