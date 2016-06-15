/*
 *	通用JS验证类
 *	使用方法：
 *	var formValidate = new formValidate();
 *	formValidate.init({});
 * 	注意：
 *	<form action="" method="post" id="formValidate"> 
 *	id为formValidate
 *
 *  <input name="" type="text" validate="zip_code" empty="yes" min=10 max=10 /><span></span>
 *	validate="zip_code"		验证是否是邮政编码
 * 	empty="yes"				验证是否允许为空
 *	min=10					最小长度
 * 	max=10					最大长度
 *	<span></span>			显示提示内容
 */
var formValidate = function () {

	var _this = this;

	this.options = {
		number : {reg : /^[0-9]+$/, str : '必须为数字'},
		decimal : {reg : /^[-]{0,1}(\d+)[\.]+(\d+)$/ , str : '必须为DECIMAL格式'},
		english : {reg : /^[A-Za-z]+$/, str : '必须为英文字母'},
		upper_english : {reg : /^[A-Z]+$/, str : '必须为大写英文字母'},
		lower_english : {reg : /^[a-z]+$/, str : '必须为小写英文字母'},
		email : {reg : /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/, str : 'Email格式不正确'},
		chinese : {reg : /[\u4E00-\u9FA5\uf900-\ufa2d]/ig, str : '必须含有中文'},
		url : {reg : /^[a-zA-z]+:\/\/[^s]*/, str : 'URL格式不正确'},
		phone : {reg : /^[1][3][0-9]{9}$/ , str : '电话号码格式不正确'},
		ip : {reg : /^(\d+)\.(\d+)\.(\d+)\.(\d+)$/ , str : 'IP地址格式不正确'},
		money : {reg : /^[0-9]+[\.][0-9]{0,3}$/ , str : '金额格式不正确'},
		number_letter : {reg : /^[0-9a-zA-Z\_]+$/ , str : '只允许输入英文字母、数字、_'},
		zip_code : {reg : /^[a-zA-Z0-9 ]{3,12}$/ , str : '邮政编码格式不正确'},
		account : {reg : /^[a-zA-Z][a-zA-Z0-9_]{4,15}$/ , str : '账号名不合法，允许5-16字符，字母下划线和数字'},
		qq : {reg : /[1-9][0-9]{4,}/ , str : 'QQ账号不正确'},
		card : {reg : /^(\d{6})(18|19|20)?(\d{2})([01]\d)([0123]\d)(\d{3})(\d|X)?$/ , str : '身份证号码不正确'},
	};

	//初始化 绑定表单 选项
	this.init = function (options) {
		this.setOptions(options);
		//this.checkForm();
	};

	//设置参数
	this.setOptions = function (options) {
		for (var key in options) {
			if (key in this.options) {
				this.options[key] = options[key];	
			}
		}
	};

	//检测表单 包括是否为空，最大值 最小值，正则验证
	this.checkForm = function () {
		$("#formValidate").submit(function () {
			var formChind = $("#formValidate").children();
			var testResult = true;
			formChind.each(function (i) {
				var child 		= formChind.eq(i);
				var value 	  	= child.val();
				var len 	  	= value.length;
				var childSpan 	= child.next();

				//属性中是否为空的情况
				if (child.attr('empty')) {		
					if (child.attr('empty') == 'yes' && value == '') {
						if (childSpan) {
							childSpan.html('');
						}
						return;
					}
				}

				//属性中min 和 max 最大和最小长度
				var min = null;
				var max = null;
				if (child.attr('min')) min = child.attr('min');
				if (child.attr('max')) max = child.attr('max');
				if (min && max) {
					if (len < min || len > max) {
						if (childSpan) {
							childSpan.html('');
							childSpan.html('  字符串长度在' + min + '与' + max + '之间');
							testResult = false;
							return;
						}
					}
				} else if (min) {
					if (len < min) {
						if (childSpan) {
							childSpan.html('');
							childSpan.html('  字符串长度应大于' + min);
							testResult = false;
							return;
						}
					}
				} else if (max) {
					if (len > max) {
						if (childSpan) {
							childSpan.html('');
							childSpan.html('  字符串长度应小于' + max);
							testResult = false;
							return;
						}
					}
				}
				
				//正则校验
				if (child.attr('validate')) {
					var type 	= child.attr('validate');
					var result	= _this.check(value, type);
					if (childSpan) {
						childSpan.html('');
						if (result != true) {
							childSpan.html('  ' + result);
							testResult = false;
						}
					}
				}

			});
			return testResult;
		});
	};

	//检测单个正则选项
	this.check = function (value, type) {
		if (this.options[type]) {
			var val = this.options[type]['reg'];
			if (!val.test(value)) {
				return this.options[type]['str'];
			}
			return true;
		} else {
			return '找不到该表单验证正则项';
		}
	};


}