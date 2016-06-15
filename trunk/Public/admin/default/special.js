function check(v)
{
	var id = v+'_state';
	var name = v+'_name';
	var img = v+'_img';
	var link = v+'_link';
	
	var id_value = GetRadioValue(id);
	var name_value = $(name).value;
	var img_value = $(img).value;
	var link_value = $(link).value;	

	if(id_value == 2){confirm('确认要删除该专题吗?');return false;}
	var _url = 'index.php?m=admin/special.edit';	
	var _para = {};
	_para['xmlname'] = v;		
	_para['id'] = id_value;	
	_para['name'] = name_value;	
	_para['img'] = img_value;	
	_para['link'] = link_value;				
	var Ajax = new classAjax();
	Ajax.setRequest(_url, _para, function(data){
		var obj = eval('(' + data + ')');
		if(obj.rs == 0){
			alert('修改失败请重试!');
		}
		if(obj.rs == 1){
			alert('修改成功!');
		}
		if(obj.rs == 2){
			alert('删除成功!');
			window.location.reload(); 
		}
		
	});
}