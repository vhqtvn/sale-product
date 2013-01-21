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
		$(function(){
			$("button").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
				
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/users/saveFunctoin",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.openCallback() ;
							window.close() ;
						}
					}); 
				}
			})
		})
   </script>

</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>功能信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
			<input type="hidden" id="id" value="<?php echo $function[0]['sc_security_function']['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>功能名称：</th><td><input type="text" id="name"   data-validator="required"
										value="<?php echo $function[0]['sc_security_function']['NAME'];?>"/></td>
								</tr>
								
								<tr>
									<th>功能类别：</th><td>
										<select id="type"   data-validator="required">
											<?php
												if( $function[0]['sc_security_function']['TYPE'] == 'MENU'){
													echo "<option value='MENU' selected>菜单</option>
													<option value='FUNCTION'>功能</option>";
												}else if( $function[0]['sc_security_function']['TYPE'] == 'FUNCTION'){
													echo "<option value='MENU' >菜单</option>
													<option value='FUNCTION' selected>功能</option>";
												}else{
													echo "<option value='' >-</option><option value='MENU' >菜单</option>
													<option value='FUNCTION'>功能</option>";
												}
											?>
											
										</select>
									</td>
								</tr>
								<tr>
									<th>父编号：</th><td><input type="text" id="parentId" 
										value="<?php echo  $function[0]['sc_security_function']['PARENT_ID'];?>"/></td>
								</tr>
								<tr>
									<th>功能编码：</th><td><input type="text" id="code"  data-validator="required"
										value="<?php echo  $function[0]['sc_security_function']['CODE'];?>"/></td>
								</tr>
								<tr>
									<th>功能URL：</th><td><input type="text" id="url" 
										value="<?php echo  $function[0]['sc_security_function']['URL'];?>"/></td>
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