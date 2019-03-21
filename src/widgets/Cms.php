<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/21
 * Time: 上午11:03
 */

namespace Yirius\Admin\widgets;


use Yirius\Admin\form\Form;
use Yirius\Admin\form\Inline;
use Yirius\Admin\form\Tab;
use Yirius\Admin\Layout;
use Yirius\Admin\model\table\CmsColumns;
use Yirius\Admin\model\table\CmsGuestbook;
use Yirius\Admin\model\table\CmsGuestbookAttr;
use Yirius\Admin\model\table\CmsModels;
use Yirius\Admin\model\table\CmsModelsField;
use Yirius\Admin\model\table\CmsProductAttr;
use Yirius\Admin\table\Table;

class Cms extends Layout
{
    /**
     * @var CmsColumns
     */
    protected $cmsColumn = null;

    /**
     * @var CmsModels
     */
    protected $cmsModel = null;

    /**
     * @var int
     */
    protected $cmsid = -1;

    /**
     * @var string
     */
    protected $type = "Table";

    /**
     * Cms constructor.
     * @param \Closure|null $cms
     */
    public function __construct(\Closure $cms = null)
    {
        if($cms instanceof \Closure){
            call($cms, [$this]);
        }
    }

    /**
     * @title modelType1
     * @description Article Model
     * @createtime 2019/3/21 上午11:20
     */
    public function articleTable()
    {
        return \Yirius\Admin\Admin::table("thinker_cms_cms", function(Table $table){

            $table
                ->setRestfulUrl("/restful/cms?columnid=" . $this->cmsColumn->getData("id"))
                ->setEditPath("/thinkercms/cmsEdit?columnid=" . $this->cmsColumn->getData("id") . "&nonce=1");

            $table->columns("id", "内容编号");

            $table->columns("title", "标题")
                ->setTemplet("<div>{{# if(d.is_b){ }}<b>{{d.title}}</b>{{# }else{ }} {{d.title}} {{# } }}</div>");

            $table->columns("create_time", "创建时间");

            $table->columns("op", "操作")->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

        })->show();
    }

    /**
     * @title articleEdit
     * @description
     * @createtime 2019/3/21 下午7:45
     * @return mixed
     * @throws \Exception
     */
    public function articleEdit()
    {
        return $this->editPage();
    }

    /**
     * @title singleTable
     * @description
     * @createtime 2019/3/21 下午5:10
     * @return mixed
     * @throws \Exception
     */
    public function singleTable()
    {
        return $this->editPage();
    }

    /**
     * @title imagesTable
     * @description
     * @createtime 2019/3/21 下午5:34
     * @return mixed
     * @throws \Exception
     */
    public function imagesTable()
    {
        return $this->editPage();
    }

    /**
     * @title downloadTable
     * @description
     * @createtime 2019/3/21 下午7:50
     * @return mixed
     * @throws \Exception
     */
    public function downloadTable()
    {
        return $this->editPage();
    }

    /**
     * @title guestbookTable
     * @description
     * @createtime 2019/3/21 下午7:10
     * @return mixed
     */
    public function guestbookTable()
    {
        return \Yirius\Admin\Admin::table("thinker_cms_cms", function(Table $table){

            $table
                ->setRestfulUrl("/restful/cmsguestbook?columnid=" . $this->cmsColumn->getData("id"))
                ->setEditPath("/thinkercms/cmsEdit?columnid=" . $this->cmsColumn->getData("id") . "&nonce=1");

            $table->columns("", "")->setType("checkbox");

            $table->columns("id", "内容编号");

            $fields = CmsGuestbookAttr::adminSelect()->setWhere([
                ['columnid', '=', $this->cmsColumn['id']]
            ])->setTextName("title")->setValueName("name")->getResult();

            foreach($fields as $i => $v){
                $table->columns($v['value'], $v['text']);
            }

            $table->columns("create_time", "创建时间");

            $table->columns("op", "操作")->delete()->setWidth(90);

            $table->tool()->delete();

            $table->toolbar()
                ->button(
                    "属性设置",
                    "/thinkercms/cmsAttr?id=" . $this->cmsColumn->getData("id"),
                    "set",
                    "layui-btn-warm",
                    true
                )
                ->delete()->event()->delete();

        })->show();
    }

    /**
     * @title guestbookAttr
     * @description
     * @createtime 2019/3/21 下午9:43
     * @return mixed
     */
    public function guestbookAttr()
    {
        return \Yirius\Admin\Admin::table("thinker_cms_cmsattr", function(Table $table){

            $table
                ->setRestfulUrl("/restful/cmsguestbookattr?columnid=" . $this->cmsColumn->getData("id"))
                ->setEditPath("/thinkercms/cmsAttrEdit?columnid=" . $this->cmsColumn->getData("id") . "&nonce=1");

            $table->columns("id", "内容编号");

            $table->columns("title", "标题");

            $table->columns("create_time", "创建时间");

            $table->columns("op", "操作")->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

        })->show();
    }

    /**
     * @title guestbookAttrEdit
     * @description
     * @createtime 2019/3/21 下午9:47
     * @return mixed
     * @throws \Exception
     */
    public function guestbookAttrEdit()
    {
        return \Yirius\Admin\Admin::form("thinker_cms_cmsattredit", function(Form $form){

            $form->text("title", "字段标题");

            $form->text("name", "字段名称");

            $form->select("type", "字段名称")->options([
                ['text' => "单行文本", 'value' => "text"],
                ['text' => "多行文本", 'value' => "textarea"],
                ['text' => "下拉单选", 'value' => "select"],
            ])->on('judgeType(obj.value);');;

            $form->textarea("values", "可选择值")->setPlaceholder('如果定义字段类型为下拉框、单选项、多选项时，此处填写被选择的项目(用“|”分割文字与值,用【键盘回车】分割选项，如“男|1【键盘回车】女|2”)。');

            $form->footer()->submit("/restful/cmsguestbookattr?columnid=" . $this->cmsColumn['id']);

            \Yirius\Admin\Admin::script(<<<HTML
var thinkeradmin_values = $("#thinkeradmin_values");
//判断是否是需要显示可选择值
function judgeType(objType){
    if(['checkbox','radio','select','selectplus','tree'].indexOf(objType) != -1){
        thinkeradmin_values.parent().parent().show();
    }else{
        thinkeradmin_values.parent().parent().hide();
    }
}
judgeType('{$form->getValue("type")}' || 'text');
HTML
            );

        })->show();
    }

    /**
     * @title productTable
     * @description
     * @createtime 2019/3/21 下午10:16
     * @return mixed
     */
    public function productTable()
    {
        return \Yirius\Admin\Admin::table("thinker_cms_cms", function(Table $table){

            $table
                ->setRestfulUrl("/restful/cmsproduct?columnid=" . $this->cmsColumn->getData("id"))
                ->setEditPath("/thinkercms/cmsEdit?columnid=" . $this->cmsColumn->getData("id") . "&nonce=1");

            $table->columns("", "")->setType("checkbox");

            $table->columns("id", "内容编号");

            $table->columns("title", "标题")
                ->setTemplet("<div>{{# if(d.is_b){ }}<b>{{d.title}}</b>{{# }else{ }} {{d.title}} {{# } }}</div>");

            $table->columns("create_time", "创建时间");

            $table->columns("op", "操作")->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()
                ->add()
                ->button(
                    "属性设置",
                    "/thinkercms/cmsAttr?id=" . $this->cmsColumn->getData("id"),
                    "set",
                    "layui-btn-warm",
                    true
                )
                ->delete()->event()->add()->delete();

        })->show();
    }

    /**
     * @title productEdit
     * @description
     * @createtime 2019/3/21 下午10:27
     * @return mixed
     * @throws \Exception
     */
    public function productEdit()
    {
        return \Yirius\Admin\Admin::form("thinker_cms_cmsEdit", function(Form $form) {
            //找到cmsid
            $id = $this->cmsid;
            $value = $id == 0 ? [] : \Yirius\Admin\model\table\CmsProduct::get(['id' => $id]);

            $form->setValue($value);

            $form->tab("常规选项", function(Tab $tab){

                $tab->text("title", "标题");

                $tab->inline(function(Inline $inline){

                    $inline->switchs("is_b", "加粗");

                    $inline->switchs("is_head", "头条");

                    $inline->switchs("is_special", "特荐");

                    $inline->switchs("is_top", "置顶");

                    $inline->switchs("is_recom", "推荐");
                });

                $tab->upload("coverpic", "封面图片");

                if(!empty($this->cmsModel)){
                    CmsModelsField::parseForm($this->cmsModel['id'], $tab);
                }
            });

            $form->tab("参数设置", function(Tab $tab) use($value){

                $fields = CmsProductAttr::where([
                    ['columnid', '=', $this->cmsColumn['id']]
                ])->select();

                $attrs = empty($value['attrs']) ? [] : json_decode($value['attrs'], true);

                foreach($fields as $i => $v){
                    $type = $v['type'];
                    if($type == "select"){
                        $options = [];
                        $values = explode("\n", $v['values']);
                        foreach($values as $j => $val){
                            list($text, $value) = explode("|", $val);
                            $options[] = ['text' => $text, 'value' => $value];
                        }
                        $tab->select($v['name'], $v['title'])->options($options)
                            ->setValue(empty($attrs[$v['name']]) ? "" : $attrs[$v['name']]);
                    }else{
                        $tab->$type($v['name'], $v['title'])
                            ->setValue(empty($attrs[$v['name']]) ? "" : $attrs[$v['name']]);
                    }
                }

            });

            $form->tab("SEO设置", function(Tab $tab){

                $tab->textarea("seo_title", "SEO标题");

                $tab->textarea("seo_keywords", "SEO关键字");

                $tab->textarea("seo_description", "SEO描述");
            });

            $form->tab("其他设置", function(Tab $tab){

                $tab->text("author", "作者");

                $tab->text("visit_num", "浏览量");

                $tab->text("list_order", "排序");
            });

            $form->footer()->submit(
                "/restful/cmsproduct?columnid=" . $this->cmsColumn['id'] . "&modelid=" . $this->cmsModel['id'],
                $id
            );

        })->show();
    }

    /**
     * @title guestbookAttr
     * @description
     * @createtime 2019/3/21 下午9:43
     * @return mixed
     */
    public function productAttr()
    {
        return \Yirius\Admin\Admin::table("thinker_cms_cmsattr", function(Table $table){

            $table
                ->setRestfulUrl("/restful/cmsproductattr?columnid=" . $this->cmsColumn->getData("id"))
                ->setEditPath("/thinkercms/cmsAttrEdit?columnid=" . $this->cmsColumn->getData("id") . "&nonce=1");

            $table->columns("id", "内容编号");

            $table->columns("title", "标题");

            $table->columns("create_time", "创建时间");

            $table->columns("op", "操作")->edit()->delete();

            $table->tool()->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

        })->show();
    }

    /**
     * @title guestbookAttrEdit
     * @description
     * @createtime 2019/3/21 下午9:47
     * @return mixed
     * @throws \Exception
     */
    public function productAttrEdit()
    {
        return \Yirius\Admin\Admin::form("thinker_cms_cmsattredit", function(Form $form){

            $form->text("title", "字段标题");

            $form->text("name", "字段名称");

            $form->select("type", "字段名称")->options([
                ['text' => "单行文本", 'value' => "text"],
                ['text' => "多行文本", 'value' => "textarea"],
                ['text' => "下拉单选", 'value' => "select"],
            ])->on('judgeType(obj.value);');;

            $form->textarea("values", "可选择值")->setPlaceholder('如果定义字段类型为下拉框、单选项、多选项时，此处填写被选择的项目(用“|”分割文字与值,用【键盘回车】分割选项，如“男|1【键盘回车】女|2”)。');

            $form->footer()->submit("/restful/cmsproductattr?columnid=" . $this->cmsColumn['id']);

            \Yirius\Admin\Admin::script(<<<HTML
var thinkeradmin_values = $("#thinkeradmin_values");
//判断是否是需要显示可选择值
function judgeType(objType){
    if(['checkbox','radio','select','selectplus','tree'].indexOf(objType) != -1){
        thinkeradmin_values.parent().parent().show();
    }else{
        thinkeradmin_values.parent().parent().hide();
    }
}
judgeType('{$form->getValue("type")}' || 'text');
HTML
            );

        })->show();
    }

    /**
     * @title editPage
     * @description
     * @createtime 2019/3/21 下午5:08
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    protected function editPage()
    {
        return \Yirius\Admin\Admin::form("thinker_cms_cmsEdit", function(Form $form) {
            //找到cmsid
            $id = $this->cmsid;
            //judge if it one to one
            if($id == -1){
                //direct for form page
                $value = \Yirius\Admin\model\table\Cms::get(['columnid' => $this->cmsColumn['id']]);
                if(empty($value)){
                    $value = [];
                    $id = 0;
                }else{
                    $id = $value['id'];
                }
            }else{
                $value = $id == 0 ? [] : \Yirius\Admin\model\table\Cms::get(['id' => $id]);
            }

            $form->setValue($value);

            $form->tab("常规选项", function(Tab $tab){

                $tab->text("title", "标题");

                $tab->inline(function(Inline $inline){

                    $inline->switchs("is_b", "加粗");

                    $inline->switchs("is_head", "头条");

                    $inline->switchs("is_special", "特荐");

                    $inline->switchs("is_top", "置顶");

                    $inline->switchs("is_recom", "推荐");
                });

                $tab->upload("coverpic", "封面图片");

                if(!empty($this->cmsModel)){
                    CmsModelsField::parseForm($this->cmsModel['id'], $tab);
                }
            });

            $form->tab("SEO设置", function(Tab $tab){

                $tab->textarea("seo_title", "SEO标题");

                $tab->textarea("seo_keywords", "SEO关键字");

                $tab->textarea("seo_description", "SEO描述");
            });

            $form->tab("其他设置", function(Tab $tab){

                $tab->text("author", "作者");

                $tab->text("visit_num", "浏览量");

                $tab->text("list_order", "排序");
            });

            $form->footer()->submit(
                "/restful/cms?columnid=" . $this->cmsColumn['id'] . "&modelid=" . $this->cmsModel['id'],
                $id
            );

        })->show();
    }

    /**
     * @title render
     * @description
     * @createtime 2019/3/21 上午11:27
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function render()
    {
        $callMethod = $this->cmsModel->getData("nid") . $this->type;

        return $this->$callMethod();
    }

    /**
     * @title setCmsModel
     * @description
     * @createtime 2019/3/21 上午11:34
     * @param $cmsModel
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setCmsModel($cmsModel)
    {
        if($cmsModel instanceof CmsModels){
            $this->cmsModel = $cmsModel;
        }else{
            $this->cmsModel = CmsModels::findIdByCache($cmsModel);
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getCmsModel()
    {
        return $this->cmsModel;
    }

    /**
     * @title setCmsColumn
     * @description
     * @createtime 2019/3/21 上午11:31
     * @param $cmsColumn
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setCmsColumn($cmsColumn)
    {
        if($cmsColumn instanceof CmsColumns){
            $this->cmsColumn = $cmsColumn;
        }else{
            $this->cmsColumn = CmsColumns::findIdByCache($cmsColumn);
        }

        $this->setCmsModel($this->cmsColumn->getData("modelid"));

        return $this;
    }

    /**
     * @return CmsColumns
     */
    public function getCmsColumn()
    {
        return $this->cmsColumn;
    }

    /**
     * @title setType
     * @description
     * @createtime 2019/3/21 下午4:58
     * @param string $type
     * @return $this
     */
    public function setType($type = "Edit")
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @title setCmsid
     * @description
     * @createtime 2019/3/21 下午7:45
     * @param $cmsid
     * @return $this
     */
    public function setCmsid($cmsid)
    {
        $this->cmsid = $cmsid;

        return $this;
    }

    /**
     * @title getCmsid
     * @description
     * @createtime 2019/3/21 下午7:45
     * @return int
     */
    public function getCmsid()
    {
        return $this->cmsid;
    }
}