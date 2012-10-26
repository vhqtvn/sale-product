<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		
		
		
		$id = '' ;
		$name = '' ;
		$scripts = '[]' ;
		 if( $rule !=null){
			$id =$rule['Product']["ID"] ;
			$name =$rule['Product']["NAME"] ;
			$scripts =$rule['Product']["SCRIPTS"] ;
			if( $scripts == null ){
				$scripts = '[]' ;
			}
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
		var ruleScripts = <?php echo $scripts; ?> ;

		//load config data
		var fields = [] ;
		var relations = [] ;
		$.ajax({
			type:"get",
			url:"/saleProduct/index.php/grid/configitem",
			data:{start:0,limit:1000,curPage:0,end:0},
			cache:false,
			dataType:"text",
			success:function(result,status,xhr){
				if(typeof result == 'string'){
					eval("result = " +result) ;
				}
				var formatDate = window.formatGridData||function(data){ return data } ;
				result = formatDate(result) ;

				$(result.records).each(function(){
					if(this.TYPE == 'field'){
						fields.push(this) ;
					}else{
						relations.push(this) ;
					}
				}) ;
				
				//init
				if( ruleScripts && ruleScripts.length>0){
					$(ruleScripts).each(function(){
						$(".rule-content").append( addCondition(this) ) ;
					}) ;
				}
			}
		}); 

		$(".add-condition").live('click',function(){
			$(".rule-content").append( addCondition() ) ;
		}) ;

		$(".save-config").live('click',function(){
			var result = [] ;

			$(".rule-content-item").each(function(){
				var key	     = $(this).find(".field").val() ;
				var label	     =  $(this).find(".field option:selected").text() ;
				var relation = $(this).find(".relation").val() ;
				var relationLabel =  $(this).find(".relation option:selected").text() ;
				var val	     = $(this).find(".value").val() ;
				if( !key ) return ;
				if(!relation) return ;
				result.push({key:key,label:label,relation:relation,relationLabel:relationLabel,val:val}) ;
			}) ;

			var scripts = $.json.encode(result) ;
			var id	   = $("#ruleId").val() ;
			var name   = $("#ruleName").val() ;
			$.ajax({
				type:"post",
				url:"/saleProduct/index.php/product/saveScript",
				data:{id:id,name:name,scripts:scripts},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					window.opener.location.reload() ;
					window.close() ;
				}
			}); 
		}) ;

		$(".relation").live("change",function(){
			var val = $(this).val() ;
			var text =jQuery.trim( $(this).parents(".rule-content-item").find('.item-value input').val()) ;
			var html = getRenderInput( val ,text) ;
			$(this).parents(".rule-content-item").find('.item-value').html(html) ;
		})


		$(".del-action").live('click',function(){
			$(this).parents(".rule-content-item").hide(300).remove() ;
		}) ;


		function addCondition(ruleScript){
			ruleScript = ruleScript||{} ;

			var html = [] ;
			html.push('<div class="rule-content-item">') ;
			html.push('<div class="item-label">') ;
			html.push('<select class="field">')	 ;
			html.push('<option value="">-</option>')	 ;
			$(fields).each(function(){
				var _ = "" ;
				if(this.KEY == ruleScript.key ){
					_= "selected" ;
				}

				html.push('<option value="'+this.KEY+'" '+_+'>'+this.LABEL+'</option>')	 ;
			}) ;
			html.push('</select>')	 ;
			html.push('</div>') ;
			html.push('<div class="item-relation">') ;
			html.push('<select class="relation">')	 ;
			html.push('<option value="">-</option>')	 ;
			$(relations).each(function(){
				var _ = "" ;
				if(this.KEY == ruleScript.relation ){
					_= "selected" ;
				}

				html.push('<option value="'+this.KEY+'" '+_+'>'+this.LABEL+'</option>')	 ;
			}) ;
			html.push('</select>')	 ;
			html.push('</div>') ;
			html.push('<div class="item-value">') ;
			if( ruleScript.relation ){
				html.push( getRenderInput( ruleScript.relation,ruleScript.val)  );
			}
			html.push('</div>') ;
			html.push('<div class="item-action">') ;
			html.push('<button class="del-action">删除</button>') ;
			html.push('</div>') ;
			html.push('</div>')
			return html.join("") ;
		}
   </script>

</head>
<body>
	<div class="toolbar toolbar-auto">
		<table>
			<tr>
				<th>
					规则名称：
				</th>
				<td>
					<input type="text" id="ruleName"  value="<?php echo $name;?>"/>
					<input type="hidden" id="ruleId" value="<?php echo $id;?>"/>
				</td>								
				<td class="toolbar-btns">
					<button class="add-condition btn btn-primary">添加条件</button>
					<button class="save-config btn btn-primary">保存设置</button>
				</td>
			</tr>						
		</table>					

	</div>	
	
	<div class="rule-content">
	</div>
</body>

</html>