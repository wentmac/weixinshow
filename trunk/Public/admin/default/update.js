function LoadUpdateInfos()
{
	var _url = 'index.php?m=admin/update.checkUpdate';	
	var _para = {};
	_para['do'] = 'check';	
	var Ajax = new classAjax();
	Ajax.setRequest(_url, _para, function(data){
		$('updateinfos').innerHTML = data;
	});	
}

function ShowWaitDiv()
{
	$('loading').style.display = 'block';
	return true;
}
