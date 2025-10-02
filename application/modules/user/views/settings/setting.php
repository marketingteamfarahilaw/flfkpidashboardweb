<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Include Bootstrap & Vue -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<section class="content" id="app">
  <div class="container pt-5">
    <div class="d-flex justify-content-between mb-4">
      <h2>User Management</h2>
      <button class="btn btn-success" @click="openModal()">Register New User</button>
    </div>

    <!-- User Cards -->
    <div class="row">
      <div class="col-md-4 mb-4" v-for="user in userlist" :key="user.id">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">{{ user.customer_firstname }} {{ user.customer_lastname }}</h5>
            <p class="card-text mb-1"><strong>Email:</strong> {{ user.customer_email }}</p>
            <p class="card-text mb-1"><strong>Username:</strong> {{ user.customer_username }}</p>
            <p class="card-text mb-1"><strong>Designation:</strong> {{ user.customer_designation }}</p>
            <p class="card-text"><strong>Department:</strong> {{ user.customer_department }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Registration Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form @submit.prevent="registerUser">
            <div class="modal-header">
              <h5 class="modal-title">Register New User</h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <div class="form-group" v-for="field in registrationFields" :key="field.model">
                <label :for="field.model">{{ field.label }}</label>
                <input :type="field.type" class="form-control" v-model="registrationForm[field.model]" :required="true">
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button class="btn btn-primary" type="submit">Register</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Vue Script -->
<script>
$(document).ready(function () {
  var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));

  new Vue({
    el: '#app',
    data: {
      userlist: [],
      registrationForm: {
        customer_password: '',
        customer_firstname: '',
        customer_lastname: '',
        customer_email: '',
        customer_designation: '',
        customer_department: '',
        customers_level: ''
      },
      registrationFields: [
        { label: 'Password', model: 'customer_password', type: 'password' },
        { label: 'Username', model: 'customer_username', type: 'text' },
        { label: 'First Name', model: 'customer_first_name', type: 'text' },
        { label: 'Last Name', model: 'customer_last_name', type: 'text' },
        { label: 'Email', model: 'customer_email', type: 'email' },
        { label: 'Designation', model: 'customer_designation', type: 'text' },
        { label: 'Department', model: 'customer_department', type: 'text' },
        { label: 'Level', model: 'customers_level', type: 'text' }
      ]
    },
    mounted() {
      this.setUserList();
    },
    methods: {
      getUserList: async function () {
        return axios.get(`http://31.97.43.196/kpidashboardapi/customer/users?token=${token}`, CONFIG.HEADER);
      },
      setUserList: async function () {
        try {
          let result = await this.getUserList();
          this.userlist = result.data.response;
        } catch (error) {
          console.error(error);
        }
      },
      openModal() {
        // Reset the form
        this.registrationForm = {
          password: '',
          customer_firstname: '',
          customer_lastname: '',
          customer_email: '',
          customer_designation: '',
          customer_department: '',
          customers_level: ''
        };
        $('#userModal').modal('show');
      },
      registerUser() {
        const formData = new FormData();
        for (let key in this.registrationForm) {
          formData.append(key, this.registrationForm[key]);
        }

        axios.post('http://31.97.43.196/kpidashboardapi/site/register', formData, CONFIG.HEADER)
          .then(response => {
            alert('User registered successfully!');
            $('#userModal').modal('hide');
            this.setUserList();
          })
          .catch(error => {
            console.error('Registration error:', error.response);
            alert('Failed to register user.');
          });
      }
    }
  });
});
</script>

<!-- Bootstrap JS (for Modal) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
