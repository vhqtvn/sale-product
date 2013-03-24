<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title></title>
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
	?>
	


   <script>
		$(function(){
			var orderId = '<?php echo $orderId; ?>' ;
			var orderItemId = '<?php echo $orderItemId; ?>' ;
			var action = '<?php echo $action; ?>' ;

			$("button").click(function(){
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;

					json.action = action ;
					json.orderId = orderId ;
					json.orderItemId = orderItemId ;
					json.type = json.type||"" ;
					$.ajax({
						type:"post",
						url:contextPath+"/order/saveRedoOrder",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							//window.opener.location.reload() ;
							alert("保存成功");
							window.close() ;
						}
					}); 
				};
			})
		})
   </script>
</head>
<body class="container-popup">
	<?php if($action == 1){//退货?>
		<!-- apply 主场景 -->
		<div class="apply-page">
			<!-- 页面标题 -->
			<div class="page-title">
				<h2>退货</h2>
			</div>
			<div class="container-fluid">
	
		        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
		        	<input type="hidden" id="actionType" value="1"/>
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table col2" >
								<tbody>	
									<tr>
										<th>退货类型：</th>
										<td>
											<select id="type">
												<option value="">--</option>
												<option value="1">质量退货</option>
												<option value="2">发错退货</option>
												<option value="3">物流退货</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>退货原因：</th>
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
	<?php }else if($action ==2){//退款?>
		<!-- apply 主场景 -->
		<div class="apply-page">
			<!-- 页面标题 -->
			<div class="page-title">
				<h2>退款</h2>
			</div>
			<div class="container-fluid">
	
		        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
		        	<input type="hidden" id="actionType" value="2"/>
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table col2" >
								<tbody>	
									<tr>
										<th>退款类型：</th>
										<td>
											<select id="type">
												<option value="">--</option>
												<option value="1">质量退款</option>
												<option value="2">发错退款</option>
												<option value="3">物流退款</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>退款原因：</th>
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
	<?php }else if($action==3){?>
		<!-- apply 主场景 -->
		<div class="apply-page">
			<!-- 页面标题 -->
			<div class="page-title">
				<h2>重发货</h2>
			</div>
			<div class="container-fluid">
	
		        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
		        	<input type="hidden" id="actionType" value="3"/>
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table col2" >
								<tbody>	
									<tr>
										<th>重发货类型：</th>
										<td>
											<select id="type">
												<option value="">--</option>
												<option value="1">质量重发货</option>
												<option value="2">发错重发货</option>
												<option value="3">物流重发货</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>重发货原因：</th>
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
	<?php }else if($action==4){?>
		<!-- apply 主场景 -->
		<div class="apply-page">
			<!-- 页面标题 -->
			<div class="page-title">
				<h2>售后管理</h2>
			</div>
			<div class="container-fluid">
	
		        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
		        	<input type="hidden" id="actionType" value="4"/>
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table col2" >
								<tbody>	
									<tr>
										<th style="200px">售后类型：</th>
										<td>
											<select id="type">
												<option value="">--</option>
												<option value="1">差评</option>
												<option value="2">品质</option>
												<option value="3">物流</option>
												<option value="4">库存</option>
												<option value="5">促销</option>
												<option value="6">邀请好评</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>原因：</th>
										<td><textarea id="memo" style="width:300px;height:100px;"></textarea></td>
									</tr>
									<tr>
										<th>解决方法：</th>
										<td><textarea id="resolver" style="width:300px;height:100px;"></textarea></td>
									</tr>
									<tr>
										<th>是否结束售后服务：</th>
										<td><input type="checkbox" id="isEndService" name="isEndService" value="1"/></td>
									</tr>
									<tr class="alert alert-danger">
										<th>是否风险客户：</th>
										<td><input type="checkbox" id="isDangerUser" name="isDangerUser" <?php if($isDangerUser) echo 'checked' ;?> value="1"/></td>
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
	<?php }else if($action==7){ //邀请好评?>
		<!-- apply 主场景 -->
		<div class="apply-page">
			<!-- 页面标题 -->
			<div class="page-title">
				<h2>邀请好评</h2>
			</div>
			<div class="container-fluid">
	
		        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
		        	<input type="hidden" id="actionType" value="7"/>
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table col2" >
								<tbody>
									<tr>
										<th>原因：</th>
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
	<?php }?>
	
</body>
</html>