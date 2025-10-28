<?php
require_once __DIR__ . '/../../controller/session_config.php';  
if (!isset($_SESSION['admin'])) {
    // Not logged in, redirect to login
    header('Location: login.php');
    exit;
  }
  $admin = $_SESSION['admin'];
  
  



include_once __DIR__ . '/../../controller/EnseignantController.php';
include_once __DIR__ . '/../../controller/MatiereController.php'; // Assuming a MatiereController exists

$controller = new ControllerEnseignant();
$enseignants = $controller->listEnseignants();

$matiereController = new ControllerMatiere(); // Assuming this controller exists
$matieres = $matiereController->listMatieres(); // Assuming this method exists
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
                                <a href="javascript:void(0);"  class="subdrop active"><i class="ti ti-user"></i><span>Teachers</span><span class="menu-arrow"></span></a>
									<ul>
										<li><a href="addenseignant.php"class="active">All Teachers</a></li>
									
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
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Teachers List</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.html">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Teachers</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">All Teachers</li>
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
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_enseignant"><i class="fas fa-plus-square me-2"></i>Add Teacher</a>
                        </div>
                    </div>
                </div>
                <!-- Teachers List -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Teachers List</h4>
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
                                        <th>Name</th>
                                        <th>Subject</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enseignants as $enseignant): ?>
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input" type="checkbox" name="select-enseignant[]" value="<?= htmlspecialchars($enseignant['id_enseignant']) ?>">
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($enseignant['id_enseignant']) ?></td>
                                            <td><?= htmlspecialchars($enseignant['nom']) ?></td>
                                            <td><?= htmlspecialchars($enseignant['matiere_name']) ?></td>
                                            <td><?= htmlspecialchars($enseignant['email']) ?></td>
                                            <td><?= htmlspecialchars($enseignant['phone']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary btn-edit-enseignant"
                                                    data-id="<?= htmlspecialchars($enseignant['id_enseignant']) ?>"
                                                    data-nom="<?= htmlspecialchars($enseignant['nom']) ?>"
                                                    data-id_matiere="<?= htmlspecialchars($enseignant['id_matiere']) ?>"
                                                    data-email="<?= htmlspecialchars($enseignant['email']) ?>"
                                                    data-phone="<?= htmlspecialchars($enseignant['phone']) ?>">Edit</button>
                                                <button class="btn btn-sm btn-danger btn-delete-enseignant"
                                                    data-id="<?= htmlspecialchars($enseignant['id_enseignant']) ?>">Delete</button>
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
        <!-- Add Teacher Modal -->
        <div class="modal fade" id="add_enseignant" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Teacher</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addEnseignantForm" method="post" action="/STAGE/controller/EnseignantController.php">
                        <input type="hidden" name="action" value="addEnseignant" />
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nomInput" class="form-label">Name</label>
                                <input type="text" class="form-control" name="nom" id="nomInput" placeholder="Enter Teacher Name" required />
                            </div>
                            <div class="mb-3">
    <label for="matiereSelect" class="form-label">Subject</label>
    <select id="matiereSelect" class="select" name="id_matiere" required>
        <option value="">Select Subject</option>
        <?php
        foreach ($matieres as $matiere):
            $matiereId = htmlspecialchars($matiere['id'] ?? '');
            $matiereName = htmlspecialchars($matiere['nom'] ?? '');
        ?>
            <option value="<?= $matiereId ?>" data-matiere-name="<?= $matiereName ?>">
                <?= $matiereName ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                            <div class="mb-3">
                                <label for="emailInput" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="emailInput" placeholder="Enter Email" required />
                            </div>
                            <div class="mb-3">
                                <label for="phoneInput" class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phoneInput" placeholder="Enter Phone Number" required />
                            </div>
                            <div id="message" style="margin-top: 10px; display: none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 290px;">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Teacher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Teacher Modal -->
        <div class="modal fade" id="editEnseignantModal" tabindex="-1" aria-labelledby="editEnseignantLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editEnseignantForm" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEnseignantLabel">Edit Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="updateEnseignant" />
                    <input type="hidden" name="id" id="editEnseignantId" />
                    <div class="mb-3">
                        <label for="editNom" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editNom" name="nom" placeholder="Enter Teacher Name" required />
                    </div>
                    <div class="mb-3">
                        <label for="editMatiereSelect" class="form-label">Subject</label>
                        <select id="editMatiereSelect" class="select" name="id_matiere" required>
                            <option value="">Select Subject</option>
                            <?php foreach ($matieres as $matiere): ?>
                                <option value="<?= htmlspecialchars($matiere['id'] ?? '') ?>" data-matiere-name="<?= htmlspecialchars($matiere['nom'] ?? '') ?>">
                                    <?= htmlspecialchars($matiere['nom'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" placeholder="Enter Email" required />
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="editPhone" name="phone" placeholder="Enter Phone Number" required />
                    </div>
                    <div id="editMessage" style="margin-top: 10px;"></div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"  style="margin-right: 270px;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Delete Teacher Modal -->
<div class="modal fade" id="deleteEnseignantModal" tabindex="-1" aria-labelledby="deleteEnseignantLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteEnseignantForm" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteEnseignantLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="deleteEnseignant" />
                    <input type="hidden" name="id" id="deleteEnseignantId" />
                    <p>Are you sure you want to delete this teacher?</p>
                    <div id="deleteMessage" style="margin-top: 10px;"></div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"  style="margin-right: 280px;">No, Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>
        <!-- View Teacher Modal -->
        <div class="modal fade" id="view_enseignant">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title">Teacher Details</h4>
                            <span class="badge badge-soft-success ms-2"><i class="fas fa-circle me-1 fs-5"></i>Active</span>
                        </div>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="teachers.html">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>ID</p>
                                        <span id="viewId"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>Name</p>
                                        <span id="viewNom"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>Subject</p>
                                        <span id="viewMatiere"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>Email</p>
                                        <span id="viewEmail"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-info">
                                        <p>Phone</p>
                                        <span id="viewPhone"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script> 
document.addEventListener('DOMContentLoaded', function() {
    const addEnseignantModal = document.getElementById('add_enseignant');
    const editEnseignantModal = document.getElementById('editEnseignantModal');
    const deleteEnseignantModal = document.getElementById('deleteEnseignantModal');
    const viewEnseignantModal = document.getElementById('view_enseignant');

    if (!addEnseignantModal) console.error('add_enseignant modal not found in DOM');
    if (!editEnseignantModal) console.error('editEnseignantModal not found in DOM');
    if (!deleteEnseignantModal) console.error('deleteEnseignantModal not found in DOM');
    if (!viewEnseignantModal) console.error('viewEnseignantModal not found in DOM');

    const addModal = addEnseignantModal ? new bootstrap.Modal(addEnseignantModal) : null;
    const editModal = editEnseignantModal ? new bootstrap.Modal(editEnseignantModal) : null;
    const deleteModal = deleteEnseignantModal ? new bootstrap.Modal(deleteEnseignantModal) : null;
    const viewModal = viewEnseignantModal ? new bootstrap.Modal(viewEnseignantModal) : null;

    // Handle Edit button click
    const editButtons = document.querySelectorAll('.btn-edit-enseignant');
    console.log('Edit buttons found:', editButtons.length);
    editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (!editModal) {
                console.error('Edit modal not initialized');
                return;
            }
            const id = btn.dataset.id;
            const nom = btn.dataset.nom;
            const id_matiere = btn.dataset.id_matiere;
            const email = btn.dataset.email;
            const phone = btn.dataset.phone;

            console.log('Edit clicked:', { id, nom, id_matiere, email, phone });

            document.getElementById('editEnseignantId').value = id;
            document.getElementById('editNom').value = nom;
            document.getElementById('editMatiereSelect').value = id_matiere;
            document.getElementById('editEmail').value = email;
            document.getElementById('editPhone').value = phone;

            editModal.show();
        });
    });

    // Handle Delete button click
    const deleteButtons = document.querySelectorAll('.btn-delete-enseignant');
    console.log('Delete buttons found:', deleteButtons.length);
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (!deleteModal) {
                console.error('Delete modal not initialized');
                return;
            }
            const id = btn.dataset.id;
            console.log('Delete clicked:', { id });

            document.getElementById('deleteEnseignantId').value = id;
            document.getElementById('deleteMessage').textContent = '';
            deleteModal.show();
        });
    });

    // Handle View button click
    const viewButtons = document.querySelectorAll('.btn-view-enseignant');
    console.log('View buttons found:', viewButtons.length);
    viewButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (!viewModal) {
                console.error('View modal not initialized');
                return;
            }
            const id = btn.dataset.id;
            const nom = btn.dataset.nom;
            const matiere = btn.dataset.matiere_name;
            const email = btn.dataset.email;
            const phone = btn.dataset.phone;

            console.log('View clicked:', { id, nom, matiere, email, phone });

            document.getElementById('viewId').textContent = id || 'N/A';
            document.getElementById('viewNom').textContent = nom || 'N/A';
            document.getElementById('viewMatiere').textContent = matiere || 'N/A';
            document.getElementById('viewEmail').textContent = email || 'N/A';
            document.getElementById('viewPhone').textContent = phone || 'N/A';

            viewModal.show();
        });
    });

    // Add Teacher form submission with fetch AJAX
    const addEnseignantForm = document.getElementById('addEnseignantForm');
    const addMessageDiv = document.getElementById('message');
    if (addEnseignantForm && addMessageDiv) {
        console.log('Add Teacher form and message div found');
        addEnseignantForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            addMessageDiv.style.display = 'none';
            addMessageDiv.textContent = '';

            const formData = new FormData(addEnseignantForm);
            const nom = formData.get('nom').trim();
            const email = formData.get('email').trim();
            const phone = formData.get('phone').trim();
            const id_matiere = formData.get('id_matiere');

            // Client-side validation
            if (!nom) {
                addMessageDiv.style.display = 'block';
                addMessageDiv.style.color = 'red';
                addMessageDiv.textContent = 'Please enter a name.';
                return;
            }
            if (!id_matiere) {
                addMessageDiv.style.display = 'block';
                addMessageDiv.style.color = 'red';
                addMessageDiv.textContent = 'Please select a subject.';
                return;
            }
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                addMessageDiv.style.display = 'block';
                addMessageDiv.style.color = 'red';
                addMessageDiv.textContent = 'Please enter a valid email.';
                return;
            }
          

            try {
                const response = await fetch('/STAGE/controller/EnseignantController.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('Add response:', result);

                addMessageDiv.style.display = 'block';
                if (result.success) {
                    addMessageDiv.style.color = 'green';
                    addMessageDiv.textContent = 'Enseignant ajouté avec succès !';
                    addEnseignantForm.reset();
                    setTimeout(() => {
                        addModal.hide();
                        window.location.reload(); // Refresh to remove deleted teacher

                        // No reload, just update UI if needed (e.g., re-fetch teachers list)
                    }, 1500);
                } else {
                    addMessageDiv.style.color = 'red';
                    addMessageDiv.textContent = result.message || 'Failed to add teacher.';
                }
            } catch (error) {
                console.error('Add teacher error:', error);
                addMessageDiv.style.display = 'block';
                addMessageDiv.style.color = 'red';
                addMessageDiv.textContent = 'An error occurred while adding the teacher.';
            }
        });
    } else {
        console.error('Add form or message div not found');
    }

    // Edit Teacher form submission with fetch AJAX
    const editEnseignantForm = document.getElementById('editEnseignantForm');
    const editMessageDiv = document.getElementById('editMessage');
    if (editEnseignantForm && editMessageDiv) {
        console.log('Edit Teacher form and message div found');
        editEnseignantForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            editMessageDiv.style.display = 'none';
            editMessageDiv.textContent = '';

            const formData = new FormData(editEnseignantForm);
            const nom = formData.get('nom').trim();
            const email = formData.get('email').trim();
            const phone = formData.get('phone').trim();
            const id_matiere = formData.get('id_matiere');

            // Client-side validation
            if (!nom) {
                editMessageDiv.style.display = 'block';
                editMessageDiv.style.color = 'red';
                editMessageDiv.textContent = 'Please enter a name.';
                return;
            }
            if (!id_matiere) {
                editMessageDiv.style.display = 'block';
                editMessageDiv.style.color = 'red';
                editMessageDiv.textContent = 'Please select a subject.';
                return;
            }
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                editMessageDiv.style.display = 'block';
                editMessageDiv.style.color = 'red';
                editMessageDiv.textContent = 'Please enter a valid email.';
                return;
            }
           

            try {
                const response = await fetch('/STAGE/controller/EnseignantController.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('Edit response:', result);

                editMessageDiv.style.display = 'block';
                if (result.status === 'success') {
    editMessageDiv.style.color = 'green';
    editMessageDiv.textContent = result.message || 'Teacher updated successfully!';
    setTimeout(() => {
        editModal.hide();
        window.location.reload(); // Refresh to show updated teacher
    }, 1500);
} else {
    editMessageDiv.style.color = 'red';
    editMessageDiv.textContent = result.message || 'Failed to update teacher.';
}

            } catch (error) {
                console.error('Edit teacher error:', error);
                editMessageDiv.style.display = 'block';
                editMessageDiv.style.color = 'red';
                editMessageDiv.textContent = 'An error occurred while updating the teacher.';
            }
        });
    } else {
        console.error('Edit form or message div not found');
    }

    // Delete Teacher form submission with fetch AJAX
    const deleteEnseignantForm = document.getElementById('deleteEnseignantForm');
    const deleteMessageDiv = document.getElementById('deleteMessage');
    if (deleteEnseignantForm && deleteMessageDiv) {
        console.log('Delete Teacher form and message div found');
        deleteEnseignantForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            deleteMessageDiv.style.display = 'none';
            deleteMessageDiv.textContent = '';

            const formData = new FormData(deleteEnseignantForm);
            const id = formData.get('id');

            if (!id) {
                deleteMessageDiv.style.display = 'block';
                deleteMessageDiv.style.color = 'red';
                deleteMessageDiv.textContent = 'Invalid teacher ID.';
                return;
            }

            try {
                const response = await fetch('/STAGE/controller/EnseignantController.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('Delete response:', result);

                deleteMessageDiv.style.display = 'block';
                if (result.status === 'success') {
    deleteMessageDiv.style.color = 'green';
    deleteMessageDiv.textContent = result.message || 'Teacher deleted successfully!';
    setTimeout(() => {
        deleteModal.hide();
        window.location.reload(); // Refresh to remove deleted teacher
    }, 1500);
} else {
    deleteMessageDiv.style.color = 'red';
    deleteMessageDiv.textContent = result.message || 'Failed to delete teacher.';
}

            } catch (error) {
                console.error('Delete teacher error:', error);
                deleteMessageDiv.style.display = 'block';
                deleteMessageDiv.style.color = 'red';
                deleteMessageDiv.textContent = 'An error occurred while deleting the teacher.';
            }
        });
    } else {
        console.error('Delete form or message div not found');
    }

    // Initialize Select2 for subject dropdowns
    if (typeof $.fn.select2 !== 'undefined') {
        $('#matiereSelect, #editMatiereSelect').select2({
            placeholder: 'Select Subject',
            allowClear: true
        });
    } else {
        console.warn('Select2 library not loaded');
    }

  

    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="select-enseignant[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }

    // Reset forms when modals are hidden
    addEnseignantModal?.addEventListener('hidden.bs.modal', () => {
        addEnseignantForm?.reset();
        addMessageDiv.style.display = 'none';
        addMessageDiv.textContent = '';
    });

    editEnseignantModal?.addEventListener('hidden.bs.modal', () => {
        editEnseignantForm?.reset();
        editMessageDiv.style.display = 'none';
        editMessageDiv.textContent = '';
    });

    deleteEnseignantModal?.addEventListener('hidden.bs.modal', () => {
        deleteEnseignantForm?.reset();
        deleteMessageDiv.style.display = 'none';
        deleteMessageDiv.textContent = '';
    });
});
    </script>
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