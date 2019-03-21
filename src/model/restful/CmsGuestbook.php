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

class CmsGuestbook extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\CmsGuestbook
     */
    protected $restfulTable = \Yirius\Admin\model\table\CmsGuestbook::class;

    protected $tableEditMsg = "修改Cms内容成功";

    protected $tableSaveMsg = "新增Cms内容成功";

    protected $tableCanEditField = ['status'];

    /**
     * @title index
     * @description
     * @createtime 2019/3/21 下午10:08
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
                ->getResult(function($item){
                    $content = json_decode($item->content, true);
                    foreach($content as $i => $v){
                        $item->$i = htmlspecialchars($v);
                    }
                })
        );
    }

    /**
     * @title save
     * @description
     * @createtime 2019/3/3 下午10:40
     * @param Request $request
     * @param array $updateWhere
     * @return mixed
     */
    public function save(Request $request, $updateWhere = [])
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
     * @createtime 2019/3/21 下午10:14
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
     * @createtime 2019/3/21 下午10:14
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