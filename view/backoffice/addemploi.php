<?php
require_once __DIR__ . '/../../controller/session_config.php';  
include_once __DIR__ . '/../../controller/ClassController.php';
include_once __DIR__ . '/../../controller/EmploiController.php';
include_once __DIR__ . '/../../controller/EnseignantController.php';
include_once __DIR__ . '/../../controller/MatiereController.php';
include_once __DIR__ . '/../../controller/ClassroomController.php';
include_once __DIR__ . '/../../controller/PresenceController.php';

include_once __DIR__ . '/../../controller/studentController.php';
if (!isset($_SESSION['admin'])) {
    // Not logged in, redirect to login
    header('Location: login.php');
    exit;
  }
  $admin = $_SESSION['admin'];
  
  



$studentController = new ControllerStudent();
$selectedClassId = $_GET['class_id'] ?? '';



$controller = new ControllerClassroom();
$classrooms = $controller->listClassrooms();
$classController = new ControllerClass();
$classes = $classController->listClasses();
usort($classes, function($a, $b) {
    return strcmp($a['class_name'], $b['class_name']);
});
$emploiController = new EmploiController();
$emplois = $emploiController->listEmplois();



$enseignantController = new ControllerEnseignant();
$enseignants = $enseignantController->listEnseignants();

$matiereController = new ControllerMatiere();
$matieres = $matiereController->listMatieres();

// Create lookup arrays for quick access
$matiereLookup = array_column($matieres, 'nom', 'id');
$enseignantLookup = array_column($enseignants, 'nom', 'id_enseignant');

// Group emplois by class
$emploisByClass = [];
foreach ($emplois as $emploi) {
    $emploisByClass[$emploi['id_classe']][] = $emploi;
}
$selectedClassId = $_GET['class_id'] ?? '';

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
    <title>Emploi du Temps</title>

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
    <style>
/* === BLOCK SIZE & LAYOUT === */
.bg-transparent-primary,
.bg-transparent-success,
.bg-transparent-light,
.bg-soft-green,
.bg-soft-pink,
.bg-soft-red,
.bg-soft-purple,
.bg-soft-yellow,
.bg-soft-blue,
.empty-slot {
    min-height: 90px;
    max-width: 150px;
    padding: 4px 6px;
    box-sizing: border-box;
    border-radius: 6px;
    margin-bottom: 6px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    transition: background-color 0.3s ease, box-shadow 0.3s ease, min-height 0.3s ease, max-width 0.3s ease;
    overflow: hidden;
}

/* Empty slot specific */
.empty-slot {
    border: 1px dashed #ccc;
    background-color: #f9f9f9;
    position: relative; 

}
.empty-slot .event-placeholder {
    display: none;
    position: absolute;
    top: 4px;
    left: 6px;
    right: 6px;
    font-size: 0.75em;
    color: #6c757d;
    background: rgba(255, 255, 255, 0.85);
    padding: 4px 6px;
    border-radius: 4px;
    z-index: 5;
    pointer-events: none; /* so it doesn't interfere with clicks */
    text-align: center;
}

.empty-slot:hover .event-placeholder {
    display: block;
}

/* === COLOR BACKGROUNDS === */
.bg-soft-green    { background-color: #d4edda; }
.bg-soft-pink     { background-color: #f8d7da; }
.bg-soft-red      { background-color: #f5c6cb; }
.bg-soft-purple   { background-color: #e2d9f3; }
.bg-soft-yellow   { background-color: #fff3cd; }
.bg-soft-blue     { background-color: #d1ecf1; }

/* Text & content inside blocks */
.bg-transparent-primary,
.bg-transparent-success,
.bg-transparent-light,
.bg-soft-green,
.bg-soft-pink,
.bg-soft-red,
.bg-soft-purple,
.bg-soft-yellow,
.bg-soft-blue {
    color: #333;
    font-size: 1.25em;
    border-radius: 6px;
    position: relative;
    cursor: pointer;
}

.bg-transparent-primary p,
.bg-transparent-success p,
.bg-transparent-light p,
.bg-soft-green p,
.bg-soft-pink p,
.bg-soft-red p,
.bg-soft-purple p,
.bg-soft-yellow p,
.bg-soft-blue p {
    margin: 0;
    padding: 0;
    font-size: 0.75em;
    line-height: 1.2;
}

/* Subject text bold */
.event-subject {
    font-weight: 600;
}

/* Teacher info */
.bg-white.rounded.p-1 {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 2px 4px;
    margin-top: 4px;
    max-width: 90px;
    font-size: 0.7em;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    line-height: 1.1;
}

/* === HOVER EFFECTS === */
.bg-transparent-primary:hover,
.bg-transparent-success:hover,
.bg-transparent-light:hover {
    background-color: #dfe6e9; /* neutral light gray hover */
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
}

.bg-transparent-primary:hover,
.bg-transparent-success:hover,
.bg-transparent-light:hover,
.bg-soft-green:hover,
.bg-soft-pink:hover,
.bg-soft-red:hover,
.bg-soft-purple:hover,
.bg-soft-yellow:hover,
.bg-soft-blue:hover,
.empty-slot:hover {
    min-height: 88px !important;
    max-width: 150px !important;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
}

/* Show buttons only on hover */
.event-buttons {
    display: none;
    justify-content: center;
    gap: 6px;
    margin-top: auto;
    z-index: 10;
}

/* Hide content except buttons on hover */
.bg-transparent-primary:hover > p,
.bg-transparent-primary:hover > div:not(.event-buttons),
.bg-transparent-success:hover > p,
.bg-transparent-success:hover > div:not(.event-buttons),
.bg-transparent-light:hover > p,
.bg-transparent-light:hover > div:not(.event-buttons),
.bg-soft-green:hover > p,
.bg-soft-green:hover > div:not(.event-buttons),
.bg-soft-pink:hover > p,
.bg-soft-pink:hover > div:not(.event-buttons),
.bg-soft-red:hover > p,
.bg-soft-red:hover > div:not(.event-buttons),
.bg-soft-purple:hover > p,
.bg-soft-purple:hover > div:not(.event-buttons),
.bg-soft-yellow:hover > p,
.bg-soft-yellow:hover > div:not(.event-buttons),
.bg-soft-blue:hover > p,
.bg-soft-blue:hover > div:not(.event-buttons) {
    opacity: 0;
    visibility: hidden;
    height: 0;
    overflow: hidden;
}

/* Show only the buttons on hover */
.bg-transparent-primary:hover .event-buttons,
.bg-transparent-success:hover .event-buttons,
.bg-transparent-light:hover .event-buttons,
.bg-soft-green:hover .event-buttons,
.bg-soft-pink:hover .event-buttons,
.bg-soft-red:hover .event-buttons,
.bg-soft-purple:hover .event-buttons,
.bg-soft-yellow:hover .event-buttons,
.bg-soft-blue:hover .event-buttons {
    display: flex;
}

/* Center buttons vertically and horizontally */
.bg-transparent-primary:hover .event-buttons,
.bg-transparent-success:hover .event-buttons,
.bg-transparent-light:hover .event-buttons,
.bg-soft-green:hover .event-buttons,
.bg-soft-pink:hover .event-buttons,
.bg-soft-red:hover .event-buttons,
.bg-soft-purple:hover .event-buttons,
.bg-soft-yellow:hover .event-buttons,
.bg-soft-blue:hover .event-buttons {
    margin: auto;
}

/* === BUTTONS === */
.edit-btn,
.delete-btn {
    padding: 4px 6px;
    border: none;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
    color: white;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}
.edit-btn { background-color: #9b59b6; }
.edit-btn:hover { background-color: #7d3c98; }
.delete-btn { background-color: #dc3545; }
.delete-btn:hover { background-color: #c82333; }

/* === FILTER DROPDOWN === */
#classFilter {
    padding: 8px 12px;
    font-size: 16px;
    border: 1.5px solid #3498db;
    border-radius: 6px;
    background-color: white;
    color: #2c3e50;
    cursor: pointer;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    min-width: 200px;
}
#classFilter:hover,
#classFilter:focus {
    border-color: #2980b9;
    box-shadow: 0 0 5px rgba(41, 128, 185, 0.6);
    outline: none;
}
label[for="classFilter"] {
    font-size: 22px;
    font-weight: 600;
    color: #2c3e50;
    margin-right: 10px;
    user-select: none;
}

/* === BLUR EFFECT === */
.blur-content {
    filter: blur(5px);
    transition: filter 0.3s ease;
}

/* === PRESENCE TABLE === */
#presenceTableContainer td {
    font-weight: normal;
    color: #000;
}
#presenceTableContainer table {
    border-collapse: collapse;
    width: 100%;
}
#presenceTableContainer th,
#presenceTableContainer td {
    border: none !important;
    padding: 4px 8px;
}
#presenceTableContainer table thead {
    border-bottom: none !important;
    background-color: transparent !important;
}

</style>


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
										<li><a href="addclass.php">All Classes</a></li>
										<li><a href="addemploi.php"  class="active"><i class="ti ti-calendar-event"></i>Schedule</a></li>
									</ul>
								</li>
								<li><a href="addclassroom.php"><i class="ti ti-building"></i><span>Class Room</span></a></li>
							 <li><a href="addmatier.php"><i class="ti ti-book"></i><span>Subject</span></a></li>
                             <li>
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
  <?php
$currentDate = new DateTime('2025-06-25 15:27:00', new DateTimeZone('CET'));
$monday = (clone $currentDate)->modify('monday this week');
$firstDayOfMonth = new DateTime('2025-06-01', new DateTimeZone('CET'));
$lastDayOfMonth = new DateTime('2025-06-30 23:59:59', new DateTimeZone('CET'));
?>
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        

        <!-- Filter Form -->
        <form method="GET" action="" style="margin-bottom: 20px;">
            <label for="classFilter">Filtrer par classe :</label>
            <select id="classFilter" name="class_id" onchange="this.form.submit()">
                <option value="">-- Toutes les classes --</option>
                <?php foreach ($classes as $classOption): ?>
                    <option value="<?= htmlspecialchars($classOption['id']) ?>" <?= ($selectedClassId == $classOption['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($classOption['class_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Card Section -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                <h4 class="mb-3">Time Table</h4>
                <div class="d-flex align-items-center flex-wrap">
                   
                </div>
            </div>

            <div class="card-body pb-0">
                <?php foreach ($classes as $class): ?>
                    <?php
                    if ($selectedClassId && $class['id'] != $selectedClassId) {
                        continue; // Skip classes not selected
                    }
                    $classEmplois = $emploisByClass[$class['id']] ?? [];
                    ?>
                    <div class="class-section mb-5">
                        <h3>Class <?= htmlspecialchars($class['class_name']) ?></h3>
                        <div class="d-flex flex-nowrap overflow-auto">
                            <?php
                            $timezone = new DateTimeZone('Africa/Tunis');
                            $monday = new DateTime('monday this week', $timezone);
                            $currentDate = new DateTime('now', $timezone);
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            foreach ($days as $dayIndex => $dayName):
                                $date = (clone $monday)->modify("+$dayIndex day");
                                $isToday = $date->format('Y-m-d') === $currentDate->format('Y-m-d');
                                $rowStyle = $isToday ? 'bg-transparent-warning' : '';

                                // Dynamically increase margin based on day index
                                $marginValue = min($dayIndex + 1, 5); // Increases from me-1 to me-5
                                $marginClass = ($dayIndex === count($days) - 1) ? '' : "me-{$marginValue}";
                            ?>
                            <div class="d-flex flex-column <?= $marginClass ?> flex-fill">
                                <div class="mb-3">
                                    <h6><?= $dayName ?></h6>
                                </div>
                                <?php
                                $startHour = 8;
                                $endHour = 18;
                                $hours = [];
                                for ($h = $startHour; $h < $endHour; $h++) {
                                    $hours[] = $h;
                                }
                                foreach ($hours as $hour) {
                                    $slotStartHour = $hour;
                                    $slotEndHour = $hour + 1;
                                    $hasEvent = false;
                                    foreach ($classEmplois as $emploi) {
                                        try {
                                            $startDate = new DateTime($emploi['date_debut'], new DateTimeZone('CET'));
                                            $endDate = new DateTime($emploi['date_fin'], new DateTimeZone('CET'));
                                            if ($date->format('Y-m-d') === $startDate->format('Y-m-d')) {
                                                $startHourEvent = (int) $startDate->format('H');
                                                $endHourEvent = (int) $endDate->format('H');
                                                if ($startHourEvent < $slotEndHour && $endHourEvent > $slotStartHour) {
                                                    $hasEvent = true;
                                                    break;
                                                }
                                            }
                                        } catch (Exception $e) {
                                            continue;
                                        }
                                    }
                                    $cellDateTime = $date->format('Y-m-d') . 'T' . sprintf('%02d:00', $slotStartHour);
                                    $cellDateTimeEnd = $date->format('Y-m-d') . 'T' . sprintf('%02d:00', $slotEndHour);
                                    if (!$hasEvent) {
                                        echo '<div class="bg-transparent-light rounded mb-2 empty-slot"
                                            data-id_classe="' . htmlspecialchars($class['id']) . '" 
                                            data-date="' . $cellDateTime . '" 
                                            data-date_end="' . $cellDateTimeEnd . '" 
                                            onclick="openAddModal(this)">';
                                    
                                        echo '<p class="event-placeholder text-muted m-0" style="font-size: 0.75em; position: absolute; top: 4px; left: 6px; right: 6px;">
                                            Tap to add subject from ' . sprintf('%02d:00', $slotStartHour) . ' to ' . sprintf('%02d:00', $slotEndHour) . '<br>
                                            in class ' . htmlspecialchars($class['class_name']) . '
                                        </p>';
                                    
                                        echo '</div>';
                                        continue;
                                    }
                                    
                                    foreach ($classEmplois as $emploi) {
                                        try {
                                            $startDate = new DateTime($emploi['date_debut'], new DateTimeZone('CET'));
                                            $endDate = new DateTime($emploi['date_fin'], new DateTimeZone('CET'));
                                            if ($startDate->format('H:i:s') === '00:00:00') {
                                                $startDate->setTime(8, 0);
                                                $endDate->setTime(9, 0);
                                            }
                                        } catch (Exception $e) {
                                            continue;
                                        }
                                        if ($date->format('Y-m-d') === $startDate->format('Y-m-d')) {
                                            $startHourEvent = (int) $startDate->format('H');
                                            $endHourEvent = (int) $endDate->format('H');
                                            if ($startHourEvent < $slotEndHour && $endHourEvent > $slotStartHour) {
                                                $matiereNom = $matiereLookup[$emploi['id_matiere']] ?? 'Unknown Matière';
                                                $enseignantNom = $enseignantLookup[$emploi['id_enseignant']] ?? 'Unknown Enseignant';
                                                $courseType = strtolower($emploi['cour_type'] ?? '');
                                                if ($courseType === 'en ligne') {
                                                    $eventClass = 'bg-transparent-primary';
                                                } else {
                                                    $softColors = ['bg-soft-green', 'bg-soft-pink', 'bg-soft-red', 'bg-soft-purple', 'bg-soft-yellow', 'bg-soft-blue'];
                                                    $eventClass = $softColors[array_rand($softColors)];
                                                }
                                                echo '<div class="' . $eventClass . ' open-edit-modal" 
                                                    data-id="' . ($emploi['id_emploi'] ?? '') . '" 
                                                    data-id_matiere="' . ($emploi['id_matiere'] ?? '') . '" 
                                                    data-id_enseignant="' . ($emploi['id_enseignant'] ?? '') . '" 
                                                    data-id_classe="' . ($emploi['id_classe'] ?? '') . '" 
                                                    data-date_debut="' . htmlspecialchars($emploi['date_debut']) . '" 
                                                    data-date_fin="' . htmlspecialchars($emploi['date_fin']) . '" 
                                                    data-cour_type="' . htmlspecialchars($emploi['cour_type'] ?? '') . '" 
                                                    data-location="' . htmlspecialchars($emploi['location_details'] ?? '') . '" 
                                                    onclick="handleEventClick(this, \'' . strtolower($emploi['cour_type'] ?? '') . '\')">';
                                                echo '<p class="mb-1"><i class="ti ti-clock me-1"></i>'
                                                    . htmlspecialchars(sprintf('%02d:00 - %02d:00', $startHourEvent, $endHourEvent)) . '</p>';
                                                echo '<p class="event-subject">' . htmlspecialchars($matiereNom) . '</p>';
                                                echo '<div>' . htmlspecialchars($enseignantNom) . '</div>';
                                                if ($courseType !== 'en ligne') {
                                                    echo '<p class="text-dark mt-1">' . htmlspecialchars($emploi['location_details'] ?? '—') . '</p>';
                                                }
                                                echo '<div class="event-buttons">';
                                                echo '<button class="edit-btn btn-edit-emploi" title="Edit" '
                                                    . 'data-id="' . ($emploi['id_emploi'] ?? '') . '" '
                                                    . 'data-id_matiere="' . ($emploi['id_matiere'] ?? '') . '" '
                                                    . 'data-id_enseignant="' . ($emploi['id_enseignant'] ?? '') . '" '
                                                    . 'data-id_classe="' . ($emploi['id_classe'] ?? '') . '" '
                                                    . 'data-date_debut="' . htmlspecialchars($emploi['date_debut']) . '" '
                                                    . 'data-date_fin="' . htmlspecialchars($emploi['date_fin']) . '" '
                                                    . 'data-cour_type="' . htmlspecialchars($emploi['cour_type'] ?? '') . '" '
                                                    . 'data-location="' . htmlspecialchars($emploi['location_details'] ?? '') . '">✏️</button>';
                                                echo '<button class="delete-btn btn-delete-emploi" title="Delete" '
                                                    . 'data-id="' . ($emploi['id_emploi'] ?? '') . '">🗑️</button>';
                                                echo '</div>';
                                                echo '</div>';
                                            }
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->
<div id="calendar"></div>

<!-- Location Popup -->
<div id="locationPopup" style="
  display: none;
  position: fixed;
  top: 30%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  border: 2px solid #3498db;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  z-index: 1000;
  max-width: 400px;
  width: 90%;
  text-align: center;
  font-family: Arial, sans-serif;
">
  <h4 style="margin-bottom: 10px;">Lien de cours en ligne</h4>
  <p id="popupLink" style="word-break: break-all; font-weight: bold;"></p>
  <button onclick="copyPopupLink()" style="
    background: none;
    border: none;
    font-size: 20px;
    color: #3498db;
    cursor: pointer;
    margin-top: 10px;
  " title="Copier le lien">
    <i class="fa-solid fa-copy"></i>
  </button>
  <span id="copyMessage" style="color: green; font-weight: bold; margin-left: 10px;"></span>
  <br>
  <button onclick="closePopup()" style="
    margin-top: 10px;
    padding: 5px 15px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  ">Fermer</button>
</div>

<!-- Add Emploi Modal -->
<div class="modal fade" id="add_emploi" tabindex="-1" aria-labelledby="addEmploiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="addEmploiForm" method="post">
        <input type="hidden" name="action" value="addEmploi" />
        <div class="modal-header">
          <h4 class="modal-title" id="addEmploiLabel">Add Subject</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="addClassSelect">Classe</label>
            <select id="addClassSelect" class="select" name="id_classe" required>
              <option value="">Select Classe</option>
              <?php foreach ($classes as $class): ?>
              <option value="<?= htmlspecialchars($class['id']) ?>"><?= htmlspecialchars($class['class_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="addMatiereSelect">Matière</label>
            <select id="addMatiereSelect" class="select" name="id_matiere" required>
              <option value="">Select Matière</option>
              <?php foreach ($matieres as $matiere): ?>
              <option value="<?= htmlspecialchars($matiere['id']) ?>"><?= htmlspecialchars($matiere['nom']) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" id="matiereIdInput" name="id_matiere_hidden">
            <input type="hidden" id="matiereNameInput" name="matiere_name">
          </div>

          <div class="mb-3">
            <label for="addEnseignantSelect">Enseignant</label>
            <select id="addEnseignantSelect" class="select" name="id_enseignant" required>
              <option value="">Select Enseignant</option>
              <?php foreach ($enseignants as $enseignant): ?>
              <option 
                value="<?= htmlspecialchars($enseignant['id_enseignant']) ?>"
                data-matiere-id="<?= htmlspecialchars($enseignant['id_matiere']) ?>"
                data-matiere-name="<?= htmlspecialchars($enseignant['nom']) ?>"
              >
                <?= htmlspecialchars($enseignant['nom']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="addDateDebut">Date Début</label>
            <input type="datetime-local" class="form-control" id="addDateDebut" name="date_debut" required>
          </div>

          <div class="mb-3">
            <label for="addDateFin">Date Fin</label>
            <input type="datetime-local" class="form-control" id="addDateFin" name="date_fin" required>
          </div>

          <div class="mb-3">
            <label for="addCourType">Type Cours</label>
            <select id="addCourType" class="select" name="cour_type" required>
              <option value="">Sélectionnez un type</option>
              <option value="Présentiel">Présentiel</option>
              <option value="En ligne">En ligne</option>
            </select>
          </div>

          <div class="mb-3" id="locationField" style="display: none;">
            <label>Détails de Lieu</label>
            <select id="locationSelect" class="form-control" name="location_select" style="display: none;">
              <option value="">Sélectionnez une salle</option>
              <?php foreach ($classrooms as $classroom): ?>
              <option value="<?= htmlspecialchars($classroom['nom']) ?>"><?= htmlspecialchars($classroom['nom']) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" id="locationInput" name="location_input" class="form-control" placeholder="Lien Meet..." style="display: none;">
            <input type="hidden" id="locationHidden" name="location_details" />
          </div>

          <div id="addMessage" class="alert mt-2" style="display: none;"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 310px;">Annuler</button>
          <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Edit Emploi Modal -->
<div class="modal fade" id="editEmploiModal" tabindex="-1" aria-labelledby="editEmploiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editEmploiLabel">Modifier Emploi</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editEmploiForm" method="post">
                <input type="hidden" name="action" value="updateEmploi" />
                <input type="hidden" name="id_emploi" id="editIdEmploi" />
                <div class="modal-body" style="padding-bottom: 0;">
                    <div class="mb-3">
                        <label for="editClasseSelect" class="form-label">Classe</label>
                        <select id="editClasseSelect" class="select" name="id_classe" required>
                            <option value="">Select Classe</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= htmlspecialchars($class['id']) ?>">
                                    <?= htmlspecialchars($class['class_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editMatiereSelect" class="form-label">Matière</label>
                        <select id="editMatiereSelect" class="select" name="id_matiere" required>
                            <option value="">Select Matière</option>
                            <?php foreach ($matieres as $matiere): ?>
                                <option value="<?php echo htmlspecialchars($matiere['id']); ?>">
                                    <?php echo htmlspecialchars($matiere['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="editMatiereIdInput" name="id_matiere_hidden">
                    </div>
                    <div class="mb-3">
                        <label for="editEnseignantSelect" class="form-label">Enseignant</label>
                        <select id="editEnseignantSelect" class="select" name="id_enseignant" required>
                            <option value="">Select</option>
                            <?php foreach ($enseignants as $enseignant): ?>
                                <option 
                                    value="<?= htmlspecialchars($enseignant['id_enseignant']) ?>"
                                    data-matiere-id="<?= htmlspecialchars($enseignant['id_matiere']) ?>"
                                    data-matiere-name="<?= htmlspecialchars($enseignant['nom']) ?>"
                                >
                                    <?= htmlspecialchars($enseignant['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editDateDebut" class="form-label">Date Début</label>
                        <input type="datetime-local" class="form-control" id="editDateDebut" name="date_debut" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDateFin" class="form-label">Date Fin</label>
                        <input type="datetime-local" class="form-control" id="editDateFin" name="date_fin" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCourType" class="form-label">Type Cours</label>
                        <select id="editCourTypeSelect" class="select" name="cour_type" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="Présentiel">Présentiel</option>
                            <option value="En ligne">En ligne</option>
                        </select>
                    </div>
                    <div class="mb-3" id="editLocationField" style="display: none;">
                        <label class="form-label">Détails de Lieu</label>
                        <select id="editLocationSelect" class="form-control" style="display: none;">
                            <option value="">Sélectionnez une salle</option>
                            <?php foreach ($classrooms as $classroom): ?>
                                <option value="<?= htmlspecialchars($classroom['nom']) ?>">
                                    <?= htmlspecialchars($classroom['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" class="form-control" id="editLocationInput" placeholder="Lien Meet..." style="display: none;" />
                        <input type="hidden" id="editLocationHidden" name="location_details" />
                    </div>
                    <div id="editMessage" style="display: none;"></div>
                </div>
                
             

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info" id="btnPresenceManager" style="margin-right: 10px;">
                        Gérer la présence
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 10px;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Enregistrer
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="presenceModal" tabindex="-1" aria-labelledby="presenceLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="width: 300px;">
    <div class="modal-content" id="presenceModalContent">
      
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="presenceLabel">Gestion de la présence</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      
      <!-- Modal Body -->
      <div class="modal-body">
        <div id="presenceTableContainer" style="max-height: 150px; overflow-y: auto;">
          <p>Chargement...</p>
        </div>
      </div>
      
      <!-- Modal Footer -->
      <div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 90px;">Fermer</button>
  <button type="button" class="btn btn-success" id="savePresenceButton">Sauvegarder</button>
</div>

    </div>
  </div>
</div>




<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteEmploiModal" tabindex="-1" aria-labelledby="deleteEmploiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteEmploiForm" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteEmploiLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="deleteEmploi" />
                    <input type="hidden" name="id_emploi" id="deleteIdEmploi" />
                    <p>Voulez-vous vraiment supprimer cet emploi ?</p>
                    <div id="deleteMessage" class="mt-2"></div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 290px;">Non</button>
                    <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
document.getElementById('savePresenceButton').addEventListener('click', function () {
    const toggles = document.querySelectorAll('#presenceTableContainer .presence-toggle');
    const updates = Array.from(toggles).map(toggle => ({
        idEmploi: toggle.dataset.emploiId,
        idStudent: toggle.dataset.studentId,
        status: toggle.checked ? 'Present' : 'Absent'
    }));
    let completed = 0;
    let hasError = false;

    updates.forEach(update => {
        const params = new URLSearchParams({
            action: 'updatePresence',
            id_emploi: update.idEmploi,
            id_student: update.idStudent,
            status: update.status
        });

        fetch('/STAGE/controller/presencecontroller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString()
        })
        .then(res => res.json())
        .then(data => {
            console.log('🔎 Response:', data);

            if (data.status !== 'success') hasError = true;
        })
        .catch(() => hasError = true)
        .finally(() => {
            completed++;
            console.log(`✅ ${completed}/${updates.length} requêtes terminées. Erreur détectée ? ${hasError}`);

            if (completed === updates.length) {
                
               
                    alert(" Toutes les présences ont été sauvegardées avec succès !");
                
            }
        });
    });
});
document.getElementById('presenceModal').addEventListener('hidden.bs.modal', function () {
    document.querySelector('#editEmploiModal .modal-content')?.classList.remove('blur-content');
});

document.getElementById('btnPresenceManager').addEventListener('click', function () {
    const emploiId = document.getElementById('editIdEmploi').value;
    if (!emploiId) return alert('Veuillez d’abord sélectionner un emploi.');

    document.querySelector('#editEmploiModal .modal-content')?.classList.add('blur-content');

    fetch('/STAGE/controller/presencecontroller.php?id_emploi=' + encodeURIComponent(emploiId))
        .then(res => res.json())
        .then(data => {
            if (!Array.isArray(data)) return alert('Erreur inattendue lors du chargement des présences.');

            const rows = data.map(presence => `
                <tr>
                <td>${escapeHtml(presence.username)} ${escapeHtml(presence.student_name)}</td>
                <td>
  <div class="form-check form-switch">
    <input 
      class="form-check-input presence-toggle" 
      type="checkbox"
      role="switch"
      id="presence-${presence.id_emploi}-${presence.id_student}"
      data-emploi-id="${escapeHtml(presence.id_emploi)}" 
      data-student-id="${escapeHtml(presence.id_student)}"
      ${presence.status === 'Present' ? 'checked' : ''}>
    <label class="form-check-label" for="presence-${presence.id_emploi}-${presence.id_student}">
      ${presence.status === 'Present' ? 'Présent' : 'Absent'}
    </label>
  </div>
</td>


                </tr>
            `).join('');
        

            document.getElementById('presenceTableContainer').innerHTML = `
                <table class="table align-middle">
                    <thead><tr><th>Étudiant</th><th>Présence</th></tr></thead>
                    <tbody>${rows}</tbody>
                </table>
            `;
            document.querySelectorAll('.presence-toggle').forEach(toggle => {
  toggle.addEventListener('change', function () {
    const label = this.closest('.form-check').querySelector('.form-check-label');
    label.textContent = this.checked ? 'Présent' : 'Absent';
  });
});
            // Show the presence modal
            new bootstrap.Modal(document.getElementById('presenceModal')).show();
        })
        .catch(() => alert('Erreur lors du chargement des présences.'));
});


function escapeHtml(text) {
    return text?.replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
}
</script>


<script>
// Variables globales accessibles partout
let addEnseignantSelect;
let editEnseignantSelect;
let allEnseignantOptions = [];

function filterEnseignantsByMatiere(matiereId, selectElement) {
    if (!selectElement) {
        console.warn('selectElement not provided');
        return;
    }

    selectElement.innerHTML = ''; // Clear current options

    // Toujours ajouter l'option vide
    const emptyOption = allEnseignantOptions.find(opt => opt.value === "");
    if (emptyOption) {
        selectElement.appendChild(emptyOption.cloneNode(true));
    }

    const matchingEnseignants = [];

    allEnseignantOptions.forEach(option => {
        if (option.value === "") return; // déjà ajoutée

        // Compare les matiereId strictement
        if (option.dataset.matiereId && option.dataset.matiereId.trim() === matiereId.trim()) {
            selectElement.appendChild(option.cloneNode(true));
            matchingEnseignants.push({
                id: option.value,
                name: option.textContent.trim(),
                matiereId: option.dataset.matiereId.trim()
            });
        }
    });

    if (window.jQuery && $.fn.select2) {
        $(selectElement).val(null).trigger('change.select2');
    }

    console.log('Matière sélectionnée :', matiereId);
    console.log('Enseignants disponibles pour cette matière :', matchingEnseignants);
}

function handleEventClick(info) {
    // Assuming FullCalendar event click; adjust based on your actual event structure
    const event = info.event;
    const extendedProps = event.extendedProps || {};

    if (event.id) {
        // Existing event: open Edit modal
        const editBtn = document.createElement('button');
        editBtn.className = 'btn-edit-emploi';
        editBtn.dataset.id = event.id;
        editBtn.dataset.id_classe = extendedProps.id_classe || '';
        editBtn.dataset.id_matiere = extendedProps.id_matiere || '';
        editBtn.dataset.id_enseignant = extendedProps.id_enseignant || '';
        editBtn.dataset.date_debut = event.start ? event.start.toISOString().slice(0, 16) : '';
        editBtn.dataset.date_fin = event.end ? event.end.toISOString().slice(0, 16) : '';
        editBtn.dataset.cour_type = extendedProps.cour_type || '';
        editBtn.dataset.location = extendedProps.location_details || '';

        // Trigger the edit button click programmatically
        editBtn.click();
    } else {
        // New event (e.g., clicking on a calendar slot): open Add modal
        const addElement = document.createElement('div');
        addElement.dataset.id_classe = extendedProps.id_classe || '';
        addElement.dataset.date = event.start ? event.start.toISOString().slice(0, 16) : '';
        addElement.dataset.date_end = event.end ? event.end.toISOString().slice(0, 16) : '';
        addElement.dataset.matiereId = extendedProps.id_matiere || '';

        openAddModal(addElement);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    addEnseignantSelect = document.getElementById('addEnseignantSelect');
    editEnseignantSelect = document.getElementById('editEnseignantSelect');
    allEnseignantOptions = Array.from(addEnseignantSelect.options).map(opt => opt.cloneNode(true));

    const matiereSelect = document.getElementById('addMatiereSelect');
    const editMatiereSelect = document.getElementById('editMatiereSelect');

    if (matiereSelect) {
        matiereSelect.addEventListener('change', function () {
            filterEnseignantsByMatiere(this.value, addEnseignantSelect);
        });

        if (window.jQuery && $.fn.select2) {
            $(matiereSelect).on('select2:select', function () {
                filterEnseignantsByMatiere(this.value, addEnseignantSelect);
            });
        }
    }

    if (editMatiereSelect) {
        editMatiereSelect.addEventListener('change', function () {
            filterEnseignantsByMatiere(this.value, editEnseignantSelect);
        });

        if (window.jQuery && $.fn.select2) {
            $(editMatiereSelect).on('select2:select', function () {
                filterEnseignantsByMatiere(this.value, editEnseignantSelect);
            });
        }
    }
});

function openAddModal(element) {
    const classeId = element.dataset.id_classe;
    const dateStart = element.dataset.date;
    const dateEnd = element.dataset.date_end;
    const matiereId = element.dataset.matiereId || '';

    console.log('openAddModal called for classe:', classeId, 'date start:', dateStart, 'date end:', dateEnd, 'matiereId:', matiereId);

    const classSelect = document.getElementById('addClassSelect');
    const dateDebutInput = document.getElementById('addDateDebut');
    const dateFinInput = document.getElementById('addDateFin');
    const matiereSelect = document.getElementById('addMatiereSelect');

    if (classSelect) {
        classSelect.value = classeId;
        if (window.jQuery && $(classSelect).select2) {
            $(classSelect).trigger('change.select2');
        }
    }
    if (dateDebutInput) dateDebutInput.value = dateStart;
    if (dateFinInput) dateFinInput.value = dateEnd;

    if (matiereSelect) {
        matiereSelect.value = matiereId;
        if (window.jQuery && $(matiereSelect).select2) {
            $(matiereSelect).trigger('change.select2');
        }

        filterEnseignantsByMatiere(matiereId, addEnseignantSelect);
    }

    const addEmploiModalElement = document.getElementById('add_emploi');
    if (addEmploiModalElement) {
        const modal = new bootstrap.Modal(addEmploiModalElement);
        modal.show();
    } else {
        console.warn('Modal element with ID add_emploi not found');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const addEmploiModalElement = document.getElementById('add_emploi');
    const editModal = new bootstrap.Modal(document.getElementById('editEmploiModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteEmploiModal'));

    addEnseignantSelect = document.getElementById('addEnseignantSelect');
    editEnseignantSelect = document.getElementById('editEnseignantSelect');
    if (addEnseignantSelect) {
        allEnseignantOptions = Array.from(addEnseignantSelect.options).map(opt => opt.cloneNode(true));
    }

    const matiereSelect = document.getElementById('addMatiereSelect');
    const editMatiereSelect = document.getElementById('editMatiereSelect');
    const editCourTypeSelect = document.getElementById('editCourTypeSelect');
    const editLocationField = document.getElementById('editLocationField');
    const editLocationSelect = document.getElementById('editLocationSelect');
    const editLocationInput = document.getElementById('editLocationInput');
    const editLocationHidden = document.getElementById('editLocationHidden');

    if (matiereSelect) {
        matiereSelect.addEventListener('change', function () {
            filterEnseignantsByMatiere(this.value, addEnseignantSelect);
            console.log('matiereSelect changed to:', this.value);
        });
        if ($.fn.select2) {
            $(matiereSelect).on('select2:select', function () {
                console.log('matiereSelect changed (select2):', this.value);
                filterEnseignantsByMatiere(this.value, addEnseignantSelect);
            });
        }
    }

    if (editMatiereSelect) {
        editMatiereSelect.addEventListener('change', function () {
            filterEnseignantsByMatiere(this.value, editEnseignantSelect);
            console.log('editMatiereSelect changed to:', this.value);
        });
        if ($.fn.select2) {
            $(editMatiereSelect).on('select2:select', function () {
                console.log('editMatiereSelect changed (select2):', this.value);
                filterEnseignantsByMatiere(this.value, editEnseignantSelect);
            });
        }
    }

    function initializeSelect2(selectElement, placeholder) {
        if ($.fn.select2 && selectElement) {
            $(selectElement).select2({
                width: '100%',
                placeholder: placeholder || 'Select an option',
                allowClear: true
            });
        }
    }

    function toggleEditLocationField() {
        const courType = editCourTypeSelect.value;
        if (courType === 'Présentiel') {
            editLocationField.style.display = 'block';
            editLocationSelect.style.display = 'block';
            editLocationSelect.required = true;
            editLocationInput.style.display = 'none';
            editLocationInput.required = false;
            editLocationInput.value = '';
        } else if (courType === 'En ligne') {
            editLocationField.style.display = 'block';
            editLocationInput.style.display = 'block';
            editLocationInput.required = true;
            editLocationSelect.style.display = 'none';
            editLocationSelect.required = false;
            editLocationSelect.value = '';
        } else {
            editLocationField.style.display = 'none';
            editLocationSelect.style.display = 'none';
            editLocationInput.style.display = 'none';
        }
    }

    if (editCourTypeSelect) {
        editCourTypeSelect.addEventListener('change', toggleEditLocationField);
        if (window.jQuery && $.fn.select2) {
            $(editCourTypeSelect).on('select2:select', toggleEditLocationField);
        }
    }

    if (addEmploiModalElement) {
        addEmploiModalElement.addEventListener('shown.bs.modal', function () {
            const enseignantSelectInside = document.getElementById('addEnseignantSelect');
            const matiereNameInput = document.getElementById('matiereNameInput');
            const matiereIdInput = document.getElementById('matiereIdInput');
            const courTypeSelect = document.getElementById('addCourType');
            const locationField = document.getElementById('locationField');
            const locationSelect = document.getElementById('locationSelect');
            const locationInput = document.getElementById('locationInput');
            const locationHidden = document.getElementById('locationHidden');

            if (enseignantSelectInside && $.fn.select2) {
                $(enseignantSelectInside).select2('destroy').off('select2:select select2:clear');
                $(courTypeSelect).select2('destroy').off('select2:select select2:clear');
            }

            initializeSelect2(document.getElementById('addClassSelect'), 'Select Classe');
            initializeSelect2(enseignantSelectInside, 'Select Enseignant');
            initializeSelect2(courTypeSelect, 'Select Type Cours');

            $(enseignantSelectInside).on('select2:select', function (e) {
                const selectedOption = e.target.selectedOptions[0];
                matiereNameInput.value = selectedOption?.dataset.matiereName || '';
                matiereIdInput.value = selectedOption?.dataset.matiereId || '';
            });

            $(enseignantSelectInside).on('select2:clear', function () {
                matiereNameInput.value = '';
                matiereIdInput.value = '';
            });

            function toggleLocationField() {
                const courType = courTypeSelect.value;
                if (courType === 'Présentiel') {
                    locationField.style.display = 'block';
                    locationSelect.style.display = 'block';
                    locationSelect.required = true;
                    locationInput.style.display = 'none';
                    locationInput.required = false;
                    locationInput.value = '';
                } else if (courType === 'En ligne') {
                    locationField.style.display = 'block';
                    locationInput.style.display = 'block';
                    locationInput.required = true;
                    locationSelect.style.display = 'none';
                    locationSelect.required = false;
                    locationSelect.value = '';
                } else {
                    locationField.style.display = 'none';
                    locationSelect.style.display = 'none';
                    locationInput.style.display = 'none';
                }
            }

            setTimeout(() => {
                toggleLocationField();
                $(courTypeSelect).trigger('change.select2');
            }, 0);

            $(courTypeSelect).on('select2:select', toggleLocationField);

            document.getElementById('addEmploiForm').addEventListener('submit', async function (e) {
                e.preventDefault();
                locationHidden.value = courTypeSelect.value === 'Présentiel' ? locationSelect.value : locationInput.value;

                const formData = new FormData(this);
                try {
                    const res = await fetch('/STAGE/controller/EmploiController.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await res.json();
                    const msg = document.getElementById('addMessage');
                    msg.style.display = 'block';
                    msg.style.color = result.status === 'success' ? 'green' : 'red';
                    msg.textContent = result.message;
                    if (result.status === 'success') {
                        this.reset();
                        $(enseignantSelectInside).val(null).trigger('change');
                        setTimeout(() => location.reload(), 1500);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });
    }

    // Edit Emploi buttons
    document.querySelectorAll('.btn-edit-emploi').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('editIdEmploi').value = btn.dataset.id;
            document.getElementById('editClasseSelect').value = btn.dataset.id_classe;
            document.getElementById('editDateDebut').value = btn.dataset.date_debut;
            document.getElementById('editDateFin').value = btn.dataset.date_fin;
            document.getElementById('editMatiereSelect').value = btn.dataset.id_matiere;

            const enseignantSelectEdit = document.getElementById('editEnseignantSelect');
            const matiereIdInput = document.getElementById('editMatiereIdInput');
            const courTypeSelect = document.getElementById('editCourTypeSelect');
            courTypeSelect.value = btn.dataset.cour_type;
            const courType = btn.dataset.cour_type;
            const location = btn.dataset.location || '';
            const select = document.getElementById('editLocationSelect');
            const input = document.getElementById('editLocationInput');
            const locationDetails = document.getElementById('editLocationHidden');

            // Initialize location field visibility and value
            if (courType === 'Présentiel') {
                editLocationField.style.display = 'block';
                select.style.display = 'block';
                select.required = true;
                input.style.display = 'none';
                input.required = false;
                select.value = location;
                input.value = '';
            } else if (courType === 'En ligne') {
                editLocationField.style.display = 'block';
                input.style.display = 'block';
                input.required = true;
                select.style.display = 'none';
                select.required = false;
                input.value = location;
                select.value = '';
            } else {
                editLocationField.style.display = 'none';
                select.style.display = 'none';
                input.style.display = 'none';
            }

            locationDetails.value = location;

            // Filter enseignants based on matiereId and set the selected enseignant
            filterEnseignantsByMatiere(btn.dataset.id_matiere, editEnseignantSelect);
            enseignantSelectEdit.value = btn.dataset.id_enseignant;

            // Set matiereIdInput based on selected enseignant
            const selectedOption = enseignantSelectEdit.querySelector(`option[value="${btn.dataset.id_enseignant}"]`);
            if (selectedOption) {
                matiereIdInput.value = selectedOption.dataset.matiereId || '';
            }

            if ($.fn.select2) {
                $('#editClasseSelect').select2({ width: '100%', placeholder: 'Select Classe', allowClear: true });
                $('#editMatiereSelect').select2({ width: '100%', placeholder: 'Select Matière', allowClear: true });
                $('#editEnseignantSelect').select2({ width: '100%', placeholder: 'Select Enseignant', allowClear: true });
                $('#editCourTypeSelect').select2({ width: '100%', placeholder: 'Select Type Cours', allowClear: true });

                // Trigger change to update Select2 display
                $(editMatiereSelect).trigger('change.select2');
                $(enseignantSelectEdit).trigger('change.select2');
                $(courTypeSelect).trigger('change.select2');
            }

            // Trigger the location field toggle to ensure correct state
            toggleEditLocationField();

            editModal.show();
        });
    });

    // Delete Emploi buttons
   
    document.querySelectorAll('.btn-delete-emploi').forEach(btn => {
    btn.addEventListener('click', () => {
        const emploiId = btn.dataset.id;
        console.log('[Delete Button Clicked] Emploi ID:', emploiId);

        document.getElementById('deleteIdEmploi').value = emploiId;
        deleteModal.show();
    });
});

document.getElementById('deleteEmploiForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    console.log('[Submitting Delete Form]');
    for (const pair of formData.entries()) {
        console.log(`FormData: ${pair[0]} = ${pair[1]}`);
    }

    try {
        const res = await fetch('/STAGE/controller/EmploiController.php', {
            method: 'POST',
            body: formData
        });

        const text = await res.text(); // Capture raw response for debugging
        console.log('[Raw Server Response]', text);

        let result;
        try {
            result = JSON.parse(text);
        } catch (jsonErr) {
            console.error('[JSON Parse Error]', jsonErr);
            console.error('[Failed Response Was]', text);
            return;
        }

        const msg = document.getElementById('deleteMessage');
        msg.style.display = 'block';
        msg.style.color = result.status === 'success' ? 'green' : 'red';
        msg.textContent = result.message;

        if (result.status === 'success') {
            console.log('[Delete Success] Reloading page...');
            setTimeout(() => {
                deleteModal.hide();
                location.reload();
            }, 1500);
        } else {
            console.warn('[Delete Failed]', result.message);
        }
    } catch (err) {
        console.error('[Delete Error]:', err);
    }
});

});

document.getElementById('editEmploiForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const courTypeSelect = document.getElementById('editCourTypeSelect');
    const locationSelect = document.getElementById('editLocationSelect');
    const locationInput = document.getElementById('editLocationInput');
    const locationHidden = document.getElementById('editLocationHidden');

    locationHidden.value = courTypeSelect.value === 'Présentiel' ? locationSelect.value : locationInput.value;

    const formData = new FormData(this);
    try {
        const res = await fetch('/STAGE/controller/EmploiController.php', {
            method: 'POST',
            body: formData
        });
        const result = await res.json();
        const msg = document.getElementById('editMessage');
        msg.style.display = 'block';
        msg.style.color = result.status === 'success' ? 'green' : 'red';
        msg.textContent = result.message;
        if (result.status === 'success') {
            this.reset();
            setTimeout(() => location.reload(), 1500);
        }
    } catch (error) {
        console.error('Error:', error);
    }
});
function handleEventClick(element, courseType) {
    const location = element.getAttribute('data-location') || '';

    if (courseType === 'en ligne') {
        showLocationPopup(location); // pass string only
    }
}

// Show the popup with the location link (expects a string now)
function showLocationPopup(location) {
    const popup = document.getElementById('locationPopup');
    const linkPara = document.getElementById('popupLink');

    linkPara.textContent = location;
    popup.style.display = 'block';
}

// Copy the link to clipboard
function copyPopupLink() {
    const link = document.getElementById('popupLink').textContent;
    navigator.clipboard.writeText(link).then(() => {
        const messageEl = document.getElementById('copyMessage');
        messageEl.textContent = 'Lien copié !';
        setTimeout(() => {
            messageEl.textContent = '';
        }, 2000);
    }).catch(err => {
        console.error('Erreur lors de la copie :', err);
    });
}

// Close the popup
function closePopup() {
    document.getElementById('locationPopup').style.display = 'none';
}
</script>

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