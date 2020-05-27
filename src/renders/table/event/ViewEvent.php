<?php


namespace Yirius\Admin\renders\table\event;


use Yirius\Admin\support\abstracts\LayoutAbstract;
use Yirius\Admin\templates\TemplateList;

abstract class ViewEvent extends LayoutAbstract
{
    /**
     * @title      _popup
     * @description
     * @createtime 2020/5/27 6:07 下午
     * @param       $view
     * @param       $title
     * @param array $config
     * @return string
     * @author     yangyuance
     */
    public function _popup($view, $title, array $config = [])
    {
        $config = array_merge([
            'area' => ['80%', '80%'],
            'id' => null,
            'data' => []
        ], $config);

        if (empty($config['id'])) $config['id'] = "popup_" . time();

        return TemplateList::table()->event()->PopupJs()->templates([
            $view, $config['id'], $title,
            json_encode($config['data']),
            json_encode($config['area'])
        ])->render();
    }

    /**
     * @title      _verfiy
     * @description
     * @createtime 2020/5/27 6:10 下午
     * @param        $title
     * @param        $url
     * @param array  $sendData
     * @param string $method
     * @param string $beforeDelete
     * @param string $afterDelete
     * @param array  $promptConfig
     * @return string
     * @author     yangyuance
     */
    public function _verify($title, $url, array $sendData = [], $method = "delete", $beforeDelete = '', $afterDelete = '', $promptConfig = []){
        $beforeDelete = empty($beforeDelete) ? "" : str_replace(["\n", "\r", "'"], "", $beforeDelete);
        $afterDelete = empty($afterDelete) ? "" : str_replace(["\n", "\r", "'"], "", $afterDelete);

        $promptConfig = array_merge(['formType' => 1, 'title' => "敏感操作，请验证口令"], $promptConfig);

        return TemplateList::table()->event()->VerifyJs()->templates([
            $title, $url, $method,
            json_encode($promptConfig), json_encode($sendData),
            $beforeDelete, $afterDelete
        ])->render();
    }

    /**
     * @title      _multiverify
     * @description
     * @createtime 2020/5/27 6:12 下午
     * @param        $title
     * @param        $url
     * @param array  $sendData
     * @param string $method
     * @param string $afterDelete
     * @param array  $promptConfig
     * @return string
     * @author     yangyuance
     */
    public function _multiverify($title, $url, array $sendData = [], $method = "delete", $beforeDelete = '', $afterDelete = '', $promptConfig = []) {

        return TemplateList::table()->event()->MultiVerifyJs()->templates([

        ])->render() . $this->_verify($title, $url, $sendData, $method, $beforeDelete, $afterDelete, $promptConfig);
    }

    /**
     * @title      getCheckedIds
     * @description
     * @createtime 2020/5/27 6:12 下午
     * @return string
     * @author     yangyuance
     */
    public function getCheckedIds() {
        return TemplateList::table()->event()->GetCheckIdsJs()->templates([

        ])->render();
    }
}