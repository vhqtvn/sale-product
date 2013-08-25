<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>Niche关键子开发</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('modules/keyword/nicheDev');
		
		$keywordId = $params['arg1'] ;
		$keyword  = ClassRegistry::init("Keyword") ;
		$kw = $keyword->getObject("d_sc_keyword_getById",array("id"=>$keywordId)) ;
//debug($kw) ;

		$canWrite =  $kw['status'] ==1 ;
	?>
  
</head>

<script>
	var keywordId = "<?php echo $keywordId;?>" ;
</script>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>开发信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="keyword_id" value="<?php echo $kw['keyword_id'] ;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content" style="padding-bottom:50px;">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<tbody>
								<tr>
									<th>关键字名称：</th>
									<td><?php echo $kw['keyword'] ;?></td>
									<th>关键字类型：</th>
									<td><?php echo $kw['keyword_type'] ;?></td>
								</tr>
								<tr>
									<th>搜索量：</th>
									<td><?php echo $kw['search_volume'] ;?></td>
									<th>CPC：</th>
									<td><?php echo $kw['cpc'] ;?></td>
								</tr>
								<tr>
									<th>竞争：</th>
									<td><?php echo $kw['competition'] ;?></td>
									<th>结果数：</th>
									<td><?php echo $kw['result_num'] ;?></td>
								</tr>
								<tr>
									<th>趋势：</th>
									<td colspan="3"><?php echo $kw['trends'] ;?></td>
								</tr>
								<tr>
									<th>开发负责人：</th><td colspan="3">
											<input type="hidden"   id="dev_charger"  class="40-input input"
											value="<?php echo $kw['dev_charger'];?>"/>
											<input type="text"  class="40-input input span2"  id="dev_charger_name"  readonly
													value="<?php echo  $kw['dev_charger_name'];?>"/>
											<button class="40-input input btn btn-charger">选择</button>
										</td>
								</tr>
								
								<tr>
									
									<th>价格范围：</th>
									<td><input id="dev_price_scope" 
									<?php echo $canWrite?"":"disabled" ;?> type="text" style="width:90%;"  value="<?php echo $kw['dev_price_scope'] ;?>"></td>
									<th>利润范围：</th>
									<td>
									<input id="dev_profile_scope" 
									<?php echo $canWrite?"":"disabled" ;?>  type="text" style="width:90%;" value="<?php echo $kw['dev_profile_scope'] ;?>"></td>
								</tr>
								
								<tr>
									<th>最佳销量排名：</th>
									<td colspan="3">
									<input id="dev_rank"
									<?php echo $canWrite?"":"disabled" ;?>  type="text"  style="width:90%;"  value="<?php echo $kw['dev_rank'] ;?>"></td>
								</tr>
								<tr>
									<th>搜索结果：</th>
									<td colspan="3"><textarea id="dev_search_result" 
									<?php echo $canWrite?"":"disabled" ;?> style="width:90%;height:40px;"><?php echo $kw['dev_search_result'] ;?></textarea></td>
								</tr>
								<tr>
									<th>有效竞争：</th>
									<td colspan="3"><textarea id="dev_competition"
									<?php echo $canWrite?"":"disabled" ;?> style="width:90%;height:40px;"><?php echo $kw['dev_competition'] ;?></textarea></td>
								</tr>
								<tr>
									<th>参考ASIN：</th>
									<td colspan="3"><textarea id="dev_asin"
									<?php echo $canWrite?"":"disabled" ;?> style="width:90%;height:40px;"><?php echo $kw['dev_asin'] ;?></textarea></td>
								</tr>
								<tr>
									<th>开发重要性：</th>
									<td colspan="3"><textarea id="dev_important"
									<?php echo $canWrite?"":"disabled" ;?>
									 style="width:90%;height:40px;"><?php echo $kw['dev_important'] ;?></textarea></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot" style="background-color:#FFF;">
						<div class="form-actions">
							<?php
							if( $kw['status'] == 1 ){ ?>
								<button type="button" class="btn  save-niche">保存</button>
							&nbsp;
							<button type="button" class="btn btn-primary pass-niche">提交审批</button>
							&nbsp;
							<button type="button" class="btn btn-danger  discart-niche">废弃</button>
							<?php } else if($kw['status'] == 2){
								echo "<div>审批通过</div>" ;
							} else if($kw['status'] == 3){
								echo "<div>已经废弃</div>" ;
							}else{
								echo "正在开发中....." ;
							} ?>
							
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>