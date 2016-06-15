// JavaScript Document
function chkForm()
{
	if($('pagename').value == ""){
	   alert("请填写页面名！");   
	   $('pagename').focus();
	   return(false);
   	}
	
	if($('title').value == ""){
	   alert("请填写页面标题！");   
	   $('title').focus();	   
	   return(false);
   	}	
	
	if($('keywords').value == ""){
	   alert("请填写页面关键字！");   
	   $('keywords').focus();
	   return(false);
   	}
	
	if($('description').value == ""){
	   alert("请填写页面关键字！");   
	   $('description').focus();
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