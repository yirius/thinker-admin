<?php


namespace Yirius\Admin\widgets;


class Tree
{
    /**
     * @var array
     */
    protected $config = [
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
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

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
            if (!isset($pidForList[$data[$i][$this->config['parentid']]])){
                $pidForList[$data[$i][$this->config['parentid']]] = [];
            }
            $pidForList[$data[$i][$this->config['parentid']]][] = $data[$i];
        }
        //use key to sort, asc
        ksort($pidForList);

        return $this->subTree($pidForList, $this->config['topindex']);
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
                } else if ($this->itemEach instanceof \Closure) {
                    $tempTree = call_user_func($this->itemEach, $v);
                }
            }
            //加入其下级
            $tempTree[$this->config['sublist']] = [];
            //judge pidForList is have this pid
            if (isset($pidForList[$v[$this->config['id']]])) {
                $tempTree[$this->config['sublist']] = $this->subTree($pidForList, $v[$this->config['id']]);
            }
            //如果不存在下级，直接去掉下级
            if(empty($tempTree[$this->config['sublist']])){
                unset($tempTree[$this->config['sublist']]);
            }
            $subTree[] = $tempTree;
        }
        return $subTree;
    }
}