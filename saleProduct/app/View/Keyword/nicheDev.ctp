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
   		echo $this->Html->css('../js/grid/jquery.llygrid');
   		echo $this->Html->css('../js/tab/jquery.ui.tabs');
   		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
   		
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
   		echo $this->Html->script('modules/keyword/nicheDev');
		
		$keywordId = $params['arg1'] ;
		$keyword  = ClassRegistry::init("Keyword") ;
		$kw = $keyword->getObject("d_sc_keyword_getById",array("id"=>$keywordId)) ;

		$canWrite = empty($kw['status']) ||  $kw['status'] <=0 || $kw['status'] ==10 || $kw['status'] == 20 ;
		

		$security  = ClassRegistry::init("Security") ;
		$loginId = $user['LOGIN_ID'] ;
		
		$audit_niche 								= $security->hasPermission($loginId , 'audit_niche') ;
		$assign_kw_charger                      = $security->hasPermission($loginId , 'assign_kw_charger') ;//回退
		$kw_relation_product				= $security->hasPermission($loginId , 'kw_relation_product') ;
		$add_kw_plan						= $security->hasPermission($loginId , 'add_kw_plan') ;
		$add_kw_task							= $security->hasPermission($loginId , 'add_kw_task') ;
		$niche_kw_dev							= $security->hasPermission($loginId , 'niche_kw_dev') ;
		
		//获取所有的关联ASIN $keywordId
		$asins = $keyword->exeSqlWithFormat("select * from sc_keyword_asin where keyword_id = '{@#keywordId#}'" , array("keywordId"=>$keywordId)) ;
		
		$siteMap = array( 
	"us"=>"www.amazon.com",
	"uk"=>"www.amazon.co.uk",
	"ca"=>"www.amazon.ca",
	"ru"=>"www.amazon.ru",
	"de"=>"www.amazon.de",
	"fr"=>"www.amazon.fr",
	"es"=>"www.amazon.es",
	"it"=>"www.amazon.it",
	"br"=>"www.amazon.br",
	"au"=>"www.amazon.com.au",
	"us.bing"=>"www.amazon.com" ) ;
	
	$siteUrl = $siteMap[ $kw['site'] ] ;		

	?>
</head>

   <style type="text/css">
		.flow-node{
			min-width:50px; 
			height:20px; 
			border:5px solid #0FF; 
			border-radius:5px;
			font-weight:bold;
		}
		
		.flow-node.active{
			border-color:#3809F7 ;
			background-color:#3809F7 ;
			color:#EEE;
		}
		
		.flow-node.passed{
			border-color:#92E492 ;
			background-color:#92E492 ;
			
		}
		
		.flow-node.termination{
			color:red;
	        background-color:pink ;
			border-color:pink;
		    white-space: nowrap;
		}
		
		.flow-node.disabled{
			border-color:#CCC ;
			background-color:#CCC ;
			color:#EEE;
		}
		
		.flow-table{
			text-align:center;
			
		}
		
		.flow-bar{
			width:100%;margin:3px auto;text-align:center;
			position:fixed;
			left:0px;
			right:0px;
			top:0px;
			height:80px;
			z-index:1000;
			background: #FFF;
		}
		
		body{
			padding-top:78px;
		}
		
		.flow-action{
			position:absolute;;
			right:10px;
			top:48px;
			z-index:100;
		}
		
		.flow-split{
			font-size:30px;
		}
		
		.memo{
			position:absolute;
			top:85px;
			z-index:1;
			right:10px;
			width:300px;
			height:50px;
			background:#ffd700;
			display:none;
		}
		
		.memo-control{
			display:none;
		}
		
		.tag-container li{
			float:left;
			list-style: none;
			margin:2px 5px;
		 	padding:2px;
		}
		
		.asin-ul{
			list-style: none;
			margin:0px;
		}
		.asin-ul li{
			float:left;
			margin:2px 3px;
			padding:2px 5px;
		}
		
		.asin-ul li input{
			width:110px;
		}
		
		.asin-action-li{
			display:none;
		}
		
		.link-to-product{
			cursor:pointer;
		}
		
		.asin-span{
			margin:0px!important;
		}
	</style>

<script>
	var keywordId = "<?php echo $keywordId;?>" ;
	var keyword   = "<?php echo $kw['keyword'];?>" ;
	var site =  "<?php echo $kw['site'];?>" ;


	function AuditAction(status , statusLabel){
		if(window.confirm("确认【"+statusLabel+"】？")){
			var memo = "("+statusLabel+")" + ($(".memo").val()||"");
			
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					json.status = status ;
					json.memo = memo ;
					$.dataservice("model:Keyword.saveNiceDev",json,function(){
						//执行状态更新
						window.location.reload();
					}) ;
				}
		}
	}

	function ForceAuditAction(status , statusLabel){
		if(window.confirm("确认【"+statusLabel+"】？")){
					var memo = "("+statusLabel+")" + ($(".memo").val()||"");
					var json = $("#personForm").toJson() ;
					json.status = status ;
					json.memo = memo ;
					$.dataservice("model:Keyword.saveNiceDev",json,function(){
						//执行状态更新
						window.location.reload();
					}) ;
		}
	}

	 var flowData = [
	        		{status:10,label:"开发中",memo:true
	        			,actions:[
		      	        	<?php if( $niche_kw_dev ){?>
								{label:"保存",action:function(){ ForceAuditAction(10,"保存") }},
								{label:"提交审批",action:function(){ AuditAction(20,"提交审批") }},
								{label:"废弃",action:function(){ AuditAction(15,"废弃") }}
							<?php }?>
        				]
	        		},
	        		{status:20,label:"审批",memo:true
	        			,actions:[
<?php if( $audit_niche ){?>
									{label:"保存",action:function(){ ForceAuditAction(20,"保存") }},
									{label:"审批通过",action:function(){ AuditAction(30,"审批通过") }},
									{label:"废弃",action:function(){ AuditAction(15,"废弃") }}
									<?php }?>
        				]
	        		},
	        		{status:30,label:"分配负责人",memo:true
	        			,actions:[
<?php if( $assign_kw_charger ){?>
									{label:"保存",action:function(){ ForceAuditAction(30,"保存") }},
									{label:"保存责任人",action:function(){ AuditAction(40,"保存责任人") }}
									<?php }?>
        				]
	        		},
	        		{status:40,label:"关联开发产品",memo:true
	        			,actions:[
<?php if( $kw_relation_product){?>
									{label:"保存",action:function(){ ForceAuditAction(40,"保存") }},
									{label:"结束开发",action:function(){ AuditAction(50,"结束开发") }}
									<?php }?>
    					]
	        		},
	        		{status:50,label:"结束"}
	        	] ;

	 $(function(){
			var flow = new Flow() ;
			flow.init(".flow-bar center",flowData) ;
			flow.draw(<?php echo $kw['status'];?>) ;
	});
	 
</script>

<body class="container-popup">
	<div  class="flow-bar">
		<center>
			<table class="flow-table">
				
			</table>
			<div class="flow-action">
			</div>
		</center>
	</div>
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="container-fluid">
			<div id="details_tab"></div>
			<div id="base-info">
	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="keyword_id" value="<?php echo $kw['keyword_id'] ;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
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
													<?php if( $assign_kw_charger && $kw['status'] == 30  ){?>
											<button class="40-input input btn btn-charger">选择</button>
											<?php }?>
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
									<td colspan="1">
									<input id="dev_rank"
									<?php echo $canWrite?"":"disabled" ;?>  type="text"  style="width:90%;"  value="<?php echo $kw['dev_rank'] ;?>"></td>
									<th>开发重要级别：</th>
									<td colspan="1">
										<select id="dev_level">
											<option value="">选择开发按级别</option>
											<option value="1"  <?php echo $kw['dev_level']==1?"selected":"" ;?>>1</option>
											<option value="2"  <?php echo $kw['dev_level']==2?"selected":"" ;?>>2</option>
											<option value="3"  <?php echo $kw['dev_level']==3?"selected":"" ;?>>3</option>
										</select>
									</td>
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
									<th>参考ASIN&nbsp;<?php if( $canWrite ){ ?><a href="#" class="add-asin no-disabled">添加</a><?php } ?>：</th>
									<td colspan="3">
										<ul  class="asin-ul">
												<?php if( $canWrite ){?>
												<li class="asin-action-li"><button class="btn save-sain">保存</button></li>
												<?php 
													}
													$isDisabled = $canWrite?"":"disabled" ; 
													$img = $canWrite?"<img class='delete-asin' src='/".$fileContextPath."/app/webroot/img/delete.gif'/>":"" ;
													if( $kw['status'] == 40 ){ //关联开发产品
														//$img .= "<img class='link-to-product' src='/".$fileContextPath."/app/webroot/img/config.gif'/>" ;
													}
													
													
													foreach( $asins as $asin ){
														$_asin = $asin['asin'] ;
														//获取是否已经关联账号产品
														$obj = $keyword->getObject("d_getAsinDetailsByASIN",array("asin"=>$_asin)) ;
														
														if( empty($obj) ){ 
															echo  "<li><span class='asin-span alert'>
																<a target='_blank' href='http://$siteUrl/gp/product/".$_asin."'>".$_asin."</a>$img" ;
															echo "未关联开发产品！" ;
															echo "</span></li>" ;
														}else{
															
															echo  "<li><span class='asin-span alert alert-success'>
																<a target='_blank' href='http://$siteUrl/gp/product/".$_asin."'>".$_asin."(".$obj['SKU'].")</a>$img" ;
															
															if( $obj['REAL_SKU'] ){
																echo '<a data-widget="dialog" data-options="{width:1000,height:650}" href="/'.$fileContextPath.'/index.php/saleProduct/details/'.$obj['REAL_SKU'].'/sku">' ;
																if( $obj['IMAGE_URL'] ){
																	echo "&nbsp;<img src='/".$fileContextPath."/".$obj['IMAGE_URL'] ."' style='width:20px;height:16px;'>" ;
																}
																echo $obj['REAL_SKU'] ;
																echo "</a>" ;
															}else{
																echo "暂未关联到货品" ;
															}
															
															echo "</span></li>" ;
														}
														
													}
												?>
												
										</ul>
									</td>
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
				</div>
			</form>
			</div>
			<div id="groupKeyword">
				<div class="niche-grid-group" style="width:880px;"></div>
			</div>
			<div id="tracks">
				<div class="grid-track" style="width:880px;"></div>
			</div>
			<div id="searchTerm">
					
					<div class="toolbar toolbar-auto">
						<table style="width:100%;" class="query-table">	
							<tr>
								<td>
									<button class="btn btn-primary  getSearchTerm"> 更新Search Term</button>
								</td>
							</tr>						
						</table>
					</div>
					<div class="grid-term" style="width:880px;"></div>
			</div>
		</div>
	</div>
</body>
</html>