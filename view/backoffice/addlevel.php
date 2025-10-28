
<?php
require_once __DIR__ . '/../../controller/session_config.php';  
if (!isset($_SESSION['admin'])) {
    // Not logged in, redirect to login
    header('Location: login.php');
    exit;
  }
  $admin = $_SESSION['admin'];
  
  



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
                                        <button type="submit"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /Search -->
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
            <a href="#" class="dropdown-item active d-flex align-items-center" data-lang-code="en">
                <img class="me-2 rounded-pill" src="assets/img/flags/us.png" alt="Img" height="22" width="22"> English
            </a>
            <a href="#" class="dropdown-item d-flex align-items-center" data-lang-code="fr">
                <img class="me-2 rounded-pill" src="assets/img/flags/fr.png" alt="Img" height="22" width="22"> French
            </a>
            <a href="#" class="dropdown-item d-flex align-items-center" data-lang-code="es">
                <img class="me-2 rounded-pill" src="assets/img/flags/es.png" alt="Img" height="22" width="22"> Spanish
            </a>
            <a href="#" class="dropdown-item d-flex align-items-center" data-lang-code="de">
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
                                    <a class="d-flex align-items-center p-2" href="profile.php">
  <i class="fas fa-user-circle me-2"></i>My Profile
</a>

<a class="dropdown-item d-inline-flex align-items-center p-2"
                                    href="#"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#theme-setting">
                                    <i class="ti ti-settings me-2"></i>Settings
                                    </a>

<hr class="m-0">

<a class="d-flex align-items-center p-2"  href="login.php?logout=true">
  <i class="fas fa-sign-out-alt me-2"></i>Logout
</a></div>
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
                    <a class="dropdown-item"  href="login.php?logout=true">Logout</a>
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
                            <a href="javascript:void(0);"  class="subdrop active"><i class="ti ti-list-numbers"></i><span>Levels</span><span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="addlevel.php"class="active">All Levels</a></li>
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
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Levels List</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.html">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Levels</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">All Levels</li>
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
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_level"><i class="fas fa-plus-square me-2"></i>Add Level</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Levels List -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Levels List</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="input-icon-start mb-3 me-2 position-relative">
                                <span class="icon-addon">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <input type="text" class="form-control date-range bookingrange" placeholder="Select" value="Academic Year : 2024 / 2025">
                            </div>
                            
                            <div class="dropdown mb-3">
  <button class="btn btn-outline-light bg-white dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="fas fa-sort-alpha-down me-2"></i><span id="sortLabel">Sort by A-Z</span>
  </button>
  <ul class="dropdown-menu p-3" aria-labelledby="sortDropdown">
  <li><a href="#" class="dropdown-item rounded-1 active" data-sort="asc">Sort A-Z (Class Name)</a></li>
  <li><a href="#" class="dropdown-item rounded-1" data-sort="desc">Sort Z-A (Class Name)</a></li>
  <li><a href="#" class="dropdown-item rounded-1" data-sort="num-asc">Sort by Number of Classes ↑</a></li>
  <li><a href="#" class="dropdown-item rounded-1" data-sort="num-desc">Sort by Number of Classes ↓</a></li>
</ul>


</div>

                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <!-- Levels List -->
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
                <th>Level Name</th>
                <th>Nombre de Classes</th> 
                <th>Nombre of Students</th>
                <th>Nombre of Seances</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($levels)): ?>
                <?php foreach ($levels as $level): ?>
                    <tr>
                        <td>
                            <div class="form-check form-check-md">
                                <input class="form-check-input" type="checkbox" name="select_level[]" value="<?= htmlspecialchars($level['id']) ?>">
                            </div>
                        </td>
                        <td><?= htmlspecialchars($level['id']) ?></td>
                        <td><?= htmlspecialchars($level['name']) ?></td>
                        <td><?= htmlspecialchars($level['number_of_classes']) ?></td>
                        <td><?= htmlspecialchars($level['number_of_students']) ?></td>
                        <td><?= htmlspecialchars($level['number_of_seances']) ?></td>
                        <td>
    <button 
        class="btn btn-sm btn-primary btn-edit-level" 
        data-id="<?= htmlspecialchars($level['id']) ?>" 
        data-name="<?= htmlspecialchars($level['name']) ?>"
        data-students="<?= htmlspecialchars($level['number_of_students']) ?>"
        data-classes="<?= htmlspecialchars($level['number_of_classes']) ?>"
        data-subjects="<?= htmlspecialchars($level['number_of_seances']) ?>"
    >Edit</button>

    <button 
        class="btn btn-sm btn-danger btn-delete-level" 
        data-id="<?= htmlspecialchars($level['id']) ?>"
    >Delete</button>
</td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">No levels found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

                        <!-- /Levels List -->
                    </div>
                </div>
                <!-- /Levels List -->
            </div>
        </div>
        <!-- /Page Wrapper -->
<!-- Edit Level Modal -->
<div class="modal fade" id="editLevelModal" tabindex="-1" aria-labelledby="editLevelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editLevelForm" method="post" action="../../controller/levelController.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editLevelLabel">Edit Level</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="updateLevel" />
          <input type="hidden" name="id" id="editLevelId" />

          <div class="mb-3">
            <label for="editLevelName" class="form-label">Level Name</label>
            <input type="text" readonly class="form-control" id="editLevelName" name="name" />
          </div>

          <div class="mb-3">
            <label for="editNumberOfStudents" class="form-label">Number of Students</label>
            <input type="number" readonly class="form-control" id="editNumberOfStudents" />
          </div>

          <div class="mb-3">
            <label for="editNumberOfClasses" class="form-label">Number of Classes</label>
            <input type="number" readonly class="form-control" id="editNumberOfClasses" />
          </div>

          <div class="mb-3">
            <label for="editNumberOfSubjects" class="form-label">number_of_seances</label>
            <input type="number" class="form-control" id="editNumberOfSubjects" name="number_of_seances" min="0" required />
          </div>

          <div id="editMessage" style="margin-top: 10px;"></div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"style="margin-right: 270px;">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="deleteLevelForm" method="post" action="../../controller/levelController.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteConfirmLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="deleteLevel" />
          <input type="hidden" name="id" id="deleteLevelId" />
          <p>Are you sure you want to delete this level?</p>
          <div id="deleteMessage" style="margin-top: 10px;"></div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 270px;">No, annuler</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editLevelModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));

    // Handle Edit button click
    document.querySelectorAll('.btn-edit-level').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const students = btn.dataset.students;
            const classes = btn.dataset.classes;
            const subjects = btn.dataset.subjects;

            document.getElementById('editLevelId').value = id;
            document.getElementById('editLevelName').value = name;
            document.getElementById('editNumberOfStudents').value = students;
            document.getElementById('editNumberOfClasses').value = classes;
            document.getElementById('editNumberOfSubjects').value = subjects;

            editModal.show();
        });
    });

    // Handle Delete button click
    document.querySelectorAll('.btn-delete-level').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            document.getElementById('deleteLevelId').value = id;
            deleteModal.show();
        });
    });
});
document.addEventListener("DOMContentLoaded", function () {
  const editLevelForm = document.getElementById("editLevelForm");
  const editMessageDiv = document.getElementById("editMessage");

  editLevelForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Prepare FormData
    const formData = new FormData(editLevelForm);

    fetch("../../controller/levelController.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        // Show message inside modal
        editMessageDiv.innerHTML = data.message;
        editMessageDiv.style.color = data.status === "success" ? "green" : "red";

        if (data.status === "success") {
          // Optionally reload or update UI after delay
          setTimeout(() => {
            // For example, reload page to see changes or hide modal
            location.reload();
          }, 1500);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        editMessageDiv.innerHTML = "<p style='color:red;'>Erreur lors de la mise à jour.</p>";
      });
  });
});
document.addEventListener("DOMContentLoaded", function () {
  const deleteLevelForm = document.getElementById("deleteLevelForm");
  const deleteMessageDiv = document.getElementById("deleteMessage");
  const deleteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteConfirmModal'));

  deleteLevelForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(deleteLevelForm);
    console.log('Deleting ID:', formData.get('id'));

    fetch("../../controller/levelController.php", {
      method: "POST",
      body: formData,
    })
      .then(response => response.json())
      .then(data => {
        deleteMessageDiv.innerHTML = data.message;
        deleteMessageDiv.style.color = data.status === "success" ? "green" : "red";

        if (data.status === "success") {
          // After short delay, hide modal and reload page or update UI
          setTimeout(() => {
            deleteModal.hide();
            location.reload();
          }, 1500);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        deleteMessageDiv.innerHTML = "<p style='color:red;'>Erreur lors de la suppression.</p>";
      });
  });
});

    </script>
      <!-- Add Level Modal -->
<div class="modal fade" id="add_level" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title">Add Level</h4>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="modal-body">
      <form id="levelForm" action="../../controller/levelController.php" method="POST">
          <input type="hidden" name="action" value="addLevel">

          <div class="mb-3">
            <label class="form-label">Level Name</label>
            <input type="text" class="form-control" name="name" placeholder="Enter Level Name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">number_of_seances</label>
            <input type="number" class="form-control" name="number_of_seances" placeholder="Enter number_of_seances" required>
          </div>

          <button type="submit" class="btn btn-primary">Add Level</button>
        </form>

        <!-- Message will appear here -->
        <div id="message" class="mt-3"></div>
      </div>

    </div>
  </div>
</div>

<script>
document.getElementById("levelForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const name = this.name.value.trim();
    const number_of_seances = this.number_of_seances.value.trim();
    const message = document.getElementById("message");

    if (name === "" || number_of_seances === "" || Number(number_of_seances) <= 0) {
        message.style.display = "block";
        message.innerHTML = `<p style="color:red;">Veuillez remplir tous les champs correctement.</p>`;
        return;
    }

    const formData = new FormData(this);
    formData.forEach((value, key) => {
        console.log(key + ": " + value);
    });

    fetch("addlevel.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log("Form submitted successfully. Response:", data);
        message.style.display = "block";
        message.innerHTML = data;

        if (data.toLowerCase().includes('succès')) {
            this.reset();
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            setTimeout(() => {
                message.style.display = "none";
            }, 3000);
        }
    })
    .catch(error => {
        message.style.display = "block";
        console.error("Error during the request:", error);
        message.innerHTML = `<p style="color:red;">Erreur lors de l'ajout: ${error.message}</p>`;
    });
});


</script>

<!-- Spinner (hidden by default) -->
<div id="loadingSpinner" style="display:none;">
    <svg xmlns="http://www.w3.org/2000/svg" style="margin:auto; background:none; display:block;" width="50px" height="50px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
        <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#007bff" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
            <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>
        </circle>
    </svg>
    <p>Loading...</p>
</div>

<!-- Message area -->
<div id="messageArea"></div>

                </div>
            </div>
        </div>
        <!-- /Add Level -->

     
        <!-- /Delete Modal -->

        <!-- View Level -->
        <div class="modal fade" id="view_level">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title">Level Details</h4>
                            <span class="badge badge-soft-success ms-2"><i class="fas fa-circle me-1 fs-5"></i>Active</span>
                        </div>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="levels.html">
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
                                        <p>Level Name</p>
                                        <span>I</span>
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
                                        <p>number_of_seances</p>
                                        <span>03</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /View Level -->
    </div>
	<!-- /Main Wrapper -->

	<!-- jQuery -->
	<script src="assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>

	<!-- Daterangepikcer JS -->
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
    <script>
document.querySelectorAll('.dropdown-item').forEach(item => {
  item.addEventListener('click', e => {
    e.preventDefault();

    // Remove "active" class from all, then activate the clicked one
    document.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
    item.classList.add('active');

    const sortOrder = item.getAttribute('data-sort');
    const sortLabel = document.getElementById('sortLabel');
    if (sortLabel) {
      sortLabel.textContent =
        sortOrder === 'asc' ? 'Sort A-Z (Class Name)' :
        sortOrder === 'desc' ? 'Sort Z-A (Class Name)' :
        sortOrder === 'num-asc' ? 'Sort by Number of Classes ↑' :
        sortOrder === 'num-desc' ? 'Sort by Number of Classes ↓' :
        '';
    }

    const tbody = document.querySelector('table.datatable tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
      let valueA, valueB;

      if (sortOrder === 'num-asc' || sortOrder === 'num-desc') {
        // Sorting based on column index 3 (Number of Classes)
        valueA = parseInt(a.cells[3].textContent.trim()) || 0;
        valueB = parseInt(b.cells[3].textContent.trim()) || 0;
        return sortOrder === 'num-asc' ? valueA - valueB : valueB - valueA;
      }

      // Default: sorting by column 2 (Class Name)
      valueA = a.cells[2].textContent.trim().toLowerCase();
      valueB = b.cells[2].textContent.trim().toLowerCase();

      if (valueA < valueB) return sortOrder === 'asc' ? -1 : 1;
      if (valueA > valueB) return sortOrder === 'asc' ? 1 : -1;
      return 0;
    });

    // Append sorted rows back to the table
    rows.forEach(row => tbody.appendChild(row));
  });
});
</script>

<script>
const API_KEY = "AIzaSyB0vk7MC71yegqH97UP-NUkFe58fG_0nk4 "; // <-- Replace this

document.addEventListener("DOMContentLoaded", () => {
    const items = document.querySelectorAll('.dropdown-item');

    items.forEach(item => {
        item.addEventListener('click', async (e) => {
            e.preventDefault();
            const targetLang = item.getAttribute('data-lang-code');
            await translatePage(targetLang);
        });
    });
});

async function translatePage(targetLang) {
    const elements = document.querySelectorAll('body *:not(script):not(style):not(img):not(link):not(meta)');
    for (let el of elements) {
        if (el.children.length === 0 && el.textContent.trim().length > 0) {
            const originalText = el.textContent.trim();
            const translated = await translateText(originalText, targetLang);
            if (translated) el.textContent = translated;
        }
    }
}

async function translateText(text, targetLang) {
    try {
        const response = await fetch(`https://translation.googleapis.com/language/translate/v2?key=${API_KEY}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                q: text,
                target: targetLang,
                format: "text"
            })
        });

        const data = await response.json();

        // Check for API errors
        if (data.error) {
            console.error("API Error:", data.error.message);
            return null;
        }

        // Validate structure
        if (!data.data || !data.data.translations || !data.data.translations[0]) {
            console.error("Unexpected response structure:", data);
            return null;
        }

        return data.data.translations[0].translatedText;

    } catch (err) {
        console.error("Translation failed", err);
        return null;
    }
}

</script>
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">

</body>

</html>