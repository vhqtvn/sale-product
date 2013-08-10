<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>Ebay消息明细</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('modules/users/edit_user');
		
		$messageID = $params['arg1'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$message = $SqlUtils->getObject("sql_ebay_message_getByMessageId",array('MessageID'=>$messageID)) ;
		//debug($message) ;
	?>
  
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<caption>主题：<?php echo $message['Subject']?> （MessageID:<?php echo $message['MessageID']?>）</caption>
							<tbody>
								<tr>
									<th>发送方(Sender)：</th>
									<td><?php echo $message['Sender']?></td>
									<th>接收方(Receiver)：</th>
									<td><?php echo $message['SendToName']?></td>
								</tr>
								<tr>
									<th>是否已读：</th>
									<td><?php echo $message['LOCAL_SREAD']=="true"?"是":"否";
										if( $message['LOCAL_SREAD']=="true" ){
											echo $message['SRead']=="true"?"(已上传)":"(未上传)" ;
										}
									?></td>
									<th>是否已标记：</th>
									<td><?php echo $message['LOCAL_FLAGGED']=="true"?"是":"否";
									if( $message['LOCAL_FLAGGED']=="true" ){
										echo $message['Flagged']=="true"?"(已上传)":"(未上传)" ;
									}
									?></td>
								</tr>
								<tr>
									<th>是否回复：</th>
									<td><?php echo $message['ResponseEnabled']=="true"?"":"不需回复";
										if( $message['ResponseEnabled']=="true" ){
											echo $message['LOCAL_REPLIED']=="true"?"已回复":"未回复" ;
											if( $message['LOCAL_REPLIED']=="true" ){
												echo $message['Replied']=="true"?"(已上传)":"(未上传)" ;
											}
										}
									?></td>
									<th>Item ID：</th>
									<td><?php echo $message['ItemID']  ?></td>
								</tr>
								<tr>
									<th>接收时间：</th>
									<td><?php echo $message['ReceiveDate'] ;?></td>
									<th>过期时间：</th>
									<td><?php echo $message['ExpirationDate'] ;?></td>
								</tr>
								<tr>
									<td colspan="4">
									<textarea id="textText" style="display:none;"><?php echo $message['Text']?></textarea>
									<iframe id="textFrm" name="textFrm" frameborder="0" style="width:100%;height:400px;"></iframe>
									<script type="text/javascript">
									textFrm.document.write( textText.value );
									</script>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
		</div>
	</div>
</body>
</html>