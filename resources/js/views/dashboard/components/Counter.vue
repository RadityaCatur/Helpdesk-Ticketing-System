<template>
  <div class="row d-flex align-items-center text-light">
    <b-col md="3" class="mb-sm-3 mb-xl-0 mb-3">
      <router-link to="/dashboard/tickets">
        <div class="card-panel card-open-ticket rounded bg-danger text-light p-3 text-center">
          <div class="card-panel-description">
            <div class="card-panel-text number">
              {{ openTickets ? openTickets.length : 0 }}
            </div>
            <span class="card-panel-num">
              Open Tiket
            </span>
          </div>
        </div>
      </router-link>
    </b-col>
    <b-col md="3" class="mb-sm-3 mb-xl-0 mb-3">
      <router-link to="/dashboard/tickets">
        <div class="card-panel card-onprogress-tickets bg-warning rounded text-light p-3 text-center">
          <div class="card-panel-description">
            <div class="card-panel-text number">
              {{ onProgressTickets ? onProgressTickets.length : 0 }}
            </div>
            <span class="card-panel-num">
              OnProgress Tickets
            </span>
          </div>
        </div>
      </router-link>
    </b-col>
    <b-col md="3" class="mb-sm-3 mb-xl-0 mb-3">
      <router-link to="/dashboard/tickets">
        <div class="card-panel card-closed-ticket bg-success rounded text-light p-3 text-center">
          <div class="card-panel-description">
            <div class="card-panel-text number">
              {{ closedTickets ? closedTickets.length : 0 }}
            </div>
            <span class="card-panel-num">
              Closed Tiket
            </span>
          </div>
        </div>
      </router-link>
    </b-col>
    <b-col md="3" class="mb-sm-3 mb-xl-0 mb-3">
      <router-link to="/dashboard/projects">
        <div class="card-panel card-aplication bg-primary rounded text-light p-3 text-center">
          <div class="card-panel-description">
            <div class="card-panel-text number">
              {{ projects ? projects.length : 0 }}
            </div>
            <span class="card-panel-num">
              Aplikasi
            </span>
          </div>
        </div>
      </router-link>
    </b-col>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  name: 'DashboardCounter',
  data() {
    return {
      openTickets: [],
      closedTickets: [],
      onProgressTickets: [],
      projects: [],
      tickets: [],
    };
  },

  created() {
    this.getData();
  },

  methods: {
    async getData() {
      await axios.get('/dashboard')
        .then((response) => {
          this.projects = response.data.projects;
          this.tickets = response.data.tickets;

          this.openTickets = this.tickets.filter(function(ticket) {
            return ticket.status === 'open';
          });

          this.onProgressTickets = this.tickets.filter(function(ticket) {
            return ticket.status === 'onProgress';
          });

          this.closedTickets = this.tickets.filter(function(ticket) {
            return ticket.status === 'closed';
          });
        });
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.bg{
  background-color: rgb(206, 56, 19);
  border-radius: 8px;
  padding: 5px;
}

.radius{
  border-radius: 8px;
}

.number{
  font-size: 24px;
}

.panel-group {
  margin-top: 18px;
  .card-panel-col{
    margin-bottom: 32px;
  }
  .card-panel {
    height: 108px;
    cursor: pointer;
    font-size: 12px;
    position: relative;
    overflow: hidden;
    color: #666;
    background: #fff;
    box-shadow: 4px 4px 40px rgba(0, 0, 0, .05);
    border-color: rgba(0, 0, 0, .05);
    &:hover {
      .card-panel-icon-wrapper {
        color: #fff;
      }
      .icon-people {
         background: #40c9c6;
      }
      .icon-message {
        background: #36a3f7;
      }
      .icon-money {
        background: #f4516c;
      }
      .icon-shopping {
        background: #34bfa3
      }
    }
    .icon-people {
      color: #40c9c6;
    }
    .icon-message {
      color: #36a3f7;
    }
    .icon-money {
      color: #f4516c;
    }
    .icon-shopping {
      color: #34bfa3
    }
    .card-panel-icon-wrapper {
      float: left;
      margin: 14px 0 0 14px;
      padding: 16px;
      transition: all 0.38s ease-out;
      border-radius: 6px;
    }
    .card-panel-icon {
      float: left;
      font-size: 48px;
    }
    .card-panel-description {
      float: right;
      font-weight: bold;
      margin: 26px;
      margin-left: 0px;
      .card-panel-text {
        line-height: 18px;
        color: rgba(0, 0, 0, 0.45);
        font-size: 16px;
        margin-bottom: 12px;
      }
      .card-panel-num {
        font-size: 20px;
      }
    }

  }

}
.card-open-ticket {
  background: #ee0979;
  background: -webkit-linear-gradient(45deg, #ee0979, #ff6a00)!important;
  background: linear-gradient(45deg, #ee0979, #ff6a00)!important;
  border-radius: 6px !important;
}
.card-onprogress-tickets {
  background: #fc4a1a;
  background: -webkit-linear-gradient(45deg, #fc4a1a, #f7b733)!important;
  background: linear-gradient(45deg, #fc4a1a, #f7b733)!important;
  border-radius: 6px !important;
}
.card-closed-ticket {
  background: #00b09b;
  background: -webkit-linear-gradient(45deg, #00b09b, #96c93d)!important;
  background: linear-gradient(45deg, #00b09b, #96c93d)!important;
  border-radius: 6px !important;
}
.card-aplication {
  background: #3d11cb;
  background: -webkit-linear-gradient(45deg, #3d11cb, #2575fc)!important;
  background: linear-gradient(45deg, #3d11cb , #2575fc)!important;
  border-radius: 6px !important;
}
</style>
