function showVariables(){
    $.facebox($('#divbindconstant').html());
}
function addspecifics(){
    $('#speci').append('<tr><th><input name="myspecifics[]" size="30"></th><td><input name="myspecificsval[]" size="40"></td><td></td></tr>');
}
function freeship(i,j){
    $('#ntr'+i+j).val('0.00');
    $('#ntr'+i+i+j).val('0.00');
    $('#ntr'+i+j*7).val('0.00');
    $('#ntr'+i+i+j*7).val('0.00');
    $('#ntr'+i+j*13).val('0.00');
    $('#ntr'+i+i+j*13).val('0.00');
}
function changeshipservice(i){
    $('#tobecost'+i).show();
    $('#tobecost'+i*7).show();
    $('#tobecost'+i*11).show();
    $('#tobecost'+i*13).show();
}
function previewItem(){
    if (typeof(KE.g['itemdescription'].iframeDoc) == 'object'){
        $('#itemdescription').val(KE.util.getData('itemdescription'));
    }
    //$('#itemdescription').val(KE.util.getData('itemdescription'));
    document.m.target="_blank";
    document.m.action="/index.php/muban/previewitem";
    document.m.submit();
    document.m.target="";
    document.m.action="";
}
function preview(){
    if (typeof(KE.g['itemdescription'].iframeDoc) == 'object'){
        $('#itemdescription').val(KE.util.getData('itemdescription'));
    }
    //$('#itemdescription').val(KE.util.getData('itemdescription'));
    document.m.target="_blank";
    document.m.action="/index.php/muban/preview";
    document.m.submit();
    document.m.target="";
    document.m.action="";
}
function imgurl_input_blur(obj){
    var t=$(obj).val(); 
    if(t.indexOf('{-BINDPRODUCTPICTURE-}')>=0){
        t= t.replace('{-BINDPRODUCTPICTURE-}',$('#bindproductpicture_val').val());
    }
    $(obj).parent().children('img').attr('src',t);
}

function goodselected(goods_id,languageid){ //转发
    $('#goods_id').val(goods_id);
    $('#languageid').val(languageid);
    isonload();
    doaktion('','');
}


function usedcategorypath(obj,value){
    var o=$(obj).children('option[value='+$(obj).val()+']');
    $('#'+$(obj).attr('name')+'_path').html(o.attr('path'));
}

function usedtemplate(obj){
    $('#template').val($(obj).val());
    var o=$(obj).children('option[value='+$(obj).val()+']');
    if(o.attr('templatepic')){
        $('#usingtemplatepic').attr('src',o.attr('templatepic'));
    }else{
        $('#usingtemplatepic').attr('src','/link/img/action/cancel.png');
    }
}

//标题长度
function inputbox_left(inputId,limitLength,text){
    var o=document.getElementById(inputId);
    if(!o) return ;
    if(text==undefined){
        left=limitLength-o.value.length;
    }else{
        left=limitLength-text.length;
    }
    $('#length_'+inputId).html(left);
    if(left>=0){
        $('#length_'+inputId).css({'color':'green'});
    }else{
        $('#length_'+inputId).css({'color':'red'});
    }
}
//选择Ebay 店铺 类目
function ebayStoreCategorySelected(elementid,selleruserid,categoryid){
    $('#selleruserid').val(selleruserid);
    $('#'+elementid).val(categoryid);
}
$('#site').change(function(){
    isonload();
    doaktion('','');
});
$('#listingtype').change(function(){
    val=$('#listingduration').val();
    if ($(this).val()=='Chinese'){
        $('.auction_only').show();
        $('.fixedprice_only').hide();
        $('#listingduration').html($('#listingduration_auction').html()).val(val);
        $('input[name=quantity]').attr('disabled','disabled');
    }else{
        $('.auction_only').hide();
        $('.fixedprice_only').show();
        $('#listingduration').html($('#listingduration_fixedprice').html()).val(val);;
        $('input[name=quantity]').removeAttr('disabled');
    }
}).trigger('change');

$(document).ready(function(){
    window.setTimeout(function(){
        //标题长度提示
        checktitlebindconstant('itemtitle');
        inputbox_left('subtitle',55);
    },100);
    imgurl_input_blur($('#img'));
});
$('#itemtitle').keydown(function(){checktitlebindconstant('itemtitle')}).keypress(function(){checktitlebindconstant('itemtitle')}).keyup(function(){checktitlebindconstant('itemtitle')});

$('.cebayfee').click(function(){
    window.open('http://pages.ebay.com/help/sell/fees.html','cebayfee');
    return false;
});
//添加一个图片 输入框
function Addimgurl_input(){
    $('#div_imgurl_input').append('<div><img src="" width="50" height="50" ><input type="text" id="imgurl'+(Math.random()*10000).toString().substring(0,4)+'" name="imgurl[]" size="80" value=""  onblur="javascript:imgurl_input_blur(this)"><a href="#13" onclick="javascript:$(this).parent().empty();return false;" >删除此图片</a></div>');
}
function Addimgurl_input2(){
    $('#div_imgurl_input2').append('<div><img src="" width="50" height="50" ><input type="text" id="imgurl'+(Math.random()*10000).toString().substring(0,4)+'" name="imgshow[]" size="80" value=""  onblur="javascript:imgurl_input_blur(this)"><a href="#13" onclick="javascript:$(this).parent().empty();return false;" >删除此图片</a></div>');
}

// 范本保存载入
$(function(){
	function loadProfile(type){
		$("#"+type+"_profile").empty();
		$("#"+type+"_profile").append("<option value=''></option>");
		$.dataservice("model:PublishEbay.loadProfile",{type:type},function(result){
			$(result).each(function(){
				$("#"+type+"_profile").append("<option value='"+this.ID+"'>"+this.NAME+"</option>");
			}) ;
 	    });
	}
	
	loadProfile("detail_wuliu") ;
	loadProfile("detail_location") ;
	loadProfile("detail_return") ;
	
    $('.profile_save').click(function(){
        p=$(this).parent().parent().parent();
        type=p.attr('id');
        str=p.find('input[type=text],input:checked,select,textarea').serialize();
        str=str.replace(/%5B%5D/g,'----').replace(/%5B/g,'\\\\').replace(/%5D/g,'//').replace(/----/g,'[]');
        do{
            save_name=window.prompt('请输入保存范本别名','');
            if (save_name == null){
                alert('保存被取消');
                return;
            }
        }while(save_name.length ==0);
        
       var json = $("#"+type).parent().toJson() ;
       json.name = save_name ;
       json.type = type ;
       
       //alert( $.json.encode(json) ) ;
      // return ;
       
       $.dataservice("model:PublishEbay.saveProfile",json,function(result){
    	   loadProfile(type) ;
    	   //load
       }) ;
    });
    $('.profile_del').click(function(){
        p=$(this).parent().parent().parent();
        type=p.attr('id');
        select_id=type+"_profile";
        profile=$('#'+type+'_profile').val();
        if(profile.length == 0){
            alert('请先选择您要删除的范本！');
            return;
        };
        if(confirm("你确定要删除此范本吗？")){
        	$.dataservice("model:PublishEbay.deleteProfile",{'id':profile,'type':type},function(result){
         	   loadProfile(type) ;
         	   //load
            }) ;
        }
    });
    $('.profile_load').click(function(){
        p=$(this).parent().parent().parent();
        type=p.attr('id');
        profile=$('#'+type+'_profile').val();
        if(profile.length == 0){
            alert('请先选择您要载入的范本！');
            return;
        }
        //$.facebox('<h1>正在读取数据...</h1>');
        
        $.dataservice("model:PublishEbay.getProfile",{type:type,id:profile},function(result){
        	for(var o in result ){
    			$(".val_"+o).val( result[o] ) ;
    		}
 	    });
    });
	$('#basicinfo').click(function(){
		$('#basicinfoid').html($(this).val());
	});
});

/** 对itemtitle使用代入bindproductname  **/
function checktitlebindconstant(inputId){
    if($('#'+inputId).val().indexOf('{-BINDPRODUCTNAME-}')>=0){
        var t=$('#'+inputId).val();
        inputbox_left(inputId,80, t.replace('{-BINDPRODUCTNAME-}',$('#bindproductname_val').val()));
    }else{
        inputbox_left(inputId,80);
    }
}
//遮罩
function isonload(){
    $.facebox('正在载入，请不要关闭此窗口');
}
function doaktion(aktion,atta){
	if( $.validation.validate('#m').errorInfo ) {
		return ;
	}
	
	//document.m.submit();
    $('input[name=aktion]').val(aktion);
    //$('#m').attr('target','_self');
    if (typeof(KE.g['itemdescription'].iframeDoc) == 'object'){
        $('#itemdescription').val(KE.util.getData('itemdescription'));
    }
    if(aktion){
        n_num=0;        
        for(i=0;i<3;i++){
            if(document.getElementById('shippingdetails[ShippingServiceOptions]['+i+'][ShippingService]').value==""){
                n_num++;
            }
        }
        if(n_num==3){           
            scrollTo('detail_wuliu');
            setTimeout(facebox,400);
            function facebox(){
                $.facebox('请填写境内物流方式');
            }
            return false;
        }
    }
    if(aktion=='send'){
        if(atta=='new'){
            if(!confirm('将打开新窗口进行发布,确定要发布吗?')) return false;
            $('#m').attr('target','_blank');
        }else{
            if(!confirm('如果你做过修改还未保存,确定要发布吗?')) return false;
        }
    }
    if(aktion=='verify'){
        $('#m').attr('target','_blank');
    }
    
   $('#m').submit();
}
/**
 * 动态翻译代码
 */
function transArray(hash,callbackname){
     var languageFrom = "en";
     switch ($('#site').val()){
         case '212': 
             languageFrom='pl';
             break;
         case '16':    
         case '77':
         case '193':
             languageFrom='de';
             break;
         case '123':
         case '146':
             languageFrom='nl';
             break;
         case '23':
         case '71':
         case '210':
             languageFrom='fr';
             break;
         case '201':
             languageFrom='zh-TW';
             break;
         case '186':
             languageFrom='es';
             break;
         case '101':
             languageFrom='it';
             break;
         case '0':
         case '2':
         case '3':
         case '15':
         case '100':
         case '203':
         default:
             languageFrom='en';
             break;
     }
     var languageTo = "zh-cn";
     var texts = '';
     var options = '{"ContentType":"text/html"}';
     var i=0;
     for(j in hash){
         arr=hash[j];
         for(k=0;arr[k];k++){
//           arr[k]=arr[k].replace('&','\&');
//           arr[k]=arr[k].replace('"','\"');
//           arr[k]=arr[k].replace('?','\?');
             console.log(arr[k]);
             txt=encodeURIComponent(arr[k]);
             if (i==0){
                 texts='["<a gid='+j+' original=\\"'+txt+'\\">'+txt+'</a>"';
             }else{
                 texts=texts+',"<a gid='+j+' original=\\"'+txt+'\\">'+txt+'</a>"';
             }
             i++;
         }
     }
     texts=texts+']';
     
     url="http://api.microsofttranslator.com/V2/Ajax.svc/TranslateArray?oncomplete="+callbackname+"&appId=1310501DADD932797FCBF94E7A5AFA3DFD2EB8B3&from=" + languageFrom +
     "&to=" + languageTo + "&texts=" + texts + "&options=" + options;
     console.log(url);
     var s = document.createElement("script");
     s.src = url;
     document.getElementsByTagName("head")[0].appendChild(s);
}
function transCallback(resArr){
    var kArr=[];
    var vArr=[];
    for (i in resArr){
        robj=$(resArr[i]['TranslatedText']);
        kArr.push(robj.attr('original'));
        vArr.push(robj.attr('original')+'===>['+robj.text()+']');
    }
    var target=$('.specific_cols [trans_id='+robj.attr('gid')+']');
    if (target.is('input')){
        target.data('combobox_suggestions',vArr);
    }else if (target.is('select')){
        for (i in kArr){
            $('[value='+kArr[i]+']',target).text(vArr[i]);
        }
    }
    $('#trans_btn').val('翻译完成');
}
var iii=1313;
function transSpecific(){
    $('.specific_cols .combo').each(function(){
        iii++;
        if ($(this).data('combobox_suggestions') != null){
            $(this).attr('trans_id',iii);
            var request={};
            request[iii]=$(this).data('combobox_suggestions');
            transArray(request,'transCallback');
        }
    });
    $('.specific_cols select').each(function(){
        iii++;
        var vArr=[];
        $(this).attr('trans_id',iii);
        $('option',this).each(function(){
            if($(this).text().length>0){
                vArr.push($(this).text());
            }
        });
        var request={};
        request[iii]=vArr;
        transArray(request,'transCallback');
    });
}
$('#trans_btn').click(function(){
    $(this).val('Loading...').attr('disabled','disabled');
    transSpecific();
})
$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
function scrollTo(id){
    if ($('#'+id).length){
        $body.animate({scrollTop: $('#'+id).offset().top}, 300);
    }
    return false;
    
}