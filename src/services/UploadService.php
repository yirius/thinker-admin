<?php
/**
 * User: Yirius
 * Date: 2018/7/13
 * Time: 17:28
 */

namespace Yirius\Admin\services;


use think\File;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\ThinkerAdmin;

class UploadService
{
    /**
     * error string
     * @var null
     */
    protected $uploadError = null;

    /**
     * @title upload
     * @description
     * @createtime 2019/3/3 下午7:42
     * @param bool $isImage
     * @return array
     */
    public function upload($isImage = true)
    {
        $result = [];

        /**
         * 循环计算一下
         */
        foreach ($_FILES as $key => $temp) {
            //上传
            $upInfo = $this->_upload($temp, $isImage);

            if ($upInfo === false) {
                ThinkerAdmin::response()->msg($this->uploadError)->fail();
            } else {
                $result[$key] = $upInfo;
            }
        }

        return $result;
    }

    /**
     * @title getFilePath
     * @description
     * @createtime 2019/2/25 下午3:44
     * @param $file
     * @return array
     */
    public function getFilePath($file)
    {
        $ext = pathinfo($file['name'])['extension'];

        $filenameMD5 = md5_file($file['tmp_name']);

        $fileName = substr($filenameMD5, 2);
        $dirPrev = substr($filenameMD5, 0, 2);

        $dirPath = env("root_path") . '/public/' . ConsConfig::$UPLOADS_PATH . $dirPrev . "/";

        if(!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        return [
            'dir' => $dirPrev,
            'ext' => $ext,
            'name' => $file['name'],
            'size' => $file['size'],
            'filename' => $fileName,
            'allpath' => $dirPath . "/" . $fileName . "." . $ext,
            'path' => '/' . ConsConfig::$UPLOADS_PATH . $dirPrev . "/" . $fileName . "." . $ext
        ];
    }

    /**
     * @title _upload
     * @description
     * @createtime 2019/2/25 下午3:54
     * @param $file
     * @param bool $isImage
     * @return array|bool|string
     */
    protected function _upload($file, $isImage = true)
    {
        $fileConfig = $this->getFilePath($file);

        if(file_exists($fileConfig['allpath'])) {
            return $fileConfig['path'];
        }

        $file = (new File($file['tmp_name']))->setUploadInfo($file);
        $info = $file
            ->validate($isImage ? [
                'size' => ConsConfig::$UPLOAD_IMAGES_MAXSIZE,
                'ext' => join(",", ConsConfig::$UPLOAD_IMAGES_SUFFIX)
            ] : [
                'size' => ConsConfig::$UPLOAD_FILES_MAXSIZE,
                'ext' => join(",", ConsConfig::$UPLOAD_FILES_SUFFIX)
            ])
            ->rule("md5")
            ->move(env('root_path') . "/public/" . ConsConfig::$UPLOADS_PATH);

        if ($info) {
            return $fileConfig['path'];
        } else {
            $this->uploadError = $file->getError();
            return false;
        }
    }
}
