import isRole from '@/utils/isRole';
import store from '@/store';

export default function checkPermissions({ next, to, from, router }) {
  const requiredPermissions = to.meta.permissions;
  if (!from.name) {
    store.dispatch('user/me').then(() => {
      const canEnter = isRole(requiredPermissions);

      if (canEnter) {
        return next();
      }

      return router.push({ name: 'home' });
    });
  } else {
    const canEnter = isRole(requiredPermissions);

    if (canEnter) {
      return next();
    }

    return router.push({ name: 'home' });
  }
}
