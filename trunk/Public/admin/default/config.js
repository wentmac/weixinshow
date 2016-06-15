// JavaScript Document
function rplCity(pid)
{
	var cid=arguments[1]?arguments[1]:0;
	if (pid!='0'){
		document.getElementById("city").length=0;
		var _url = 'index.php?m=admin/article.getCity';	
		var _para = {};
		_para['pid'] = pid;	
		_para['type'] = 'no';	
		var Ajax = new classAjax();
		Ajax.setRequest(_url, _para, function(data){
			var obj = eval(data);
			document.getElementById("city").options.add(new   Option('--请选择--','0'));
			for(var i=0;i<data.length;i++)   
			{   
				var   id   =   obj[i].cityid+'|'+obj[i].cityname;   
				var   name   =   obj[i].cityname;
				document.getElementById("city").options.add(new   Option(name,id));
				if(cid == id) 
				{ 
					document.getElementById("city").options[i+1].selected=true; 
				}
			} 
		});	 		
	}
}


function chkForm()
{
	if($('nvarname').value == ""){
	   alert("请填写变量名称！");   
	   $('nvarname').focus();	   
	   return(false);
   	}

	
	if(!CheckRadio('vartype')){
	   alert("请选择变量类型！");   
  	   return(false);
	} else {
		if(GetRadioValue('vartype') == 'select' || GetRadioValue('vartype') == 'radio'){
			
			if($('item').value == ''){
				alert('请填写数据值！')
		  	    $('item').focus();	
				return false;		
			}			
			
		}
	}
	
	if($('varmsg').value == ""){
		alert("请填写参数说明！");
	   $('varmsg').focus();			
		return false;	
	}	

}

function typechange(v)
{
	if(v == 'select' || v == 'radio'){
		$('changetype').style.display='block';		
	} else { 
		$('changetype').style.display='none';			
	}
}