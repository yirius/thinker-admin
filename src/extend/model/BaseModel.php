<?php


namespace Yirius\Admin\extend\model;


use think\facade\Cache;
use think\Model;
use Yirius\Admin\extend\ThinkerModel;
use Yirius\Admin\ThinkerAdmin;

abstract class BaseModel
{
    /**
     * @var Model
     */
    protected $model;
    protected $modelFields = [];

    public function __construct(Model $model)
    {
        $this->setModel($model);
    }

    /**
     * @title      setModel
     * @description
     * @createtime 2020/5/27 10:27 下午
     * @param $model
     * @return $this
     * @author     yangyuance
     */
    public function setModel($model)
    {
        $this->model = $model;
        $this->modelFields = Cache::get("table_" . $this->model->getName(), null);
        if(empty($this->modelFields)) {
            $this->modelFields = $this->model->getTableFields();
            Cache::tag("table_fields")->set("table_" . $this->model->getName(), $this->modelFields);
        }
        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    public abstract function getResult();
}