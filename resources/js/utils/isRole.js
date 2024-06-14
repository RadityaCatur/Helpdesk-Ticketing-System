import store from '@/store';

export default roles => {
  const userPermissions = store.getters['user/userDetails'].roles;
  let canEnter = false;

  if (!userPermissions || !roles) {
    return canEnter;
  }

  if (!Array.isArray(roles)) {
    canEnter = userPermissions.includes(roles);
  } else {
    roles.forEach(role => {
      if (userPermissions.includes(role)) {
        canEnter = true;
      }
    });
  }

  return canEnter;
};
