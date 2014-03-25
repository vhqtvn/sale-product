<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('default/module/index/layout');
		echo $this->Html->css('default/module/index/menu');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		
		$groupCode = $user["GROUP_CODE"] ;
		$menuId = $params['arg1'] ;
		
		$userModel  = ClassRegistry::init("User") ;
		$Utils  = ClassRegistry::init("Utils") ;
		$funs = $userModel->getSecurityFunctions( $groupCode ); 
		
		$functions = $funs['functions'] ;
		
		$tree = $Utils->formatTreeForRecords($functions) ;
		
	?>
	<script type="text/javascript">
		var treeData = <?php echo $tree;?> ;
		var menuId = '<?php echo $menuId ;?>' ;
		//1111
		$(function(){
			$(treeData).each(function(){
				if(this.id == menuId){
					$(this.childNodes).each(function(){
						//alert($.json.encode(this));
						var imgRoot = "/"+fileContextPath+"/app/webroot/css/default/module/index/" ;
						var html = '<li><a href="'+this.URL+'"><img src="'+imgRoot+'t2.png" />'+this.text+'</a></li>'  ;
						var menu = $(html).appendTo($("#nav")) ;
						
						if( this.childNodes ){
							menu.find(">a").addClass("sub").attr("tabindex",1);
							menu.append('<img src="'+imgRoot+'up.gif" alt="" />') ;
							var ul = $("<ul  style='margin:0px;'></ul>").appendTo(menu) ;
							$(this.childNodes).each(function(){
								var html = '<li><a href="'+this.URL+'"><img src="'+imgRoot+'t2.png" />'+this.text+'</a></li>'  ;
								$(html).appendTo(ul) ;
							}) ;
						}
						
					}) ;
				}
				
				$("#home").css("height",$(window).height()) ;
			}) ;
			
			$("#nav a").live("click",function(){
				var text = $(this).text() ;
				$(".navigation").html(text);
				
				if($(this).parent().find("ul")){
					$(this).parent().find("ul").show();
				}
				
				var href = $(this).attr("href") ;
				if(href){
					$("#home").attr("src",contextPath+"/"+href);
				}
				
				return false ;
			});
			
			
					
		}) ;
	</script>
	
	<style type="text/css">
		.navigation{
			background:#EEE;
			height:25px;
			width:100%;
			font-weight:bold;
			font-size:15px;
		}
	</style>

</head>
<body>

	<div class="row-fluid">
		<div class="span2">
				<ul id="nav" style="margin:0px;">
	            </ul>
		</div>
		<div class="span10">
			<div class="navigation"></div>
			<iframe frameborder="0" name="home" id="home" src="" style="width:100%;"></iframe>
		</div>
	</div>
</body>
</html>
