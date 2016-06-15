function $(str){
	return document.getElementById(str);
}

function $_(str){
	return document.getElementsByName(str);
}

// JavaScript Document
//浏览器判断
var lBrowser = {}; 
lBrowser.agt = navigator.userAgent.toLowerCase(); 
lBrowser.isW3C = document.getElementById ? true:false; 
lBrowser.isIE = ((lBrowser.agt.indexOf("msie") != -1) && (lBrowser.agt.indexOf("opera") == -1) && (lBrowser.agt.indexOf("omniweb") == -1)); 
lBrowser.isNS6 = lBrowser.isW3C && (navigator.appName=="Netscape"); 
if(lBrowser.isNS6){ //firefox innerText define 
	HTMLElement.prototype.__defineGetter__( "innerText", function(){  return this.textContent;});  
	HTMLElement.prototype.__defineSetter__( "innerText", function(sText){ this.textContent=sText;});  
} 

function trim(a){  
	//   	type 1:
	//   	用正则表达式将前后空格  
	//   	用空字符串替代。  
	   	if (typeof(a)=="string") return a.replace(/(^\s+)|(\s+$)/g,'');  
		else return a;
	//	type 2:
/*	for(var i=0; i<a.length && (a.charAt(i)<'0'); i++);
    for(var j=a.length; j>0 && (a.charAt(j-1)<'0'); j--);
    if (i>j) return '';  
    return a.substring(i+2,j);  
*/
}

// Ajax类
function classAjax(){
	var _z = false; //xmlHTTP
	try {
		_z = new XMLHttpRequest();
	}catch (trymicrosoft){
		try {
			_z = new ActiveXObject("Msxml2.XMLHTTP");
		}catch (othermicrosoft){
			try {
				_z = new ActiveXObject("Microsoft.XMLHTTP");
			}catch (failed){
				_z = false;
			}
		}
	}
	this.setRequest = function(url,_para,fun){
		_z.open("POST",url,true); 
		_z.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		_z.onreadystatechange = function(){
			if (_z.readyState==4){
				if (_z.status==200){
					fun(_z.responseText);
				}
			}
		}
		var para = '';
		for(i in _para){para += "&"+i+"="+_para[i];}
		para = para.substr(1)
		_z.send(encodeURI(para));
	}
}

//Dom 

function A(a, b, c, d, e, f, g){
  var _z = document.createElement("div");
  	a.appendChild(_z);
	vec(_z, b, c, d, e);
	if (f){
		for (var _y in f){
			_z.style[_y] = f[_y]; 
			if (_y=="class"){
				_z.className=f[_y]
			}
		}
	}
	if (g) I(_z, g);
    return _z;
}
function Aa(a, b, c, d, e, f, g){
	var _z = document.createElement("a");
	_z.href="#";
	vec(_z, b, c, d, e);
	if (f){
		for (var _y in f){
			if (_y=="class"){
				_z.className=f[_y]
			}else{
				_z.style[_y] = f[_y]; 
			}
		}
	}
	if (g) I(_z, g);
	a.appendChild(_z);
    return _z;
}
function Aspan(a, b){
	var _z = document.createElement("span");
	_z.style.position="static";
	if (b) I(_z, b);
	a.appendChild(_z);
    return _z;
}
function I(a, b){
	if (!a) return;
	a.innerHTML = (b) ? b : '';
}
function vE(a){
	$(a).style.display = "";
}
function hE(a){ 
	$(a).style.display = "none";
}
function iV(a){
	if ($(a).style.display=="none") return false;
	else return true;
}
function cE(a){
	if (iV(a))hE(a);
	else vE(a);
}
function vec(a, b, c, d, e){
	if (b) a.style.left = b;
	if (c) a.style.top = c;
	if (d) a.style.width = d;
	if (e) a.style.height = e;
}

function getRadioValue(a)	//得到radio的value值
{
	var Obj = document.getElementsByName(a);
	for(i=0;i<Obj.length;i++)
		{
			if(Obj[i].checked){break}
		};
	if(i==Obj.length)
		{
			filetype = "";
		}
		else
		{
			filetype = Obj[i].value;
		}	
	return filetype;
}

function CheckRadio(obj){
	o = $_(obj);
	p = false;
	for(i = 0;i < o.length;i ++){
		if (o[i].checked)
		{
			p = true;
			break;
		}
	}
	return p;
}

function GetRadioValue(obj){
	o = $_(obj);
	for(i = 0;i < o.length;i ++){
		if (o[i].checked)
		{
			myvalue = o[i].value;
			break;
		}
	}
	return myvalue;
}

function GetCheckboxValue(obj){
	o = $_(obj);
	var myvalue = '';
	for(i = 0;i < o.length;i ++){
		if (o[i].checked)
		{
			myvalue += o[i].value + ",";
		}
	}
	return myvalue.substring(0,myvalue.length-1);
}

function CheckBoxValue(obj,value){
	var objvalue = "," + GetCheckboxValue(obj) + ",";
	var value = "," + value + ",";
	if(objvalue.indexOf(value) > -1)
		return 1;
	else
		return 0;
}

function SeePic(img,f)
{
	if( f.value != '' ) 	img.src = f.value;
	if( f.value == '0' ) 	img.src = '/control/images/picview.gif';
}

function select_fx(){
	for(var i=0;i<document.list_form.elements.length;i++){
		var obj = document.list_form.elements[i];
		if(obj.name=="id_a[]"&&obj.type=="checkbox"){
			obj.checked = !obj.checked;
		}
	}
}