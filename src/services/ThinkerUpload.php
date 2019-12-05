<?php
/**
 * User: Yirius
 * Date: 2018/7/13
 * Time: 17:28
 */

namespace Yirius\Admin\services;


use think\File;
use Yirius\Admin\ThinkerAdmin;

class ThinkerUpload
{
    /**
     * @var array
     */
    protected $images = [
        'water' => false,
        'validate' => [
            'size' => 1024 * 1024 * 5,
            'ext' => "jpg,png,gif,jpeg,do,bmp"
        ]
    ];

    /**
     * @var array
     */
    protected $files = [
        'size' => 1024 * 1024 * 5,
        'ext' => "png,jpg,jpeg,gif,bmp,flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid,rar,zip,tar,gz,7z,bz2,cab,iso,doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md,xml"
    ];

    /**
     * error string
     * @var null
     */
    protected $uploadError = null;

    /**
     * @title upload
     * @description
     * @createtime 2019/3/3 下午7:42
     * @param bool $returnArray
     * @param bool $isImage
     * @return array
     */
    public function upload($returnArray = false, $isImage = true)
    {
        $result = [];
        if ($config = config("thinkeradmin.upload." . ($isImage ? 'images' : 'files'))) {
            if($isImage){
                $this->images = array_merge($this->images, $config);
            }else{
                $this->files = array_merge($this->files, $config);
            }
        }
        /**
         * 循环计算一下
         */
        foreach ($_FILES as $key => $temp) {
            //check file is empty
            $isEmpty = $this->checkFileEmpty($temp);
            //empty
            if ($isEmpty === false) {
                $upInfo = $this->_upload($temp, ($isImage ? $this->images['validate'] : $this->files), $isImage);
                if ($upInfo === false) {
                    \Yirius\Admin\Admin::tools()->jsonSend([], 0, $this->uploadError);
                } else {
                    $result[$key] = $upInfo;
                }
            } else {
                $result[$key] = $isEmpty;
            }
        }
        if($returnArray){
            return $result;
        }else{
            //return json
            ThinkerAdmin::Send()->json($result);
        }
    }

    /**
     * @title getFilePath
     * @description
     * @createtime 2019/2/25 下午3:44
     * @param $file
     * @return array
     */
    protected function getFilePath($file)
    {
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
    protected function checkFileEmpty($fileTemp)
    {
        if (!empty(pathinfo($fileTemp['name'])['extension'])) {
            $file = $this->getFilePath($fileTemp);
            //如果相同文件存在,不在二次上传,直接返回路径
            if (file_exists($file['allpath'])) {
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
    protected function _water($path)
    {
        $image = \think\Image::open($path);
        $thumbName = null;
        $water = $this->images['water'];
        if (!is_array($water)) {
            $water = [$water];
        }
        $image
            ->water($water[0], !isset($water[1]) ? 3 : $water[1], !isset($water[2]) ? 20 : $water[2])
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
    protected function _upload($file, $validate, $isImage = true)
    {
        $fileConfig = $this->getFilePath($file);

        $file = (new File($file['tmp_name']))->setUploadInfo($file);
        $info = $file
            ->validate($validate)
            ->rule("md5")
            ->move(env('root_path') . "/public/uploads/");

        if ($info) {
            if ($isImage && $this->images['water'] !== false) {
                $this->_water($fileConfig['allpath']);
            }
            return $fileConfig['path'];
        } else {
            $this->uploadError = $file->getError();
            return false;
        }
    }
}
