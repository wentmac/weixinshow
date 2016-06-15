<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: ApiXmlException.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
class ApiXmlException extends Exception
{

    /**
     * 异常类型
     * @var string
     * @access private
     */
    private $type;

    /**
     * 构造器
     *
     * @param string $message
     * @param int $code
     * @access public
     */
    public function __construct($message = 'Unknown Error', $code = 0)
    {
        parent::__construct($message, $code);
        $this->type = get_class($this);
        $debug = $this->getCode(); //是否是debug
        if ($this->type == 'ApiXmlException') {
            try {
                //放出接口出错时的返回值
                $return = array(
                    'status' => -1,
                    'success' => false,
                    'message' => $this->getMessage(),
                );
                header('Content-Type: text/xml; charset=utf-8');
                $dom = new DomDocument('1.0', 'UTF-8');
                $dom->formatOutput = true;

                // 创建一个根元素
                $ret = $dom->createElement("ret");
                $dom->appendChild($ret);
                
                
                // 创建一个根元素
                $errorCode = $dom->createElement("errorCode");
                $ret->appendChild($errorCode);

                // 创建$item的子文本节点
                $text = $dom->createTextNode($code);
                $errorCode->appendChild($text);
                
                
                
                // 创建一个根元素
                $errorCode = $dom->createElement("errorInfo");
                $ret->appendChild($errorCode);

                // 创建$item的子文本节点
                $text = $dom->createTextNode($this->getMessage());
                $errorCode->appendChild($text);




                echo $dom->savexml();

                exit(1);
            } catch (Exception $e) {
                echo $this->getMessage();
            }
        } else {
            die($this->getMessage());
        }
    }

}

?>
