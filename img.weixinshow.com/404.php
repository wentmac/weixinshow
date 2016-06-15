<?php

$real_img_path = 'uploads';
$thumb_img_path = 'thumb';
require_once("images.class.php");
require_once("images.config.php");
$uri = strtolower( $_SERVER[ 'REQUEST_URI' ] );
$directory = dirname( __FILE__ );
//(shops|avatar|room)_(\d+|\d+x\d+)\/(\d{4})\/(\d{2})\/(\d{2})\/([a-z0-9]{1})\/([a-z0-9]{1})\/([a-z0-9]{16})(.big)?.(jpg)
if ( preg_match_all( "/(" . IMAGE_TYPE . ")_(\d+|\d+x\d+)\/([a-z0-9]{2})\/([a-z0-9]{2})\/([a-z0-9]{12}).(jpg)/i", $uri, $matches, 2 ) ) {
    $size = $matches[ 0 ][ 2 ];
    $sourceFile = $directory . DIRECTORY_SEPARATOR . $real_img_path . DIRECTORY_SEPARATOR . $matches[ 0 ][ 1 ] . DIRECTORY_SEPARATOR . $matches[ 0 ][ 3 ] . DIRECTORY_SEPARATOR . $matches[ 0 ][ 4 ] . DIRECTORY_SEPARATOR . $matches[ 0 ][ 5 ] . '.' . $matches[ 0 ][ 6 ];
    $sourceFile = str_replace( $thumb_img_path, $real_img_path, $sourceFile );
    if ( file_exists( $sourceFile ) ) {
        $littlePic = $directory . DIRECTORY_SEPARATOR . $thumb_img_path . DIRECTORY_SEPARATOR . $matches[ 0 ][ 0 ];
        $size = $matches[ 0 ][ 2 ]; //200x200|40        
        $img = new images();
        $imageType = $matches[ 0 ][ 1 ];    //图片分类
        if ( isset( $config[ 'images' ][ 'size' ][ $imageType ] ) ) {
            foreach ( $config[ 'images' ][ 'size' ][ $imageType ] AS $k => $v ) {
                if ( $v == $size ) {
                    $img->loadFile( $sourceFile );
                    if ( preg_match_all( "/(\d+)x(\d+)/i", $size, $sizeArray ) ) {
                        $thumbWidth = $sizeArray[ 1 ][ 0 ];
                        $thumbHeight = $sizeArray[ 2 ][ 0 ];
                    } else {
                        $thumbWidth = $size;
                        $thumbHeight = $size;
                    }
                    if ( $thumbWidth == 0 || $thumbHeight == 0 ) {//如果宽高中有等于0的则是进行定宽高的等比例压缩;
                        $img->resize( $thumbWidth, $thumbHeight );
                    } else {
                        $img->thumb( $thumbWidth, $thumbHeight );
                    }
                    $img->save( $littlePic, 100 );
                }
            }
        }
        if ( file_exists( $littlePic ) ) {
            //$url = substr( $uri, 1 );
            //header( "Location: " . CFG_INDEX_URL . $url );            
              $image = imagecreatefromjpeg($littlePic);
              header('Content-Type: image/jpeg');
              imagepng($image);
              imagedestroy($image);             
        } else {
            nophotoLocation( "nophoto.gif" );
        }
    } else {
        nophotoLocation( "nophoto.gif" );
    }
}

function nophotoLocation( $nophotoUrl )
{
    //header( "Location: " . CFG_INDEX_URL . $nophotoUrl );    
    $image = imagecreatefromgif( $nophotoUrl );
    header( 'Content-Type: image/gif' );
    imagegif( $image );
    imagedestroy( $image );          
}

?>
