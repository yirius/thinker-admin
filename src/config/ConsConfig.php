<?php


namespace Yirius\Admin\config;


class ConsConfig
{
    /**
     * request请求头属性
     */
    public static $JWT_HEADER = "Access-Token";

    /**
     * JWT-account
     */
    public static $JWT_KEY = "id";

    /**
     * JWT-currentTimeMillis
     */
    public static $JWT_CURRENT_TIME = "curTime";

    /**
     * JWT-currentTimeMillis
     */
    public static $JWT_ACCESS_TYPE = "access_type";

    /**
     * 上传文件的path
     */
    public static $UPLOADS_PATH = "uploads/";

    /**
     * 允许上传的图片类型，根据需求自己添加（小写）
     */
    public static $UPLOAD_IMAGES_SUFFIX = ["jpg","jpeg","png","gif","do","bmp"];

    /**
     * 上传图片最大大小
     */
    public static $UPLOAD_IMAGES_MAXSIZE = 1024*1024*10;

    /**
     * 允许上传的图片类型，根据需求自己添加（小写）
     */
    public static $UPLOAD_FILES_SUFFIX = ["jpg","png","gif","jpeg","do","bmp","flv","swf","mkv","avi","rm","rmvb","mpeg","mpg","ogg","ogv","mov","wmv","mp4","webm","mp3","wav","mid","rar","zip","tar","gz","7z","bz2","cab","iso","doc","docx","xls","xlsx","ppt","pptx","pdf","txt","md","xml"];

    /**
     * 上传图片最大大小
     */
    public static $UPLOAD_FILES_MAXSIZE = 1024*1024*10;

    /**
     * 上传图片最大大小
     */
    public static $HTTPTRACE_MAXSIZE = 1000;

    /**
     * 默认的队列名称
     */
    public static $QUEUE_DEFAULT_EXCHANGE = "thinker";

    /**
     * 默认的队列名称
     */
    public static $QUEUE_DEFAULT_NAME = "thinker_queue";

    /**
     * 默认的订单队列名称
     */
    public static $QUEUE_ORDER_NAME = "order_queue";

    /**
     * 默认的订单队列重试最大次数
     */
    public static $QUEUE_RETRY_COUNT = 5;

    /**
     * 默认的订单队列重试延迟时间
     */
    public static $QUEUE_RETRY_DELAY = 60;
}