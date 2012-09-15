<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('../grid/grid');
	?>

	<script>
		$(function(){
			$("button").click(function(){
				var name = $("[name='name']").val() ;
				var url = $("[name='url']").val() ;

				if( !($.trim(name) && $.trim(url)) ){
					alert("名称和地址不能为空！");
					return ;
				}

				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/seller/save",
					data:{name:name,url:url},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						alert(result);
					}
				}); 
			}) ;
		}) ;
	</script>

   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body>
	商家名称：<br/>
	<input type="text" name="name" style="width:100%;"><br/>
	商家地址：<br/>
	<input type="text" name="url" style="width:100%;"><br/>
	<button>保存</button><br/>
</body>
</html>
