<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>需求问题编辑</title>
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
		echo $this->Html->script('modules/suggest/editSuggest');
		
		$u = null ;

		$suggestId = $params['arg1'] ;
		$Utils  = ClassRegistry::init("Utils") ;
		if( !empty($suggestId) )
		$u = $Utils->getObject("sql_suggest_getById",array('id'=>$suggestId)) ;
		
		
	?>
  
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>需求问题编辑</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="id" value="<?php echo $u['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>类型：</th>
									<td>
										<select name="type"   data-validator="required">
											<option value="">-选择-</option>
											<option value="1" 
											<?php echo $u['TYPE']=='1'?'selected':'' ;?>
											>需求</option>
											<option value="2" <?php echo $u['TYPE']=='2'?'selected':'' ;?> >问题</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>标题：</th>
									<td><input type="text"  data-validator="required"
										id="title" value="<?php echo  $u['TITLE'];?>"/></td>
								</tr>
								<?php
									if(!empty($suggestId)){
								?>
								<tr>
									<th>状态：</th>
									<td>
										<select name="status" >
											<option value="0"  <?php echo $u['STATUS']=='0'?'selected':'' ;?>>未处理</option>
											<option value="1"  <?php echo $u['STATUS']=='1'?'selected':'' ;?>>已处理</option>
											<option value="2"  <?php echo $u['STATUS']=='2'?'selected':'' ;?>>暂不处理</option>
										</select>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<th>备注：</th><td>
										<textarea name="memo" style="width:80%;height:130px;"><?php echo  $u['MEMO'];?></textarea>
									</td>
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