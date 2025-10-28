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
$gcMaxLifetime = ini_get('session.gc_maxlifetime');
$cookieLifetime = ini_get('session.cookie_lifetime');

// Optional: print or log them (for debugging)
error_log("session.gc_maxlifetime: $gcMaxLifetime seconds");
error_log("session.cookie_lifetime: $cookieLifetime seconds");

// Or just echo them inside HTML for test
echo "<!-- gc_maxlifetime = $gcMaxLifetime -->";
echo "<!-- cookie_lifetime = $cookieLifetime -->";
// Include controllers
include_once __DIR__ . '/../../controller/ClassController.php';
include_once __DIR__ . '/../../controller/EmploiController.php';
include_once __DIR__ . '/../../controller/EnseignantController.php';
include_once __DIR__ . '/../../controller/MatiereController.php'; 
include_once __DIR__ . '/../../controller/ClassroomController.php';
include_once __DIR__ . '/../../controller/PresenceController.php';
include_once __DIR__ . '/../../controller/studentController.php';
include_once __DIR__ . '/../../controller/levelController.php';
include_once __DIR__ . '/../../controller/PaimentController.php';
include_once __DIR__ . '/../../controller/chargeController.php';

$classroomcontroller = new ControllerClassroom();


$controller = new ControllerCharge();
$charges = $controller->listCharges();
// Aggregate total amount per charge type
$chargeTypeData = [];

// Assume each charge has: 'charge_type', 'amount', 'charge_date' (Y-m-d)
foreach ($charges as $charge) {
    $month = substr($charge['charge_date'], 0, 7); // Format: YYYY-MM
    $type = $charge['charge_type'] ?? 'Unknown';
    $amount = floatval($charge['amount']);

    if (!isset($chargeTypeData[$month])) {
        $chargeTypeData[$month] = [];
    }
    if (!isset($chargeTypeData[$month][$type])) {
        $chargeTypeData[$month][$type] = 0;
    }

    $chargeTypeData[$month][$type] += $amount;
}

ksort($chargeTypeData); // Optional

// Sum amount per day for charges chart
$dailyChargesMap = [];
foreach ($charges as $charge) {
    $date = date('Y-m', strtotime($charge['charge_date']));
    if (!isset($dailyChargesMap[$date])) {
        $dailyChargesMap[$date] = 0;
    }
    $dailyChargesMap[$date] += floatval($charge['amount']);
}
ksort($dailyChargesMap);

$chargeDates = array_keys($dailyChargesMap);
$dailyCharges = array_values($dailyChargesMap);

// Instantiate controllers
$classController = new ControllerClass(); 
$emploiController = new EmploiController(); 
$studentController = new ControllerStudent();
$enseignantController = new ControllerEnseignant();
$matiereController = new ControllerMatiere();
$presenceController = new PresenceController();
$levelController = new ControllerLevel();
$paimentController = new ControllerPaiment();


$paiements = $paimentController->listAllPayments();

// Sum montant per day for earnings chart
$dailyEarningsMap = [];
foreach ($paiements as $payment) {
    $date = date('Y-m', strtotime($payment['date_paiement']));
    if (!isset($dailyEarningsMap[$date])) {
        $dailyEarningsMap[$date] = 0;
    }
    $dailyEarningsMap[$date] += floatval($payment['montant']);
}
ksort($dailyEarningsMap);

$paymentDates = array_keys($dailyEarningsMap);
$dailyEarnings = array_values($dailyEarningsMap);

// Count payments per day for counts chart (optional)
$dailyPayments = [];
foreach ($paiements as $payment) {
    $date = date('Y-m-d', strtotime($payment['date_paiement']));
    if (!isset($dailyPayments[$date])) {
        $dailyPayments[$date] = 0;
    }
    $dailyPayments[$date]++;
}
ksort($dailyPayments);

$dates = array_keys($dailyPayments);
$counts = array_values($dailyPayments);

// Fetch class & level data
$classesList = $classController->listClasses(); 
$levels = $levelController->listLevels(); 

// Count how many classes per level
$classCountsByLevel = [];
foreach ($levels as $level) {
    $levelId = $level['id'];
    $levelName = $level['name'];
    $classCountsByLevel[$levelName] = 0;

    foreach ($classesList as $class) {
        if ($class['level_id'] == $levelId) {
            $classCountsByLevel[$levelName]++;
        }
    }
}
ksort($classCountsByLevel);

$classes = [];

foreach ($classesList as $class) {
    $classId = $class['id'];
    $className = $class['class_name'];

    // Call the controller function to get data
    $subjects = $presenceController->getPresenceCountBySubjectInClass($classId);

    // Calculate max and completion %
    $maxPresence = 0;
    foreach ($subjects as $s) {
        if ($s['presence_count'] > $maxPresence) {
            $maxPresence = $s['presence_count'];
        }
    }

    foreach ($subjects as &$subject) {
      $subject['completion'] = $subject['presence_count'] . '/' . $maxPresence;
      $subject['percent'] = $maxPresence ? round(($subject['presence_count'] / $maxPresence) * 100) : 0;
          }
    unset($subject);

    $classes[$className] = $subjects;
}

ksort($classes);


// Student-related calculations
$students = $studentController->listStudents();
$totalStudents = count($students); 
$joinCounts = [];
foreach ($students as $student) {
    $date = new DateTime($student['joined_at']);
    $dayLabel = $date->format('Y-m-d'); // per exact day
    if (!isset($joinCounts[$dayLabel])) {
        $joinCounts[$dayLabel] = 0;
    }
    $joinCounts[$dayLabel]++;
}
ksort($joinCounts);
$joinLabels = array_keys($joinCounts);
$joinData = array_values($joinCounts);
usort($students, fn($a, $b) => $b['moyenne'] <=> $a['moyenne']);
$topStudents = array_slice($students, 0, 3);
$activeStudents = array_filter($students, fn($s) => strtolower($s['status']) === 'active'); 
$graduatedStudents = array_filter($students, fn($s) => strtolower($s['status']) === 'graduated');
$suspendedStudents = array_filter($students, fn($s) => strtolower($s['status']) === 'suspended');

$activePercentage = round((count($activeStudents) / max($totalStudents, 1)) * 100, 1); 
$badgeClass = 'bg-danger'; 
if ($activePercentage >= 75) {
    $badgeClass = 'bg-success'; 
} elseif ($activePercentage >= 50) {
    $badgeClass = 'bg-warning'; 
} 

// General counts
$totalTeachers = count($enseignantController->listEnseignants()); 
$totalClasses = count($classController->listClasses()); 
$totalSubjects = count($matiereController->listMatieres());
$totalclassrooms = count($classroomcontroller->listClassrooms());
// Teacher-related calculations
$teachers = $enseignantController->listEnseignants();
$teacherJoinCounts = [];

foreach ($teachers as $teacher) {
    if (!empty($teacher['joined_at'])) {
        $date = new DateTime($teacher['joined_at']);
        $dayLabel = $date->format('Y-m-d');
        if (!isset($teacherJoinCounts[$dayLabel])) {
            $teacherJoinCounts[$dayLabel] = 0;
        }
        $teacherJoinCounts[$dayLabel]++;
    }
}
ksort($teacherJoinCounts);
$teacherJoinLabels = array_keys($teacherJoinCounts);
$teacherJoinData = array_values($teacherJoinCounts);

// Attendance stats for the current week
$presenceCount = 0;
$absenceCount = 0;

if (isset($_GET['from']) && isset($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];

    $presenceCount = $presenceController->countPresenceBetweenDates($from, $to);
    $absenceCount = $presenceController->countAbsenceBetweenDates($from, $to);
}

$statusCounts = [
    'Active' => count($activeStudents),
    'Graduated' => count($graduatedStudents),
    'Suspended' => count($suspendedStudents),
];
date_default_timezone_set('Africa/Tunis');
$today = date('Y-m-d');
?>
    <?php
$totalMontant = 0;
foreach ($paiements as $payment) {
    $totalMontant += floatval($payment['montant']);
}
$totalPayments = 0;
foreach ($paiements as $payment) {
    $totalPayments += floatval($payment['montant']);
}

$totalCharges = 0;
foreach ($charges as $charge) {
    $totalCharges += floatval($charge['amount']);
}

$totalEarnings = $totalPayments - $totalCharges;
$teachersPerSubject = [];

// Get all subjects
$allSubjects = $matiereController->listMatieres();
$subjectMap = []; // map id_matiere => nom
foreach ($allSubjects as $subject) {
    $subjectMap[$subject['id']] = $subject['nom'];
}

// Count teachers for each subject
foreach ($teachers as $teacher) {
    $id_matiere = $teacher['id_matiere'] ?? null;
    if ($id_matiere && isset($subjectMap[$id_matiere])) {
        $subjectName = $subjectMap[$id_matiere];
        if (!isset($teachersPerSubject[$subjectName])) {
            $teachersPerSubject[$subjectName] = 0;
        }
        $teachersPerSubject[$subjectName]++;
    }
}

ksort($teachersPerSubject); // Sort alphabetically
$classLevelMap = [];
foreach ($classesList as $class) {
    $classLevelMap[$class['id']] = null;
    foreach ($levels as $level) {
        if ($class['level_id'] == $level['id']) {
            $classLevelMap[$class['id']] = $level['name'];
            break;
        }
    }
}

// Count students per level
$studentCountsByLevel = [];
foreach ($levels as $level) {
    $studentCountsByLevel[$level['name']] = 0;
}

foreach ($students as $student) {
    $classId = $student['class_id'] ?? null;
    if ($classId && isset($classLevelMap[$classId])) {
        $levelName = $classLevelMap[$classId];
        if ($levelName !== null) {
            $studentCountsByLevel[$levelName]++;
        }
    }
}

ksort($studentCountsByLevel);
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

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">

	<!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

	<!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

	<!-- Owl Carousel CSS -->
	<link rel="stylesheet" href="assets/plugins/owlcarousel/owl.carousel.min.css">
	<link rel="stylesheet" href="assets/plugins/owlcarousel/owl.theme.default.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">


</head>

<body>

	<div id="global-loader">
		<div class="page-loader"></div>
	</div>

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
									</div>
								</div>
							</form>
						</div>
					</div>
					<!-- /Search -->

					<div class="d-flex align-items-center">
						<div class="dropdown me-2">
							<a href="#" class="btn btn-outline-light fw-normal bg-white d-flex align-items-center p-2"
								data-bs-toggle="dropdown" aria-expanded="false">
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
								<a href="#"
									class="btn btn-outline-light bg-white btn-icon d-flex align-items-center me-1 p-2"
									data-bs-toggle="dropdown" aria-expanded="false">
									<img src="assets/img/flags/us.png" alt="Language" class="img-fluid rounded-pill">
								</a>
								<div class="dropdown-menu dropdown-menu-right">
									<a href="javascript:void(0);"
										class="dropdown-item active d-flex align-items-center">
										<img class="me-2 rounded-pill" src="assets/img/flags/us.png" alt="Img"
											height="22" width="22"> English
									</a>
									<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
										<img class="me-2 rounded-pill" src="assets/img/flags/fr.png" alt="Img"
											height="22" width="22"> French
									</a>
									<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
										<img class="me-2 rounded-pill" src="assets/img/flags/es.png" alt="Img"
											height="22" width="22"> Spanish
									</a>
									<a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">
										<img class="me-2 rounded-pill" src="assets/img/flags/de.png" alt="Img"
											height="22" width="22"> German
									</a>
								</div>
							</div>
						</div>
						<div class="pe-1">
							<div class="dropdown">
								<a href="#" class="btn btn-outline-light bg-white btn-icon me-1"
									data-bs-toggle="dropdown" aria-expanded="false">
									<i class="ti ti-square-rounded-plus"></i>
								</a>
								<div class="dropdown-menu dropdown-menu-right border shadow-sm dropdown-md">
									<div class="p-3 border-bottom">
										<h5>Add New</h5>
									</div>
									<div class="p-3 pb-0">
										<div class="row gx-2">
											<div class="col-6">
												<a href="add-student.html"
													class="d-block bg-primary-transparent ronded p-2 text-center mb-3 class-hover">
													<div class="avatar avatar-lg mb-2">
														<span
															class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-primary rounded-circle"><i
																class="ti ti-school"></i></span>
													</div>
													<p class="text-dark">Students</p>
												</a>
											</div>
											<div class="col-6">
												<a href="add-teacher.html"
													class="d-block bg-success-transparent ronded p-2 text-center mb-3 class-hover">
													<div class="avatar avatar-lg mb-2">
														<span
															class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-success rounded-circle"><i
																class="ti ti-users"></i></span>
													</div>
													<p class="text-dark">Teachers</p>
												</a>
											</div>
											<div class="col-6">
												<a href="add-staff.html"
													class="d-block bg-warning-transparent ronded p-2 text-center mb-3 class-hover">
													<div class="avatar avatar-lg rounded-circle mb-2">
														<span
															class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-warning rounded-circle"><i
																class="ti ti-users-group"></i></span>
													</div>
													<p class="text-dark">Staffs</p>
												</a>
											</div>
											<div class="col-6">
												<a href="add-invoice.html"
													class="d-block bg-info-transparent ronded p-2 text-center mb-3 class-hover">
													<div class="avatar avatar-lg mb-2">
														<span
															class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-info rounded-circle"><i
																class="ti ti-license"></i></span>
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
							<a href="#" id="dark-mode-toggle"
								class="dark-mode-toggle activate btn btn-outline-light bg-white btn-icon me-1">
								<i class="ti ti-moon"></i>
							</a>
							<a href="#" id="light-mode-toggle"
								class="dark-mode-toggle btn btn-outline-light bg-white btn-icon me-1">
								<i class="ti ti-brightness-up"></i>
							</a>
						</div>
						<div class="pe-1" id="notification_item">
							<a href="#" class="btn btn-outline-light bg-white btn-icon position-relative me-1"
								id="notification_popup">
								<i class="ti ti-bell"></i>
								<span class="notification-status-dot"></span>
							</a>
							<div class="dropdown-menu dropdown-menu-end notification-dropdown p-4">
								<div
									class="d-flex align-items-center justify-content-between border-bottom p-0 pb-3 mb-3">
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
														<p class="mb-1"><span class="text-dark fw-semibold">Shawn</span>
															performance in Math is
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
														<p class="mb-1"><span
																class="text-dark fw-semibold">Sylvia</span> added
															appointment on
															02:00 PM</p>
														<span>10 mins ago</span>
														<div
															class="d-flex justify-content-start align-items-center mt-1">
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
														<p class="mb-1">New student record <span
																class="text-dark fw-semibold"> George</span> is
															created by <span class="text-dark fw-semibold">
																Teressa</span></p>
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
														<p class="mb-1">A new teacher record for <span
																class="text-dark fw-semibold">Elisa</span>
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
							<a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center"
								data-bs-toggle="dropdown">
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
        <a class="dropdown-item d-inline-flex align-items-center p-2" href="profile.php">
            <i class="ti ti-user-circle me-2"></i>My Profile</a>
            <a class="dropdown-item d-inline-flex align-items-center p-2"
                                    href="#"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#theme-setting">
                                    <i class="ti ti-settings me-2"></i>Settings
                                    </a>
        <hr class="m-0">
        <a class="dropdown-item d-inline-flex align-items-center p-2"  href="login.php?logout=true">
            <i class="ti ti-login me-2"></i>Logout</a>
    </div>
</div>

						</div>
					</div>

				</div>
			</div>

			<!-- Mobile Menu -->
			<div class="dropdown mobile-user-menu">
				<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
					aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
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
								<a href="javascript:void(0);"  class="subdrop active"><i class="ti ti-user"></i><span>Dashboard</span><span class="menu-arrow"></span></a>
									<ul>
										<li><a href="dashboard.php"class="active">Student Dashboard</a></li>
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
            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
					<div class="my-auto mb-2">
						<h3 class="page-title mb-1">Admin Dashboard</h3>
						<nav>
							<ol class="breadcrumb mb-0">
								<li class="breadcrumb-item">
									<a href="index.html">Dashboard</a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
							</ol>
						</nav>
					</div>
					
				</div>
        
				<div class="row">
					<div class="col-md-12">
						

						<!-- Dashboard Content -->
						<div class="card bg-dark">
							<div class="overlay-img">
								<img src="assets/img/bg/shape-04.png" alt="img" class="img-fluid shape-01">
								<img src="assets/img/bg/shape-01.png" alt="img" class="img-fluid shape-02">
								<img src="assets/img/bg/shape-02.png" alt="img" class="img-fluid shape-03">
								<img src="assets/img/bg/shape-03.png" alt="img" class="img-fluid shape-04">
							</div>
							<div class="card-body">
								<div
									class="d-flex align-items-xl-center justify-content-xl-between flex-xl-row flex-column">
									<div class="mb-3 mb-xl-0">
										<div class="d-flex align-items-center flex-wrap mb-2">
                    <h1 class="text-white me-2">Welcome Back, Mr. <?= htmlspecialchars($_SESSION['admin']['last_name']) ?></h1>
											
										</div>
										<p class="text-white">Have a Good day at work</p>
									</div>
                  <p class="text-white"><i class="ti ti-refresh me-1"></i> Updated Recently on <?= date('d M Y') ?></p>

								</div>
							</div>
						</div>
						<!-- /Dashboard Content -->

					</div>
				</div>
            <!-- /Page Header -->
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <!-- Total Students -->
                <div class="five-col d-flex">
                    <div class="card flex-fill animate-card border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl bg-danger-transparent me-2 p-1">
                                    <img src="assets/img/icons/student.svg" alt="Student Icon">
                                </div>
                                <div class="overflow-hidden flex-fill">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h2 class="counter"><?php echo htmlspecialchars($totalStudents); ?></h2>
                                        <span class="badge <?php echo htmlspecialchars($badgeClass); ?>">
                                            <?php echo round((count($activeStudents) / max($totalStudents, 1)) * 100, 1); ?>%
                                        </span>
                                    </div>
                                    <p>Total Students</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between border-top mt-3 pt-3">
                                <p class="mb-0">Active: <span class="text-dark fw-semibold"><?php echo count($activeStudents); ?></span></p>
                                <span class="text-light">|</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Teachers -->
                <div class="five-col d-flex">
                    <div class="card flex-fill animate-card border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl me-2 bg-secondary-transparent p-1">
                                    <img src="assets/img/icons/teacher.svg" alt="Teacher Icon">
                                </div>
                                <div class="overflow-hidden flex-fill">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h2 class="counter"><?php echo htmlspecialchars($totalTeachers); ?></h2>
                                    </div>
                                    <p>Total Teachers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             
                
                <!-- Total Classes -->
                <div class="five-col d-flex">
                    <div class="card flex-fill animate-card border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl me-2 bg-warning-transparent p-1">
                                    <img src="assets/img/icons/staff.svg" alt="Classes Icon">
                                </div>
                                <div class="overflow-hidden flex-fill">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h2 class="counter"><?php echo htmlspecialchars($totalClasses); ?></h2>
                                    </div>
                                    <p>Total Classes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Subjects -->
                <div class="five-col d-flex">
                    <div class="card flex-fill animate-card border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl me-2 bg-success-transparent p-1">
                                    <img src="assets/img/icons/subject.svg" alt="Subjects Icon">
                                </div>
                                <div class="overflow-hidden flex-fill">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h2 class="counter"><?php echo htmlspecialchars($totalSubjects); ?></h2>
                                    </div>
                                    <p>Total Subjects</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="five-col d-flex">
                    <div class="card flex-fill animate-card border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl me-2 bg-success-transparent p-1">
                                    <img src="assets/img/icons/classroom.svg" alt="Subjects Icon">
                                </div>
                                <div class="overflow-hidden flex-fill">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h2 class="counter"><?php echo htmlspecialchars($totalclassrooms); ?></h2>
                                    </div>
                                    <p>Total Classrooms</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance and Top Students -->
            <div class="row mb-4">
  <!-- Left Column: Attendance + Top Students +teachers per subject -->
  <div class="col-xxl-4 col-xl-6 col-md-12 d-flex flex-column gap-3">
    
    <!-- Attendance -->
    <div class="card flex-fill">
      <div class="card-header">
        <h4 class="card-title mb-2">Attendance</h4>
      </div>
      <div class="d-flex gap-2 px-3 pb-3">
        <input type="date" id="startDate" class="form-control form-control-sm" value="<?= $today ?>" style="max-width: 150px;">
        <input type="date" id="endDate" class="form-control form-control-sm" value="<?= $today ?>" style="max-width: 150px;">
      </div>
      <div class="card-body text-center">
        <canvas id="student-chart" width="200" height="200" class="mb-4"></canvas>
        <a href="liststudent.php" class="btn btn-light">
          <i class="ti ti-calendar-share me-1"></i>View All
        </a>
      </div>
    </div>

    <!-- Top Students -->
    <div class="bg-info p-3 br-5 text-center flex-fill" style="height: 482px; max-width: 350px; margin: 0 auto;">
      <h5 class="mb-3 text-white">Top 3 Students by Moyenne</h5>
      <div id="student-cards-container" style="position: relative; height: 320px; overflow: hidden;">
        <?php foreach ($topStudents as $index => $student): ?>
          <div class="student-card text-white p-3 rounded shadow-sm position-absolute w-100"
               style="top: 0; left: <?= $index === 0 ? '0' : '100%' ?>; transition: left 0.4s ease; z-index: <?= $index === 0 ? 2 : 1 ?>;">
            <h6 class="mb-1"><?= htmlspecialchars($student['username'] . ' ' . $student['name']) ?></h6>
            <p class="mb-0">Moyenne: <?= number_format($student['moyenne'], 2) ?></p>
            <p class="mb-0"><?= htmlspecialchars($student['class_name'] ?? '') ?></p>
            <img src="<?= isset($student['picture']) && $student['picture'] ? '/stage/controller/' . htmlspecialchars($student['picture']) : 'assets/img/students/default.jpg' ?>"
                 alt="Student Image"
                 style="width: 100%; height: 280px; object-fit: cover; display: block; margin: 15px 0;">
          </div>
        <?php endforeach; ?>
      </div>
      <div class="d-flex justify-content-center gap-3 mt-3">
        <button id="prevStudent" type="button" class="btn fw-semibold px-4"
                style="background-color: #1e90ff; color: white; border-radius: 50px; border: none;">&larr;</button>
        <button id="nextStudent" type="button" class="btn fw-semibold px-4"
                style="background-color: #1e90ff; color: white; border-radius: 50px; border: none;">&rarr;</button>
      </div>
    </div>

    <!-- Teachers Per Subject -->
    <div class="card flex-fill" style="height: 350px;">
      <div style="padding: 0.3rem 0.8rem; display: flex; align-items: center; justify-content: space-between;">
        <h4 class="card-title" style="font-size: 0.95rem; margin: 0;">Teachers Per Subject</h4>
      </div>
      <div class="card-body" style="padding: 0.5rem;">
        <ul class="list-group" id="teachersPerSubjectList"></ul>
      </div>
    </div>

  </div>
<!-- Middle Column: Performance + Top Subjects + Quick Links stacked vertically -->
<div class="col-xxl-4 col-xl-4 d-flex flex-column" style="gap: 1rem; max-height: 1100px;">

  <!-- Performance Card -->
  <div class="card flex-fill" style="font-size: 0.85rem; padding: 0.5rem; min-height: 350px;">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding: 0.3rem 0.5rem;">
      <h4 class="card-title" style="font-size: 1rem; margin: 0;">Classes in Levels</h4>
    </div>
    <div class="card-body" style="padding: 0.5rem;">
      <div class="text-center" style="max-height: 280px;">
        <canvas id="classLevelChart" height="250"></canvas>
      </div>
    </div>
  </div>
  <div class="card flex-fill" style="font-size: 0.85rem; padding: 0.5rem; min-height: 350px;">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding: 0.3rem 0.5rem;">
      <h4 class="card-title" style="font-size: 1rem; margin: 0;">Student Status</h4>
    </div>
    <div class="card-body" style="padding: 0.5rem;">
      <div class="text-center" style="max-height: 280px;">
        <canvas id="studentStatusPieChart" height="250"></canvas>
      </div>
    </div>
  </div>
 
  <!-- Top Subjects Card -->
  <div class="card flex-fill" style="max-height: 350px; font-size: 13px; display: flex; flex-direction: column;">
  <div style="padding: 0.3rem 0.8rem; flex-shrink: 0; display: flex; align-items: center; justify-content: space-between;">
    <h4 class="card-title" style="font-size: 0.95rem; margin: 0;">Subject Attendance Overview Per Class</h4>
    <select id="classSelect" class="form-select form-select-sm" 
            style="width: 120px; font-size: 0.8rem; padding: 0.15rem 0.3rem;">
      <?php foreach ($classes as $className => $subjects): ?>
        <option value="<?= htmlspecialchars($className) ?>">
          <?= htmlspecialchars($className) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <ul id="subjectsList" class="list-group list-group-flush" 
      style="overflow-y: auto; font-size: 0.85rem; flex-grow: 1; margin-bottom: 0; padding-right: 5px;">
    <?php if (!empty($classes)): ?>
      <?php 
        $firstClassSubjects = reset($classes);
        $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
        $i = 0;
      ?>
      <?php foreach ($firstClassSubjects as $subject): ?>
        <?php 
          $colorClass = $colors[$i++ % count($colors)];
          $completion = htmlspecialchars($subject['completion']);
          $percent = (int) $subject['percent'];
        ?>
        <li class="list-group-item py-1" style="font-size: 0.85rem;">
          <div class="row align-items-center">
            <div class="col-4">
              <p class="text-dark mb-0" style="font-size: 0.85rem;">
                <?= htmlspecialchars($subject['subject_name']) ?>
              </p>
            </div>
            <div class="col-8">
              <div class="progress progress-xs flex-grow-1" style="height: 10px;">
                <div class="progress-bar <?= $colorClass ?> rounded" role="progressbar" 
                     style="width: <?= $percent ?>%;" 
                     aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                </div>
              </div>
              <small class="text-muted"><?= $completion ?> présents</small>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <li class="list-group-item text-center">No subjects available</li>
    <?php endif; ?>
  </ul>
</div>
<div class="card flex-fill" style="font-size: 0.85rem; padding: 0.5rem; min-height: 350px;">
  <div class="card-header d-flex align-items-center justify-content-between" style="padding: 0.3rem 0.5rem;">
    <h4 class="card-title" style="font-size: 1rem; margin: 0;">Students in Levels</h4>
  </div>
  <div class="card-body" style="padding: 0.5rem;">
    <div class="text-center" style="max-height: 280px;">
      <canvas id="studentLevelChart" height="250"></canvas>
    </div>
  </div>
</div>


  <!-- Quick Links Card -->
 

</div>
<!-- Right Column: Quick Links -->
<div class="col-xxl-4 col-xl-2 col-md-12 d-flex flex-column" style="min-height: 300px; max-height: 800px; display: flex; flex-direction: column; gap: 1rem;">

  <!-- Quick Links Card -->
  <div class="card flex-fill" 
       style="font-size: 0.85rem; padding: 0.5rem; min-height: 350px; display: flex; flex-direction: column;">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding: 0.3rem 0.5rem;">
      <h4 class="card-title" style="font-size: 1rem; margin: 0;">Quick Links</h4>
    </div>
    <div class="card-body pb-1">
      <div class="owl-carousel link-slider">
        <div class="item">
          <a href="addemploi.php" class="d-block bg-success-transparent ronded p-2 text-center mb-3 class-hover">
            <div class="avatar avatar-lg border p-1 border-success rounded-circle mb-2">
              <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-success rounded-circle">
                <i class="ti ti-calendar"></i>
              </span>
            </div>
            <p class="text-dark">Shedule</p>
          </a>
          <a href="addclass.php" class="d-block bg-secondary-transparent ronded p-2 text-center mb-3 class-hover">
            <div class="avatar avatar-lg border p-1 border-secondary rounded-circle mb-2">
              <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-secondary rounded-circle">
                <i class="ti ti-book"></i>
              </span>
            </div>
            <p class="text-dark">Classes</p>
          </a>
        </div>
        <div class="item">
          <a href="addstudent.php" class="d-block bg-primary-transparent ronded p-2 text-center mb-3 class-hover">
            <div class="avatar avatar-lg border p-1 border-primary rounded-circle mb-2">
              <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-primary rounded-circle">
                <i class="ti ti-school"></i>
              </span>
            </div>
            <p class="text-dark">Students</p>
          </a>
          <a href="addmatier.php" class="d-block bg-danger-transparent ronded p-2 text-center mb-3 class-hover">
            <div class="avatar avatar-lg border p-1 border-danger rounded-circle mb-2">
              <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-danger rounded-circle">
                <i class="ti ti-file-text"></i>
              </span>
            </div>
            <p class="text-dark">Subjects</p>
          </a>
        </div>
        <div class="item">
          <a href="addenseignant.php" class="d-block bg-warning-transparent ronded p-2 text-center mb-3 class-hover">
            <div class="avatar avatar-lg border p-1 border-warning rounded-circle mb-2">
              <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-warning rounded-circle">
                <i class="ti ti-user"></i>
              </span>
            </div>
            <p class="text-dark">Teachers</p>
          </a>
          <a href="listpaiement.php" class="d-block bg-skyblue-transparent ronded p-2 text-center mb-3 class-hover">
            <div class="avatar avatar-lg border p-1 border-skyblue rounded-circle mb-2">
              <span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-skyblue rounded-circle">
                <i class="ti ti-credit-card"></i>
              </span>
            </div>
            <p class="text-dark">Paiement</p>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Student Joined Line Chart Card -->
  <div class="card flex-fill" style="font-size: 0.85rem; padding: 0.5rem; min-height: 350px; display: flex; flex-direction: column;">
    <div class="card-header d-flex align-items-center justify-content-between" style="padding: 0.3rem 0.5rem;">
      <h4 class="card-title" style="font-size: 1rem; margin: 0;">Students Joined Over Time</h4>
    </div>
    <div class="card-body" style="padding: 0.5rem; flex: 1 1 auto; min-height: 0; display: flex; align-items: center; justify-content: center;">
      <canvas id="studentsJoinChart" height="250" style="width: 100%; height: 100%;"></canvas>
    </div>
  </div>
  <!-- Teachers Joined Line Chart Card -->

  <div class="card flex-fill" style="font-size: 0.75rem; padding: 0.4rem; min-height: 675px; display: flex; flex-direction: column;">
  <div style="padding: 0.2rem 0.5rem; display: flex; align-items: center; justify-content: space-between;">
    <h4 class="card-title" style="font-size: 1rem; margin: 0;">Charge Amount by Type</h4>
    <input type="month" id="chargeMonthPicker" style="font-size: 0.8rem; padding: 0.1rem;">
  </div>
  <div class="card-body" style="padding: 0.5rem; flex-grow: 1; display: flex; align-items: center; justify-content: center;">
    <canvas id="chargeTypeChart" style="width: 100%; max-height: 600px; height: 600px;"></canvas>
  </div>
</div>


</div>


<!-- Chart Script -->


            <!-- Fees and Leave Requests -->
            <!-- Fees and Leave Requests -->
        <div class="row mb-4 mt-5">
            <div class="col-xxl-12 col-xl-11 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title">Payments per Day</h4>
                <div class="dropdown"></div>
                </div>
                <div class="card-body pb-0" style="position: relative; height: 350px;">
                <canvas id="feesChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
            </div>    
        </div>
       <!-- Total Earnings Section -->
<div class="row mb-4 mt-5">
  <div class="col-xxl-12 col-xl-11 d-flex">
    <div class="card flex-fill">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title">Total Earnings</h4>
      </div>
      <div class="card-body pb-0" style="position: relative; height: 350px;">
        <div class="d-flex flex-column align-items-start mb-3">
          <h6 class="mb-1" style="font-size: 0.95rem;">Total Earnings</h6>
          <h2 style="font-size: 1.5rem;"><?= number_format(array_sum($dailyEarnings), 3) ?> DT</h2>
        </div>
        <canvas id="totalEarningsChart" style="width: 200%; height: 100px;"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Total Charges Section -->
<div class="row mb-4 mt-5">
  <div class="col-xxl-12 col-xl-11 d-flex">
    <div class="card flex-fill">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title">Total Charges</h4>
      </div>
      <div class="card-body pb-0" style="position: relative; height: 350px;">
        <div class="d-flex flex-column align-items-start mb-3">
          <h6 class="mb-1" style="font-size: 0.95rem;">Total Charges</h6>
          <h2 style="font-size: 1.5rem;"><?= number_format(array_sum($dailyCharges), 3) ?> DT</h2>
        </div>
        <canvas id="totalChargesChart" style="width: 200%; height: 100px;"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Monthly Profit Section -->
<div class="row mb-4 mt-5">
  <div class="col-xxl-12 col-xl-11 d-flex">
    <div class="card flex-fill">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title">Monthly Profit</h4>
      </div>
      <div class="card-body pb-0" style="position: relative; height: 350px;">
        <canvas id="profitChart" style="width: 200%; height: 100px;"></canvas>
      </div>
    </div>
  </div>
</div>



</div>



          
  


<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Reuse these from your PHP-generated data and HTML elements
  const chargeTypeData = <?= json_encode($chargeTypeData) ?>;
  const monthInput = document.getElementById('chargeMonthPicker');
  const ctxChargeType = document.getElementById('chargeTypeChart').getContext('2d');
  const chartContainer = ctxChargeType.canvas.parentElement;

  // Colors for bars
  const chargeTypeBarColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];

  // Chart instance reference
  let chargeTypeChart;

  function createChargeChart(dataForMonth) {
    const labels = Object.keys(dataForMonth);
    const data = Object.values(dataForMonth);

    // Destroy previous chart instance if exists
    if (chargeTypeChart) chargeTypeChart.destroy();

    chargeTypeChart = new Chart(ctxChargeType, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Charge Amount (DT)',
          data: data.map(() => 0),  // Start with zero for animation
          backgroundColor: labels.map((_, i) => chargeTypeBarColors[i % chargeTypeBarColors.length]),
          borderRadius: 6,
          barThickness: 15,
          maxBarThickness: 20
        }]
      },
      options: {
        responsive: true,
        animation: {
          duration: 1500,
          easing: 'easeOutQuart'
        },
        plugins: { legend: { display: false } },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });

    // Animate bars when chart container scrolls into view
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          chargeTypeChart.data.datasets[0].data = data;
          chargeTypeChart.update();
          obs.unobserve(entry.target); // Animate only once
        }
      });
    }, { threshold: 0.3 });

    observer.observe(chartContainer);
  }

  function updateChartForSelectedMonth() {
    const selectedMonth = monthInput.value;
    if (selectedMonth && chargeTypeData[selectedMonth]) {
      createChargeChart(chargeTypeData[selectedMonth]);
      console.log(`Chart updated for month: ${selectedMonth}`, chargeTypeData[selectedMonth]);
    } else {
      createChargeChart({});
      console.log(`No data for month: ${selectedMonth}`);
    }
  }

  // Set default month input value to current month
  monthInput.value = new Date().toISOString().slice(0, 7);

  // Initial chart render
  updateChartForSelectedMonth();

  // Listen for month input changes to update chart
  monthInput.addEventListener('change', updateChartForSelectedMonth);
});

</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Data variables (no redeclaration)
  const studentLevelLabels = <?= json_encode(array_keys($studentCountsByLevel)) ?>;
  const studentLevelCounts = <?= json_encode(array_values($studentCountsByLevel)) ?>;
  const ctxStudentLevel = document.getElementById('studentLevelChart').getContext('2d');
  const chartContainer = document.getElementById('studentLevelChart').parentElement;

  // Create the chart with zero data initially
  const studentLevelChart = new Chart(ctxStudentLevel, {
    type: 'bar',
    data: {
      labels: studentLevelLabels,
      datasets: [{
        label: 'Number of Students',
        data: studentLevelCounts.map(() => 0), // start with zeros for animation
        backgroundColor: [
          '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'
        ],
        borderRadius: 6,
        barThickness: 10,
        maxBarThickness: 15
      }]
    },
    options: {
      responsive: true,
      animation: {
        duration: 1500,
        easing: 'easeOutQuart'
      },
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } }
      }
    }
  });

  // Intersection Observer to trigger animation when visible
  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Update chart data to actual counts
        studentLevelChart.data.datasets[0].data = studentLevelCounts;
        studentLevelChart.update();
        obs.unobserve(entry.target); // Only animate once
      }
    });
  }, { threshold: 0.3 });

  observer.observe(chartContainer);
});
</script>

<script>
const teachersSubjectsStats = <?= json_encode($teachersPerSubject) ?>;
const teachersList = document.getElementById('teachersPerSubjectList');
const barColors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];

let currentTeacherBars = {}; // Store bars keyed by subject for animation

function renderTeachersPerSubject() {
  teachersList.innerHTML = '';
  currentTeacherBars = {};

  const max = Math.max(...Object.values(teachersSubjectsStats));

  Object.entries(teachersSubjectsStats).forEach(([subject, count], index) => {
    const color = barColors[index % barColors.length];
    const percent = max ? Math.round((count / max) * 100) : 0;

    const li = document.createElement('li');
    li.className = 'list-group-item py-1';

    // Start width at 0%, store target in data attribute for animation
    li.innerHTML = `
      <div class="row align-items-center">
        <div class="col-4">
          <p class="text-dark mb-0" style="font-size: 0.85rem;">${subject}</p>
        </div>
        <div class="col-8">
          <div class="progress progress-xs flex-grow-1" style="height: 10px;">
            <div class="progress-bar ${color} rounded progress-animate" role="progressbar"
              data-target="${percent}" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            </div>
          </div>
          <small class="text-muted">${count} enseignants</small>
        </div>
      </div>
    `;

    teachersList.appendChild(li);

    // Save reference to bar for animation
    const bar = li.querySelector('.progress-bar');
    currentTeacherBars[subject] = bar;
  });
}

function animateTeacherBars() {
  Object.values(currentTeacherBars).forEach(bar => {
    const target = parseInt(bar.getAttribute('data-target'), 10);
    let value = 0;

    const interval = setInterval(() => {
      value++;
      if (value > target) {
        clearInterval(interval);
      } else {
        bar.style.width = `${value}%`;
        bar.setAttribute('aria-valuenow', value);
      }
    }, 10);
  });
}

function observeTeachersSection() {
  const container = teachersList.parentElement;

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateTeacherBars();
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  observer.observe(container);
}

// Call on load
renderTeachersPerSubject();
observeTeachersSection();
</script>

<script>
     const joinLabels = <?= json_encode($joinLabels); ?>;
  const joinData = <?= json_encode($joinData); ?>;

    console.log("Join Labels:", joinLabels);
  console.log("Join Data:", joinData);
  const ctxStudentsJoin = document.getElementById('studentsJoinChart').getContext('2d');
  const chartContainer = document.getElementById('studentsJoinChart').parentElement;

  let studentsJoinChart;

  // Static chart (no animation)
  function createStaticStudentsJoinChart() {
    studentsJoinChart = new Chart(ctxStudentsJoin, {
      type: 'line',
      data: {
        labels: joinLabels,
        datasets: [{
          label: 'Students Joined',
          data: joinData,
          borderColor: '#4e73df',
          backgroundColor: 'rgba(78, 115, 223, 0.1)',
          fill: true,
          tension: 0.3,
          borderWidth: 2,
          borderDash: [],
          borderDashOffset: 0,
          pointRadius: 3,
          pointBackgroundColor: '#4e73df',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
        }]
      },
      options: {
        animation: false,
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  }

  // Animated chart with line dash animation
  function createAnimatedStudentsJoinChart() {
    studentsJoinChart = new Chart(ctxStudentsJoin, {
      type: 'line',
      data: {
        labels: joinLabels,
        datasets: [{
          label: 'Students Joined',
          data: joinData,
          borderColor: '#4e73df',
          backgroundColor: 'rgba(78, 115, 223, 0.1)',
          fill: true,
          tension: 0.3,
          borderWidth: 2,
          borderDash: [1000],
          borderDashOffset: 1000,
          pointRadius: 3,
          pointBackgroundColor: '#4e73df',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 0 },
        animations: {
          borderDashOffset: {
            from: 1000,
            to: 0,
            duration: 6000,
            easing: 'linear'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  }

  // Initially create the static chart
  createStaticStudentsJoinChart();

  // Observe scroll and trigger animation on visibility
  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Destroy current static chart before creating animated one
        if (studentsJoinChart) studentsJoinChart.destroy();

        // Create animated chart with line dash effect
        createAnimatedStudentsJoinChart();

        // Stop observing after animation started once
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  observer.observe(chartContainer);

</script>


<script>
const labels = <?= json_encode($paymentDates) ?>;
const dataPoints = <?= json_encode($dailyEarnings) ?>;

console.log('Payment Dates (labels):', labels);
console.log('Daily Earnings (data):', dataPoints);
// Total Earnings Chart
(() => {
  const ctx = document.getElementById('totalEarningsChart').getContext('2d');
  const container = document.getElementById('totalEarningsChart').parentElement;

  let totalEarningsChart;

  function createStaticChart() {
    totalEarningsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels, // should contain ['2024-01', '2024-02', ...]
        datasets: [{
          label: 'Monthly Earnings (DT)',
          data: dataPoints, // should match monthly totals
          fill: true,
          backgroundColor: 'rgba(52, 152, 219, 0.2)',
          borderColor: '#3498db',
          borderWidth: 3,
          tension: 0.3,
          borderDash: [],
          borderDashOffset: 0,
          pointRadius: 3,
          pointBackgroundColor: '#3498db',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
        }]
      },
      options: {
        animation: false,
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            title: { display: true, text: 'Month' },
            ticks: { maxRotation: 45, minRotation: 30 }
          },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Earnings (DT)' },
            ticks: { callback: val => val + 'DT' }
          }
        },
        plugins: { legend: { position: 'top' } }
      }
    });
  }

  function createAnimatedChart() {
    totalEarningsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Monthly Earnings (DT)',
          data: dataPoints,
          fill: true,
          backgroundColor: 'rgba(52, 152, 219, 0.2)',
          borderColor: '#3498db',
          borderWidth: 3,
          tension: 0.3,
          borderDash: [1600],
          borderDashOffset: 1600,
          pointRadius: 3,
          pointBackgroundColor: '#3498db',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 0 },
        animations: {
          borderDashOffset: {
            from: 1600,
            to: 0,
            duration: 6000,
            easing: 'linear'
          }
        },
        scales: {
          x: {
            title: { display: true, text: 'Month' },
            ticks: { maxRotation: 45, minRotation: 30 }
          },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Earnings (DT)' },
            ticks: { callback: val => val + 'DT' }
          }
        },
        plugins: { legend: { position: 'top' } }
      }
    });
  }

  createStaticChart();

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        if (totalEarningsChart) totalEarningsChart.destroy();
        createAnimatedChart();
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  observer.observe(container);
})();

const chargeLabels = <?= json_encode($chargeDates) ?>;
const chargeDataPoints = <?= json_encode($dailyCharges) ?>;

(() => {
  const ctx = document.getElementById('totalChargesChart').getContext('2d');
  const container = document.getElementById('totalChargesChart').parentElement;

  let totalChargesChart;

  function createStaticChart() {
    totalChargesChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: chargeLabels, // should be months: ['2024-01', '2024-02', ...]
        datasets: [{
          label: 'Monthly Charges (DT)',
          data: chargeDataPoints, // should be monthly totals
          fill: true,
          backgroundColor: 'rgba(231, 76, 60, 0.2)', // red-ish
          borderColor: '#e74c3c',
          borderWidth: 3,
          tension: 0.3,
          pointRadius: 3,
          pointBackgroundColor: '#e74c3c',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
        }]
      },
      options: {
        animation: false,
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            title: { display: true, text: 'Month' },
            ticks: { maxRotation: 45, minRotation: 30 }
          },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Charges (DT)' },
            ticks: { callback: val => val + 'DT' }
          }
        },
        plugins: { legend: { position: 'top' } }
      }
    });
  }

  function createAnimatedChart() {
    totalChargesChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: chargeLabels,
        datasets: [{
          label: 'Monthly Charges (DT)',
          data: chargeDataPoints,
          fill: true,
          backgroundColor: 'rgba(231, 76, 60, 0.2)',
          borderColor: '#e74c3c',
          borderWidth: 3,
          tension: 0.3,
          borderDash: [1600],
          borderDashOffset: 1600,
          pointRadius: 3,
          pointBackgroundColor: '#e74c3c',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 0 },
        animations: {
          borderDashOffset: {
            from: 1600,
            to: 0,
            duration: 6000,
            easing: 'linear'
          }
        },
        scales: {
          x: {
            title: { display: true, text: 'Month' },
            ticks: { maxRotation: 45, minRotation: 30 }
          },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Charges (DT)' },
            ticks: { callback: val => val + 'DT' }
          }
        },
        plugins: { legend: { position: 'top' } }
      }
    });
  }

  createStaticChart();

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        if (totalChargesChart) totalChargesChart.destroy();
        createAnimatedChart();
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  observer.observe(container);
})();

// Calculate and display Earnings - Charges difference
const profitLabels = <?= json_encode($paymentDates) ?>; // Same months as earnings/charges 
const profitDataPoints = <?= json_encode(array_map(fn($e, $c) => round($e - $c, 3), $dailyEarnings, $dailyCharges)) ?>;

(() => {
  const ctx = document.getElementById('profitChart').getContext('2d');
  const container = document.getElementById('profitChart').parentElement;

  // Insert zero crossing points (same as you have)
  function insertZeroCrossings(labels, data) {
    const newLabels = [];
    const newData = [];

    for (let i = 0; i < data.length - 1; i++) {
      const y0 = data[i];
      const y1 = data[i + 1];
      const label0 = labels[i];

      newLabels.push(label0);
      newData.push(y0);

      if ((y0 > 0 && y1 < 0) || (y0 < 0 && y1 > 0)) {
        newLabels.push(''); // no label on zero crossing
        newData.push(0);
      }
    }

    newLabels.push(labels[labels.length - 1]);
    newData.push(data[data.length - 1]);

    return { newLabels, newData };
  }

  const { newLabels: extendedLabels, newData: extendedData } = insertZeroCrossings(profitLabels, profitDataPoints);

  // Segment style by average endpoints (same as your code)
  const getSegmentStyle = (p0, p1) => {
    const avg = (p0 + p1) / 2;
    return {
      borderColor: avg >= 0 ? '#2ecc71' : '#e74c3c',
      backgroundColor: avg >= 0 ? 'rgba(46, 204, 113, 0.2)' : 'rgba(231, 76, 60, 0.2)',
      pointBackgroundColor: avg >= 0 ? '#2ecc71' : '#e74c3c'
    };
  };

  // Custom plugin to draw labels between points
  const middleLabelPlugin = {
    id: 'middleLabelPlugin',
    afterDraw(chart) {
      const ctx = chart.ctx;
      const xScale = chart.scales.x;
      const yScale = chart.scales.y;
      const labels = chart.data.labels;

      ctx.save();
      ctx.fillStyle = '#666';
      ctx.font = '12px Arial, sans-serif';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'top';

      // Draw labels centered between points, skip empty labels
      for (let i = 0; i < labels.length - 1; i++) {
        if (!labels[i]) continue;  // skip empty labels

        const x1 = xScale.getPixelForValue(i);
        const x2 = xScale.getPixelForValue(i + 1);
        const xMid = (x1 + x2) / 2;
        const yPos = yScale.bottom + 10;  // slightly below x-axis

        ctx.fillText(labels[i], xMid, yPos);
      }
      ctx.restore();
    }
  };

  let profitChart;

  function createChart(animated = false) {
    if (profitChart) profitChart.destroy();

    profitChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: extendedLabels,
        datasets: [{
          label: 'Monthly Profit (DT)',
          data: extendedData,
          fill: true,
          segment: {
            borderColor: ctx => {
              const p0 = ctx.p0?.parsed?.y ?? 0;
              const p1 = ctx.p1?.parsed?.y ?? 0;
              return getSegmentStyle(p0, p1).borderColor;
            },
            backgroundColor: ctx => {
              const p0 = ctx.p0?.parsed?.y ?? 0;
              const p1 = ctx.p1?.parsed?.y ?? 0;
              return getSegmentStyle(p0, p1).backgroundColor;
            }
          },
          pointBackgroundColor: extendedData.map(v => v >= 0 ? '#2ecc71' : '#e74c3c'),
          borderWidth: 3,
          tension: 0.3,
          pointRadius: 3,
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          borderDash: animated ? [1600] : [],
          borderDashOffset: animated ? 1600 : 0,
        }]
      },
      options: {
        animation: animated ? { duration: 0 } : false,
        animations: animated ? {
          borderDashOffset: {
            from: 1600,
            to: 0,
            duration: 6000,
            easing: 'linear'
          }
        } : {},
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            title: { display: true, text: 'Month' },
            ticks: {
              maxRotation: 45,
              minRotation: 30,
              // Hide default labels because of custom drawing
              callback: () => ''
            }
          },
          y: {
            title: { display: true, text: 'Profit (DT)' },
            ticks: {
              callback: val => val + 'DT'
            }
          }
        },
        plugins: {
          legend: { position: 'top' },

          tooltip: {
            enabled: true,
            mode: 'nearest',
            intersect: false,
            callbacks: {
              label(context) {
                const value = context.parsed.y;
                return `Profit: ${value} DT`;
              }
            }
          }
        }
      },
      plugins: [middleLabelPlugin]
    });
  }

  createChart(false); // static chart initially

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        createChart(true); // animated chart on visible
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  observer.observe(container);
})();






// Students Joined Chart
(() => {
  const ctxStudentsJoin = document.getElementById('studentsJoinChart').getContext('2d');
  const containerStudents = document.getElementById('studentsJoinChart').parentElement;

  let studentsJoinChart;

  function createStaticStudentsJoinChart() {
    studentsJoinChart = new Chart(ctxStudentsJoin, {
      type: 'line',
      data: {
        labels: joinLabels,
        datasets: [{
          label: 'Students Joined',
          data: joinData,
          borderColor: '#4e73df',
          backgroundColor: 'rgba(78, 115, 223, 0.1)',
          fill: true,
          tension: 0.3,
          borderWidth: 2,
          borderDash: [],
          borderDashOffset: 0,
          pointRadius: 3,
          pointBackgroundColor: '#4e73df',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
        }]
      },
      options: {
        animation: false,
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  }

  function createAnimatedStudentsJoinChart() {
    studentsJoinChart = new Chart(ctxStudentsJoin, {
      type: 'line',
      data: {
        labels: joinLabels,
        datasets: [{
          label: 'Students Joined',
          data: joinData,
          borderColor: '#4e73df',
          backgroundColor: 'rgba(78, 115, 223, 0.1)',
          fill: true,
          tension: 0.3,
          borderWidth: 2,
          borderDash: [1000],
          borderDashOffset: 1000,
          pointRadius: 3,
          pointBackgroundColor: '#4e73df',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 0 },
        animations: {
          borderDashOffset: {
            from: 1000,
            to: 0,
            duration: 6000,
            easing: 'linear'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  }

  createStaticStudentsJoinChart();

  const observerStudents = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        if (studentsJoinChart) studentsJoinChart.destroy();
        createAnimatedStudentsJoinChart();
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  observerStudents.observe(containerStudents);
})();

</script>

        </div>
    </div>
    <!-- /Page Wrapper -->

	<!-- /Main Wrapper -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
   const paymentDates = <?= json_encode($dates) ?>;
  const paymentCounts = <?= json_encode($counts) ?>;
  const ctxFees = document.getElementById('feesChart').getContext('2d');
  const containerFees = document.getElementById('feesChart').parentElement;

  let feesChart;

  // Static chart: bars instantly rendered at full height (no animation)
  function createStaticFeesChart() {
    feesChart = new Chart(ctxFees, {
      type: 'bar',
      data: {
        labels: paymentDates,
        datasets: [{
          label: 'Payments per Day',
          data: paymentCounts,
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1,
          borderRadius: 4,
          maxBarThickness: 20
        }]
      },
      options: {
        animation: false,
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: { maxRotation: 90, minRotation: 45 },
            title: { display: true, text: 'Date' }
          },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Number of Payments' },
            ticks: { precision: 0, stepSize: 1 }
          }
        },
        plugins: { legend: { display: false } }
      }
    });
  }

  // Animated chart: bars grow from zero to their value (animated)
  function createAnimatedFeesChart() {
    feesChart = new Chart(ctxFees, {
      type: 'bar',
      data: {
        labels: paymentDates,
        datasets: [{
          label: 'Payments per Day',
          data: paymentCounts,
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1,
          borderRadius: 4,
          maxBarThickness: 20
        }]
      },
      options: {
        animation: {
          duration: 2500,
          easing: 'easeOutQuart'
        },
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: { maxRotation: 90, minRotation: 45 },
            title: { display: true, text: 'Date' }
          },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Number of Payments' },
            ticks: { precision: 0, stepSize: 1 }
          }
        },
        plugins: { legend: { display: false } }
      }
    });
  }

  // Initially create static chart (no animation)
  createStaticFeesChart();

  // IntersectionObserver to start animation on scroll into view
  const observerFees = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        if (feesChart) feesChart.destroy();
        createAnimatedFeesChart();
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  observerFees.observe(containerFees);
</script>
    <script>
        
document.addEventListener('DOMContentLoaded', () => {
  // Data for Performance Chart
  const levelLabels = <?= json_encode(array_keys($classCountsByLevel)) ?>;
  const levelCounts = <?= json_encode(array_values($classCountsByLevel)) ?>;

  const ctxLevel = document.getElementById('classLevelChart').getContext('2d');
  new Chart(ctxLevel, {
    type: 'bar',
    data: {
      labels: levelLabels,
      datasets: [{
        label: 'Number of Classes',
        data: levelCounts,
        backgroundColor: [
          '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'
        ],
        borderRadius: 6,
        barThickness: 10,
        maxBarThickness: 15
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } }
      }
    }
  });

  // Data for Student Status Pie Chart
  const statusLabels = <?= json_encode(array_keys($statusCounts)) ?>;
 const statusData = <?= json_encode(array_values($statusCounts)) ?>;

 const ctxStatus = document.getElementById('studentStatusPieChart').getContext('2d');
const containerStatus = document.getElementById('studentStatusPieChart').parentElement;

let statusChart;

// Create static pie chart (no animation)
function createStaticPieChart() {
  statusChart = new Chart(ctxStatus, {
    type: 'pie',
    data: {
      labels: statusLabels,
      datasets: [{
        label: 'Students',
        data: statusData,
        backgroundColor: [
          '#1cc88a', // Active
          '#4e73df', // Graduated
          '#e74a3b'  // Suspended
        ],
        borderWidth: 1
      }]
    },
    options: {
      animation: false,
      responsive: true,
      plugins: {
        legend: { position: 'bottom', labels: { font: { size: 14 } } },
        tooltip: {
          callbacks: {
            label: ctx => `${ctx.label}: ${ctx.parsed} students`
          }
        }
      }
    }
  });
}

// Create animated pie chart
function createAnimatedPieChart() {
  statusChart = new Chart(ctxStatus, {
    type: 'pie',
    data: {
      labels: statusLabels,
      datasets: [{
        label: 'Students',
        data: statusData,
        backgroundColor: [
          '#1cc88a',
          '#4e73df',
          '#e74a3b'
        ],
        borderWidth: 1
      }]
    },
    options: {
      animation: { duration: 2000, easing: 'easeOutQuart' },
      responsive: true,
      plugins: {
        legend: { position: 'bottom', labels: { font: { size: 14 } } },
        tooltip: {
          callbacks: {
            label: ctx => `${ctx.label}: ${ctx.parsed} students`
          }
        }
      }
    }
  });
}

// Initially show static chart
createStaticPieChart();

// Animate on scroll into view
const observer = new IntersectionObserver((entries, obs) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      if (statusChart) statusChart.destroy();
      createAnimatedPieChart();
      obs.unobserve(entry.target);
    }
  });
}, { threshold: 0.3 });

observer.observe(containerStatus);

});
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const classes = <?= json_encode($classes) ?>;
const classSelect = document.getElementById('classSelect');
const subjectsList = document.getElementById('subjectsList');
const colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];

// Store subject elements for animation trigger later
let currentSubjects = [];

function renderSubjects(className) {
  subjectsList.innerHTML = '';
  currentSubjects = [];

  if (!classes[className]) return;

  classes[className].forEach((subject, index) => {
    const colorClass = colors[index % colors.length];
    const li = document.createElement('li');
    li.className = 'list-group-item py-1';

    // Initially set progress width to 0%, then animate later
    li.innerHTML = `
      <div class="row align-items-center">
        <div class="col-4">
          <p class="text-dark mb-0" style="font-size: 0.85rem;">${subject.subject_name}</p>
        </div>
        <div class="col-8">
          <div class="progress progress-xs flex-grow-1" style="height: 10px;">
            <div class="progress-bar ${colorClass} rounded progress-animate" role="progressbar"
            data-target="${subject.percent}"
                 style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            </div>
          </div>
          <small class="text-muted">${subject.completion} présents</small>
        </div>
      </div>
    `;
    subjectsList.appendChild(li);
    currentSubjects.push(li.querySelector('.progress-bar'));
  });
}

// Animate the bars from 0 to their real % width
function animateProgressBars() {
  currentSubjects.forEach(bar => {
    const target = parseInt(bar.getAttribute('data-target'), 10);
    let value = 0;

    const interval = setInterval(() => {
      value++;
      if (value > target) {
        clearInterval(interval);
      } else {
        bar.style.width = `${value}%`;
        bar.setAttribute('aria-valuenow', value);
      }
    }, 10); // Adjust speed here
  });
}

// Initial render
renderSubjects(classSelect.value);

// Update on change
classSelect.addEventListener('change', () => {
  renderSubjects(classSelect.value);
  observeSubjectsSection(); // Reattach observer
});

// Observer to trigger animation
function observeSubjectsSection() {
  const container = subjectsList.parentElement;

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateProgressBars();
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  observer.observe(container);
}

observeSubjectsSection();
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const levelLabels = <?= json_encode(array_keys($classCountsByLevel)) ?>;
    const levelCounts = <?= json_encode(array_values($classCountsByLevel)) ?>;

    const ctxClassLevel = document.getElementById('classLevelChart').getContext('2d');

    new Chart(ctxClassLevel, {
      type: 'bar',
      data: {
        labels: levelLabels,
        datasets: [{
          label: 'Number of Classes',
          data: levelCounts,
          backgroundColor: [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'
          ],
          borderRadius: 6,
          barThickness: 10,  // makes bars thinner
          maxBarThickness: 15
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } }
        }
      }
    });
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Attendance Chart Script -->
<script>
  let presenceCount = <?= json_encode($presenceCount ?? 0) ?>;
  let absenceCount = <?= json_encode($absenceCount ?? 0) ?>;

  const ctxAttendance = document.getElementById('student-chart').getContext('2d');
  const attendanceChart = new Chart(ctxAttendance, {
    type: 'doughnut',
    data: {
      labels: ['Presence', 'Absence'],
      datasets: [{
        data: [presenceCount, absenceCount],
        backgroundColor: ['#198754', '#dc3545'],
        hoverOffset: 20
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: { size: 14 } }
        },
        tooltip: {
          callbacks: {
            label: ctx => {
              const label = ctx.label || '';
              const value = ctx.parsed || 0;
              const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
              const percent = total ? ((value / total) * 100).toFixed(1) : 0;
              return `${label}: ${value} (${percent}%)`;
            }
          }
        }
      }
    }
  });

  function updateAttendanceChart(presence, absence) {
    console.log('🟢 Chart updated with:');
    console.log('Presence:', presence);
    console.log('Absence:', absence);
    attendanceChart.data.datasets[0].data = [presence, absence];
    attendanceChart.update();
  }

  function fetchAndUpdateChart() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;

    console.log('📅 Fetching attendance between:', start, 'and', end);

    fetch(`../../controller/presencecontroller.php?from=${start}&to=${end}`)
      .then(response => response.text()) // get raw response text
      .then(text => {
        console.log("Raw response:", text); // debug server response
        try {
          const data = JSON.parse(text);
          if (data.presenceCount !== undefined && data.absenceCount !== undefined) {
            updateAttendanceChart(data.presenceCount, data.absenceCount);
          } else {
            console.error('Invalid data format:', data);
          }
        } catch(e) {
          console.error('JSON parse error:', e);
        }
      })
      .catch(err => console.error('Fetch error:', err));
  }

  // Fetch on page load automatically
  window.addEventListener('DOMContentLoaded', () => {
    fetchAndUpdateChart();
  });

  // Also fetch when user changes date inputs
  document.getElementById('startDate').addEventListener('change', fetchAndUpdateChart);
  document.getElementById('endDate').addEventListener('change', fetchAndUpdateChart);
</script>

<script>
  // Prepare chart data from PHP
  const chartLabels = <?= json_encode(array_keys($classCountsByLevel)) ?>;
  const chartData = <?= json_encode(array_values($classCountsByLevel)) ?>;

  const ctxClass = document.getElementById('class-chart').getContext('2d');
  new Chart(ctxClass, {
    type: 'bar',
    data: {
      labels: chartLabels,
      datasets: [{
        label: 'Number of Classes',
        data: chartData,
        backgroundColor: [
          '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
          '#858796', '#5a5c69', '#fd7e14', '#20c997'
        ],
        borderRadius: 4
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: ctx => `${ctx.label}: ${ctx.raw} classes`
          }
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    }
  });
</script>

<script>
  const cards = document.querySelectorAll('#student-cards-container .student-card');
  let currentIndex = 0;

  function showCard(index) {
    cards.forEach((card, i) => {
      if (i === index) {
        card.style.left = '0';
        card.style.zIndex = 2;
      } else {
        card.style.left = '100%';
        card.style.zIndex = 1;
      }
    });
    currentIndex = index;
  }

  document.getElementById('prevStudent').addEventListener('click', () => {
    const prevIndex = (currentIndex - 1 + cards.length) % cards.length;
    showCard(prevIndex);
  });

  document.getElementById('nextStudent').addEventListener('click', () => {
    const nextIndex = (currentIndex + 1) % cards.length;
    showCard(nextIndex);
  });
</script>

	<!-- jQuery -->
	<script src="assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>

	<!-- Daterangepikcer JS -->
	<script src="assets/js/moment.js"></script>
	<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
	<script src="assets/js/bootstrap-datetimepicker.min.js"></script>

	<!-- Feather Icon JS -->
	<script src="assets/js/feather.min.js"></script>

	<!-- Slimscroll JS -->
	<script src="assets/js/jquery.slimscroll.min.js"></script>

	<!-- Chart JS -->
	<script src="assets/plugins/apexchart/apexcharts.min.js"></script>
	<script src="assets/plugins/apexchart/chart-data.js"></script>

	<!-- Owl JS -->
	<script src="assets/plugins/owlcarousel/owl.carousel.min.js"></script>

	<!-- Select2 JS -->
	<script src="assets/plugins/select2/js/select2.min.js"></script>

	<!-- Counter JS -->
	<script src="assets/plugins/countup/jquery.counterup.min.js"></script>
	<script src="assets/plugins/countup/jquery.waypoints.min.js">	</script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>
  <style>
  .five-col {
  width: 20%;
  padding: 0.5rem;
  box-sizing: border-box;
}

@media (max-width: 1400px) {
  .five-col {
    width: 50%; /* 2 per row on smaller screens */
  }
}
@media (max-width: 768px) {
  .five-col {
    width: 100%; /* 1 per row on mobile */
  }
}
</style>
<script>
  // Pass PHP session variable to JS
  const adminImage = <?= json_encode($_SESSION['admin']['image'] ?? '') ?>;
  console.log('Admin image path:', adminImage);
</script>

</body>

</html>