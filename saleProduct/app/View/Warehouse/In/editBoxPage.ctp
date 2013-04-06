<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>包装箱编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('calendar/WdatePicker');
	
	?>
	<script type="text/javascript">
   	var inId = '<?php echo $params['arg1'] ;?>' ;	 
   </script>
	<script>
	$(function(){
		var isSaved = false ;
		$(".btn-save").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					if(isSaved) return ;
					var json = $("#personForm").toJson() ;
					isSaved = true ;
					$.dataservice("model:Warehouse.In.doSaveBox",json,function(result){
						window.close();
					});

				};
				return false ;
			}) ;
	});
   </script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>包装箱信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        <input type="hidden" id="id" value="<?php echo $result['ID'];?>"/>
	        <input type="hidden" id="IN_ID" value="<?php echo $params['arg1'] ;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<tbody>										   
								<tr>
									<th>包装箱发货编码：</th>
									<td><input type="text" data-validator="required" id="BOX_NUMBER" value="<?php echo $result['BOX_NUMBER'];?>"/></td>
								</tr>
								<tr>
									<th>物流费用：</th><td><input data-validator="required" type="text" id="SHIP_FEE"
										value="<?php echo $result['SHIP_FEE'];?>"/></td>
								</tr>
								<tr>
									<th>重量：</th><td><input data-validator="required" type="text" id="WEIGHT"
										value="<?php echo $result['WEIGHT'];?>"/></td>
								</tr>
								<tr>
									<th>尺寸(长X宽X高)：</th>
									<td>
									<input type="text" id="LENGTH" data-validator="required" class="span1"
										value="<?php echo $result['LENGTH'];?>"/>
									X
									<input type="text" id="WIDTH" data-validator="required" class="span1"
										value="<?php echo $result['WIDTH'];?>"/>
									X
									<input type="text" id="HEIGHT" data-validator="required" class="span1"
										value="<?php echo $result['HEIGHT'];?>"/>
									</td>
								</tr>
						
								<tr>
									<th>备注：</th><td>
										<textarea name="MEMO" id="MEMO"  style="width:90%;height:100px;"><?php echo $result['MEMO'];?></textarea>
									</td>
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