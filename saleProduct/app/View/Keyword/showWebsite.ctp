<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>搜索网址</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('dialog/jquery.dialog');
		
		$keywordId = $params['arg1'] ;
	?>
	
	<style type="text/css">
	.website-container{
		background:#FFF;
	}
	
	.website-container ul{
		list-style: none;
	}
	
	.website-container ul li{
		padding:3px;
	}
	
	.title{
		font-weight:bold;
		padding:10px 30px;; 
	}
	</style>
	
	<script type="text/javascript">

	var args = $.dialogAraguments() ;
	
	$(function(){
		$(".title").html("当前关键字：&nbsp;"+args.keyword) ;
		
		$.dataservice("model:Keyword.getWebSite",{'keywordId':'<?php echo $keywordId;?>'},function(result){
			$(".website-container ul").empty().show() ;
			$(result).each(function(){
				$(".website-container ul").append("<li><a href='"+this.url+"' target='_blank'>"+this.url+"</a></li>") ;
			}) ;
		});
	}) ;
	</script>
  
</head>

<body class="container-popup">
		<div class="website-container">
				<div class="title"></div>
				<ul></ul>
		</div>
</body>
</html>