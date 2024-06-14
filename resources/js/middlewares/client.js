import store from '@/store';

export default function client({ next, router }) {
  store.dispatch('user/me').then((user) => {
    if (user.role === 'pic') {
      return next();
    } else {
      return router.push({ name: 'dashboard' });
    }
  });
}
