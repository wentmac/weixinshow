<?php

ini_set( "gd.jpeg_ignore_warning", 1 );

/**
 * @author  zhuqiang by time 2014-07-04
 * 验证码
 * 
 */
class service_utils_Captcha_base extends Model
{

    public static $image; //生成的图片
    public static $width = 88; //图片宽度
    public static $height = 40; //图片高度
    public static $len = 4; //验证码长度
    public static $left = 2; //验证码长度
    public static $top = 25; //验证码长度
    public static $randnum = ""; //生成的验证码
    public static $y; //y轴坐标值
    public static $randcoloer; //随机颜色
    public static $is_rand_bg = false; //是否随机背景色
    public static $red = 255;
    public static $green = 255;
    public static $blue = 255;
    public static $font_max_size = 30; //字体最大值
    public static $font_min_size = 30; //字体最小值
    public static $font_size = 30; //字体最小值
    public static $font_path = 'base/var/Data/font/arial.ttf'; //字体路径
    public static $ext_num_type = '';
    public static $ext_pixel = false; //干扰点
    public static $ext_pixel_num = 100; //干扰点数量
    public static $ext_line = false; //干扰线
    public static $ext_line_num = 50; //干扰线数量
    public static $ext_rand_y = true; //Ｙ轴随机

    public static function generate()
    {
        $im_width = self::$width;
        $im_height = self::$height;
        $len = self::$len;
        $red = self::$red;
        $green = self::$green;
        $blue = self::$blue;

        $code = self::getCode( self::$ext_num_type, $len );
        $captcha_i = new Imagick ();
        $bg = new ImagickPixel ();
        $bg->setcolor( 'white' );
        $ImagickDraw = new ImagickDraw ();

        //设置字体
        $font = WEB_ROOT . self::$font_path;
        $ImagickDraw->setfont( $font );
        $ImagickDraw->setfontsize( self::$font_size );
        $captcha_i->newimage( $im_width, $im_height, $bg );
        $captcha_i->annotateimage( $ImagickDraw, self::$left, self::$top, 5, $code );
        $captcha_i->swirlimage( 20 );

        $ImagickDraw->line( rand( 0, 70 ), rand( 0, 30 ), rand( 0, 70 ), rand( 0, 30 ) );
        $ImagickDraw->line( rand( 0, 70 ), rand( 0, 30 ), rand( 0, 70 ), rand( 0, 30 ) );
        $ImagickDraw->line( rand( 0, 70 ), rand( 0, 30 ), rand( 0, 70 ), rand( 0, 30 ) );
        $ImagickDraw->line( rand( 0, 70 ), rand( 0, 30 ), rand( 0, 70 ), rand( 0, 30 ) );
        $ImagickDraw->line( rand( 0, 70 ), rand( 0, 30 ), rand( 0, 70 ), rand( 0, 30 ) );
        $captcha_i->drawimage( $ImagickDraw );
        $captcha_i->setimageformat( 'jpeg' );
        header( "Content-Type:image/{$captcha_i->getImageFormat()}" );
        echo $captcha_i->getimageblob();
        return md5( strtolower( $code ) );
    }

    //生成背景  
    private static function createBg( $width, $height, $is_rand_bg, $red, $green, $blue )
    {
        $image = imagecreatetruecolor( $width, $height );
        if ( $is_rand_bg ) {
            $color = imagecolorallocate( $image, mt_rand( 157, 255 ), mt_rand( 157, 255 ), mt_rand( 157, 255 ) );
        } else {
            $color = imagecolorallocate( $image, $red, $green, $blue );
        }

        imagefilledrectangle( $image, 0, $height, $width, 0, $color );
        return $image;
    }

    // 获得任意位数的随机码
    public static function getCode( $ext_num_type, $len )
    {
        $an1 = 'abcdefghjkmnpqrstwxyz';
        $an2 = 'ABCDEFGHJKMNPQRSTWXYZ';
        $an3 = '23456789';
        $randnum = '';
        if ( $ext_num_type == '' )
            $str = $an1 . $an2 . $an3;
        if ( $ext_num_type == 1 )
            $str = $an1;
        if ( $ext_num_type == 2 )
            $str = $an2;
        if ( $ext_num_type == 3 )
            $str = $an3;
        for ( $i = 0; $i < $len; $i ++ ) {
            $start = rand( 1, strlen( $str ) - 1 );
            $randnum .= substr( $str, $start, 1 );
        }
        return $randnum;
    }

    // 获得随机色
    private static function get_randcolor( $image )
    {
        return imagecolorallocate( $image, rand( 0, 100 ), rand( 0, 150 ), rand( 0, 200 ) );
    }

    // 添加干扰线
    private static function set_ext_line( $image, $im_width, $im_height, $line_num )
    {
        for ( $j = 0; $j < $line_num; $j ++ ) {
            $rand_x = rand( 2, $im_width );
            $rand_y = rand( 2, $im_height );
            $rand_x2 = rand( 2, $im_width );
            $rand_y2 = rand( 2, $im_height );
            $rand_color = self::get_randcolor( $image );
            imageline( $image, $rand_x, $rand_y, $rand_x2, $rand_y2, $rand_color );
        }
    }

    // 添加干扰点
    private static function set_ext_pixel( $image, $pixel_num )
    {
        for ( $i = 0; $i < $pixel_num; $i ++ ) {
            $rand_color = self::get_randcolor( $image );
            imagesetpixel( $image, rand() % 100, rand() % 100, $rand_color );
        }
    }

    // 获得验证码图片Y轴
    private static function get_y( $im_height, $is_y )
    {
        if ( $is_y )
            return rand( 5, $im_height / 5 );
        else
            return $im_height / 4;
    }

}
