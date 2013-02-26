<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>用户组分配权限</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../kissu/widgets/core/tree/ui.tree');

		echo $this->Html->script('../kissu/scripts/jquery');
		echo $this->Html->script('../kissu/scripts/jquery.utils');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../kissu/widgets/core/tree/jquery.tree');

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
    var treeData = {id:"root",text:"功能树",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;
    var itemCache = [] ;
    var code     = '<?php echo $GroupCode;?>'
    <?php
    	$index = 0 ;
		foreach( $Functions as $Record ){
			$sfs = $Record['sc_security_function']  ;
			$selected =  $Record[0]['selected'] ;
			
			$id   = $sfs['ID'] ;
			$code = $sfs['CODE'] ;
			$name = $sfs['NAME'] ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'$code',text:'$name',pid:'$pid'} ;" ;
			
			if( !empty($selected) ){
				echo " item$index ['checkstate'] = 1 ;" ;
			}
			echo " itemCache.push( item$index ) ;" ;
			
			echo " treeMap['id_$id'] = item$index  ;" ;
			if(empty($pid)){
				echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
				echo "treeData.childNodes.push( item$index ) ;" ;
			}else{
				//echo " treeMap['id_$pid'].childNodes.push( item$index ) ;" ;
			}
			$index++ ;
		} ;
		
		foreach( $filterRules as $Record  ){
			$sfs = $Record['sc_security_function']  ;
			$selected =  $Record[0]['selected'] ;
			
			$id   = $sfs['ID'] ;
			$code = $sfs['CODE'] ;
			$name = $sfs['NAME'] ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'r___$code',text:'$name',pid:'$pid'} ;" ;
			
			if( !empty($selected) ){
				echo " item$index ['checkstate'] = 1 ;" ;
			}
			echo " itemCache.push( item$index ) ;" ;
			
			echo " treeMap['id_r___$id'] = item$index  ;" ;
			if(empty($pid)){
				echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
				echo "treeData.childNodes.push( item$index ) ;" ;
			}else{
				//echo " treeMap['id_$pid'].childNodes.push( item$index ) ;" ;
			}
			$index++ ;
		}
		
		$amazonAccount  = ClassRegistry::init("Amazonaccount") ;
		$accounts = $amazonAccount->getAllAccounts(); 
	?>
	 var accounts = [] ;
    <?php 
    	
		foreach( $accounts as $Record ){
			$sfs = $Record['sc_amazon_account']  ;
			$aid   = $sfs['ID'] ;
			$name  = $sfs["NAME"] ;
			$security = $accountSecuritys[$aid] ;
			$security1 = $accountSecuritys1[$aid] ;
			
			$selected1 = '0' ;
			if( !empty($security1) ){
				$selected1 = '1' ;
			}
			
			echo " var account_$aid = {id:'a___$aid',accountId:'$aid',text:'$name',checkstate:$selected1,childNodes:[]} ;" ;
			echo " accounts.push(account_$aid ) ;" ;
			
			foreach( $security as $Record1 ){
				$sfs1 = $Record1['sc_security_function']  ;
				$selected =  $Record1[0]['selected'] ;
				if(empty($selected))
					$selected = '0' ;
				
				$id   = $sfs1['ID'] ;
				$code = $sfs1['CODE'] ;
				$name = $sfs1['NAME'] ;
				$pid  = $sfs1['PARENT_ID'] ;
				
				echo " account_$aid.childNodes.push({id:'a___$aid"."_"."$code',text:'$name',pid:'$pid',checkstate:$selected});" ;
				
			} ;
		} ;
		
	?>
	
	$(itemCache).each(function(){
		if( this.pid && treeMap['id_'+this.pid]){
			treeMap['id_'+this.pid].childNodes = treeMap['id_'+this.pid].childNodes||[] ;
			treeMap['id_'+this.pid].childNodes.push(this) ;
		}
		
		if(this.id == "order_manage"){
			this.childNodes = this.childNodes||[] ;
			var me = this ;
			$(accounts).each(function(){
				me.childNodes.push(this) ;
			}) ;
		}
		
		/*if( this.pid =="account" ){
			var me = this ;
			$(accounts).each(function(){
				this.childNodes = this.childNodes||[] ;
				me.id = this.id+"_"+me.id
				this.childNodes.push(me) ;
			}) ;
		}*/
	}) ;
   
	$(function(){


		$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData,
				showCheck:true
           }) ;
           
        $("button").click(function(){
        	var ids = $('#default-tree').tree().getSelectedIds() ;
        	$.ajax({
				type:"post",
				url:"/saleProduct/index.php/users/saveAssignFunctions/"+code+"/"+ids,
				data:{},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					window.opener.location.reload() ;
					window.close() ;
				}
			}); 
        }) ;
		
	})
   </script>

</head>
<body>
<div id='content-default' class='demo'>
		<h1></h1>
	<div id="default-tree" class="tree" style="padding: 5px; "></div>
	
	<button>保存权限设置</button>
</div>

</html>