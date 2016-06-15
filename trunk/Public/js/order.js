function check()
{
	if($('oname').value == ""){
	   alert("请填写入住人姓名！");   
	   $('oname').focus();	   
	   return(false);
   	}	
	if($('mobile').value == ""){
	   alert("请填写联系人手机！");   
	   $('mobile').focus();	   
	   return(false);
	} else {
		var p1 =/^((\(\d{2,3}\))|(\d{3}\-))?(13|15|18)\d{9}$/;                     //判断 手机  	
		var strPhone = $('mobile').value;
		if(p1.test(strPhone)==false){
		   alert("手机号码格式不对！");    
	   	   $('mobile').focus();	    
		   return false;
		}
	}
}