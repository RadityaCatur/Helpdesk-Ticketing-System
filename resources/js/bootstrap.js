window._ = require('lodash');
window.Popper = require('popper.js').default;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
import 'core-js';

try {
  window.$ = window.jQuery = require('jquery');

  require('bootstrap');
} catch (e) {
  window.console.warn(e);
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

const token = localStorage.getItem('token');

// Enable pusher logging - don't include this in production
window.Pusher.logToConsole = true

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,  
  authEndpoint: '/api/broadcast/auth',
  auth: {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  },
});
