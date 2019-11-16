<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午11:26
 */

namespace Yirius\Admin\form\assemblys;


use Yirius\Admin\ThinkerAdmin;

class SelectPlus extends Select
{
    /**
     * @title direction
     * @description select direction
     * @createtime 2019/2/25 上午12:02
     * @param string $mode
     * @return $this
     * @throws \Exception
     */
    public function direction($mode = "auto")
    {
        $this->setAttrs("xm-select-direction", $mode);

        return $this;
    }

    /**
     * @title radio
     * @description
     * @createtime 2019/2/25 上午12:06
     * @return $this
     * @throws \Exception
     */
    public function radio()
    {
        $this->setAttrs("xm-select-radio", true);

        return $this;
    }

    /**
     * @title count
     * @description
     * @createtime 2019/2/25 上午12:07
     * @param $count
     * @return $this
     * @throws \Exception
     */
    public function count($count)
    {
        $this->setAttrs('xm-select-show-count', $count);

        return $this;
    }

    /**
     * @title search
     * @description
     * @createtime 2019/2/25 上午12:28
     * @param string $search
     * @param string $type
     * @return $this
     * @throws \Exception
     */
    public function search($search = '', $type = 'dl')
    {
        $this->setAttrs('xm-select-search', $search);

        $this->setAttrs('xm-select-search-type', $type);

        return $this;
    }

    /**
     * @title skin
     * @description
     * @createtime 2019/2/25 上午12:27
     * @param string $skin
     * @return $this
     * @throws \Exception
     */
    public function skin($skin = 'primary')
    {
        $this->setAttrs('xm-select-skin', $skin);

        return $this;
    }

    /**
     * @title template
     * @description set template
     * @createtime 2019/2/25 上午11:26
     * @param $template
     * @return $this
     * @throws \Exception
     */
    public function template($template)
    {
        $this->setAttrs('data-template', htmlspecialchars($template));

        return $this;
    }

    /**
     * @title linkage
     * @description
     * @createtime 2019/2/25 上午11:41
     * @param $data
     * @param int $width
     * @return $this
     * @throws \Exception
     */
    public function linkage($data, $width = 130)
    {
        if(is_array($data)){
            $data = json_encode($data);
            ThinkerAdmin::script(<<<HTML
layui.formSelects.data("{$this->getId()}", 'local', {
    arr: $data,
    linkage: true,
    linkageWidth: {$width}
});
HTML
            );
        }else{
            ThinkerAdmin::script(<<<HTML
layui.formSelects.data("{$this->getId()}", 'server', {
    url: "{$data}",
    linkage: true,
    linkageWidth: {$width}
});
HTML
            );
        }

        return $this;
    }

    /**
     * @title setJsValue
     * @description
     * @createtime 2019/3/6 下午2:26
     * @param array $value
     */
    public function setJsValue(array $value)
    {
        if(!empty($value)){
            $value = json_encode($value);
            ThinkerAdmin::script(<<<HTML
layui.formSelects.value("{$this->getId()}", {$value});
HTML
            );
        }
    }

    /**
     * @title on
     * @description
     * @createtime 2019/2/25 下午12:03
     * @param $callback
     * @param bool $isNow
     * @return $this
     * @throws \Exception
     */
    public function on($callback, $isNow = true)
    {
        ThinkerAdmin::script(<<<HTML
layui.formSelects.on("{$this->getId()}", function(id, vals, val, isAdd, isDisabled){
    {$callback}
}, $isNow);
HTML
        );

        return $this;
    }

    /**
     * @title afterSetForm
     * @description
     * @createtime 2019/2/24 下午11:28
     */
    protected function _init()
    {
        ThinkerAdmin::script('formSelects', false, true);

        ThinkerAdmin::style('formSelects', true);

        $this->setAttrs("xm-select", $this->getId());
    }
}