import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

/**
 * 公共路由
 */
export const constantRoutes = [{
        path: '/redirect',
        component: Layout,
        hidden: true,
        children: [{
            path: '/redirect/:path(.*)',
            component: () => import('@/views/redirect/index')
        }]
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/login/index'),
        hidden: true
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@/views/register/index'),
        hidden: true
    },
    {
        path: '/404',
        component: () => import('@/views/error-page/404'),
        hidden: true
    },
    {
        path: '/401',
        component: () => import('@/views/error-page/401'),
        hidden: true
    },
    {
        path: '/',
        component: Layout,
        redirect: '/dashboard',
        children: [{
            path: 'dashboard',
            component: () => import('@/views/dashboard/index'),
            name: 'Dashboard',
            meta: {
                title: '首页',
                icon: 'el-icon-s-home',
                affix: true
            }
        }]
    }
]

/**
 * 异步权限控制路由
 */
export const asyncRoutes = [{
        path: '/member-set',
        component: Layout,
        children: [{
            path: 'memberset',
            component: () => import('@/views/memberset/index'),
            name: '设置',
            meta: {
                title: '设置',
                icon: 'el-icon-setting'
            }
        }]
    },
    {
        path: '/files',
        component: Layout,
        children: [{
            path: 'files',
            component: () => import('@/views/files/index'),
            name: '文件管理',
            meta: {
                title: '文件管理',
                icon: 'el-icon-folder'
            }
        }]
    },
    {
        path: '/renew',
        component: Layout,
        children: [{
            path: 'renew',
            component: () => import('@/views/renew/index'),
            name: '账户续费',
            meta: {
                title: '账户续费',
                icon: 'el-icon-coin'
            }
        }]
    },
    {
        path: '/shop',
        component: Layout,
        children: [{
            path: 'shop',
            component: () => import('@/views/shop/index'),
            name: '周边商城',
            meta: {
                title: '周边商城',
                icon: 'el-icon-goods'
            }
        }]
    },
    {
        path: '/order',
        component: Layout,
        hidden: true,
        children: [{
            path: 'order',
            component: () => import('@/views/order/index'),
            name: '购物车',
            meta: {
                title: '购物车',
                icon: 'el-icon-goods'
            }
        }]
    },
    // 404 page must be placed at the end !!!
    {
        path: '*',
        redirect: '/404',
        hidden: true
    }
]

const createRouter = () => new Router({
    // mode: 'history', // require service support
    scrollBehavior: () => ({
        y: 0
    }),
    routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
    const newRouter = createRouter()
    router.matcher = newRouter.matcher // reset router
}

export default router
