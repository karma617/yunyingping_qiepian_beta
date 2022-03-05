<?php

namespace app\system\admin;

use app\common\controller\Common;
use app\system\model\SystemMenu as MenuModel;
use app\system\model\SystemRole as RoleModel;
use app\system\model\SystemUser as UserModel;
use app\system\model\SystemLog as LogModel;
use app\system\model\SystemUserRole as UserRoleModel;
use think\Db;
use think\facade\Env;
use think\facade\Log;

/**
 * 后台公共控制器
 * @package app\system\admin
 */
class Admin extends Common
{
    // [通用添加、修改专用] 模型名称，格式：模块名/模型名
    protected $oneModel = '';
    // [通用添加、修改专用] 表名(不含表前缀) 
    protected $oneTable = '';
    // [通用添加、修改专用] 验证器类，格式：app\模块\validate\验证器类名
    protected $oneValidate = false;
    //[通用添加专用] 添加数据验证场景名
    protected $oneAddScene = false;
    //[通用更新专用] 更新数据验证场景名
    protected $oneEditScene = false;
    // 数据权限设置，可选值：own 个人，org 组织，false 不启用
    protected $dataRight = false;
    // 数据权限字段名
    protected $dataRightField = 'admin_id';
    // 请求参数
    public $params;

    /**
     * 初始化方法
     */
    protected function initialize()
    {
        parent::initialize();
        $this->params = input();
        $model = new UserModel();
        // 判断登陆
        $login = $model->isLogin();
        if (!isset($login['uid']) || !$login['uid']) {
            return $this->redirect('/'.config('sys.admin_path'));
        }

        if (!defined('ADMIN_ID')) {

            define('ADMIN_ID', $login['uid']);
            define('ADMIN_ROLE', $login['role_id']);

            $oneCurMenu = MenuModel::getInfo();
            if ($oneCurMenu) {
                if (
                    !RoleModel::checkAuth($oneCurMenu['id'])
                ) {
                    return $this->error('[' . $oneCurMenu['title'] . '] 访问权限不足');
                }
            } else if (config('sys.admin_whitelist_verify')) {
                return $this->error('节点不存在或者已禁用！');
            } else {
                $oneCurMenu = ['title' => '', 'url' => '', 'id' => 0];
            }
            

            $this->_systemLog($oneCurMenu['title']);

            // 如果不是ajax请求，则读取菜单
            if (!$this->request->isAjax()) {
                $breadCrumbs = [];
                $menuParents = ['pid' => 1];
                if ($oneCurMenu['id']) {
                    $menuParents = current($breadCrumbs);
                }
                //导航菜单
                $oneMenus = MenuModel::getMainMenu(true);
                //菜单完整路径ID
                $oneCurMenuArr = MenuModel::getParentsArr($oneCurMenu['id']);
                //面包屑
                $oneBreadcrumb =MenuModel::getBreadCrumbs($oneCurMenuArr);
        
                $this->assign(compact('oneMenus','oneCurMenu','oneCurMenuArr','oneBreadcrumb'));
                
                // 表单数据默认变量名
                $this->assign('formData', '');
                $this->assign('login', $login);
                $this->assign('oneHead', '');
                $this->view->engine->layout('system@block/layout');
            }
        }
    }

    /**
     * 系统日志记录
     * @return string
     */
    private function _systemLog($title)
    {
        // 系统日志记录
        $log            = [];
        $log['uid']     = ADMIN_ID;
        $log['title']   = $title ? $title : '未加入系统菜单';
        $log['url']     = $this->request->url();
        $log['remark']  = '浏览数据';

        if ($this->request->isPost()) {
            $log['remark'] = '保存数据';
        }

        $result = LogModel::where($log)->find();

        $log['param']   = json_encode($this->request->param());
        $log['ip']      = $this->request->ip();

        if (!$result) {
            LogModel::create($log);
        } else {
            $log['id'] = $result->id;
            $log['count'] = $result->count + 1;
            LogModel::update($log);
        }
    }

    /**
     * 获取当前方法URL
     * @return string
     */
    protected function getActUrl()
    {
        $model      = request()->module();
        $controller = request()->controller();
        $action     = request()->action();
        return $model . '/' . $controller . '/' . $action;
    }

    /**
     * [通用方法]添加页面展示和保存
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {

            $postData = array_merge((array)$this->params,(array)input('post.'));

            if ($this->oneValidate) { // 数据验证

                if (strpos($this->oneValidate, '\\') === false) {

                    if (defined('IS_PLUGINS')) {
                        $this->oneValidate = 'plugins\\' . $this->request->param('_p') . '\\validate\\' . $this->oneValidate;
                    } else {
                        $this->oneValidate = 'app\\' . $this->request->module() . '\\validate\\' . $this->oneValidate;
                    }
                }

                if ($this->oneAddScene) {
                    $this->oneValidate = $this->oneValidate . '.' . $this->oneAddScene;
                }

                $result = $this->validate($postData, $this->oneValidate);
                if ($result !== true) {
                    return $this->error($result);
                }
            }

            if ($this->oneModel) { // 通过Model添加

                $model = $this->model();

                if (!$model->save($postData)) {
                    return $this->error($model->getError());
                }
            } else if ($this->oneTable) { // 通过Db添加

                if (!Db::name($this->oneTable)->insert($postData)) {
                    return $this->error('保存失败');
                }
            } else {

                return $this->error('当前控制器缺少属性（oneModel、oneTable至少定义一个）');
            }

            return $this->success('保存成功', '');
        }

        $template = $this->request->param('template', 'form');

        return $this->fetch($template);
    }

    /**
     * [通用方法]编辑页面展示和保存
     * @return mixed
     */
    public function edit()
    {
        $id = input('id/d', 0);
        if ($this->request->isPost()) { // 数据验证
            $postData = array_merge((array)$this->params,(array)input('post.'));
            if ($this->oneValidate) {
                if (strpos($this->oneValidate, '\\') === false) {
                    if (defined('IS_PLUGINS')) {
                        $this->oneValidate = 'plugins\\' . $this->request->param('_p') . '\\validate\\' . $this->oneValidate;
                    } else {
                        $this->oneValidate = 'app\\' . $this->request->module() . '\\validate\\' . $this->oneValidate;
                    }
                }
                if ($this->oneEditScene) {
                    $this->oneValidate = $this->oneValidate . '.' . $this->oneEditScene;
                }
                $result = $this->validate($postData, $this->oneValidate);
                if ($result !== true) {
                    return $this->error($result);
                }
            }
        }
        $where = [];
        if ($this->oneModel) { // 通过Model更新
            $model = $this->model();
            $pk = $model->getPk();
            $id = ($id != 0) ? $id : $this->request->param($pk);
            $where[] = [$pk, '=', $id];
            $where  = $this->getRightWhere($where);
            if ($this->request->isPost()) {
                if ($model->isUpdate($id > 0 ? true : false)->save($postData) === false) {
                    return $this->error($model->getError());
                }
                return $this->success('保存成功', '');
            }
            $formData = $model->where($where)->find();
        } else if ($this->oneTable) { // 通过Db更新
            $db = Db::name($this->oneTable);
            $pk = $db->getPk();
            $id = ($id != 0) ? $id : $this->request->param($pk);
            $where[] = [$pk, '=', $id];
            $where  = $this->getRightWhere($where);
            if ($this->request->isPost()) {
                if (!$db->where($where)->update($postData)) {
                    return $this->error('保存失败');
                }
                return $this->success('保存成功', '');
            }
            $formData = $db->where($where)->find();
        } else {
            return $this->error('当前控制器缺少属性（oneModel、oneTable至少定义一个）');
        }
        $this->assign('formData', $formData ? $formData->toArray() : []);
        $template = $this->request->param('template', 'form');
        return $this->fetch($template);
    }

    /**
     * [通用方法]状态设置
     * 禁用、启用都是调用这个内部方法
     * @return mixed
     */
    public function status()
    {
        $val        = isset($this->params['val']) ? $this->params['val'] : 0;
        $id         = isset($this->params['id']) ? $this->params['id'] : 0;
        $field      = isset($this->params['field']) ? $this->params['field'] : 'status';

        if (empty($id)) {
            return $this->error('缺少id参数');
        }

        if ($this->oneModel) {

            $obj = $this->model();
        } else if ($this->oneTable) {

            $obj = db($this->oneTable);
        } else {

            return $this->error('当前控制器缺少属性（oneModel、oneTable至少定义一个）');
        }

        $pk     = $obj->getPk();

        $where  = [];
        $where[] = [$pk, 'in', $id];
        $where  = $this->getRightWhere($where);

        $result = $obj->where($where)->setField($field, $val);
        if ($result === false) {
            return $this->error('状态设置失败');
        }

        return $this->success('状态设置成功', '');
    }

    /**
     * [通用方法]删除单条记录
     * @return mixed
     */
    public function del()
    {

        $id = isset($this->params['id']) ? $this->params['id'] : 0;
        if (empty($id)) {
            return $this->error('缺少id参数');
        }

        if ($this->oneModel) {
            $model = $this->model();
            $pk = $model->getPk();
            $where[] = [$pk, 'in', $id];
            $where = $this->getRightWhere($where);
            if (method_exists($model, 'withTrashed')) {
                $rows = $model->withTrashed()->where($where)->select();
                foreach ($rows as $v) {
                    if ($v->trashed()) {
                        $result = $v->delete(true);
                    } else {
                        $result = $v->delete();
                    }

                    if (!$result) {
                        return $this->error($v->getError());
                    }
                }
            } else {
                $row = $model->where($where)->delete();
            }
        } else if ($this->oneTable) {
            $db = db($this->oneTable);
            $pk = $db->getPk();

            $where  = [];
            $where[] = [$pk, 'in', $id];
            $where  = $this->getRightWhere($where);

            $db->where($where)->delete();
        } else {

            return $this->error('当前控制器缺少属性（oneModel、oneTable至少定义一个）');
        }

        return $this->success('删除成功', '');
    }

    /**
     * [通用方法]排序
     * @return mixed
     */
    public function sort()
    {
        $val        = isset($this->params['val']) ? $this->params['val'] : 0;
        $id         = isset($this->params['id']) ? $this->params['id'] : 0;
        $field      = isset($this->params['field']) ? $this->params['field'] : 'status';

        if (empty($id)) {
            return $this->error('缺少id参数');
        }

        if ($this->oneModel) {

            $obj = $this->model();
        } else if ($this->oneTable) {

            $obj = db($this->oneTable);
        } else {

            return $this->error('当前控制器缺少属性（oneModel、oneTable至少定义一个）');
        }

        $pk     = $obj->getPk();
        $result = $obj->where([$pk => $id])->setField($field, $val);

        if ($result === false) {
            return $this->error('排序设置失败');
        }

        return $this->success('排序设置成功', '');
    }

    /**
     * [通用方法]上传附件
     * @return mixed
     */
    public function upload()
    {
        $model = new \app\common\model\SystemAnnex;

        return json($model::fileUpload());
    }

    /** 
     * 实例化模型类($oneModel)
     */
    protected function model()
    {

        if (!$this->oneModel) {
            $this->error('oneModel属性未定义');
        }

        if (defined('IS_PLUGINS')) {
            if (strpos($this->oneModel, '\\') === false) {
                $this->oneModel = 'plugins\\' . $this->request->param('_p') . '\\model\\' . $this->oneModel;
            }

            return (new $this->oneModel);
        } else {
            if (strpos($this->oneModel, '/') === false) {
                $this->oneModel = $this->request->module() . '/' . $this->oneModel;
            }

            return model($this->oneModel);
        }
    }

    /** 
     * 实例化数据库类
     */
    protected function db($name = '')
    {
        $name = $name ?: $this->oneTable;
        if (!$name) {
            $this->error('oneTable属性未定义');
        }

        return Db::name($name);
    }

    /**
     * 获取同组织下的所有管理员ID
     * @return array
     */
    protected function getAdminIds()
    {

        if (ADMIN_ID == 1 || !$this->dataRight) {
            return [];
        }

        $ids = [ADMIN_ID];

        if ($this->dataRight == 'org') { // 组织
            $ids = UserRoleModel::getOrgUserId(ADMIN_ROLE);
        }

        return $ids;
    }

    /**
     * 获取数据权限 where
     * @param array $where
     * @return array
     */
    protected function getRightWhere($where = [])
    {
        $ids = $this->getAdminIds();

        if ($ids) {
            $ids[] = 0;
            $where[] = [$this->dataRightField, 'in', $ids];
        }

        return $where;
    }

    /**
     * 输出layui的json数据
     *
     * @param array $data
     * @param integer $count
     * @return void
     */
    protected function layuiJson($data, $count = 0)
    {
        return json(['data' => $data, 'count' => $count, 'code' => 0]);
    }
}
