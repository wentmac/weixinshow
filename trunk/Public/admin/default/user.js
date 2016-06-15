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

	if($('uid').value == ""){	
		if($('password').value == ""){
		   alert("请填写密码！");   
		   $('password').focus();
		   return(false);
		}
	}
	

	if($('email').value != ""){	
		var tip = $('email');
		var a = $('email').value;
		//var emailPat=/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
		var emailPat=/^(([0-9a-zA-Z]+)|([0-9a-zA-Z]+[_.0-9a-zA-Z-]*[_.0-9a-zA-Z]+))@([a-zA-Z0-9-]+[.])+([a-zA-Z]{2}|net|NET|com|COM|gov|GOV|mil|MIL|org|ORG|edu|EDU|int|INT)$/;  
		var matchArray=a.match(emailPat);
		if (matchArray==null) {
			alert("电子邮件地址格式无效");
		   $('email').focus();
			return false;
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
