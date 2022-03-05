<?php

namespace app\system\admin;

use app\system\model\SystemUser as UserModel;
use app\system\model\SystemRole as RoleModel;
use app\system\model\SystemMenu as MenuModel;
use one\Tree;

/**
 * 后台用户、角色控制器
 * @package app\system\admin
 */
class User extends Admin
{
    public $tabData = [];
    protected $oneTable = 'SystemUser';
    protected $oneModel = 'SystemUser';
    /**
     * 初始化方法
     */
    protected function initialize()
    {
        parent::initialize();

        $tabData['column'] = [
            [
                'label' => '管理用户',
                'name' => 'user',
                'url' => url('system/user/index'),
            ],
            [
                'label' => '管理角色',
                'name' => 'role',
                'url' => url('system/user/role'),
            ],
        ];
        $this->tabData = $tabData;
    }

    /**
     * 用户管理
     * @return mixed
     */
    public function index($q = '')
    {
        if ($this->request->isAjax()) {
            $where      = $data = [];
            $page       = $this->request->param('page/d', 1);
            $limit      = $this->request->param('limit/d', 15);
            $keyword    = $this->request->param('keyword/s');
            $where[]    = ['id', 'neq', 1];
            if ($keyword) {
                $where[] = ['username', 'like', "%{$keyword}%"];
            }

            $data['data'] = UserModel::with('hasRoles')->where($where)->page($page)->limit($limit)->select();
            $data['count'] = UserModel::where($where)->count('id');
            $data['code'] = 0;
            $data['msg'] = '';
            return json($data);
        }

        $assign = [];
        $this->tabData['current']= 'user';
        $assign['tabData'] = $this->tabData;

        return $this->assign($assign)->fetch();
    }

    /**
     * 添加用户
     * @return mixed
     */
    public function addUser()
    {
        if ($this->request->isPost()) {

            $data = $this->request->post();
            $data['password'] = md5($data['password']);
            $data['password_confirm'] = md5($data['password_confirm']);

            // 验证
            $result = $this->validate($data, 'SystemUser');
            if($result !== true) {
                return $this->error($result);
            }
            
            unset($data['id'], $data['password_confirm']);

            $data['last_login_ip'] = '';
            $data['auth'] = '';
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            if (!UserModel::create($data)) {
                return $this->error('添加失败');
            }

            return $this->success('添加成功');
        }
        
        $this->assign('menus', []);
        $this->assign('roles', RoleModel::where('id', '>', 1)->order('id asc')->field('id as value,name as label')->select()->toArray());

        return $this->fetch('userform');
    }

    /**
     * 修改用户
     * @param int $id
     * @return mixed
     */
    public function editUser($id = 0)
    {
        if ($id == 1 || ADMIN_ID == $id) {
            return $this->error('禁止修改当前登录用户');
        }
        
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            if (isset($data['password'])) {
                $data['password'] = md5($data['password']);
                $data['password_confirm'] = md5($data['password_confirm']);
            }
            
            // 验证
            $result = $this->validate($data, 'SystemUser.update');
            if($result !== true) {
                return $this->error($result);
            }

            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']);
            }

            if (!UserModel::update($data)) {
                return $this->error('修改失败');
            }
            return $this->success('修改成功');
        }

        $row = UserModel::with('hasIndexs')->where('id', '=', $id)->field('id,username,nick,email,mobile,status')->find()->toArray();

        $row['role_id'] = array_column($row['has_indexs'], 'role_id');

        $this->assign('roles', RoleModel::where('id', '>', 1)->order('id asc')->field('id as value,name as label')->select()->toArray());
        $this->assign('formData', $row);
        return $this->fetch('userform');
    }

    /**
     * 修改个人信息
     * @return mixed
     */
    public function info()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['id'] = ADMIN_ID;
            // 防止伪造篡改
            unset($data['role_id'], $data['status']);

            if (isset($data['password'])) {
                $data['password'] = md5($data['password']);
                $data['password_confirm'] = md5($data['password_confirm']);
            }

            // 验证
            $result = $this->validate($data, 'SystemUser.info');
            if($result !== true) {
                return $this->error($result);
            }

            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']);
            }

            if (!UserModel::update($data)) {
                return $this->error('修改失败');
            }
            return $this->success('修改成功');
        }

        $row = UserModel::where('id', ADMIN_ID)->field('username,nick,email,mobile')->find()->toArray();
        $this->assign('formData', $row);
        return $this->fetch();
    }

    /**
     * 删除用户
     * @param int $id
     * @return mixed
     */
    public function delUser()
    {
        parent::del();
    }

    // +----------------------------------------------------------------------
    // | 角色
    // +----------------------------------------------------------------------

    /**
     * 角色管理
     * @return mixed
     */
    public function role()
    {
        if ($this->request->isAjax()) {
            $data = [];
            $page = $this->request->param('page/d', 1);
            $limit = $this->request->param('limit/d', 15);

            $data['data'] = RoleModel::where('id', '<>', 1)->select();
            $data['count'] = RoleModel::where('id', '<>', 1)->count('id');
            $data['code'] = 0;
            $data['msg'] = '';
            return json($data);
        }
        $this->tabData['current']= 'role';
        $this->assign('tabData', $this->tabData);
        return $this->fetch();
    }

    /**
     * 添加角色
     * @return mixed
     */
    public function addRole()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
            $result = $this->validate($data, 'SystemRole');
            if($result !== true) {
                return $this->error($result);
            }
            unset($data['id']);
            if (!RoleModel::create($data)) {
                return $this->error('添加失败');
            }
            return $this->success('添加成功');
        }
        $tabData = [];
        $tabData['menu'] = [
            ['title' => '添加角色'],
            ['title' => '设置权限'],
        ];

        $this->assign('menus', MenuModel::getAuthTree());
        $this->assign('oneTabData', $tabData);
        $this->assign('oneTabType', 2);
        
        return $this->fetch('roleform');
    }

    /**
     * 修改角色
     * @return mixed
     */
    public function editRole($id = 0)
    {
        if ($id <= 1) {
            return $this->error('禁止编辑');
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            // 验证
            $result = $this->validate($data, 'SystemRole');
            if($result !== true) {
                return $this->error($result);
            }
            if (!RoleModel::update($data)) {
                return $this->error('修改失败');
            }

            // 更新权限缓存
            if(isset($data['auth'])){
                session('role_auth_'.$data['id'], $data['auth']);
            }

            return $this->success('修改成功');
        }

        $row = RoleModel::where('id', $id)->field('id,name,intro,auth,status')->find();
        $row = $row ? $row->toArray() : [];
        
        $this->assign('menus', MenuModel::getAuthTree());
        $this->assign('formData', $row);
        
        return $this->fetch('roleform');
    }

    /**
     * 角色状态设置
     */
    public function statusRole()
    {
        $this->oneModel = 'SystemRole';
        parent::status();
    }
    /**
     * 用户状态设置
     */
    public function statusUser()
    {
        $this->oneModel = 'SystemUser';
        parent::status();
    }

    /**
     * 删除角色
     * @param int $id
     * @return mixed
     */
    public function delRole()
    {
        $id=input('id/a');
        if (empty($id)) {
            return $this->error('缺少id参数');
        }
        try {
            RoleModel::destroy($id);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        return $this->success('删除成功');
    }
}
