<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/27
 * Time: 下午2:16
 */

namespace Yirius\Admin\model\restful;


use think\Request;
use Yirius\Admin\Admin;
use Yirius\Admin\model\AdminRestful;

class AdminRule extends AdminRestful
{
    /**
     * @title index
     * @description
     * @createtime 2019/2/28 上午11:46
     * @param Request $request
     * @return mixed|void
     */
    public function index(Request $request)
    {

        $allMenus = \Yirius\Admin\model\table\AdminRule::all()->toArray();

        $result = Admin::tools()->tree($allMenus, null, [
            'parentid' => "mid"
        ]);

        $this->send(count($allMenus), $this->getMenusTree($result));
    }

    /**
     * @title getMenusTree
     * @description
     * @createtime 2019/2/27 下午5:10
     * @param $trees
     * @param string $prev
     * @return array
     */
    protected function getMenusTree($trees, $prev = '')
    {
        $result = [];

        foreach($trees as $i => $tree){
            $tree['title'] = $prev . $tree['title'];
            if(!empty($tree['list'])){
                $list = $tree['list'];
                $tree['list'] = [];
                $result[] = $tree;
                $result = array_merge($result, $this->getMenusTree($list, empty($prev) ? "|--" : $prev . "--"));
            }else{
                $result[] = $tree;
            }
        }

        return $result;
    }

    /**
     * @title save
     * @description
     * @createtime 2019/2/28 上午11:46
     * @param Request $request
     * @return mixed|void
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
     * @createtime 2019/2/28 上午11:46
     * @param $id
     * @param Request $request
     * @return mixed|void
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
     * @createtime 2019/2/28 上午11:46
     * @param Request $request
     * @return mixed|void
     */
    public function deleteall(Request $request)
    {
        // TODO: Implement deleteall() method.
    }

}