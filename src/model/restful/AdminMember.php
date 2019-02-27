<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午3:59
 */

namespace Yirius\Admin\model\restful;


use Yirius\Admin\Admin;

class AdminMember extends AdminRestful
{
    /**
     * @title index
     * @description
     * @createtime 2019/2/26 下午4:01
     */
    public function index()
    {
        $result = \Yirius\Admin\model\table\AdminMember::adminList()->getResult();

        $this->send($result);
    }

    /**
     * @title save
     * @ajaxMethod POST
     * @description add a new line
     * @createtime 2019/2/26 下午4:10
     * @return mixed
     */
    public function save()
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
     * @return mixed
     */
    public function update($id)
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
     * @return mixed
     */
    public function deleteall()
    {
        // TODO: Implement deleteall() method.
    }
}