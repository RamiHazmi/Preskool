
<?php
require_once __DIR__ . '/../../controller/session_config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputCode = trim($_POST['code'] ?? '');
    if ((string)$inputCode === (string)($_SESSION['reset_code'] ?? '')) {
        $_SESSION['code_verified'] = true;
        header('Location: resetpassword.php');
        exit;
    } else {
        $message = 'Code incorrect. Veuillez réessayer.';
    }
    

    // Console log values
    echo "<script>
        console.log('POST code: " . addslashes($inputCode) . "');
        console.log('SESSION reset_code: " . addslashes($_SESSION['reset_code'] ?? 'null') . "');
        console.log('Message: " . addslashes($message) . "');
    </script>";
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
	<title>Vérfification du code</title>

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
<body class="account-page">

<div class="main-wrapper">
  <div class="container-fuild">
    <div class="login-wrapper w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
      <div class="row">
        <div class="col-lg-6">
          <div class="d-lg-flex align-items-center justify-content-center bg-light-300 d-lg-block d-none flex-wrap vh-100 overflowy-auto bg-01">
            <div>
              <img src="assets/img/authentication/authentication-10.svg" alt="Img">
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
          <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap">
            <div class="col-md-8 mx-auto p-4">
              <form method="post" novalidate>
                <div class="mx-auto mb-5 text-center">
                  <img src="<?= htmlspecialchars('/stage/' . ($_SESSION['societe_logo'] ?? 'assets/img/logo.png')) ?>" class="img-fluid" alt="Logo">
                </div>
                <div class="card">
                  <div class="card-body p-4">
                    <div class="mb-4">
                      <h2 class="mb-2">Code de vérification</h2>
                      <p class="mb-0">Entrez le code à 6 chiffres que vous avez reçu par email.</p>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Code</label>
                      <input type="text" name="code" class="form-control" maxlength="6" pattern="\d{6}" required>
                    </div>
                    <?php if ($message): ?>
                      <div class="text-danger text-center mb-3"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                      <button type="submit" class="btn btn-primary w-100">Vérifier</button>
                    </div>
                    <div class="text-center">
                      <h6 class="fw-normal text-dark mb-0">
                        Retour à <a href="login.php" class="hover-a">Login</a>
                      </h6>
                    </div>
                  </div>
                </div>
                <div class="mt-5 text-center">
                  <p class="mb-0">Copyright &copy; <?= date('Y') ?> - <?= htmlspecialchars($_SESSION['societe_nom'] ?? 'Votre Société') ?></p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
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
