<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		
		echo $this->Html->css('../js/validator/jquery.validation');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		
		echo $this->Html->script('validator/jquery.validation');
	?>

	<script>
		$(function(){
			$("button").click(function(){

				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					
					$.dataservice("model:Seller.saveSeller",json,function(result){
							window.close();
					});
				}
			}) ;
		}) ;
	</script>

   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body class="container-body">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>商家信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>商家信息</caption>
							<tbody>										   
								<tr>
									<th><label>商家名称：</label></th>
									<td>
									<input type="text"  data-validator="required" name="name" class="input-large">
									</td>
                                </tr>
                                <tr>
									<th>所属平台</th>
									<td>
									<select name="platformId"  data-validator="required"  class="input 10-input" >
										<option value="">--选择平台--</option>
										<?php 
											$SqlUtils  = ClassRegistry::init("SqlUtils") ;
											$strategys = $SqlUtils->exeSql("sql_platform_list",array()) ;
											foreach( $strategys as $s){
												$s = $SqlUtils->formatObject($s) ;
												$selected = '' ;
												if( $s['ID'] == $account[0]['sc_amazon_account']['PLATFORM_ID'] ){
													$selected = "selected" ;
												}
												echo "<option $selected value='".$s['ID']."'>".$s['NAME']."</option>" ;
											}
										?>
										</select>
									</td>
								</tr>
                                <tr>
									<th><label>商家地址：</label></th>
									<td><input type="text" name="url"  data-validator="required"  class="input-large"></td>
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
