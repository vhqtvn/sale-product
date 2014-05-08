<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>计算需求设置</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		 include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/dialog/jquery.dialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('calendar/WdatePicker');
		
		$accountProductId = $params['arg1'] ;
		$isRisk =  $params['arg2'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		
	?>
	
	<script>
		$(function(){
			 $.dialogReturnValue(false) ;
			$(".save-risk").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					
					$.dataservice("model:Amazonaccount.saveProductRisk",json,function(result){
						 $.dialogReturnValue(true) ;
						 $("#personForm").dialogClose() ;
					});
				}
			})
		}) ;
	</script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>设置风险</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="id" value="<?php echo $accountProductId;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<tbody>
								<tr>
									<th>是否计算需求：</th>
									<td>
										计算需求<input type="radio"  name="isAnalysis" value="1"/>
										不计算需求<input type="radio"  name="isAnalysis" value="0"/>
									</td>
								</tr>	
								<?php /*								   
								<tr>
									<th>是否存在风险：</th>
									<td>
											<select  name="isRisk">
													<option value="">-</option>
													<option value="1">存在风险</option>
													<option value="2">不存在风险</option>
											</select>
									</td>
								</tr>*/?>	
								<tr>
									<th>类型：</th>
									<td>
										<?php 
											$items = $SqlUtils->exeSqlWithFormat("select * from sc_config where type= 'riskType'",array()) ;
										?>
											<select  name="riskType">
													<option value="">-</option>
													<?php  foreach( $items as $item ){ ?>
															<option value="<?php echo $item['KEY']?>"><?php echo $item['LABEL']?></option>
													<?php  } ?>
													
										    </select>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary save-risk">保&nbsp;存</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>