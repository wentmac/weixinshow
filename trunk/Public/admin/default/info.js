function rplCity(pid)
{
	var cid=arguments[1]?arguments[1]:0;
	if (pid!='0'){
		document.getElementById("cityid").length=0;
		var _url = php_self+'?m=info.getCity';	
		var _para = {};
		_para['pid'] = pid;	
		var Ajax = new classAjax();
		Ajax.setRequest(_url, _para, function(data){
			var obj = eval(data);
			document.getElementById("cityid").options.add(new   Option('--请选择--','0'));
			for(var i=0;i<data.length;i++)   
			{   
				var   id   =   obj[i].cityid;   
				var   name   =   obj[i].cityname;
				document.getElementById("cityid").options.add(new   Option(name,id));
				if(cid == id) 
				{ 
					document.getElementById("cityid").options[i+1].selected=true; 
				}
			} 
		});	 		
	}
}

// JavaScript Document
function chkForm()
{
	if($('class_id').value == "0"){
	   alert("请选择所属类别！");   
	   $('class_id').focus();
	   return(false);
   	}
	
	if($('cityid').value == "0"){
	   alert("请选择城市类别！");   
	   $('cityid').focus();	   
	   return(false);
   	}	
	
	if($('title').value == ""){
	   alert("请填写标题！");   
	   $('title').focus();
	   return(false);
   	}	
	
	if(!CheckRadio('state_radio')){
	   alert("请选择状态！");   
  	   return(false);
	} else {
		if(GetRadioValue('state_radio') == 3){
			if($('thumb').value == ''){
				alert('请上传图片！')
		  	    $('thumb_upload').focus();	
				return false;		
			}
		}
	}
	
	if($('author').value == ""){
	   alert("请填写作者！");   
	   $('author').focus();
	   return(false);
   	}
	
	if($('order').value == ""){
	   alert("请填写排序！");   
	   $('order').focus();
	   return(false);
   	}					
	
	var oEditor = FCKeditorAPI.GetInstance('content');   
    var checkContent = oEditor.GetXHTML();
	if( checkContent == '' )
	{
		alert('请填写内容');	
		return(false);		
	}
}

function checkDelForm(){
	var check = GetCheckboxValue('id_a[]');
	if( check == '' )
	{
		alert("好像您没有选择任何要删除资讯吧?:-(");	
		return false;
	}
}

function exviewimg(v)
{
	if(v == '3'){
		$('lm2').style.display='block';
	} else {
		$('lm2').style.display='none';
	}
}
