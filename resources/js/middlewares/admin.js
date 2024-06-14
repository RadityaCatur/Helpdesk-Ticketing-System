import store from '@/store';

export default function admin({ next, router }) {
  const roles = store.getters && store.getters.roles;
  if (roles.includes('admin')) {
    return next();
  } else {
    return router.push({ name: 'dashboard' });
  }
}
