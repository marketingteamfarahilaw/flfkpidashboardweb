
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$first_part = $components[2];
?>

<script type="text/javascript">
    $(document).ready(function() {
        var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
        new Vue({
            data: {
                profData: [],
            },
            mounted() {
                this.setUserDetail();
            },
            methods: {
                getProfileDetail: async function () {
                    let response = axios.get(endPoints.LOGIN.concat('?token=').concat(token), CONFIG.HEADER);

                    return response;
                },
                setUserDetail: async function () {
                try {
                    let result = await this.getProfileDetail();
                    this.profData = result.data.response;

                    console.log(this.profData.customer_first_name);
                } catch (error) {
                    console.log(error);
                }
            },
            }
        }).$mount('#userApp');
        
        
        $(".logout-btn").click( () => {
            var bodyFormData = new FormData();
            bodyFormData.set('token', token );
            axios.post(endPoints.LOGOUT, bodyFormData, CONFIG.HEADER)
                  .then( (response) => {
                    LocalStorage.delete(STORAGE_ITEM.TOKEN);
                    LocalStorage.delete(STORAGE_ITEM.LOGIN);
                    $(location).attr('href', viewRoutes.HOME);
                  })
                  .catch( (error) => {
                    LocalStorage.delete(STORAGE_ITEM.TOKEN);
                  })
        });
    });
</script>

<!-- Navbar -->
<nav class="pr-0 pl-0 main-header navbar navbar-expand bg-white navbar-light border-bottom">
  	<div class="container-fluid pl-2 pr-2 pl-md-5 pr-md-5">
      <!-- Left navbar links -->
      	<ul class="navbar-nav">
	        <li class="nav-item mr-md-3">
	            <div class="__vertical_center">
	              <a class="nav-link pr-3 pl-0 " data-widget="pushmenu" href="#">
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	              </a>
	            </div>
	        </li>
	        <li class="nav-item d-sm-inline-block mt-1">
	            <a href="<?= site_url('/') ?>">
	              <img src="<?= base_url('assets/images/JFJ-Logo.png') ?>" alt="logo" class="img-fluid header-logo">
	            </a>
	        </li>
      	</ul>
  	</div>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar container-fluid -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" id="userApp">
  <!-- Sidebar -->
  	<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
		<div class="user-panel pt-2 mt-3 pb-3 mb-3 d-flex">
			<div class="">
				<div class="pull-left image">
					<img src="" class="img-circle member_image" alt="User Image">
				</div>
				<div class="pull-left info">
					<p class="fs-24 c-white fw-300">Hi, <span v-text="profData.customer_first_name"></span>!</p>
					<a href="<?= base_url('profile') ?>" class="fs-14">My Profile</a>
				</div>
			</div>
		</div>
	  	<!-- Sidebar Menu -->
	  	<nav class="mt-3 ml-4 mr-4">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle <?= ($first_part == 'digital-marketing-kpi' || $first_part == 'content' || $first_part == 'asana' || $first_part == 'gbpseo' || $first_part == 'webdev' || $first_part == 'webdev') ? 'active' : '' ?>" data-toggle="dropdown">
                        <p>Productivity Reports</p>
                    </a>
                    <div class="dropdown-menu nav-item">
                        <a href="<?= site_url('digital-marketing-kpi') ?>" class="dropdown-item <?= ($first_part == 'digital-marketing-kpi') ? 'active' : '' ?>">
                            Digital Marketing KPI Report
                        </a>
                        <a href="<?= site_url('content') ?>" class="dropdown-item <?= ($first_part == 'content') ? 'active' : '' ?>">
                            Content
                        </a>
                        <a href="<?= site_url('asana') ?>" class="dropdown-item <?= ($first_part == 'asana') ? 'active' : '' ?>">
                            Asana
                        </a>
                        <a href="<?= site_url('gbpseo') ?>" class="dropdown-item <?= ($first_part == 'gbpseo') ? 'active' : '' ?>">
                            GBP / SEO
                        </a>
                        <a href="<?= site_url('webdev') ?>" class="dropdown-item <?= ($first_part == 'webdev') ? 'active' : '' ?>">
                            WebDev
                        </a>
                    </div>
                </li>
                
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle <?= ($first_part == 'tlc' || $first_part == 'lead-docket-tracker' || $first_part == 'active-campaign' || $first_part == 'citation' || $first_part == 'keyword-ranking'  || $first_part == 'keyword-ranking' || $first_part == 'analytics-engagement' || $first_part == 'analytics-acquisition') ? 'active' : '' ?>" data-toggle="dropdown">
                        <p>Campaigns Performance</p>
                    </a>
                    <div class="dropdown-menu nav-item">
                        <a href="<?= site_url('lead-docket-tracker') ?>" class="dropdown-item <?= ($first_part == 'lead-docket-tracker') ? 'active' : '' ?>">
                            Lead Docket Tracker
                        </a>
                        <a href="<?= site_url('tlc') ?>" class="dropdown-item <?= ($first_part == 'tlc') ? 'active' : '' ?>">
                            TLC
                        </a>
                        <a href="<?= site_url('active-campaign') ?>" class="dropdown-item <?= ($first_part == 'active-campaign') ? 'active' : '' ?>">
                            Active Campaign
                        </a>
                        <a href="<?= site_url('citation') ?>" class="dropdown-item <?= ($first_part == 'citation') ? 'active' : '' ?>">
                            Citations
                        </a>
                        <a href="<?= site_url('keyword-ranking') ?>" class="dropdown-item <?= ($first_part == 'keyword-ranking') ? 'active' : '' ?>">
                            Keyword Ranking
                        </a>
                        <a href="<?= site_url('analytics-engagement') ?>" class="dropdown-item <?= ($first_part == 'analytics-engagement') ? 'active' : '' ?>">
                            Google Analytics Engagement
                        </a>
                        <a href="<?= site_url('analytics-acquisition') ?>" class="dropdown-item <?= ($first_part == 'analytics-acquisition') ? 'active' : '' ?>">
                            Google Analytics Acquisition
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
    <!-- /.sidebar-menu -->
	</div>
  	<div class="mt-3 ml-4 mr-4">

        <a class="c-white fs-24 d-inline-block mr-4" href="<?= site_url('customer-settings') ?>">
            <span class="__vertical_center">
                <i class="fa fa-cog mr-2 c-primary" aria-hidden="true"></i>
                <span class="fs-16 fw-300">Settings</span>
            </span>
        </a>
      	<a class="c-white fs-24 d-inline-block logout-btn">
	        <span class="__vertical_center">
	            <i class="fa fa-sign-out mr-2 c-primary" aria-hidden="true"></i>
	            <span class=" fs-16 fw-300">Logout</span>
	        </span>
      	</a>
        <hr class="bhr-white">
        <p class="fw-300 c-white fs-13">© <?= date('Y') ?> Farahi Law Firm. All Rights Reserved. </p>
  	</div>
  <!-- /.sidebar -->

</aside>
<div class="main-preloader __vertical_center">
  	<img src="<?= base_url('assets/images/icons/lawdger-3.gif') ?>" alt="preloader">
</div>