<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/28
 * Time: 上午11:48
 */

namespace Yirius\Admin\model\model;


use think\facade\Validate;
use think\Model;
use Yirius\Admin\model\AdminModelBase;

class Save extends AdminModelBase
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
                $this->addData[$i] = is_array($v) ? join(",", $v) : $v;
            }
        }

        return $this;
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

        if(empty($this->where)){
            $this->model->isUpdate(false)->save($this->addData);

            return $this->model;
        }else{
            $flag = $this->model->save($this->addData, $this->where);

            if($flag){
                return $this->model;
            }else{
                return false;
            }
        }
    }
}