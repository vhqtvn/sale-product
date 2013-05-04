<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品开发</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		 include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/validator/jquery.validation');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$taskId = $params['arg1'] ;
	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
 </style>
 
 <script type="text/javascript">
	$(function(){
			$(".btn-save").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					if(window.confirm("确认保存吗？")){
						var json = $("#personForm").toJson() ;
						
						$.dataservice("model:ProductDev.addAsinToTask",json,function(result){
							window.location.reload();
						});
					}
				}
				
			}) ;
	}) ;
 </script>
 
</head>
<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>添加ASIN</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
				<input type="hidden" id="taskId" value="<?php echo $taskId;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<tbody>	
								<tr>
									<th>ASIN（逗号分隔）：</th>
									<td><textarea id="asins" data-validator="required" style="width:95%;height:200px;"></textarea></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="button" class="btn btn-primary btn-save">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>

</html>