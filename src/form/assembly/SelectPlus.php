<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午11:26
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;

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
        $this->setAttributes("xm-select-direction", $mode);

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
        $this->setAttributes("xm-select-radio", true);

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
        $this->setAttributes('xm-select-show-count', $count);

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
        $this->setAttributes('xm-select-search', $search);

        $this->setAttributes('xm-select-search-type', $type);

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
        $this->setAttributes('xm-select-skin', $skin);

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
        $this->setAttributes('data-template', htmlspecialchars($template));

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
            Admin::script(<<<HTML
layui.formSelects.data("{$this->getId()}", 'local', {
    arr: json_encode($data),
    linkage: true,
    linkageWidth: {$width}
});
HTML
);
        }else{
            Admin::script(<<<HTML
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
        Admin::script(<<<HTML
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
    protected function afterSetForm()
    {
        Admin::script('formSelects', 2);

        Admin::style('formSelects', 1);

        $this->setAttributes("xm-select", $this->getId());
    }
}