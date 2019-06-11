import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

/**
 * Note: sub-menu only appear when route children.length >= 1
 * Detail see: https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
 *
 * hidden: true                   if set true, item will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu
 *                                if not set alwaysShow, when item has more than one children route,
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noRedirect           if set noRedirect will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['admin','editor']    control the page roles (you can set multiple roles)
    title: 'title'               the name show in sidebar and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    breadcrumb: false            if set false, the item will hidden in breadcrumb(default is true)
    activeMenu: '/example/list'  if set path, the sidebar will highlight the path you set
  }
 */

/**
 * constantRoutes
 * a base page that does not have permission requirements
 * all roles can be accessed
 */
export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },

  {
    path: '/404',
    component: () => import('@/views/error-page/404'),
    hidden: true
  },

  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [{
      path: 'dashboard',
      name: 'Dashboard',
      component: () => import('@/views/dashboard/index'),
      meta: { title: '控制台', icon: 'dashboard' }
    }]
  },

  {
    path: '/profile',
    component: Layout,
    redirect: 'noRedirect',
    children: [{
      path: 'index',
      name: 'Profile',
      component: () => import('@/views/dashboard/index'),
      meta: { title: '个人中心' },
      hidden: true
    }]
  }
]

/**
 * asyncRoutes
 * the routes that need to be dynamically loaded based on user roles
 */
export const asyncRoutes = [
  {
    // 第一个栏目会有折叠bug，目前正在寻找其他解决办法
    path: '/fixed',
    component: () => import('@/views/index'),
    hidden: true
  },
  {
    path: '/columns',
    component: Layout,
    redirect: 'noRedirect',
    name: 'Columns',
    meta: { title: '网站栏目', icon: 'tree' },
    children: [
      {
        path: 'management',
        component: () => import('@/views/index'),
        redirect: '/columns/management/index',
        name: 'ColumnsManagement',
        meta: { title: '栏目管理' },
        children: [
          {
            path: 'index',
            component: () => import('@/views/columns/management/index'),
            name: 'ColumnsManagementIndex',
            meta: { title: '栏目管理', breadcrumb: false }
          }
        ]
      },
      {
        path: 'article-list',
        component: () => import('@/views/columns/article/list'),
        name: 'ColumnsArticleList',
        meta: { title: '列表页示例' }
      },
      {
        path: 'article-edit',
        component: () => import('@/views/columns/article/edit'),
        name: 'ColumnsArticleEdit',
        meta: { title: '详情页示例' }
      }
    ]
  },
  {
    path: '/members',
    component: Layout,
    redirect: 'noRedirect',
    name: 'Members',
    meta: { title: '会员中心', icon: 'user' },
    children: [
      {
        path: 'management',
        component: () => import('@/views/index'),
        redirect: '/members/management/index',
        name: 'MembersManagement',
        meta: { title: '会员管理' },
        children: [
          {
            path: 'index',
            component: () => import('@/views/members/management/index'),
            name: 'MembersManagementIndex',
            meta: { title: '会员管理', breadcrumb: false }
          },
          {
            path: 'edit/:id',
            component: () => import('@/views/members/management/edit'),
            name: 'MembersManagementEdit',
            meta: { title: '会员修改', activeMenu: '/members/management/index' },
            hidden: true
          }
        ]
      },
      {
        path: 'orders',
        component: () => import('@/views/index'),
        name: 'MembersOrders',
        meta: { title: '客服工单' },
        children: [
          {
            path: 'index',
            component: () => import('@/views/members/orders/index'),
            name: 'MembersOrderstIndex',
            meta: { title: '客服工单', breadcrumb: false }
          },
          {
            path: 'edit/:id',
            component: () => import('@/views/members/orders/edit'),
            name: 'MembersOrdersEdit',
            meta: { title: '工单查看', activeMenu: '/members/orders/index' },
            hidden: true
          }
        ]
      }
    ]
  },
  {
    path: '/messages',
    component: Layout,
    redirect: 'noRedirect',
    name: 'Messages',
    meta: { title: '在线留言', icon: 'email' },
    children: [
      {
        path: 'index',
        component: () => import('@/views/messages/index'),
        name: 'MessagesIndex',
        meta: { title: '在线留言', breadcrumb: false }
      }
    ]
  },
  {
    path: '/medias',
    component: Layout,
    redirect: 'noRedirect',
    name: 'Medias',
    meta: { title: '文件管理', icon: 'zip' },
    children: [
      {
        path: 'index',
        component: () => import('@/views/medias/index'),
        name: 'MediasIndex',
        meta: { title: '文件管理', breadcrumb: false }
      }
    ]
  },
  {
    path: '/users',
    component: Layout,
    redirect: 'noRedirect',
    name: 'Users',
    meta: { title: '系统用户', icon: 'password' },
    children: [
      {
        path: 'management',
        component: () => import('@/views/index'),
        redirect: '/users/management/index',
        name: 'UsersManagement',
        meta: { title: '成员管理' },
        children: [
          {
            path: 'index',
            component: () => import('@/views/users/management/index'),
            name: 'UsersManagementIndex',
            meta: { title: '成员管理', breadcrumb: false }
          },
          {
            path: 'create',
            component: () => import('@/views/users/management/edit'),
            name: 'UsersManagementCreate',
            meta: { title: '新成员', activeMenu: '/users/management/index' },
            hidden: true
          },
          {
            path: 'edit/:id',
            component: () => import('@/views/users/management/edit'),
            name: 'UsersManagementEdit',
            meta: { title: '成员修改', activeMenu: '/users/management/index' },
            hidden: true
          }
        ]
      },
      {
        path: 'roles',
        component: () => import('@/views/index'),
        redirect: '/users/roles/index',
        meta: { title: '角色管理' },
        children: [
          {
            path: 'index',
            component: () => import('@/views/users/roles/index'),
            name: 'UserRolesIndex',
            meta: { title: '角色管理', breadcrumb: false }
          },
          {
            path: 'create',
            component: () => import('@/views/users/roles/edit'),
            name: 'UserRolesCreate',
            meta: { title: '新角色', activeMenu: '/users/roles/index' },
            hidden: true
          },
          {
            path: 'edit/:id',
            component: () => import('@/views/users/roles/edit'),
            name: 'UserRolesEdit',
            meta: { title: '角色修改', activeMenu: '/users/roles/index' },
            hidden: true
          }
        ]
      },
      {
        path: 'logs',
        component: () => import('@/views/users/logs'),
        name: 'UsersLogs',
        meta: { title: '登录日志' }
      }
    ]
  },
  {
    path: '/system',
    component: Layout,
    name: 'System',
    redirect: 'noRedirect',
    meta: { title: '系统功能', icon: 'component' },
    children: [
      {
        path: 'task',
        component: () => import('@/views/dashboard/index'),
        name: 'SystemTask',
        meta: { title: '计划任务（未制作）' }
      },
      {
        path: 'template',
        component: () => import('@/views/dashboard/index'),
        name: 'SystemTemplate',
        meta: { title: '模板管理（未制作）' }
      },
      {
        path: 'setting',
        component: () => import('@/views/system/setting'),
        name: 'SystemSetting',
        meta: { title: '系统设置' }
      },
      {
        path: 'help',
        component: () => import('@/views/system/help'),
        name: 'SystemHelp',
        meta: { title: '帮助中心' }
      }
    ]
  },

  // 404 page must be placed at the end !!!
  { path: '*', redirect: '/404', hidden: true }
]

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
