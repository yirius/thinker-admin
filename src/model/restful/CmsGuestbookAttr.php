<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/21
 * Time: 下午9:36
 */

namespace Yirius\Admin\model\restful;


use think\Request;
use Yirius\Admin\model\AdminRestful;

class CmsGuestbookAttr extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\Cms
     */
    protected $restfulTable = \Yirius\Admin\model\table\CmsGuestbookAttr::class;

    protected $tableEditMsg = "修改Cms内容成功";

    protected $tableSaveMsg = "新增Cms内容成功";

    protected $tableCanEditField = ['status'];

    /**
     * @title index
     * @description get table's list
     * @createtime 2019/2/26 下午4:09
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->send(
            ($this->restfulTable)::adminList()
                ->setWhere([
                    "columnid"
                ])
                ->getResult()
        );
    }

    /**
     * @title save
     * @description
     * @createtime 2019/3/21 下午9:58
     * @param Request $request
     * @param array $updateWhere
     * @return mixed|void
     * @throws \Exception
     */
    public function save(Request $request, $updateWhere = [])
    {
        $this->defaultSave($request->param(), [[
            'title' => "require",
            'name' => "require",
            'type' => "require"
        ], [
            'title.require' => "字段标题必须填写",
            'name.require' => "字段名称必须填写",
            'type.require' => "字段类型必须选择"
        ]], $updateWhere);
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
     * @createtime 2019/3/21 下午10:00
     * @param $id
     * @param Request $request
     * @return mixed|void
     * @throws \Exception
     */
    public function update($id, Request $request)
    {
        if($request->param("__type") == "field"){
            $this->defaultUpdate($id, $request->param("field"), $request->param('value'));
        }else{
            $this->save($request, [
                ['id', '=', $id]
            ]);
        }
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