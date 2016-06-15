// JavaScript Document
function changeCommentType(comment_id, type)
{
	var _url = php_self+'?m=comment.changeCommentType';	
	var _para = {};
	_para['comment_id'] = comment_id;
	_para['type'] = type;	
	var Ajax = new classAjax();
	Ajax.setRequest(_url, _para, function(data){
		var json = eval('['+data+']');
		var id = json[0]['id'];
		var data = json[0]['data'];
		var error = json[0]['error'];
		if(error == 1){
			alert('失败，请重试');
		} else {
			$('type_'+id).innerHTML = data;
		}
	});		
}

function checkDelForm(){
	var check = GetCheckboxValue('id_a[]');
	var comment_do = $('comment_do').value;
	if( comment_do == '' )
	{
		alert("好像您没有选择任何管理操作吧?:-(");	
		document.getElementById('comment_do').focus();
		return false;		
	}
	if( check == '')
	{
		alert("好像您没有选择任何要操作的评论吧?:-(");	
		return false;
	}
}


function chkForm()
{
	if($('comment_author').value == ""){
	   alert("请填写评论作者！");   
	   $('comment_author').focus();
	   return(false);
   	}
	if($('comment_author_email').value == ""){
	   alert("请填写电子邮件！");   
	   $('comment_author_email').focus();
	   return(false);
   	}

	if($('comment_content').value == ""){
	   alert("请填写评论内容！");   
	   $('comment_content').focus();
	   return(false);
   	}			
}