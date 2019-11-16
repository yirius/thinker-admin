<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/20
 * Time: 下午11:45
 */

namespace Yirius\Admin\extend\model;

class Delete extends BaseModel
{
    /**
     * @var array
     */
    protected $notDelete = [];

    /**
     * @var array
     */
    protected $delete = [];

    /**
     * @var string
     */
    protected $pk = "id";

    /**
     * @title notDelete
     * @description
     * @createtime 2019/2/28 上午11:54
     * @param $notDelete
     * @return $this
     */
    public function notDelete($notDelete)
    {
        if(is_array($notDelete)){
            $this->notDelete = $notDelete;
        }else if($notDelete instanceof \Closure){
            $this->notDelete = call($notDelete, [$this]);
        }else{
            $this->notDelete[] = $notDelete;
        }

        return $this;
    }

    /**
     * @title delete
     * @description
     * @createtime 2019/2/28 上午11:57
     * @param array $data
     * @param string $pk
     * @return $this
     */
    public function delete(array $data)
    {
        if(count($data) == count($data, 1)){
            $this->delete = $data;
        }else{
            foreach($data as $i => $v){
                $this->delete[] = $v[$this->pk];
            }
        }

        return $this;
    }

    /**
     * @title getResult
     * @description
     * @createtime 2019/2/28 下午12:04
     * @return array|bool|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function getResult()
    {
        $deleteIds = [];$notDelete = [];

        foreach($this->delete as $i => $v){
            if(!in_array($v, $this->notDelete)){
                $deleteIds[] = $v;
            }else{
                $notDelete[] = $v;
            }
        }

        $flag = $this->getModel()->where($this->pk, "in", $deleteIds)->delete();

        if($flag){
            if(count($deleteIds) == count($this->delete)){
                return true;
            }else{
                return $notDelete;
            }
        }else{
            return false;
        }
    }

    /**
     * @title setPk
     * @description
     * @createtime 2019/2/28 下午12:01
     * @param $pk
     * @return $this
     */
    public function setPk($pk)
    {
        $this->pk = $pk;

        return $this;
    }
}