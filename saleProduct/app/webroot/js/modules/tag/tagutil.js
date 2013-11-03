var DynTag = {
		listByType : function(entityType,callback){
			var img =   getImage('tabs.gif','标签','tag-list ') ;
			$(document.body).append("<div class='tag-list-container'>"+img+"<ul></ul></div>") ;
			
			$(".tag-list-container>img").toggle(function(){
				$(".tag-list-container ul").show() ;
			},function(){
				$(".tag-list-container ul").hide() ;
			}) ;
			
			$(".tag-list-container ul li").live("click",function(){
				$(".tag-list-container ul li").removeClass("active") ;
				$(this).addClass("active") ;
				var tagId = $(this).attr("tagId") ;
				callback&& callback(entityType,tagId) ;
			}) ;
			
			$.dataservice("model:Tag.listByType",{entityType:entityType},function(result){
				$(result).each(function(){
					$(".tag-list-container ul").append("<li  tagId='"+this.ID+"'>"+this.NAME+"("+this.COUNT+")</li>") ;
				}) ;
			});
		},
		listByEntity : function(entityType , entityId){
			var img =   getImage('tabs.gif','标签','tag-list ') ;
			$(document.body).append("<div class='tag-list-container'>"+img+"<ul></ul><div class='log'></div>") ;
			$(".tag-list-container>img").toggle(function(){
				$(".tag-list-container ul").show() ;
			},function(){
				$(".tag-list-container ul").hide() ;
			}) ;
			
			$(".tag-list-container ul li").live("click",function(){
				$(".tag-list-container ul li").removeClass("active") ;
				$(this).addClass("active") ;
				var tagId = $(this).attr("tagId") ;
				//callback&& callback(entityType,tagId) ;
			}) ;
			
			DynTag.loadListByEntity(entityType,entityId) ;
		},
		loadListByEntity :function(entityType,entityId){
			$(".tag-list-container ul").empty() ;
			$.dataservice("model:Tag.listByEntity",{entityType:entityType,entityId:entityId},function(result){
				$(result).each(function(){
					var tag = $("<li  tagId='"+this.ID+"'  tagEntityId='"+this.TAG_ENTITY_ID+"'>"+this.NAME +"</li>").appendTo(".tag-list-container ul") ;
					if(parseInt(this.COUNT)){
						tag.append("<a class='delete'>删除</a>") ;//<a>备注</a>
						tag.append("<div>"+this.MEMO+"</div>") ;
					}else{
						tag.append("<a  class='add'>添加</a>") ;
						tag.append("<div class='add-container' style='display:none;'><textarea style='width:90%;height:50px;'></textarea><button class='btn save'>保存</button></div>") ;
					}
				}) ;
				
				$(".tag-list-container ul") .append("<li><button class='btn log'>查看标签历史</button></li>") ;
				
				$(".tag-list-container ul li .add").toggle(function(){
					$(this).parent().find(".add-container").show() ;
				},function(){
					$(this).parent().find(".add-container").hide() ;
				}) ;
				
				$(".tag-list-container ul li .save").click(function(){
					var tagId = $(this).closest("li").attr("tagId") ;
					var memo = $(this).prev().val() ;
					$.dataservice("model:Tag.addTag",{entityType:entityType,tagId:tagId,entityId:entityId,memo:memo},function(result){
						DynTag.loadListByEntity(entityType,entityId) ;
					});
				}) ;
				
				$(".tag-list-container ul li .delete").click(function(){
					var tagEntityId = $(this).closest("li").attr("tagEntityId") ;
					if(window.confirm("确认删除？")){
						//var memo = $(this).prev().val() ;
						$.dataservice("model:Tag.deleteTag",{tagEntityId:tagEntityId},function(result){
							DynTag.loadListByEntity(entityType,entityId) ;
						});
					}
				}) ;
				
				$(".tag-list-container ul li .log").click(function(){
					openCenterWindow(contextPath+"/page/forward/Tag.showLog/"+entityType+"/"+entityId,700,450,function(){
					}) ;
				}) ;
			});
		}
}