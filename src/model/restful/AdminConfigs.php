<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/26
 * Time: 下午2:29
 */

namespace Yirius\Admin\model\restful;


use think\facade\Cache;
use think\Request;
use Yirius\Admin\Admin;
use Yirius\Admin\model\AdminRestful;

class AdminConfigs extends AdminRestful
{
    /**
     * @title index
     * @description get table's list
     * @createtime 2019/2/26 下午4:09
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        // TODO: Implement index() method.
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
        foreach($request->param() as $i => $v){
            (new \Yirius\Admin\model\table\AdminConfigs)->save([
                'name' => $i,
                'value' => $v
            ], [
                ['name', '=', $i]
            ]);
        }
        Cache::rm("thinker_admin_configsvalue");
        Admin::tools()->jsonSend([], 1, "编辑网站参数成功");
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