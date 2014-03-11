<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" " http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>列表选择页面</title>
	<?php 
	include_once ('../../../../config/config.php');
	?>
	<base target="_self"></base>
	<link  href="../layout/jquery.layout.css" type="text/css" rel="stylesheet"/>
	<link  href="../grid/jquery.llygrid.css" type="text/css" rel="stylesheet"/>
	<link  href="..//tree/jquery.tree.css" type="text/css" rel="stylesheet"/>
	<link  href="../../css/default/style.css" class="view-source" rel="stylesheet">
	<link  href="jquery.treegridselectdialog.template.css" class="view-source" rel="stylesheet">
	
	<script src="../jquery.js"  class="view-source" type="text/javascript"></script>
	<script src="../jquery.json.js"  class="view-source" type="text/javascript"></script>
	<script src="../common.js"  class="view-source" type="text/javascript"></script>
	<script src="../browserfix.js"  class="view-source" type="text/javascript" ></script>
	<script src="../jquery.json.js" type="text/javascript"></script>
	
	<script type="text/javascript" src="../layout/jquery.layout.js"></script>
	<script type="text/javascript" src="../tree/jquery.tree.js"></script>
	<script type="text/javascript" src="../grid/jquery.llygrid.js"></script>
	<script type="text/javascript" src="../dialog/jquery.dialog.js"></script>
	<script type="text/javascript" src="jquery.treegridselectdialog.template.js"></script>

</head>

<body class=" container-popup">
<!-- iframe start -->
	<div class="page-title">
		<h2></h2>
	</div>
	
	<div class="container-fluid">
		<!-- panel内容开始 -->
		<div class="panel form-panel">
			<div class="panel-head">
				<div class="row-fluid">
					<div class="span6 first">
					</div>
					<div class="span6">
					</div>
				</div>
				<a href="#" class="toggle"></a>
			</div>
			
			<div class="panel-content" style="overflow:hidden;">
					<div class="ui-layout" style="width:100%;height:100%;">
						<div region="west" split="true" title="树选择" class="tree-layout" style="width:150px;">
							<div class="tree-container"></div>
						</div>
						<div id="content" region="center" class="grid-layout" title="列表选择" style="padding:5px;">
							<div class="panel search-panel">
								<div class="panel-head">
									<div class="row-fluid">
										<div class="span6 first">
											<i class="icon-list-alt"></i>查询条件
										</div>
										<div class="span6">
										</div>
									</div>
									<a href="#" class="toggle"></a>
								</div>
								<div class="panel-content">
									<div class="toolbar toolbar-auto">
										<form class="form-inline" id="select-searchform" action="" data-widget="validator,grid-search" data-options="{gridId:'select-user-list'}">
										</form>
									</div>
								</div>
							</div>
							<div class="grid-container"></div>
						</div>
						<div id="south" region="south"   title="" style="padding:5px;height:120px;overflow:hidden;">
							<div class="selected-container">
								<ul>
								</ul>
							</div>
						</div>
					</div>	
			 </div>
			 
			 <!-- panel 内容结束 -->	
			<div class="panel-foot">
					<div class="form-actions">
						<button class="btn btn-primary confirm-btn">确定</button>
					    <button class="btn close-btn">关闭</button>
						<button class="btn clear-btn">清空全部</button>
					</div>
			</div>
		</div>
		<script type="text/javascript">
		$(function(){
			window.dialog = $(".select-supplier").getDialog() ;
		}); 
		</script>
	</div>
</body>
</html>