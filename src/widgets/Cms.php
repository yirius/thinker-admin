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
use Yirius\Admin\model\table\CmsModels;
use Yirius\Admin\model\table\CmsModelsField;
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

    public function downloadTable()
    {

    }

    public function productTable()
    {

    }

    public function guestbookTable()
    {

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
        return $this->editPage(-1);
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
        return $this->editPage(-1);
    }

    /**
     * @title editPage
     * @description
     * @createtime 2019/3/21 下午5:08
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    protected function editPage($id = 0)
    {
        return \Yirius\Admin\Admin::form("thinker_cms_cmsEdit", function(Form $form) use($id){

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

            //判断是否存在其他表
            if(!empty($value)){
                $value->cmscontent;
                $value = $value->toArray();
                $cmsContentArr = $value['cmscontent'];
                unset($value['cmscontent']);
                $value = array_merge($value, $cmsContentArr);
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
}