import axios from '@/axios';

const state = {
  rooms: [],
  onlineUsers: [],
  chatroomName: 'chatroom',
  newRoomMembers: [],
  newMessagesArrived: [],
  userCreatedNewRoom: null,
};

const mutations = {
  setChatroomName(state, payload) {
    state.chatroomName = payload;
  },

  addRoom(state, payload) {
    state.rooms = state.rooms.filter(
      item => item.id !== 0 && item.id !== payload.id
    );

    state.rooms.unshift(payload);
  },

  updateRoom(state, payload) {
    state.rooms.map(room => {
      if (room.id === payload.id) {
        room.name = payload.name;
        room.users = payload.users;
      }
    });
  },

  setRooms(state, payload) {
    payload.sort(function(a, b) {
      a = new Date(a.updated_at);
      b = new Date(b.updated_at);
      return a > b ? -1 : a < b ? 1 : 0;
    });
    state.rooms = payload;
  },

  sortRooms(state) {
    state.rooms.sort(function(a, b) {
      a = new Date(a.updated_at);
      b = new Date(a.updated_at);
      return a > b ? -1 : a < b ? 1 : 0;
    });
  },

  removeRoom(state, payload) {
    state.rooms.map(item => {
      if (item.id === payload.id) {
        item.name = room.reason;
        item.owner_id = 0;
        item.id = 0;
        item.users = [];
        item.messages = [];
      }
    });
  },

  setOnlineUsers(state, payload) {
    state.onlineUsers = payload;
  },

  addToOnlineUsers(state, payload) {
    // not to show the same user twice
    if (!state.onlineUsers.find(el => el.id === payload.id)) {
      state.onlineUsers.push(payload);
    }
  },

  removeFromOnlineUsers(state, payload) {
    state.onlineUsers = state.onlineUsers.filter(item => item !== payload);
  },

  setNewRoomMembers(state, payload) {
    state.newRoomMembers = payload;
  },

  cleanUpRooms(state) {
    state.rooms = state.rooms.filter(item => item.id !== 0);
  },

  setMessagesForRoom(state, payload) {
    state.rooms.map(item => {
      if (item.id === payload.id) {
        item.messages = payload.messages;
      }
    });
  },

  clearUserCreatedNewRoom(state) {
    state.userCreatedNewRoom = null;
  },

  addToNewMessagesArrived(state, payload) {
    state.newMessagesArrived.push(payload);
  },

  clearRoomFromNewMessagesArrived(state, payload) {
    state.newMessagesArrived = state.newMessagesArrived.filter(item => item.room_id !== payload);
  },
};

const actions = {
  loadRooms({ commit }) {
    axios.get('/rooms')
      .then(response => {
        if (response.data) {
          commit('setRooms', response.data);
        }
      })
      .catch(err => window.console.warn(err));
  },

  sendMessage({ commit }, payload) {
    axios.post('/messages', payload)
      .then(response => {
        if (!response.data) {
          window.console.warn(response);
        }
        if (response.data.status) {
          window.console.log('Message sent');
        }
      })
      .catch(err => window.console.warn(err));
  },

  deleteMessage({ commit }, payload) {
    axios.delete(`/messages/${payload}`)
      .then(response => {
        if (!(response.data && response.data === 'deleted')) {
          window.console.warn(response);
        }
      })
      .catch(err => window.console.warn(err));
  },

  setReadingProgres({ commit }, payload) {
    axios.pos(`/rooms/${payload}/setreading`)
      .then(response => {
        if (!response.data) {
          window.console.warn(response);
        }
      })
      .catch(err => window.console.warn(err));
  },

  setReadingProgress({ commit }, payload) {
    axios.post(`/rooms/${payload}/typing`)
      .then(response => {
        if (!response.data) {
          window.console.warn(response);
        }

        if (response.data.status) {
          window.console.log(response);
        }
      })
      .catch(error => window.console.warn(error));
  },

  userIsTyping({ commit }, payload) {
    axios.post(`/rooms/${payload}/typing`)
      .then(response => {
        if (!response.data) {
          window.console.warn(response);
        }
      })
      .catch(err => window.console.warn(err));
  },

  getMessagesForRoom({ commit }, payload) {
    axios.get(`/rooms/${payload.id}`)
      .then(response => {
        if (!response.data) {
          window.console.warn(response);
        } else {
          commit('setMessagesForRoom', response.data);
        }
      })
      .catch(error => window.console.warn(error));
  },

  joinChatroom({ state, commit, dispatch, rootState }, payload) {
    window.Echo.leave(state.chatroomName);

    window.Echo.join(state.chatroomName)
      .here(users => commit('setOnlineUsers', users))
      .joining(user => commit('addToOnlineUsers', user))
      .leaving(user => commit('removeFromOnlineUsers', user))
      .listen('RoomCreated', e => {
        if (e.room) {
          // only add this room if current user is a member
          if (e.room.users.find(item => item.id === payload.id)) {
            commit('addRoom', e.room);
            if (e.room.owner_id === rootState.user.userDetails.id) {
              state.userCreatedNewRoom = e.room.id;
            }
          }
        } else {
          window.console.warn(e);
        }
      })
      .listen('RoomDeleted', e => {
        if (e.room) {
          commit('removeRoom', {
            id: e.room,
            reason: 'chatroom was deleted by owner',
          });
        } else {
          window.console.warn(e);
        }
      })
      .listen('RoomUpdated', e => {
        if (e.room) {
          // only update this room if current user is a member
          if (e.room.users.find(user => user.id === payload.id)) {
            // check if the current user already has this room
            if (state.rooms.find(room => room.id === e.room.id)) {
              commit('updateRoom', e.room);
            } else {
              commit('addRoom', e.room);
            }
          } else {
            commit('removeRoom', {
              id: e.room,
              reason: 'user was removed from this chatroom',
            });
          }
        } else {
          window.console.warn(e);
        }
      })
      .listen('UsersChanged', () => {
        dispatch('loadUsers');
      })
      .on('pusher:subscription_succeeded', () => {
        window.console.log(
          `Subscription to Presence Channel "${state.chatroomName}" was successful`
        );
      });
  },
};

const getters = {
  rooms(state) {
    return state.rooms;
  },
  onlineUsers(state) {
    return state.onlineUsers;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
