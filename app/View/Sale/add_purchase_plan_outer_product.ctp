<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');

	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}

		.rule-content-item{
			clear:both;
		}

		.item-label,.item-relation,.item-value,.item-value{
			float:left;
		}
   </style>

   <script>
   		var planId = '<?php echo $planId;?>' ;
   
		$(function(){
			
			$("button").click(function(){
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/sale/savePurchasePlanProducts",
					data:{
						planId:planId,
						asins:$.trim($("textarea").val())
					},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.opener.$(".grid-content-details").llygrid("reload",{planId:planId}) ;
						window.close() ;
					}
				}); 
			})
			
			$( "#plan_time" ).datepicker({dateFormat:"yy-mm-dd"});
		})
   </script>

</head>
<body>
	<div>
	<textarea style="width:98%;height:300px;"></textarea>
	</div>

	<table>
		<tr>
			<td></td><td><button>保存</button></td>
		</tr>
	</table>

</html>