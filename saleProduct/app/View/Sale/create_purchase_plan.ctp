<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>编辑采购计划</title>
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
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		
		
		$type = $plan[0]['sc_purchase_plan']['TYPE'] ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$defaultCode = $plan[0]['sc_purchase_plan']['CODE'] ;
		if( empty($plan[0]['sc_purchase_plan']['CODE']) ){
			$index = $SqlUtils->getMaxValue("PP" , null , 1) ;
			if( strlen($index) < 5 ){
				$len = 5-strlen($index) ;
				for($i=0 ;$i < $len ;$i++){
					$index = '0'.$index ;
				}
			}
			$defaultCode = "PP"."-".date("ymd").'-'.$index ;
		}
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
			var __ =false ;
			$(".btn-primary").click(function(){
				if(__) return ;
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					__ = true ;
					$.dataservice("model:Sale.savePurchasePlan",json,function(){
						window.close();
					}) ;
				
				};
				return false ;
			}) ;
			
				var chargeGridSelect = {
						title:'用户选择页面',
						defaults:[],//默认值
						key:{value:'LOGIN_ID',label:'NAME'},//对应value和label的key
						multi:false,
						width:600,
						height:560,
						grid:{
							title:"用户选择",
							params:{
								sqlId:"sql_user_list_forwarehouse"
							},
							ds:{type:"url",content:contextPath+"/grid/query"},
							pagesize:10,
							columns:[//显示列
								{align:"center",key:"ID",label:"编号",width:"20%"},
								{align:"center",key:"LOGIN_ID",label:"登录ID",sort:true,width:"30%"},
								{align:"center",key:"NAME",label:"用户姓名",sort:true,width:"36%"}
							]
						}
				   } ;
				   
				$(".add-on").listselectdialog( chargeGridSelect,function(){
					var args = jQuery.dialogReturnValue() ;
					var value = args.value ;
					var label = args.label ;
					//$("#executor").val(value) ;
					//$("#executorName").val(label) ;

					$("#executor").val(label) ;
					$("#executorId").val(value) ;
					return false;
				}) ;
		}) ;
		
		function addUser(user){
			$("#executor").val(user.NAME) ;
			$("#executorId").val(user.LOGIN_ID) ;
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
						<table class="form-table" >
							<caption>基本信息</caption>
								<tr>
									<th>采购编号：</th><td><input type="text"  disabled id="code"  
										value="<?php echo $defaultCode?>" style="width:300px;"/></td>
								</tr>
								<tr>
									<th>采购名称：</th><td><input type="text" data-validator="required" id="name"  
										value="<?php echo $plan[0]['sc_purchase_plan']['NAME']?>" style="width:300px;"/></td>
								</tr>
								
								<tr>
									<th>计划采购时间：</th>
									<td><input id="planTime" class="span2"  data-validator="required" data-widget="calendar" type="text" value="<?php echo $plan[0]['sc_purchase_plan']['PLAN_TIME']?>"/>到
									<input id="planEndTime" class="span2"   data-validator="required" data-widget="calendar" type="text" value="<?php echo $plan[0]['sc_purchase_plan']['PLAN_END_TIME']?>"/>
									</td>
								</tr>
								<tr>
									<th>用途：</th>
									<td>
										<select id="type">
											<option value="">-</option>
											<option value="1" <?php if($type == 1) echo 'selected';?>>试销</option>
											<option value="2" <?php if($type == 2) echo 'selected';?>>正式采购</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>负责人：</th>
									<td>
										<input id="executorId" type="hidden" value="<?php echo $plan[0]['sc_purchase_plan']['EXECUTOR']?>"/>
										<input id="executor" data-validator="required"  type="text" value="<?php echo $plan[0][0]['EXECUTOR_NAME']?>"/> 
										<button class="btn add-on">选择用户</button>
									</td>
								</tr>
								<tr>
									<th>备注：</th><td><textarea id="memo" style="width:300px;height:100px;"
										><?php echo $plan[0]['sc_purchase_plan']['MEMO']?></textarea></td>
								</tr>
							</table>
						</div>
<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</html>