<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>用户编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');

	?>

	<script type="text/javascript">
	   
	
		$(function(){
			var args = $.dialogAraguments() ;
			var price = args.content ;
			$("#price").val(price) ;
			
				$(".save-config").click(function(){
					var result =  $(".form-table").toJson() ;
					$.dialogReturnValue(result) ;
					window.close() ;
				}) ;
		}) ;
	</script>
  
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>策略配置</h2>
		</div>
		<div class="container-fluid">

				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content" style="padding:2px;">
						<table class="form-table" >
							<tbody>										   
								<tr>
									<th>选择策略</th>
									<td>
										<select>
											<option>定价</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>价格</th>
									<td>
										<input type="text" id="price" value=""/>
									</td>
								</tr>
							</tbody>
						</table>
						<!-- 数据列表样式 -->
						
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary save-config">确定</button>
						</div>
					</div>
				</div>
		</div>
	</div>
</body>
</html>