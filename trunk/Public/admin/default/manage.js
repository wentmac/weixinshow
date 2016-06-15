// JavaScript Document
function chkForm()
{
	if($('username').value == ""){
	   alert("请填写用户名！");   
	   $('username').focus();
	   return(false);
   	}
	
	if($('name').value == ""){
	   alert("请填写真实姓名！");   
	   $('name').focus();	   
	   return(false);
   	}	

	if($('id').value == ""){	
		if($('password').value == ""){
		   alert("请填写密码！");   
		   $('password').focus();
		   return(false);
		}
	}
}

function checkDelForm(){
	var check = GetCheckboxValue('id_a[]');
	if( check == '' )
	{
		alert("好像您没有选择任何要删除的吧?:-(");	
		return false;
	}
}
