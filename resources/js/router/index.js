import Vue from 'vue';
import VueRouter from 'vue-router';

import DashboardLayout from '@/layout/DashboardLayout.vue';
import NotFound from '@/views/pages/NotFoundPage.vue';
import AppLayout from '@/layout/AppLayout.vue';
import UserProfile from '@/views/user/EditProfileForm.vue';
import Dashboard from '@/views/dashboard';

import Middlewares from '@/middlewares';

import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

NProgress.configure({ showSpinner: false });

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    component: AppLayout,
    redirect: '/login',
    children: [
      {
        path: '/login',
        name: 'login',
        component: () =>
          import(/* webpackChunkName: "login" */ '@/views/auth/Login.vue'),
        meta: {
          middleware: [Middlewares.guest],
        },
      },
      {
        path: '/forgot-password',
        name: 'forgotPassword',
        component: () =>
          import(
            /* webpackChunkName: "forgotPassword" */ '@/views/auth/ForgotPassword.vue'
          ),
        meta: {
          middleware: [Middlewares.guest],
        },
      },
      {
        path: 'register',
        name: 'register',
        component: () =>
          import(
            /* webpackChunkName: "register" */ '@/views/auth/Register.vue'
          ),
        meta: {
          middleware: [Middlewares.guest],
        },
      },
      {
        path: '/reset-password',
        name: 'reset-password',
        component: () =>
          import(
            /* webpackChunkName: "resetPassword" */ '@/views/auth/ResetPassword.vue'
          ),
        meta: {
          middleware: [Middlewares.guest],
        },
      },
      {
        path: '/verify-email',
        name: 'verifyEmail',
        component: () =>
          import(
            /* webpackChunkName: "verifyEmail" */ '@/views/auth/VerifyEmail.vue'
          ),
        meta: {
          middleware: [Middlewares.guest],
        },
      },
    ],
  },
  {
    path: '/dashboard',
    component: DashboardLayout,
    meta: {
      middleware: [Middlewares.auth],
    },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: Dashboard,
        meta: {
          title: 'dashboard',
          middleware: [Middlewares.auth],
        },
      },
      {
        path: 'roles-permissions',
        component: () => import('@/views/role-permission/List'),
        name: 'RoleList',
        meta: {
          title: 'roles-permission',
          icon: 'role',
          permissions: [
            Middlewares.auth,
            // Middlewares.admin,
          ],
        },
      },
      {
        path: 'profile',
        name: 'profile',
        component: UserProfile,
        meta: {
          title: 'profile',
          middleware: [Middlewares.auth],
        },
      },
      {
        path: 'tickets',
        name: 'tickets',
        component: () => import('@/views/ticket/List.vue'),
        meta: {
          title: 'tickets',
          middleware: [Middlewares.auth],
        },
      },
      {
        path: 'add-ticket',
        name: 'add-ticket',
        component: () => import('@/views/ticket/Add.vue'),
        meta: {
          title: 'add-ticket',
          middleware: [Middlewares.auth],
        },
      },
      {
        path: 'show-ticket/:id',
        name: 'show-ticket',
        component: () => import('@/views/ticket/Show.vue'),
        meta: {
          title: 'show-ticket',
          middleware: [Middlewares.auth],
        },
      },
      {
        path: 'edit-ticket/:id',
        name: 'edit-ticket',
        component: () => import('@/views/ticket/Edit.vue'),
        meta: {
          title: 'edit-ticket',
          middleware: [Middlewares.auth],
        },
      },
      {
        path: 'projects',
        name: 'projects',
        component: () => import('@/views/project/List.vue'),
        meta: {
          title: 'projects',
          middleware: [Middlewares.auth],
        },
      },
      {
        path: 'add-project',
        name: 'add-project',
        component: () => import('@/views/project/Add.vue'),
        meta: {
          title: 'add-ticket',
          middleware: [
            Middlewares.auth,
            // Middlewares.admin,
          ],
        },
      },
      {
        path: 'show-project/:id',
        name: 'show-project',
        component: () => import('@/views/project/Show.vue'),
        meta: {
          title: 'show-project',
          middleware: [Middlewares.auth],
        },
      },
      {
        path: 'edit-project/:id',
        name: 'edit-project',
        component: () => import('@/views/project/Edit.vue'),
        meta: {
          title: 'edit-project',
          middleware: [Middlewares.auth],
        },
      },
      {
        path: 'users',
        name: 'users',
        component: () => import('@/views/user/List.vue'),
        meta: {
          title: 'List User',
          // middleware: [Middlewares.admin],
        },
      },
      {
        path: 'show-user/:id',
        name: 'show-user',
        component: () => import('@/views/user/show-user.vue'),
        meta: {
          title: 'Show user',
        },
      },
      {
        path: 'edit-user/:id',
        name: 'edit-user',
        component: () => import('@/views/user/Edit.vue'),
        meta: {
          title: 'Edit User',
          // middleware: [Middlewares.admin],
        },
      },
      {
        path: 'add-user',
        name: 'add-user',
        component: () => import('@/views/user/Add.vue'),
        meta: {
          title: 'Add User',
          // middleware: [Middlewares.admin],
        },
      },
      {
        path: 'activities',
        name: 'activities',
        component: () => import('@/views/Activities/index.vue'),
        meta: {
          title: 'Activities',
          // middleware: [Middlewares.admin],
        },
      },
      {
        path: 'categories',
        name: 'categories',
        component: () => import('@/views/categories/index.vue'),
        meta: {
          title: 'Application Categories',
          permissions: [
            Middlewares.auth,
          ],
        },
      },
      {
        path: 'client',
        name: 'client',
        component: () => import('@/views/client/index.vue'),
        meta: {
          title: 'Client',
          // middleware: [Middlewares.admin],
        },
      },
      {
        path: 'add-client',
        name: 'add-client',
        component: () => import('@/views/client/Add.vue'),
        meta: {
          title: 'Add Client',
          // middleware: [Middlewares.admin],
        },
      },
      {
        path: 'positions',
        name: 'positions',
        component: () => import('@/views/positions/index.vue'),
        meta: {
          title: 'Posisi',
          permissions: [
            Middlewares.auth,
          ],
        },
      },
      {
        path: 'companies',
        name: 'companies',
        component: () => import('@/views/companies/index.vue'),
        meta: {
          title: 'Perusahaan',
          permissions: [
            Middlewares.auth,
          ],
        },
      },
    ],
  },
  { path: '*', component: NotFound },
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.APP_URL,
  routes,
  fallback: true,
});

function nextCheck(context, middleware, index) {
  const nextMiddleware = middleware[index];

  if (!nextMiddleware) {
    return context.next;
  }

  return (...parameters) => {
    context.next(...parameters);
    const nextMid = nextCheck(context, middleware, index + 1);

    nextMiddleware({ ...context, next: nextMid });
  };
}

router.beforeEach((to, from, next) => {
  NProgress.start();
  if (to.meta.middleware) {
    const middleware = Array.isArray(to.meta.middleware)
      ? to.meta.middleware
      : [to.meta.middleware];

    const ctx = {
      from,
      next,
      router,
      to,
    };

    const nextMiddleware = nextCheck(ctx, middleware, 1);

    return middleware[0]({ ...ctx, next: nextMiddleware });
  }

  return next();
});

router.afterEach(() => {
  NProgress.done();
});

export default router;
