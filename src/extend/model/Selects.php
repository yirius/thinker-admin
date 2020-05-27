<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/28
 * Time: 下午8:04
 */

namespace Yirius\Admin\extend\model;


class Selects extends BaseModel
{
    /**
     * @var string
     */
    protected $textName = "title";

    /**
     * @var string
     */
    protected $valueName = "id";

    /**
     * @var null
     */
    protected $where = null;

    /**
     * @var callable
     */
    private $parseQuery = null;

    /**
     * @title setTextName
     * @description
     * @createtime 2019/2/28 下午8:08
     * @param $textName
     * @return $this
     */
    public function setTextName($textName)
    {
        $this->textName = $textName;

        return $this;
    }

    /**
     * @title setValueName
     * @description
     * @createtime 2019/2/28 下午8:08
     * @param $valueName
     * @return $this
     */
    public function setValueName($valueName)
    {
        $this->valueName = $valueName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTextName()
    {
        return $this->textName;
    }

    /**
     * @return string
     */
    public function getValueName()
    {
        return $this->valueName;
    }

    /**
     * @title setWhere
     * @description
     * @createtime 2019/2/28 下午8:10
     * @param $where
     * @return $this
     */
    public function setWhere($where)
    {
        $this->where = $where;

        return $this;
    }

    /**
     * @return null
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @title      setParseQuery
     * @description
     * @createtime 2020/5/27 10:46 下午
     * @param callable $parseQuery
     * @return $this
     * @author     yangyuance
     */
    public function setParseQuery(callable $parseQuery)
    {
        $this->parseQuery = $parseQuery;
        return $this;
    }

    /**
     * @title getResult
     * @description
     * @createtime 2019/2/28 下午8:15
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getResult()
    {
        $queryObject = $this->model->field($this->textName . " as text," . $this->valueName . " as value");

        if(!empty($this->where)){
            $queryObject = $queryObject->where($this->where);
        }

        //before fetch array, make call
        if(is_callable($this->parseQuery)){
            call($this->parseQuery, [$queryObject]);
        }

        return $queryObject->select()->toArray();
    }
}