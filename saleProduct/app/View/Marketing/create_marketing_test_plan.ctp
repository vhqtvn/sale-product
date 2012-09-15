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
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('style-all');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('validator/jquery.validation');

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
   		var planId = '' ;
   
		$(function(){
			
			$("button").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
				
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/marketing/saveMarketingTestPlan",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.location.reload() ;
							window.close() ;
						}
					}); 
				};

					
			})
			
			$( "#plan_time" ).datepicker({dateFormat:"yy-mm-dd"});
		})
   </script>

</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">
	<table>
		<tr>
			<td>试销计划名称：</td><td><input type="text" id="name"  data-validator="required" value="" style="width:300px;"/></td>
		</tr>
		<tr>
			<td>备注：</td><td><textarea id="memo" style="width:300px;height:100px;"></textarea></td>
		</tr>
		<tr>
			<td>计划试销时间：</td>
			<td><input id="plan_time" type="text"/>
			</td>
		</tr>
		
		<tr>
			<td></td><td><button class="btn btn-primary">保存</button></td>
		</tr>
	</table>
</form>
</html>