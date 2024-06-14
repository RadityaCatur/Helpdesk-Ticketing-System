import axios from 'axios';

const ax = axios.create({
  baseURL: `/api`,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json; charset=utf-8',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

ax.interceptors.request.use(
  function(config) {
    // Set Request Headers
    config.headers.common['Accept'] = 'application/json; charset=utf-8';
    config.headers.common['Content-Type'] = 'application/json';
    config.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Do something before request is sent
    const token = localStorage.getItem('token');

    if (token) {
      config.headers.common['Authorization'] = `Bearer ${token}`;
    }

    return config;
  },
  function(error) {
    // Do something with request eror
    return Promise.reject(error);
  }
);

export default ax;
