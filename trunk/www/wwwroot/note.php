<?php
$host = 'http://'.$_SERVER['HTTP_HOST'].'/';
$apiUrl = $host;
?>
<html>
    <head>
        <title>接口清单！</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style type="text/css">
            <!--
            body,td,th {
                font:12px/1.5 "Microsoft Yahei",Tahoma, Helvetica, Arial, "SimSun", sans-serif;
                background:#3F3F3F;
                color:#DCDCCC
            }
            a {
                font-size： 12px;
                color:#CC9393;
				text-decoration:none;
            }
            a:hover{
                color:#AF6755;
				text-decoration:underline;
            }
            a:visited {color: #6E9F7F} 

            #main pre {
                background-color： #F5F5F5;
                border： 1px solid #CDCDCD;
                overflow： auto;
                width： 668px;
            }			
            -->			
			.apiUl li dl{display:none}	
			.cursor{cursor:pointer;}			
        </style></head>
    <body>
<?php
        $indexUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $pwd = empty($_POST['pwd']) ? 0 : $_POST['pwd'];
        if (empty($pwd)) {
?>  
            <form name="forms" id="forms" action="" method="post">
                <LI>password:<Input type='text' name='pwd'><input type='submit' value='提交'></li>    
            </form>
<?php
die;
        } else {
            if ($pwd <> 'pharos_2014') {
                echo 'password is error!<br>Access Denied!';
                die;
            }
        }
?>        
        <div id="main">
            <ul>
                <h2>约定和标准</h2>
            </ul>
            <ul>
                <li><?php echo $apiUrl;?> API是采用REST基础的接口规范。所有的API都是通过HTTP GET、POST方法向<?php echo $apiUrl;?> API Server(<?php echo $apiUrl;?>)发送请求实现的。</li>
                <li>所有返回均为json格式</li>                
                <li><?php echo $apiUrl;?> API Server 支持gzip，这样能降低网络的开销，建议开发者在应用中加入gzip。</li>
                <li>接口中日期和时间的约定： %Y-%m-%d %H：%M：%S 如： 2010-10-10 10：10：10</li>
            </ul>

            <ul><h2>编码</h2></ul>
            <ul>
                <Li><?php echo $apiUrl;?> API Server 假定客户端的请求均为UTF-8编码格式进行处理。所有的数据都将以UTF-8编码后存储。</Li>
                <li><?php echo $apiUrl;?> API Server 返回的字符型字段均进行了html转义, 客户端在显示这些字段时进行反转义。以下为被转义的字符对照：</li>
                <div class="code"><pre>    &amp; =&gt; &amp;amp;
    &lt; =&gt; &amp;lt;
    &gt; =&gt; &amp;gt;
    ' =&gt; &amp;#39;
    " =&gt; &amp;quot;	</pre></div>
            </ul>

            <ul><h2>通用返回值说明</h2></ul>
            <ul>
                <li><?php echo $apiUrl;?> API Server接受客户端请求后进行处理, 如果在处理过程中存在错误,如客户端上传的参数不合法, <?php echo $apiUrl;?> API Server将返回HTTP状态码400, 相应的错误回应,格式如下：</li>
                <li>1.所有返回均为json格式，包括如下三个字段:
                    <ul>
                        <LI>status: int 0表示正常，非0表示异常。</li>
                        <LI>success: boolean true表示正常，false表示异常。</li>
                        <LI>data: array 实际的返回数据。返回异常时无此字段。</li>
                        <LI>message: string 异常描述。返回正常时无此字段。</li>
                    </ul>
                </li>
                <li>2.备注
                    <Ul>		
                        <li>请将请求的域名作为全局常量或者配置项，方便日后修改域名。</li>			
                    </ul>	
                </li>
                <li>接口中带有列表形式的返回值, 需要分页执行的返回的格式如下：</li>
                <li>retHeader：array()(服务器返回的数据总数,当前页,总页数)</li>
                <li>retmsg：1(1：成功|0：失败)</li>
                <li>retcode：search(服务器接收到的请求信息)</li>
                <li>reqdata：array()(服务器返回数据数组)</li>        
            </ul>

			<ul><h2>参数签名算法</h2></ul>
            <ul>
                <li>参数签名生成算法采取如下方式（PHP版），其它语言根据注释描述完成等同功能：</li>
                <li>
                    <dl>
                        <dt>API系统必选参数：(GET)</dt>                            
                            <dd>timestamp=时间戳 (API服务端允许客户端请求时间误差为10分钟) int</dd>
                            <dd>appkey=分配给客户端的Key string</dd>
                            <dd>sign=根据key计算出来的签名 string</dd>
                    </dl>
                    <dl>
                        <dt>按照参数名称升序排列：</dt>                            
                            <dd>appkey=分配给客户端的Key string</dd>
                            <dd>timestamp=时间戳 (API服务端允许客户端请求时间误差为60分钟) int</dd>
                            <dd>method=API接口名称 string</dd>
                    </dl>                    
                    <dl>
                        <dt>拼装字符串，连接参数名与参数值：</dt>                            
                            <dd>appkey=<em>$appkey</em>method=vendor/room.setInfosignsecret=<em>$sceret</em>timestamp=1351274609</dd>
                            <dd><img src="createSignStringFunction.png"></dd>
                        <dt>生成签名(sign)：</dt>
                            <dd>32位小写md5值->a382b989615a40b3d619193f13a5bb8f</dd>
                    </dl>                     
                </li>
                
                
            </ul>   		
			
			
            <ul><h3>账户系统</h3></ul>
            <ul class="apiUl">
                <li><a class="cursor">发送激活短信码(member.sendVerifyCode)</a>	
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>member.sendVerifyCode" target="_blank"><?php echo $apiUrl;?>member.sendVerifyCode</a></dt>
						<dt>必选参数：(GET)</dt>							
							<dd>无</dd>
						<dt>必选参数：(POST)</dt>
							<dd>mobile=要发送的验证码的手机号 string</dd>							
						<dt>可选参数</dt>
							<dd>无</dd>
						<dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>		
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li></li>                                
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">短信码激活(member.verifyMobile)</a>
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>member.verifyMobile" target="_blank"><?php echo $apiUrl;?>member.verifyMobile</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>无</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>mobile=要发送的验证码的手机号 string</dd>                          
                            <dd>sms_code=收到的验证码 string</dd>                          
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>uid=用户uid int</li>                                
                                <li>token=用户的登录token (如果token为空，则进入设置密码的界面) string</li>                                
								<li>username=用户名 string</li>
								<li>member_type=用户类型 （1：学生｜2：家长｜3：教师｜4：学校管理员） int</li>
								<li>member_class=用户子类型，当member_type=1时（1：课代表｜2:信息委员）当member_type=2时（1：父亲｜2：母亲）int</li>
                            </ul>
                        </dd>
                    </dl>
                </li>
                <li><a class="cursor">重置密码(member.setPassword)</a>
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>member.setPassword" target="_blank"><?php echo $apiUrl;?>member.setPassword</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>                            
                        <dt>必选参数：(POST)</dt>
                            <dd>uid=用户UID int</dd>							
                            <dd>password=要重置的密码 string</dd>                          
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>uid=用户uid int</li>                                
                                <li>token=用户的登录token (以后操作用户个人中心的必传字段) string</li>                                
								<li>username=用户名 string</li>
								<li>member_type=用户类型 （1：学生｜2：家长｜3：教师｜4：学校管理员） int</li>
								<li>member_class=用户子类型，当member_type=1时（1：课代表｜2:信息委员）当member_type=2时（1：父亲｜2：母亲）int</li>
                            </ul>
                        </dd>
                    </dl>
                </li>
			</ul>	
			
			
            <ul><h3>公共IM</h3></ul>
            <ul class="apiUl">
                <li><a class="cursor">关闭IM聊天窗口时更新聊天会话的最后更新时间(member.set_chat_last_read_time)</a>	
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>member.set_chat_last_read_time" target="_blank"><?php echo $apiUrl;?>member.set_chat_last_read_time</a></dt>
						<dt>API必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
						<dt>业务必选参数：(POST)</dt>
							<dd>other_uid=IM聊天对方用户的uid int</dd>							
							<dd>chat_group_id=IM聊天的情景会话模式的时的chat_group_id (如果是单独交流请传值：0) int</dd>							
							<dd>last_message_content=IM聊天对话中最后一条消息 (有可能是自己发的也有可能是对方发的) string</dd>							
						<dt>可选参数</dt>
							<dd>无</dd>
						<dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>		
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li></li>                                
                            </ul>
                        </dd>
                    </dl>
                </li>
				
                <li><a class="cursor">通过环信用户名（md5）取用户在系统中的uid(member.get_uid_by_md5)</a>	
                    <dl>						
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>member.get_uid_by_md5" target="_blank"><?php echo $apiUrl;?>member.get_uid_by_md5</a></dt>
						<dt>API必选参数：(GET)</dt>  
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
						<dt>业务必选参数：(GET)</dt>
							<dd>uid_md5=环信用户名 （32位MD5） string</dd>
						<dt>可选参数</dt>
							<dd>无</dd>
						<dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>		
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>uid=系统用户uid int</li>                                
								<li>username=系统用户呢称 string</li>                                
                            </ul>
                        </dd>
                    </dl>
                </li>				
			</ul>				
			
            <ul><h3>家长版-主页</h3></ul>
            <ul class="apiUl">
                <li><a class="cursor">课堂表现(parents/home.classShow)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/home.classShow" target="_blank"><?php echo $apiUrl;?>parents/home.classShow</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>chart=左边的柱图数据 (0:代表今天，1:代表昨天，2:代表前天) 有可能只有今天没有昨天和前天 array</li>                                
                                <li>description=大描述 string</li>                                
                                <li>describe=小描述 string</li>                                
                                <li>notice=信息通知数量 array
                                    <ul>
                                        <li>ordinary=普通信息数量 int</li>
                                        <li>important=重要信息数量 int</li>
                                    </ul>
                                </li>                                
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业(parents/home.homework)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/home.homework" target="_blank"><?php echo $apiUrl;?>parents/home.homework</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>chart=左边的饼图数据 array</li>                                
                                <li>description=大描述 string</li>                                
                                <li>describe=小描述 string</li>                                
                                <li>notice=信息通知数量 array
                                    <ul>
                                        <li>ordinary=普通信息数量 int</li>
                                        <li>important=重要信息数量 int</li>
                                    </ul>
                                </li>                                
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">成绩(parents/home.score)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/home.score" target="_blank"><?php echo $apiUrl;?>parents/home.score</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>chart=左边的柱图数据 (0:代表今天，1:代表昨天，2:代表前天) 有可能只有今天没有昨天和前天 array</li>                                
                                <li>description=大描述 string</li>                                
                                <li>describe=小描述 string</li>                                
                                <li>notice=信息通知数量 array
                                    <ul>
                                        <li>ordinary=普通信息数量 int</li>
                                        <li>important=重要信息数量 int</li>
                                    </ul>
                                </li>                                
                            </ul>
                        </dd>
                    </dl>
                </li>                

            </ul>

            <ul><h3>家长版-成绩</h3></ul>
            <ul class="apiUl">
                <li><a class="cursor">成绩-成绩趋势-控制(parents/score.trend_control)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.trend_control" target="_blank"><?php echo $apiUrl;?>parents/score.trend_control</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>subject=科目的标题 array
                                    <ul>
                                        <li>id=科目ID int</li>
                                        <li>name=科目名称 string</li>
                                    </ul>                                        
                                </li>                                
                                <li>provider=数据对比数组 array
                                    <ul>
                                        <li>id=对比数据的ID int</li>
                                        <li>name=对比数据的名称 string</li>
                                    </ul>
                                </li>                                
                                <li>exam_type=考试分类 array
                                    <ul>
                                        <li>id=考试分类ID int</li>
                                        <li>name=考试分类名称 string</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li>  

                <li><a class="cursor">成绩-成绩趋势-控制查询结果(parents/score.trend_control_search)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.trend_control_search" target="_blank"><?php echo $apiUrl;?>parents/score.trend_control_search</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>
                            <dd>provider_id=数据对比ID (1,2,4) intString</dd>
                        <dt>可选参数：(POST)</dt>
                            <dd>exam_type_id=考试类型ID (1,2) intString</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>exam_date=考试时间 chart的x轴 date</li>                                
                                <li>provider_name=数据对比名称数组 array
                                    <ul>
                                        <li>provider_id=provider_name</li>
                                    </ul>
                                </li>                                
                                <li>provider_value=数据对比值数组 array
                                    <ul>
                                        <li>provider_id=provider_value</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li> 

                <li><a class="cursor">成绩-成绩趋势-分析(parents/score.trend_analyze)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.trend_analyze" target="_blank"><?php echo $apiUrl;?>parents/score.trend_analyze</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>content=内容 string</li>                                                        
                                <li>analyze=分析 string</li>                                                        
                                <li>complex=综合分析内容 string</li>                                                        
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">成绩-成绩趋势-交流(parents/score.trend_ac)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.trend_ac" target="_blank"><?php echo $apiUrl;?>parents/score.trend_ac</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>id=科目ID [当id=0是代表班主任] int</li>                                                              
                                <li>name=科目名称 string</li>
                                <li>uid=科目对应的老师UID 用于发于IM聊天时使用 int</li>
                                <li>username=用户名称老师名称 stirng</li>
								<li>chat_group_id=情景聊天组ID int</li>
                            </ul>
                        </dd>
                    </dl>
                </li> 

                <li><a class="cursor">成绩-成绩趋势-详情(parents/score.trend_detail)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.trend_detail" target="_blank"><?php echo $apiUrl;?>parents/score.trend_detail</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>
							<dd>exam_type_id=考试类型ID (1,2) intString</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>exam_name=考试名称 string</li>                                                              
                                <li>exam_date=大考的时间 string</li>
                                <li>student_name=参加考试的同学名称 string</li>
                                <li>exam_result_table=考试结果的数据 array
                                    <ul>
                                        <li>subject=科目名称 string</li>
                                        <li>score=分数 int</li>
                                        <li>class_rank=班级排名 int</li>
                                        <li>grade_rank=年级排名 int</li>
                                        <li>rank_status=排名较上次考试的升降 (up|down) string</li>
                                        <li>class_average_rank=班级平均分排名 int</li>
                                        <li>grade_average_rank=年级平均分排名 int</li>
                                        <li>exam_date=科目考试时间 有可能为空，为空时显示上面的大考时间 string</li>
                                    </ul>
                                </li>
                                <li>class_rank=所有科目的班级排名 int</li>
                                <li>grade_rank=所有科目的年级排名 int</li>
                            </ul>
                        </dd>
                    </dl>
                </li>     

                <li><a class="cursor">成绩-单次考试-考试(parents/score.single_exam)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.single_exam" target="_blank"><?php echo $apiUrl;?>parents/score.single_exam</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>subject_id=科目ID (当ID＝0时代表此栏是大考) int</li>                                                              
                                <li>subject_name=科目ID对应的科目名称 string</li>
                                <li>exam_array=考试数据 array
                                    <ul>
                                        <li>exam_id=考试ID int</li>
                                        <li>exam_name=考试名称 string</li>
                                    </ul>
                                </li>
                            </ul>
                        </dd>
                    </dl>
                </li> 

                <li><a class="cursor">成绩-单次考试-控制(parents/score.single_control)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.single_control" target="_blank"><?php echo $apiUrl;?>parents/score.single_control</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>subject=科目的标题 如果是小考的话subject节点不返回 array
                                    <ul>
                                        <li>id=科目ID int</li>
                                        <li>name=科目名称 string</li>
                                    </ul>                                        
                                </li>                                
                                <li>provider=数据对比数组 array
                                    <ul>
                                        <li>id=对比数据的ID int</li>
                                        <li>name=对比数据的名称 string</li>
                                    </ul>
                                </li>                                                                                           
                            </ul>
                        </dd>
                    </dl>
                </li>


                <li><a class="cursor">成绩-单次考试-控制查询结果(parents/score.single_control_search)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.single_control_search" target="_blank"><?php echo $apiUrl;?>parents/score.single_control_search</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>                            
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>
                            <dd>provider_id=数据对比ID (1,2,4) intString</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>name=科目名称 雷达图的角或折线图的x轴 string</li>                                
                                <li>provider_name=数据对比名称数组 array
                                    <ul>
                                        <li>provider_id=provider_name</li>
                                    </ul>
                                </li>                                
                                <li>provider_value=数据对比值数组 array
                                    <ul>
                                        <li>provider_id=provider_value</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li>


                <li><a class="cursor">成绩-单次考试-分析(parents/score.single_analyze)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.single_analyze" target="_blank"><?php echo $apiUrl;?>parents/score.single_analyze</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>
							<dd>subject_id＝科目ID (1,2,4) intString</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>content=内容 string</li>                                                        
                                <li>analyze=分析 string</li>                                                        
                                <li>complex=综合分析内容 string</li>                                                        
                            </ul>
                        </dd>
                    </dl>
                </li>                                 

                <li><a class="cursor">成绩-单次考试-评价(parents/score.single_comment)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.single_comment" target="_blank"><?php echo $apiUrl;?>parents/score.single_comment</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>id=评价ID int</li>
                                <li>subject_id=科目ID int</li>
                                <li>subject_name=科目名称 string</li>
                                <li>uid=评价老师的UID int</li>
                                <li>username=评价老师的名称 string</li>
                                <li>comment_content=评价内容 string</li>
                                <li>comment_time=评价时间 string</li>
                            </ul>
                        </dd>
                    </dl>
                </li>


                <li><a class="cursor">成绩-单次考试-交流(parents/score.single_ac)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.single_ac" target="_blank"><?php echo $apiUrl;?>parents/score.single_ac</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>id=科目ID [当id=0是代表班主任] int</li>                                                              
                                <li>name=科目名称 string</li>
                                <li>uid=科目对应的老师UID 用于发于IM聊天时使用 int</li>
                                <li>username=用户名称老师名称 stirng</li>Î
                                <li>im_status=是否情景交流 (true:im交流|false:情景交流) boolean</li>Î
								<li>chat_group_id=情景聊天组ID int</li>                                                                                            
                            </ul>
                        </dd>
                    </dl>
                </li>                                                      

                <li><a class="cursor">成绩-单次考试-详情(parents/score.single_detail)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/score.single_detail" target="_blank"><?php echo $apiUrl;?>parents/score.single_detail</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>
							<dd>subject_id＝科目ID (1,2,4) intString</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>exam_name=考试名称 string</li>                                                              
                                <li>exam_date=大考的时间 string</li>
                                <li>student_name=参加考试的同学名称 string</li>
                                <li>exam_result_table=考试结果的数据 array
                                    <ul>
                                        <li>subject=科目名称 string</li>
                                        <li>score=分数 int</li>
                                        <li>class_rank=班级平均分 int</li>
                                        <li>grade_rank=年级平均分 int</li>
                                        <li>class_average_rank=班级平均分排名 int</li>
                                        <li>grade_average_rank=年级平均分排名 int</li>
                                    </ul>
                                </li>
                                <li>class_rank=所有科目的班级排名 int</li>
                                <li>grade_rank=所有科目的年级排名 int</li>
                            </ul>
                        </dd>
                    </dl>
                </li>                                      

            </ul>

            <ul><h3>家长版-作业</h3></ul>
            <ul class="apiUl">
                <li><a class="cursor">作业-进行中(parents/homework.underway)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/homework.underway" target="_blank"><?php echo $apiUrl;?>parents/homework.underway</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>homework_id=作业ID int</li>                                                                                            
                                <li>homework_name=作业名称 string</li>                                                                                            
                                <li>homework_description=作业描述 string</li>                                                                                            
                                <li>expiry_time=作业截止时间 时间戳 int</li>                                                                                            
                                <li>is_parents_with=是否需要家长配合 boolean</li>                                                                                            
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-进行中-展开(parents/homework.detail)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/homework.detail" target="_blank"><?php echo $apiUrl;?>parents/homework.detail</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>homework_id＝作业ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>homework_id=作业ID int</li>                                                                                            
                                <li>homework_name=作业名称 string</li>                                                                                            
                                <li>homework_description=作业描述 string</li>                                                                                            
                                <li>expiry_time=作业截止时间 时间戳 int</li>                                                                                            
                                <li>is_parents_with=是否需要家长配合 [配合（parents/homework.with）|“我不清楚，向老师请教”进入C－作业－进行中－交流,情景IM] boolean</li>
								<li>chat_group_id=情景聊天组ID int</li>                                                                                            
                                <li>parents_message=给家长的留言 string</li>                                                                                            
                                <li>uid=老师的uid 用来启动IM时使用 int</li>                                                                                            
                                <li>username=老师的名字 用来启动IM时使用 string</li>                                                                                            
                            </ul>
                        </dd>
                    </dl>
                </li>
				
                <li><a class="cursor">作业-家长同意配合作业(parents/homework.with)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/homework.with" target="_blank"><?php echo $apiUrl;?>parents/homework.with</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>homework_id＝作业ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=执行功能返回空</dd>
                    </dl>
                </li>				

                <li><a class="cursor">作业-已完结(parents/homework.ended_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/homework.ended_list" target="_blank"><?php echo $apiUrl;?>parents/homework.ended_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>pagesize=分页数 int</dd>
                            <dd>page=当前页数 int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>retHeader=查询到的列表总数信息 object，成员：
                                    <ul>
                                        <li>totalput=查询到结果总数 int</li>
                                        <li>totalpg=总页数 int</li>
                                        <li>pagesize=每页返回记录条数 int</li>
                                        <li>page=当前页数 int</li>
                                    </ul>
                                </li>
                                <li>retcode=服务器接收到的请求信息 string</li>
                                <li>retmsg=查询请求结果(1：成功|0：失败) int</li>                       
                                <li>reqdata=服务器返回数据数组 数组，数组每个元素为object，成员：
                                    <ul>
                                        <li>today=今天的作业列表 array
                                            <ul>
                                                <li>homework_id=作业ID int</li>                                                                                            
                                                <li>homework_name=作业名称 string</li>                                                                                            
                                                <li>homework_description=作业描述 string</li>  
                                                <li>result_status=作业完成结果 string</li>  
                                                <li>homework_date=作业的日期 string</li>  
                                            </ul>
                                        </li>
                                        <li>ago=昨天以前的作业列表 array
                                            <ul>
                                                <li>homework_id=作业ID int</li>                                                                                            
                                                <li>homework_name=作业名称 string</li>                                                                                            
                                                <li>homework_description=作业描述 string</li>  
                                                <li>result_status=作业完成结果 string</li>  
                                                <li>homework_date=作业的日期 string</li>  
                                            </ul>
                                        </li>                                           
                                    </ul>
                                </li>                   
                            </ul>
                        </dd>
                    </dl>
                </li>                 


                <li><a class="cursor">作业-统计-控制(parents/homework.statistics_control)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/homework.statistics_control" target="_blank"><?php echo $apiUrl;?>parents/homework.statistics_control</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>pagesize=分页数 int</dd>
                            <dd>page=当前页数 int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>subject=科目的标题 array
                                    <ul>
                                        <li>id=科目ID int</li>
                                        <li>name=科目名称 string</li>
                                    </ul>                                        
                                </li>                                                              
                                <li>category=X轴的显示类型 array
                                    <ul>
                                        <li>id=X轴的显示类型ID int</li>
                                        <li>name=X轴的显示类型名称 string</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-统计-控制查询结果(parents/homework.statistics_control_search)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/homework.statistics_control_search" target="_blank"><?php echo $apiUrl;?>parents/homework.statistics_control_search</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>                            
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>                            
                            <dd>category_id=数据对比分类ID (1,2) intString</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>category=X轴分类名称 string</li>                                
                                <li>provider_name=数据对比名称数组 array
                                    <ul>
                                        <li>provider_id=provider_name</li>
                                    </ul>
                                </li>                                
                                <li>provider_value=数据对比值数组 array
                                    <ul>
                                        <li>provider_id=provider_value</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-统计-分析(parents/homework.statistics_analyze)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/homework.statistics_analyze" target="_blank"><?php echo $apiUrl;?>parents/homework.statistics_analyze</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>                            
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>content=内容 string</li>                                                        
                                <li>analyze=分析 string</li>                                                        
                                <li>complex=综合分析内容 string</li>                                                        
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-统计-交流(parents/homework.statistics_ac)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/homework.statistics_ac" target="_blank"><?php echo $apiUrl;?>parents/homework.statistics_ac</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>                            
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>id=科目ID [当id=0是代表班主任] int</li>                                                              
                                <li>name=科目名称 string</li>
                                <li>uid=科目对应的老师UID 用于发于IM聊天时使用 int</li>
                                <li>username=用户名称老师名称 stirng</li>
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-统计-详情(parents/homework.statistics_detail)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/homework.statistics_detail" target="_blank"><?php echo $apiUrl;?>parents/homework.statistics_detail</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>                            
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>name=作业名称 string</li>                                                              
                                <li>student_name=参加考试的同学名称 string</li>
                                <li>result_table=考试结果的数据 array
                                    <ul>
                                        <li>subject=科目名称 string</li>
                                        <li>score=分数 int</li>
                                        <li>class_average_rank=班级平均分排名 int</li>
                                        <li>grade_average_rank=年级平均分排名 int</li>
                                    </ul>
                                </li>
                            </ul>
                        </dd>
                    </dl>
                </li>                                                                                                                   
            </ul>


            <ul><h3>家长版-课堂表现</h3></ul>
            <ul class="apiUl">
                 <li><a class="cursor">课堂表现-今天(parents/classshow.today)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/classshow.today" target="_blank"><?php echo $apiUrl;?>parents/classshow.today</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>class_show=课堂表现 array
                                    <ul>
                                        <li>subject=科目 stinrg</li>
                                        <li>result=科目课堂表现结果 stinrg</li>
                                    </ul>
                                </li>                                                                                            
                                <li>analyze=分析内容 string</li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">课堂表现-一周(parents/classshow.week)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/classshow.week" target="_blank"><?php echo $apiUrl;?>parents/classshow.week</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)上一自然周</dt>
                            <dd>week=周数 比如1就是下一周，2就是下两周 -1就是上一周，-2就是上两周 int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>class_show=课堂表现 array
                                    <ul>
                                        <li>subject=科目 stinrg</li>
                                        <li>result_score=科目课堂表现得分 int</li>
                                        <li>result_total_score=科目课堂表现满分 int</li>
                                        <li>result_array=科目课堂表现详细 array
                                            <ul>
                                                <li>name=表现名称 string</li>
                                                <li>count=表现次数 int</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>        
                                <li>start_date=开始日期 当前周的开始日期代指周一日期 string</li>
                                <li>end_date=结束日期 当前周的结束日期代指周日日期 string</li> 
                                <li>week_string=周的显示 string</li>                                                                                   
                                <li>analyze=分析内容 string</li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">课堂表现-历史(parents/classshow.history)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/classshow.history" target="_blank"><?php echo $apiUrl;?>parents/classshow.history</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>class_show=课堂表现 array
                                    <ul>
                                        <li>subject=科目 stinrg</li>
                                        <li>result_score=科目课堂表现得分 int</li>
                                        <li>result_total_score=科目课堂表现满分 int</li>
                                        <li>result_array=科目课堂表现详细 array
                                            <ul>
                                                <li>name=表现名称 string</li>
                                                <li>count=表现次数 int</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>                                                                                            
                                <li>analyze=分析内容 string</li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>                                 
            </ul>

            <ul><h3>家长版-交流</h3></ul>
            <ul class="apiUl">
                 <li><a class="cursor">交流-通知(parents/im.notice_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/im.notice_list" target="_blank"><?php echo $apiUrl;?>parents/im.notice_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>pagesize=分页数 int</dd>
                            <dd>page=当前页数 int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>retHeader=查询到的列表总数信息 object，成员：
                                    <ul>
                                        <li>totalput=查询到结果总数 int</li>
                                        <li>totalpg=总页数 int</li>
                                        <li>pagesize=每页返回记录条数 int</li>
                                        <li>page=当前页数 int</li>
                                    </ul>
                                </li>
                                <li>retcode=服务器接收到的请求信息 string</li>
                                <li>retmsg=查询请求结果(1：成功|0：失败) int</li>                       
                                <li>reqdata=服务器返回数据数组 数组，数组每个元素为object，成员：
                                    <ul>
                                        <LI>notice_id=通知ID int</li>
                                        <LI>notice_title=通知标题 string</li>
                                        <LI>notice_content=通知内容 string</li>
                                        <LI>uid=发布通知的用户uid int</li>    
                                        <LI>username=发布通知的用户名称 string</li>   
                                        <li>notice_time=发布通知时间 string</li>
                                        <li>is_important=是否重要通知 boolean</li>
                                        <li>unread_count=未读通知总数 int</li>
                                        <li>notice_type=通知类型（1：通知|2：大考产生的通知|3：作业产生的通知) int</li>
                                        <li>chat_message=用户和老师是否有过交流记录 boolean</li>
                                        <li>规则说明：如果notice_type=1(通知)时，点击item时，直接跳到情景IM<br/>
                                            如果notice_type=2(大考产生的通知)时，判断chat_message，如果chat_message=false时，点击该item时，就会跳转到成绩模块，的单次考试里，并选中所有科目，跳到详情选项卡中。如果chat_message=true时，跳到E2-交流-通知-考试示例.jpg界面中<br/>
                                            如果notice_type=3(作业产生的通知)时，判断chat_message，如果chat_message=false时，点击该item时，就会跳转C2页面。如果chat_message=true时，直接跳到与该老师在该次作业产生的情景IM中。</li>
                                        </li>
										<li>chat_group_id=情景聊天组ID int</li>
                                    </ul>
                                </li>                   
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">交流-通知-考试列表(parents/im.notice_exam_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/im.notice_exam_list" target="_blank"><?php echo $apiUrl;?>parents/im.notice_exam_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>notice_id=通知ID int</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>notice=通知详情 object
                                    <ul>
                                        <LI>notice_id=通知ID int</li>
                                        <LI>notice_title=通知标题 string</li>
                                        <LI>notice_content=通知内容 string</li>
                                        <LI>uid=发布通知的用户uid int</li>    
                                        <LI>username=发布通知的用户名称 string</li>   
                                        <li>notice_time=发布通知时间 string</li>
                                        <li>is_important=是否重要通知 boolean</li>
                                        <li>unread_count=未读通知总数 int</li>
                                        <li>notice_type=通知类型（1：通知|2：大考产生的通知|3：作业产生的通知) int</li>
                                        <li>chat_message=用户和老师是否有过交流记录 boolean</li>
                                    </ul>
                                </li>                                                                                            
                                <li>chat=跟科目老师的聊天列表 array
                                    <ul>
                                        <li>subject_id=科目ID int</li>
                                        <li>subject_name=科目名称 string</li>
                                        <li>chat_group_id=情景聊天组ID int</li>
                                        <li>chat_uid=进行情景IM的用户uid int</li>
                                        <li>chat_username=进行情景IM的用户名称 int</li>
                                        <li>chat_message_content=情景聊天最后一条聊天记录 string</li>
                                        <li>chat_time=最后一条聊天时间 string</li>
                                    </ul>
                                </li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">交流-单独交流(parents/im.notice_alone_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/im.notice_alone_list" target="_blank"><?php echo $apiUrl;?>parents/im.notice_alone_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>pagesize=分页数 int</dd>
                            <dd>page=当前页数 int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>retHeader=查询到的列表总数信息 object，成员：
                                    <ul>
                                        <li>totalput=查询到结果总数 int</li>
                                        <li>totalpg=总页数 int</li>
                                        <li>pagesize=每页返回记录条数 int</li>
                                        <li>page=当前页数 int</li>
                                    </ul>
                                </li>
                                <li>retcode=服务器接收到的请求信息 string</li>
                                <li>retmsg=查询请求结果(1：成功|0：失败) int</li>                       
                                <li>reqdata=服务器返回数据数组 数组，数组每个元素为object，成员：
                                    <ul>
                                        <li>subject_id=科目ID int</li>
                                        <li>subject_name=科目名称 string</li>
                                        <li>chat_group_id=情景聊天组ID int</li>
                                        <li>chat_uid=进行情景IM的用户uid int</li>
                                        <li>chat_username=进行情景IM的用户名称 int</li>
                                        <li>chat_message_content=情景聊天最后一条聊天记录 string</li>
                                        <li>chat_time=最后一条聊天时间 string</li>
                                    </ul>
                                </li>                   
                            </ul>
                        </dd>
                    </dl>
                </li>                                 

            </ul>

            <ul><h3>教师版-主页</h3></ul>			
            <ul class="apiUl">
                <li><a class="cursor">课堂表现(teacher/home.class_show)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/home.class_show" target="_blank"><?php echo $apiUrl;?>teacher/home.class_show</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>chart=左边的柱图数据 (0:代表今天，1:代表昨天，2:代表前天) 有可能只有今天没有昨天和前天 array</li>                                
                                <li>description=大描述 string</li>                                
                                <li>describe=小描述 string</li>                                
                                <li>notice=信息通知数量 int</li>                                
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业(teacher/home.homework)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/home.homework" target="_blank"><?php echo $apiUrl;?>teacher/home.homework</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
								<li>check_out_status=老师查看过这个登记信息的状态 boolean</li>
								<li>check_out=老师查看过登记信息使用第三张图当界面 (当check_out_status=true时，显示第三张图当界面) array
									<ul>
										<li>chart=左边的饼图数据 array</li>                                
										<li>description=大描述 string</li>                                
										<li>describe=小描述 string</li> 
									</ul>
								</li>
                                <li>un_check_out=老师有没有查看的登记信息 (当check_out_status=false时，显示第二张图，点击查看后是跳到 “作业模块-已完结”) string</li>
                                <li>notice=信息通知数量 int</li>                              
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">成绩(teacher/home.score)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/home.score" target="_blank"><?php echo $apiUrl;?>teacher/home.score</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>chart=左边的柱图数据 (0:代表今天，1:代表昨天，2:代表前天) 有可能只有今天没有昨天和前天 array</li>                                
                                <li>description=大描述 string</li>                                
                                <li>describe=小描述 string</li>                                
                                <li>notice=信息通知数量 int</li>                               
                            </ul>
                        </dd>
                    </dl>
                </li>
				
                 <li><a class="cursor">座次设定-取年级班级列表(teacher/home.get_class_map)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/home.get_class_map" target="_blank"><?php echo $apiUrl;?>teacher/home.get_class_map</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>grade_id=年级ID int</li>                                                                                            
                                <li>grade_name=年级名称 string</li>                                                                                            
                                <li>class=科目下的班级数据 arrray
                                    <ul>
                                        <li>class_map_id=班级ID int</li>                                                                                            
                                        <li>class_map_name=年级班级名称 string</li> 
                                        <li>class_name=班级名称 string</li> 
                                    </ul>
                                </li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">座次设定-通过班级class_map_id取班级的所有学生(teacher/home.get_student_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/home.get_student_list" target="_blank"><?php echo $apiUrl;?>teacher/home.get_student_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>class_map_id=班级ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>position=学生座位统计相关 object
                                    <ul>
                                        <li>student_count=学生总数 int</li>
                                        <li>row_count=总行数 int</li>
                                        <li>column_count=每列人数 int</li>
                                    </ul>
                                </li>								
                                <li>res=学生数据 array
                                    <ul>
                                        <li>uid=学生UID int</li>
                                        <li>username=学生姓名 string</li>
                                        <li>column=学生座位列，以左上角为原点的X轴数值 int</li>
                                        <li>row=学生座位行，以左上角为原点的Y轴数值 int</li>                                        										
                                    </ul>
                                </li>                                                         
                            </ul>
                        </dd>
                    </dl>
                </l>
				
				<li><a class="cursor">公共学生选择器-搜索学生名字(teacher/home.search_student_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/home.search_student_list" target="_blank"><?php echo $apiUrl;?>teacher/home.search_student_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>class_map_id=班级ID int</dd>
							<dd>query=学生姓名关键字 string</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>uid=学生UID int</li>                                                                                            
                                <li>username=学生用户名 string</li>                                                                                                                     
								<li>realname=学生真实姓名 string</li>                                                                     
                                <li>class_map_id=学生所在的班级ID int</li>                                                                                            
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">座次设定-修改学生座次(teacher/home.modify_student_seat)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/home.modify_student_seat" target="_blank"><?php echo $apiUrl;?>teacher/home.modify_student_seat</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>class_map_id=班级ID int</dd>
                            <dd>student_json=学生在班级的新座次数据 demo:[{"uid":1,"column":1,"row":2},{"uid":2,"column":2,"row":3}] string</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=成功返回空</dd>
                    </dl>
                </li>				
			</ul>	
			
            <ul><h3>教师版-成绩</h3></ul>
            <ul class="apiUl">
                <li><a class="cursor">成绩-成绩趋势-控制(teacher/score.trend_control)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/score.trend_control" target="_blank"><?php echo $apiUrl;?>teacher/score.trend_control</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>subject=科目的标题 array
                                    <ul>
                                        <li>id=科目ID int</li>
                                        <li>name=科目名称 string</li>
                                    </ul>                                        
                                </li>                                
                                <li>provider=数据对比数组 array
                                    <ul>
                                        <li>id=对比数据的ID int</li>
                                        <li>name=对比数据的名称 string</li>
                                    </ul>
                                </li>                                
                                <li>exam_type=考试分类 array
                                    <ul>
                                        <li>id=考试分类ID int</li>
                                        <li>name=考试分类名称 string</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li>  

                <li><a class="cursor">成绩-成绩趋势-控制查询结果(teacher/score.trend_control_search)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/score.trend_control_search" target="_blank"><?php echo $apiUrl;?>teacher/score.trend_control_search</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>
                            <dd>provider_id=数据对比ID (1,2,4) intString</dd>
                        <dt>可选参数：(POST)</dt>
                            <dd>exam_type_id=考试类型ID (1,2) intString</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>exam_date=考试时间 chart的x轴 date</li>                                
                                <li>provider_name=数据对比名称数组 array
                                    <ul>
                                        <li>provider_id=provider_name</li>
                                    </ul>
                                </li>                                
                                <li>provider_value=数据对比值数组 array
                                    <ul>
                                        <li>provider_id=provider_value</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li> 

                <li><a class="cursor">成绩-成绩趋势-分析(teacher/score.trend_analyze)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/score.trend_analyze" target="_blank"><?php echo $apiUrl;?>teacher/score.trend_analyze</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>
                            <dd>provider_id=数据对比ID (1,2,4) intString</dd>                        
                            <dd>exam_type_id=考试类型ID (1,2) intString</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>content=内容 string</li>                                                        
                                <li>analyze=分析 string</li>                                                        
                                <li>complex=综合分析内容 string</li>                                                        
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">成绩-成绩趋势-详情(teacher/score.trend_detail)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/score.trend_detail" target="_blank"><?php echo $apiUrl;?>teacher/score.trend_detail</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>
							<dd>provider_id=数据对比ID (1,2,4) intString</dd>                        
                            <dd>exam_type_id=考试类型ID (1,2) intString</dd>							
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>exam_name=考试名称 string</li>                                                                                              
                                <li>exam_result_table=考试结果的数据 array
                                    <ul>
                                        <li>subject=科目名称 string</li>
                                        <li>score=分数 int</li>                                        
                                        <li>grade_rank=年级排名 int</li>
                                        <li>rank_status=排名较上次考试的升降 (up|down) string</li>                                        
                                        <li>grade_average_rank=年级平均分排名 int</li>
                                        <li>exam_date=科目考试时间 有可能为空，为空时显示上面的大考时间 string</li>
                                    </ul>
                                </li>                                
                            </ul>
                        </dd>
                    </dl>
                </li>     

                <li><a class="cursor">成绩-单次考试-考试(teacher/score.single_exam)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/score.single_exam" target="_blank"><?php echo $apiUrl;?>teacher/score.single_exam</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>subject_id=科目ID (当ID＝0时代表此栏是大考) int</li>                                                              
                                <li>subject_name=科目ID对应的科目名称 string</li>
                                <li>exam_array=考试数据 array
                                    <ul>
                                        <li>exam_id=考试ID int</li>
                                        <li>exam_name=考试名称 string</li>
                                    </ul>
                                </li>
                            </ul>
                        </dd>
                    </dl>
                </li> 

                <li><a class="cursor">成绩-单次考试-控制(teacher/score.single_control)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/score.single_control" target="_blank"><?php echo $apiUrl;?>teacher/score.single_control</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>subject=科目的标题 如果是小考的话subject节点不返回 array
                                    <ul>
                                        <li>id=科目ID int</li>
                                        <li>name=科目名称 string</li>
                                    </ul>                                        
                                </li>                                
                                <li>provider=数据对比数组 array
                                    <ul>
                                        <li>id=对比数据的ID int</li>
                                        <li>name=对比数据的名称 string</li>
                                    </ul>
                                </li>                                                                                           
                            </ul>
                        </dd>
                    </dl>
                </li>


                <li><a class="cursor">成绩-单次考试-控制查询结果(teacher/score.single_control_search)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/score.single_control_search" target="_blank"><?php echo $apiUrl;?>teacher/score.single_control_search</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>                            
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>
                            <dd>provider_id=数据对比ID (1,2,4) intString</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>name=科目名称 雷达图的角或折线图的x轴 string</li>                                
                                <li>provider_name=数据对比名称数组 array
                                    <ul>
                                        <li>provider_id=provider_name</li>
                                    </ul>
                                </li>                                
                                <li>provider_value=数据对比值数组 array
                                    <ul>
                                        <li>provider_id=provider_value</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li>


                <li><a class="cursor">成绩-单次考试-分析(teacher/score.single_analyze)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/score.single_analyze" target="_blank"><?php echo $apiUrl;?>teacher/score.single_analyze</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>
                        <dt>必选参数：(POST)</dt>							
							<dd>subject_id＝科目ID (1,2,4) intString</dd>                            
                            <dd>provider_id=数据对比ID (1,2,4) intString</dd>							
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>content=内容 string</li>                                                        
                                <li>analyze=分析 string</li>                                                        
                                <li>complex=综合分析内容 string</li>                                                        
                            </ul>
                        </dd>
                    </dl>
                </li>                                 

                                                   
                <li><a class="cursor">成绩-单次考试-详情(teacher/score.single_detail)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/score.single_detail" target="_blank"><?php echo $apiUrl;?>teacher/score.single_detail</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>exam_id=考试ID int</dd>
                        <dt>必选参数：(POST)</dt>							
							<dd>subject_id＝科目ID (1,2,4) intString</dd>                            
                            <dd>provider_id=数据对比ID (1,2,4) intString</dd>								
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>exam_name=考试名称 string</li>                                                              
                                <li>exam_result_table=考试结果的数据 array
                                    <ul>
                                        <li>subject=科目名称 string</li>
                                        <li>score=分数 int</li>
                                        <li>grade_rank=年级排名 int</li>
                                        <li>grade_average_rank=年级平均分排名 int</li>
                                    </ul>
                                </li>
                            </ul>
                        </dd>
                    </dl>
                </li>                                      

                <li><a class="cursor">成绩-成绩趋势和单次考试中用到的交流-通用api-取年级班级列表－对应效果图是[设计图\app\app教师版 v0.97\E 交流\E1-交流.jpg]最后一张图(teacher/im.get_class_map)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/im.get_class_map" target="_blank"><?php echo $apiUrl;?>teacher/im.get_class_map</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>grade_id=年级ID int</li>                                                                                            
                                <li>grade_name=年级名称 string</li>                                                                                            
                                <li>class=科目下的班级数据 arrray
                                    <ul>
                                        <li>class_map_id=班级ID int</li>                                                                                            
                                        <li>class_map_name=年级班级名称 string</li> 
                                        <li>class_name=班级名称 string</li> 
										<li>unread_num=班级中的未读消息数</li>
										<li>member_type=班级对应的用户角色 array
											<ul>
												<li>type_id=角色分类ID int</li>
												<li>type_name=角色分类名称 string</li>
												<li>unread_num=角色分类的未读消息数 int</li>
											</ul>
										</li>
                                    </ul>
                                </li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">座次设定-通过班级class_map_id取班级的所有学生(teacher/im.get_student_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/im.get_student_list" target="_blank"><?php echo $apiUrl;?>teacher/im.get_student_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>type_id=班级的用户角色ID int</dd>
                            <dd>class_map_id=班级ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>uid=角色UID int</li>                                                                                            
                                <li>username=角色名称 string</li>                                                                                                                     
                                <li>column=角色的列 int</li>                                                            
                                <li>row=角色的行 int</li>                                                            
                                <li>unread=未读的消息数 int</li>                                                            
                            </ul>
                        </dd>
                    </dl>
                </li>
				
                <li><a class="cursor">成绩-学生视角</a>    
                    <dl>
                        <dt>规则说明</dt>                                            
                            <dd>学生视角－考试－成绩趋势（按钮）控制页面中（后面的控制，分析，详情三个选项卡会载入这个学生在<b>成绩趋势</b>下的分别所对应的内容）</dd>
							<dd>学生视角－考试－点击考试里的的任何一次考试（后面的控制，分析，详情三个选项卡会载入这个学生在<b>单次考试</b>下的分别所对应的内容）</dd>
                            <dd>重新选择学生－调用“座次设定-取年级班级列表(teacher/home.get_class_map)”来选班级的学生</dd>                       
							<dd>学生视角里的对应api全都是调用家长版对应的api,唯一区分是在api的url中加上<b>other_uid=学生uid</b></dd>
                    </dl>
                </li>				
            </ul>
			
            <ul><h3>教师版-作业</h3></ul>
            <ul class="apiUl">
                <li><a class="cursor">作业-进行中(teacher/homework.underway)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.underway" target="_blank"><?php echo $apiUrl;?>teacher/homework.underway</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
								<li>subject_id=科目ID int</li>
								<li>subject_name=科目名称 string</li>								
								<li>homework=科目下的作业列表 array
									<ul>
										<li>homework_id=作业ID int</li>                                                                                            
										<li>homework_name=作业名称 string</li>                                                                                            
										<li>homework_description=作业描述 string</li>                                                                                            
										<li>expiry_time=作业截止时间 时间戳 int</li>                                                                                            
										<li>is_parents_with=是否需要家长配合 boolean</li>                                                                                            
									</ul>
								</li>								
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-进行中-展开(teacher/homework.detail)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.detail" target="_blank"><?php echo $apiUrl;?>teacher/homework.detail</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>homework_id＝作业ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>homework_id=作业ID int</li>                                                                                            
                                <li>homework_name=作业名称 string</li>                                                                                            
                                <li>homework_description=作业描述 string</li>                                                                                            
                                <li>expiry_time=作业截止时间 时间戳 int</li>                                                                                            
                                <li>is_parents_with=是否需要家长配合 [配合（parents/homework.with）|“我不清楚，向老师请教”进入C－作业－进行中－交流,情景IM] boolean</li>
								<li>chat_group_id=情景聊天组ID int</li>                                                                                            
                                <li>parents_message=给家长的留言 string</li>                                                                                            
                                <li>uid=老师的uid 用来启动IM时使用 int</li>                                                                                            
                                <li>username=老师的名字 用来启动IM时使用 string</li>
								<li>subject_id=科目ID int</li>
								<li>subject_name=科目名称 string</li>									
								<li>class_string=班级信息 string</li>									
								<li>parents_with_result_string=家长配合信息 string</li>									
								<li>unread_num=未读消息数 (如果值为０，就显示“尚未有家长发送消息”　点击跳转到E1交流页面，最后的学生选择器上[teacher/im.get_class_map]，记得要传作业ID[notice_type,linked_id]) int</li>									
								<li>class_map=作业下的班级完成情况数组 array
									<ul>
										<li>class_map_id=班级ID int</li>
										<li>class_map_name=班级名称 string</li>
										<li>result=班级作业完成情况 string</li>
									</ul>
								</li>									
                            </ul>
                        </dd>
                    </dl>
                </li>
				
                <li><a class="cursor">作业-进行中-查看班级学生的作业完成情况/登记作业完成情况(teacher/homework.get_result)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.get_result" target="_blank"><?php echo $apiUrl;?>teacher/homework.get_result</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>homework_id=作业ID int</dd>
                            <dd>class_map_id=班级ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>class_name=班级名称 string</li>                                                                                            
                                <li>class_homework_info=班级作业完成情况 string</li>                                                                                            
                                <li>result_status=作业完成结果数组 用于给登记时使用 array
									<ul>
										<li>id=完成状态ID</li>
										<li>name=完成状态</li>
									</ul>
								</li>
                                <li>position=学生座位统计相关 object
                                    <ul>
                                        <li>student_count=学生总数 int</li>
                                        <li>row_count=总行数 int</li>
                                        <li>column_count=每列人数 int</li>
                                    </ul>
                                </li>								
                                <li>classshow_list=课堂表现学生数据 array
                                    <ul>
                                        <li>uid=学生UID int</li>
                                        <li>username=学生姓名 string</li>
                                        <li>column=学生座位列，以左上角为原点的X轴数值 int</li>
                                        <li>row=学生座位行，以左上角为原点的Y轴数值 int</li>
                                        <li>result_status=学生对作业的完成状态（0：未完成｜1：已完成｜2：优秀｜3：最佳） int</li>
										<li>register_status=学生是否登记过 boolean</li>
                                    </ul>
                                </li> 								                                									
                            </ul>
                        </dd>
                    </dl>
                </li>				
                
                <li><a class="cursor">作业-进行中-作业登记(teacher/homework.set_result)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.set_result" target="_blank"><?php echo $apiUrl;?>teacher/homework.set_result</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>class_map_id=班级ID int</dd>
                            <dd>homework_id=作业ID int</dd>
                            <dd>student_json=学生的作业完成情况登记数据 demo:[{"uid":1,"result_status":1},{"uid":2,"result_status":2}] string</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=成功返回空</dd>
                    </dl>
                </li>				
						
                <li><a class="cursor">作业-已完结(teacher/homework.ended_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.ended_list" target="_blank"><?php echo $apiUrl;?>teacher/homework.ended_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>无</dd>                            
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
						<ul>
							<li>newest=最新登记列表 点击跳转进行中展示页面 array
								<ul>
									<li>homework_id=作业ID int</li>                                                                                            
									<li>homework_name=作业名称 string</li>
									<li>result_info=作业完成结果 string</li>  
									<li>result_percent=作业完成百分率 string</li>  
								</ul>
							</li>
							<li>subject=已经完结的作业科目 点击调用（teacher/homework.get_ended_list_by_subject） array
								<ul>
									<li>subject_id=科目ID int</li>                                                                                            
									<li>subject_name=科目名称 string</li>                                                                                            
								</ul>
							</li>                                           
						</ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-根据已完结的科目ID取作业列表(teacher/homework.get_ended_list_by_subject)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.get_ended_list_by_subject" target="_blank"><?php echo $apiUrl;?>teacher/homework.get_ended_list_by_subject</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>subject_id=(teacher/homework.ended_list)接口中返回的科目ID int</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
						<ul>
							<li>homework_id=作业ID int</li>                                                                                            
							<li>homework_name=作业名称 string</li>
							<li>result_info=作业完成结果 string</li>  
							<li>result_percent=作业完成百分率 string</li>  
						</ul>
                        </dd>
                    </dl>
                </li> 				

                <li><a class="cursor">作业-统计-控制(teacher/homework.statistics_control)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.statistics_control" target="_blank"><?php echo $apiUrl;?>teacher/homework.statistics_control</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>subject=科目的标题 array
                                    <ul>
                                        <li>id=科目ID int</li>
                                        <li>name=科目名称 string</li>
                                    </ul>                                        
                                </li>                                                              
                                <li>provider=折线对比的项目 array
                                    <ul>
                                        <li>id=折线对比的项目ID int</li>
                                        <li>name=折线对比的项目名称 string</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-统计-控制查询结果(teacher/homework.statistics_control_search)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.statistics_control_search" target="_blank"><?php echo $apiUrl;?>teacher/homework.statistics_control_search</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>                            
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_id＝科目ID (1,2,4) intString</dd>                            
                            <dd>provider_id=要进行对比的班级分类ID (1,2) intString</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>category=X轴分类名称 string</li>                                
                                <li>provider_name=数据对比名称数组 array
                                    <ul>
                                        <li>provider_id=provider_name</li>
                                    </ul>
                                </li>                                
                                <li>provider_value=数据对比值数组 array
                                    <ul>
                                        <li>provider_id=provider_value</li>
                                    </ul>
                                </li>                                                              
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-统计-分析(teacher/homework.statistics_analyze)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.statistics_analyze" target="_blank"><?php echo $apiUrl;?>teacher/homework.statistics_analyze</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
							<dd>subject_id＝科目ID (1,2,4) intString</dd>                            
                            <dd>provider_id=要进行对比的班级分类ID (1,2) intString</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>content=内容 string</li>                                                        
                                <li>analyze=分析 string</li>                                                        
                                <li>complex=综合分析内容 string</li>                                                        
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-统计-详情(teacher/homework.statistics_detail)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.statistics_detail" target="_blank"><?php echo $apiUrl;?>parents/teacher.statistics_detail</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
							<dd>subject_id＝科目ID (1,2,4) intString</dd>                            
                            <dd>provider_id=要进行对比的班级分类ID (1,2) intString</dd>                      
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 object，成员：
                            <ul>
                                <li>name=作业名称 string</li>                                                                                              
                                <li>result_table=考试结果的数据 array
                                    <ul>
                                        <li>subject=科目名称 string</li>
                                        <li>score=分数 int</li>
                                        <li>class_average_rank=班级平均分排名 int</li>
                                        <li>grade_average_rank=年级平均分排名 int</li>
                                    </ul>
                                </li>
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">作业-学生视角</a>    
                    <dl>
                        <dt>规则说明</dt>                                                                        
                            <dd>重新选择学生－调用“座次设定-取年级班级列表(teacher/home.get_class_map)”来选班级的学生</dd>                       
							<dd>重新选择学生－调用“座次设定-根据名称搜索学生(teacher/home.search_student_list)”来选班级的学生</dd>                       
							<dd>学生视角里的对应api全都是调用家长版对应的api,唯一区分是在api的url中加上<b>other_uid=学生uid</b></dd>
                    </dl>
                </li>					

                <li><a class="cursor">作业-作业发布时取要发布的年级班级，科目列表(teacher/homework.get_class_map)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.get_class_map" target="_blank"><?php echo $apiUrl;?>teacher/homework.get_class_map</a></dt>
                        <dt>规则说明：科目是单选的，班级是多选的class_map_id=1,2,4</dt>
						<dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
								<li>grade=年级班级 array
									<ul>
										<li>grade_id=年级ID int</li>                                                                                            
										<li>grade_name=年级名称 string</li>                                                                                            
										<li>class=科目下的班级数据 arrray
											<ul>
												<li>class_map_id=班级ID int</li>                                                                                            
												<li>class_map_name=年级班级名称 string</li> 
												<li>class_name=班级名称 string</li> 
											</ul>
										</li>									
									</ul>
								</li>
								<li>subject=科目 array
									<ul>
										<li>subject_school_id=科目ID int</li>                                                                                            
										<li>subject_name=科目名称 string</li> 									
									</ul>
								</li>								
                            </ul>
                        </dd>
                    </dl>
                </li>
				
                <li><a class="cursor">作业-作业发布保存(teacher/homework.save)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/homework.save" target="_blank"><?php echo $apiUrl;?>teacher/homework.save</a></dt>                        
						<dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>subject_school_id=作业要发布的科目ID int</dd>
                            <dd>class_map_id=作业要发布的班级 1,2,3 intString</dd>
                            <dd>homework_name=作业名称 string</dd>
                            <dd>homework_description=作业描述 string</dd>
                            <dd>expiry_time=作业截止时间 int</dd>
                            <dd>is_parents_with=是否需要家长配合 (0：不需要｜1：需要) int</dd>
                            <dd>parents_message=给家长的留言 string</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
								<li>homework_id=作业ID int</li>							
                            </ul>
                        </dd>
                    </dl>
                </li>				
			</ul>
			
            <ul><h3>教师版-课堂表现</h3></ul>
            <ul class="apiUl">
                 <li><a class="cursor">课堂表现-评价(teacher/classshow.comment_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/classshow.comment_list" target="_blank"><?php echo $apiUrl;?>teacher/classshow.comment_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>subject_id=科目ID int</li>                                                                                            
                                <li>subject_name=科目名称 string</li>                                                                                            
                                <li>class=科目下的班级数据 arrray
                                    <ul>
                                        <li>class_map_id=班级ID int</li>                                                                                            
                                        <li>class_map_name=班级名称 string</li> 
                                        <li>classshow_status=班级课堂表现点评状态 boolean</li> 
                                    </ul>
                                </li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">课堂表现-取要评价的科目班级学生数据(teacher/classshow.get_classshow_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/classshow.get_classshow_list" target="_blank"><?php echo $apiUrl;?>teacher/classshow.get_classshow_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>class_map_id=班级ID int</dd>
                            <dd>subject_id=科目ID int</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>position=学生座位统计相关 object
                                    <ul>
                                        <li>student_count=学生总数 int</li>
                                        <li>row_count=总行数 int</li>
                                        <li>column_count=每列人数 int</li>
                                    </ul>
                                </li> 							
                                <li>classshow_list=课堂表现学生数据 array
                                    <ul>
                                        <li>uid=学生UID int</li>
                                        <li>username=学生姓名 string</li>
                                        <li>column=学生座位列，以左上角为原点的X轴数值 int</li>
                                        <li>row=学生座位行，以左上角为原点的Y轴数值 int</li>
                                        <li>result=学生在班级科目中的课堂表现结果 int</li>
                                    </ul>
                                </li>                                                                                            
                                <li>result=课堂表现的结果 array
                                    <ul>
                                        <li>id＝课堂表现ID int</li>
                                        <li>name＝课堂表现 string</li>
                                    </ul>
                                </li>                                                                                                                                                                                                                                                                                
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">课堂表现-提交学生的课堂表现数据(teacher/classshow.classshow_register)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/classshow.classshow_register" target="_blank"><?php echo $apiUrl;?>teacher/classshow.classshow_register</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>class_map_id=班级ID int</dd>
                            <dd>subject_id=科目ID int</dd>
                            <dd>classshow_result_json=学生在班级中的科目课堂表现情况 demo:[{"uid":1,"result":0},{"uid":2,"result":1}] string</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=空 string</dd>
                    </dl>
                </li>                                

                 <li><a class="cursor">课堂表现-今天(teacher/classshow.today)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/classshow.today" target="_blank"><?php echo $apiUrl;?>teacher/classshow.today</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>class_map_id=班级ID int</li>                                                                                            
                                <li>class_map_name=班级名称 string</li>                                                                                            
                                <li>subject=班级下的科目 arrray
                                    <ul>
                                        <li>subject_name=科目 stinrg</li>
                                        <li>result_score=科目课堂表现得分 int</li>
                                        <li>result_total_score=科目课堂表现满分 int</li>
                                        <li>result_array=科目课堂表现详细 array
                                            <ul>
                                                <li>name=表现名称 string</li>
                                                <li>count=表现次数 int</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">课堂表现-一周(teacher/classshow.week)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/classshow.week" target="_blank"><?php echo $apiUrl;?>teacher/classshow.week</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)上一自然周</dt>
                            <dd>week=周数 比如1就是下一周，2就是下两周 -1就是上一周，-2就是上两周 int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
								<li>res=班级数据 array
									<ul>
										<li>class_map_id=班级ID int</li>                                                                                            
										<li>class_map_name=班级名称 string</li>                                                                                            
										<li>subject=班级下的科目 array
											<ul>
												<li>subject_name=科目 stinrg</li>
												<li>result_score=科目课堂表现得分 int</li>
												<li>result_total_score=科目课堂表现满分 int</li>
												<li>result_array=科目课堂表现详细 array
													<ul>
														<li>name=表现名称 string</li>
														<li>count=表现次数 int</li>
													</ul>
												</li>
											</ul>
										</li>       
									</ul>
								</li>
                                <li>start_date=开始日期 当前周的开始日期代指周一日期 string</li>
                                <li>end_date=结束日期 当前周的结束日期代指周日日期 string</li> 
                                <li>week_string=周的显示 string</li>                                                                                                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">课堂表现-历史(teacher/classshow.history)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>parents/teacher.history" target="_blank"><?php echo $apiUrl;?>teacher/classshow.history</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>class_map_id=班级ID int</li>                                                                                            
                                <li>class_map_name=班级名称 string</li>                                                                                            
                                <li>subject=班级下的科目 arrray
                                    <ul>
                                        <li>subject_name=科目 stinrg</li>
                                        <li>result_score=科目课堂表现得分 int</li>
                                        <li>result_total_score=科目课堂表现满分 int</li>
                                        <li>result_array=科目课堂表现详细 array
                                            <ul>
                                                <li>name=表现名称 string</li>
                                                <li>count=表现次数 int</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>                                                                                                                                                                                    
                            </ul>
                        </dd>
                    </dl>
                </li>                                 
            </ul>                                                                  
	
            <ul><h3>教师版-交流</h3></ul>
            <ul class="apiUl">
                 <li><a class="cursor">交流-通知(teacher/im.notice_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/im.notice_list" target="_blank"><?php echo $apiUrl;?>teacher/im.notice_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>pagesize=分页数 int</dd>
                            <dd>page=当前页数 int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>retHeader=查询到的列表总数信息 object，成员：
                                    <ul>
                                        <li>totalput=查询到结果总数 int</li>
                                        <li>totalpg=总页数 int</li>
                                        <li>pagesize=每页返回记录条数 int</li>
                                        <li>page=当前页数 int</li>
                                    </ul>
                                </li>
                                <li>retcode=服务器接收到的请求信息 string</li>
                                <li>retmsg=查询请求结果(1：成功|0：失败) int</li>                       
                                <li>reqdata=服务器返回数据数组 数组，数组每个元素为object，成员：
                                    <ul>
                                        <LI>notice_id=通知ID int</li>
                                        <LI>notice_title=通知标题 string</li>
                                        <LI>notice_content=通知内容 string</li>
                                        <LI>uid=发布通知的用户uid int</li>    
                                        <LI>username=发布通知的用户名称 string</li>   
                                        <li>notice_time=发布通知时间 string</li>
                                        <li>is_important=是否重要通知 boolean</li>
                                        <li>unread_count=未读通知总数 int</li>
                                        <li>notice_type=通知类型（1：通知|2：大考产生的通知|3：作业产生的通知) int</li>                                        
                                        <li>规则说明：如果notice_type=1(通知)，跳到与该老师在该次通知产生的情景IM中。通过班级选择器(teacher/im.get_class_map)<br/>
                                            如果notice_type=2(大考产生的通知)时，第到第二张中(teacher/im.notice_exam_list)<br/>
                                            如果notice_type=3(作业产生的通知)时，跳到与该老师在该次作业产生的情景IM中。通过班级选择器(teacher/im.get_class_map)</li>
                                        </li>
										<li>linked_id=通知类型关联ID（当notice_type=2时，关联考试科目ID[exam_subject_id]。当notice_type=3时，关联作业ID）int</li>
                                    </ul>
                                </li>                   
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">交流-通知-考试列表(teacher/im.notice_exam_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/im.notice_exam_list" target="_blank"><?php echo $apiUrl;?>teacher/im.notice_exam_list</a></dt>
						<dt>规则说明：进入考试的科目情景聊天的学生列表选择时，(im.get_class_map和im.get_student_list)需要传入notice_type和linked_id</dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>notice_id=通知ID int</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>notice=通知详情 object
                                    <ul>
                                        <LI>notice_id=通知ID int</li>
                                        <LI>notice_title=通知标题 string</li>
                                        <LI>notice_content=通知内容 string</li>
                                        <LI>uid=发布通知的用户uid int</li>    
                                        <LI>username=发布通知的用户名称 string</li>   
                                        <li>notice_time=发布通知时间 string</li>
                                        <li>is_important=是否重要通知 boolean</li>
                                        <li>unread_count=未读通知总数 int</li>
                                        <li>notice_type=通知类型（1：通知|2：大考产生的通知|3：作业产生的通知) int</li>                                        
										<li>linked_id=通知类型关联ID（当notice_type=2时，关联考试ID[exam_id],下面的chat数组中的subject_id是本次考试exam_id下的所有考试科目ID[exam_subject_id]。当notice_type=3时，关联作业ID）int</li>
                                    </ul>
                                </li>                                                                                            
                                <li>chat=科目情景下的聊天列表 array
                                    <ul>
                                        <li>subject_id=科目ID int</li>
                                        <li>subject_name=科目名称 string</li>                                                                                
                                        <li>chat_time=最后一条聊天时间 string</li>
										<li>unread_count=该科目情景聊天下的未读聊天数 int</li>
                                    </ul>
                                </li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                 <li><a class="cursor">交流-单独交流(teacher/im.notice_alone_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/im.notice_alone_list" target="_blank"><?php echo $apiUrl;?>teacher/im.notice_alone_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>
                            <dd>pagesize=分页数 int</dd>
                            <dd>page=当前页数 int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>retHeader=查询到的列表总数信息 object，成员：
                                    <ul>
                                        <li>totalput=查询到结果总数 int</li>
                                        <li>totalpg=总页数 int</li>
                                        <li>pagesize=每页返回记录条数 int</li>
                                        <li>page=当前页数 int</li>
                                    </ul>
                                </li>
                                <li>retcode=服务器接收到的请求信息 string</li>
                                <li>retmsg=查询请求结果(1：成功|0：失败) int</li>                       
                                <li>reqdata=服务器返回数据数组 数组，数组每个元素为object，成员：
                                    <ul>
                                        <li>subject_id=科目ID int</li>
                                        <li>subject_name=科目名称 string</li>
                                        <li>chat_group_id=情景聊天组ID int</li>
                                        <li>chat_uid=进行情景IM的用户uid int</li>
                                        <li>chat_username=进行情景IM的用户名称 int</li>
                                        <li>chat_message_content=情景聊天最后一条聊天记录 string</li>
                                        <li>chat_time=最后一条聊天时间 string</li>
                                    </ul>
                                </li>                   
                            </ul>
                        </dd>
                    </dl>
                </li>                                 

                <li><a class="cursor">交流-发布新通知(teacher/im.save)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/im.save" target="_blank"><?php echo $apiUrl;?>teacher/im.save</a></dt>                        
						<dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(POST)</dt>
                            <dd>class_map_id=作业要发布的班级 1,2,3 intString</dd>
                            <dd>notice_title=通知标题 string</dd>
                            <dd>notice_content=通知内容 string</dd>
                            <dd>is_important=是否需要家长配合 (0：不需要｜1：需要) int</dd>
                            <dd>type_id=成员ID 比如：1,2 [发布通知是一年级内的班级多选，及成员多选。比如：高一4个班，学生和家长] string</dd>
                        <dt>可选参数</dt>
                            <dd>无</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
								<li>notice_id=通知ID int</li>							
                            </ul>
                        </dd>
                    </dl>
                </li>
				
                <li><a class="cursor">交流-交流模块中的情景交流的学生/家长的筛选(teacher/im.get_class_map)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/im.get_class_map" target="_blank"><?php echo $apiUrl;?>teacher/im.get_class_map</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>无</dd>
                        <dt>可选参数：(GET)</dt>                            
                            <dd>notice_type=通知类型（1：通知|2：大考产生的通知|3：作业产生的通知） int</dd>
                            <dd>linked_id=通知类型关联ID（当notice_type=2时，关联考试科目ID[exam_subject_id]。当notice_type=3时，关联作业ID）int</dd>
							<dd>说明：当$notice_type和$linked_id都不为空时，返回字段中有unread_num和member_type。都为空时返回notice_member_type。</dd>
							<dd>is_notice=是否是发布通知 (0：不是发布通知时调用｜1：发布通知时调用) int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>grade_id=年级ID int</li>                                                                                            
                                <li>grade_name=年级名称 string</li>                                                                                            
                                <li>class=科目下的班级数据 arrray
                                    <ul>
                                        <li>class_map_id=班级ID int</li>                                                                                            
                                        <li>class_map_name=年级班级名称 string</li> 
                                        <li>class_name=班级名称 string</li> 
										<li>unread_num=班级中的未读消息数(<b>当$notice_type和$linked_id都不为空时</b>)</li>
										<li>member_type=班级对应的用户角色 (<b>当$notice_type和$linked_id都不为空时</b>) array
											<ul>
												<li>type_id=角色分类ID int</li>
												<li>type_name=角色分类名称 string</li>
												<li>unread_num=角色分类的未读消息数 int</li>
											</ul>
										</li>
										<li>notice_member_type=发布通知时用到的班级对应的用户角色 (<b>当$notice_type和$linked_id都为空时</b>) array
											<ul>
												<li>type_id=角色分类ID int</li>
												<li>type_name=角色分类名称 string</li>												
											</ul>
										</li>										
                                    </ul>
                                </li>                                                                                                                                                                                     
                            </ul>
                        </dd>
                    </dl>
                </li>

                <li><a class="cursor">交流-通过班级class_map_id取班级的所有学生(teacher/im.get_student_list)</a>    
                    <dl>
                        <dt>接口地址：<a href="<?php echo $apiUrl;?>teacher/im.get_student_list" target="_blank"><?php echo $apiUrl;?>teacher/im.get_student_list</a></dt>
                        <dt>必选参数：(GET)</dt>                         
                            <dd>uid=用户UID int</dd>
                            <dd>token=账户系统中返回的token string</dd>
                        <dt>必选参数：(GET)</dt>
                            <dd>type_id=班级的用户角色ID int</dd>
                            <dd>class_map_id=班级ID int</dd>                        
                        <dt>可选参数：(GET)</dt>                            
                            <dd>notice_type=通知类型（1：通知|2：大考产生的通知|3：作业产生的通知） int</dd>
                            <dd>linked_id=通知类型关联ID（当notice_type=2时，关联考试科目ID[exam_subject_id]。当notice_type=3时，关联作业ID）int</dd>
							<dd>is_notice=是否是发布通知 (0：不是发布通知时调用｜1：发布通知时调用) int</dd>
                        <dt>返回参数：</dt>
                        <dd>status=执行结果 0表示正常|非0表示异常 int</dd>
                        <dd>success=执行结果 true表示正常|false表示异常 boolean</dd>        
                        <dd>data=返回数据 array，成员：
                            <ul>
                                <li>uid=角色UID int</li>                                                                                            
                                <li>username=角色名称 string</li>                                                                                                                     
                                <li>column=角色的列 int</li>                                                            
                                <li>row=角色的行 int</li>                                                            
                                <li>unread=未读的消息数  (<b>当$notice_type和$linked_id都为空时</b>)  int</li>                                                          
								<li>chat_group_id=情景聊天组ID (<b>当$notice_type和$linked_id都为空时</b>) int</li>
							</ul>
                        </dd>
                    </dl>
                </li>
				
            </ul>	
        </div>
    </body>
</html>
<script type="text/javascript" src="http://libs.baidu.com/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){		
	$(".apiUl li a").click(function(){
		var dlDom = $(this);		
		dlDom.siblings('dl').slideToggle("fast");
	});
});
</script>