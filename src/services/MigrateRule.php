<?php


namespace Yirius\Admin\services;


class MigrateRule
{
    protected $id = 1;

    protected $pid = 0;

    protected $name = '';

    protected $title = '';

    protected $status = 1;

    protected $type = 1;

    protected $url = '';

    protected $icon = '';

    protected $list_order = 0;

    protected $condition = '';

    protected $restful = '';

    protected $tableConf = ['add', 'del', 'edit', 'exports'];

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param mixed $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param mixed $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param mixed $list_order
     */
    public function setListOrder($list_order)
    {
        $this->list_order = $list_order;

        return $this;
    }

    /**
     * @param bool $restful
     */
    public function setRestful($restful)
    {
        $this->restful = $restful;

        return $this;
    }

    /**
     * @param array $tableConf
     */
    public function setTableConf(array $tableConf = [])
    {
        $this->tableConf = $tableConf;

        return $this;
    }

    /**
     * @title      getResult
     * @description
     * @createtime 2019/9/30 12:28 上午
     * @return array
     * @author     yangyuance
     */
    public function getResult()
    {
        $result = [
            [
                'id' => $this->id,
                'pid' => $this->pid,
                'name' => $this->name,
                'title' => $this->title,
                'status' => $this->status,
                'type' => $this->type,
                'url' => $this->url,
                'icon' => $this->icon,
                'list_order' => $this->list_order,
                'create_time' => date("Y-m-d H:i:s"),
                'update_time' => date("Y-m-d H:i:s")
            ]
        ];

        $pid = $this->id;
        if(!empty($this->tableConf)){
            foreach($this->tableConf as $i => $v){
                switch ($v){
                    case "add":
                        $title = "新增";
                        break;
                    case "del":
                        $title = "删除";
                        break;
                    case "edit":
                        $title = "修改";
                        break;
                    case "exports":
                        $title = "导出";
                        break;
                    default:
                        $title = $i;
                        break;
                }
                $result[] = [
                    'id' => ++$this->id,
                    'pid' => $pid,
                    'name' => $this->name . ":" . $v,
                    'title' => $this->title . "-" . $title,
                    'status' => $this->status,
                    'type' => 3,
                    'url' => '',
                    'icon' => '',
                    'list_order' => --$this->list_order,
                    'create_time' => date("Y-m-d H:i:s"),
                    'update_time' => date("Y-m-d H:i:s")
                ];
            }
            //判断是否存在添加和修改，存在就需要新增对应界面
            if(in_array("add", $this->tableConf) || in_array("edit", $this->tableConf)){
                $result[] = [
                    'id' => ++$this->id,
                    'pid' => $pid,
                    'name' => $this->name . ":editpage",
                    'title' => $this->title . "-新增/修改界面",
                    'status' => $this->status,
                    'type' => 2,
                    'url' => $this->url . "Edit",
                    'icon' => '',
                    'list_order' => --$this->list_order,
                    'create_time' => date("Y-m-d H:i:s"),
                    'update_time' => date("Y-m-d H:i:s")
                ];
            }
        }

        if($this->restful){
            $result[] = [
                'id' => ++$this->id,
                'pid' => $pid,
                'name' => $this->name . ":restful",
                'title' => $this->title . "-RestfulApi",
                'status' => $this->status,
                'type' => 2,
                'url' => $this->restful,
                'icon' => '',
                'list_order' => --$this->list_order,
                'create_time' => date("Y-m-d H:i:s"),
                'update_time' => date("Y-m-d H:i:s")
            ];
        }

        return $result;
    }
}