<?php

/**
 * 后台对上传文件的处理类(实现图片上传，图片缩略图)
 * ============================================================================
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Image.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class Image
{

    var $data_dir;
    var $thumb_dir;
    var $error_msg = ''; //错误信息

    //构造函数

    function __construct( $data_dir = '/uploadfiles/allimg/', $thumb_dir = '/uploadfiles/litimg/' )
    {
        $this->data_dir = $data_dir;
        $this->thumb_dir = $thumb_dir;
    }

    /**
     * 图片上传的处理函数
     * @access     public
     * @param      string    upload   上传的图片文件class名     
     * @param      array    dir      文件要上传在$this->data_dir下的目录名。如果为空图片放在则在$this->images_dir下以当月命名的目录下
     * @param     string    img_name 上传图片名称，为空则随机生成
     * @prarm     string    suffix   文件格式后缀 默认为空
     * @prarm     string    type 文件上传格式(后缀名) 默认为空
     * @return     mix       如果成功则返回文件名，否则返回false
     */
    function upload_image( $upload, $dir = '', $img_name = '', $max_file_size = 4, $suffix = '', $type = '' )
    {
        if ( $_FILES[ '' . $upload . '' ][ 'error' ] > 0 ) {
            switch ( $_FILES[ '' . $upload . '' ][ 'error' ] )
            {
                case 1:
                    $this->error_msg = '图片大小超过服务器限制';
                    break;
                case 2:
                    $this->error_msg = '图片太大！';
                    break;
                case 3:
                    $this->error_msg = '图片只加载了一部分！';
                    break;
                case 4:
                    $this->error_msg = '图片加载失败！';
                    break;
            }
            return false;
        }
        if ( ($max_file_size * 1024 * 1024) < $_FILES[ '' . $upload . '' ][ 'size' ] ) {  //判断文件是否超过限制大小
            $this->error_msg = "你上传的图片过大,本系统最大图片为{$max_file_size}MB!";
            return false;
        }
        $img1 = $_FILES[ '' . $upload . '' ][ 'tmp_name' ];         //得到文件名 上传到服务器.临时目录
        $img2 = basename( $_FILES[ '' . $upload . '' ][ 'name' ] );   //得到文件名
        $houzui = $this->getImageType( $img1 );                   //得到后缀名
        if ( $type == "" ) {
            $image_allow_array = array( 'gif', 'jpg', 'png', 'bmp', 'jpeg' );
            if ( !in_array( $houzui, $image_allow_array ) ) {
                $this->error_msg = "对不起，这里您只能上传图片文件！";
                return false;
            }
            //Todo 可以继续判断允许上传的文件类型 
        } elseif ( $type == 'file' ) {
            $file_array = array( 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pdf' );
            if ( !in_array( $houzui, $file_array ) ) {
                $this->error_msg = "对不起，这里您只能上传zip, rar, doc, docx, xls, xlsx, ppt, pdf文件！";
                return false;
            }
        } else {
            $this->error_msg = "对不起，这里您只能上传" . $type . "格式文件！";
            return false;
        }
        $imgNameDate = date( "Y-m" );
        $imgNameRand = date( "YmdHis" ) . rand( 1000, 9999 );
        //判断dir 是否为空 为空就放在时间为文件夹的目录里面
        $dir == "" ? $dirname = $imgNameDate : $dirname = $dir;
        //判断$img_name 是否为空 为空就以时间随机命名
        $img_name == "" ? $imagename = $imgNameRand : $imagename = $img_name;
        $img_dir = $this->data_dir . $dirname . "/";
        $images_suffix = empty( $suffix ) ? $houzui : $suffix; //图片文件后缀名称
        $imgname = $img_dir . $imagename . "." . $images_suffix;
        //以年月为名建立文件夹
        $this->CheckFolder( $img_dir );
        if ( move_uploaded_file( $img1, $imgname ) ) {
            return $imgname;
        } else {
            $this->error_msg = "Upload Image No Successed";
            return false;
        }
    }

    /**
     * 利用gd库生成缩略图
     *
     * @author  Walton
     * @param   string	  $image                     原图片路径
     * @param   string	  $path                        指定生成图片的目录名
     * @param   int         $thumb_width          缩略图宽度
     * @param   int         $thumb_height         缩略图高度 可选
     * @param   int         $max_size             缩略图宽度高度最大尺寸 可选
     * @param   string    $img_type                 生成图片的保存类型 可选
     * @param   int         $quality                    缩略图品质 100之内的正整数
     * @return  boolean	 成功返回 true 失败返回 false
     *
     */
    function thumb( $image, $path = '', $thumb_width, $thumb_height = 0, $max_size = 0, $img_type = 'jpg', $quality = '85' )
    {
        // 检查原始文件是否存在及获得原始文件的信息
        $data = @getimagesize( $image );
        if ( !$data ) {
            $this->error_msg = 'No Found The Picture';
            return false;
        }
        preg_match( '/(.*)\.(jpeg|jpg|png|gif)$/', $path, $fileExt );   //暂时去掉后缀名
        $path = $fileExt[ 1 ];
        //检查判断生成的图片类型
        switch ( $img_type )
        {
            case 'jpeg':
                $imgtype = 'imagejpeg';
                $path .= '.jpeg';
                break;
            case 'jpg':
                $imgtype = 'imagejpeg';
                $path .= '.jpg';
                break;
            case 'png':
                $imgtype = 'imagepng';
                $path .= '.png';
                $quality = ($quality - 100) / 11.111111; //FORMAT
                $quality = round( abs( $quality ) );
                break;
            case 'gif':
                $imgtype = 'imagegif';
                $path .= '.gif';
                break;
            default:
                $this->error_msg = "不支持" . $img_type . "格式图片文件的生成";
                return false;
                break;
        }
        $func_imagecreate = function_exists( 'imagecreatetruecolor' ) ? 'imagecreatetruecolor' : 'imagecreate';
        $func_imagecopy = function_exists( 'imagecopyresampled' ) ? 'imagecopyresampled' : 'imagecopyresized';
        $image_width = $data[ 0 ];
        $image_height = $data[ 1 ];
        if ( empty( $thumb_height ) && !empty( $thumb_width ) ) {
            $thumb_height = $image_height * $thumb_width / $image_width;        //定宽不定高
            $dst_x = 0;
            $dst_y = 0;
            $dst_w = $thumb_width;
            $dst_h = $thumb_height;
            $src_x = $src_y = 0;
        } elseif ( !empty( $thumb_height ) && empty( $thumb_width ) ) {               //定高不定宽
            $thumb_width = $image_width * $thumb_height / $image_height;
            $dst_x = 0;
            $dst_y = 0;
            $dst_w = $thumb_width;
            $dst_h = $thumb_height;
            $src_x = $src_y = 0;
        } elseif ( $max_size > 0 && empty( $thumb_width ) && empty( $thumb_height ) ) { //最大尺寸
            if ( $image_width > $image_height ) {
                $thumb_width = $max_size;
                $thumb_height = $image_height * $thumb_width / $image_width;
            } else {
                $thumb_height = $max_size;
                $thumb_width = $image_width * $thumb_height / $image_height;
            }
            $dst_x = 0;
            $dst_y = 0;
            $dst_w = $thumb_width;
            $dst_h = $thumb_height;
            $src_x = $src_y = 0;
        } else {
            if ( $image_width / $image_height > $thumb_width / $thumb_height ) {
                $dst_w = $thumb_width;
                $dst_h = $thumb_height;
                $dst_x = 0;
                $dst_y = 0;
                $src_x = intval( ($image_width * $thumb_height / $image_height - $thumb_width) / 2 );
                $src_y = 0;
                $image_width = intval( $image_height * $thumb_width / $thumb_height );
            } else {
                $dst_w = $thumb_width;
                $dst_h = $thumb_height;
                $dst_x = 0;
                $dst_y = 0;
                $src_x = 0;
                $src_y = ceil( ($image_height * $thumb_width / $image_width - $thumb_height) * 2 / 3 );
                $image_height = intval( $image_width * $thumb_height / $thumb_width );
            }
        }
        switch ( $data[ 2 ] )
        {
            case 1:
                $im = imagecreatefromgif( $image );
                break;
            case 2:
                $im = imagecreatefromjpeg( $image );
                break;
            case 3:
                $im = imagecreatefrompng( $image );
                break;
            default:
                die( "Cannot process this picture format: ($image)" . $data[ 'mime' ] );
                break;
        }
        $ni = $func_imagecreate( $thumb_width, $thumb_height );
        if ( $func_imagecreate == 'imagecreatetruecolor' ) {
            imagefill( $ni, 0, 0, imagecolorallocate( $ni, 255, 255, 255 ) );
        } else {
            imagecolorallocate( $ni, 255, 255, 255 );
        }
        //重采样拷贝部分图像并调整大小
        $func_imagecopy( $ni, $im, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $image_width, $image_height );
        /* 创建当月目录 */
        if ( empty( $path ) ) {
            $dir = $this->thumb_dir . date( 'Ym' ) . '/';
        } else {
            $dir = $path;
        }
        //检查是否有此文件夹 没有则建之
        $this->CheckFolder( dirname( $dir ) );
        $imgtype( $ni, $dir, $quality );     //以 JPEG 格式将图像输出到浏览器或文件
        return is_file( $dir ) ? str_replace( '..', '', $dir ) : false;
    }

    /**
     * 旋转图像
     * @param type $filename
     * @param type $src
     * @param type $degrees
     * @return type 
     */
    function rotate( $filename, $src, $degrees = 90 )
    {
        //读取图片
        $data = @getimagesize( $filename );
        if ( !$data )
            return false;
        switch ( $data[ 2 ] )
        {
            case 1:
                $im = imagecreatefromgif( $filename );
                break;
            case 2:
                $im = imagecreatefromjpeg( $filename );
                break;
            case 3:
                $im = imagecreatefrompng( $filename );
                break;
            default:
                $this->error_msg = "Cannot process this picture format: ($filename)" . $data[ 'mime' ];
                return false;
                break;
        }
        // Rotate
        $rotate = imagerotate( $im, $degrees, 0 );
        // Output
        if ( !imagejpeg( $rotate, $src ) )
            return false;
        @imagedestroy( $rotate );
        return true;
    }

    //创建目录 递归创建多级目录
    function CheckFolder( $filedir )
    {
        if ( !file_exists( $filedir ) ) {
            return mkdir( $filedir, 0777, true );
        }
        return true;
    }

    /**
     * 根据图片地址返回图片的类型 后缀
     * @param type $file_path
     * @return string 
     */
    function getImageType( $file_path )
    {
        $type_array = array( 1 => 'gif', 2 => 'jpg', 3 => 'png', 4 => 'swf', 5 => 'psd', 6 => 'bmp', 15 => 'wbmp' );
        if ( file_exists( $file_path ) ) {
            $img_info = @getimagesize( $file_path );
            if ( isset( $type_array[ $img_info[ 2 ] ] ) ) {
                return $type_array[ $img_info[ 2 ] ];
            }
        } else {
            $this->error_msg = '文件不存在,不能取得文件类型!';
            return false;
        }
    }

    /**
     * 返回错误信息
     * @return  string   错误信息
     */
    function error_msg()
    {
        return $this->error_msg;
    }

}

?>