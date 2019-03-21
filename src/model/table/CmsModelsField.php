<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/13
 * Time: 下午5:12
 */

namespace Yirius\Admin\model\table;


use think\Request;
use Yirius\Admin\form\Form;
use Yirius\Admin\form\Inline;
use Yirius\Admin\form\Tab;
use Yirius\Admin\model\AdminModel;
use Yirius\Admin\model\AdminRestful;

class CmsModelsField extends AdminModel
{
    protected $table = "ices_cms_models_field";

    /**
     * @title findFieldByCache
     * @description
     * @createtime 2019/3/19 下午7:38
     * @param $modelid
     * @param bool $isField
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findFieldByCache($modelid, $isField = false)
    {
        $allFields = self::where('modelid', '=', $modelid)
            ->order("list_order", 'desc')
            ->cache("thinker_cms_models_field_" . $modelid, 0, "thinker_admin_cms")
            ->select()
            ->toArray();

        if($isField){
            $result = [];
            foreach($allFields as $i => $v){
                $result[] = $v['name'];
            }
            return $result;
        }else{
            return $allFields;
        }
    }

    /**
     * @title parseForm
     * @description
     * @createtime 2019/3/21 下午5:31
     * @param $modelid
     * @param $form
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function parseForm($modelid, $form)
    {
        if($form instanceof Form || $form instanceof Tab || $form instanceof Inline){
            $allFields = self::findFieldByCache($modelid);
            foreach($allFields as $i => $v){
                $type = $v['type'];
                //特异性设置
                if(in_array($type, ['datetime', 'uploadmulti'])){
                    if($type == "datetime"){
                        $typeObject = $form->date($v['name'], $v['title'])->datetime();
                    }else if($type == "uploadmulti"){
                        $typeObject = $form->upload($v['name'], $v['title'])->multi();
                    }
                }else{
                    $typeObject = $form->$type($v['name'], $v['title']);
                }
                if(in_array($type, ['checkbox','radio','select','selectplus','tree']) && !empty($v['values'])){
                    //需要设置可选择项
                    $options = [];
                    $values = explode("\n", $v['values']);
                    foreach($values as $j => $val){
                        list($text, $value) = explode("|", $val);
                        $options[] = ['text' => $text, 'value' => $value];
                    }
                    if($v['type'] == "tree"){
                        $typeObject->setData($options);
                    }else{
                        $typeObject->options($options);
                    }
                }

                //判断默认值
                if(empty($typeObject->getValue()) && $v['dvalue'] != ""){
                    $typeObject->setValue($v['dvalue']);
                }
            }
        }
    }
}