<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/28
 * Time: 下午8:04
 */

namespace Yirius\Admin\model\model;


use Yirius\Admin\model\AdminModelBase;

class Select extends AdminModelBase
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
        if(empty($where)){
            return $this->model
                ->field($this->textName . " as text," . $this->valueName . " as value")
                ->select()
                ->toArray();
        }else{
            return $this->model
                ->field($this->textName . " as text," . $this->valueName . " as value")
                ->where($this->where)
                ->select()
                ->toArray();
        }
    }
}