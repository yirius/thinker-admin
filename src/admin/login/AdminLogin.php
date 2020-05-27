<?php


namespace Yirius\Admin\admin\login;


use Yirius\Admin\admin\model\AdminMemberModel;
use Yirius\Admin\support\abstracts\LoginAbstract;
use Yirius\Admin\ThinkerAdmin;

class AdminLogin extends LoginAbstract
{

    /**
     * @title      getUser
     * @description
     * @createtime 2020/5/27 11:52 下午
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author     yangyuance
     */
    public function getUser($id)
    {
        if(is_numeric($id)) {
            return AdminMemberModel::get(['id' => intval($id)])->toArray();
        } else {
            return AdminMemberModel::where("username|phone", "=", $id)->find()->toArray();
        }
    }

    /**
     * @title       login
     * @description 登陆的接口
     * @createtime  2020/5/27 12:59 上午
     * @param $username
     * @param $password
     * @return array
     * @author      yangyuance
     */
    public function login($username, $password)
    {
        $adminMember = $this->getUser($username);

        if(empty($adminMember)) {
            ThinkerAdmin::response()->msg("登录失败，账号不存在")->fail();
        }

        if(empty($adminMember['password'])) {
            ThinkerAdmin::response()->msg("登录失败，密码不存在，无法登录")->fail();
        }

        if(!$this->verifyPassword($password, $adminMember)) {
            ThinkerAdmin::response()->msg("登录失败，账号密码不匹配")->fail();
        }

        return [
            'id' => $adminMember['id'],
            'username' => $adminMember['username'],
            'access_type' => 0
        ];
    }

    /**
     * @title       verifyPassword
     * @description 校验密码
     * @createtime  2020/5/27 12:59 上午
     * @param $password
     * @param $user
     * @return boolean
     * @author      yangyuance
     */
    public function verifyPassword($password, $user)
    {
        return strtoupper($user['password']) === strtoupper(sha1($password . $user['salt']));
    }

    /**
     * @title       updatePassword
     * @description 更新用户密码
     * @createtime  2020/5/27 12:59 上午
     * @param $oldPassword
     * @param $newPassword
     * @param $id
     * @return AdminMemberModel
     * @author      yangyuance
     */
    public function updatePassword($oldPassword, $newPassword, $id)
    {
        $adminMember = $this->getUser($id);

        if($adminMember != null) {
            if($this->verifyPassword($oldPassword, $adminMember)) {
                $salt = ThinkerAdmin::tools()->rand(6);

                return AdminMemberModel::update([
                    'password' => sha1($newPassword . $salt),
                    'salt' => $salt
                ], ['id' => intval($id)]);
            }
        }

        return null;
    }
}