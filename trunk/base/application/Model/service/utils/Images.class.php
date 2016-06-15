<?php

/**
 * Image  
 * 
 * 提供图片的一些常用处理程序 
 * $img = new images();
 * $img->loadFile("test.gif")->crop(0,0,100,100)->resize(50,50)->waterMark("mark.png", 'left','center')->save("b.gif");
 */
class service_utils_Images_base extends Model
{

    var $img;   //图像标识符 
    var $info;  //保存图片的一些信息 
    var $error_msg = ''; //错误信息
    var $success = true;

    function __construct ( $file = null )
    {
        if ( !extension_loaded ( 'gd' ) ) {
            return $this->exceptionFunc ( "GD库没有加载." );
        }
        if ( $file )
            $this->loadFile ( $file );
    }

    function exceptionFunc ( $msg )
    {
        $this->error_msg = $msg;
        $this->success = false;
    }

    /**
     * 返回错误信息
     * @return  string   错误信息
     */
    function error_msg ()
    {
        return $this->error_msg;
    }

    function __destruct ()
    {
        if ( is_resource ( $this->img ) ) {
            imagedestroy ( $this->img );
        }
    }

    /**
     * 图片上传的处理函数
     * @access     public
     * @param      string    upload   上传的图片文件class名     
     * @param      array    dir      文件要上传在$this->data_dir下的目录名。如果为空图片放在则在$this->images_dir下以当月命名的目录下     /home/www/123
     * @prarm     string    suffix   文件格式后缀 默认为空
     * @prarm     string    type 文件上传格式(后缀名) 默认为空
     * @return     mix       如果成功则返回文件名，否则返回false
     */
    function upload_image ( $upload_files, $dir = '', $max_file_size = 4, $suffix = '', $type = '' )
    {
        if ( $upload_files[ 'error' ] > 0 ) {
            switch ( $upload_files[ 'error' ] )
            {
                case 1:
                    $this->exceptionFunc ( '图片大小超过服务器限制' );
                    return false;                    
                case 2:
                    $this->exceptionFunc ( '图片太大' );
                    return false;                    
                case 3:
                    $this->exceptionFunc ( '图片只加载了一部分' );
                    return false;                    
                case 4:
                    $this->exceptionFunc ( '图片加载失败' );
                    return false;                    
                default :
                    $this->exceptionFunc ( '图片上传服务器环境有错误' );
                    return false;                    
            }
            return false;
        }
        if ( ($max_file_size * 1024 * 1024) < $upload_files[ 'size' ] ) {  //判断文件是否超过限制大小            
            $this->exceptionFunc ( "你上传的图片过大,本系统最大图片为{$max_file_size}MB!" );
            return false;
        }
        $img1 = $upload_files[ 'tmp_name' ];         //得到文件名 上传到服务器.临时目录
        $img2 = basename ( $upload_files[ 'name' ] );   //得到文件名
        $houzui = $this->getImageType ( $img1 );                   //得到后缀名
        if ( $type == "" ) {
            $image_allow_array = array('gif', 'jpg', 'png', 'bmp', 'jpeg');
            if ( !in_array ( $houzui, $image_allow_array ) ) {
                $this->exceptionFunc ( "对不起，这里您只能上传图片文件！" );
                return false;
            }
            //Todo 可以继续判断允许上传的文件类型 
        } elseif ( $type == 'file' ) {
            $file_array = array('zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pdf', 'jpg', 'bmp', 'png', 'wav', 'mp3', 'avi', 'mp4', 'mkv');
            $path = $upload_files[ 'name' ];
            $houzui = pathinfo ( $path, PATHINFO_EXTENSION );
            if ( !in_array ( $houzui, $file_array ) ) {
                $this->exceptionFunc ( "对不起，这里您只能上传zip, rar, doc, docx, xls, xlsx, ppt, pdf文件！" . $houzui );
                return false;
            }
        } else {
            $this->exceptionFunc ( "对不起，这里您只能上传" . $type . "格式文件！" );
            return false;
        }

        $dir_pathinfo = pathinfo ( $dir );
        $suffix = empty ( $suffix ) ? $houzui : $suffix;
        $images_suffix = !empty ( $dir_pathinfo[ 'extension' ] ) ? $dir_pathinfo[ 'extension' ] : $suffix; //图片文件后缀名称 
        $imgname = $dir_pathinfo[ 'dirname' ] . DIRECTORY_SEPARATOR . $dir_pathinfo[ 'filename' ] . '.' . $images_suffix;
        //以年月为名建立文件夹
        $this->CheckFolder ( $dir_pathinfo[ 'dirname' ] );
        if ( move_uploaded_file ( $img1, $imgname ) ) {
            return $imgname;
        } else {
            $this->exceptionFunc ( "Upload Image No Successed" );
            return false;
        }
    }

    /*
      //返回图片信息
      function __call($method, $arg) {
      if(substr($method, 0, 3) == 'get') {
      $attr = strtolower(substr($method, 3));
      if(isset($this->info[$attr]))
      return $this->info[$attr];
      return null;
      }
      }
     */

    //返回图片资源 
    function getResource ()
    {
        if ( isset ( $this->img ) )
            return $this->img;
        return null;
    }

    // 获取图片信息
    function getImgInfo ( $key = false )
    {
        if ( $key ) {
            return isset ( $this->info[ $key ] ) ? $this->info[ $key ] : null;
        } else {
            return $this->info;
        }
    }

    /**
     * 保存到文件 
     *  
     * @param string $path 文件的绝对路径  
     * @param string $quality jpeg文件的质量  
     */
    function save ( $path, $quality = null )
    {
        return $this->_output ( $path, null, $quality );
    }

    /**
     * 将图片输出到浏览器 
     * 
     * @param $type 格式 
     */
    function output ( $type = 'gif' )
    {
        return $this->_output ( 'stream', $type );
    }

    /**
     * 初始化，创建图像标识符 
     * 
     * @param $file 源文件 
     */
    function loadFile ( $file )
    {
        if ( !file_exists ( $file ) ) {
            $this->exceptionFunc ( "指定的文件不存在 => $file" );
            return false;
        }
        $string = file_get_contents ( $file );
        $images_info = getimagesize ( $file );
        $this->info[ 'width' ] = $images_info[ 0 ];
        $this->info[ 'height' ] = $images_info[ 1 ];
        $this->info[ 'type' ] = $images_info[ 2 ];
        $this->info[ 'file' ] = $file;
        $this->img = imagecreatefromstring ( $string );
        return $this;
    }

    /**
     * 图片缩放 
     * 
     * @param int $dst_w 目标宽度 
     * @param int $dst_h 目标高度 
     * @param boolean $keepScale，是否等比缩放 
     * @return array $wh 包含缩放后宽度和高度的数组 $wh[0]为宽度，$wh[1]为高度 
     */
    function resize ( $dst_w, $dst_h, $keepScale = true )
    {
        $src_w = $this->getWidth ();
        $src_h = $this->getHeight ();
        $dst_x = $dst_y = 0; //需要载入的图片在新图中的x|y坐标
        $src_x = $src_y = 0; //载入图片要载入的区域x坐标    
        if ( $keepScale ) {
            //开始处理如果缩略图尺寸大于原图尺寸宽高的情况
            if ( $dst_w > $src_w ) {//如果生成的新图宽度大于原图的宽度                 
                $dst_w = $src_w; //新图的宽度 等于 原图要载入的宽度
            }
            if ( $dst_h > $src_h ) {//如果生成的新图高度大于原图的高度 
                $dst_h = $src_h;
            }
            if ( !empty ( $dst_w ) && !empty ( $dst_h ) ) {    //宽和高哪个超了 压缩哪个
                //原始宽高比大于目标宽高比,调整高度 
                if ( ( double ) ($src_w / $src_h) > ( double ) ($dst_w / $dst_h) ) {
                    $dst_h = ceil ( $src_h * $dst_w / $src_w );
                } else {
                    ///原始宽高比小于目标宽高比,调整宽度 
                    $dst_w = ceil ( $src_w * $dst_h / $src_h );
                }
            } else {//定宽不定高 或 定高不定宽 压缩
                if ( empty ( $dst_h ) && !empty ( $dst_w ) ) {
                    $dst_h = ceil ( $src_h * $dst_w / $src_w );        //定宽不定高                                                            
                } elseif ( !empty ( $dst_h ) && empty ( $dst_w ) ) {
                    $dst_w = ceil ( $src_w * $dst_h / $src_h );         //定高不定宽                    
                }
            }
        }
        //创建一个透明背景的图像 
        $newimg = $this->_createAlphaImage ( $dst_w, $dst_h );
        //将原始重新采样复制到透明背景上 
        imagecopyresampled ( $newimg, $this->img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );
        imagedestroy ( $this->img );
        $this->img = $newimg;
        $this->info[ 'width' ] = $dst_w;
        $this->info[ 'height' ] = $dst_h;
        return $this;
    }

    /**
     * 创建一个透明图片,用于图像复制 
     * 
     * @param int $width 宽度 
     * @param int $height 高度 
     */
    function _createAlphaImage ( $width, $height )
    {
        $newimg = imagecreatetruecolor ( $width, $height );
        if ( $this->getType () == 1 ) { //gif图片 
            $colorCount = imagecolorstotal ( $this->img );
            imagetruecolortopalette ( $newimg, true, $colorCount );
            imagepalettecopy ( $newimg, $this->img );
            $transparentcolor = imagecolortransparent ( $this->img );
            imagefill ( $newimg, 0, 0, $transparentcolor );
            imagecolortransparent ( $newimg, $transparentcolor );
        } elseif ( $this->getType () == 3 ) { //png图片 
            imagealphablending ( $newimg, false );
            $col = imagecolorallocatealpha ( $newimg, 255, 255, 255, 127 );
            imagefilledrectangle ( $newimg, 0, 0, $width, $height, $col );
            imagealphablending ( $newimg, true );
        }
        return $newimg;
    }

    /**
     * 生成缩略图 
     * 
     * @param int $dst_w 宽度 生成的新图宽度
     * @param int $dst_h 高度 生成的新图高度
     * @param boolean $crop 是否对超出部分进行裁剪,默认为是, 如果不裁剪,则缩图将等比缩放至小于目标尺寸 
     * @param boolean $center crop时是否在中间裁剪 
     * @param string $path 要生成的新文件名 
     */
    function thumb ( $dst_w = 128, $dst_h = 128, $crop = true, $center = true, $path = null )
    {

        $destw = min ( $this->getWidth (), $dst_w );
        $desth = min ( $this->getHeight (), $dst_h );
        if ( $crop ) {
            if ( empty ( $dst_w ) || empty ( $dst_h ) ) {
                $this->exceptionFunc ( "对超出部分进行裁剪时 => 压缩的宽高不能为空" );
                return false;
            }
            $src_w = $this->getWidth ();
            $src_h = $this->getHeight ();
            $dst_x = $dst_y = 0; //需要载入的图片在新图中的x|y坐标
            $src_x = $src_y = 0; //载入图片要载入的区域x坐标       
            //开始处理如果缩略图尺寸大于原图尺寸宽高的情况
            if ( $dst_w > $src_w ) {//如果生成的新图宽度大于原图的宽度                 
                $dst_w = $src_w; //新图的宽度 等于 原图要载入的宽度
            }
            if ( $dst_h > $src_h ) {//如果生成的新图高度大于原图的高度 
                $dst_h = $src_h;
            }
            //开始等比例压缩 计算宽高
            if ( ( double ) ($src_w / $src_h) > ( double ) ($dst_w / $dst_h) ) {
                //计算应COPY的宽度 
                $src_w = ceil ( $dst_w * $src_h / $dst_h );
                //计算起始的x坐标 
                if ( $center )
                    $src_x = ceil ( ($this->getWidth () - $src_w) / 2 );
            } else {
                //计算应COPY的高度 
                $src_h = ceil ( $dst_h * $src_w / $dst_w );
                //计算起始的y坐标 
                if ( $center )
                    $src_y = ceil ( ($this->getHeight () - $src_h) / 2 );
            }
            //创建一个透明背景的图像 
            $newimg = $this->_createAlphaImage ( $dst_w, $dst_h );
            //将原始重新采样复制到透明背景上 
            imagecopyresampled ( $newimg, $this->img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );
            imagedestroy ( $this->img );
            $this->img = $newimg;
            $this->info[ 'width' ] = $dst_w;
            $this->info[ 'height' ] = $dst_h;
        } else {
            $this->resize ( $destw, $desth );
        }
        if ( $path )
            return $this->save ( $path );
        return $this;
    }

    /**
     * 图片裁剪 
     * 
     * @param int $x x坐标 
     * @param int $y y坐标 
     * @param int $w 裁剪宽度 
     * @param int $h 裁剪高度 
     * @return resource 返回裁剪后的图片资源 
     */
    function crop ( $x, $y, $w, $h )
    {
        $w = min ( $w, $this->getWidth () );
        $h = min ( $h, $this->getHeight () );
        $newimg = $this->_createAlphaImage ( $w, $h );
        imagecopy ( $newimg, $this->img, 0, 0, $x, $y, $w, $h );
        imagedestroy ( $this->img );
        $this->img = $newimg;
        $this->info[ 'width' ] = $w;
        $this->info[ 'height' ] = $h;
        return $this;
    }

    /**
     * 对图片进行波纹处理 
     * 
     * @param int $grade 弯曲度数,越大图片变形越厉害 
     * @param string $dir h=水平, v=垂直 
     */
    function wave ( $grade = 5, $dir = "h" )
    {
        $w = $this->getWidth ();
        $h = $this->getHeight ();
        if ( $dir == "h" ) {
            for ( $i = 0; $i < $w; $i+=2 ) {
                imagecopyresampled ( $this->img, $this->img, $i - 2, sin ( $i / 10 ) * $grade, $i, 0, 2, $h, 2, $h );
            }
        } else {
            for ( $i = 0; $i < $h; $i+=2 ) {
                imagecopyresampled ( $this->img, $this->img, sin ( $i / 10 ) * $grade, $i - 2, 0, $i, $w, 2, $w, 2 );
            }
        }
        return $this;
    }

    /**
     * 给图片加带背景的一行文字 
     * 
     * @param string $text 水印文字 
     * @param string $font 字体文件的路径 
     * @param $color 文字的颜色 16进制，默认为黑色 
     * @param int $size     文字的大小 
     * @param string $path 如果指定则生成图片到$path 
     * @return 生成的水印图片路径 
     */
    function textMark ( $text, $font, $color = "#000000", $size = 9, $path = null )
    {
        if ( !file_exists ( $font ) ) {
            $this->exceptionFunc ( "字体文件不可用 => $font" );
            return false;
        }

        //取得图片的高度和宽度 
        $mwidth = $this->getWidth ();
        $mheight = $this->getHeight ();

        $color = $this->_hexColor ( $color );
        $color = imagecolorallocate ( $this->img, $color[ 'r' ], $color[ 'g' ], $color[ 'b' ] );
        $black = imagecolorallocate ( $this->img, 0, 0, 0 );
        $alpha = imagecolorallocatealpha ( $this->img, 230, 230, 230, 40 );
        //填充文字背景 
        $box = imagettfbbox ( $size, 0, $font, $text );
        //文字补白 
        $padding = 6;
        $textheight = $box[ 1 ] - $box[ 7 ];
        $bgheight = $textheight + $padding * 2;
        //文字背景 
        imagefilledrectangle ( $this->img, 0, $mheight - $bgheight, $mwidth, $mheight, $alpha );
        //小竖条 
        imagefilledrectangle ( $this->img, 10, $mheight - $padding - $textheight, 15, $mheight - $padding, $color );
        //填充文字 
        $texty = $mheight - $bgheight / 2 + $textheight / 2;
        imagettftext ( $this->img, $size, 0, 20, $texty, $color, $font, $text );
        if ( $path )
            return $this->save ( $path );
        return $this;
    }

    /**
     * 用一张PNG图片给原始图片加水印，水印图片将自动调整到目标图片大小 
     * 
     * @param string $png png图片的路径 
     * @param string $hp 水平位置 left|center|right 
     * @param string $vp 垂直位置 top|center|bottom 
     * @param int    $spacing 水印距上下左右的间距
     * @param int    $pct 水印的透明度 0-100, 0为完全透明,100为完全不透明,只适用于非PNG图片水印 
     * @param string $path 如果指定则生成图片到$path 
     * @param  
     * @return 
     */
    function waterMark ( $markImg, $hp = 'center', $vp = 'center', $spacing = 0, $pct = 50, $path = null )
    {
        //原图信息 
        $srcw = $this->getWidth ();
        $srch = $this->getHeight ();

        //水印图信息 
        $mark = new self ( $markImg );
        $markw = $mark->getWidth ();
        $markh = $mark->getHeight ();

        //水印图片大于目标图片，调整大小 
        if ( $markw > $srcw || $markh > $srch ) {
            //先将水印图片调整到原始图片大小-10个像素 
            $mark->resize ( $srcw - 10, $srch - 10, true );
            $markw = $mark->getWidth ();
            $markh = $mark->getHeight ();
        }

        //判断水印位置 
        $arrx = array('left' => 0 + $spacing, 'center' => floor ( ($srcw - $markw) / 2 ), 'right' => $srcw - $markw - $spacing);
        $arry = array('top' => 0 + $spacing, 'center' => floor ( ($srch - $markh) / 2 ), 'bottom' => $srch - $markh - $spacing);
        $x = isset ( $arrx[ $hp ] ) ? $arrx[ $hp ] : $arrx[ 'center' ];
        $y = isset ( $arry[ $vp ] ) ? $arry[ $vp ] : $arry[ 'center' ];

        //png图片水印 
        if ( $mark->getType () == 3 ) {
            //打开混色模式 
            imagealphablending ( $this->img, true );
            imagecopy ( $this->img, $mark->getResource (), $x, $y, 0, 0, $markw, $markh );
        } else {
            imagecopymerge ( $this->img, $mark->getResource (), $x, $y, 0, 0, $markw, $markh, $pct );
        }
        unset ( $mark );
        if ( $path )
            return $this->save ( $path, 100 );
        return $this;
    }

    /**
     * 旋转图像
     * @param type $filename
     * @param type $src
     * @param type $degrees
     * @return type 
     */
    function rotate ( $src, $degrees = 90 )
    {
        // Rotate
        $rotate = imagerotate ( $this->img, $degrees, 0 );
        // Output
        if ( !imagejpeg ( $rotate, $src ) )
            return false;
        @imagedestroy ( $rotate );
        return true;
    }

    //返回由16进制组成的颜色索引 
    function _hexColor ( $hex )
    {
        $color = hexdec ( substr ( $hex, 1 ) );
        return array(
            "r" => ($color & 0xFF0000) >> 16,
            "g" => ($color & 0xFF00) >> 8,
            "b" => $color & 0xFF
        );
    }

    //png的alpha校正 
    function _pngalpha ( $format )
    {
        //PNG图像要保持alpha通道 
        if ( $format == 'png' ) {
            imagealphablending ( $this->img, false );
            imagesavealpha ( $this->img, true );
        }
    }

    /**
     * 输出函数，内部使用 
     * @param type $path
     * @param type $type
     * @param type $quality 如果是jpg的可以设置图片 质量 默认75
     * @return images 
     */
    function _output ( $path, $type = null, $quality = null )
    {
        $toFile = false;
        //输出到文件 
        if ( $path != 'stream' ) {
            $this->CheckFolder ( dirname ( $path ) );
            $type = pathinfo ( $path, PATHINFO_EXTENSION );
            $toFile = true;
        }
        //png的alpha校正 
        $this->_pngalpha ( $type );

        if ( $type == "jpg" )
            $type = "jpeg";
        $func = "image" . $type;
        if ( !function_exists ( $func ) ) {
            $type = 'gif';
            $func = 'imagegif';
        }
        if ( $toFile ) {
            if ( $func == 'imagejpeg' && $quality != null ) {
                $func ( $this->img, $path, $quality );
            } else {
                $func ( $this->img, $path );
            }
        } else {
            if ( !headers_sent () )
                header ( "Content-type:image/" . $type );
            $func ( $this->img );
        }
        return $this;
    }

    function getWidth ()
    {
        if ( isset ( $this->info[ 'width' ] ) ) {
            return $this->info[ 'width' ];
        }
        return false;
    }

    function getHeight ()
    {
        if ( isset ( $this->info[ 'height' ] ) ) {
            return $this->info[ 'height' ];
        }
        return false;
    }

    function getType ()
    {
        if ( isset ( $this->info[ 'type' ] ) ) {
            return $this->info[ 'type' ];
        }
        return false;
    }

    //创建目录 递归创建多级目录
    function CheckFolder ( $filedir )
    {
        if ( !file_exists ( $filedir ) ) {            
            if ( !mkdir ( $filedir, 0777, true ) ) {
                $this->exceptionFunc ( '指定的路径权限不足 =>' . $filedir );
                return false;
            }
        }
        return true;
    }

    /**
     * 根据图片地址返回图片的类型 后缀
     * @param type $file_path
     * @return string 
     */
    function getImageType ( $file_path )
    {
        $type_array = array(1 => 'gif', 2 => 'jpg', 3 => 'png', 4 => 'swf', 5 => 'psd', 6 => 'bmp', 15 => 'wbmp');
        if ( file_exists ( $file_path ) ) {
            $img_info = @getimagesize ( $file_path );
            if ( isset ( $type_array[ $img_info[ 2 ] ] ) ) {
                return $type_array[ $img_info[ 2 ] ];
            }
        } else {
            $this->exceptionFunc ( '文件不存在,不能取得文件类型!' );
            return false;
        }
    }

}

?>
