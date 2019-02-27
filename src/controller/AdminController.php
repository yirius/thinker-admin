<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午12:21
 */

namespace Yirius\Admin\controller;


use think\Controller;

class AdminController extends Controller
{
    /**
     * @var array
     */
    protected $token = [];

    /**
     * @var bool
     */
    protected $returnJsonError = false;

    /**
     * which rule type to check
     * @var int
     */
    protected $checkType = 1;

    protected function initialize()
    {
        //judge is there have access_token
        $access_token = $this->request->param('access_token');
        if(empty($access_token)){
            $this->sendError(lang("no access_token to access"));
        }
        //if success, save token info
        $this->token = \Yirius\Admin\Admin::jwt()->decode($access_token, function($err){
            $this->sendError(lang("no authority to access"));
        });

        //judge if member have permission to request
        $currentPath = $this->request->path();
        //if there no prev fix '/', make it
        if(substr($currentPath, 0, 1) != "/") $currentPath = "/" . $currentPath;
        //if check error, sendError
        if(!\Yirius\Admin\Admin::auth()->check($currentPath, $this->getToken('id'), $this->checkType)){
            $this->sendError(lang("do not have authority to access", ['url' => $currentPath]));
        }
    }

    /**
     * @title getToken
     * @description
     * @createtime 2019/2/26 下午3:11
     * @param null $field
     * @return array|bool|mixed
     */
    protected function getToken($field = null)
    {
        if(is_null($field)){
            return $this->token;
        }else{
            if(!isset($this->token[$field])){
                return false;
            }else{
                return $this->token[$field];
            }
        }
    }

    /**
     * @title sendError
     * @description
     * @createtime 2019/2/26 下午3:06
     * @param $msg
     * @param int $code
     */
    protected function sendError($msg, $code = 1001)
    {
        if($this->returnJsonError){
            \Yirius\Admin\Admin::tools()->jsonSend([], $code, $msg);
        }else{
            response(<<<HTML
<script>
layui.layer.alert("{$msg}", {title: "温馨提示"}, function(index){
    layer.close(index);
    layui.session.logout();
});
</script>
HTML
            )->send();
            exit;
        }
    }
}