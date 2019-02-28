<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/20
 * Time: 下午11:47
 */

namespace Yirius\Admin\model;


use think\Model;

abstract class AdminModelBase
{

    /**
     * @var Model
     */
    protected $model;

    /**
     * model has fields
     * @var array
     */
    protected $modelFields = [];

    /**
     * ModelList constructor.
     * @param Model $model
     */
    public function __construct(Model $model = null)
    {
        if (!is_null($model) && $model instanceof Model) {
            $this->setModel($model);
        }
    }

    /**
     * @title setModel
     * @description
     * @createtime 2019/2/20 下午5:40
     * @param $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        //save fields
        $this->modelFields = $this->model->getTableFields();

        $this->afterSetModel();

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @title afterSetModel
     * @description
     * @createtime 2019/2/20 下午11:49
     */
    protected function afterSetModel(){}

    /**
     * @title getResult
     * @description get total result
     * @createtime 2019/2/28 上午11:51
     * @return mixed
     */
    abstract public function getResult();
}