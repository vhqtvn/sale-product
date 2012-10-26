<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
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
		
		
		$type = $plan[0]['sc_purchase_plan']['TYPE'] ;
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
			
			$(".btn-primary").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
				
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/sale/savePurchasePlan",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.location.reload() ;
							window.close() ;
						}
					}); 
				};
				return false ;
			}) ;
			
			$(".add-on").click(function(){
				openCenterWindow("/saleProduct/index.php/users/selectUsers",600,400) ;
			}) ;
			
			$( "#plan_time" ).datepicker({dateFormat:"yy-mm-dd"});
		}) ;
		
		function addUser(user){
			$("#executor").val(user.NAME) ;
			$("#executor_id").val(user.LOGIN_ID) ;
		}
   </script>

</head>
<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>创建采购计划</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
			<input id="id" type="hidden" value="<?php echo $plan[0]['sc_purchase_plan']['ID']?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>基本信息</caption>
								<tr>
									<td>采购名称：</td><td><input type="text" data-validator="required" id="name"  
										value="<?php echo $plan[0]['sc_purchase_plan']['NAME']?>" style="width:300px;"/></td>
								</tr>
								<tr>
									<td>备注：</td><td><textarea id="memo" style="width:300px;height:100px;"
										><?php echo $plan[0]['sc_purchase_plan']['MEMO']?></textarea></td>
								</tr>
								<tr>
									<td>计划采购时间：</td>
									<td><input id="plan_time" type="text" value="<?php echo $plan[0]['sc_purchase_plan']['PLAN_TIME']?>"/>
									</td>
								</tr>
								<tr>
									<td>用途：</td>
									<td>
										<select id="type">
											<option value="">-</option>
											<option value="1" <?php if($type == 1) echo 'selected';?>>试销</option>
											<option value="2" <?php if($type == 2) echo 'selected';?>>正式采购</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>执行人：</td>
									<td>
										<input id="executor_id" type="hidden" value="<?php echo $plan[0]['sc_purchase_plan']['EXECUTOR']?>"/>
										<input id="executor" type="text" value="<?php echo $plan[0][0]['EXECUTOR_NAME']?>"/> 
										<button class="btn add-on">选择用户</button>
									</td>
								</tr>
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
</html>