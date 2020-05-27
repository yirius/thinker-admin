<?php


namespace Yirius\Admin\widgets;


use Yirius\Admin\ThinkerAdmin;

class ThinkerHttp
{
    /**
     * cURL的资源句柄
     * @type resource
     */
    protected $curl = null;

    /**
     * 返回信息出现错误
     * @type bool
     */
    protected $error = false;

    /**
     * 记录错误码,当错误码不为0,就是错误
     * @type int
     */
    protected $errorCode = 0;

    /**
     * 记录错误信息
     * @type null
     */
    protected $errorMessage = null;

    /**
     * 记录curl的错误
     * @type bool
     */
    protected $curlError = false;

    /**
     * curl错误码
     * @type int
     */
    protected $curlErrorCode = 0;

    /**
     * curl错误信息
     * @type null
     */
    protected $curlErrorMessage = null;

    /**
     * http返回信息
     * @type bool
     */
    protected $httpError = false;

    /**
     *  http返回信息是否错误
     * @type int
     */
    protected $httpStatusCode = 0;

    /**
     * http错误的发挥信息
     * @type null
     */
    protected $httpErrorMessage = null;

    /**
     * 发送的设置
     * @var array
     */
    protected $options = [];

    /**
     * 发送的头部参数
     * @var array
     */
    protected $headers = [];

    /**
     * @type string
     */
    private $jsonPattern = '/^(?:application|text)\/(?:[a-z]+(?:[\.-][0-9a-z]+){0,}[\+\.]|x-)?json(?:-[a-z]+)?/i';

    /**
     * @type string
     */
    private $xmlPattern = '~^(?:text/|application/(?:atom\+|rss\+)?)xml~i';

    /**
     * 回调的参数
     * @var string
     */
    protected $rawResponse = '';

    /**
     * @var array
     */
    protected $events = [
        'afterDownload' => null
    ];

    /**
     * @title      _init
     * @description 初始化参数
     * @createtime 2019/11/27 5:13 下午
     * @author     yangyuance
     */
    public function _init()
    {
        if (!extension_loaded('curl')) {
            ThinkerAdmin::response()->msg('cURL library is not loaded')->fail();
        }

        //初始化curl
        $this->curl = curl_init();
        //首先设置可以returntransfer
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        //直接关闭SSL校验
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, false);
    }

    /**
     * @title 设置参数
     * @description 设置参数项
     * @createtime: 2018/2/24 14:45
     * @param $key
     * @param $value
     * @return $this
     */
    public function setOpt($key, $value)
    {
        $this->options[$key] = $value;
        curl_setopt($this->curl, $key, $value);
        return $this;
    }

    /**
     * @title      setUrl
     * @description
     * @createtime 2019/11/27 5:01 下午
     * @param $url
     * @return ThinkerHttp
     * @author     yangyuance
     */
    public function setUrl($url)
    {
        return $this->setOpt(CURLOPT_URL, $url);
    }

    /**
     * @title      setHeader
     * @description 设置数据发送的头部
     * @createtime 2019/11/27 5:01 下午
     * @param      $key
     * @param null $value
     * @return $this
     * @author     yangyuance
     */
    public function setHeader($key, $value = null)
    {
        //首先记录头部
        if(is_array($this->headers)){
            $this->headers = array_merge($this->headers, $this->headers);
        }else{
            $this->headers[$key] = $value;
        }
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
        unset($headers);

        return $this;
    }

    /**
     * @title 取消设置header头部
     * @description 取消设置header头部
     * @createtime: 2018/2/24 15:02
     * @param $key
     * @return $this
     */
    public function unsetHeader($key)
    {
        if(isset($this->headers[$key])){
            unset($this->headers[$key]);

            $headers = [];
            foreach ($this->headers as $key => $value) {
                $headers[] = $key . ': ' . $value;
            }
            $this->setOpt(CURLOPT_HTTPHEADER, $headers);
            unset($headers);
        }
        return $this;
    }

    /**
     * @title      get
     * @description 用get方法获取
     * @createtime 2019/11/27 5:07 下午
     * @param       $url
     * @param array $data
     * @return $this
     * @author     yangyuance
     */
    public function get($url, $data = null)
    {
        if(is_array($data)){
            $this->setUrl($url . "?" . http_build_query($data));
        }else{
            $this->setUrl($url);
        }
        $this
            ->setOpt(CURLOPT_CUSTOMREQUEST, 'GET')
            ->setOpt(CURLOPT_HTTPGET, true)
            ->exec();
        return $this;
    }

    /**
     * @title 创建post的数据
     * @description 创建post的数据
     * @createtime: 2018/2/24 14:58
     * @param $data
     * @return array|string
     */
    public function buildPostData($data)
    {
        $binary_data = false;
        if (is_array($data)) {
            // Return JSON-encoded string when the request's content-type is JSON.
            if (isset($this->headers['Content-Type']) && preg_match($this->jsonPattern, $this->headers['Content-Type'])) {
                $json_str = json_encode($data);
                if (!($json_str === false)) {
                    $data = $json_str;
                }
            } else {
                foreach ($data as $key => $value) {
                    //如果使用@开头,并且是一个字符串,同时存在这个文件
                    if (is_string($value) &&
                        strpos($value, '@') === 0 &&
                        is_file(substr($value, 1))
                    ) {
                        $binary_data = true;
                        if (class_exists('CURLFile')) {
                            $data[$key] = new \CURLFile(substr($value, 1));
                        }
                    } elseif ($value instanceof \CURLFile) {
                        $binary_data = true;
                    }
                }
            }
        }
        if (!$binary_data && (is_array($data) || is_object($data))) {
            $data = http_build_query($data);
        }
        return $data;
    }

    /**
     * @title      post
     * @description
     * @createtime 2019/11/27 5:08 下午
     * @param       $url
     * @param array $data
     * @return $this
     * @author     yangyuance
     */
    public function post($url, $data = []){
        //首先设置类型是POST
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        //设置提交网址
        $this->setUrl($url);
        //设置提交参数
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->buildPostData($data));
        //执行
        $this->exec();
        return $this;
    }

    /**
     * @title      download
     * @description 文件下载
     * @createtime 2019/11/27 5:10 下午
     * @param        $url
     * @param        $filename
     * @param string $path
     * @param string $mode
     * @return $this
     * @author     yangyuance
     */
    public function download($url, $filename, $path = 'public/uploads/', $mode = "a")
    {
        $this->get($url)->getResponse(function($raw) use ($filename,$path,$mode){
            $this->rawResponse = env("root_path") . $path . $filename;
            $resource = fopen($this->rawResponse, $mode);
            fwrite($resource, $raw);
            fclose($resource);
        });
        return $this;
    }

    /**
     * @title 真正的执行方法
     * @description
     * @createtime: 2018/2/24 15:34
     */
    protected function exec(){
        $this->rawResponse = curl_exec($this->curl);
        $this->curlErrorCode = curl_errno($this->curl);
        $this->curlErrorMessage = curl_error($this->curl);

        $this->curlError = !($this->curlErrorCode === 0);

        if ($this->curlError && function_exists('curl_strerror')) {
            $this->curlErrorMessage =
                curl_strerror($this->curlErrorCode) . (
                empty($this->curlErrorMessage) ? '' : ': ' . $this->curlErrorMessage
                );
        }

        $this->httpStatusCode = $this->getInfo(CURLINFO_HTTP_CODE);
        $this->httpError = in_array(floor($this->httpStatusCode / 100), array(4, 5));

        return $this;
    }

    /**
     * @title 获取到错误信息
     * @description 如果存在错误,那就直接返回错误语句,如果不存在,就返回false,所以应该用===false来判断
     * @createtime: 2018/2/24 14:49
     * @return bool|string
     */
    public function getError(){
        if($this->curlErrorCode){
            return $this->curlErrorMessage;
        }else{
            return false;
        }
    }

    /**
     * @title 参数回调
     * @description 参数回调
     * @createtime: 2018/2/24 15:17
     * @param $func
     * @param $opts
     * @return $this
     */
    public function call($func, $opts = null){
        if(is_callable($func)){
            call($func, [$this, $opts]);
        }
        return $this;
    }

    /**
     * @title      getResponse
     * @description
     * @createtime 2019/11/27 5:21 下午
     * @param callable|null $callable
     * @return mixed|string
     * @author     yangyuance
     */
    public function getResponse(callable $callable = null){
        if(is_callable($callable)){
            return call($callable, [$this->rawResponse, $this]);
        }
        return $this->rawResponse;
    }

    /**
     * @title xml_decode
     * @description xml_decode
     * @createtime: 2018/2/24 16:00
     * @param $xml
     * @return mixed
     */
    public function xml_decode($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /**
     * @title 获取到信息参数
     * @description 利用指定信息获取到参数
     * @createtime: 2018/2/24 15:20
     * @param null $opt
     * @return mixed
     */
    public function getInfo($opt = null)
    {
        $args = array();
        $args[] = $this->curl;
        if (func_num_args()) {
            $args[] = $opt;
        }
        return call_user_func_array('curl_getinfo', $args);
    }
}