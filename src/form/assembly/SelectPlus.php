<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午11:26
 */

namespace Yirius\Admin\form\assembly;


use Yirius\Admin\Admin;
use Yirius\Admin\form\Assembly;

class SelectPlus extends Assembly
{
    /**
     * @var string
     */
    protected $class = 'layui-input-block';

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * @var array
     */
    protected $inputClass = ['layui-input'];

    /**
     * @var array
     */
    protected $optionsArray = [];

    /**
     * @title setPlaceholder
     * @description
     * @createtime 2019/2/24 下午6:39
     * @param $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        if(empty($this->placeholder)){
            return '';
        }else{
            return '<option value="">' . $this->placeholder . '</option>';
        }
    }

    /**
     * @title setInputClass
     * @description
     * @createtime 2019/2/24 下午6:41
     * @param string $inputClass
     * @return $this
     */
    public function setSelectClass($inputClass)
    {
        $this->inputClass[] = $inputClass;

        return $this;
    }

    /**
     * @return array
     */
    public function getSelectClass()
    {
        return join(" ", $this->inputClass);
    }

    /**
     * @title options
     * @description
     * @createtime 2019/2/24 下午8:38
     * @param array $optionsArray
     * @return $this
     */
    public function options(array $optionsArray)
    {
        $this->optionsArray = $optionsArray;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $result = [];

        foreach($this->optionsArray as $i => $v){
            if(empty($v['list'])){
                $result[] = '<option value="'. $v['value'] .'">'. $v['text'] .'</option>';
            }else{
                $temp = [];
                $temp[] = '<optgroup label="'. $v['text'] .'">';
                foreach($v['list'] as $j => $val){
                    $temp[] = '<option value="'. $val['value'] .'">'. $val['text'] .'</option>';
                }
                $temp[] = '</optgroup>';
                $result[] = join("", $temp);
            }
        }

        return join("", $result);
    }

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
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return mixed
     */
    public function render()
    {
        return <<<HTML
<label class="layui-form-label">{$this->getLabel()}</label>
<div class="{$this->getClass()}">
    <select class="{$this->getSelectClass()}" name="{$this->getName()}" id="{$this->getId()}" lay-filter="{$this->getId()}" xm-select="{$this->getId()}" {$this->getAttributes()} >
        {$this->getPlaceholder()}
        {$this->getOptions()}
    </select>
</div>
HTML;
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
    }
}