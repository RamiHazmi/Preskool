<?php
require_once __DIR__ . '/../../controller/session_config.php';  
if (!isset($_SESSION['admin'])) {
  // Not logged in, redirect to login
  header('Location: login.php');
  exit;
}
$admin = $_SESSION['admin'];





include_once __DIR__ . '/../../controller/ClassController.php';

$controller = new ControllerClass();
$classes  = $controller->listClasses();
include_once __DIR__ . '/../../controller/levelController.php';

$controller = new ControllerLevel();
$levels = $controller->listLevels();
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

	<!-- Theme Script -->
	<script src="assets/js/theme-script.js"></script>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">

	<!-- Feather CSS -->
	<link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">

	<!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

	<!-- Datatable CSS -->
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
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
            <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>
            <div class="header-user">
                <div class="nav user-menu">
                    <div class="nav-item nav-search-inputs me-auto">
                        <div class="top-nav-search">
                            <a href="javascript:void(0);" class="responsive-search">
                                <i class="fa fa-search"></i>
                            </a>
                            <form action="#" class="dropdown">
                                <div class="searchinputs" id="dropdownMenuClickable">
                                    <input type="text" placeholder="Search">
                                    <div class="search-addon">
                                        <button type="submit"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="dropdown me-2">
                            <a href="#" class="btn btn-outline-light fw-normal bg-white d-flex align-items-center p-2" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-calendar-alt me-1"></i>Academic Year : 2024 / 2025
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
                                <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center me-1 p-2" data-bs-toggle="dropdown" aria-expanded="false">
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
                                <a href="#" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-plus"></i>
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
                                                        <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-primary rounded-circle"><i class="fas fa-school"></i></span>
                                                    </div>
                                                    <p class="text-dark">Students</p>
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="add-teacher.html" class="d-block bg-success-transparent ronded p-2 text-center mb-3 class-hover">
                                                    <div class="avatar avatar-lg mb-2">
                                                        <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-success rounded-circle"><i class="fas fa-users"></i></span>
                                                    </div>
                                                    <p class="text-dark">Teachers</p>
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="add-staff.html" class="d-block bg-warning-transparent ronded p-2 text-center mb-3 class-hover">
                                                    <div class="avatar avatar-lg rounded-circle mb-2">
                                                        <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-warning rounded-circle"><i class="fas fa-user-friends"></i></span>
                                                    </div>
                                                    <p class="text-dark">Staffs</p>
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="add-invoice.html" class="d-block bg-info-transparent ronded p-2 text-center mb-3 class-hover">
                                                    <div class="avatar avatar-lg mb-2">
                                                        <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-info rounded-circle"><i class="fas fa-file-invoice"></i></span>
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
                                <i class="fas fa-moon"></i>
                            </a>
                            <a href="#" id="light-mode-toggle" class="dark-mode-toggle btn btn-outline-light bg-white btn-icon me-1">
                                <i class="fas fa-sun"></i>
                            </a>
                        </div>
                        <div class="pe-1" id="notification_item">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon position-relative me-1" id="notification_popup">
                                <i class="fas fa-bell"></i>
                                <span class="notification-status-dot"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown p-4">
                                <div class="d-flex align-items-center justify-content-between border-bottom p-0 pb-3 mb-3">
                                    <h4 class="notification-title">Notifications (2)</h4>
                                    <div class="d-flex align-items-center">
                                        <a href="#" class="text-primary fs-15 me-3 lh-1">Mark all as read</a>
                                        <div class="dropdown">
                                            <a href="javascript:void(0);" class="bg-white dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-calendar-alt me-1"></i>Today</a>
                                            <ul class="dropdown-menu mt-2 p-3">
                                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1">This Week</a></li>
                                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Last Week</a></li>
                                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Last Month</a></li>
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
                                                        <p class="mb-1"><span class="text-dark fw-semibold">Shawn</span> performance in Math is below the threshold.</p>
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
                                                        <p class="mb-1"><span class="text-dark fw-semibold">Sylvia</span> added appointment on 02:00 PM</p>
                                                        <span>10 mins ago</span>
                                                        <div class="d-flex justify-content-start align-items-center mt-1">
                                                            <span class="btn btn-light btn-sm me-2">Deny</span>
                                                            <span class="btn btn-primary btn-sm">Approve</span>
                                                        </div>
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
                                <i class="fab fa-hipchat"></i>
                                <span class="chat-status-dot"></span>
                            </a>
                        </div>
                        <div class="pe-1">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon me-1">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                        </div>
                        <div class="pe-1">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon me-1" id="btnFullscreen">
                                <i class="fas fa-expand"></i>
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
                                    <a class="dropdown-item d-inline-flex align-items-center p-2" href="profile.php"><i class="fas fa-user-circle me-2"></i>My Profile</a>
                                    <a class="dropdown-item d-inline-flex align-items-center p-2"
                                    href="#"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#theme-setting">
                                    <i class="ti ti-settings me-2"></i>Settings
                                    </a>                                    <hr class="m-0">
                                    <a class="dropdown-item d-inline-flex align-items-center p-2"  href="login.php?logout=true"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dropdown mobile-user-menu">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="profile-settings.html">Settings</a>
                    <a class="dropdown-item"  href="login.php?logout=true">Logout</a>
                </div>
            </div>
        </div>
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
									<a href="javascript:void(0);" ><i class="ti ti-user"></i><span>Teachers</span><span class="menu-arrow"></span></a>
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
               <a href="javascript:void(0);"><i class="ti ti-list-numbers"></i><span>Levels</span><span class="menu-arrow"></span></a>
               <ul>
                    <li><a href="addlevel.php">All Levels</a></li>
                </ul>
                 </li>
								<li class="submenu">
                <a href="javascript:void(0);"  class="subdrop active"><i class="ti ti-chalkboard"></i><span>Classes</span><span class="menu-arrow"></span></a>
									<ul>
										<li><a href="addclass.php" class="active">All Classes</a></li>
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
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Classes List</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.html">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Classes</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">All Classes</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="pe-1 mb-2">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh">
                                <i class="fas fa-refresh"></i>
                            </a>
                        </div>
                        <div class="pe-1 mb-2">
                            <button type="button" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Print" data-bs-original-title="Print">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                        <div class="dropdown me-2 mb-2">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-light fw-medium d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="fas fa-file-export me-2"></i>Export
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="fas fa-file-pdf me-1"></i>Export as PDF</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="fas fa-file-excel me-1"></i>Export as Excel</a></li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_class"><i class="fas fa-plus-square me-2"></i>Add Class</a>
                        </div>
                    </div>
                </div>
                <!-- Classes List -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Classes List</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="input-icon-start mb-3 me-2 position-relative">
                                <span class="icon-addon">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <input type="text" class="form-control date-range bookingrange" placeholder="Select" value="Academic Year : 2024 / 2025">
                            </div>
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fas fa-filter me-2"></i>Filter</a>
                                <div class="dropdown-menu drop-width">
                                    <form action="classes.html">
                                        <div class="d-flex align-items-center border-bottom p-3">
                                            <h4>Filter</h4>
                                        </div>
                                        <div class="p-3 border-bottom pb-0">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Class Name</label>
                                                        <input type="text" class="form-control" name="class_name" placeholder="Enter Class Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Level</label>
                                                        <select class="select" name="level_id">
                                                            <option value="">Select</option>
                                                            <option value="L1">Level I</option>
                                                            <option value="L2">Level II</option>
                                                            <option value="L3">Level III</option>
                                                        </select>
                                                    </div>
                                                </div>
                                               
                                            </div>
                                        </div>
                                        <div class="p-3 d-flex align-items-center justify-content-end">
                                            <a href="#" class="btn btn-light me-3">Reset</a>
                                            <button type="submit" class="btn btn-primary">Apply</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="dropdown mb-3">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-sort-alpha-down me-2"></i>Sort by A-Z</a>
                                <ul class="dropdown-menu p-3">
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 active">Ascending</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Descending</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Recently Viewed</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Recently Added</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Class Name</th>
                                        <th>Level</th>
                                        <th>No of Students</th>
                                        <th>Year</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php foreach ($classes as $class): ?>
<tr>
    <td>
        <div class="form-check form-check-md">
            <input class="form-check-input" type="checkbox" name="select-class[]" value="<?= htmlspecialchars($class['id']) ?>">
        </div>
    </td>
    <td><?= htmlspecialchars($class['id']) ?></td>
    <td><?= htmlspecialchars($class['class_name']) ?></td>
    <td><?= htmlspecialchars($class['level_name']) ?></td>
    <td><?= htmlspecialchars($class['number_of_students']) ?></td>
    <td><?= htmlspecialchars($class['year']) ?></td>
    <td>
        <button 
            class="btn btn-sm btn-primary btn-edit-class" 
            data-id="<?= htmlspecialchars($class['id']) ?>" 
            data-class_name="<?= htmlspecialchars($class['class_name']) ?>" 
            data-level_id="<?= htmlspecialchars($class['level_id']) ?>" 
            data-number_of_students="<?= htmlspecialchars($class['number_of_students']) ?>" 
            data-year="<?= htmlspecialchars($class['year']) ?>"
        >Edit</button>

        <button 
            class="btn btn-sm btn-danger btn-delete-class" 
            data-id="<?= htmlspecialchars($class['id']) ?>"
        >Delete</button>
    </td>
</tr>
<?php endforeach; ?>
</tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     <!-- ... (Header, Sidebar, Page Wrapper, Classes List, and other sections) -->

<!-- Add Class Modal -->
<!-- ... (Header, Sidebar, Page Wrapper, Classes List, and other sections) -->

<!-- Add Class Modal -->
<div class="modal fade" id="add_class" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Class</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addClassForm" method="post" action="/STAGE/controller/ClassController.php">
        <input type="hidden" name="action" value="addClass" />
        <div class="modal-body">
          <div class="mb-3">
            <label for="levelSelect" class="form-label">Level</label>
            <select id="levelSelect" class="select" name="level_id" required>
  <option value="">Select Level</option>
  <?php 
  usort($levels, function($a, $b) {
      return strcmp($a['name'], $b['name']);
  });

  foreach ($levels as $level): ?>
    <option value="<?= htmlspecialchars($level['id']) ?>" data-level-name="<?= htmlspecialchars($level['name']) ?>">
      <?= htmlspecialchars($level['name']) ?>
    </option>
  <?php endforeach; ?>
</select>

          </div>
          <div class="mb-3">
            <label for="classNameInput" class="form-label">Class Name</label>
            <input type="text" class="form-control" name="class_name" id="classNameInput" placeholder="Select a level first" required autocomplete="off" disabled />
          </div>
          <div id="message" style="margin-top: 10px; display: none;"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 310px;">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Class</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editClassForm" method="post" action="/STAGE/controller/ClassController.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editClassLabel">Edit Class</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="updateClass" />
          <input type="hidden" name="id" id="editClassId" />
          <div class="mb-3">
            <label for="editClassName" class="form-label">Class Name</label>
            <input type="text" class="form-control" id="editClassName" name="class_name" placeholder="Select a level first" required autocomplete="off" disabled />
          </div>
          <div class="mb-3">
            <label for="editLevelSelect" class="form-label">Level</label>
            <select id="editLevelSelect" class="select" name="level_id" required>
              <option value="">Select Level</option>
              <?php foreach ($levels as $level): ?>
                <option value="<?= htmlspecialchars($level['id']) ?>" data-level-name="<?= htmlspecialchars($level['name']) ?>">
                  <?= htmlspecialchars($level['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="editNumberOfStudents" class="form-label">Number of Students</label>
            <input type="number" readonly class="form-control" id="editNumberOfStudents" name="number_of_students" />
          </div>
          <div class="mb-3">
            <label for="editYear" class="form-label">Year</label>
            <input type="number" readonly class="form-control" id="editYear" name="year" />
          </div>
          
          <div id="editMessage" style="margin-top: 10px;"></div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"style="margin-right: 285px;">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Class Modal -->
<div class="modal fade" id="deleteClassModal" tabindex="-1" aria-labelledby="deleteClassLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="deleteClassForm" method="post" action="/STAGE/controller/ClassController.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteClassLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="deleteClass" />
          <input type="hidden" name="id" id="deleteClassId" />
          <p>Are you sure you want to delete this class?</p>
          <div id="deleteMessage" style="margin-top: 10px;"></div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 274px;">No, Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Debug modal elements
  const addClassModal = document.getElementById('add_class');
  const editClassModal = document.getElementById('editClassModal');
  const deleteClassModal = document.getElementById('deleteClassModal');
  if (!addClassModal) console.error('add_class modal not found in DOM');
  if (!editClassModal) console.error('editClassModal not found in DOM');
  if (!deleteClassModal) console.error('deleteClassModal not found in DOM');

  const addModal = addClassModal ? new bootstrap.Modal(addClassModal) : null;
  const editModal = editClassModal ? new bootstrap.Modal(editClassModal) : null;
  const deleteModal = deleteClassModal ? new bootstrap.Modal(deleteClassModal) : null;

  // Handle Edit button click
  const editButtons = document.querySelectorAll('.btn-edit-class');
  console.log('Edit buttons found:', editButtons.length);
  editButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      if (!editModal) {
        console.error('Edit modal not initialized');
        return;
      }
      const id = btn.dataset.id;
      const className = btn.dataset.class_name;
      const levelId = btn.dataset.level_id;
      const numberOfStudents = btn.dataset.number_of_students;
      const year = btn.dataset.year;

      console.log('Edit clicked:', { id, className, levelId, numberOfStudents, year });

      document.getElementById('editClassId').value = id;
      document.getElementById('editClassName').value = className;
      document.getElementById('editLevelSelect').value = levelId;
      document.getElementById('editNumberOfStudents').value = numberOfStudents;
      document.getElementById('editYear').value = year;
      document.getElementById('editClassName').disabled = true;

      editModal.show();
    });
  });

  // Handle Delete button click
  const deleteButtons = document.querySelectorAll('.btn-delete-class');
  console.log('Delete buttons found:', deleteButtons.length);
  deleteButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      if (!deleteModal) {
        console.error('Delete modal not initialized');
        return;
      }
      const id = btn.dataset.id;
      console.log('Delete clicked:', { id });

      document.getElementById('deleteClassId').value = id;
      document.getElementById('deleteMessage').textContent = '';
      deleteModal.show();
    });
  });

  // Add Class form submission with fetch AJAX
  const addClassForm = document.getElementById('addClassForm');
  const addMessageDiv = document.getElementById('message');
  if (addClassForm && addMessageDiv) {
    console.log('Add Class form and message div found');
    addClassForm.addEventListener('submit', function(e) {
      e.preventDefault();
      addMessageDiv.style.display = 'none';
      addMessageDiv.textContent = '';
      const fetchUrl = addClassForm.getAttribute('action') || '/STAGE/controller/ClassController.php';
      console.log('Fetch URL:', fetchUrl);

      const levelSelect = document.getElementById('levelSelect');
      const selectedOption = levelSelect.options[levelSelect.selectedIndex];
      const levelName = selectedOption?.getAttribute('data-level-name')?.trim() || '';
      const className = document.getElementById('classNameInput').value.trim();

      if (!levelSelect.value) {
        addMessageDiv.style.display = 'block';
        addMessageDiv.style.color = 'red';
        addMessageDiv.textContent = 'Veuillez sélectionner un niveau.';
        return;
      }

      if (className === '') {
        addMessageDiv.style.display = 'block';
        addMessageDiv.style.color = 'red';
        addMessageDiv.textContent = 'Le nom de la classe est requis.';
        return;
      }

      if (!className.toLowerCase().startsWith(levelName.toLowerCase())) {
        addMessageDiv.style.display = 'block';
        addMessageDiv.style.color = 'red';
        addMessageDiv.textContent = `Le nom de la classe doit commencer par "${levelName}".`;
        return;
      }

      const formData = new FormData(addClassForm);
      fetch(fetchUrl, {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(data => {
        addMessageDiv.style.display = 'block';
        addMessageDiv.style.color = data.toLowerCase().includes('succès') ? 'green' : 'red';
        addMessageDiv.innerHTML = data;
        if (data.toLowerCase().includes('succès')) {
          setTimeout(() => {
            if (addModal) addModal.hide();
            location.reload();
          }, 1500);
        } else {
          setTimeout(() => {
            addMessageDiv.style.display = 'none';
            addMessageDiv.textContent = '';
          }, 3000);
        }
      })
      .catch(err => {
        addMessageDiv.style.display = 'block';
        addMessageDiv.style.color = 'red';
        addMessageDiv.textContent = `Erreur lors de l'ajout: ${err.message}`;
      });
    });
  } else {
    console.error('Add Class form or message div not found');
  }

  // Edit Class form submission with fetch AJAX
  const editClassForm = document.getElementById('editClassForm');
  const editMessageDiv = document.getElementById('editMessage');
  if (editClassForm && editMessageDiv) {
    console.log('Edit Class form and message div found');
    editClassForm.addEventListener('submit', function(e) {
      e.preventDefault();
      editMessageDiv.style.display = 'none';
      editMessageDiv.textContent = '';
      const fetchUrl = editClassForm.getAttribute('action') || '/STAGE/controller/ClassController.php';
      console.log('Fetch URL:', fetchUrl);

      const formData = new FormData(editClassForm);
      fetch(fetchUrl, {
        method: 'POST',
        body: formData,
      })
      .then(response => response.json())
      .then(data => {
        editMessageDiv.innerHTML = data.message;
        editMessageDiv.style.color = data.status === 'success' ? 'green' : 'red';
        editMessageDiv.style.display = 'block';

        if (data.status === 'success') {
          setTimeout(() => {
            if (editModal) editModal.hide();
            location.reload();
          }, 1500);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        editMessageDiv.innerHTML = '<p style="color:red;">Error updating class.</p>';
        editMessageDiv.style.display = 'block';
      });
    });
  } else {
    console.error('Edit Class form or message div not found');
  }

  // Delete Class form submission with fetch AJAX
  const deleteClassForm = document.getElementById('deleteClassForm');
  const deleteMessageDiv = document.getElementById('deleteMessage');
  if (deleteClassForm && deleteMessageDiv) {
    console.log('Delete Class form and message div found');
    deleteClassForm.addEventListener('submit', function(e) {
      e.preventDefault();
      deleteMessageDiv.style.display = 'none';
      deleteMessageDiv.textContent = '';
      const fetchUrl = deleteClassForm.getAttribute('action') || '/STAGE/controller/ClassController.php';
      console.log('Fetch URL:', fetchUrl);

      const formData = new FormData(deleteClassForm);
      fetch(fetchUrl, {
        method: 'POST',
        body: formData,
      })
      .then(response => response.json())
      .then(data => {
        deleteMessageDiv.innerHTML = data.message;
        deleteMessageDiv.style.color = data.status === 'success' ? 'green' : 'red';
        deleteMessageDiv.style.display = 'block';

        if (data.status === 'success') {
          setTimeout(() => {
            if (deleteModal) deleteModal.hide();
            location.reload();
          }, 1500);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        deleteMessageDiv.innerHTML = '<p style="color:red;">Error deleting class.</p>';
        deleteMessageDiv.style.display = 'block';
      });
    });
  } else {
    console.error('Delete Class form or message div not found');
  }

  // Select2 and input handling for Add Class
  if ($('#levelSelect').length) {
    $('#levelSelect').select2({
      placeholder: 'Select Level',
      allowClear: true,
      width: '100%'
    }).on('select2:select', function(e) {
      const selectedOption = e.params.data.element;
      const levelName = selectedOption.getAttribute('data-level-name')?.trim() || '';
      console.log('Selected level:', levelName);
      if (levelName) {
        const classNameInput = document.getElementById('classNameInput');
        classNameInput.disabled = false;
        classNameInput.value = levelName;
        classNameInput.placeholder = `Enter class name (e.g., ${levelName}A)`;
        setTimeout(() => {
          classNameInput.selectionStart = classNameInput.selectionEnd = levelName.length;
          classNameInput.focus();
        }, 0);
      }
    }).on('select2:unselect', function() {
      const classNameInput = document.getElementById('classNameInput');
      classNameInput.disabled = true;
      classNameInput.value = '';
      classNameInput.placeholder = 'Select a level first';
    });
  } else {
    console.error('levelSelect not found for Select2');
  }

  // Select2 and input handling for Edit Class
  if ($('#editLevelSelect').length) {
    $('#editLevelSelect').select2({
      placeholder: 'Select Level',
      allowClear: true,
      width: '100%'
    }).on('select2:select', function(e) {
      const selectedOption = e.params.data.element;
      const levelName = selectedOption.getAttribute('data-level-name')?.trim() || '';
      console.log('Selected level (Edit):', levelName);
      if (levelName) {
        const editClassName = document.getElementById('editClassName');
        editClassName.disabled = false;
        editClassName.value = levelName;
        setTimeout(() => {
          editClassName.selectionStart = editClassName.selectionEnd = levelName.length;
          editClassName.focus();
        }, 0);
      }
    }).on('select2:unselect', function() {
      const editClassName = document.getElementById('editClassName');
      editClassName.disabled = true;
      editClassName.value = '';
      editClassName.placeholder = 'Select a level first';
    });
  } else {
    console.error('editLevelSelect not found for Select2');
  }

  // Prevent deleting prefix and handle paste for Edit Class
  const editClassName = document.getElementById('editClassName');
  if (editClassName) {
    editClassName.addEventListener('keydown', (e) => {
      const levelSelect = document.getElementById('editLevelSelect');
      const selectedOption = levelSelect.options[levelSelect.selectedIndex];
      const levelName = selectedOption?.getAttribute('data-level-name')?.trim() || '';
      if (!levelName) return;

      const navKeys = ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Home', 'End'];
      if (navKeys.includes(e.key)) return;

      const prefixLength = levelName.length;
      if ((e.key === 'Backspace' && editClassName.selectionStart <= prefixLength) ||
          (e.key === 'Delete' && editClassName.selectionStart < prefixLength)) {
        e.preventDefault();
      }
    });

    editClassName.addEventListener('paste', (e) => {
      const levelSelect = document.getElementById('editLevelSelect');
      const selectedOption = levelSelect.options[levelSelect.selectedIndex];
      const levelName = selectedOption?.getAttribute('data-level-name')?.trim() || '';
      if (!levelName) return;

      e.preventDefault();
      const pasteData = (e.clipboardData || window.clipboardData).getData('text');
      editClassName.value = levelName + pasteData;
      const newCursorPos = levelName.length + pasteData.length;
      editClassName.selectionStart = editClassName.selectionEnd = newCursorPos;
    });
  } else {
    console.error('editClassName not found');
  }
});
</script>

<!-- ... (remaining unchanged sections) -->

<!-- ... (Edit Class, Delete Modal, View Class, and scripts remain unchanged) -->

<!-- ... (Edit Class, Delete Modal, View Class, and scripts remain unchanged) -->


<!-- ... (Edit Class, Delete Modal, View Class, and scripts remain unchanged) -->

        <!-- Edit Class -->
    
        <!-- View Class -->
        <div class="modal fade" id="view_class">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title">Class Details</h4>
                            <span class="badge badge-soft-success ms-2"><i class="fas fa-circle me-1 fs-5"></i>Active</span>
                        </div>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="classes.html">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>ID</p>
                                        <span>C138038</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>Class Name</p>
                                        <span>Class I-A</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>Level</p>
                                        <span>Level I</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>Number of Students</p>
                                        <span>30</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>Year</p>
                                        <span>2024</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>Created At</p>
                                        <span>2025-06-13 09:42:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Core JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <!-- Daterangepicker JS -->
    <script src="assets/js/moment.js"></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Feather Icon JS -->
    <script src="assets/js/feather.min.js"></script>
    <!-- Slimscroll JS -->
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <!-- Datatable JS -->
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>
    <!-- Select2 JS -->
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">

</body>

</html>