<?php


namespace Yirius\Admin\route\restful;


use think\db\Query;
use Yirius\Admin\extend\ThinkerRestful;

class TeAdminRules extends ThinkerRestful
{
    protected $_UseTable = \Yirius\Admin\route\model\TeAdminRules::class;

    protected function indexEach($item)
    {
        // TODO: Implement indexEach() method.
    }

    /**
     * @title      indexQuery
     * @description
     * @createtime 2019/11/16 6:46 下午
     * @param Query $query
     * @return Query
     * @author     yangyuance
     */
    protected function indexQuery(Query $query)
    {
        // TODO: Implement indexQuery() method.
    }

}