
<?php
require_once __DIR__ . '/../../controller/session_config.php';  

if (!isset($_SESSION['admin'])) {
  // Not logged in, redirect to login
  header('Location: login.php');
  exit;
}
$admin = $_SESSION['admin'];



// Optionally, you can assign the admin session to a variable for easier access

include_once __DIR__ . '/../../controller/societeController.php';
$societe = [
    'nom_centre' => $_SESSION['societe_nom'] ?? 'Not set',
    'matricule_fiscale' => $_SESSION['societe_matricule'] ?? 'Not set',
    'numero_telephone' => $_SESSION['societe_telephone'] ?? 'Not set',
    'adresse' => $_SESSION['societe_adresse'] ?? 'Not set',
    'logo' => $_SESSION['societe_logo'] ?? ''
];
?>
<?php
include_once __DIR__ . '/../../controller/StudentController.php';
include_once __DIR__ . '/../../controller/ClassController.php';
include_once __DIR__ . '/../../controller/PresenceController.php';
include_once __DIR__ . '/../../controller/PaimentController.php';


$controller = new ControllerStudent();
$students = $controller->listStudents();
$controller = new ControllerClass();
$classes  = $controller->listClasses();
usort($classes, function($a, $b) {
    return strcmp($a['class_name'], $b['class_name']);
});
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    error_log(print_r($data, true));

    if (isset($data['action']) && $data['action'] === 'deleteStudent') {
        $studentId = htmlspecialchars(trim($data['id']));
        
        // Initialize your Student model object here
        $student = new Student("", "", "", "", "", "", "", "", "", "", "", "");

        if (!empty($studentId)) {
            $deleted = $student->deleteStudent($studentId);
            if ($deleted) {
                $response = ['status' => 'success', 'message' => 'Étudiant supprimé avec succès.'];
            } else {
                $response = ['status' => 'error', 'message' => 'Échec de la suppression.'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'ID invalide.'];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
$classFilter = $_GET['class_id'] ?? '';
$genderFilter = $_GET['gender'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$searchFilter = strtolower(trim($_GET['search'] ?? ''));

function matchesSearch($student, $searchWords) {
    foreach ($searchWords as $word) {
        $wordFound = false;
        if (
            stripos($student['id'], $word) !== false ||    
            stripos($student['username'], $word) !== false ||
            stripos($student['name'], $word) !== false ||
            stripos($student['email'], $word) !== false ||
            stripos($student['phone_number'], $word) !== false
        ) {
            $wordFound = true;
        }
        if (!$wordFound) {
            return false; // This word not found in any field
        }
    }
    return true; // All words found
}


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
                                    </a>									<hr class="m-0">
									<a class="dropdown-item d-inline-flex align-items-center p-2"   href="login.php?logout=true?logout=true"><i class="ti ti-login me-2"></i>Logout</a>
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
					<a class="dropdown-item"   href="login.php?logout=true?logout=true">Logout</a>
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
								<a href="javascript:void(0);"  class="subdrop active"><i class="ti ti-user"></i><span>Students</span><span class="menu-arrow"></span></a>

									<ul>
										<li><a href="liststudent.php" class="active">All Students</a></li>
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
			<div class="content content-two">
				
				<!-- Page Header -->
				<div class="d-md-flex d-block align-items-center justify-content-between mb-3">
					<div class="my-auto mb-2">
						<h3 class="page-title mb-1">Students</h3>
                     	<nav>
                       		<ol class="breadcrumb mb-0">
                          		<li class="breadcrumb-item">
                            		<a href="index.html">Dashboard</a>
                          		</li>
                          		<li class="breadcrumb-item">
                            		Peoples
                          		</li>
                          		<li class="breadcrumb-item active" aria-current="page">Students Grid</li>
                        	</ol>
                      	</nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                      	<div class="pe-1 mb-2">
                       		<a href="#" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh">
                         		<i class="ti ti-refresh"></i>
                        	</a>
                        </div>
                      	<div class="pe-1 mb-2">
                        	<button type="button" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Print" data-bs-original-title="Print">
                         		<i class="ti ti-printer"></i>
                        	</button>
                     	</div>    
						 <div class="dropdown me-2 mb-2">
							<a href="javascript:void(0);" class="dropdown-toggle btn btn-light fw-medium d-inline-flex align-items-center" data-bs-toggle="dropdown">
								<i class="ti ti-file-export me-2"></i>Export
							</a>
							<ul class="dropdown-menu dropdown-menu-end p-3">
								<li>
									<a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="exportToPDF()"><i class="ti ti-file-type-pdf me-2"></i>Export as PDF</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="exportToExcel()"><i class="ti ti-file-type-xls me-2"></i>Export as Excel</a>
								</li>
							</ul>	
                         </div>                
                      	<div class="mb-2">
							<a href="addstudent.php" class="btn btn-primary d-flex align-items-center"><i class="ti ti-square-rounded-plus me-2"></i>Add Student</a>
                      	</div>
                    </div>
                </div>
				<!-- /Page Header -->
				
		<!-- Filter Form -->
<!-- Filter -->
<!-- Filter -->
<div class="container my-4">
  <div class="bg-white p-3 border rounded-1 d-flex align-items-center justify-content-between flex-wrap mb-4 pb-0">
    <h4 class="mb-3">Students Grid</h4>
    <div class="d-flex align-items-center flex-wrap">

      <!-- Filter Dropdown -->
      <div class="dropdown mb-3 me-2">
        <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">
          <i class="ti ti-filter me-2"></i>Filter
        </a>
        <div class="dropdown-menu p-3" style="min-width: 360px;">
          <form method="get" class="mb-2">
            <div class="row g-2 align-items-center">
              <div class="col-md-4">
                <label for="classFilter" class="form-label small mb-1">Class:</label>
                <select id="classFilter" name="class_id" class="form-select form-select-sm w-100">
                  <option value="">All</option>
                  <?php foreach ($classes as $class): ?>
                    <option value="<?php echo htmlspecialchars($class['id']); ?>" <?php if ($class['id'] === $classFilter) echo 'selected'; ?>>
                      <?php echo htmlspecialchars($class['class_name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label for="genderFilter" class="form-label small mb-1">Gender:</label>
                <select id="genderFilter" name="gender" class="form-select form-select-sm w-100">
                  <option value="" <?php if ($genderFilter === '') echo 'selected'; ?>>All</option>
                  <option value="Male" <?php if ($genderFilter === 'Male') echo 'selected'; ?>>Male</option>
                  <option value="Female" <?php if ($genderFilter === 'Female') echo 'selected'; ?>>Female</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="statusFilter" class="form-label small mb-1">Status:</label>
                <select id="statusFilter" name="status" class="form-select form-select-sm w-100">
                  <option value="" <?php if ($statusFilter === '') echo 'selected'; ?>>All</option>
                  <option value="Active" <?php if ($statusFilter === 'Active') echo 'selected'; ?>>Active</option>
                  <option value="Graduated" <?php if ($statusFilter === 'Graduated') echo 'selected'; ?>>Graduated</option>
                  <option value="Suspended" <?php if ($statusFilter === 'Suspended') echo 'selected'; ?>>Suspended</option>
                </select>
              </div>

              <div class="col-auto mt-2">
                <button type="submit" class="btn btn-sm btn-primary">Apply</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Username Search Input -->
      <div class="mb-3 me-2 d-flex align-items-center">
        <input
          type="text"
          id="studentSearchInput"
          placeholder="Rechercher par id, nom, username, téléphone ou email"
          class="form-control form-control-sm"
          style="min-width: 280px;"
          autocomplete="off"
        >
      </div>

    </div>
  </div>
</div>

    <div class="row" id="studentGrid">
        <?php foreach ($students as $student): ?>
            <?php
            $searchWords = preg_split('/\s+/', $searchFilter, -1, PREG_SPLIT_NO_EMPTY);

            if (
                ($classFilter === '' || $student['class_id'] === $classFilter) &&
                ($genderFilter === '' || $student['gender'] === $genderFilter) &&
                ($statusFilter === '' || strtolower($student['status']) === strtolower($statusFilter)) &&
                (
                    $searchFilter === '' || matchesSearch($student, $searchWords)
                )
            ):
                $status = htmlspecialchars($student['status']);
                $badgeColor = 'secondary';

                switch (strtolower($status)) {
                    case 'active': $badgeColor = 'success'; break;
                    case 'graduated': $badgeColor = 'warning'; break;
                    case 'suspended': $badgeColor = 'danger'; break;
                }
        ?>


        <div class="col-xxl-3 col-xl-4 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <a href="student-details.html" class="link-primary"><?php echo htmlspecialchars($student['id']); ?></a>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-soft-<?php echo $badgeColor; ?> d-inline-flex align-items-center me-1">
                            <i class="ti ti-circle-filled fs-5 me-1"></i><?php echo $status; ?>
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="bg-light-300 rounded-2 p-3 mb-3">
                        <div class="d-flex align-items-center">
                            <a href="student-details.html" class="avatar avatar-lg flex-shrink-0">
                                <img src="<?php 
                                    echo isset($student['picture']) && $student['picture'] 
                                        ? '/stage/controller/' . htmlspecialchars($student['picture']) 
                                        : 'assets/img/students/default.jpg'; 
                                ?>" class="img-fluid rounded-circle" alt="img">
                            </a>
                            <div class="ms-2">
                                <h5 class="mb-0 text-dark text-truncate">
                                    <a href="student-details.html">
                                        <?php echo htmlspecialchars($student['username']) . " " . htmlspecialchars($student['name']); ?>
                                    </a>
                                </h5>
                                <p><?php echo htmlspecialchars($student['id']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between gx-2">
                        <div>
                            <p class="mb-0">Name</p>
                            <p class="text-dark"><?php echo htmlspecialchars($student['name']); ?></p>
                        </div>
                        <div>
                            <p class="mb-0">Gender</p>
                            <p class="text-dark"><?php echo htmlspecialchars($student['gender']); ?></p>
                        </div>
                        <div>
                        <p class="mb-0">Joined on</p>
                        <p class="text-dark"><?php echo date('d M Y', strtotime($student['joined_at'])); ?></p>
                        </div>

                    </div>

                    <div class="d-flex align-items-center justify-content-between gx-2">
                        <div>
                            <p class="mb-0">Class</p>
                            <p class="text-dark"><?php echo htmlspecialchars($student['class_name']); ?></p>
                        </div>
                        <div>
                            <p class="mb-0">Number</p>
                            <p class="text-dark"><?php echo htmlspecialchars($student['phone_number']); ?></p>
                        </div>
                        <div>
                            <p class="mb-0">Address</p>
                            <p class="text-dark"><?php echo htmlspecialchars($student['address']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-2">
                            <i class="ti ti-brand-hipchat"></i>
                        </a>
                        <a href="#"
                class="paiement-btn btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-2"
                title="Ajouter Paiement"
                data-id-student="<?= htmlspecialchars($student['id']) ?>"
                data-id-level="<?= htmlspecialchars($student['level_id']) ?>"
                data-level-name="<?= htmlspecialchars($student['level_name']) ?>"
                data-methode-paiement="<?= htmlspecialchars($student['methode_paiement']) ?>"
                data-number-of-seances="<?= htmlspecialchars($student['number_of_seances']) ?>"
                >
                <i class="ti ti-cash"></i>
                </a>


                        <a href="#"
                    class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 historique-btn"
                    title="Voir l'historique"
                    data-student-id="<?php echo htmlspecialchars($student['id']); ?>"
                    data-student-name="<?php echo htmlspecialchars($student['username'] . ' ' . $student['name']); ?>">
                    <i class="ti ti-history"></i>
                    </a>


                        <!-- Edit Button -->
                        <?php
                        $dateOfBirthRaw = $student['date_of_birth'] ?? '';
                        $dateOfBirth = '';
                        if ($dateOfBirthRaw && $dateOfBirthRaw !== '0000-00-00') {
                            try {
                                $dateObj = new DateTime($dateOfBirthRaw);
                                $dateOfBirth = $dateObj->format('Y-m-d');
                            } catch (Exception $e) {
                                error_log("Invalid date_of_birth for student {$student['id']}: $dateOfBirthRaw");
                            }
                        }
                        ?>
                      <a href="#"
                        class="modifier-btn btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-2"
                        title="Edit Details"
                        data-id="<?php echo htmlspecialchars($student['id']); ?>"
                        data-name="<?php echo htmlspecialchars($student['name']); ?>"
                        data-username="<?php echo htmlspecialchars($student['username']); ?>"
                        data-gender="<?php echo htmlspecialchars($student['gender'] ?? 'Male'); ?>"
                        data-datebirth="<?php echo htmlspecialchars($student['date_of_birth']); ?>"
                        data-moyenne="<?php echo htmlspecialchars($student['moyenne']); ?>"
                        data-picture="<?php echo htmlspecialchars($student['picture'] ?? ''); ?>"
                        data-classid="<?php echo htmlspecialchars($student['class_id']); ?>"
                        data-status="<?php echo htmlspecialchars($student['status']); ?>"
                        data-address="<?php echo htmlspecialchars($student['address'] ?? ''); ?>"
                        data-email="<?php echo htmlspecialchars($student['email'] ?? ''); ?>"
                        data-phone_number="<?php echo htmlspecialchars($student['phone_number'] ?? ''); ?>"
                        data-methode_paiement="<?php echo htmlspecialchars($student['methode_paiement'] ?? 'par mois'); ?>">
                        <i class="ti ti-edit"></i>
                        </a>


                        <!-- Delete Button -->
                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-2 delete-student-btn" title="Delete" data-id="<?php echo htmlspecialchars($student['id']); ?>">
                            <i class="ti ti-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
     </div>
   <div class="modal fade" id="paiementModal" tabindex="-1" aria-labelledby="paiementModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="paiementForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paiementModalLabel">Ajouter Paiement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="addPaiment">
        <input type="hidden" name="id_student" id="paiement-student-id">
        <input type="hidden" name="id_level" id="paiement-level-id">

        <div class="mb-3">
          <label for="montant" class="form-label">Montant</label>
          <input type="number" name="montant" class="form-control" required readonly>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">Méthode</label>

          <div class="form-check">
            <input class="form-check-input methode-checkbox" type="checkbox" name="methode[]" id="methode-espece" value="Espèce">
            <label class="form-check-label" for="methode-espece">Espèce</label>
          </div>

          <div class="form-check">
            <input class="form-check-input methode-checkbox" type="checkbox" name="methode[]" id="methode-cheque-bancaire" value="Chèque bancaire">
            <label class="form-check-label" for="methode-cheque-bancaire">Chèque bancaire</label>
          </div>

          <div class="form-check">
            <input class="form-check-input methode-checkbox" type="checkbox" name="methode[]" id="methode-cheque-postale" value="Chèque postale">
            <label class="form-check-label" for="methode-cheque-postale">Chèque postale</label>
          </div>

          <div class="form-check">
            <input class="form-check-input methode-checkbox" type="checkbox" name="methode[]" id="methode-versement-bancaire" value="Versement bancaire">
            <label class="form-check-label" for="methode-versement-bancaire">Versement bancaire</label>
          </div>

          <div class="form-check">
            <input class="form-check-input methode-checkbox" type="checkbox" name="methode[]" id="methode-versement-postale" value="Versement postale">
            <label class="form-check-label" for="methode-versement-postale">Versement postale</label>
          </div>

          <div class="form-check">
            <input class="form-check-input methode-checkbox" type="checkbox" name="methode[]" id="methode-carte-bancaire" value="Carte bancaire">
            <label class="form-check-label" for="methode-carte-bancaire">Carte bancaire</label>
          </div>
        </div>

        <div class="mb-3">
          <label for="date_paiement" class="form-label">Date de paiement</label>
          <input type="date" name="date_paiement" class="form-control" required>
        </div>

        <div class="mb-3" id="versement-group" style="display: none;">
          <label for="date_versement" class="form-label">Date de versement</label>
          <input type="date" name="date_versement" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"style="margin-right: 310px;">Annuler</button>
        <button type="submit" class="btn btn-primary">Ajouter</button>

      </div>
    </form>
  </div>
  </div>

<div class="modal fade" id="historiqueModal" tabindex="-1" aria-labelledby="historiqueLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header flex-column align-items-start">
        <h5 class="modal-title" id="historiqueLabel">Historique de présence</h5>
        <p id="historiqueStudentName" class="mb-0 text-muted"></p>
      </div>
      <div class="modal-body">
        <!-- 🔍 Date filter -->
        <div class="mb-3">
  <label class="form-label">Filtrer par intervalle de dates :</label>
  <div class="d-flex gap-2">
    <input type="date" id="filterStartDate" class="form-control form-control-sm" />
    <input type="date" id="filterEndDate" class="form-control form-control-sm" />
  </div>
</div>

        <!-- 📄 Content area -->
        <div id="historiqueContent" style="max-height: 350px; overflow-y: auto;">
          <p>Chargement...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>




<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal" tabindex="-1" role="dialog" style="display:none;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmer la suppression</h5>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer cet étudiant ?</p>
      </div>
      <div class="modal-footer">
      <button id="btnDeleteCancel" type="button" class="btn btn-secondary" style="margin-right: 240px;">Non, annuler</button>
        <button id="btnDeleteConfirm" type="button" class="btn btn-danger" >Oui, supprimer</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="studentEditModal" tabindex="-1" aria-labelledby="studentEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentEditModalLabel">Modifier Étudiant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="studentEditForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="edit-id" class="form-label">ID</label>
                        <input type="text" class="form-control" id="edit-id" name="id" readonly>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))); ?>">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit-username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-classid" class="form-label">Classe</label>
                        <select class="form-select" id="edit-classid" name="class_id" required>
                            <option value="">Sélectionner une classe</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['id']); ?>">
                                    <?php echo htmlspecialchars($class['class_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-gender" class="form-label">Genre</label>
                        <select class="form-select" id="edit-gender" name="gender" required>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
</select>

                    </div>
                    <div class="mb-3">
                        <label for="edit-datebirth" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="edit-datebirth" name="date_of_birth" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-moyenne" class="form-label">Moyenne</label>
                        <input type="number" class="form-control" id="edit-moyenne" name="moyenne" step="0.01" min="0" max="20" required>
                    </div>
                   
                    <!-- Existing moyenne input -->

<!-- Add missing fields here -->
<div class="mb-3">
    <label for="edit-address" class="form-label">Adresse</label>
    <input type="text" class="form-control" id="edit-address" name="address" placeholder="Adresse">
</div>
<div class="mb-3">
    <label for="edit-email" class="form-label">Email</label>
    <input type="email" class="form-control" id="edit-email" name="email" placeholder="Email">
</div>
<div class="mb-3">
    <label for="edit-phone" class="form-label">Téléphone</label>
    <input type="tel" class="form-control" id="edit-phone" name="phone_number" placeholder="Téléphone">
</div>

<div class="mb-3">
                        <label for="edit-status" class="form-label">Statut</label>
                        <select class="form-select" id="edit-status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Graduated">Graduated</option>
                            <option value="Suspended">Suspended</option>

                        </select>
                    </div>
                    <div class="mb-3">
  <label for="edit-methode-paiement" class="form-label">Méthode de Paiement</label>
  <select class="form-select" id="edit-methode-paiement" name="methode_paiement" required>
    <option value="par mois">Par Mois</option>
    <option value="par séance">Par séance</option>
    
  </select>
</div>

                    <div class="mb-3">
                        <label for="edit-picture" class="form-label">Image</label>
                        <div class="d-flex align-items-center">
                            <img id="edit-picture-preview" src="" alt="Preview" class="img-fluid rounded-circle me-3" style="max-width: 80px; display: none;">
                            <input type="file" class="form-control" id="edit-picture" name="picture" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"style="margin-right: 250px;">Annuler</button>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>

                      </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("studentSearchInput");
    const grid = document.getElementById("studentGrid");

    if (!input || !grid) {
        console.warn("Search input or grid not found.");
        return;
    }

    input.addEventListener("input", function () {
    const query = input.value.trim().toLowerCase();
    const cards = grid.querySelectorAll(".col-xxl-3, .col-xl-4, .col-md-6");

    cards.forEach(card => {
        const text = card.innerText.toLowerCase();

        // Try to collect important data-* attributes manually:
        const dataEmail = card.querySelector("[data-email]")?.dataset.email?.toLowerCase() || "";
        const dataPhone = card.querySelector("[data-phone_number]")?.dataset.phone_number?.toLowerCase() || "";
        const dataName = card.querySelector("[data-name]")?.dataset.name?.toLowerCase() || "";

        const searchableText = text + " " + dataEmail + " " + dataPhone + " " + dataName;

        if (searchableText.includes(query)) {
            card.classList.remove("card-hidden");
        } else {
            card.classList.add("card-hidden");
        }
    });
});

});
</script>






<script>
document.addEventListener("DOMContentLoaded", () => {
  const paiementModal = new bootstrap.Modal(document.getElementById('paiementModal'));
  const paiementForm = document.getElementById('paiementForm');
  const versementGroup = document.getElementById('versement-group');
  const montantInput = paiementForm.querySelector('input[name="montant"]');
  const methodeCheckboxes = document.querySelectorAll('.methode-checkbox');

  // Show/hide "date_versement" if 'Chèque bancaire' checkbox is checked
  function updateVersementGroupVisibility() {
    const isChequeBancaireChecked = Array.from(methodeCheckboxes).some(cb => cb.checked && cb.value === 'Chèque bancaire');
    versementGroup.style.display = isChequeBancaireChecked ? 'block' : 'none'; 
  }

  methodeCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      if (cb.checked) {
        // Uncheck all others
        methodeCheckboxes.forEach(otherCb => {
          if (otherCb !== cb) otherCb.checked = false;
        });
      }
      updateVersementGroupVisibility();
    });
  });

  document.querySelectorAll('.paiement-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();

      const studentId = btn.getAttribute('data-id-student');
      const levelId = btn.getAttribute('data-id-level');
      const levelName = btn.getAttribute('data-level-name') || '';
      const methodePaiement = btn.getAttribute('data-methode-paiement') || 'par mois';
      const numberOfSeances = parseInt(btn.getAttribute('data-number-of-seances')) || 1;

      paiementForm.reset();
      versementGroup.style.display = 'none';

      document.getElementById('paiement-student-id').value = studentId;
      document.getElementById('paiement-level-id').value = levelId;

      const tunisDate = new Date().toLocaleString("en-US", { timeZone: "Africa/Tunis" });
const tunisDateObj = new Date(tunisDate);
const year = tunisDateObj.getFullYear();
const month = String(tunisDateObj.getMonth() + 1).padStart(2, '0');  // months are 0-based
const day = String(tunisDateObj.getDate()).padStart(2, '0');

const today = `${year}-${month}-${day}`;
console.log("Correct Tunisia date:", today);

paiementForm.querySelector('input[name="date_paiement"]').value = today;


      // Reset checkboxes
      methodeCheckboxes.forEach(cb => cb.checked = false);

      const match = levelName.match(/\d+/);
      if (match) {
        const levelNumber = parseInt(match[0]);
        let baseMontant = 100 + (levelNumber - 1) * 50;
        if (methodePaiement === 'par séance' && numberOfSeances > 0) {
          const montant = baseMontant / numberOfSeances;
          montantInput.value = montant.toFixed(3);
        } else {
          montantInput.value = baseMontant.toFixed(3);
        }
      } else {
        montantInput.value = '';
      }

      updateVersementGroupVisibility();
      paiementModal.show();
    });
  });

  paiementForm.addEventListener('submit', e => {
    e.preventDefault();

    // Collect checked methods (only one) and send as a string
    const checkedMethods = Array.from(methodeCheckboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value)
      .join(',');

    const formData = new FormData(paiementForm);
    formData.delete('methode[]'); // Remove checkbox array entries
    formData.append('methode', checkedMethods); // Append single string

    fetch('/stage/controller/paimentController.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(response => {
      console.log(response);
      if (response.includes('succès')) {
        alert("Paiement ajouté !");
        paiementModal.hide();
        location.reload();
      } else {
        alert("Erreur: " + response);
      }
    })
    .catch(err => {
      alert("Erreur serveur: " + err.message);
    });
  });
});
</script>


<script>
document.querySelectorAll('.historique-btn').forEach(button => {
  button.addEventListener('click', function (e) {
    e.preventDefault();
    const studentId = this.dataset.studentId;
    const studentName = this.dataset.studentName;

    const modalEl = document.getElementById('historiqueModal');
    const historiqueContent = document.getElementById('historiqueContent');
    const historiqueStudentName = document.getElementById('historiqueStudentName');
    const startInput = document.getElementById('filterStartDate');
    const endInput = document.getElementById('filterEndDate');

    historiqueContent.innerHTML = '<p>Chargement...</p>';
    historiqueStudentName.textContent = studentName || '';
    startInput.value = '';
    endInput.value = '';

    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    fetch(`/stage/controller/presencecontroller.php?student_id=${encodeURIComponent(studentId)}`)
      .then(response => response.json())
      .then(data => {
        if (!Array.isArray(data) || data.length === 0) {
          historiqueContent.innerHTML = '<p>Aucune donnée de présence disponible pour cet étudiant.</p>';
          return;
        }

        const presenceData = data;

        // 🧾 Render full table by default
        renderTable(presenceData);

        // 🎯 Filter when dates are selected
        function applyDateFilter() {
          const startDate = startInput.value;
          const endDate = endInput.value;

          if (!startDate && !endDate) {
            renderTable(presenceData);
            return;
          }

          const filtered = presenceData.filter(record => {
            const date = record.presence_date;
            return (!startDate || date >= startDate) && (!endDate || date <= endDate);
          });

          renderTable(filtered);
        }

        startInput.addEventListener('change', applyDateFilter);
        endInput.addEventListener('change', applyDateFilter);

        // 🧾 Render table
        function renderTable(filteredData) {
          let tableHTML = `<table class="table">
            <thead>
              <tr>
                <th>Statut</th>
                <th>Matière</th>
                <th>Heure Début</th>
                <th>Heure Fin</th>
              </tr>
            </thead>
            <tbody>`;

          filteredData.forEach(record => {
            const statusIcon = record.status === 'Present'
              ? '<i class="ti ti-check text-success" title="Présent"></i>'
              : '<i class="ti ti-x text-danger" title="Absent"></i>';

            tableHTML += `<tr>
              <td class="text-center">${statusIcon}</td>
              <td>${escapeHtml(record.subject_name)}</td>
              <td>${escapeHtml(record.start_time)}</td>
              <td>${escapeHtml(record.end_time)}</td>
            </tr>`;
          });

          tableHTML += '</tbody></table>';
          historiqueContent.innerHTML = tableHTML;
        }
      })
      .catch(error => {
        console.error('Erreur lors du chargement des données :', error);
        historiqueContent.innerHTML = '<p>Erreur lors du chargement des données.</p>';
      });
  });
});

// 🔒 Escape HTML
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}


// 📅 Get current week's dates (Mon to Sun)
function getCurrentWeekDates() {
  const today = new Date();
  const monday = new Date(today);
  monday.setDate(today.getDate() - ((today.getDay() + 6) % 7));

  const dates = [];
  for (let i = 0; i < 7; i++) {
    const d = new Date(monday);
    d.setDate(monday.getDate() + i);
    dates.push(d.toISOString().slice(0, 10));
  }
  return dates;
}

// 🗓️ Format date to French string
function formatDate(yyyy_mm_dd) {
  const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
  const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

  const date = new Date(yyyy_mm_dd);
  const dayName = days[date.getDay()];
  const monthName = months[date.getMonth()];
  const year = date.getFullYear();

  return `${dayName} ${date.getDate()} ${monthName} ${year}`;
}





// Simple escapeHtml function to prevent XSS (you may already have this)
function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, "&amp;")
               .replace(/</g, "&lt;")
               .replace(/>/g, "&gt;")
               .replace(/"/g, "&quot;")
               .replace(/'/g, "&#039;");
}

  window.addEventListener('load', function () {
    if (window.location.search.length > 0) {
      // Remove the query parameters from the URL without reloading the page
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
async function exportToPDF() {
  const { jsPDF } = window.jspdf;
  const studentCards = document.querySelectorAll('.card');
  const societeLogoPath = <?= json_encode(htmlspecialchars('/stage/' . ($_SESSION['societe_logo'] ?? ''))) ?>;

const logo = new Image();
logo.src = societeLogoPath || 'assets/img/default-logo.png';

  await new Promise(resolve => {
    logo.onload = resolve;
    logo.onerror = resolve;
  });

  const doc = new jsPDF('p', 'mm', 'a4');
  const pageWidth = doc.internal.pageSize.getWidth();
  const margin = 12;
  const startY = 30;
  const rowHeight = 35;
  const cellPadding = 2.5;
  let y = startY;

  // Draw logo top center
  const logoWidth = 40;
  const logoHeight = 15;
  doc.addImage(logo, 'PNG', (pageWidth - logoWidth) / 2, 10, logoWidth, logoHeight);

  // Columns definition
  const columns = [
    { title: 'Photo', width: 20 },
    { title: 'ID', width: 18 },
    { title: 'Username', width: 18 },
    { title: 'Name', width: 18 },
    { title: 'Gender', width: 18 },
    { title: 'DOB', width: 22 },
    { title: 'Class', width: 18 },
    { title: 'Phone', width: 20 },
    { title: 'Address', width: 18 },
    { title: 'Status', width: 21 }
  ];

  // Calculate total width of columns
  const totalColumnsWidth = columns.reduce((sum, col) => sum + col.width, 0);

  // Draw header background (only totalColumnsWidth wide)
  doc.rect(margin, y, totalColumnsWidth, 10, 'F');

  // Draw header text - bold white
  doc.setFontSize(11);
  doc.setTextColor(255, 255, 255);
  doc.setFont(undefined, 'bold');
  let x = margin;
  columns.forEach(col => {
    doc.text(col.title, x + cellPadding, y + 7);
    x += col.width;
  });

  doc.setFont(undefined, 'normal');
  y += 10;

  async function getAvatarDataURL(img) {
    try {
      const canvas = await html2canvas(img, { backgroundColor: null });
      return canvas.toDataURL('image/png');
    } catch {
      return null;
    }
  }

  for (let i = 0; i < studentCards.length; i++) {
    if (y + rowHeight > doc.internal.pageSize.getHeight() - margin) {
      doc.addPage();
      y = startY;

      // redraw header on new page
      doc.setFillColor(70, 130, 180);
      doc.rect(margin, y, totalColumnsWidth, 10, 'F');

      doc.setFontSize(11);
      doc.setTextColor(255, 255, 255);
      doc.setFont(undefined, 'bold');
      x = margin;
      columns.forEach(col => {
        doc.text(col.title, x + cellPadding, y + 7);
        x += col.width;
      });
      doc.setFont(undefined, 'normal');
      y += 10;
    }

    const card = studentCards[i];

    // Extract data
    const id = card.querySelector('.card-header a')?.textContent.trim() || '';
    const nameLine = card.querySelector('h5 a')?.textContent.trim() || '';
    const [username, ...nameParts] = nameLine.split(' ');
    const name = nameParts.join(' ');
    const status = card.querySelector('.badge')?.textContent.trim() || '';

    const cardSections = card.querySelectorAll('.card-body .d-flex');
    const gender = cardSections[1]?.children[1]?.querySelector('p.text-dark')?.textContent.trim() || '';
    const dob = cardSections[1]?.children[2]?.querySelector('p.text-dark')?.textContent.trim() || '';
    const className = cardSections[2]?.children[0]?.querySelector('p.text-dark')?.textContent.trim() || '';
    const phone = cardSections[2]?.children[1]?.querySelector('p.text-dark')?.textContent.trim() || '';
    const address = cardSections[2]?.children[2]?.querySelector('p.text-dark')?.textContent.trim() || '';

    const avatarImg = card.querySelector('.avatar img');
    const avatarDataURL = avatarImg ? await getAvatarDataURL(avatarImg) : null;

    // Alternate row colors (only totalColumnsWidth wide)
    if (i % 2 === 0) {
      doc.setFillColor(245, 248, 252); // very light blue
    } else {
		doc.setFillColor(245, 248, 252); // very light blue
    }
    doc.rect(margin, y, totalColumnsWidth, rowHeight, 'F');

    // Draw cell borders (only totalColumnsWidth wide)
    doc.setDrawColor(200);
    let borderX = margin;
    columns.forEach(col => {
      doc.rect(borderX, y, col.width, rowHeight);
      borderX += col.width;
    });

    x = margin;

    // Draw avatar
    if (avatarDataURL) {
		doc.addImage(avatarDataURL, 'PNG', x + 2, y + 5, 16, 25);
    }
    x += columns[0].width;

    doc.setFontSize(9);
    doc.setTextColor(30);

    // Draw text fields column by column
    doc.text(id, x + cellPadding, y + 15, { maxWidth: columns[1].width - cellPadding * 2 });
    x += columns[1].width;

    doc.text(username, x + cellPadding, y + 15, { maxWidth: columns[2].width - cellPadding * 2 });
    x += columns[2].width;

    doc.text(name, x + cellPadding, y + 15, { maxWidth: columns[3].width - cellPadding * 2 });
    x += columns[3].width;

    doc.text(gender, x + cellPadding, y + 15, { maxWidth: columns[4].width - cellPadding * 2 });
    x += columns[4].width;

    doc.text(dob, x + cellPadding, y + 15, { maxWidth: columns[5].width - cellPadding * 2 });
    x += columns[5].width;

    doc.text(className, x + cellPadding, y + 15, { maxWidth: columns[6].width - cellPadding * 2 });
    x += columns[6].width;

    doc.text(phone, x + cellPadding, y + 15, { maxWidth: columns[7].width - cellPadding * 2 });
    x += columns[7].width;

    doc.text(address, x + cellPadding, y + 15, { maxWidth: columns[8].width - cellPadding * 2 });
    x += columns[8].width;

	let statusColor;
const statusLower = status.toLowerCase();
if (statusLower === 'active') {
  statusColor = [0, 128, 0];       // green
} else if (statusLower === 'graduated') {
  statusColor = [218, 165, 32];    // gold
} else {
  statusColor = [255, 0, 0];       // red
}
doc.setTextColor(...statusColor);
doc.text(status, x + cellPadding, y + 15, { maxWidth: columns[9].width - cellPadding * 2 });

doc.setTextColor(30);

    y += rowHeight;
  }

  doc.save("students_table.pdf");
}






function exportToExcel() {
    const wb = XLSX.utils.book_new();
    const wsData = [];

    // En-tête
    wsData.push([
        'ID', 'Username', 'Name', 'Gender', 'Date of Birth',
        'Class', 'Phone Number', 'Address', 'Status'
    ]);

    const studentCards = document.querySelectorAll('#studentGrid .card');

    studentCards.forEach((card, index) => {
    console.log(`\n🎓 Étudiant #${index + 1}`);

    const id = card.querySelector('.card-header a')?.textContent.trim() || '';
    console.log(`🆔 ID: ${id}`);

    const nameLine = card.querySelector('h5 a')?.textContent.trim() || '';
    const [username, ...nameParts] = nameLine.split(' ');
    const name = nameParts.join(' ');
    console.log(`👤 Username: ${username}, Name: ${name}`);

    const cardSections = card.querySelectorAll('.card-body .d-flex');
    console.log(`📦 Nombre de blocs .d-flex: ${cardSections.length}`);

    // Bloc 2 = index 1
    const gender = cardSections[1]?.children[1]?.querySelector('p.text-dark')?.textContent.trim() || '';
    const dob = cardSections[1]?.children[2]?.querySelector('p.text-dark')?.textContent.trim() || '';
    console.log(`🚻 Gender: ${gender}`);
    console.log(`🎂 Date of Birth: ${dob}`);

    // Bloc 3 = index 2
    const className = cardSections[2]?.children[0]?.querySelector('p.text-dark')?.textContent.trim() || '';
    const phone = cardSections[2]?.children[1]?.querySelector('p.text-dark')?.textContent.trim() || '';
    const address = cardSections[2]?.children[2]?.querySelector('p.text-dark')?.textContent.trim() || '';
    console.log(`🏫 Class: ${className}`);
    console.log(`📞 Phone: ${phone}`);
    console.log(`📍 Address: ${address}`);

    const status = card.querySelector('.badge')?.textContent.trim() || '';
    console.log(`📌 Status: ${status}`);

    wsData.push([id, username, name, gender, dob, className, phone, address, status]);
});


    const ws = XLSX.utils.aoa_to_sheet(wsData);
    XLSX.utils.book_append_sheet(wb, ws, 'Student List');
    XLSX.writeFile(wb, 'student_list.xlsx');
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-student-btn').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-id');
            console.log("Attempting to delete student with ID: " + studentId);

            const modal = document.getElementById('deleteConfirmModal');
            const loadingIndicator = document.getElementById('loadingIndicator'); // optional

            if (!modal) {
                console.error('Delete confirmation modal not found!');
                return;
            }

            modal.style.display = 'block';

            const yesButton = document.getElementById('btnDeleteConfirm');
            const noButton = document.getElementById('btnDeleteCancel');

            yesButton.onclick = function() {
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'block';
                }

                const requestData = { action: 'deleteStudent', id: studentId };
                console.log('Request Data:', requestData);

                fetch('/stage/controller/studentController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(requestData)
                })
                .then(response => response.text())
                .then(text => {
                    console.log("Raw response text:", text);
                    try {
                        const data = JSON.parse(text);
                        console.log('Parsed response:', data);
                        if (data.status === 'success') {
                            alert(data.message);
                            const row = document.getElementById('row-' + studentId);
                            if(row) row.remove();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                        alert('Failed to parse server response. Please try again.');
                    } finally {
                        modal.style.display = 'none';
                        if (loadingIndicator) {
                            loadingIndicator.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Failed to delete the student. Please try again.');
                    modal.style.display = 'none';
                    if (loadingIndicator) {
                        loadingIndicator.style.display = 'none';
                    }
                });
            };

            noButton.onclick = function() {
                modal.style.display = 'none';
            };
        });
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("studentEditForm");
    const modalElement = document.getElementById("studentEditModal");
    const pictureInput = document.getElementById("edit-picture");
    const picturePreview = document.getElementById("edit-picture-preview");

    // Initialize Bootstrap modal
    const modal = new bootstrap.Modal(modalElement);

    // Populate form when edit button is clicked
    document.querySelectorAll(".modifier-btn").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            // Log button data for debugging
            console.log("Button dataset:", this.dataset);

            document.getElementById("edit-id").value = this.dataset.id || '';
            document.getElementById("edit-name").value = this.dataset.name || '';
            document.getElementById("edit-username").value = this.dataset.username || '';
            document.getElementById("edit-classid").value = this.dataset.classid || '';
            document.getElementById("edit-gender").value = this.dataset.gender || '';
            document.getElementById("edit-address").value = this.dataset.address || '';
            document.getElementById("edit-email").value = this.dataset.email || '';
            document.getElementById("edit-phone").value = this.dataset.phone_number || '';

            // Add methode_paiement value
            document.getElementById("edit-methode-paiement").value = this.dataset.methode_paiement || 'par mois';

            // Handle date_of_birth specially to avoid invalid date "0000-00-00"
            const rawDate = this.dataset.datebirth;
            document.getElementById("edit-datebirth").value = (rawDate && rawDate !== "0000-00-00") ? rawDate : '';

            document.getElementById("edit-moyenne").value = this.dataset.moyenne || '';
            document.getElementById("edit-status").value = this.dataset.status || '';

            console.log("Form ID set to:", document.getElementById("edit-id").value);
            console.log("Form Class ID set to:", document.getElementById("edit-classid").value);

            if (this.dataset.picture) {
                picturePreview.src = "/stage/controller/" + this.dataset.picture;
                picturePreview.style.display = "block";
            } else {
                picturePreview.src = "assets/img/students/default.jpg";
                picturePreview.style.display = "block";
            }

            // Show modal
            modal.show();
        });
    });

    // Preview new picture before upload
    pictureInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                picturePreview.src = e.target.result;
                picturePreview.style.display = "block";
            };
            reader.readAsDataURL(file);
        } else {
            picturePreview.style.display = "none";
        }
    });

    // Submit the form
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Client-side validation
        const id = document.getElementById("edit-id").value.trim();
        const name = document.getElementById("edit-name").value.trim();
        const username = document.getElementById("edit-username").value.trim();
        const classId = document.getElementById("edit-classid").value.trim();
        const gender = document.getElementById("edit-gender").value;
        const datebirth = document.getElementById("edit-datebirth").value;
        const moyenne = parseFloat(document.getElementById("edit-moyenne").value);
        const status = document.getElementById("edit-status").value;
        const methodePaiement = document.getElementById("edit-methode-paiement").value;

        // Validate ID as a non-empty string
        if (!id) {
            alert("L'ID de l'étudiant est requis.");
            return;
        }

        // Validate other fields including methode_paiement
        if (!name || !username || !classId || !gender || !datebirth || isNaN(moyenne) || moyenne < 0 || moyenne > 20 || !status || !methodePaiement) {
            alert("Veuillez remplir tous les champs correctement. La moyenne doit être entre 0 et 20 et la méthode de paiement doit être sélectionnée.");
            return;
        }

        const formData = new FormData(form);
        formData.append("action", "updateStudent");

        // Log form data for debugging
        for (let [key, value] of formData.entries()) {
            console.log(`FormData ${key}: ${value}`);
        }

        fetch("/stage/controller/studentController.php", {
            method: "POST",
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Erreur réseau: ${response.statusText} (Code: ${response.status}, Response: ${text})`);
                });
            }
            return response.text().then(text => {
                console.log("Réponse brute du serveur:", text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("Raw response:", text);
                    throw new Error("Réponse non-JSON valide: " + e.message);
                }
            });
        })
        .then(data => {
            if (data.status === "success") {
                alert(data.message || "Étudiant mis à jour avec succès!");
                modal.hide();
                location.reload();
            } else {
                alert("Erreur: " + (data.message || "Échec de la mise à jour."));
            }
        })
        .catch(err => {
            console.error("Erreur:", err);
            alert("Erreur lors de la mise à jour: " + err.message);
        });
    });

    // Reset form when modal is closed
    modalElement.addEventListener('hidden.bs.modal', function () {
        form.reset();
        picturePreview.style.display = "none";
        picturePreview.src = "";
    });
});

</script>
		<!-- Add Fees Collect -->
		<div class="modal fade" id="add_fees_collect">
			<div class="modal-dialog modal-dialog-centered  modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<div class="d-flex align-items-center">
							<h4 class="modal-title">Collect Fees</h4>
							<span class="badge badge-sm bg-primary ms-2">AD124556</span>
						</div>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
							aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="student-grid.html">
						<div class="modal-body">
							<div class="bg-light-300 p-3 pb-0 rounded mb-4">
								<div class="row align-items-center">
									<div class="col-lg-3 col-md-6">
										<div class="d-flex align-items-center mb-3">
											<a href="student-details.html" class="avatar avatar-md me-2">
												<img src="assets/img/students/student-01.jpg" alt="img">
											</a>
											<a href="student-details.html" class="d-flex flex-column"><span class="text-dark">Janet</span>III, A</a>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="mb-3">
											<span class="fs-12 mb-1">Total Outstanding</span>
											<p class="text-dark">2000</p>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="mb-3">
											<span class="fs-12 mb-1">Last Date</span>
											<p class="text-dark">25 May 2024</p>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="mb-3">
											<span class="badge badge-soft-danger"><i
											class="ti ti-circle-filled me-2"></i>Unpaid</span>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Fees Group</label>
										<select class="select">
											<option>Select</option>
											<option>Class 1 General</option>
											<option>Monthly Fees</option>
											<option>Admission-Fees</option>
											<option>Class 1- I Installment</option>
										</select>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Fees Type</label>
										<select class="select">
											<option>Select</option>
											<option>Tuition Fees</option>
											<option>Monthly Fees</option>
											<option>Admission Fees</option>
											<option>Bus Fees</option>
										</select>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Amount</label>
										<input type="text" class="form-control" placeholder="Enter Amout">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Collection Date</label>
										<div class="date-pic">
											<input type="text" class="form-control datetimepicker" placeholder="Select">
											<span class="cal-icon"><i class="ti ti-calendar"></i></span>
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Payment Type</label>
										<select class="select">
											<option>Select</option>
											<option>Paytm</option>
											<option>Cash On Delivery</option>
										</select>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Payment Reference No</label>
										<input type="text" class="form-control"
											placeholder="Enter Payment Reference No">
									</div>
								</div>
								<div class="col-lg-12">
									<div
										class="modal-satus-toggle d-flex align-items-center justify-content-between mb-3">
										<div class="status-title">
											<h5>Status</h5>
											<p>Change the Status by toggle </p>
										</div>
										<div class="status-toggle modal-status">
											<input type="checkbox" id="user1" class="check">
											<label for="user1" class="checktoggle"> </label>
										</div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="mb-0">
										<label class="form-label">Notes</label>
										<textarea rows="4" class="form-control" placeholder="Add Notes"></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
							<button type="submit" class="btn btn-primary">Pay Fees</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- Add Fees Collect -->

		<!-- Delete Modal -->
		<div class="modal fade" id="delete-modal">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<form action="student-grid.html">
						<div class="modal-body text-center">
							<span class="delete-icon">
								<i class="ti ti-trash-x"></i>
							</span>
							<h4>Confirm Deletion</h4>
							<p>You want to delete all the marked items, this cant be undone once you delete.</p>
							<div class="d-flex justify-content-center">
								<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Yes, Delete</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Delete Modal -->

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

	<!-- Datetimepicker JS -->
	<script src="assets/js/bootstrap-datetimepicker.min.js"></script>

	<!-- Slimscroll JS -->
	<script src="assets/js/jquery.slimscroll.min.js"></script>

	<!-- Select2 JS -->
	<script src="assets/plugins/select2/js/select2.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">

</body>
</html>