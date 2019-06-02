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
    component: () => import('@/views/404'),
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
  }
]

/**
 * asyncRoutes
 * the routes that need to be dynamically loaded based on user roles
 */
export const asyncRoutes = [

  {
    path: '/website',
    component: Layout,
    redirect: 'noRedirect',
    name: 'Website',
    meta: { title: '网站栏目', icon: 'tree' },
    children: [
      {
        path: 'column',
        component: () => import('@/views/dashboard/index'),
        name: 'WebsiteColumn',
        meta: { title: '栏目管理', breadcrumb: false },
        children: [
          {
            path: 'list',
            component: () => import('@/views/dashboard/index'),
            name: 'WebsiteColumnList',
            meta: { title: '栏目管理', noCache: true }
          },
          {
            path: 'edit/:id(\\d+)',
            component: () => import('@/views/dashboard/index'),
            name: 'EditWebsiteColumn',
            hidden: true,
            meta: { title: '资料管理', noCache: true }
          }
        ]
      },
      {
        path: 'list',
        component: () => import('@/views/dashboard/index'),
        name: 'ArticleList',
        meta: { title: '文章列表页', noCache: true }
      },
      {
        path: 'single',
        component: () => import('@/views/dashboard/index'),
        name: 'EditArticle',
        meta: { title: '详情页', noCache: true }
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
        path: 'list',
        component: () => import('@/views/dashboard/index'),
        name: 'MembersList',
        meta: { title: '会员管理' }
      },
      {
        path: 'reply',
        component: () => import('@/views/dashboard/index'),
        name: 'ReplyMembers',
        meta: { title: '客服工单' }
      }
    ]
  },
  {
    path: '/message',
    component: Layout,
    redirect: 'noRedirect',
    name: 'Message',
    meta: { title: '在线留言', icon: 'email' },
    children: [
      {
        path: 'list',
        component: () => import('@/views/dashboard/index'),
        name: 'MessageList',
        meta: { title: '在线留言', breadcrumb: false }
      }
    ]
  },
  {
    path: '/media',
    component: Layout,
    redirect: 'noRedirect',
    name: 'Media',
    meta: { title: '文件管理', icon: 'zip' },
    children: [
      {
        path: 'list',
        component: () => import('@/views/dashboard/index'),
        name: 'MediaList',
        meta: { title: '文件管理', breadcrumb: false }
      }
    ]
  },
  {
    path: '/users',
    component: Layout,
    redirect: 'noRedirect',
    meta: { title: '系统用户', icon: 'password' },
    children: [
      {
        path: 'management',
        component: () => import('@/views/index'),
        redirect: 'list',
        meta: { title: '成员管理' },
        children: [
          {
            path: 'list',
            component: () => import('@/views/users/list'),
            name: 'UserList',
            meta: { title: '成员管理', breadcrumb: false }
          },
          {
            path: 'insert',
            component: () => import('@/views/users/edit'),
            name: 'NewUser',
            meta: { title: '新成员', activeMenu: '/users/management/list' },
            hidden: true
          },
          {
            path: 'edit/:id',
            component: () => import('@/views/users/edit'),
            name: 'EditUser',
            meta: { title: '成员修改', activeMenu: '/users/management/list' },
            hidden: true
          }
        ]
      },
      {
        path: 'roles',
        component: () => import('@/views/index'),
        meta: { title: '角色管理' },
        children: [
          {
            path: 'list',
            component: () => import('@/views/roles/list'),
            name: 'RoleList',
            meta: { title: '角色管理', breadcrumb: false }
          },
          {
            path: 'insert',
            component: () => import('@/views/roles/edit'),
            name: 'NewRole',
            meta: { title: '新角色', activeMenu: '/users/roles/list' },
            hidden: true
          },
          {
            path: 'edit/:id',
            component: () => import('@/views/roles/edit'),
            name: 'EditRole',
            meta: { title: '角色修改', activeMenu: '/users/roles/list' },
            hidden: true
          }
        ]
      },
      {
        path: 'record',
        component: () => import('@/views/dashboard/index'),
        name: 'UserRecord',
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
        meta: { title: '计划任务' }
      },
      {
        path: 'template',
        component: () => import('@/views/dashboard/index'),
        name: 'SystemTemplate',
        meta: { title: '模板管理' }
      },
      {
        path: 'setting',
        component: () => import('@/views/dashboard/index'),
        name: 'SystemSetting',
        meta: { title: '系统设置' }
      },
      {
        path: 'help',
        component: () => import('@/views/dashboard/index'),
        name: 'SystemHelp',
        meta: { title: '帮助中心' }
      }
    ]
  },

  {
    path: '/icon',
    component: Layout,
    children: [
      {
        path: 'index',
        component: () => import('@/views/icons/index'),
        name: 'Icons',
        meta: { title: '图标查看（demo）', icon: 'icon', noCache: true }
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
