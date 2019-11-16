<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/28
 * Time: 上午11:48
 */

namespace Yirius\Admin\extend\model;


use think\Model;
use think\Validate;

class Save extends BaseModel
{
    /**
     * @var array
     */
    protected $addData = [];

    /**
     * @var \think\Validate
     */
    protected $validate = null;

    /**
     * @var array
     */
    protected $where = [];

    /**
     * @var callable|null
     */
    protected $beforeSave = null;

    /**
     * @var callable|null
     */
    protected $afterSave = null;

    /**
     * @title setAdd
     * @description
     * @createtime 2019/2/28 下午12:11
     * @param array $data
     * @return $this
     */
    public function setAdd(array $data)
    {
        foreach($data as $i => $v){
            if(in_array($i, $this->modelFields)){
                $this->addData[$i] = addslashes(is_array($v) ? join(",", $v) : $v);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAddData()
    {
        return $this->addData;
    }

    /**
     * @title setValidate
     * @description
     * @createtime 2019/3/3 下午10:34
     * @param array|Validate $validate
     * @param array $msg
     * @return $this
     */
    public function setValidate($validate, array $msg = null)
    {
        if($validate instanceof Validate){
            $this->validate = $validate;
        }else{
            if(is_array($validate) && is_array($msg)){
                $this->validate = Validate::make($validate, $msg);
            }
        }

        return $this;
    }

    /**
     * @title setWhere
     * @description
     * @createtime 2019/2/28 下午12:20
     * @param array $where
     * @return $this
     */
    public function setWhere(array $where)
    {
        $this->where = $where;

        return $this;
    }

    /**
     * @title getError
     * @description
     * @createtime 2019/2/28 下午1:45
     * @return array|bool
     */
    public function getError()
    {
        if(!empty($this->validate))
        {
            return $this->validate->getError();
        }else{
            return "未知错误，请您联系客服";
        }
    }

    /**
     * @title      setBeforeSave
     * @description
     * @createtime 2019/11/16 8:10 下午
     * @param callable $beforeSave
     * @return $this
     * @author     yangyuance
     */
    public function setBeforeSave(callable $beforeSave)
    {
        $this->beforeSave = $beforeSave;

        return $this;
    }

    /**
     * @title      setAfterSave
     * @description
     * @createtime 2019/11/16 8:11 下午
     * @param callable $afterSave
     * @return $this
     * @author     yangyuance
     */
    public function setAfterSave(callable $afterSave)
    {
        $this->afterSave = $afterSave;

        return $this;
    }

    /**
     * @title getResult
     * @description get total result
     * @createtime 2019/2/28 上午11:51
     * @return Model|bool
     */
    public function getResult()
    {
        if(!empty($this->validate)){
            if(!$this->validate->check($this->addData)){
                return false;
            }
        }

        /**
         * 对数据进行最终处理
         */
        if(!is_null($this->beforeSave)){
            $this->addData = call($this->beforeSave, [$this->addData, $this->model]);
        }

        if(empty($this->where)){
            //如果是新增，重置参数
            $this->model->id = "";

            $this->model->isUpdate(false)->save($this->addData);

            //成功执行
            if(!is_null($this->afterSave)){
                call($this->afterSave, [false, $this->addData, $this->model]);
            }

            return $this->model;
        }else{
            $flag = $this->model->save($this->addData, $this->where);

            if($flag){
                //成功执行
                if(!is_null($this->afterSave)){
                    call($this->afterSave, [true, $this->addData, $this->model]);
                }

                return $this->model;
            }else{
                return false;
            }
        }
    }
}