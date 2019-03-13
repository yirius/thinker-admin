<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/13
 * Time: 下午5:07
 */

namespace Yirius\Admin\controller;


use Yirius\Admin\form\Form;
use Yirius\Admin\form\Tab;
use Yirius\Admin\model\table\CmsColumns;
use Yirius\Admin\model\table\CmsModels;
use Yirius\Admin\table\Table;

class Cms extends AdminController
{
    /**
     * @title models
     * @description
     * @createtime 2019/3/13 下午5:15
     * @return mixed
     */
    public function models()
    {
        return \Yirius\Admin\Admin::table("thinker_cms_models", function(Table $table){

            $table->setRestfulUrl("/restful/cmsmodels")->setEditPath("/thinkercms/modelsEdit");

            $table->columns("", "")->setType("checkbox");

            $table->columns("id", "编号");

            $table->columns("nid", "模型标识");

            $table->columns("title", "模型名称");

            $table->columns("status", "模型状态")->setSwitchTemplet("status");

            $table->columns("update_time", "更新时间");

            $table->columns("op", "操作")->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

            $table->on()->edit();

        })->show();
    }

    /**
     * @title modelsEdit
     * @description
     * @createtime 2019/3/13 下午5:36
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function modelsEdit($id = 0)
    {
        return \Yirius\Admin\Admin::form("thinker_cms_modelsedit", function(Form $form) use($id){
            $form->setValue($id == 0 ? [] : CmsModels::get(['id' => $id])->toArray());

            $form->text("nid", "模型标识");

            $form->text("title", "模型名称");

            $form->text("stitle", "模型简称");

            $form->text("table", "对应库表");

            $form->switchs("status", "是否开启");

            $form->text("list_order", "排序");

            $form->footer()->submit("/restful/cmsmodels", $id);
        })->show();
    }

    /**
     * @title columns
     * @description
     * @createtime 2019/3/13 下午6:32
     */
    public function columns()
    {
        return \Yirius\Admin\Admin::table("thinker_cms_columns", function(Table $table){

            $table->setRestfulUrl("/restful/cmscolumns")->setEditPath("/thinkercms/columnsEdit");

            $table->columns("", "")->setType("checkbox");

            $table->columns("id", "编号");

            $table->columns("name", "栏目名称");

            $table->columns("level", "栏目层级");

            $table->columns("cmsmodels.title", "栏目层级")->setTemplet("<div>{{d.cmsmodels.title}}</div>");

            $table->columns("is_hidden", "是否隐藏")->setSwitchTemplet("is_hidden");

            $table->columns("status", "栏目状态")->setSwitchTemplet("status");

            $table->columns("list_order", "排序");

            $table->columns("update_time", "更新时间");

            $table->columns("op", "操作")
                ->edit()->button("子栏目", "addsub", "add-1", "layui-btn-primary")->delete()
                ->setWidth(220);

            $table->tool()->edit()->delete()->add("addsub", "/thinkercms/columnsEdit?pid={{d.id}}&level={{d.level+1}}");

            $table->toolbar()->add()->delete()->event()->add()->delete();

            $table->on()->edit();

            $table->setLimit(100000)->setLimits([100000]);

        })->show();
    }

    /**
     * @title columnsEdit
     * @description
     * @createtime 2019/3/13 下午6:51
     * @param int $topid
     * @param int $pid
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function columnsEdit($pid = 0, $id = 0, $level = 0)
    {
        return \Yirius\Admin\Admin::form("thinker_cms_columnsdit", function(Form $form) use($pid, $id, $level){
            $value = $id == 0 ? [] : CmsColumns::get(['id' => $id])->toArray();
            $form->setValue($value);

            $form->tab("基础设置", function(Tab $tab){

                $tab->text("name", "*栏目名称");

                $tab->text("en_name", "英文名称");

                $tab->select("modelid", "*内容模型")->options(CmsModels::adminSelect()->getResult());

                $tab->text("list_order", "*排序");

                $tab->upload("coverpic", "封面图片");

                $tab->switchs("is_hidden", "是否隐藏");
            });

            $form->tab("高级设置", function(Tab $tab){

                $tab->switchs("is_link", "是否链接")->on('$("#thinkeradmin_link").parent().parent()[obj.elem.checked ? "show" : "hide"]();');

                $tab->text("link", "链接地址");

                $tab->textarea("seo_title", "SEO标题");

                $tab->textarea("seo_keywords", "SEO关键字");

                $tab->textarea("seo_description", "SEO描述");

            });

            $form->hidden("pid", "")->setValue(!empty($value['pid']) ? $value['pid'] : $pid);
            $form->hidden("level", "")->setValue(!empty($value['level']) ? $value['level'] : $level);

            $form->footer()->submit("/restful/cmscolumns", $id);

            //隐藏设置
            \Yirius\Admin\Admin::script('$("#thinkeradmin_link").parent().parent().hide();');
        })->show();
    }
}