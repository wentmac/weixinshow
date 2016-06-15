function StringBuilder(){this.arr=[]}StringBuilder.prototype.append=function(a){this.arr.push(a)};StringBuilder.prototype.appendFormat=function(){for(var a=arguments[0],c=0;c<arguments.length-1;c++)a=a.replace(new RegExp("\\{"+c+"\\}"),arguments[c+1]);this.arr.push(a)};StringBuilder.prototype.toString=function(){return this.arr.join("")};
function _g(a){return document.getElementById(a)}
function citytab(a){
	var c=_g("cityhead").getElementsByTagName("li");
	if(c){
		for(var b=0;b<c.length;b++)
		c[b].className="search_li01";
		if(b=_g("li"+a))
		b.className="search_li02"
	}
	if(c=_g("city_box").getElementsByTagName("div"))
	{
		for(b=1;b<c.length;b++)
		c[b].className="list_main unshow";
		
		if(b=_g("city"+a))
			b.className="list_main"
	}
	document.getElementById("top_getiframe").style.height=document.getElementById("city_box").offsetHeight+2+"px";
}
		
function prefixTab(a){var c=_g("cityall").getElementsByTagName("ul");if(c){for(var b=1;b<c.length;b++)c[b].className="city_sugg unshow";if(a=_g("ul"+a))a.className="city_sugg"}document.getElementById("top_getiframe").style.height=document.getElementById("city_box").offsetHeight+2+"px"};

function replaceHtml(el, html) {
    var oldEl = typeof el == "string" ? document.getElementById(el) : el;
    var newEl = oldEl.cloneNode(false);
    newEl.innerHTML = html;
    oldEl.parentNode.replaceChild(newEl, oldEl);
    return newEl;
};

var parentbject;
window.city_suggest = function(){
	this.object = '';
	this.id2 = '';
	this.taskid = 0;
	this.delaySec = 10; // 默认延迟多少毫秒出现提示框
	this.hot= [];
	this.letter = [];
	this.hotelcity ={};
	this.hotelcityid = {};
	this.hotHC = [];
	this.hotP = [];
	this.hotelcityp = {};
	/**
	* 初始化类库
	*/
	this.init_zhaobussuggest=  function(){
		var objBody = document.getElementsByTagName("body").item(0);
		var objiFrame = document.createElement("iframe");
		var objplatform = document.createElement("div");
		objiFrame.setAttribute('id','top_getiframe');
		objiFrame.setAttribute("src","about:blank");
		objiFrame.style.zIndex='10';
		objiFrame.style.border='0';
		objiFrame.style.width='0px';
		objiFrame.style.height='0px';
		objiFrame.style.position = 'absolute';
		objplatform.setAttribute('id','top_getplatform');
		objplatform.setAttribute('align','left');
		objplatform.style.zIndex='10';
		objplatform.style.position = 'absolute';
		objplatform.style.border = 'solid 2px #a19283';
		objplatform.style.background = '#ffffff';
		if(objBody){
		    objBody.insertBefore(objiFrame,document.getElementById("container"));
		    if(objiFrame){
		        objiFrame.ownerDocument.body.insertBefore(objplatform,document.getElementById("container"));
		    }
		}
				
		if(!document.all) {
			window.document.addEventListener("click",this.hidden_suggest,false);
		}else{
			window.document.attachEvent("onclick",this.hidden_suggest);
		}
	}

	/***************************************************fill_div()*********************************************/
	//函数功能：动态填充div的内容，该div显示所有的提示内容
	//函数参数：allplat 一个字符串数组，包含了所有可能的提示内容
	this.fill_div = function(allplat){
		var _html=new StringBuilder;_html.append('<div id="city_box" class="choose_frame">');_html.append('\t<div id="cityhead" class="list_head">');_html.append('\t\t<a onclick="parentbject.hidden();" style="cursor:pointer;" class="fright" title="\u5173\u95ed"><img height="15" width="15"  src="'+webpath+'js/calendar/close.gif" /></a>');_html.append('\t\t<ul class="fleft">');_html.append('\t\t\t<li id="lihot" class="search_li02" onclick="citytab(\'hot\');">\u70ed\u95e8\u57ce\u5e02</li>');
		if (this.object!=document.getElementById('txtDepartureCity')) {_html.append('\t\t\t<li id="liall" class="search_li01" onclick="citytab(\'all\');">\u66f4\u591a\u57ce\u5e02</li>');}
		_html.append("\t\t</ul>");
	if (this.object!=document.getElementById('txtDepartureCity')) {	_html.append('\t\t<span class="fcenter">\uff08\u6309\u62fc\u97f3\u9996\u5b57\u6bcd\uff09</span>');}
        _html.append("\t</div>");_html.append('\t<div id="cityhot" class="list_main ">');_html.append('\t\t<ul class="city_sugg">');for(var i=0;i<this.hot.length;i++)_html.appendFormat("\t\t\t<li><a href=\"javascript:void(0);\" onclick=\"parentbject.add_input_text('{0}','{1}','{3}','{4}');\">{2}</a></li>",this.hot[i],this.hot[i],this.hot[i],this.hotHC[i],this.hotP[i]);_html.append("\t\t</ul>");
        if (this.object!=document.getElementById('txtDepartureCity')) {_html.append('\t\t<span class="more_city link01"><a href="javascript:void(0);" onclick="citytab(\'all\');" style="text-decoration:underline;">\u66f4\u591a\u57ce\u5e02</a></span>');}
        else { _html.append('\t\t<div style=" width:100%; height:16px; clear:both;">&nbsp;</div>');}
        _html.append("\t</div>");_html.append('\t<div id="cityall" class="list_main unshow">');_html.append('\t\t<ul class="city_list2 link01">');for(i=0;i<this.letter.length;i++)_html.appendFormat("\t\t\t<li><a href=\"javascript:prefixTab('{0}');\">{1}</a></li>",this.letter[i],this.letter[i]);_html.append("\t\t</ul>");
        for(i=0;i<this.letter.length;i++){i==0?_html.appendFormat('\t\t<ul id="ul{0}" class="city_sugg">',this.letter[i]):_html.appendFormat('\t\t<ul id="ul{0}" class="city_sugg unshow">',this.letter[i]);var c=this.hotelcity[this.letter[i]];var g = this.hotelcityid[this.letter[i]];var p = this.hotelcityp[this.letter[i]];if(c)for(var j=0;j<c.length;j++)_html.appendFormat("\t\t\t<li><a href=\"javascript:void(0);\" onclick=\"parentbject.add_input_text('{0}','{1}','{3}','{4}');\">{2}</a></li>",c[j],c[j],c[j],g[j],p[j]);_html.appendFormat("\t\t</ul>")}_html.append("\t</div>");_html.append("</div>");
        msgplat = _html.toString();
        
        var el = document.getElementById("top_getplatform");

        window.setTimeout(function(){
            replaceHtml(el, msgplat);
            
            document.getElementById("top_getiframe").style.width = document.getElementById("top_getplatform").clientWidth+2;
            document.getElementById("top_getiframe").style.height = document.getElementById("top_getplatform").clientHeight+2;
        },10);
		
		
	}

	/***************************************************fix_div_coordinate*********************************************/
	//函数功能：控制提示div的位置，使之刚好出现在文本输入框的下面
	this.fix_div_coordinate = function(){
		var leftpos=0;
		var toppos=0;
		var aTag = this.object;
		var l = $("#top_getplatform");
			var tipX = $("#"+aTag.id+"").position().left;
			var tipY = $("#"+aTag.id+"").position().top+25;
			var w=(tipX + l.outerWidth(true))-($(window).scrollLeft() + $(window).width());
			var h=(tipY + l.outerHeight(true))-($(window).height()+$(window).scrollTop());
			if(w>0) tipX -= w;
			if(h>0) tipY -= h;
			l.css({"left":tipX +"px","top":tipY+ "px"});
		 	$("#top_getiframe").css({"left":tipX +"px","top":tipY+ "px"});
	}

    /***************************************************hidden_suggest*********************************************/
	//函数功能：隐藏提示框
	this.hidden_suggest = function (event){
		if (event.target) targ = event.target;  else if (event.srcElement) targ = event.srcElement;
		if(targ.tagName!='LI' && targ.tagName!='A'){	
		    document.getElementById("top_getiframe").style.visibility = "hidden";
		    document.getElementById("top_getplatform").style.visibility = "hidden";
		}
	}
	this.hidden = function(){if(document.getElementById("top_getiframe")){document.getElementById("top_getiframe").style.visibility = "hidden";document.getElementById("top_getplatform").style.visibility = "hidden";}}

	/***************************************************show_suggest*********************************************/
	//函数功能：显示提示框
	this.show_suggest = function (){
		document.getElementById("top_getiframe").style.visibility = "visible";
		document.getElementById("top_getplatform").style.visibility = "visible";
	}

	this.is_showsuggest= function (){
		if(document.getElementById("top_getplatform").style.visibility == "visible") return true;else return false;
	}

	this.sleep = function(n){
		var start=new Date().getTime(); //for opera only
		while(true) if(new Date().getTime()-start>n) break;
	}

	this.ltrim = function (strtext){
		return strtext.replace(/[\$&\|\^*%#@! ]+/, '');
	}

    /***************************************************add_input_text*********************************************/
	//函数功能：当用户选中时填充相应的城市名字

	this.add_input_text = function (keys,szm,id,py){
		keys=this.ltrim(keys)
		this.object.value = keys;
		document.getElementById("cityid").value = id;
		try{loadWord(id,''+szm+'')}catch(e){}
		var id=this.object.id;
		var id2 = this.id2;
		if(document.id2){
			document.getElementById(this.id2).value = szm;
		}
		document.getElementById(id).style.color="#000000";
		document.getElementById(id).value=keys;
		document.getElementById("top_getiframe").style.visibility = "hidden";
		document.getElementById("top_getplatform").style.visibility = "hidden";
		this.clear();
     }
     
     this.ajaxac_getkeycode = function (e){
		var code;
		if (!e) var e = window.event;
		if (e.keyCode) code = e.keyCode;
		else if (e.which) code = e.which;
		return code;
	}

    /***************************************************display*********************************************/
	//函数功能：入口函数，将提示层div显示出来
	//输入参数：object 当前输入所在的对象，如文本框
	//输入参数：e IE事件对象
	this.display = function (object,id2,e){
        //if(!e){e=this.display.caller.arguments;}
		this.id2 = id2;
		if(!document.getElementById("top_getplatform")) this.init_zhaobussuggest();
		if (!e) e = window.event;
		e.stopPropagation;
		e.cancelBubble = true;
		if (e.target) targ = e.target;  else if (e.srcElement) targ = e.srcElement;
		if (targ.nodeType == 3)  targ = targ.parentNode;

		this.object = object;
		if(window.opera) this.sleep(100);//延迟0.1秒
		parentbject = this;
		if(this.taskid) window.clearTimeout(this.taskid);
        this.taskid=setTimeout("parentbject.localtext();" , this.delaySec);
	}

	//函数功能：从本地js数组中获取要填充到提示层div中的文本内容
	this.localtext = function(){
		var id=this.object.id;
		parentbject.show_suggest();
		parentbject.fill_div('');
		parentbject.fix_div_coordinate();
	}
	this.clear=function(){
		var f=$("#form_hotel");
		f.find("input[name='bid']").val('');
		f.find("input[name='lsid']").val('');
		f.find("input[name='mapid']").val('');
		f.find("input[name='keys']").val('');
		f.find("input[name='key']").val('');
		f.find("input[name='keystemp']").val('');
	}
};
var suggestH = new city_suggest();suggestH.letter = letterH;suggestH.hot = hotH;suggestH.hotelcity = citysH;suggestH.hotelcityid = citysHC;suggestH.hotHC=hotHC;suggestH.hotP=hotP;suggestH.hotelcityp = citysP;