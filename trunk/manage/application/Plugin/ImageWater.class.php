<?php

/*
 * 图片上打水印类，支持文字图片水印的透明度设置、水印图片背景透明。
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: ImageWater.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of imageWater
 *
 * @author Tracy McGrady
 */
class ImageWater
{

    public $waterType = 1;                      //水印类型，0：文字水印 1：图片水印
    public $pos = 0;                            //水印位置 9宫格的位置
    public $transparent = 45;                   //水印透明度
    public $waterStr = 'www.t-mac.org';         //水印文字，如果水印类型为文字水印时候写上
    public $fontSize = 16;                      //水印文字大小
    public $fontColor = array(255, 0, 255);     //水印文字颜色=>RGB
    public $fontFile = 'AHGBold.ttf';           //水印文件字体
    public $waterImg = 'logo.png';              //水印图片
    private $srcImg = '';                       //所打的水印图片
    private $im = '';                           //图片句柄
    private $water_im = '';                     //水印图片句柄
    private $srcImg_info = '';                  //图片信息
    private $waterImg_info = '';                //水印图片信息
    private $str_w = '';                        //水印文字宽度
    private $str_h = '';                        //水印文字高度
    private $x = '';                            //水印X坐标信息
    private $y = '';                            //水印Y坐标信息
    public $errormsg = '';                     //出错信息

    function __construct($img)
    {
        $this->srcImg = file_exists($img) ? $img : $this->throwError('groundImage404');
    }

    /**
     * 获取需要添加水印的图片的信息，并载入图片。   
     * @param type $img 
     */
    function imginfo()
    {
        //取得需要添加水印的图片信息
        $this->srcImg_info = getimagesize($this->srcImg);
        switch ($this->srcImg_info[2])
        {
            case 3:
                $this->im = imagecreatefrompng($this->srcImg);
                break 1;
            case 2:
                $this->im = imagecreatefromjpeg($this->srcImg);
                break 1;
            case 1:
                $this->im = imagecreatefromgif($this->srcImg);
                break 1;
            default:
                $this->throwError('NonType');
        }
    }

    /**
     * 获取水印图片的信息，并载入图片
     */
    function waterimginfo()
    {
        $this->waterImg_info = getimagesize($this->waterImg);
        switch ($this->waterImg_info[2])
        {
            case 3:
                $this->water_im = imagecreatefrompng($this->waterImg);
                break 1;
            case 2:
                $this->water_im = imagecreatefromjpeg($this->waterImg);
                break 1;
            case 1:
                $this->water_im = imagecreatefromgif($this->waterImg);
                break 1;
            default:
                $this->throwError('NonWaterImageType');
        }
    }

    /**
     * 水印位置算法
     */
    private function waterpos()
    {
        switch ($this->pos)
        {
            case 0: //位置随机
                $this->x = rand(0, $this->srcImg_info[0] - $this->waterImg_info[0]);
                $this->y = rand(0, $this->srcImg_info[1] - $this->waterImg_info[1]);
                break 1;
            case 1: //上左
                $this->x = 0;
                $this->y = 0;
                break 1;
            case 2: //上中
                $this->x = ($this->srcImg_info[0] - $this->waterImg_info[0]) / 2;
                $this->y = 0;
                break 1;
            case 3: //上右
                $this->x = $this->srcImg_info[0] - $this->waterImg_info[0];
                $this->y = 0;
                break 1;
            case 4: //中左
                $this->x = 0;
                $this->x = ($this->srcImg_info[1] - $this->waterImg_info[1]) / 2;
                break 1;
            case 5: //中中
                $this->x = ($this->srcImg_info[0] - $this->waterImg_info[0]) / 2;
                $this->y = ($this->srcImg_info[1] - $this->waterImg_info[1]) / 2;
                break 1;
            case 6: //中右
                $this->x = $this->srcImg_info[0] - $this->waterImg_info[0];
                $this->y = ($this->srcImg_info[1] - $this->waterImg_info[1]) / 2;
                break 1;
            case 7: //下左
                $this->x = 0;
                $this->y = $this->srcImg_info[1] - $this->waterImg_info[1];
                break 1;
            case 8: //下中
                $this->x = ($this->srcImg_info[0] - $this->waterImg_info[0]) / 2;
                $this->y = $this->srcImg_info[1] - $this->waterImg_info[1];
                break 1;
            default: //下右
                $this->x = $this->srcImg_info[0] - $this->waterImg_info[0];
                $this->y = $this->srcImg_info[1] = $this->waterImg_info[1];
        }
    }

    /**
     * 打图片水印
     */
    private function waterimg()
    {
        //判断 水印不能比原图还大
        if ($this->srcImg_info[0] <= $this->waterImg_info[0] || $this->srcImg_info[1] <= $this->waterImg_info[1]) {
            $this->throwError('TooBig');
        }
        //默认位置随机
        $this->waterpos();
        //创建一个新图像
        $cut = imagecreatetruecolor($this->waterImg_info[0], $this->waterImg_info[1]);
        //根据源比例,把源图片压入真色彩图模 这一步是取原图片和要打水印位置一样大小的图片 下一步做透明度用
        imagecopy($cut, $this->im, 0, 0, $this->x, $this->y, $this->waterImg_info[0], $this->waterImg_info[1]);
        $pct = $this->transparent;
        //把水印打到上一步从源图像中栽下来的位置
        imagecopy($cut, $this->water_im, 0, 0, 0, 0, $this->waterImg_info[0], $this->waterImg_info[1]);
        //将图片与水印图片合成
        imagecopymerge($this->im, $cut, $this->x, $this->y, 0, 0, $this->waterImg_info[0], $this->waterImg_info[1], $pct);
    }

    /**
     * 打上文字水印
     */
    private function waterstr()
    {
        //默认位置随机
        $this->waterpos();        
        $fontHeight = $this->fontSize;
        $this->water_im = imagecreatetruecolor($w, $h); //新建一个真彩色图像
        imagealphablending($this->water_im, false); //设定图像的混色模式
        imagesavealpha($this->water_im, true);  //设置标记以在保存 PNG 图像时保存完整的 alpha 通道信息
        $white_alpha = imagecolorallocatealpha($this->water_im, 255, 255, 255, 127);    //为一幅图像分配颜色 + alpha
        imagefill($this->water_im, 0, 0, $white_alpha); //区域填充
        $color = imagecolorallocate($this->water_im, $this->fontColor[0], $this->fontColor[1], $this->fontColor[2]); //为一幅图像分配颜色
        //imagettftext($this->water_im, $this->fontSize, 0, 0, $this->fontSize, $color, $this->fontFile, $this->waterStr); //用 TrueType 字体向图像写入文本
        imagettftext($this->water_im, $this->fontSize, 0, 0, $this->fontSize, $color, $this->fontFile, $this->waterStr); //用 TrueType 字体向图像写入文本
        $this->waterImg_info = array(
            0 => $w, 1 => $h
        );
        $this->waterimg();
    }

    /**
     * 打上文字水印
     */
    private function waterstrbak()
    {
        $rect = imagettfbbox($this->fontSize, 0, $this->fontFile, $this->waterStr);
        $w = abs($rect[2] - $rect[6]);
        $h = abs($rect[3] - $rect[7]);
        $fontHeight = $this->fontSize;
        $this->water_im = imagecreatetruecolor($w, $h); //新建一个真彩色图像
        imagealphablending($this->water_im, false); //设定图像的混色模式
        imagesavealpha($this->water_im, true);  //设置标记以在保存 PNG 图像时保存完整的 alpha 通道信息
        $white_alpha = imagecolorallocatealpha($this->water_im, 255, 255, 255, 127);    //为一幅图像分配颜色 + alpha
        imagefill($this->water_im, 0, 0, $white_alpha); //区域填充
        $color = imagecolorallocate($this->water_im, $this->fontColor[0], $this->fontColor[1], $this->fontColor[2]); //为一幅图像分配颜色
        //imagettftext($this->water_im, $this->fontSize, 0, 0, $this->fontSize, $color, $this->fontFile, $this->waterStr); //用 TrueType 字体向图像写入文本
        imagettftext($this->water_im, $this->fontSize, 0, 0, $this->fontSize, $color, $this->fontFile, $this->waterStr); //用 TrueType 字体向图像写入文本
        $this->waterImg_info = array(
            0 => $w, 1 => $h
        );
        $this->waterimg();
    }

    public function output()
    {
        $this->imginfo();   //获取需要添加水印的图片的信息，并载入图片
        if ($this->waterType == 0) { //打文字水印
            $this->waterstr();
        } else {                     //打图片水印
            $this->waterimginfo();
            $this->waterimg();
        }
        switch ($this->srcImg_info[2])
        {
            case 3 :
                imagepng($this->im, $this->srcImg);
                break;
            case 2 :
                imagejpeg($this->im, $this->srcImg);
                break;
            case 1 :
                imagegif($this->im, $this->srcImg);
                break;
            default :
                $this->throwError('outputError');
                break;
        }
        //图片合成后的后续销毁处理  
        imagedestroy($this->im);
        imagedestroy($this->water_im);
    }

    /**
     * 出错类
     * @param type $errType 
     */
    function throwError($errType)
    {
        switch ($errType)
        {
            case "TooBig":
                $this->errorMsg = '水印比原图大！';
                break;
            case "groundImage404":
                $this->errorMsg = "要打水印图片不存在";
                break;
            case "waterImage404":
                $this->errorMsg = "水印图片不存在";
                break;
            case "NonGD":
                $this->errorMsg = "没有安装GD库";
                break;
            case "NonType":
                $this->errorMsg = "原图片格式不对,只支持PNG、JPEG、GIF";
                break;
            case 'NonWaterImageType':
                $this->errorMsg = '水印图片格式不对，只支持PNG、JPEG、GIF。';
                break;
            case "WrongColor":
                $this->errorMsg = "错误的颜色格式";
                break;
            case "outputError":
                $this->errorMsg = "添加水印失败！";
            default:
                $this->errorMsg = "未知错误";
        }

        die($this->errorMsg);
        exit();
    }

}

?>
