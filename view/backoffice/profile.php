<?php
require_once __DIR__ . '/../../controller/session_config.php';  
require_once __DIR__ . '/../../controller/adminController.php';  
if (!isset($_SESSION['admin'])) {
  // Not logged in, redirect to login
  header('Location: login.php');
  exit;
}


$admin = $_SESSION['admin'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="Preskool - Bootstrap Admin Template">
	<meta name="keywords" content="admin, estimates, bootstrap, business, html5, responsive, Projects">
	<meta name="author" content="Dreams technologies - Bootstrap Admin Template">
	<meta name="robots" content="noindex, nofollow">
	<title>Preskool Admin Template</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

	<!-- Theme Script js -->
	<script src="assets/js/theme-script.js"></script>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">

	 <!-- Feather CSS -->
	 <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">

	 <!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">

	<!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
<style>
  /* Modal dialog width */
#historiqueModal .modal-dialog.modal-lg {
  max-width: 600px; /* your preferred size */
}

/* Modal content padding */
#historiqueModal .modal-content {
  padding: 0.25rem 0.5rem;
}

/* Modal header padding */
#historiqueModal .modal-header {
  padding: 0.25rem 0.5rem 0.25rem 0.5rem; /* top, right, bottom, left */
  border-bottom: 1px solid #dee2e6;
}

/* Modal footer padding */
#historiqueModal .modal-footer {
  padding: 0.5rem 1rem;
}

/* Modal body padding */
#historiqueModal .modal-body {
  padding: 0.5rem 1rem;
}

/* Table styles */
#historiqueContent table {
  width: auto;
  margin: 0 auto;
  font-size: 0.9rem;
  border-collapse: collapse;
}

/* Table header and cell styles */
#historiqueContent table th,
#historiqueContent table td {
  padding: 4px 8px;
  white-space: nowrap;
  vertical-align: middle;
  border: 1px solid #dee2e6; /* optional border */
}

/* First column (status icon) */
#historiqueContent table th:first-child,
#historiqueContent table td:first-child {
  width: 70px;
  padding-left: 10px;
  padding-right: 10px;
  text-align: center;
}


/* Style the custom select elements */
.custom-select {
    min-width: 150px !important;
    font-size: 1rem;
    height: 50px; /* Slightly taller select box */
}
.card-hidden {
    display: none !important;
}

	</style>

	<!-- Main Wrapper -->
        <div class="main-wrapper">

          <!-- Header -->
          <div class="header">

            <!-- Logo -->
            <div class="header-left active" style="display: flex; align-items: center; gap: 10px;">
          <a href="societe.php" class="logo logo-normal" style="display: flex; align-items: center;">
              <?php if (!empty($_SESSION['societe_logo'])): ?>
                  <img
        src="<?= htmlspecialchars('/stage/' . $_SESSION['societe_logo']) ?>"
        alt="<?= !empty($_SESSION['societe_nom']) ? htmlspecialchars($_SESSION['societe_nom']) . ' Logo' : 'Société Logo' ?>"
        style="max-height: 50px; object-fit: contain;"
        onerror="this.style.display='none';"
      />
              <?php else: ?>
                
              <?php endif; ?>
          </a>
      </div>

			<!-- /Logo -->

			<a id="mobile_btn" class="mobile_btn" href="#sidebar">
				<span class="bar-icon">
					<span></span>
					<span></span>
					<span></span>
				</span>
			</a>

			<div class="header-user">
				<div class="nav user-menu">
					
					<!-- Search -->
					<div class="nav-item nav-search-inputs me-auto">
						<div class="top-nav-search">
							<a href="javascript:void(0);" class="responsive-search">
								<i class="fa fa-search"></i>
							</a>
							<form action="#" class="dropdown">
								<div class="searchinputs" id="dropdownMenuClickable">
									<input type="text" placeholder="Search">
									<div class="search-addon">
										<button type="submit"><i class="ti ti-command"></i></button>
									</div>
								</div>
							</form>
						</div>
					</div>
					<!-- /Search -->

					<div class="d-flex align-items-center">
						<div class="dropdown me-2">
							<a href="#" class="btn btn-outline-light fw-normal bg-white d-flex align-items-center p-2"  data-bs-toggle="dropdown" aria-expanded="false">
								<i class="ti ti-calendar-due me-1"></i>Academic Year : 2024 / 2025
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
									Academic Year : 2023 / 2024
								</a>
								<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
									Academic Year : 2022 / 2023
								</a>
								<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
									Academic Year : 2021 / 2022
								</a>
							</div>
						</div>
						<div class="pe-1 ms-1">
							<div class="dropdown">
								<a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center me-1 p-2"  data-bs-toggle="dropdown" aria-expanded="false">
									<img src="assets/img/flags/us.png" alt="Language" class="img-fluid rounded-pill">
								</a>
								<div class="dropdown-menu dropdown-menu-right">
									<a href="javascript:void(0);" class="dropdown-item active d-flex align-items-center">
										<img class="me-2 rounded-pill" src="assets/img/flags/us.png" alt="Img" height="22" width="22"> English
									</a>
									<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
										<img class="me-2 rounded-pill" src="assets/img/flags/fr.png" alt="Img" height="22" width="22"> French
									</a>
									<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
										<img class="me-2 rounded-pill" src="assets/img/flags/es.png" alt="Img" height="22" width="22"> Spanish
									</a>
									<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
										<img class="me-2 rounded-pill" src="assets/img/flags/de.png" alt="Img" height="22" width="22"> German
									</a>
								</div>
							</div>
						</div>
						<div class="pe-1">
							<div class="dropdown">
								<a href="#" class="btn btn-outline-light bg-white btn-icon me-1"  data-bs-toggle="dropdown" aria-expanded="false">
									<i class="ti ti-square-rounded-plus"></i>
								</a>
								<div class="dropdown-menu dropdown-menu-right border shadow-sm dropdown-md">
									<div class="p-3 border-bottom">
										<h5>Add New</h5>
									</div>
									<div class="p-3 pb-0">
										<div class="row gx-2">
											<div class="col-6">
												<a href="add-student.html" class="d-block bg-primary-transparent ronded p-2 text-center mb-3 class-hover">
													<div class="avatar avatar-lg mb-2">
														<span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-primary rounded-circle"><i class="ti ti-school"></i></span>
													</div>
													<p class="text-dark">Students</p>
												</a>
											</div>
											<div class="col-6">
												<a href="add-teacher.html" class="d-block bg-success-transparent ronded p-2 text-center mb-3 class-hover">
													<div class="avatar avatar-lg mb-2">
														<span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-success rounded-circle"><i class="ti ti-users"></i></span>
													</div>
													<p class="text-dark">Teachers</p>
												</a>
											</div>
											<div class="col-6">
												<a href="add-staff.html" class="d-block bg-warning-transparent ronded p-2 text-center mb-3 class-hover">
													<div class="avatar avatar-lg rounded-circle mb-2">
														<span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-warning rounded-circle"><i class="ti ti-users-group"></i></span>
													</div>	
													<p class="text-dark">Staffs</p>
												</a>
											</div>
											<div class="col-6">
												<a href="add-invoice.html" class="d-block bg-info-transparent ronded p-2 text-center mb-3 class-hover">
													<div class="avatar avatar-lg mb-2">
														<span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-info rounded-circle"><i class="ti ti-license"></i></span>
													</div>
													<p class="text-dark">Invoice</p>
												</a>
											</div>
										</div>
									</div>											
								</div>
							</div>		
						</div>
						<div class="pe-1">
							<a href="#" id="dark-mode-toggle" class="dark-mode-toggle activate btn btn-outline-light bg-white btn-icon me-1">
								<i class="ti ti-moon"></i>
							</a>
							<a href="#" id="light-mode-toggle" class="dark-mode-toggle btn btn-outline-light bg-white btn-icon me-1">
								<i class="ti ti-brightness-up"></i>
							</a>
						</div>
						<div class="pe-1" id="notification_item">
							<a href="#" class="btn btn-outline-light bg-white btn-icon position-relative me-1" id="notification_popup">
								<i class="ti ti-bell"></i>
								<span class="notification-status-dot"></span>
							</a>
							<div class="dropdown-menu dropdown-menu-end notification-dropdown p-4">
								<div class="d-flex align-items-center justify-content-between border-bottom p-0 pb-3 mb-3">
									<h4 class="notification-title">Notifications (2)</h4>
									<div class="d-flex align-items-center">
										<a href="#" class="text-primary fs-15 me-3 lh-1">Mark all as read</a>
										<div class="dropdown">
											<a href="javascript:void(0);" class="bg-white dropdown-toggle"
												data-bs-toggle="dropdown"><i class="ti ti-calendar-due me-1"></i>Today
											</a>
											<ul class="dropdown-menu mt-2 p-3">
												<li>
													<a href="javascript:void(0);" class="dropdown-item rounded-1">
														This Week
													</a>
												</li>
												<li>
													<a href="javascript:void(0);" class="dropdown-item rounded-1">
														Last Week
													</a>
												</li>
												<li>
													<a href="javascript:void(0);" class="dropdown-item rounded-1">
														Last Week
													</a>
												</li>
											</ul>
										</div>
									</div>
								</div>
	
								<div class="noti-content">
									<div class="d-flex flex-column">
										<div class="border-bottom mb-3 pb-3">
											<a href="activities.html">
												<div class="d-flex">
													<span class="avatar avatar-lg me-2 flex-shrink-0">
														<img src="assets/img/profiles/avatar-27.jpg" alt="Profile">
													</span>
													<div class="flex-grow-1">
														<p class="mb-1"><span class="text-dark fw-semibold">Shawn</span> performance in Math is
															below the threshold.</p>
														<span>Just Now</span>
													</div>
												</div>
											</a>
										</div>
										<div class="border-bottom mb-3 pb-3">
											<a href="activities.html" class="pb-0">
												<div class="d-flex">
													<span class="avatar avatar-lg me-2 flex-shrink-0">
														<img src="assets/img/profiles/avatar-23.jpg" alt="Profile">
													</span>
													<div class="flex-grow-1">
														<p class="mb-1"><span class="text-dark fw-semibold">Sylvia</span> added appointment on
															02:00 PM</p>
														<span>10 mins ago</span>
														<div class="d-flex justify-content-start align-items-center mt-1">
															<span class="btn btn-light btn-sm me-2">Deny</span>
															<span class="btn btn-primary btn-sm">Approve</span>
														</div>
													</div>
												</div>
											</a>					
										</div>
										<div class="border-bottom mb-3 pb-3">
											<a href="activities.html">
												<div class="d-flex">
													<span class="avatar avatar-lg me-2 flex-shrink-0">
														<img src="assets/img/profiles/avatar-25.jpg" alt="Profile">
													</span>
													<div class="flex-grow-1">
														<p class="mb-1">New student record <span class="text-dark fw-semibold"> George</span> is
															created by <span class="text-dark fw-semibold"> Teressa</span></p>
														<span>2 hrs ago</span>
													</div>
												</div>
											</a>
										</div>
										<div class="border-0 mb-3 pb-0">
											<a href="activities.html">
												<div class="d-flex">
													<span class="avatar avatar-lg me-2 flex-shrink-0">
														<img src="assets/img/profiles/avatar-01.jpg" alt="Profile">
													</span>
													<div class="flex-grow-1">
														<p class="mb-1">A new teacher record for <span class="text-dark fw-semibold">Elisa</span>
														</p>
														<span>09:45 AM</span>
													</div>
												</div>
											</a>
										</div>
									</div>
								</div>
								<div class="d-flex p-0">
									<a href="#" class="btn btn-light w-100 me-2">Cancel</a>
									<a href="activities.html" class="btn btn-primary w-100">View All</a>
								</div>
							</div>
						</div>
						<div class="pe-1">
							<a href="chat.html" class="btn btn-outline-light bg-white btn-icon position-relative me-1">
								<i class="ti ti-brand-hipchat"></i>
								<span class="chat-status-dot"></span>
							</a>
						</div>
						<div class="pe-1">
							<a href="#" class="btn btn-outline-light bg-white btn-icon me-1">
								<i class="ti ti-chart-bar"></i>
							</a>
						</div>
						<div class="pe-1">
							<a href="#" class="btn btn-outline-light bg-white btn-icon me-1" id="btnFullscreen">
								<i class="ti ti-maximize"></i>
							</a>
						</div>
						<div class="dropdown ms-1">
							<a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
								<span class="avatar avatar-md rounded">
                <img src="<?= htmlspecialchars('/stage/controller/' . $_SESSION['admin']['image']) ?>" alt="Profile Picture" />
								</span>
							</a>
							<div class="dropdown-menu">
								<div class="d-block">
                <div class="d-flex align-items-center p-2">
            <span class="avatar avatar-md me-2 online avatar-rounded">
            <img src="<?= htmlspecialchars('/stage/controller/' . $_SESSION['admin']['image']) ?>" alt="Profile Picture" />

            </span>
            <div>
                <h6 class=""><?= 
                    htmlspecialchars($_SESSION['admin']['last_name'] ?? 'User'); ?></h6>
                <p class="text-primary mb-0">Administrator</p>
            </div>
      </div>
									<hr class="m-0">
									<a class="dropdown-item d-inline-flex align-items-center p-2" href="profile.php"> <i class="ti ti-user-circle me-2"></i>My Profile</a>
                  <a class="dropdown-item d-inline-flex align-items-center p-2"
                                    href="#"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#theme-setting">
                                    <i class="ti ti-settings me-2"></i>Settings
                                    </a>
                          <hr class="m-0">
<a class="dropdown-item d-inline-flex align-items-center p-2" href="login.php?logout=true">
  <i class="ti ti-login me-2"></i>Logout
</a>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<!-- Mobile Menu -->
			<div class="dropdown mobile-user-menu">
				<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
				<div class="dropdown-menu dropdown-menu-end">
					<a class="dropdown-item" href="profile.php">My Profile</a>
					<a class="dropdown-item" href="profile-settings.html">Settings</a>
					<a class="dropdown-item" href="login.php">Logout</a>
				</div>
			</div>
			<!-- /Mobile Menu -->

		</div>
		<!-- /Header -->

		<!-- Sidebar -->
		<div class="sidebar" id="sidebar">
			<div class="sidebar-inner slimscroll">
				<div id="sidebar-menu" class="sidebar-menu">
					
					<ul>
          <li>
							<h6 class="submenu-hdr"><span>Main</span></h6>
							<ul>
								<li class="submenu">
                                <a href="javascript:void(0);"><i class="ti ti-layout-dashboard"></i><span>Dashboard</span><span class="menu-arrow"></span></a>
									<ul>
										<li><a href="dashboard.php">Student Dashboard</a></li>
									</ul>
								</li>
							
							</ul>
						</li>
						
						
						<li>
							<h6 class="submenu-hdr"><span>Peoples</span></h6>
							<ul>
								<li class="submenu">
								<a href="javascript:void(0);"><i class="ti ti-user"></i><span>Students</span><span class="menu-arrow"></span></a>

									<ul>
										<li><a href="liststudent.php">All Students</a></li>
										<li><a href="addstudent.php"><i class="ti ti-user-plus"></i>Add Student</a></li>
										
									</ul>
								</li>
								
								
								<li class="submenu">
								<a href="javascript:void(0);"><i class="ti ti-user"></i><span>Teachers</span><span class="menu-arrow"></span></a>
									<ul>
										<li><a href="addenseignant.php">All Teachers</a></li>
									
									</ul>
								</li>
							</ul>
						</li>
						<li>
							<h6 class="submenu-hdr"><span>Academic</span></h6>
							<ul>
							<li class="submenu">
                                    <a href="javascript:void(0);"><i class="ti ti-list-numbers"></i></i><span>Levels</span><span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="addlevel.php">All Levels</a></li>
                                    </ul>
                                </li>
								<li class="submenu">
                                <a href="javascript:void(0);"><i class="ti ti-chalkboard"></i><span>Classes</span><span class="menu-arrow"></span></a>
									<ul>
										<li><a href="addclass.php">All Classes</a></li>
										<li><a href="addemploi.php"><i class="ti ti-calendar-event"></i>Schedule</a></li>
									</ul>
								</li>
								<li><a href="addclassroom.php"><i class="ti ti-building"></i><span>Class Room</span></a></li>
							 <li><a href="addmatier.php"><i class="ti ti-book"></i><span>Subject</span></a></li>
                             <h6 class="submenu-hdr"><span>MODULE COMPTABILITÉ</span></h6>
							<ul>
                            <li><a href="listpaiement.php"><i class="ti ti-book"></i><span>Paiement</span></a></li>
                            <li><a href="addcharge.php"><i class="ti ti-book"></i><span>Charge</span></a></li>
  
                          </ul>
        </li>
        <h6 class="submenu-hdr"><span>Settings</span></h6>

        <li>
          <a href="addsociete.php"><i class="ti ti-building"></i><span>Société</span></a>
        </li>

      </ul>
				
				</div>
			</div>
		</div>
		<!-- /Sidebar -->
        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between border-bottom pb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Profile</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Profile</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="pe-1 mb-2">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="row">
                    <div class="col-lg-4 col-md-5">
                        <div class="card mb-4">
                            <div class="card-body text-center">
                                <div class="avatar avatar-xl mb-3">
                                <img src="<?= htmlspecialchars('/stage/controller/' . $_SESSION['admin']['image']) ?>" alt="Profile Picture" />
                                </div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($admin['first_name'] ?? 'Kevin') . ' ' . htmlspecialchars($admin['last_name'] ?? 'Larry'); ?></h5>
                                <p class="text-muted mb-3">Administrator</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-7">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">First Name</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($admin['first_name'] ?? 'Kevin'); ?></dd>
                                    <dt class="col-sm-4">Last Name</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($admin['last_name'] ?? 'Larry'); ?></dd>
                                    
                                    <dt class="col-sm-4">Email</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($admin['email'] ?? 'kevin@example.com'); ?></dd>
                                    <dt class="col-sm-4">Phone</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($admin['phone'] ?? '+1 123-456-7890'); ?></dd>
                                </dl>
                               
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Address Information</h5>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Address</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($admin['address'] ?? '123 Main St'); ?></dd>
                                    <dt class="col-sm-4">Country</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($admin['country'] ?? 'United States'); ?></dd>
                                    <dt class="col-sm-4">State/Province</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($admin['state'] ?? 'California'); ?></dd>
                                    <dt class="col-sm-4">City</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($admin['city'] ?? 'San Francisco'); ?></dd>
                                    <dt class="col-sm-4">Postal Code</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($admin['postal_code'] ?? '94105'); ?></dd>
                                </dl>
                                
                            </div>
                        </div>
                       
                    </div>
                </div>
                <!-- /Profile Content -->
            </div>
        </div>
        <!-- /Page Wrapper -->

        <!-- Edit Personal Information Modal -->
       <!-- After all profile info cards, add this: -->
       <div class="text-center mt-4">
  <button id="editProfileBtn" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
    Edit Profile
  </button>
  <button id="deleteProfileBtn" class="btn btn-danger">
    Delete Profile
  </button>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <ul class="nav nav-tabs" id="editProfileTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">Personal Info</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab" aria-controls="address" aria-selected="false">Address Info</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Change Password</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="picture-tab" data-bs-toggle="tab" data-bs-target="#picture" type="button" role="tab" aria-controls="picture" aria-selected="false">Profile Picture</button>
          </li>
        </ul>

        <div class="tab-content pt-3" id="editProfileTabsContent">

          <!-- Personal Info Form -->
          <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
            <form id="personalInfoForm" novalidate>
              <div class="mb-3">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($admin['first_name'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($admin['last_name'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>">
              </div>
              <button type="submit" class="btn btn-primary">Save Personal Info</button>
            </form>
          </div>

          <!-- Address Info Form -->
          <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
            <form id="addressInfoForm" novalidate>
              <div class="mb-3">
                <label>Address</label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($admin['address'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label>Country</label>
                <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($admin['country'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label>State/Province</label>
                <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($admin['state'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($admin['city'] ?? '') ?>">
              </div>
              <div class="mb-3">
                <label>Postal Code</label>
                <input type="text" name="postal_code" class="form-control" value="<?= htmlspecialchars($admin['postal_code'] ?? '') ?>">
              </div>
              <button type="submit" class="btn btn-primary">Save Address Info</button>
            </form>
          </div>

          <!-- Change Password Form -->
          <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
          <span class="ti toggle-password ti-eye-off"></span>

          <form id="changePasswordForm" novalidate>
              <div class="mb-3">
                <label>Current Password</label>
                <input type="password" name="current_password" class="pass-input form-control">
              </div>
              <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="new_password" class="pass-input form-control">
              </div>
              <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="pass-input form-control">
              </div>
              <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
          </div>

          <!-- Profile Picture Form -->
          <div class="tab-pane fade" id="picture" role="tabpanel" aria-labelledby="picture-tab">
            <form id="profilePictureForm" enctype="multipart/form-data" novalidate>
              <input type="hidden" name="action" value="updateAdmin">
              <input type="hidden" name="id" value="<?= htmlspecialchars($admin['id'] ?? '') ?>">
              <div class="mb-3 text-center">
  <div style="max-width: 300px; margin: auto;">
    <img id="profilePicturePreview" 
         src="<?= htmlspecialchars('/stage/controller/' . ($_SESSION['admin']['image'] ?? 'default.png')) ?>" 
         alt="Preview" 
         style="width: 100%; max-height: 300px;">
  </div>
</div>

              <div class="mb-3">
                <label for="profile_picture" class="form-label">Upload New Profile Picture</label>
                <input type="file" name="picture" id="profile_picture" accept="image/*" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary">Upload Picture</button>
            </form>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const adminId = <?= json_encode($admin['id'] ?? null) ?>;

  async function postJSON(url = '', data = {}) {
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    return response.json();
  }

  // Personal Info Form
  document.getElementById('personalInfoForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = e.target;
    const data = {
      action: 'updateAdmin',
      id: adminId,
      first_name: form.first_name.value.trim(),
      last_name: form.last_name.value.trim(),
      email: form.email.value.trim(),
      phone: form.phone.value.trim()
    };
    try {
      const res = await postJSON('../../controller/adminController.php', data);
      alert(res.message || 'Personal info updated!');
    } catch {
      alert('Error updating personal info');
    }
  });

  // Address Info Form
  document.getElementById('addressInfoForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = e.target;
    const data = {
      action: 'updateAdmin',
      id: adminId,
      address: form.address.value.trim(),
      country: form.country.value.trim(),
      state: form.state.value.trim(),
      city: form.city.value.trim(),
      postal_code: form.postal_code.value.trim()
    };
    try {
      const res = await postJSON('../../controller/adminController.php', data);
      alert(res.message || 'Address info updated!');
    } catch {
      alert('Error updating address info');
    }
  });

  // Change Password Form
  document.getElementById('changePasswordForm').addEventListener('submit', async e => {
  e.preventDefault();
  const form = e.target;
  const data = {
    action: 'updateAdmin',
    id: adminId,
    current_password: form.current_password.value,
    new_password: form.new_password.value,
    confirm_password: form.confirm_password.value
  };

  console.log("Password update data:", data); // debug console log

  try {
    const res = await postJSON('../../controller/adminController.php', data);
    console.log("Response:", res); // debug response
    alert(res.message || 'Password change result');
    if (res.status === 'success') form.reset();
  } catch {
    alert('Error changing password');
  }
});


  // Profile Picture Form (multipart/form-data)
let cropper;

const pictureInput = document.getElementById('profile_picture');
const picturePreview = document.getElementById('profilePicturePreview');

pictureInput.addEventListener('change', () => {
  const file = pictureInput.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      picturePreview.src = e.target.result;

      // Destroy old cropper if exists
      if (cropper) cropper.destroy();

      // Wait for image to load, then initialize cropper
      picturePreview.onload = () => {
        cropper = new Cropper(picturePreview, {
  aspectRatio: NaN, // Allow any aspect ratio (optional: use 1 for square)
  viewMode: 1,
  autoCrop: true,
  autoCropArea: 1, // ← This makes the crop box cover the full image
  zoomable: true,
  scalable: true,
  movable: true,
  cropBoxMovable: false,
  cropBoxResizable: false // prevent user from resizing the crop box
});

      };
    };
    reader.readAsDataURL(file);
  }
});

// Modify the form submission to send the cropped image
document.getElementById('profilePictureForm').addEventListener('submit', async e => {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData();

  formData.set('action', 'updateAdmin');
  formData.set('id', <?= json_encode($admin['id'] ?? null) ?>);

  // Crop the image before uploading
  if (cropper) {
    const canvas = cropper.getCroppedCanvas({
      width: 300,
      height: 300
    });

    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg'));
    formData.append('picture', blob, 'profile.jpg');
  }

  try {
    const response = await fetch('../../controller/adminController.php', {
      method: 'POST',
      body: formData
    });

    const res = await response.json();
    alert(res.message || 'Profile picture updated!');
    if (res.status === 'success' && res.imageUrl) {
      const img = document.getElementById('profilePicturePreview');
      img.src = res.imageUrl + '?' + new Date().getTime(); // force reload
    }

  } catch (err) {
    console.error(err);
    alert('Error uploading profile picture');
  }
});

  // Delete Profile button
  document.getElementById('deleteProfileBtn').addEventListener('click', async () => {
    if (!confirm('Are you sure you want to delete your profile? This cannot be undone.')) return;
    try {
      const res = await postJSON('../../controller/adminController.php', {
        action: 'deleteAdmin',
        id: adminId
      });
      if (res.status === 'success') {
        alert('Profile deleted successfully.');
        window.location.href = 'logout.php';
      } else {
        alert(res.message || 'Failed to delete profile.');
      }
    } catch {
      alert('Error deleting profile.');
    }
  });
});
</script>

    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Daterangepicker JS -->
    <script src="assets/js/moment.js"></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js"></script>

    <!-- Datetimepicker JS -->
    <script src="assets/plugins/moment/moment.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Feather Icon JS -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Slimscroll JS -->
    <script src="assets/js/jquery.slimscroll.min.js"></script>

    <!-- Datatable JS -->
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <!-- Cropper.js CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<!-- Cropper.js JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">

</body>
</html>