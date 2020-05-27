<?php


namespace Yirius\Admin\widgets;


class ThinkerTree
{
    /**
     * @var array
     */
    protected $treeconfig = [
        //上级id
        'parentid' => "pid",
        //当前ID
        'id' => "id",
        //顶级序列层
        'topindex' => 0,
        //下级的名称
        'sublist' => "childs",
        //转化为text-value结构
        'textName' => "name",
        'valueName' => "id"
    ];

    /**
     * @var \Closure
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
     * @param \Closure $itemEach
     * @return $this
     * @author     yangyuance
     */
    public function setItemEach(\Closure $itemEach)
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
            //找到顶层的所有pid，做一次转换
            $itemPid = $data[$i][$this->treeconfig['parentid']];
            //转换格式
            if (!isset($pidForList[$itemPid])){
                $pidForList[$itemPid] = [];
            }
            $pidForList[$itemPid][] = $data[$i];
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
            if (is_callable($this->itemEach)) {
                $tempTree = call($this->itemEach, [$v]);
            } else {
                $tempTree = $v;
            }
            //加入其下级
            $tempTree[$this->treeconfig['sublist']] = [];

            //判断当前的pid是否存在下级，存在重新计算
            if (isset($pidForList[$v[$this->treeconfig['id']]])) {
                $tempTree[$this->treeconfig['sublist']] =
                    $this->subTree($pidForList, $v[$this->treeconfig['id']]);
            }

            //如果不存在下级，直接去掉下级
            if(empty($tempTree[$this->treeconfig['sublist']])){
                unset($tempTree[$this->treeconfig['sublist']]);
            }

            //拼装text-value
            if(isset($tempTree[$this->treeconfig['textName']]))
                $tempTree['text'] = $tempTree[$this->treeconfig['textName']];
            if(isset($tempTree[$this->treeconfig['valueName']]))
                $tempTree['value'] = $tempTree[$this->treeconfig['valueName']];

            $subTree[] = $tempTree;
        }

        return $subTree;
    }
}