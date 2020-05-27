<?php


namespace Yirius\Admin\renders\form\assemblys;


use Yirius\Admin\ThinkerAdmin;

class Date extends Text
{
    public function __construct($field, $text)
    {
        parent::__construct($field, $text);

        ThinkerAdmin::script("laydate", false, true);

        $this->addAttr("lay-date", "")->addAttr("readonly", "readonly");
    }


    /**
     * @title      year
     * @description
     * @createtime 2019/2/24 下午7:21
     * @throws \Exception
     */
    public function year()
    {
        $this->addAttr("data-type", "year");

        return $this;
    }

    /**
     * @title      month
     * @description
     * @createtime 2019/2/24 下午7:21
     * @throws \Exception
     */
    public function month()
    {
        $this->addAttr("data-type", "month");

        return $this;
    }

    /**
     * @title      time
     * @description
     * @createtime 2019/2/24 下午7:21
     * @throws \Exception
     */
    public function time()
    {
        $this->addAttr("data-type", "time");

        return $this;
    }

    /**
     * @title      datetime
     * @description
     * @createtime 2019/2/24 下午7:21
     * @throws \Exception
     */
    public function datetime()
    {
        $this->addAttr("data-type", "datetime");

        return $this;
    }

    /**
     * @title      range
     * @description
     * @createtime 2019/2/25 下午11:16
     * @return $this
     * @throws \Exception
     */
    public function range($range = "/")
    {
        $this->addAttr("data-range", $range);

        return $this;
    }

    /**
     * @title      format
     * @description
     * @createtime 2019/2/25 下午11:16
     * @return $this
     * @throws \Exception
     */
    public function format($format)
    {
        $this->addAttr("data-format", $format);

        return $this;
    }

    /**
     * @title      min
     * @description
     * @createtime 2019/2/24 下午7:27
     * @return $this
     * @throws \Exception
     */
    public function min($datetime)
    {
        $this->addAttr("data-min", $datetime);

        return $this;
    }

    /**
     * @title      max
     * @description
     * @createtime 2019/2/24 下午7:27
     * @return $this
     * @throws \Exception
     */
    public function max($datetime)
    {
        $this->addAttr("data-max", $datetime);

        return $this;
    }

    /**
     * @title      calendar
     * @description
     * @createtime 2019/2/24 下午7:29
     * @return $this
     * @throws \Exception
     */
    public function calendar()
    {
        $this->addAttr("data-calendar", true);

        return $this;
    }

    /**
     * @title      onChange
     * @description
     * @createtime 2019/2/24 下午7:31
     * @return $this
     * @throws \Exception
     */
    public function onChange($callback)
    {
        $this->addAttr("data-change", $callback);

        return $this;
    }

    /**
     * @title      onReady
     * @description
     * @createtime 2019/2/24 下午7:45
     * @return $this
     * @throws \Exception
     */
    public function onReady($callback)
    {
        $this->addAttr("data-ready", $callback);

        return $this;
    }

    /**
     * @title      onDone
     * @description
     * @createtime 2019/2/24 下午7:45
     * @return $this
     * @throws \Exception
     */
    public function onDone($callback)
    {
        $this->addAttr("data-done", $callback);

        return $this;
    }

    /**
     * @title  setShowBottom
     * @description
     * @param isShow
     * @return {@link Date}
     **@author YangYuanCe
     */
    public function setShowBottom($isShow)
    {
        $this->addAttr("data-showBottom", $isShow);

        return $this;
    }

    /**
     * @title  setBtns
     * @description
     * @param btns
     * @return {@link Date}
     **@author YangYuanCe
     */
    public function setBtns(array $btns)
    {
        $this->addAttr("data-btns", json_encode($btns));

        return $this;
    }

    /**
     * @title  setLang
     * @description
     * @param lang
     * @return {@link Date}
     **@author YangYuanCe
     */
    public function setLang($lang)
    {
        $this->addAttr("data-lang", $lang);

        return $this;
    }

    /**
     * @title  setTheme
     * @description
     * @param theme
     * @return {@link Date}
     **@author YangYuanCe
     */
    public function setTheme($theme)
    {
        $this->addAttr("data-theme", $theme);

        return $this;
    }

    /**
     * @title  setMark
     * @description
     * @param mark
     * @return {@link com.thinker.admin.framework.renders.form.assemblys.Date}
     **@author YangYuanCe
     */
    public function setMark(array $mark)
    {
        $this->addAttr("data-theme", json_encode($mark));

        return $this;
    }
}