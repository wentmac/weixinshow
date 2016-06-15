//酒店跳转
function ebook(rid){window.open('ebook.asp?rid='+rid+'',"ebook")}

// 自动最大化网页，以获得最佳浏览效果
if ( screen.availWidth > 1000 && document.documentElement.offsetWidth < 1010 )	{
	self.moveTo(-4,-4);
	self.resizeTo(screen.availWidth+7,screen.availHeight+8);
	document.body.style.cssText="overflow-x:hidden;";
}else if ( screen.availWidth < 1000 && document.documentElement.offsetWidth < 750 )	{
	self.moveTo(-3,-3);
	self.resizeTo(screen.availWidth+5,screen.availHeight+6);
}

// 容错代码
function killErrors() {
return true;
}
//window.onerror = killErrors;

//首页TAG
function selectTag(showContent,selfObj){
	// 操作标签
	var tag = document.getElementById("tags").getElementsByTagName("li");
	var taglength = tag.length;
	for(i=0; i<taglength; i++){
		tag[i].className = "";
	}
	selfObj.parentNode.className = "selectTag";
	// 操作内容
	for(i=0; j=document.getElementById("tagcontent"+i); i++){
		j.style.display = "none";
	}
	document.getElementById(showContent).style.display = "block";
}

function _g(n){return document.getElementById(n)}
function xmlHttp(Url,xmlBack){var xObj=null;try{xObj=new ActiveXObject("MSXML2.XMLHTTP")}catch(e){try{xObj=new ActiveXObject("Microsoft.XMLHTTP")}catch(e2){try{xObj=new XMLHttpRequest()}catch(e){}}};with(xObj){open("get",Url, true);onreadystatechange=function(){if(readyState==4&&status==200){xmlBack(responseText)}};send(null)}};

function xmlHttp2(Url,xmlBack,_obj){var xObj=null;try{xObj=new ActiveXObject("MSXML2.XMLHTTP")}catch(e){try{xObj=new ActiveXObject("Microsoft.XMLHTTP")}catch(e2){try{xObj=new XMLHttpRequest()}catch(e){}}};with(xObj){open("get",Url, true);onreadystatechange=function(){if(readyState==4&&status==200){xmlBack(responseText,_obj)}};send(null)}};

document.getElementsByClassName=function(tag,cName){var els= [];var myclass=new RegExp("\\b"+cName+"\\b");var elem=this.getElementsByTagName(tag);for(var h=0;h<elem.length;h++){if(myclass.test(elem[h].className))els.push(elem[h])}return els};
function loadcs(id,cityname,obj){v1(obj);xmlHttp('./tuijian.asp?cid='+id+'&cname='+escape(cityname)+'&r='+Math.random( ),function(e){_g('tagcontent').innerHTML=e;_g('loadimg').style.display="none";});}
function v1(obj){
  var objs=_g('tags').getElementsByTagName('a');
  for(var k=0;k<objs.length;k++){
	  objs[k].className='';
  }
  obj.className='hhtc';
  _g('loadimg').style.display="";
}

(function($,options){ $.fn.loading = function(options){
	var o = $.extend(true, {},options);
	return this.each(function() {
		if(!o.div)o.div="body";
		var q;
		if(o.div=="body"){
			q=$(document.body);
			var s=$("<div class='loading'>").css({position:"fixed",'z-index':2,width:"100%",height:"100%",left:"0",top:"0"});
			var r=$("<div class='pop_load loading'>").css({left:($(window).width()/2)-150,top:$(window).height()/2-100}).html("<div class='pop_loading'><dl><dt>请稍等，您查询的酒店正在搜索中......</dt><dd>预订酒店完全免费，全国15000多家酒店任您选！</dd></dl></div>");
		}else{
			q=$(""+o.div+"");
			var s=$("<div class='loading'>").css({position:"absolute",'z-index':2,width:q.outerWidth(),height:q.outerHeight(),left:0,top:0});
			if(o.mini){
					q.css({position:"relative"});
					var r=$("<div class='pop_load1 loading'>").css({position:"absolute",left:(q.width()/2)-100,top:q.height()/2-10}).html("<div class='pop_loading1'>正在加载最新数据，请稍候...</div>");
				}else{
					s.css({left:q.position().left,top:q.position().top});
					var r=$("<div class='pop_load loading'>").css({position:"absolute",left:($(window).width()/2)-150,top:$(window).height()/2-100}).html("<div class='pop_loading'><dl><dt>请稍等，您查询的酒店正在搜索中......</dt><dd>预订酒店完全免费，全国15000多家酒店任您选！</dd></dl></div>");
				}
		}
		r.appendTo(q);s.appendTo(q);s.css({opacity:0,background:'#B0D1E8'}).fadeTo(700,0.4);
		if($.browser.msie&&($.browser.version == "6.0")){$(window).scroll(function(){var f_top = $(window).scrollTop() + $(window).height() - $(window).height()/2-100;$(r).css( 'top' , f_top );});}
	});
}})(jQuery);

(function($,options){ $.fn.simpletooltip = function(options){
	var o = $.extend(true, {},options);
	return this.each(function() {
		var text = (!$(this).attr("title")) ? o.html:$(this).attr("title").replace(/；/g,"<br>");
		if(!text)return;
		if(o.br!=false){var i = 0;var d = "";text=text.replace(/<.+?>/g,'');var le=text.replace(/[\u4e00-\u9fa5]/g, "**").length;for(var a=0;a<(Math.floor(le/50)+1);a++){
var k=String(text).substring(i,i+30);if(i<le&&k!=""){d += (k+"<br>");i+=30;}}text=d;}
		if(!text)return;
		$(this).attr("title", "");
		if ($.trim(o.id) != ''){var id=o.id}else{var id='simpleTooltip'};
		if($.trim(o.el)){
			if ($.trim(id) != ''){var st='id="'+id+'"'}else{var st='id="simpleTooltip"'};
				$(this).attr("title", ""); 
				$("body").append("<div "+st+" style='position: absolute; z-index: 100; display: none;-moz-border-radius:4;-webkit-border-radius:4;'>" + text + "</div>");
				if($.browser.msie) var tipWidth = $("#"+id+"").outerWidth(true)
				else var tipWidth = $("#"+id+"").width()
				$("#"+id+"").width(tipWidth);
				$("#"+id+"").css("left", ($(this).position().left+55)).css("top", ($(this).position().top+15)).fadeIn("medium");
			}
		if(text != undefined) {
			$(this).hover(function(e){
				if ($.trim(id) != ''){var st='id="'+id+'"'}else{var st='id="simpleTooltip"'};
				var tipX = e.pageX + 12;
				var tipY = e.pageY + 12;
				$(this).attr("title", ""); 
				$("body").append("<div "+st+" style='position: absolute; z-index: 100; display: none;-moz-border-radius:4;-webkit-border-radius:4;'>" + text + "</div>");
				if($.browser.msie) var tipWidth = $("#"+id+"").outerWidth(true)
				else var tipWidth = $("#"+id+"").width()
				$("#"+id+"").width(tipWidth);
				$("#"+id+"").css("left", tipX).css("top", tipY).fadeIn("medium");
			}, function(){
				$("#"+id+"").remove();
				$(this).attr("title", text);
			});
			$(this).mousemove(function(e){
				var tipX = e.pageX + 12;
				var tipY = e.pageY + 12;
				var tipWidth = $("#"+id+"").outerWidth(true);
				var tipHeight = $("#"+id+"").outerHeight(true);
				if(tipX + tipWidth > $(window).scrollLeft() + $(window).width()) tipX = e.pageX - tipWidth;
				if($(window).height()+$(window).scrollTop() < tipY + tipHeight) tipY = e.pageY - tipHeight;
				$("#"+id+"").css("left", tipX).css("top", tipY).fadeIn("medium");
			});
		}
	});
}})(jQuery);

function OnClickCompatible(id)
{ 
   var ie=navigator.appName=="Microsoft Internet Explorer" ? true : false;
   if(ie)
   {
       document.getElementById(id).click();
   }
   else
   {
       var a=document.createEvent('MouseEvents');
       a.initEvent('click', true, true);
       document.getElementById(id).dispatchEvent(a);
   }
}

function checkindex(){
	var f = document.form_hotel;
	if(!f.cityid.value||!f.txtCity.value)
	{
		alert("请选择城市！");
		OnClickCompatible('txtCity');return false;
	}
	if(!f.tm1.value)
	{
		alert("请输入入住日期！");
		OnClickCompatible('tm1');return false;
	}
	if(!f.tm2.value)
	{
		alert("请输入离店日期 ！");
		OnClickCompatible('tm2');return false;
	}
	if(!f.minprice.value)f.minprice.value=0;
	if(!f.maxprice.value)f.maxprice.value=0;
	if(isNaN(f.minprice.value)||isNaN(f.maxprice.value))
	{
		alert("房价范围必须为数字!");
		f.minprice.focus();return false;
	}
	if((f.minprice.value*1>=f.maxprice.value*1)&&f.maxprice.value!='0')
	{
		alert("最低价不能高于最高价！");f.minprice.focus();return false;
	}
	if(ZNDate.parse(f.tm1.value)>=ZNDate.parse(f.tm2.value)){
		alert("请输入的日期不合法！");
		OnClickCompatible('tm1');return false;
	}
	$(this).loading({div:"#mainContent"});
	//return false;
}
function rancolor(){var c = (Math.floor(Math.random()*0xffffff).toString(16).toUpperCase());if((c.length!=6)||(parseInt("0x"+c,16)>14474460)){return rancolor();}else{return c;}}
function refreshprice(id,Order_api){
	try{
	var tm1=$("#txtComeDate").val();
	var tm2=$("#txtOutDate").val();
	document.doBook.tm1.value=tm1;
	document.doBook.tm2.value=tm2;
	_v1=null,dn=0,tb1='',wks='',d,hotels=null,n1=0;
	if(tm1!=''&&tm2!=''){
	$("#h"+id+"").html("<p style='text-align:center;'><img src='images/loadprice.gif' /></p>");
	var file = Order_api+"json.php?hid="+id+"&tm1="+tm1+"&tm2="+tm2+"&call=callback";
	var head = document.getElementsByTagName("head")[0] || document.documentElement;
	var js = document.createElement("script");
	js.src = file;
	js.charset = "utf-8";
	head.insertBefore( js,head.firstChild);
	}
	}catch(e){}
}
function dobook(a,b,c){
	document.doBook.hid.value=a;
	document.doBook.rid.value=b;
	document.doBook.pid.value=c;
	document.doBook.submit();
}
function rotatorimg(i){$("#Slider").stop(true,false).animate({top : -210*i},400);$("#numeric li").eq(i).addClass("on").siblings().removeClass("on");}
function rotatorimg2(i){$("#Slider").stop(true,false).animate({top : -83*i},400);}
function addfavorites(site,name){
     if (document.all){  
         window.external.addFavorite(site,name);  
     }else if (window.sidebar){  
         window.sidebar.addPanel(name,site, "");  
     }  
}
function setColor(oName,k,c1,Seconds){
	var obj=$(""+oName+"");k--;
	if(obj.css("backgroundColor")!=c1){obj.css("backgroundColor",c1)}else{obj.css("backgroundColor","");}	
	if(k>0){setTimeout('setColor("'+oName+'",'+k+',"'+c1+'",'+Seconds+')',Seconds)}else{obj.css("backgroundColor","")};
}
function openwindow(url,name,iWidth,iHeight,scrollbar)
{
	
	var url; //转向网页的地址;
	var name; //网页名称，可为空;
	var iWidth; //弹出窗口的宽度;
	var iHeight; //弹出窗口的高度;
	var iTop = (window.screen.availHeight-30-iHeight)/2; //获得窗口的垂直位置;
	var iLeft = (window.screen.availWidth-10-iWidth)/2; //获得窗口的水平位置;
	if(!scrollbar){scrollbar='auto'}
	window.open(url,name,'height='+iHeight+',,innerHeight='+iHeight+',width='+iWidth+',innerWidth='+iWidth+',top='+iTop+',left='+iLeft+',toolbar=no,menubar=no,scrollbars='+scrollbar+',resizeable=no,location=no,status=no');
}