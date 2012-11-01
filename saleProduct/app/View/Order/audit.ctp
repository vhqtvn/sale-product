<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>订单审核</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
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
		
		input{
			width:300px;
		}
   </style>

   <script>
		$(function(){

			$("button").click(function(){
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;

					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/amazonaccount/saveAccount",
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
		})
   </script>
</head>
<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>订单审核</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<tbody>										   
								<tr>
									<th>审核类型：</th>
									<td>
										风险客户<input type="radio" name="status" value="2">
										待退单<input type="radio" name="status" value="3">
										外购订单<input type="radio" name="status" value="4">
										合格订单<input type="radio" name="status" value="5">
										加急单<input type="radio" name="status" value="6">
										特殊单<input type="radio" name="status" value="7">
									</td>
								</tr>
								<tr>
									<th>备注：</th>
									<td><textarea id="memo" style="width:300px;height:100px;"></textarea></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="button" class="btn btn-primary">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>