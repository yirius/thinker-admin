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
        $this->send(($this->restfulTable)::adminList()->setWith("cmsmodels,cmscolumns")->getResult());
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

        $adminSaveModel = ($this->restfulTable)::adminSave();

        $adminSaveModel->setValidate([
            'title' => "require",
            'modelid' => "require|number",
            'list_order' => "require"
        ], [
            'title.require' => "标题必须填写",
            'modelid.require' => "对应模型必须填写",
            'list_order.require' => "模型排序必须填写"
        ]);

        $adminSaveModel = $adminSaveModel->setAdd($addData);

        //新增
        if(empty($updateWhere)){
            $isAdd = $adminSaveModel->getResult();
            if($isAdd !== false){
                //新增成功
                //向其他参数表添加内容
                $fields = \Yirius\Admin\model\table\CmsModelsField::findFieldByCache(
                    $request->param("modelid"), true
                );
                $saveData = [];
                foreach($fields as $i => $v){
                    if($request->param($v)){
                        $saveData[$v] = $request->param($v);
                    }
                }
                if(!empty($saveData)){
                    $data = \Yirius\Admin\model\table\CmsModels::findIdByCache($request->param("modelid"));
                    $saveData['cmsid'] = $isAdd->id;
                    (new $data['table'])->save($saveData);
                }
            }else{
                //新增失败
                Admin::tools()->jsonSend([], 0, $adminSaveModel->getError());
            }
        }else{
            //修改记录,为空可能未修改主表，只修改了分表
            $isAdd = $adminSaveModel->setWhere($updateWhere)->getResult();
            //判断哪些内容需要修改
            $fields = \Yirius\Admin\model\table\CmsModelsField::findFieldByCache(
                $request->param("modelid"), true
            );
            $saveData = [];
            foreach($fields as $i => $v){
                if($request->param($v)){
                    $saveData[$v] = $request->param($v);
                }
            }
            if($isAdd !== false){
                //都有修改，直接改就行
                if(!empty($saveData)){
                    $data = \Yirius\Admin\model\table\CmsModels::findIdByCache($request->param("modelid"));
                    $saveData['cmsid'] = $isAdd->id;
                    (new $data['table'])->save($saveData, [
                        ['cmsid', '=', $isAdd->id]
                    ]);
                }
            }else{
                //修改失败，判断是否内容一致
                if($adminSaveModel->getError() == "未知错误，请您联系客服"){
                    //没修改主表，直接修改分表
                    $data = \Yirius\Admin\model\table\CmsModels::findIdByCache($request->param("modelid"));
                    $updateWhere[0][0] = "cmsid";
                    (new $data['table'])->save($saveData, $updateWhere);
                }else{
                    Admin::tools()->jsonSend([], 0, $adminSaveModel->getError());
                }
            }
        }
        //所有成功返回
        Admin::tools()->jsonSend($isAdd->toArray(), 1, (empty($where) ? $this->tableSaveMsg : $this->tableEditMsg));
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
        if($request->param("__type") == "field"){
            $this->defaultUpdate($id, $request->param("field"), $request->param("value"));
        }else{
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

        $this->defaultDelete($id, [1,2,3,4,5,6]);
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
        foreach($data as $i => $v){
            $deleteIds[] = $v['id'];
        }
        $this->defaultDelete($deleteIds, [1,2,3,4,5,6]);
    }
}