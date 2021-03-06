<?php
namespace app\common\behavior;

use app\system\model\SystemHook as HookModel;
use app\system\model\SystemHookPlugins as HookPluginsModel;
use app\system\model\SystemPlugins as PluginsModel;

/**
 * 注册钩子
 * @package app\common\behavior
 */
class Hook
{
    public function run()
    {
        // 安装操作直接return
        if (defined('ENTRANCE') && ENTRANCE=='one') return;
        $hookPlugins    = cache('hook_plugins');
        $hooks          = cache('hooks');
        $plugins        = cache('plugins');
        if (!$hookPlugins) {
            $hooks          = HookModel::where('status', 1)->column('status', 'name');
            $plugins        = PluginsModel::where('status', 2)->column('status', 'name');
            $hookPlugins    = HookPluginsModel::where('status', 1)
                                                ->field('hook,plugins')
                                                ->order('sort')
                                                ->select();
            // 非开发模式，缓存数据
            if (config('app_debug') === false) {
                cache('hook_plugins', $hookPlugins);
                cache('hooks', $hooks);
                cache('plugins', $plugins);
            }
        }
        // 全局插件
        if ($hookPlugins) {
            foreach ($hookPlugins as $value) {
                if (isset($hooks[$value->hook]) && isset($plugins[$value->plugins])) {
                    \Hook::add($value->hook, get_plugins_class($value->plugins));
                }
            }
        }
    }
}
