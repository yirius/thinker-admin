<?php


namespace Yirius\Admin\route\controller;


use think\captcha\Captcha;
use think\facade\Cache;
use think\Request;
use Yirius\Admin\extend\ThinkerController;
use Yirius\Admin\form\assemblys\Tree;
use Yirius\Admin\form\ThinkerForm;
use Yirius\Admin\layout\ThinkerCard;
use Yirius\Admin\layout\ThinkerPage;
use Yirius\Admin\route\model\TeAdminRoles;
use Yirius\Admin\route\model\TeAdminRolesAccess;
use Yirius\Admin\route\model\TeAdminRules;
use Yirius\Admin\route\model\TeAdminUsers;
use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\ThinkerAdmin;

class Admin extends ThinkerController
{
    /**
     * @var array
     */
    protected $tokenAuth = [
        'except' => ['captcha', 'login', 'menus']
    ];

    /**
     * @title      captcha
     * @description
     * @createtime 2019/11/12 11:45 下午
     * @return \think\Response
     * @author     yangyuance
     */
    public function captcha()
    {
        return (new Captcha([
            // 验证码字体大小
            'fontSize'    =>    30,
            // 验证码位数
            'length'      =>    4,
            // 关闭验证码杂点
            'useNoise'    =>    true,
            'height'      =>    38
        ]))->entry();
    }

    /**
     * @title            login
     * @description 后台登录界面
     * @createtime       2019/11/12 7:29 下午
     * @param Request $request
     * @author           yangyuance
     */
    public function login(Request $request)
    {
        //验证信息
        $params = ThinkerAdmin::Validate()->make($request->param(), [
            'username' => "require",
            'password' => "require",
            'vercode'  => "require",
        ], [
            'username.require' => "请您填写用户名",
            'password.require' => "请您填写密码",
            'vercode.require'  => "请您填写动态验证码",
        ]);

        //判断当前使用的用户类型
        $accessType = intval($request->param("access_type", 0));

        //初始化Send
        $send = ThinkerAdmin::Send();

        //判断是否开启了验证码
        if(config('thinkeradmin.auth.vercode')){
            if(!captcha_check($request->param('vercode'))){
                $send->json([], 0, "验证码不正确");
            }
        }

        //判断是否已经超过了错误次数限制
        $loginErrorCount = config("thinkeradmin.auth.login_error_count");
        $loginCacheName = "login_count_" . addslashes($params['username']) . "_" . $accessType;
        if(!empty($loginErrorCount)){
            //找到当前登录用户名的次数
            $loginCount = Cache::get($loginCacheName, 0);
            if($loginCount > $loginErrorCount){
                $send->json([], 0, "登录密码错误次数超过限制，请您联系管理员");
            }
        }

        //前面都过了，就可以开始获取用户信息
        $auth = ThinkerAdmin::Auth()->setAccessType($accessType);

        //找到用户信息
        $userInfo = $auth->getUser(addslashes($params['username']));

        //如果不存在用户
        if(empty($userInfo)){
            $send->json([], 0, "用户名或密码错误");
        }

        //判断能否登录
        if(isset($userInfo['status']) && $userInfo['status'] == 0){
            $send->json([], 0, "用户无法登录，请您联系管理员");
        }

        //有一个状态参数
        $resultData = null;

        if($accessType === 0){
            //总后台登录,使用自定义的算法
            if ($userInfo['password'] != sha1($params['password'].$userInfo['salt'])) {
                $resultData = false;
            }else{
                $resultData = [
                    'id' => $userInfo['id'],
                    'username' => $userInfo['username'],
                    'access_type' => $accessType
                ];
            }
        }else{
            //判断是否存在自定义登录方法
            $login_verfiy_func = config("thinkeradmin.auth.login_verfiy_func");
            if($login_verfiy_func instanceof \Closure){
                $resultData = call($login_verfiy_func, [$params, $userInfo, $accessType]);
            }else{
                if ($userInfo['password'] != sha1($params['password'].$userInfo['salt'])) {
                    $resultData = false;
                }else{
                    $resultData = [
                        'id' => $userInfo['id'],
                        'username' => $userInfo['username'],
                        'access_type' => $accessType
                    ];
                }
            }
        }

        //如果是真等于false，说明失败了
        if($resultData === false){
            if(!empty($loginErrorCount)){
                //是否开启登录次数
                if(Cache::has($loginCacheName)){
                    Cache::inc($loginCacheName);
                }else{
                    Cache::set($loginCacheName, 1);
                }
            }
            $send->json([], 0, lang("incorrect username or password"));
        }else{
            if(!empty($loginErrorCount)) {
                //是否开启登录次数
                Cache::set($loginCacheName, 0);
            }

            //首先赋值token
            $resultData[config('thinkeradmin.auth.token_name')] = ThinkerAdmin::jwt()->encode($resultData);

            //否则的话直接返回对应的jwt
            $send->json($resultData, 1, lang("login success"));
        }
    }

    /**
     * @title      menus
     * @description 获取到菜单
     * @createtime 2019/11/13 2:17 下午
     * @author     yangyuance
     */
    public function menus()
    {
        $this->getAuth();

        //获取所有的菜单
        $menus = $this->auth->getMenus($this->tokenInfo['id']);

        //如果存在自定义菜单过滤，就使用
        $menu_fliter = config("thinkeradmin.menu_fliter");
        if($menu_fliter instanceof \Closure){
            $menus = call_user_func($menu_fliter, $menus, $this->tokenInfo, $this->auth);
        }

        ThinkerAdmin::Send()->json($menus);
    }

    /**
     * @title      getRuleTree
     * @description
     * @createtime 2019/11/20 2:35 下午
     * @return ThinkerCard
     * @author     yangyuance
     */
    protected function getRuleTree()
    {
        //序列化所有的菜单
        $treeData = ThinkerAdmin::Tree()
            ->setConfig([
                'sublist' => "children"
            ])
            ->tree(TeAdminRules::select()->toArray());

        return (new ThinkerCard())->setBodyLayout(
            (new Tree("tree"))
                ->setData($treeData)
                ->setEdit(['add', 'update', 'del'])
                ->setBeforeOperateEvent(<<<HTML
if(type == "add"){
    return false;
}else{
    if(type == "update"){
        layui.form.val("tree_rules", obj.data);
        layui.form.render();
    }else if(type == "del"){
        if(obj.data.children && obj.data.children.length != 0){
            layui.admin.modal.error("存在下级规则，无法删除");
        }else{
            parent.layer.prompt({formType: 1,title: '敏感操作，请验证口令'}, function(value, index){
                parent.layer.close(index);
                parent.layer.confirm('是否确认要删除该用户规则？', function(index) {
                    parent.layer.close(index);
                    var url = layui.laytpl("/restful/thinkeradmin/TeAdminRules{{parseInt(d.id)?'/'+d.id:''}}").render(obj.data || {});
                    layui.admin.http.delete(url, {password: value}, function(res){
                        $(obj.elem).remove();
                    });
                });
            });
        }
    }
    return true;
}
HTML
                )
                ->setOperateEvent(<<<HTML
//console.log(obj);
HTML
                )
                ->setClickEvent(<<<HTML
//console.log(obj);
HTML
                )
        );
    }

    /**
     * @title      getRulesForm
     * @description
     * @createtime 2019/11/20 2:41 下午
     * @return string
     * @author     yangyuance
     */
    protected function getRulesForm()
    {
        return ThinkerAdmin::Form(function(ThinkerForm $form){
            $form->setId("tree_rules");

            $form->hidden("id", "")->setValue(0);

            $form->text("pid", "上级编号");

            $form->text("name", "规则英文");

            $form->text("title", "规则名称");

            $form->switchs("status", "规则状态");

            $form->select("type", "规则类型")->options([
                ['text' => "菜单栏目", 'value' => 1],
                ['text' => "非菜单界面", 'value' => 2],
                ['text' => "界面权限", 'value' => 3],
            ]);

            $form->text("url", "对应网址");

            $form->text("icon", "对应图标");

            $form->text("list_order", "规则排序");

        })->submit(
            "/restful/thinkeradmin/TeAdminRules{{parseInt(d.id)?'/'+d.id:''}}",
            true, null, <<<HTML
function(obj, url){
    return {
        method: parseInt(obj.field.id) ? 'put' : 'post'
    };
}
HTML
        )->render();
    }

    /**
     * @title      rules
     * @description
     * @createtime 2019/11/15 6:47 下午
     * @author     yangyuance
     */
    public function rules()
    {
        ThinkerAdmin::send()->html(
            (new ThinkerPage(function(ThinkerPage $page){
                $rows = $page->rows()->space(10);
                $rows->cols()->sm(7)->layout(
                    $this->getRuleTree()
                );
                $rows->cols()->sm(5)->layout(
                    (new ThinkerCard())->setBodyLayout(
                        $this->getRulesForm()
                    )
                );
            }))->setTitle("规则管理")->render()
        );
    }

    /**
     * @title      rulesEdit
     * @description
     * @createtime 2019/11/16 10:28 下午
     * @param int $id
     * @author     yangyuance
     */
    public function rulesEdit($id = 0)
    {
        ThinkerAdmin::Form(function(ThinkerForm $form) use($id){
            $form->setValue($id == 0 ? [] : TeAdminRules::get(['id' => intval($id)])->toArray());

            $form->text("pid", "上级编号");

            $form->text("name", "规则英文");

            $form->text("title", "规则名称");

            $form->switchs("status", "规则状态");

            $form->select("type", "规则类型")->options([
                ['text' => "菜单栏目", 'value' => 1],
                ['text' => "非菜单界面", 'value' => 2],
                ['text' => "界面权限", 'value' => 3],
            ]);

            $form->text("url", "对应网址");

            $form->text("icon", "对应图标");

            $form->text("list_order", "规则排序");

        })->submit("/restful/thinkeradmin/TeAdminRules", $id)->send("规则修改界面");
    }

    /**
     * @title      roles
     * @description
     * @createtime 2019/11/25 5:25 下午
     * @author     yangyuance
     */
    public function roles()
    {
        ThinkerAdmin::Table(function(ThinkerTable $table){
            $table->restful("/restful/thinkeradmin/TeAdminRoles")
                ->setOperateUrl("/thinkeradmin/Admin/rolesEdit");

            $table->columns("id", "ID");

            $table->columns("title", "角色名称");

            $table->columns("status", "角色状态")->switchs("status");

            $table->columns("rules", "规则数量")
                ->setTemplet("<div>{{d.rules.split(',').length}}种</div>");

            $table->columns("op", "操作")->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

            $table->colsEvent()->edit()->delete();

        })->send("角色管理");
    }

    /**
     * @title      rolesEdit
     * @description
     * @createtime 2019/11/25 5:32 下午
     * @param int $id
     * @author     yangyuance
     */
    public function rolesEdit($id = 0)
    {
        ThinkerAdmin::Form(function(ThinkerForm $form) use($id){

            $value = $id == 0 ? [] : TeAdminRoles::get(['id' => $id]);

            $form->setValue($value);

            $form->text("title", "规则名称");

            $form->switchs("status", "状态");

            //序列化所有的菜单
            $useRules = empty($value['rules']) ? [] : explode(",", $value['rules']);
            $treeData = ThinkerAdmin::Tree()
                ->setConfig([
                    'sublist' => "children"
                ])
                ->setItemEach(functioN($value) use($useRules){
                    if(in_array($value['id'], $useRules)){
                        $value['checked'] = true;
                    }
                    return $value;
                })
                ->tree(TeAdminRules::select()->toArray());

            $form->tree("rules", "使用规则")->setData($treeData)->setShowCheckbox(true);

        })->submit("/restful/thinkeradmin/TeAdminRoles", $id)->send("角色编辑");
    }

    /**
     * @title      users
     * @description
     * @createtime 2019/11/25 5:57 下午
     * @author     yangyuance
     */
    public function users()
    {
        ThinkerAdmin::Table(function(ThinkerTable $table){
            $table->restful("/restful/thinkeradmin/TeAdminUsers")
                ->setOperateUrl("/thinkeradmin/Admin/usersEdit");

            $table->columns("id", "ID");

            $table->columns("username", "用户名称");

            $table->columns("phone", "手机号");

            $table->columns("realname", "真实姓名");

            $table->columns("status", "角色状态")->switchs("status");

            $table->columns("op", "操作")->edit()->delete();

            $table->toolbar()->add()->delete()->event()->add()->delete();

            $table->colsEvent()->edit()->delete();

        })->send("角色管理");
    }

    /**
     * @title      rolesEdit
     * @description
     * @createtime 2019/11/25 5:32 下午
     * @param int $id
     * @author     yangyuance
     */
    public function usersEdit($id = 0)
    {
        ThinkerAdmin::Form(function(ThinkerForm $form) use($id){

            $value = $id == 0 ? [] : TeAdminUsers::get(['id' => $id]);

            $form->setValue($value);

            $form->text("username", "用户名称");

            $form->text("phone", "手机号");

            $form->text("realname", "展示姓名");

            $form->password("password", "密码(不修改可不填写)")->setValue('');

            $form->switchs("status", "状态");

            //找到所有的角色
            $roles = TeAdminRoles::adminSelect()->setWhere([
                ['status', '=', 1]
            ])->getResult();
            //找到当前使用角色
            $useRoles = $id == 0 ? [] : TeAdminRolesAccess::getAccess($id);
            //构造渲染tree角色
            $treeData = [];
            foreach ($roles as $role){
                $treeData[] = [
                    'title' => $role['text'],
                    'id' => $role['value'],
                    'checked' => in_array($role['value'], $useRoles)
                ];
            }
            $form->tree("groups", "归属用户组")
                ->setData($treeData)
                ->setShowCheckbox(true)
                ->setValue(join(",", $useRoles));

        })->submit("/restful/thinkeradmin/TeAdminUsers", $id)->send("角色编辑");
    }
}