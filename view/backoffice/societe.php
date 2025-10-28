<?php
require_once __DIR__ . '/../../controller/session_config.php';  
if (!isset($_SESSION['admin'])) {
    // Not logged in, redirect to login
    header('Location: login.php');
    exit;
  }
  $admin = $_SESSION['admin'];
  
  


$societe = [
    'id' => $_SESSION['societe_id'] ?? 'Not set',
    'nom_centre' => $_SESSION['societe_nom'] ?? 'Not set',
    'matricule_fiscale' => $_SESSION['societe_matricule'] ?? 'Not set',
    'numero_telephone' => $_SESSION['societe_telephone'] ?? 'Not set',
    'adresse' => $_SESSION['societe_adresse'] ?? 'Not set',
    'logo' => $_SESSION['societe_logo'] ?? ''
];
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

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

	<!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

	<!-- Bootstrap Tagsinput CSS -->
	<link rel="stylesheet" href="assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css">

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
                <?php endif; ?>
            </a>
        </div>
        <!-- /Logo -->

        <!-- Mobile Menu Toggle -->
        <a id="mobile_btn" class="mobile_btn" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <!-- User Menu -->
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
                    <!-- Academic Year Dropdown -->
                    <div class="dropdown me-2">
                        <a href="#" class="btn btn-outline-light fw-normal bg-white d-flex align-items-center p-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-calendar-due me-1"></i>Academic Year: 2024 / 2025
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
                                Academic Year: 2023 / 2024
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
                                Academic Year: 2022 / 2023
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
                                Academic Year: 2021 / 2022
                            </a>
                        </div>
                    </div>

                    <!-- Google Translate -->
                    <div class="pe-1 ms-1">
                        <div id="google_translate_element"></div>
                    </div>

                    <!-- Add New Dropdown -->
                    <div class="pe-1">
                        <div class="dropdown">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-square-rounded-plus"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right border shadow-sm dropdown-md">
                                <div class="p-3 border-bottom">
                                    <h5>Add New</h5>
                                </div>
                                <div class="p-3 pb-0">
                                    <div class="row gx-2">
                                        <div class="col-6">
                                            <a href="add-student.html" class="d-block bg-primary-transparent rounded p-2 text-center mb-3 class-hover">
                                                <div class="avatar avatar-lg mb-2">
                                                    <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-primary rounded-circle">
                                                        <i class="ti ti-school"></i>
                                                    </span>
                                                </div>
                                                <p class="text-dark">Students</p>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="add-teacher.html" class="d-block bg-success-transparent rounded p-2 text-center mb-3 class-hover">
                                                <div class="avatar avatar-lg mb-2">
                                                    <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-success rounded-circle">
                                                        <i class="ti ti-users"></i>
                                                    </span>
                                                </div>
                                                <p class="text-dark">Teachers</p>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="add-staff.html" class="d-block bg-warning-transparent rounded p-2 text-center mb-3 class-hover">
                                                <div class="avatar avatar-lg rounded-circle mb-2">
                                                    <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-warning rounded-circle">
                                                        <i class="ti ti-users-group"></i>
                                                    </span>
                                                </div>
                                                <p class="text-dark">Staffs</p>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="add-invoice.html" class="d-block bg-info-transparent rounded p-2 text-center mb-3 class-hover">
                                                <div class="avatar avatar-lg mb-2">
                                                    <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-info rounded-circle">
                                                        <i class="ti ti-license"></i>
                                                    </span>
                                                </div>
                                                <p class="text-dark">Invoice</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dark/Light Mode Toggle -->
                    <div class="pe-1">
                        <a href="#" id="dark-mode-toggle" class="dark-mode-toggle activate btn btn-outline-light bg-white btn-icon me-1">
                            <i class="ti ti-moon"></i>
                        </a>
                        <a href="#" id="light-mode-toggle" class="dark-mode-toggle btn btn-outline-light bg-white btn-icon me-1">
                            <i class="ti ti-brightness-up"></i>
                        </a>
                    </div>

                    <!-- Notifications -->
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
                                        <a href="javascript:void(0);" class="bg-white dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="ti ti-calendar-due me-1"></i>Today
                                        </a>
                                        <ul class="dropdown-menu mt-2 p-3">
                                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1">This Week</a></li>
                                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Last Week</a></li>
                                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Last Week</a></li>
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
                                    <div class="border-bottom mb-3 pb-3">
                                        <a href="activities.html">
                                            <div class="d-flex">
                                                <span class="avatar avatar-lg me-2 flex-shrink-0">
                                                    <img src="assets/img/profiles/avatar-25.jpg" alt="Profile">
                                                </span>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">New student record <span class="text-dark fw-semibold">George</span> is created by <span class="text-dark fw-semibold">Teressa</span></p>
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
                                                    <p class="mb-1">A new teacher record for <span class="text-dark fw-semibold">Elisa</span></p>
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

                    <!-- Chat -->
                    <div class="pe-1">
                        <a href="chat.html" class="btn btn-outline-light bg-white btn-icon position-relative me-1">
                            <i class="ti ti-brand-hipchat"></i>
                            <span class="chat-status-dot"></span>
                        </a>
                    </div>

                    <!-- Statistics -->
                    <div class="pe-1">
                        <a href="#" class="btn btn-outline-light bg-white btn-icon me-1">
                            <i class="ti ti-chart-bar"></i>
                        </a>
                    </div>

                    <!-- Fullscreen -->
                    <div class="pe-1">
                        <a href="#" class="btn btn-outline-light bg-white btn-icon me-1" id="btnFullscreen">
                            <i class="ti ti-maximize"></i>
                        </a>
                    </div>

                    <!-- User Profile Dropdown -->
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
                                        <h6><?= htmlspecialchars($_SESSION['admin']['last_name'] ?? 'User'); ?></h6>
                                        <p class="text-primary mb-0">Administrator</p>
                                    </div>
                                </div>
                                <hr class="m-0">
                                <a class="dropdown-item d-inline-flex align-items-center p-2" href="profile.php">
                                    <i class="ti ti-user-circle me-2"></i>My Profile
                                </a>
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

        <!-- Mobile User Menu -->
        <div class="dropdown mobile-user-menu">
            <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="profile.php">My Profile</a>
                <a class="dropdown-item" href="profile-settings.html">Settings</a>
                <a class="dropdown-item" href="login.php?logout=true">Logout</a>
            </div>
        </div>
    </div>
    <!-- /Header -->

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <!-- Main Menu -->
                    <li>
                        <h6 class="submenu-hdr"><span>Main</span></h6>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-layout-dashboard"></i><span>Dashboard</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="dashboard.php">Student Dashboard</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- Peoples -->
                    <li>
                        <h6 class="submenu-hdr"><span>Peoples</span></h6>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-user"></i><span>Students</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="liststudent.php">All Students</a></li>
                                    <li><a href="addstudent.php"><i class="ti ti-user-plus"></i>Add Student</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-user"></i><span>Teachers</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="addenseignant.php">All Teachers</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- Academic -->
                    <li>
                        <h6 class="submenu-hdr"><span>Academic</span></h6>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-list-numbers"></i><span>Levels</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="addlevel.php">All Levels</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-chalkboard"></i><span>Classes</span><span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="addclass.php">All Classes</a></li>
                                    <li><a href="addemploi.php"><i class="ti ti-calendar-event"></i>Schedule</a></li>
                                </ul>
                            </li>
                            <li><a href="addclassroom.php"><i class="ti ti-building"></i><span>Class Room</span></a></li>
                            <li><a href="addmatier.php"><i class="ti ti-book"></i><span>Subject</span></a></li>
                        </ul>
                    </li>

                    <!-- Module Comptabilité -->
                    <li>
                        <h6 class="submenu-hdr"><span>MODULE COMPTABILITÉ</span></h6>
                        <ul>
                            <li><a href="listpaiement.php"><i class="ti ti-book"></i><span>Paiement</span></a></li>
                            <li><a href="addcharge.php"><i class="ti ti-book"></i><span>Charge</span></a></li>
                        </ul>
                    </li>

                    <!-- Settings -->
                    <li>
                        <h6 class="submenu-hdr"><span>Settings</span></h6>
                        <ul>
                            <li class="active">
                                <a href="addsociete.php"><i class="ti ti-building"></i><span>Société</span></a>
                            </li>
                        </ul>
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
                    <h3 class="mb-1">Edit Your Company</h3>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="students.html">Société</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Company</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Page Header -->
    <div class="row">
            <div class="col-md-12">
    <form class="form-horizontal" id="societeForm" enctype="multipart/form-data">
      <input type="hidden" name="action" value="updateSociete" />
      <input type="hidden" name="id_societe" value="<?= htmlspecialchars($societe['id']) ?>" />

      <div class="card">
        <div class="card-header bg-light">
          <div class="d-flex align-items-center">
            <span class="bg-white avatar avatar-sm me-2 text-gray-7 flex-shrink-0">
              <i class="ti ti-building fs-16"></i>
            </span>
            <h4 class="text-dark">Edit Company Information</h4>
          </div>
        </div>

        <div class="card-body pb-1">
          <!-- Logo Upload -->
          <div class="row mb-3">
            <div class="col-md-12 d-flex align-items-center flex-wrap row-gap-3">
            <div id="societeImageContainer" class="d-flex align-items-center justify-content-center avatar avatar-xxl border border-dashed me-2 flex-shrink-0 text-dark frames" style="position: relative;">
  <img src="<?= htmlspecialchars('/stage/' . ($_SESSION['societe_logo'] ?? 'view/images/default-logo.png')) ?>"
       alt="Company Logo"
       id="societePreviewImage"
       style="max-height: 50px; object-fit: contain;"
       onerror="this.src='/stage/view/images/default-logo.png';" />
</div>


              <div class="profile-upload">
                <div class="profile-uploader d-flex align-items-center">
                  <div class="drag-upload-btn mb-3">
                    Upload Logo
                    <input type="file" id="societeImageInput" class="form-control image-sign" name="image" accept=".jpg,.png,.svg" />
                  </div>
                </div>
                <p class="fs-12">Upload image size 4MB, Format JPG, PNG, SVG</p>
              </div>
            </div>
          </div>

          <!-- Form Fields -->
          <div class="row row-cols-md-3">
            <div class="col">
              <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input type="text" class="form-control" name="nom_centre" value="<?= htmlspecialchars($societe['nom_centre']) ?>" required />
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label class="form-label">Matricule Fiscale</label>
                <input type="text" class="form-control" name="matricule_fiscale" value="<?= htmlspecialchars($societe['matricule_fiscale']) ?>" required />
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" class="form-control" name="numero_telephone" value="<?= htmlspecialchars($societe['numero_telephone']) ?>" required />
              </div>
            </div>
            <div class="col-md-12">
              <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="adresse" rows="2" required><?= htmlspecialchars($societe['adresse']) ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="text-end mt-3">
        <button type="reset" class="btn btn-light me-3">Cancel</button>
        <button type="submit" class="btn btn-success me-2">Save Changes</button>
        <button type="button" id="deleteSocieteBtn" class="btn btn-danger">Delete Company</button>
      </div>
    </form>

    <div id="societeMessage" style="display:none; margin-top: 1rem;"></div>
  </div>
</div>


  </div>
</div>
<script>
  const logoSrc = <?= json_encode('/stage/' . ($_SESSION['societe_logo'] ?? 'default-logo.png')) ?>;
  console.log('Company Logo src:', logoSrc);
</script>
<script>
  console.log('societe_id:', <?= json_encode($_SESSION['societe_id'] ?? '') ?>);
  console.log('societe_nom:', <?= json_encode($_SESSION['societe_nom'] ?? '') ?>);
  // etc.
</script>

<script>
  // Preview uploaded logo
  document.getElementById('societeImageInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (event) {
      const container = document.getElementById('societeImageContainer');
      container.innerHTML = `<img src="${event.target.result}" id="societePreviewImage" style="max-height: 50px; object-fit: contain;" />`;
    };
    reader.readAsDataURL(file);
  });

 

  // UPDATE COMPANY (via fetch)
  document.getElementById('societeForm').addEventListener('submit', function (e) {
  e.preventDefault(); // Stop default form submission

  const form = e.target;
  const formData = new FormData(form); // Includes all form inputs

  // DEBUG: Log all form data being sent
  for (let [key, value] of formData.entries()) {
    console.log(`${key}: ${value}`);
  }

  fetch('../../controller/societeController.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(response => {
    const msg = document.getElementById('societeMessage');
    msg.innerHTML = response;
    msg.style.display = 'block';
    setTimeout(() => {
      location.reload();
    }, 1500);
  })
  .catch(err => {
    console.error(err);
    document.getElementById('societeMessage').innerHTML = '<p style="color:red;">Erreur de mise à jour.</p>';
  });
});


  // DELETE COMPANY (via fetch)
  document.getElementById('deleteSocieteBtn').addEventListener('click', function () {
    if (!confirm('Are you sure you want to delete this company?')) return;

    const societeId = document.querySelector('input[name="id_societe"]').value;

    fetch('../../controller/societeController.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'deleteSociete', id: societeId })
    })
    .then(res => res.json())
    .then(data => {
      const messageDiv = document.getElementById('societeMessage');
      messageDiv.innerHTML = `<p style="color:${data.status === 'success' ? 'green' : 'red'};">${data.message}</p>`;
      messageDiv.style.display = 'block';

      if (data.status === 'success') {
        setTimeout(() => {
      location.reload();
    }, 1500);
      }
    })
    .catch(() => {
      const messageDiv = document.getElementById('societeMessage');
      messageDiv.innerHTML = '<p style="color:red;">Erreur lors de la suppression.</p>';
      messageDiv.style.display = 'block';
    });
  });
</script>

        <!-- /Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->
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
    <!-- Select2 JS -->
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <!-- Datetimepicker JS -->
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <!-- Bootstrap Tagsinput JS -->
    <script src="assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">
<style>
    #pictureContainer .ti {
  font-size: 32px;
  color: #ccc;
  background-color: transparent !important;
  display: flex;
  align-items: center;
  justify-content: center;
}
#google_translate_element { display: block !important; 
 left: -9999px;}

    </style>

<!-- Language links -->
<script>
  let isGoogleTranslateReady = false;

  function waitForComboBox(callback, timeout = 5000) {
    const start = Date.now();
    const observer = new MutationObserver(() => {
      const combo = document.querySelector('.goog-te-combo');
      if (combo) {
        observer.disconnect();
        callback(combo);
      } else if (Date.now() - start > timeout) {
        observer.disconnect();
        console.error('❌ Timeout waiting for Google Translate combo box');
      }
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }

  function googleTranslateElementInit() {
    new google.translate.TranslateElement({
      pageLanguage: 'en',
      includedLanguages: 'en,fr,es,de,ar',
      layout: google.translate.TranslateElement.InlineLayout.SIMPLE
    }, 'google_translate_element');

    waitForComboBox(() => {
      isGoogleTranslateReady = true;
      console.log('✅ Google Translate combo box is ready.');
    });
  }

  function onLangClick(langCode, el) {
    translateTo(langCode);
  }

  function translateTo(lang) {
    if (!isGoogleTranslateReady) {
      console.warn("⏳ Google Translate not ready. Waiting...");
      setTimeout(() => translateTo(lang), 300);
      return;
    }

    const combo = document.querySelector('.goog-te-combo');
    if (combo) {
      combo.value = lang;
      combo.dispatchEvent(new Event('change'));
      console.log('🌐 Translating to:', lang);
    } else {
      console.error('❌ Translate dropdown not found even though ready flag is set.');
    }
  }
</script>

<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<!-- Load Google Translate API -->
<!-- Load Google Translate API -->


</body>
</html>