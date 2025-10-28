<?php
require_once __DIR__ . '/../../controller/session_config.php';  

if (!isset($_SESSION['admin'])) {
    // Not logged in, redirect to login
    header('Location: login.php');
    exit;
  }
  $admin = $_SESSION['admin'];
  
  



include_once __DIR__ . '/../../controller/chargeController.php';

$controller = new ControllerCharge();
$charges = $controller->listCharges();
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

    <!-- Daterangepicker CSS -->
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
                                                <a href="add-student.html" class="d-block bg-primary-transparent ronded p-2 text-center mb-3 feas
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
                                        <a href="#" class="text-primary fs-15 lh-1">Mark all as read</a>
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
                                    <a href="javascript:void(0);"><i class="ti ti-chalkboard"></i><span>Classes</span><span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="addclass.php">All Classes</a></li>
                                        <li><a href="addemploi.php"><i class="ti ti-calendar-event lerp-4"></i>Schedule</a></li>
                                    </ul>
                                </li>
                                <li><a href="addclassroom.php"><i class="ti ti-building"></i><span>Class Room</span></a></li>
                                <li><a href="addmatier.php"><i class="ti ti-book"></i><span>Subject</span></a></li>
                            </ul>
                            <h6 class="submenu-hdr"><span>MODULE COMPTABILITÉ</span></h6>
                            <ul>
                            <li><a href="listpaiement.php"><i class="ti ti-book"></i><span>Paiement</span></a></li>

                                <li class="active"><a href="addcharge.php"><i class="ti ti-book"></i><span>Charge</span></a></li>
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
                        <h3 class="page-title mb-1">Charges List</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.html">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">Charges</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">All Charges</li>
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
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_charge"><i class="fas fa-plus-square me-2"></i>Add Charge</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Charges List -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Charges List</h4>
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
                                    <li><a href="#" class="dropdown-item rounded-1 active" data-sort="asc">Sort A-Z (Charge Type)</a></li>
                                    <li><a href="#" class="dropdown-item rounded-1" data-sort="desc">Sort Z-A (Charge Type)</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable" id="chargeTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Charge Type</th>
                                        <th>Amount</th>
                                        <th>Charge Date</th>
                                        <th>Payment Date</th>
                                        <th>Payment Method</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($charges)): ?>
                                        <?php foreach ($charges as $charge): ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox" name="select_charge[]" value="<?= htmlspecialchars($charge['id']) ?>">
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($charge['id']) ?></td>
                                                <td><?= htmlspecialchars($charge['charge_type']) ?></td>
                                                <td><?= htmlspecialchars(number_format($charge['amount'], 3)) ?></td>
                                                <td><?= htmlspecialchars($charge['charge_date']) ?></td>
                                                <td><?= htmlspecialchars($charge['date_versement'] == '0000-00-00' ? '-' : $charge['date_versement']) ?></td>
                                                <td><?= htmlspecialchars($charge['payment_method']) ?></td>
                                                <td>
                                                <button class="btn btn-sm btn-primary btn-edit-charge"
                                                        data-id="<?= htmlspecialchars($charge['id']) ?>"
                                                        data-charge_type="<?= htmlspecialchars($charge['charge_type']) ?>"
                                                        data-amount="<?= htmlspecialchars($charge['amount']) ?>"
                                                        data-charge_date="<?= htmlspecialchars($charge['charge_date']) ?>"
                                                        data-date_versement="<?= htmlspecialchars($charge['date_versement']) ?>"
                                                        data-reception_date="<?= htmlspecialchars($charge['reception_date']) ?>"
                                                        data-payment_method="<?= htmlspecialchars($charge['payment_method']) ?>">
                                                    Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-delete-charge"
                                                        data-id="<?= htmlspecialchars($charge['id']) ?>">
                                                    Delete
                                                </button>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" style="text-align:center;">No charges found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /Charges List -->
            </div>
        </div>
        <!-- /Page Wrapper -->

        <!-- Add Charge Modal -->
        <div class="modal fade" id="add_charge" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Charge</h4>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <form id="addChargeForm" method="POST">
          <input type="hidden" name="action" value="addCharge">

          <!-- Payment Method -->
          <div class="mb-3">
            <label class="form-label">Payment Method</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Espèce" id="pm_espece">
              <label class="form-check-label" for="pm_espece">Espèce</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Chèque bancaire" id="pm_cb">
              <label class="form-check-label" for="pm_cb">Chèque bancaire</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Chèque postale" id="pm_cp">
              <label class="form-check-label" for="pm_cp">Chèque postale</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Versement bancaire" id="pm_vb">
              <label class="form-check-label" for="pm_vb">Versement bancaire</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Versement postale" id="pm_vp">
              <label class="form-check-label" for="pm_vp">Versement postale</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Carte bancaire" id="pm_cbk">
              <label class="form-check-label" for="pm_cbk">Carte bancaire</label>
            </div>
          </div>

          <!-- Charge Type (Text Input, no options) -->
          <div class="mb-3">
            <label for="charge_type_add" class="form-label">Charge Type</label>
            <input type="text" class="form-control" name="charge_type" id="charge_type_add" placeholder="Enter charge type" required>
          </div>

          <!-- Reception Date -->
          <div class="mb-3">
            <label class="form-label">Reception Date</label>
            <input type="date" class="form-control" name="reception_date" id="reception_date_add" required>
          </div>

          <!-- Amount -->
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" class="form-control" name="amount" placeholder="Enter Amount" step="0.001" required>
          </div>

          <!-- Charge Date -->
          <?php date_default_timezone_set('Africa/Tunis'); $today = date('Y-m-d'); ?>
          <div class="mb-3">
            <label class="form-label">Charge Date</label>
            <input type="date" class="form-control" name="charge_date" value="<?= $today ?>" required>
          </div>

          <!-- Conditional Payment Date -->
          <div class="mb-3" id="date_versement_container" style="display: none;">
            <label class="form-label">Versement Date</label>
            <input type="date" class="form-control" name="date_versement" id="date_versement">
          </div>

          <button type="submit" class="btn btn-primary">Add Charge</button>
        </form>
        <div id="addMessage" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Charge Modal -->
<div class="modal fade" id="editChargeModal" tabindex="-1" aria-labelledby="editChargeLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editChargeForm" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editChargeLabel">Edit Charge</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="updateCharge">
          <input type="hidden" name="id" id="editChargeId">

          <!-- Payment Method -->
          <div class="mb-3">
            <label class="form-label">Payment Method</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Espèce" id="epm_espece">
              <label class="form-check-label" for="epm_espece">Espèce</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Chèque bancaire" id="epm_cb">
              <label class="form-check-label" for="epm_cb">Chèque bancaire</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Chèque postale" id="epm_cp">
              <label class="form-check-label" for="epm_cp">Chèque postale</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Versement bancaire" id="epm_vb">
              <label class="form-check-label" for="epm_vb">Versement bancaire</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Versement postale" id="epm_vp">
              <label class="form-check-label" for="epm_vp">Versement postale</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="payment_method[]" value="Carte bancaire" id="epm_cbk">
              <label class="form-check-label" for="epm_cbk">Carte bancaire</label>
            </div>
          </div>

          <!-- Charge Type (Text Input, no options) -->
          <div class="mb-3">
            <label for="charge_type_edit" class="form-label">Charge Type</label>
            <input type="text" class="form-control" name="charge_type" id="charge_type_edit" placeholder="Enter charge type" required>
          </div>

          <!-- Reception Date -->
          <div class="mb-3">
            <label class="form-label">Reception Date</label>
            <input type="date" class="form-control" name="reception_date" id="reception_date_edit" required>
          </div>

          <!-- Amount -->
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" class="form-control" name="amount" id="edit_amount" step="0.001" required>
          </div>

          <!-- Charge Date -->
          <div class="mb-3">
            <label class="form-label">Charge Date</label>
            <input type="date" class="form-control" name="charge_date" id="edit_charge_date" required>
          </div>

          <!-- Conditional Payment Date -->
          <div class="mb-3" id="edit_date_versement_container" style="display: none;">
            <label class="form-label">Versement Date</label>
            <input type="date" class="form-control" name="date_versement" id="edit_date_versement">
          </div>

          <div id="editMessage" class="mt-3"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 270px;">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>
        <!-- /Edit Charge Modal -->

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="deleteChargeForm" method="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteConfirmLabel">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="deleteCharge">
                            <input type="hidden" name="id" id="deleteChargeId">
                            <p>Are you sure you want to delete this charge?</p>
                            <div id="deleteMessage" class="mt-3"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="margin-right: 270px;">No, cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /Delete Confirmation Modal -->
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
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
<script>
  // Ensure only one payment method checkbox can be selected at a time (both add and edit forms)
  function setupSingleCheckboxSelection(formSelector) {
    const checkboxes = document.querySelectorAll(`${formSelector} input[name="payment_method[]"]`);
    checkboxes.forEach(box => {
      box.addEventListener('change', function() {
        if (this.checked) {
          checkboxes.forEach(cb => {
            if (cb !== this) cb.checked = false;
          });
        }
      });
    });
  }
  setupSingleCheckboxSelection('#addChargeForm');
  setupSingleCheckboxSelection('#editChargeForm');

  document.addEventListener('DOMContentLoaded', function () {
    // Initialize modals
    const addModalEl = document.getElementById('add_charge');
    const editModalEl = document.getElementById('editChargeModal');
    const deleteModalEl = document.getElementById('deleteConfirmModal');

    const addModal = new bootstrap.Modal(addModalEl);
    const editModal = new bootstrap.Modal(editModalEl);
    const deleteModal = new bootstrap.Modal(deleteModalEl);

    // Toggle Payment Date field visibility
    function togglePaymentDateField(selectedValues, containerId, dateInputId) {
      const container = document.getElementById(containerId);
      const dateInput = document.getElementById(dateInputId);
      if (selectedValues.includes('Chèque bancaire') || selectedValues.includes('Chèque postale')) {
        container.style.display = 'block';
        dateInput.setAttribute('required', 'required');
      } else {
        container.style.display = 'none';
        dateInput.removeAttribute('required');
        dateInput.value = '';
      }
    }

    // Add immediate toggle on checkbox changes for Add form
    document.querySelectorAll('#addChargeForm input[name="payment_method[]"]').forEach(cb => {
      cb.addEventListener('change', () => {
        const selected = Array.from(document.querySelectorAll('#addChargeForm input[name="payment_method[]"]:checked')).map(el => el.value);
        togglePaymentDateField(selected, 'date_versement_container', 'date_versement');
      });
    });

    // Add immediate toggle on checkbox changes for Edit form
    document.querySelectorAll('#editChargeForm input[name="payment_method[]"]').forEach(cb => {
      cb.addEventListener('change', () => {
        const selected = Array.from(document.querySelectorAll('#editChargeForm input[name="payment_method[]"]:checked')).map(el => el.value);
        togglePaymentDateField(selected, 'edit_date_versement_container', 'edit_date_versement');
      });
    });

    // ➕ Handle Add Charge form submit
    document.getElementById('addChargeForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const form = this;
      const formData = new FormData(form);
      const addMessage = document.getElementById('addMessage');

      // Collect single payment method value
      const paymentMethods = Array.from(form.querySelectorAll('input[name="payment_method[]"]:checked')).map(cb => cb.value);

      // Remove existing payment_method[] fields and re-append for consistent data
      formData.delete('payment_method[]');
      paymentMethods.forEach(val => formData.append('payment_method[]', val));

      // Handle conditional payment date
      togglePaymentDateField(paymentMethods, 'date_versement_container', 'date_versement');
      if (!paymentMethods.includes('Chèque bancaire') && !paymentMethods.includes('Chèque postale')) {
        formData.set('date_versement', '0000-00-00');
      }

      fetch('../../controller/chargeController.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        addMessage.innerHTML = data;
        if (data.includes('succès')) {
          setTimeout(() => {
            addModal.hide();
            location.reload();
          }, 1500);
        }
      })
      .catch(error => {
        addMessage.innerHTML = `<p style="color:red;">Erreur lors de l'ajout: ${error.message}</p>`;
      });
    });

    // ✏️ Handle Edit Button Click to populate modal
    document.body.addEventListener('click', function (e) {
      if (e.target.classList.contains('btn-edit-charge')) {
        const btn = e.target;
        const id = btn.getAttribute('data-id');
        const chargeType = btn.getAttribute('data-charge_type') || '';
        const amount = parseFloat(btn.getAttribute('data-amount')).toFixed(3);
        const chargeDate = btn.getAttribute('data-charge_date');
        const dateVersement = btn.getAttribute('data-date_versement');
        const paymentMethod = btn.getAttribute('data-payment_method') ? btn.getAttribute('data-payment_method').split(',') : [];

        if (id) {
          document.getElementById('editChargeId').value = id;
          document.getElementById('charge_type_edit').value = chargeType; // charge_type input in edit modal
          document.getElementById('edit_amount').value = amount;
          document.getElementById('edit_charge_date').value = chargeDate;
          document.getElementById('edit_date_versement').value = (dateVersement === '0000-00-00') ? '' : dateVersement;

          // Reset all checkboxes in edit form first
          document.querySelectorAll('#editChargeForm input[name="payment_method[]"]').forEach(input => {
            input.checked = false;
          });
          // Check checkboxes based on data
          paymentMethod.forEach(pm => {
            const checkbox = document.querySelector(`#editChargeForm input[name="payment_method[]"][value="${pm}"]`);
            if (checkbox) checkbox.checked = true;
          });

          togglePaymentDateField(paymentMethod, 'edit_date_versement_container', 'edit_date_versement');
          editModal.show();
        }
      }
    });

    // ✏️ Handle Edit Form Submit
    document.getElementById('editChargeForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const form = this;
      const formData = new FormData(form);
      const editMessage = document.getElementById('editMessage');

      // Collect selected payment method (only one)
      const paymentMethods = Array.from(form.querySelectorAll('input[name="payment_method[]"]:checked')).map(cb => cb.value);

      // Clean and reappend payment_method[] fields
      formData.delete('payment_method[]');
      paymentMethods.forEach(val => formData.append('payment_method[]', val));

      if (!paymentMethods.includes('Chèque bancaire') && !paymentMethods.includes('Chèque postale')) {
        formData.set('date_versement', '0000-00-00');
      }

      fetch('../../controller/chargeController.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        editMessage.innerHTML = data.message;
        editMessage.style.color = data.status === 'success' ? 'green' : 'red';
        if (data.status === 'success') {
          setTimeout(() => {
            editModal.hide();
            location.reload();
          }, 1500);
        }
      })
      .catch(error => {
        editMessage.innerHTML = `<p style="color:red;">Erreur lors de la mise à jour: ${error.message}</p>`;
      });
    });

    // 🗑️ Handle Delete Button Click
    document.body.addEventListener('click', function (e) {
      if (e.target.classList.contains('btn-delete-charge')) {
        const id = e.target.getAttribute('data-id');
        if (id) {
          document.getElementById('deleteChargeId').value = id;
          deleteModal.show();
        }
      }
    });

    // 🗑️ Handle Delete Form Submit
    document.getElementById('deleteChargeForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const deleteMessage = document.getElementById('deleteMessage');

      fetch('../../controller/chargeController.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        deleteMessage.innerHTML = data.message;
        deleteMessage.style.color = data.status === 'success' ? 'green' : 'red';
        if (data.status === 'success') {
          setTimeout(() => {
            deleteModal.hide();
            location.reload();
          }, 1500);
        }
      })
      .catch(error => {
        deleteMessage.innerHTML = `<p style="color:red;">Erreur lors de la suppression: ${error.message}</p>`;
      });
    });

  });
</script>



<link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">

</body>
</html>