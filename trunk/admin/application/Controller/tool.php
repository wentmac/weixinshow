<?php

/**
 * 工具 (upload) Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: tool.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class toolAction extends service_Controller_admin
{

    private $tmp_model;

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        set_time_limit( 0 );
    }

    /**
     * 资讯类别管理 首页
     */
    public function index()
    {
        $this->redirect( '正在转向首页!', '/' );
        exit;
    }

    /**
     * 文件上传
     */
    public function upload()
    {
        $filename = !empty( $_GET[ 'filename' ] ) ? $_GET[ 'filename' ] : 'thumb_upload';
        $action = !empty( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : 'article';
        $type = $this->getParam( 'type' );

        $data_dir = WEB_ROOT . 'Public/uploadfiles/';
        $dir = $action . '/' . date( 'Y-m' );

        if ( $type == 'file' ) {
            $typefile = $type;
        } else {
            $typefile = '';
        }

        $image = new Image( $data_dir, $thumb_dir = '' );
        $upload_image = $image->upload_image( $filename, 0, $dir );
        $error = '';
        if ( $upload_image === false ) {
//            $rs = $image->error_msg();
            $error = $image->error_msg();
            $url = '';
        } else {
            $url = $upload_image;
        }
        echo "{";
        echo "error: '" . $error . "',\n";
        echo "url: '" . $url . "'\n";
        echo "}";
    }

    /**
     * 上传图片
     */
    public function uploadImg()
    {
        header( 'Content-Type: text/html; charset=UTF-8' );
        $inputName = 'filedata'; //表单文件域name
        $attachDir = VAR_ROOT . 'Data' . DIRECTORY_SEPARATOR . 'upload'; //上传文件保存路径，结尾不要带/
        $dirType = 1; //1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
        $maxAttachSize = 8097152; //最大上传大小，默认是2M
        $upExt = 'txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid'; //上传扩展名
        $msgType = 1; //返回上传参数的格式：1，只返回url，2，返回参数数组
        $immediate = isset( $_GET[ 'immediate' ] ) ? $_GET[ 'immediate' ] : 0; //立即上传模式，仅为演示用        

        $imageType = !empty( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : 'article';

        $err = "";
        $msg = "''";
        $tempPath = $attachDir . '/' . date( "YmdHis" ) . mt_rand( 10000, 99999 ) . '.tmp';
        $localName = '';

        $this->tmp_model = Tmac::model( 'Tool', APP_ADMIN_NAME );

        if ( isset( $_SERVER[ 'HTTP_CONTENT_DISPOSITION' ] ) && preg_match( '/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i', $_SERVER[ 'HTTP_CONTENT_DISPOSITION' ], $info ) ) {//HTML5上传
            file_put_contents( $tempPath, file_get_contents( "php://input" ) );
            $localName = $info[ 2 ];
        } else {//标准表单式上传
            $upfile = @$_FILES[ $inputName ];
            if ( !isset( $upfile ) )
                $err = '文件域的name错误';
            elseif ( !empty( $upfile[ 'error' ] ) ) {
                switch ( $upfile[ 'error' ] )
                {
                    case '1':
                        $err = '文件大小超过了php.ini定义的upload_max_filesize值';
                        break;
                    case '2':
                        $err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
                        break;
                    case '3':
                        $err = '文件上传不完全';
                        break;
                    case '4':
                        $err = '无文件上传';
                        break;
                    case '6':
                        $err = '缺少临时文件夹';
                        break;
                    case '7':
                        $err = '写文件失败';
                        break;
                    case '8':
                        $err = '上传被其它扩展中断';
                        break;
                    case '999':
                    default:
                        $err = '无有效错误代码';
                }
            } elseif ( empty( $upfile[ 'tmp_name' ] ) || $upfile[ 'tmp_name' ] == 'none' )
                $err = '无文件上传';
            else {
                move_uploaded_file( $upfile[ 'tmp_name' ], $tempPath );
                $localName = $upfile[ 'name' ];
            }
        }

        if ( $err == '' ) {
            $fileInfo = pathinfo( $localName );
            $extension = $fileInfo[ 'extension' ];
            if ( preg_match( '/' . str_replace( ',', '|', $upExt ) . '/i', $extension ) ) {
                $bytes = filesize( $tempPath );
                if ( $bytes > $maxAttachSize )
                    $err = '请不要上传大小超过' . $this->tmp_model->formatBytes( $maxAttachSize ) . '的文件';
                else {
                    switch ( $dirType )
                    {
                        case 1: $attachSubDir = 'day_' . date( 'ymd' );
                            break;
                        case 2: $attachSubDir = 'month_' . date( 'ym' );
                            break;
                        case 3: $attachSubDir = 'ext_' . $extension;
                            break;
                    }
                    //$attachDir = $attachDir . '/' . $attachSubDir;
                    if ( !is_dir( $attachDir ) ) {
                        @mkdir( $attachDir, 0777 );
                    }
                    PHP_VERSION < '4.2.0' && mt_srand( (double) microtime() * 1000000 );
                    $newFilename = date( "YmdHis" ) . mt_rand( 1000, 9999 ) . '.' . $extension;
                    $targetPath = $attachDir . '/' . $newFilename;

                    rename( $tempPath, $targetPath );
                    @chmod( $targetPath, 0755 );
                    //$targetPath = $this->tmp_model->jsonString($targetPath);

                    $imageMd5 = md5_file( $targetPath );
                    $postField = array(
                        'imageResource' => '@' . $targetPath,
                        'imageMd5' => $imageMd5,
                        'imageDirectory' => $imageType,
                        'key' => md5( UPAPI_KEY . $imageMd5 )
                    );
                    if ( class_exists( '\CURLFile' ) ) {
                        $postField[ 'imageResource' ] = new \CURLFile( realpath( $targetPath ), 'image/png', 'imageResource' );
                    }
                    $re = Functions::curl_post_contents( UPLOAD_URL . 'uploadfiles/upapi/upfile.php', $postField );
                    $uploadImagesRes = json_decode( $re, true );
                    if ( $uploadImagesRes[ 'success' ] ) {
                        $msg = $this->tmp_model->jsonString( $uploadImagesRes[ 'data' ][ 'imageUrl' ] );
                        @unlink( $targetPath );
                    } else {
                        $msg = '';
                    }
                }
            } else
                $err = '上传文件扩展名必需为：' . $upExt;

            @unlink( $tempPath );
        }

        echo "{'err':'" . $this->tmp_model->jsonString( $err ) . "','msg':'" . $msg . "'}";
    }

    public function Captcha()
    {
        $image = imagecreatetruecolor( 58, 22 );
        $color_Background = imagecolorallocate( $image, 255, 255, 255 );
        imagefill( $image, 0, 0, $color_Background );
        $key = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
        $string = null;
        $char_X = 6;
        $char_Y = 0;
        for ( $i = 0; $i < 4; $i++ ) {
            $char_Y = mt_rand( 0, 5 );
            $char = $key[ mt_rand( 0, 9 ) ];
            $string .= $char;
            $color_Char = imagecolorallocate( $image, mt_rand( 0, 230 ), mt_rand( 0, 230 ), mt_rand( 0, 230 ) );
            imagechar( $image, 5, $char_X, $char_Y, $char, $color_Char );
            $char_X = $char_X + mt_rand( 8, 15 );
        }
        $line_X1 = 0;
        $line_Y1 = 0;
        $line_X2 = 0;
        $line_Y2 = 0;
        for ( $i = 0; $i < mt_rand( 0, 64 ); $i++ ) {
            $line_X1 = mt_rand( 0, 58 );
            $line_Y1 = mt_rand( 0, 22 );
            $line_X2 = mt_rand( 0, 58 );
            $line_Y2 = mt_rand( 0, 22 );
            $line_X1 = $line_X1;
            $line_Y1 = $line_Y1;
            $line_X2 = $line_X1 + mt_rand( 1, 8 );
            $line_Y2 = $line_Y1 + mt_rand( 1, 8 );
            $color_Line = imagecolorallocate( $image, mt_rand( 0, 230 ), mt_rand( 0, 230 ), mt_rand( 0, 230 ) );
            imageline( $image, $line_X1, $line_Y1, $line_X2, $line_Y2, $color_Line );
        }
        $_SESSION[ 'valid' ] = md5( $string );
        header( 'Content-Type: image/jpeg' );
        imagepng( $image );
        imagedestroy( $image );
    }

    public function uploadImageByAjax()
    {
        $upload = !empty( $_GET[ 'filename' ] ) ? $_GET[ 'filename' ] : 'thumb_upload';
        $pic_type = !empty( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : 'works';
        $size = !empty( $_GET[ 'size' ] ) ? $_GET[ 'size' ] : '200x150';
        $max_file_size = 5; //图片大小5MB                        
        $pic_url = '';
        $error = $imageUrl = $imageId = '';

        if ( empty( $imageUrl ) ) {
            //TODO 上传图片并返回图片url            
            $image = new images();
            $img_path = VAR_ROOT . 'Data' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $pic_type . '/' . date( 'YmdHis' ) . '_' . rand( 1000, 9999 );
            $upload_image = $image->upload_image( $upload, $img_path, $max_file_size, 'jpg' );
            if ( $upload_image === false ) {
                $error = $image->error_msg();
            } else {
                $image_realpath = $upload_image;
                $imageMd5 = md5_file( $image_realpath );
                $postField = array(
                    'key' => md5( UPAPI_KEY . $imageMd5 ),
                    'imageResource' => '@' . $image_realpath,
                    'imageType' => $pic_type,
                    'imageMd5' => $imageMd5,
                    'size' => $size
                );
                if ( class_exists( '\CURLFile' ) ) {
                    $postField[ 'imageResource' ] = new \CURLFile( realpath( $image_realpath ), 'image/png', 'imageResource' );
                }
                $re = Functions::curl_post_contents( IMAGE_URL . 'upapi/upfile.php', $postField );
                if ( $re ) {
                    $result = json_decode( $re, true );
                    if ( $result[ 'success' ] ) {
                        $imageUrl = $result[ 'data' ][ 'imageUrl' ];
                        $imageId = $result[ 'data' ][ 'imageId' ];
                    } else {
                        $error = $result[ 'message' ];
                    }
                } else {
                    $error = '照片上传到图片服务器失败，请重试或联系网站客服';
                }
            }
            //图片上传结束   
            file_exists( $image_realpath ) && @unlink( $image_realpath );
        }

        echo "{";
        echo "error: '" . $error . "',\n";
        echo "photo_url: '" . $imageUrl . "',\n";
        echo "photo_id: '" . $imageId . "',\n";
        echo "}";
    }

    public function uploadFileByAjax()
    {
        $upload = !empty( $_GET[ 'filename' ] ) ? $_GET[ 'filename' ] : 'thumb_upload';
        $type = !empty( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : 'works';

        $max_file_size = 5; //图片大小5MB                        
        $pic_url = '';
        $error = $url = $id = '';

        if ( empty( $imageUrl ) ) {
            //TODO 上传图片并返回图片url            
            $image = new images();
            $fileMd5 = md5_file( $_FILES[ '' . $upload . '' ][ 'tmp_name' ] );
            $fileMd5 = substr( $fileMd5, 8, 16 );
            $id = substr( $fileMd5, 0, 2 ) . '/' . substr( $fileMd5, 2, 2 ) . '/' . substr( $fileMd5, 4 ); //$imageId=a2/xs/sajiknilijklkjj            
            $img_path = STATIC_ROOT . 'uploadfiles' . DIRECTORY_SEPARATOR . 'attachment' . DIRECTORY_SEPARATOR . $type . '/' . $id;
            $upload_image = $image->upload_image( $upload, $img_path, $max_file_size, '', 'file' );
            if ( $upload_image === false ) {
                $error = $image->error_msg();
            } else {
                $id .= '.' . end( explode( '.', $upload_image ) );
                $url = STATIC_URL . 'attachment.php?type=' . $type . '&id=' . $id;
            }
        }

        echo "{";
        echo "error: '" . $error . "',\n";
        echo "url: '" . $url . "',\n";
        echo "id: '" . $id . "',\n";
        echo "}";
    }

    public function upload_image_by_ajax()
    {
        //TODO checklogin
        $upload = !empty( $_GET[ 'filename' ] ) ? $_GET[ 'filename' ] : 'thumb_upload';
        $pic_type = !empty( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : 'works';
        $size = !empty( $_GET[ 'size' ] ) ? $_GET[ 'size' ] : '200x150';
        $max_file_size = 5; //图片大小5MB                        
        $pic_url = '';
        $error = $imageUrl = $imageId = '';

        if ( empty( $imageUrl ) ) {
            //TODO 上传图片并返回图片url            
            $image = new images();
            $img_path = VAR_ROOT . 'Data' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $pic_type . '/' . date( 'YmdHis' ) . '_' . rand( 1000, 9999 );
            $upload_image = $image->upload_image( $upload, $img_path, $max_file_size, 'jpg' );
            if ( $upload_image === false ) {
                $error = $image->error_msg();
            } else {
                $image_realpath = $upload_image;
                $imageMd5 = md5_file( $image_realpath );
                $postField = array(
                    'key' => md5( UPAPI_KEY . $imageMd5 ),
                    'imageResource' => '@' . $image_realpath,
                    'imageType' => $pic_type,
                    'imageMd5' => $imageMd5,
                    'size' => $size
                );
                if ( class_exists( '\CURLFile' ) ) {
                    $postField[ 'imageResource' ] = new \CURLFile( realpath( $image_realpath ), 'image/png', 'imageResource' );
                }
                $re = Functions::curl_post_contents( IMAGE_URL . 'upapi/upfile.php', $postField );
                if ( $re ) {
                    $result = json_decode( $re, true );
                    if ( $result[ 'success' ] ) {
                        $imageUrl = $result[ 'data' ][ 'imageUrl' ];
                        $imageId = $result[ 'data' ][ 'imageId' ];
                    } else {
                        $error = $result[ 'message' ];
                    }
                } else {
                    $error = '照片上传到图片服务器失败，请重试或联系网站客服';
                }
            }
            //图片上传结束   
            file_exists( $image_realpath ) && @unlink( $image_realpath );

            if ( !empty( $error ) ) {
                $result = array(
                    'success' => false,
                    'error' => $error
                );
            } else {
                $result = array(
                    'success' => true,
                    'newUuid' => $imageId,
                    'uploadName' => $imageUrl
                );
            }

            header( "Content-Type: text/plain" );
            echo json_encode( $result );
        }
    }

    public function delete_image_by_ajax()
    {
        $uuid = Input::post( 'qquuid', '' )->required( '要删除的图片不能为空' )->sql();
        header( "Content-Type: text/plain" );

        if ( Filter::getStatus() === false ) {
            $result = array(
                'success' => false,
                'error' => Filter::getFailMessage()
            );
            echo json_encode( $result );
            exit;
        }
        $result = array( "success" => true, "uuid" => stripslashes( $uuid ) );
        echo json_encode( $result );
    }

    public function getRegion()
    {
        $callback = Input::get( 'callback', 'callback' )->string();

        $region_id = Input::get( 'id', 0 )->required( 'ID不能为空' )->int();
        if ( empty( $region_id ) ) {
            throw new ApiException( Filter::getFailMessage(), -1, $callback );
        }

        $region_model = new service_Region_base();
        $res = $region_model->getRegionListByPid( $region_id );
        $this->apiReturn( $res ); //ajax成功
    }

}
