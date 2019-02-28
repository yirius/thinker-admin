<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午3:59
 */

namespace Yirius\Admin\model;

use think\Request;
use Yirius\Admin\controller\AdminController;

abstract class AdminRestful extends AdminController
{
    protected $returnJsonError = true;

    /**
     * @title index
     * @description get table's list
     * @createtime 2019/2/26 下午4:09
     * @param Request $request
     * @return mixed
     */
    public abstract function index(Request $request);

    /**
     * @title save
     * @description add a new line
     * @createtime 2019/2/26 下午4:10
     * @param Request $request
     * @return mixed
     */
    public abstract function save(Request $request);

    /**
     * @title read
     * @description get a line use id
     * @createtime 2019/2/26 下午4:10
     * @param $id
     * @return mixed
     */
    public abstract function read($id);

    /**
     * @title update
     * @description
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public abstract function update($id, Request $request);

    /**
     * @title delete
     * @description
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @return mixed
     */
    public abstract function delete($id);

    /**
     * @title deleteall
     * @description
     * @createtime 2019/2/27 上午1:47
     * @param Request $request
     * @return mixed
     */
    public abstract function deleteall(Request $request);

    /**
     * @title send
     * @description
     * @createtime 2019/2/26 下午4:15
     * @param $count
     * @param $data
     */
    public function send($count, array $data = null)
    {
        //judge if data is AdminModel
        if(is_array($count)){
            $data = $count['result'];
            $count = $count['count'];
        }
        //direct send
        response([
            'code' => 1,
            'count' => $count,
            'data' => $data,
            'msg' => "success"
        ], 200, [], "json")->send();

        exit;
    }
}