<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/21
 * Time: 下午10:18
 */

namespace Yirius\Admin\model\restful;


use think\Request;
use Yirius\Admin\model\AdminRestful;

class CmsProduct extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\CmsProduct
     */
    protected $restfulTable = \Yirius\Admin\model\table\CmsProduct::class;

    protected $tableEditMsg = "修改Cms内容成功";

    protected $tableSaveMsg = "新增Cms内容成功";

    protected $tableCanEditField = ['status'];

    /**
     * @title index
     * @description
     * @createtime 2019/3/21 下午10:18
     * @param Request $request
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
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
     * @createtime 2019/3/21 下午10:43
     * @param Request $request
     * @param array $updateWhere
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save(Request $request, $updateWhere = [])
    {
        $addData = $request->param();

        $validate = [[
            'title' => "require",
            'modelid' => "require|number",
        ], [
            'title.require' => "标题必须填写",
            'modelid.require' => "对应模型必须填写",
        ]];

        $fields = \Yirius\Admin\model\table\CmsProductAttr::where([
            ['columnid', '=', $addData['columnid']]
        ])->select();

        $jsonData = [];
        foreach($fields as $i => $v){
            $jsonData[$v['name']] = empty($addData[$v['name']]) ? '' : $addData[$v['name']];
            unset($addData[$v['name']]);
            if($v['is_must']){
                $validate[0][$i] = "require";
                $validate[1][$i] = $v['title'] . "必须填写";
            }
        }
        $addData['attrs'] = json_encode($jsonData);

        $this->defaultSave($addData, $validate, $updateWhere);
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
     * @createtime 2019/3/21 下午10:44
     * @param $id
     * @param Request $request
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function update($id, Request $request)
    {
        if ($request->param("__type") == "field") {
            $this->defaultUpdate($id, $request->param("field"), $request->param("value"));
        } else {
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