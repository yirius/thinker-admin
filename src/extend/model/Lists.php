<?php


namespace Yirius\Admin\extend\model;


use Yirius\Admin\ThinkerAdmin;

class Lists extends BaseModel
{
    private $alias = "";
    private $fields = "*";
    private $page = 1;
    private $limit = 10;
    private $sort = 'id';
    private $order = 'DESC';

    /**
     * @var callable
     */
    private $eachClosure = null;

    private $where = [];
    private $with = [];
    /**
     * @var callable
     */
    private $parseQuery = null;

    public function __construct($model)
    {
        parent::__construct($model);

        $sort = input("param.sort", false);
        if(!empty($sort)) {
            $this->setSort($sort);
        }
        $order = input("param.order", false);
        if(!empty($order)) {
            $this->setOrder($order);
        }
        $page = input("param.page", false);
        if(!empty($page)) {
            $this->setPage($page);
        }
        $limit = input("param.limit", false);
        if(!empty($limit)) {
            $this->setLimit($limit);
        }
    }

    /**
     * @title      getResult
     * @description
     * @createtime 2020/5/27 10:46 下午
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author     yangyuance
     */
    public function getResult()
    {
        $queryObject = $this->getModel()
            ->field($this->fields)
            ->where($this->where);

        if(!empty($this->alias)){
            $queryObject = $queryObject->alias($this->alias)
                ->order($this->alias.".".$this->sort, $this->order);
        }else{
            $queryObject = $queryObject->order($this->sort, $this->order);
        }

        if(!empty($this->with)){
            $queryObject = $queryObject->with($this->with);
        }

        //before fetch array, make call
        if(is_callable($this->parseQuery)){
            call($this->parseQuery, [$queryObject]);
        }

        //some error with this Object, so colne one for use
        $count = (clone $queryObject)->count();

        $selected = $queryObject->page($this->page, $this->limit)->select();

        //if fetchSql
        if(is_string($selected)){
            return $selected;
        }else{
            //each collections
            if (is_callable($this->eachClosure)) {
                $selected->each($this->eachClosure);
            }
            return [
                'count' => $count,
                'data' => $selected->toArray()
            ];
        }
    }

    /**
     * @title      setAlias
     * @description
     * @createtime 2020/5/27 10:33 下午
     * @param $alias
     * @return $this
     * @author     yangyuance
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @title      setEachClosure
     * @description
     * @createtime 2020/5/27 10:40 下午
     * @param callable $eachClosure
     * @return $this
     * @author     yangyuance
     */
    public function setEachClosure(callable $eachClosure = null)
    {
        $this->eachClosure = $eachClosure;
        return $this;
    }

    /**
     * @title      setFields
     * @description
     * @createtime 2020/5/27 10:40 下午
     * @param $fields
     * @return $this
     * @author     yangyuance
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @title      setPage
     * @description
     * @createtime 2020/5/27 10:37 下午
     * @param $page
     * @return $this
     * @author     yangyuance
     */
    public function setPage($page)
    {
        $this->page = intval($page);
        return $this;
    }

    /**
     * @title      setLimit
     * @description
     * @createtime 2020/5/27 10:37 下午
     * @param $limit
     * @return $this
     * @author     yangyuance
     */
    public function setLimit($limit)
    {
        $this->limit = intval($limit);
        return $this;
    }

    /**
     * @title      setWith
     * @description
     * @createtime 2020/5/27 10:37 下午
     * @param $with
     * @return $this
     * @author     yangyuance
     */
    public function setWith($with)
    {
        $this->with = $with;
        return $this;
    }

    /**
     * @title      setWhere
     * @description
     * @createtime 2020/5/27 10:41 下午
     * @param array $params
     * @param null  $values
     * @return $this
     * @author     yangyuance
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
        $this->where = $where;

        return $this;
    }

    /**
     * @title      setSort
     * @description
     * @createtime 2020/5/27 10:37 下午
     * @param $sort
     * @return $this
     * @author     yangyuance
     */
    public function setSort($sort)
    {
        if (!in_array($sort, $this->modelFields)) {
            ThinkerAdmin::response()->msg("order field like 'id' not exsit in this table")->fail();
        }
        $this->sort = $sort;
        return $this;
    }

    /**
     * @title      setOrder
     * @description
     * @createtime 2020/5/27 10:37 下午
     * @param $order
     * @return $this
     * @author     yangyuance
     */
    public function setOrder($order)
    {
        if(!in_array(strtolower($order), ['asc', 'desc'])) {
            ThinkerAdmin::response()->msg("order is not asc/desc")->fail();
        }
        $this->order = $order;
        return $this;
    }

    /**
     * @title      setParseQuery
     * @description
     * @createtime 2020/5/27 10:46 下午
     * @param callable $parseQuery
     * @return $this
     * @author     yangyuance
     */
    public function setParseQuery(callable $parseQuery = null)
    {
        $this->parseQuery = $parseQuery;
        return $this;
    }
}