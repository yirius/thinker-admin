<?php
/**
 * User: Yirius
 * Date: 2018/7/13
 * Time: 17:28
 */

namespace Yirius\Admin\controller;


use think\File;

class Upload
{
    /**
     * 给ueditor返回的参数
     * @type array
     */
    protected $config = [
        'imageActionName' => 'uploadimage',
        "imageFieldName" => 'upfile',
        "imageMaxSize" => 2048000,
        "imageAllowFiles" => ['.png', '.jpg', '.jpeg', '.gif', '.bmp'],
        "imageCompressEnable" => 1,
        "imageCompressBorder" => 1600,
        "imageInsertAlign" => "none",
        "imageUrlPrefix" => '',
        //涂鸦相关
        "scrawlActionName" => 'uploadscrawl',
        "scrawlFieldName" => 'upfile',
        "scrawlMaxSize" => 2048000,
        "scrawlUrlPrefix" => '',
        "scrawlInsertAlign" => "none",
        //视频相关
        "videoActionName" => 'uploadvideo',
        "videoFieldName" => 'upfile',
        "videoPathFormat" => '/ueditor/php/upload/video/{yyyy}{mm}{dd}/{time}{rand:6}',
        "videoUrlPrefix" => '',
        "videoMaxSize" => 102400000,
        "videoAllowFiles" => [".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg", ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid"],
        //上传文件
        "fileActionName" => 'uploadfile',
        "fileFieldName" => 'upfile',
        "filePathFormat" => '/ueditor/php/upload/file/{yyyy}{mm}{dd}/{time}{rand:6}',
        "fileUrlPrefix" => '',
        "fileMaxSize" => 51200000,
        "fileAllowFiles" => [".png", ".jpg", ".jpeg", ".gif", ".bmp", ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg", ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid", ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso", ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"]
    ];

    protected $images = [
        'water' => false,
        'validate' => [
            'size' => 1024*1024,
            'ext' => "jpg,png,gif,jpeg,do,bmp"
        ]
    ];

    protected $files = [
        'size' => 1024*1024,
        'ext' => "png,jpg,jpeg,gif,bmp,flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid,rar,zip,tar,gz,7z,bz2,cab,iso,doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md,xml"
    ];

    protected $imageError = null;

    protected $isUeditor = false;

    public function ueditor($action)
    {
        $this->isUeditor = true;
        $response = null;
        switch($action){
            case 'config':
                $response = Response::create($this->config, 'json');
                break;
                /* 上传图片 */
            case 'uploadimage':
                $response = $this->images();
                break;
            /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $response = $this->uploads();
                break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
                $response = $this->imagesbase64();
                break;
            /* 列出图片 */
            case 'listimage':
                $result = include("action_list.php");
                break;
            /* 列出文件 */
//            case 'listfile':
//                $result = include("action_list.php");
//                break;
//
//            /* 抓取远程文件 */
//            case 'catchimage':
//                $result = include("action_crawler.php");
//                break;

            default:
                $response = "非法行为";
                break;
        }
        return $response;
    }

    /**
     * @title 图片上传接口
     * @description 图片上传接口
     * @createtime: 2018/7/13 18:04
     * @return \think\response
     */
    public function images(){
        $result = [];
        if(config("thinkeradmin.upload.images")){
            $this->images = array_merge($this->images, config("thinkeradmin.upload.images"));
        }
        /**
         * 循环计算一下
         */
        foreach($_FILES as $key => $temp){
            //check file is empty
            $isEmpty = $this->checkFileEmpty($temp);
            //empty
            if($isEmpty === false){
                $imageInfo = $this->_upload($temp, $this->images['validate']);
                if($imageInfo === false){
                    \Yirius\Admin\Admin::tools()->jsonSend([], 0, $this->imageError);
                }else{
                    $result[$key] = $imageInfo;
                }
            }else{
                $result[$key] = $isEmpty;
            }
        }
        //return json
        \Yirius\Admin\Admin::tools()->jsonSend($result);
    }

    /**
     * @title uploads
     * @description
     * @createtime 2019/2/25 下午4:41
     * @return mixed
     */
    public function upload(){
        $result = [];
        if(config("thinkeradmin.upload.files")){
            $this->files = array_merge($this->files, config("thinkeradmin.upload.files"));
        }
        /**
         * 循环计算一下
         */
        foreach($_FILES as $key => $temp){
            //check file is empty
            $isEmpty = $this->checkFileEmpty($temp);
            //empty
            if($isEmpty === false){
                $imageInfo = $this->_upload($temp, $this->files['validate'], false);
                if($imageInfo === false){
                    \Yirius\Admin\Admin::tools()->jsonSend([], 0, $this->imageError);
                }else{
                    $result[$key] = $imageInfo;
                }
            }else{
                $result[$key] = $isEmpty;
            }
        }
        //return json
        \Yirius\Admin\Admin::tools()->jsonSend($result);
    }

    /**
     * @title getFilePath
     * @description
     * @createtime 2019/2/25 下午3:44
     * @param $file
     * @return array
     */
    protected function getFilePath($file){
        $ext = pathinfo($file['name'])['extension'];

        $filenameMD5 = md5_file($file['tmp_name']);

        $fileName = substr($filenameMD5, 2);
        $dirPrev = substr($filenameMD5, 0, 2);

        return [
            'dir' => $dirPrev,
            'ext' => $ext,
            'name' => $file['name'],
            'size' => $file['size'],
            'filename' => $fileName,
            'allpath' => env("root_path") . '/public/uploads/' . $dirPrev . "/" . $fileName . "." . $ext,
            'path' => '/uploads/' . $dirPrev . "/" . $fileName . "." . $ext
        ];
    }

    /**
     * @title checkFileEmpty
     * @description 检查指定文件的md5在服务器是否存在
     * @createtime 2019/2/25 下午3:47
     * @param $fileTemp
     * @return array|bool|mixed
     */
    protected function checkFileEmpty($fileTemp){
        if(!empty(pathinfo($fileTemp['name'])['extension'])){
            $file = $this->getFilePath($fileTemp);
            //如果相同文件存在,不在二次上传,直接返回路径
            if(file_exists($file['allpath'])){
                return $file['path'];
            }
        }
        return false;
    }

    /**
     * @title _water
     * @description set image water
     * @createtime 2019/2/25 下午4:41
     * @param $path
     */
    protected function _water($path){
        $image = \think\Image::open($path);
        $thumbName = null;
        $water = $this->images['water'];
        if(!is_array($water)){
            $water = [$water];
        }
        $image
            ->water($water[0], !isset($water[1])?3:$water[1], !isset($water[2])?20:$water[2])
            ->save($path);
    }

    /**
     * @title _upload
     * @description
     * @createtime 2019/2/25 下午3:54
     * @param $file
     * @param $validate
     * @param bool $isImage
     * @return array|bool|string
     */
    protected function _upload($file, $validate, $isImage = true){
        $fileConfig = $this->getFilePath($file);

        $file = (new File($file['tmp_name']))->setUploadInfo($file);
        $info = $file
            ->validate($validate)
            ->rule("md5")
            ->move(env('root_path') . "/public/uploads/");

        if($info){
            if($isImage && $this->images['water'] !== false){
                $this->_water($fileConfig['allpath']);
            }
            return $fileConfig['path'];
        }else{
            $this->imageError = $file->getError();
            return false;
        }
    }
}
