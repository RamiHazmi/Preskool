
<?php
require_once __DIR__ . '/../../controller/session_config.php';  
if (!isset($_SESSION['admin'])) {
  // Not logged in, redirect to login
  header('Location: login.php');
  exit;
}
$admin = $_SESSION['admin'];





include_once __DIR__ . '/../../controller/societeController.php';
$societe = [
  'nom_centre' => $_SESSION['societe_nom'] ?? 'Not set',
  'matricule_fiscale' => $_SESSION['societe_matricule'] ?? 'Not set',
  'numero_telephone' => $_SESSION['societe_telephone'] ?? 'Not set',
  'adresse' => $_SESSION['societe_adresse'] ?? 'Not set',
  'logo' => $_SESSION['societe_logo'] ?? ''
];

include_once __DIR__ . '/../../controller/PaimentController.php';
include_once __DIR__ . '/../../controller/StudentController.php';


$controller = new ControllerStudent();
$students = $controller->listStudents();
$controller = new ControllerPaiment();
$paiements = $controller->listPaiment();
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
										<li><a href="addemploi.php"><i class="ti ti-calendar-event"></i>Schedule</a></li>
									</ul>
								</li>
								<li><a href="addclassroom.php"><i class="ti ti-building"></i><span>Class Room</span></a></li>
							 <li><a href="addmatier.php"><i class="ti ti-book"></i><span>Subject</span></a></li>
                             <h6 class="submenu-hdr"><span>MODULE COMPTABILITÉ</span></h6>
							<ul>
              <li class="active"><a href="addmatier.php"><i class="ti ti-book"></i><span>Paiement</span></a></li>
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
        <h3 class="page-title mb-1">Paiement List</h3>
        <nav>
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Paiement</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Paiement</li>
          </ol>
        </nav>
      </div>

    </div>
    <!-- /Page Header -->

    <!-- Paiement List -->
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
        <h4 class="mb-3">Liste des Paiements par Étudiant</h4>
      </div>

      <div class="card-body p-0 py-3">
        <div class="custom-datatable-filter table-responsive">
          <table class="table datatable">
            <thead class="thead-light">
              <tr>
                <th>Nom</th>
                <th>Date d'inscription</th>
                <th>Méthode de paiement</th>
                <th>Dernier paiement</th>
                <th>Nombre de paiements</th>
                <th>État</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($paiements as $student): ?>
                <tr>
                  <td>
                    <?= htmlspecialchars($student['username']) ?><br>
                    <small class="text-muted"><?= htmlspecialchars($student['name']) ?></small>
                  </td>
                  <td><?= date('d M Y', strtotime($student['joined_at'])) ?></td>
                  <td><?= htmlspecialchars($student['methode_paiement']) ?></td>
                  <td><?= $student['last_payment_date'] ? date('d M Y', strtotime($student['last_payment_date'])) : '—' ?></td>
                  <td><?= $student['total_paiements'] ?></td>
                  <td>
                    <?php
                      $etat = htmlspecialchars($student['etat']);
                      $badge = $etat === 'Payé' ? 'success' : 'danger';
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= $etat ?></span>
                  </td>
                  <td class="text-center">
                    <div class="dropdown">
                      
                      <ul class="">
                        <li>
                        <a class="dropdown-item view-paiement-history" href="#"
                        data-student-id="<?= $student['student_id'] ?>"
                        data-student-name="<?= htmlspecialchars($student['username'] . ' ' . $student['name']) ?>">
                        <i class="fas fa-eye me-2"></i>Voir détails
                        </a>


                        </li>
                      </ul>
                    </div>
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
<!-- Modal to show payment history -->
<div class="modal fade" id="paiementHistoryModal" tabindex="-1" aria-labelledby="paiementHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paiementHistoryLabel">Historique des paiements</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <h5 id="studentNameHeader" class="mb-4 fw-bold"></h5>
        <!-- Container for the payment forms -->
        <div id="paiementHistoryContainer" class="container-fluid"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
document.addEventListener('click', function(e) {
  if (e.target.closest('.view-paiement-history')) {
    e.preventDefault();
    const btn = e.target.closest('.view-paiement-history');
    const studentId = btn.getAttribute('data-student-id');
    const studentName = btn.getAttribute('data-student-name') || 'Étudiant';

    fetch('/stage/controller/paimentController.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ action: 'getPaiementsByStudent', id_student: studentId })
    })
    .then(res => res.json())
    .then(paiements => {
      if (!Array.isArray(paiements)) {
        alert('Erreur: impossible de charger les paiements.');
        return;
      }

      const container = document.getElementById('paiementHistoryContainer');
      container.innerHTML = '';
      document.getElementById('studentNameHeader').textContent = `Paiements de ${studentName}`;

      if (paiements.length === 0) {
        const p = document.createElement('p');
        p.textContent = 'Aucun paiement trouvé pour cet étudiant.';
        container.appendChild(p);
      } else {
        paiements.forEach(p => {
          const form = document.createElement('form');
          form.classList.add('border', 'p-3', 'mb-3', 'rounded');

          // -------- FIRST ROW: Montant + Méthode (checkboxes) ----------
          const row1 = document.createElement('div');
          row1.classList.add('row', 'g-3', 'align-items-center');

          // Montant input group (readonly)
          const montantDiv = document.createElement('div');
          montantDiv.classList.add('col-md-2');
          const montantLabel = document.createElement('label');
          montantLabel.classList.add('form-label', 'fw-bold');
          montantLabel.textContent = 'Montant';
          const montantInput = document.createElement('input');
          montantInput.type = 'text';
          montantInput.name = 'montant';
          montantInput.readOnly = true;
          montantInput.classList.add('form-control-plaintext');
          montantInput.value = p.montant || '';
          montantDiv.appendChild(montantLabel);
          montantDiv.appendChild(montantInput);
          row1.appendChild(montantDiv);

          // Méthode checkboxes group (readonly view = text, edit mode will replace with checkboxes)
          const methodeDiv = document.createElement('div');
          methodeDiv.classList.add('col-md-10');
          const methodeLabel = document.createElement('label');
          methodeLabel.classList.add('form-label', 'fw-bold', 'd-block');
          methodeLabel.textContent = 'Méthode';
          const methodeText = document.createElement('div');
          methodeText.textContent = p.methode || '';
          methodeText.classList.add('form-control-plaintext');
          methodeDiv.appendChild(methodeLabel);
          methodeDiv.appendChild(methodeText);
          row1.appendChild(methodeDiv);

          form.appendChild(row1);

          // -------- SECOND ROW: Date Paiement + Date Versement (conditionally) ----------
          const row2 = document.createElement('div');
          row2.classList.add('row', 'g-3', 'align-items-center', 'mt-2');

          // Date Paiement input (readonly)
          const datePaiementDiv = document.createElement('div');
          datePaiementDiv.classList.add('col-md-3');
          const datePaiementLabel = document.createElement('label');
          datePaiementLabel.classList.add('form-label', 'fw-bold');
          datePaiementLabel.textContent = 'Date Paiement';
          const datePaiementInput = document.createElement('input');
          datePaiementInput.type = 'text';
          datePaiementInput.name = 'date_paiement';
          datePaiementInput.readOnly = true;
          datePaiementInput.classList.add('form-control-plaintext');
          datePaiementInput.value = p.date_paiement ? p.date_paiement.replace(/-/g, '/') : '';
          datePaiementDiv.appendChild(datePaiementLabel);
          datePaiementDiv.appendChild(datePaiementInput);
          row2.appendChild(datePaiementDiv);

          // Date Versement input (readonly), only if méthode === 'Chèque bancaire'
          if (p.methode === 'Chèque bancaire') {
            const dateVersementDiv = document.createElement('div');
            dateVersementDiv.classList.add('col-md-3');
            dateVersementDiv.dataset.versementGroup = true;

            const dateVersementLabel = document.createElement('label');
            dateVersementLabel.classList.add('form-label', 'fw-bold');
            dateVersementLabel.textContent = 'Date Versement';

            const dateVersementInput = document.createElement('input');
            dateVersementInput.type = 'text';
            dateVersementInput.name = 'date_versement';
            dateVersementInput.readOnly = true;
            dateVersementInput.classList.add('form-control-plaintext');
            dateVersementInput.value = p.date_versement ? p.date_versement.replace(/-/g, '/') : '';

            dateVersementDiv.appendChild(dateVersementLabel);
            dateVersementDiv.appendChild(dateVersementInput);
            row2.appendChild(dateVersementDiv);
          }

          form.appendChild(row2);

          // ---------- Buttons group ----------
          const btnGroup = document.createElement('div');
          btnGroup.classList.add('mt-3', 'd-flex', 'gap-2');

          const editBtn = document.createElement('button');
          editBtn.type = 'button';
          editBtn.classList.add('btn', 'btn-primary');
          editBtn.textContent = 'Modifier';

          const deleteBtn = document.createElement('button');
          deleteBtn.type = 'button';
          deleteBtn.classList.add('btn', 'btn-danger');
          deleteBtn.textContent = 'Annuler Paiement';

          const receiptBtn = document.createElement('button');
          receiptBtn.type = 'button';
          receiptBtn.classList.add('btn', 'btn-success');
          receiptBtn.textContent = 'Reçu';
          receiptBtn.dataset.paiementId = p.id;

          receiptBtn.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
              orientation: "portrait",
              unit: "mm",
              format: [248, 425]
            });
            const societeLogoPath = <?= json_encode(htmlspecialchars('/stage/' . ($_SESSION['societe_logo'] ?? ''))) ?>;

            const logo = new Image();
            logo.src = societeLogoPath || 'assets/img/default-logo.png';
            logo.onload = function () {
              doc.addImage(logo, 'PNG', 15, 15, 70, 40);
              doc.setFontSize(28);
              doc.setFont("helvetica", "bold");
              doc.setTextColor(33, 37, 41);
              doc.text("Reçu du paiement", doc.internal.pageSize.getWidth() / 2, 65, { align: "center" });

              doc.setFont("helvetica", "normal");
              doc.setFontSize(18);
              doc.setTextColor(0, 0, 0);

              let y = 90;
              const year = p.date_paiement ? new Date(p.date_paiement).getFullYear() : '—';
              const montant = p.montant ? `${p.montant} TND` : '—';

              doc.text(`Nom complet : ${studentName}`, 25, y); y += 18;
              doc.text(`Classe : ${p.class_name || '—'}`, 25, y); y += 18;
              const yearRange = year && !isNaN(year) ? `${year - 1}-${year}` : '—';
              doc.text(`Année : ${yearRange}`, 25, y); y += 18;
              doc.text(`Montant payé : ${montant}`, 25, y); y += 18;

              doc.setFontSize(18);
              doc.text("Cachet :", 25, y);

              doc.setFontSize(14);
              doc.setTextColor(80);
              const pageHeight = doc.internal.pageSize.getHeight();
              const societeAdresse = <?= json_encode(htmlspecialchars($_SESSION['societe_adresse'] ?? '')) ?>;
  const societeTelephone = <?= json_encode(htmlspecialchars($_SESSION['societe_telephone'] ?? '')) ?>;
  doc.text("Adresse : " + societeAdresse, 25, pageHeight - 50);
doc.text("Tel : " + societeTelephone, 25, pageHeight - 40);


              const fileName = `Recu_${studentName.replace(/\s+/g, '_')}_${p.id}.pdf`;
              doc.save(fileName);
            };

            logo.onerror = () => {
              alert("Erreur lors du chargement du logo.");
            };
          });

          btnGroup.appendChild(editBtn);
          btnGroup.appendChild(deleteBtn);
          btnGroup.appendChild(receiptBtn);

          form.appendChild(btnGroup);

          // ------- Edit button logic -------
          editBtn.addEventListener('click', () => {
            // Remove existing date versement if any (readonly)
            const existingVersementGroup = form.querySelector('[data-versement-group]');
            if (existingVersementGroup) {
              existingVersementGroup.remove();
            }

            // First row: Montant stays readonly, Méthode text replaced by checkboxes
            montantInput.readOnly = true;
            montantInput.classList.replace('form-control-plaintext', 'form-control');

            // Replace méthode text with checkboxes inside the same div
            methodeDiv.innerHTML = ''; // clear

            const methodeLabelEdit = document.createElement('label');
            methodeLabelEdit.classList.add('form-label', 'fw-bold', 'd-block');
            methodeLabelEdit.textContent = 'Méthode';
            methodeDiv.appendChild(methodeLabelEdit);

            // Create container for checkboxes
            const checkboxContainer = document.createElement('div');
            checkboxContainer.classList.add('d-flex', 'flex-wrap', 'gap-3');

            const methods = [
              "Espèce",
              "Chèque bancaire",
              "Chèque postale",
              "Versement bancaire",
              "Versement postale",
              "Carte bancaire"
            ];

            methods.forEach(method => {
              const checkboxId = `method_${p.id}_${method.replace(/\s+/g, '')}`;

              const label = document.createElement('label');
              label.classList.add('form-check-label', 'me-3');

              const checkbox = document.createElement('input');
              checkbox.type = 'checkbox';
              checkbox.name = 'methode_checkbox';
              checkbox.value = method;
              checkbox.classList.add('form-check-input');
              checkbox.id = checkboxId;

              // Check if this method is the current one
              if (p.methode === method) checkbox.checked = true;

              // Only one checkbox can be checked — enforce single select behavior
              checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                  checkboxContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    if (cb !== checkbox) cb.checked = false;
                  });

                  // Show/hide Date Versement input on second row based on méthode selection
                  if (method === 'Chèque bancaire') {
                    showDateVersementInput();
                  } else {
                    removeDateVersementInput();
                  }
                }
              });

              label.appendChild(checkbox);
              label.appendChild(document.createTextNode(' ' + method));
              checkboxContainer.appendChild(label);
            });

            methodeDiv.appendChild(checkboxContainer);

            // Second row: Date Paiement becomes editable date input
            datePaiementInput.readOnly = false;
            datePaiementInput.classList.replace('form-control-plaintext', 'form-control');
            datePaiementInput.type = 'date';
            if (p.date_paiement) {
              datePaiementInput.value = p.date_paiement;
            }

            // Date Versement input: add if méthode = Chèque bancaire
            function showDateVersementInput() {
              if (!row2.querySelector('[data-versement-group]')) {
                const dateVersementDiv = document.createElement('div');
                dateVersementDiv.classList.add('col-md-3');
                dateVersementDiv.dataset.versementGroup = true;

                const dateVersementLabel = document.createElement('label');
                dateVersementLabel.classList.add('form-label', 'fw-bold');
                dateVersementLabel.textContent = 'Date Versement';

                const dateVersementInput = document.createElement('input');
                dateVersementInput.type = 'date';
                dateVersementInput.name = 'date_versement';
                dateVersementInput.classList.add('form-control');

                // Prefill if data exists
                dateVersementInput.value = p.date_versement || '';

                dateVersementDiv.appendChild(dateVersementLabel);
                dateVersementDiv.appendChild(dateVersementInput);

                row2.appendChild(dateVersementDiv);
              }
            }
            function removeDateVersementInput() {
              const versementGroup = row2.querySelector('[data-versement-group]');
              if (versementGroup) versementGroup.remove();
            }

            // Show/hide date versement on load depending on current méthode
            if (p.methode === 'Chèque bancaire') {
              showDateVersementInput();
            } else {
              removeDateVersementInput();
            }

            // Buttons: replace with Save + Cancel
            btnGroup.innerHTML = '';

            const saveBtn = document.createElement('button');
            saveBtn.type = 'submit';
            saveBtn.classList.add('btn', 'btn-success');
            saveBtn.textContent = 'Enregistrer';

            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.classList.add('btn', 'btn-secondary');
            cancelBtn.textContent = 'Annuler';

            btnGroup.appendChild(saveBtn);
            btnGroup.appendChild(cancelBtn);

            cancelBtn.addEventListener('click', () => {
              const modalEl = document.getElementById('paiementHistoryModal');
              const modalInstance = bootstrap.Modal.getInstance(modalEl);
              if (modalInstance) modalInstance.hide();
            });
          });

          // FORM SUBMIT HANDLER
          form.addEventListener('submit', (ev) => {
            ev.preventDefault();
            const formData = new FormData(form);
            formData.append('action', 'updatePaiment');
            formData.append('id', p.id);

            // Montant remains readonly, so formData includes it
            // Méthode from checked checkbox
            const checkedMethod = form.querySelector('input[name="methode_checkbox"]:checked');
            formData.set('methode', checkedMethod ? checkedMethod.value : '');

            // Date Paiement (format yyyy-mm-dd)
            // Already date input value or fallback text input
            let datePaiementVal = form.querySelector('input[name="date_paiement"]').value;
            formData.set('date_paiement', datePaiementVal);

            // Date Versement only if méthode = Chèque bancaire
            if (formData.get('methode') === 'Chèque bancaire') {
              let dateVersementVal = form.querySelector('input[name="date_versement"]')?.value || '0000-00-00';
              formData.set('date_versement', dateVersementVal);
            } else {
              formData.set('date_versement', '0000-00-00');
            }

            fetch('/stage/controller/paimentController.php', {
              method: 'POST',
              body: new URLSearchParams(formData)
            })
            .then(res => res.json())
            .then(data => {
              if (data.status === 'success') {
                alert('Paiement mis à jour avec succès.');
                window.location.reload();
              } else {
                alert('Erreur lors de la mise à jour: ' + (data.message || ''));
              }
            })
            .catch(() => alert('Erreur serveur lors de la mise à jour.'));
          });

          // DELETE BUTTON HANDLER
          deleteBtn.addEventListener('click', () => {
            form.innerHTML = '';
            const confirmMsg = document.createElement('p');
            confirmMsg.textContent = 'Voulez-vous vraiment annuler ce paiement ?';

            const confirmBtn = document.createElement('button');
            confirmBtn.type = 'button';
            confirmBtn.classList.add('btn', 'btn-danger', 'me-2');
            confirmBtn.textContent = 'Confirmer annulation';

            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.classList.add('btn', 'btn-secondary');
            cancelBtn.textContent = 'Annuler';

            form.appendChild(confirmMsg);
            form.appendChild(confirmBtn);
            form.appendChild(cancelBtn);

            cancelBtn.addEventListener('click', () => {
              const modalEl = document.getElementById('paiementHistoryModal');
              const modalInstance = bootstrap.Modal.getInstance(modalEl);
              if (modalInstance) modalInstance.hide();
            });

            confirmBtn.addEventListener('click', () => {
              fetch('/stage/controller/paimentController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'deletePaiment', id: p.id })
              })
              .then(res => res.json())
              .then(data => {
                if (data.status === 'success') {
                  alert('Paiement annulé avec succès.');
                  window.location.reload();
                } else {
                  alert('Erreur lors de l\'annulation: ' + (data.message || ''));
                }
              })
              .catch(() => alert('Erreur serveur lors de l\'annulation.'));
            });
          });

          container.appendChild(form);
        });
      }

      new bootstrap.Modal(document.getElementById('paiementHistoryModal')).show();
    })
    .catch(() => alert('Erreur serveur'));
  }
});
</script>



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