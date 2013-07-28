
/*
*标题:jQuery四图切换动画仿flash展示效果
*作者：周晶
*时间：2010年8月1日
*jQuery版本：1.4.2
*/


$(document).ready(function(){

	$("#adver ul li").eq(0).css({"background":"black","cursor":"pointer","color":"red","font-size":"14px"});//页面载入后默认第一幅图的样式
	
	/*
	*定时器自动滑动相关
	*/
	c = 1;   //全局自增属性,可理解为指针变量，指向当前显示标签
	function circle(){    //执行样式切换
		var k = c%$("#adver ul li").length;         //利用求余实现自动循环
		$("#adver ul li").eq(k).css({"background":"black","cursor":"pointer","color":"red","font-size":"14px"});
		$(".pic").each(
			function(j){
				if(j!=k){
					$(this).fadeOut(1000);
				}else{
					$(this).fadeIn(1000);
				}
			}
		);
		$("#adver ul li").each(function(j){
			if(k!=j){
				$("#adver ul li").eq(j).css({"background":"url(trans.png)","color":"black","font-size":"12px"});
			}
		});
		c++;
	}
	int = setInterval(circle,1000*3);

	/*
	*鼠标交互相关
	*/	
	$("#adver ul li").each(function(i){
		$(this).hover(
			function(){
				c = i;    //将定时器指向当前鼠标移动到的元素上
				$(this).css({"background":"black","cursor":"pointer","color":"red","font-size":"14px"});
				$("#adver ul li").each(function(k){
					if(k!=i){
						$(this).css({"background":"url(trans.png)","color":"black","font-size":"12px"});
					}
				});
				$(".pic").each(
					function(j){
						if(j!=i){
							$(this).fadeOut(1000);
						}else{
							$(this).fadeIn(1000);
						}
					}
				);
			},function(){
			}
		);
	});
});
