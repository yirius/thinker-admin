<?php


namespace Yirius\Admin\widgets;


class Tree extends Widgets
{
    /**
     * @var array
     */
    protected $treeconfig = [
        'parentid' => "pid",
        'id' => "id",
        'topindex' => 0,
        'sublist' => "list"
    ];

    /**
     * @var \Closure|array
     */
    protected $itemEach = null;

    /**
     * @title      setConfig
     * @description
     * @createtime 2019/11/12 10:46 下午
     * @param array $config
     * @return $this
     * @author     yangyuance
     */
    public function setTreeconfig(array $config)
    {
        $this->treeconfig = array_merge($this->treeconfig, $config);

        return $this;
    }

    /**
     * @title      setItemEach
     * @description
     * @createtime 2019/11/12 10:50 下午
     * @param \Closure|array $itemEach
     * @return $this
     * @author     yangyuance
     */
    public function setItemEach($itemEach)
    {
        $this->itemEach = $itemEach;

        return $this;
    }

    /**
     * @title tree
     * @description make data array for tree
     * @createtime 2019/2/22 下午3:12
     * @param array $data
     * @param array|\Closure|null $keys
     * @param array|null $config
     * @return array
     */
    public function tree(array $data)
    {
        if(empty($data)) return [];
        //first, add all data to array and set key = pid
        $pidForList = [];
        for ($i = 0; $i < count($data); $i++) {
            if (!isset($pidForList[$data[$i][$this->treeconfig['parentid']]])){
                $pidForList[$data[$i][$this->treeconfig['parentid']]] = [];
            }
            $pidForList[$data[$i][$this->treeconfig['parentid']]][] = $data[$i];
        }
        //use key to sort, asc
        ksort($pidForList);

        return $this->subTree($pidForList, $this->treeconfig['topindex']);
    }

    /**
     * @title subTree
     * @description get tree data's sublist
     * @createtime 2019/2/22 下午3:12
     * @param $pidForList
     * @param $pid
     * @param $keys
     * @param $config
     * @return array
     */
    protected function subTree($pidForList, $pid)
    {
        $subTree = [];
        foreach ($pidForList[$pid] as $i => $v) {
            //拼装一下参数
            if (is_null($this->itemEach)) {
                $tempTree = $v;
            } else {
                if (is_array($this->itemEach)) {
                    $tempTree = [];
                    foreach ($this->itemEach as $key) {
                        $tempTree[$key] = isset($v[$key]) ? $v[$key] : '';
                    }
                } else if (is_callable($this->itemEach)) {
                    $tempTree = call($this->itemEach, [$v]);
                }
            }
            //加入其下级
            $tempTree[$this->treeconfig['sublist']] = [];
            //judge pidForList is have this pid
            if (isset($pidForList[$v[$this->treeconfig['id']]])) {
                $tempTree[$this->treeconfig['sublist']] = $this->subTree($pidForList, $v[$this->treeconfig['id']]);
            }
            //如果不存在下级，直接去掉下级
            if(empty($tempTree[$this->treeconfig['sublist']])){
                unset($tempTree[$this->treeconfig['sublist']]);
            }
            $subTree[] = $tempTree;
        }
        return $subTree;
    }
}