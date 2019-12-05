<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/20
 * Time: 下午5:24
 */

namespace Yirius\Admin\extend\model;

class Lists extends BaseModel
{
    /**
     * @var int
     */
    private $paramPage = 1;

    /**
     * limit, default 10
     * @var int
     */
    private $paramLimit = 10;

    /**
     * @var string
     */
    private $paramOrder = '';

    /**
     * @var array
     */
    private $paramWhere = [];

    /**
     * @var array
     */
    private $paramWith = [];

    /**
     * @var array
     */
    private $paramJoin = [];

    /**
     * @var string
     */
    private $paramField = "*";

    /**
     * @var string
     */
    private $paramAlias = "";

    /**
     * @title afterSetModel
     * @description
     * @createtime 2019/3/10 下午4:47
     * @throws \Exception
     */
    protected function afterSetModel()
    {
        //inject order info
        $this->setOrder(input('param.sort', 'id'), input('param.order', 'desc'));

        //inject page params
        $this->setPage(input('param.page', 1), input('param.limit', 10));
    }

    /**
     * @title setOrder
     * @description
     * @createtime 2019/2/27 下午2:21
     * @param $field
     * @param null $order
     * @return $this
     * @throws \Exception
     */
    public function setOrder($field, $order = null)
    {
        if (is_null($order)) {
            $this->checkOrderInject($field);

            $this->paramOrder = $field;
        } else {
            $this->checkOrderInject($field . " " . $order);

            $this->paramOrder = $field . " " . $order;
        }
        return $this;
    }

    /**
     * @title checkOrderInject
     * @description
     * @createtime 2019/2/27 下午2:21
     * @param $order
     * @throws \Exception
     */
    protected function checkOrderInject($order)
    {
        //judge 'id desc,name asc'
        if (strpos($order, ",")) {
            foreach (explode(",", $order) as $i => $v) {
                $this->checkOrderInject($v);
            }
        } else {
            //judge 'id desc'
            if (strpos($order, " ")) {
                $orderExploded = explode(" ", $order);
                if(strpos($orderExploded[0], ".") === false){
                    if (!in_array($orderExploded[0], $this->modelFields)) {
                        throw new \Exception("order field like 'id' not exsit in this table");
                    }
                }else{
                    if (!in_array(explode(".", $orderExploded[0])[1], $this->modelFields)) {
                        throw new \Exception("order field like 'id' not exsit in this table");
                    }
                }
                if (!in_array($orderExploded[1], ['asc', 'desc'])) {
                    throw new \Exception("order's order is not asc/desc");
                }
            } else {
                throw new \Exception("order info is not full");
            }
        }
    }

    /**
     * @title setWhere
     * @description
     * @createtime 2019/2/20 下午7:08
     * @param array $params
     * @param null $values
     * @return $this
     */
    public function setWhere(array $params, $values = null)
    {
        if(is_null($values) || !is_array($values)){
            $values = input('param.');
        }
        $where = [];
        foreach ($params as $i => $v) {
            if(!is_numeric($i)){
                $paramName = $i;
            }else{
                if(is_array($v)){
                    $paramName = $v[0];
                }else{
                    $paramName = $v;
                }
            }
            //judge where param is exsit and value not eq ''
            if (isset($values[$paramName]) && $values[$paramName] != "") {
                if (is_array($v)) {
                    //[['id', 'like', '%name%']]
                    $where[] = [$v[0], $v[1], str_replace("_var", addslashes($values[$paramName]), $v[2])];
                }else if($v instanceof \Closure){
                    //['createtime' => function($value){return ['id', 'name', 'value'];}]
                    $where[] = $v(addslashes($values[$paramName]));
                }else{
                    //id
                    $where[] = [$v, "=", addslashes($values[$paramName])];
                }
            }
        }
        $this->paramWhere = $where;

        return $this;
    }

    /**
     * @title setPage
     * @description set model page params
     * @createtime 2019/2/20 下午6:37
     * @param int $page
     * @param int $limit
     * @return $this
     */
    public function setPage($page = 1, $limit = 10)
    {
        $this->paramLimit = intval($limit);
        $this->paramPage = intval($page);
        return $this;
    }

    /**
     * @title setWith
     * @description set with table
     * @createtime 2019/2/20 下午7:29
     * @param $with
     * @return $this
     */
    public function setWith($with)
    {
        $this->paramWith = $with;
        return $this;
    }

    /**
     * @title setJoin
     * @description
     * @createtime 2019/3/1 上午11:49
     * @param array $join
     * @return $this
     */
    public function setJoin(array $join)
    {
        $this->paramJoin = $join;
        return $this;
    }

    /**
     * @title setField
     * @description
     * @createtime 2019/2/28 上午11:49
     * @param $field
     * @return $this
     */
    public function setField($field)
    {
        $this->paramField = $field;
        return $this;
    }

    /**
     * @title setField
     * @description
     * @createtime 2019/2/28 上午11:49
     * @param $field
     * @return $this
     */
    public function setAlias($field)
    {
        $this->paramAlias = $field;
        return $this;
    }

    /**
     * @title getResult
     * @description
     * @createtime 2019/2/20 下午7:30
     * @param callable|null $eachFuncs
     * @param \Closure|null $beforeFetch
     * @return string|array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getResult(callable $eachFuncs = null, \Closure $beforeFetch = null)
    {
        $queryObject = $this->getModel()
            ->field($this->paramField)
            ->where($this->paramWhere);

        if(!empty($this->paramAlias)){
            $queryObject = $queryObject->alias($this->paramAlias)->order($this->paramAlias.".".$this->paramOrder);
        }else{
            $queryObject = $queryObject->order($this->paramOrder);
        }

        if(!empty($this->paramWith)){
            $queryObject = $queryObject->with($this->paramWith);
        }

        if(!empty($this->paramJoin)){
            $queryObject = $queryObject->join($this->paramJoin);
        }

        //before fetch array, make call
        if(!is_null($beforeFetch)){
            $queryObject = $beforeFetch($queryObject);
        }

        //some error with this Object, so colne one for use
        $count = (clone $queryObject)->count();

        $selected = $queryObject->page($this->paramPage, $this->paramLimit)->select();

        //if fetchSql
        if(is_string($selected)){
            return $selected;
        }else{
            //each collections
            if (!is_null($eachFuncs)) {
                $selected->each($eachFuncs);
            }

            return [
                'count' => $count,
                'data' => $selected->toArray()
            ];
        }
    }
}