<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午3:59
 */

namespace Yirius\Admin\model\restful;

use think\Request;
use Yirius\Admin\model\AdminRestful;

class AdminMember extends AdminRestful
{
    /**
     * @title index
     * @description
     * @createtime 2019/2/28 上午11:47
     * @param Request $request
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $result = \Yirius\Admin\model\table\AdminMember::adminList()->getResult();

        $this->send($result);
    }

    /**
     * @title save
     * @description add a new line
     * @createtime 2019/2/26 下午4:10
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request)
    {
        // TODO: Implement save() method.
    }

    /**
     * @title read
     * @description get a line use id
     * @createtime 2019/2/26 下午4:10
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        // TODO: Implement read() method.
    }

    /**
     * @title update
     * @description
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        // TODO: Implement update() method.
    }

    /**
     * @title delete
     * @description
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @title deleteall
     * @description
     * @createtime 2019/2/27 上午1:47
     * @param Request $request
     * @return mixed
     */
    public function deleteall(Request $request)
    {
        // TODO: Implement deleteall() method.
    }
}