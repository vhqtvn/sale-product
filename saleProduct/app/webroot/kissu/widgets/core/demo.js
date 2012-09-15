
function loadXMLDoc(xmlFile){
	var xmlDoc;
	if(window.ActiveXObject){
		xmlDoc = new ActiveXObject('Microsoft.XMLDOM');
		xmlDoc.async = false;
		xmlDoc.load(xmlFile);
	}else if (document.implementation&&document.implementation.createDocument){
		var xmlhttp = new window.XMLHttpRequest();
	    xmlhttp.open("GET", xmlFile, false);
	    xmlhttp.send(null);
	    
	    xmlDoc = xmlhttp.responseXML;
	}else{
		return null;
	}
	return xmlDoc;
}

//nodeType text:3
function loadControlConfig(src){
	var resultObj = {} ;
try{
	//加载xml文件
	var doc = loadXMLDoc(src) ;

	//获取分类
	var nodes = xpathSelect(doc,"/Root/category");
	resultObj.categorys = [] ;
	
	for(var i = 0 ;i < nodes.length ;i ++){
		var node = nodes[i] ;
		if(node.nodeType && node.nodeType == 3) continue ;
		
		resultObj.categorys[i] = {name : node.getAttribute('name')};
		
		var demo_nodes = xpathSelect(doc,"/Root/category["+getXpathIndex(i)+"]/demo");
		
		resultObj.categorys[i].demos = [];
		
		for(var j = 0 ;j < demo_nodes.length ;j ++){
			var demo_node = demo_nodes[j] ;
			
			if(demo_node.nodeType && demo_node.nodeType == 3) continue ;
			
			resultObj.categorys[i].demos[j] = {
			    name: demo_node.getAttribute('name'),
				url: demo_node.getAttribute('url')
			}
		}
	}

}catch(e){
	alert(e.message);
}	
	return resultObj ;
}

function getXpathIndex(i){
	if (window.ActiveXObject){
		return i ;
	}else if (document.implementation && document.implementation.createDocument){
		return i+1;
	}
}

function xpathSelect(xml,path){
	if (window.ActiveXObject){
		 var nodes=xml.documentElement.selectNodes(path);
		 return nodes ;
	}else if (document.implementation && document.implementation.createDocument){
		var nodes=xml.evaluate(path, xml, null, XPathResult.ANY_TYPE, null);
		result=nodes.iterateNext();
		var nodeArray = [] ;
		while (result){
		  nodeArray.push(result) ;
		  result=nodes.iterateNext();
		 }
	 	return nodeArray ;
	}
}


if( !$.widget ){
	document.write('<script src="../../../../scripts/plugins/jquery.ui.core.js" type="text/javascript" ></script>') ;
	document.write('<script src="../../../../scripts/plugins/jquery.ui.widget.js" type="text/javascript" ></script>') ;
	document.write('<script src="../../../../scripts/plugins/jquery.ui.position.js" type="text/javascript" ></script>') ;
}	


if(!window.prettyPrint){
	document.write('<link  href="../../../../scripts/plugins/google-code-prettify/prettify.css" rel="stylesheet">') ;
	document.write('<script src="../../../../scripts/plugins/google-code-prettify/prettify.js"></script>') ;
}

	
if( !$.fn.tabs){
	document.write('<script src="../../tab/jquery.ui.tabs.js" type="text/javascript"></script>') ;
	document.write('<link  href="../../tab/jquery.ui.tabs.css" rel="stylesheet" type="text/css" />') ;
}

$(function(){
	
	if($.fn.tabs && $(".view-source")[0]){
		var vsContainer = $(".vs-container")[0]||document.body ;
		
		var tabs = $("<div></div>").appendTo(vsContainer) ;
		
		$("<div id='vs-source'></div>").appendTo(document.body).addClass("vs-doc") ;
		$("<div id='vs-options'></div>").appendTo(document.body).addClass("vs-doc") ;
		$("<div id='vs-event'></div>").appendTo(document.body).addClass("vs-doc") ;
		$("<div id='vs-method'></div>").appendTo(document.body).addClass("vs-doc") ;
		
		tabs.tabs( {
			tabs:[
				{label:'view source',content:'vs-source',selected:true},
				{label:'options',content:'vs-options'},
				{label:'event',content:'vs-event'},
				{label:'method ',content:'vs-method'}
			]
		} ) ;
	}
	
		
	
	var html = [] ;
	$(".view-source").each(function(){
		var el = $(this).removeClass("view-source") ;
		if( el[0].className == "" ){
			el.removeAttr("class") ;
		}
		
		removeAttr(el,["autocomplete","role","aria-autocomplete","aria-haspopup","__render__"]) ;
		
		var text = getText( el ) ;
		
		
		if(text.startWith("<link")||text.startWith("<script")){
			html.push( text ) ;
			html.push("\n") ;
		}else{
			html.push("\n") ;
			html.push( text ) ;
			html.push("\n") ;
		}
	}) ;

	$("<pre></pre>").appendTo($("#vs-source")).addClass("prettyprint linenums").text( html.join("") ) ;
	
	window.prettyPrint &&window.prettyPrint() ;
	
	function getText(el){
		var isInnerCode = el.hasClass("inner-code") ;
		
		var text = isInnerCode?el.html():el.get(0).outerHTML;//.get(0).outerHTML ;
		//alert(text)
		//el.hasClass("inner-code")&& alert(el.html()) ;
		el.removeClass("inner-code") ;
		if( el[0].className == "" ){
			el.removeAttr("class") ;
		}
			
		if( !el.hasClass("noparse") ){
			text = formatCode(text) ;
		}else{
			el.removeClass("noparse") ;
			if( el[0].className == "" ){
				el.removeAttr("class") ;
			}
			text = isInnerCode?el.html():el.get(0).outerHTML ;
		}
		return text ;
	}
	
	function removeAttr(el,attrs){
		//autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"
		$(attrs).each(function(){
			el.removeAttr(this) ;
		}) ;
	}
	
	function formatCode(codeHtml){
		var bHtml = [] ;
		var codes = $.trim(codeHtml).split(/(|)/g) ;
		var start , end , tag = [] , code = null ,prev  , tagStrack={tags:[],level:0} ;
		$(codes).each(function(){
			if( this == '<' ){
				tagStrack.tags.push(this) ;
				prev = this ;
				if(code&&$.trim(code.join(""))){
					bHtml.push("    "+ getSpacer(tagStrack.level -1 ) + $.trim(code.join(""))  ) ;
				}
				code = null ;
				start = true ;
				end = false ;
				tag = [] ;
				tag.push(this) ;
			}else if(this == '>'){
				prev = this ;
				tagStrack.tags.push(this) ;
				end   = true ;
				tag.push(this) ;
				//clear
				code = [] ;
				parseLevel( tagStrack );
				if(tagStrack.isEndTag){
					bHtml.push( getSpacer(tagStrack.level) +tag.join("")  ) ;//
					tagStrack.isEndTag = false;
				}else  if(tagStrack.isOnlyTag){
					bHtml.push( getSpacer(tagStrack.level) +tag.join("")   ) ;//
					tagStrack.isOnlyTag = false;
				}else
					bHtml.push( getSpacer(tagStrack.level-1) +tag.join("")  ) ;//
				
				start = false;
				tag = null ;
			}else if(this == '/'){
				prev = this ;
				tag.push(this) ;
				if(code != null ){
					return ;
				}
				tagStrack.tags.push(this) ;
			}else{
				prev = this ;
				if( start ){
					tag.push(this) ;
					
					if( $.trim(this) && tagStrack.tags[tagStrack.tags.length-1] == "/" ){
						if( tagStrack.tags[tagStrack.tags.length-2] == "c" ){
							tagStrack.tags.pop() ;
						}
					}
					
					if($.trim(this) && tagStrack.tags[tagStrack.tags.length-1] != 'c' ){
							tagStrack.tags.push("c") ;
					}
				}
				if( end ){
					code.push(this) ;
				}
			}
		}) ;
		if(bHtml.length <= 2){
			return bHtml.join("") ;
		}else{
			return bHtml.join("\n") ;
		}
		
	}

	function parseLevel(tagStrack){
		if( tagStrack.tags.length <= 2 ){
			return   ;
		}
		var prev1 = tagStrack.tags.pop() ;
		var prev2 = tagStrack.tags.pop() ;
		if( prev2 == "<" && prev1==">" ){
			tagStrack.level = tagStrack.level + 1 ;
			return ;
		}
		
		
		var prev3 = tagStrack.tags.pop() ;
		if( prev3 == "<" && prev2=="c" && prev1==">" ){
			tagStrack.level = tagStrack.level+1 ;
		}else{
			if( tagStrack.tags.length >=1 ){
				prev4 = tagStrack.tags.pop() ;
				if( prev4 == "<" && prev3 == 'c' && prev2== '/'&& prev1== '>' ){
					tagStrack.isOnlyTag = true ;
				}else{
					tagStrack.isEndTag = true ;
					tagStrack.level-- ;
				}
			}else{
				prev3 && tagStrack.tags.push(prev3) ;
				prev2 && tagStrack.tags.push(prev2) ;
				prev1 && tagStrack.tags.push(prev1) ;
			}
		}
	}

	
	function getSpacer(level){
		var space = "" ;
		for(var i=0 ;i<level ;i++){
			space = space +"    " ;
		}
		return space ;
	}

}) ;
