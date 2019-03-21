<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/13
 * Time: 下午5:12
 */

namespace Yirius\Admin\model\restful;


use think\Request;
use Yirius\Admin\Admin;
use Yirius\Admin\model\AdminRestful;

class Cms extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\Cms
     */
    protected $restfulTable = \Yirius\Admin\model\table\Cms::class;

    protected $tableEditMsg = "修改Cms内容成功";

    protected $tableSaveMsg = "新增Cms内容成功";

    protected $tableCanEditField = ['status'];

    /**
     * @title index
     * @description
     * @createtime 2019/3/13 下午5:19
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
                ->setWith("cmsmodels,cmscolumns")
                ->setWhere([
                    "columnid"
                ])
                ->getResult()
        );
    }

    /**
     * @title save
     * @description
     * @createtime 2019/3/13 下午6:54
     * @param Request $request
     * @param array $updateWhere
     * @return mixed|void
     * @throws \Exception
     */
    public function save(Request $request, $updateWhere = [])
    {
        $addData = $request->param();
        $addData['list_order'] = $request->param("list_order", 0);

        $validate = [[
            'title' => "require",
            'modelid' => "require|number",
        ], [
            'title.require' => "标题必须填写",
            'modelid.require' => "对应模型必须填写",
        ]];

        //判断其他参数是否是必填
        $fields = \Yirius\Admin\model\table\CmsModelsField::findFieldByCache(
            $request->param("modelid"), true
        );
        foreach($fields as $i => $v){
            if($v['is_must']){
                $validate[0][$i] = "require";
                $validate[1][$i] = $v['title'] . "必须填写";
            }
        }

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
     * @createtime 2019/3/13 下午5:55
     * @param $id
     * @param Request $request
     * @return mixed|void
     * @throws \Exception
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
     * @createtime 2019/3/13 下午5:58
     * @param $id
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($id)
    {
        $this->checkLoginPwd();

        $this->defaultDelete($id);
    }

    /**
     * @title deleteall
     * @description
     * @createtime 2019/3/13 下午5:58
     * @param Request $request
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteall(Request $request)
    {
        $this->checkLoginPwd();

        $data = json_decode($request->param("data"), true);
        $deleteIds = [];
        foreach ($data as $i => $v) {
            $deleteIds[] = $v['id'];
        }
        $this->defaultDelete($deleteIds);
    }
}