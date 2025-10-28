<?php
require_once __DIR__ . '/../../controller/session_config.php';
include_once __DIR__ . '/../../database.php';

if (!($_SESSION['code_verified'] ?? false)) {
    header('Location: verify-code.php');
    exit;
}

// Get email from session and trim whitespace
$adminEmail = trim($_SESSION['reset_email'] ?? '');

if (!$adminEmail) {
    // If email is missing in session, redirect or show error
    header('Location: forgot-password.php');
    exit;
}
$conn = config::getConnexion();

$stmt = $conn->prepare("SELECT id FROM admin WHERE email = :email LIMIT 1");
$stmt->bindParam(':email', $adminEmail);

if ($stmt->execute()) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $adminId = $row['id'] ?? null;
} else {
    echo "Query failed";
}

if (!$adminId) {
    // If no admin found for email, redirect or show error
    header('Location: forgot-password.php');
    exit;
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
	<title>Reset Password</title>

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
          <div class="d-lg-flex align-items-center justify-content-center bg-light-300 d-lg-block d-none flex-wrap vh-100 bg-01">
            <img src="assets/img/authentication/authentication-09.svg" alt="Reset Image">
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
          <div class="row justify-content-center align-items-center vh-100 overflow-auto">
            <div class="col-md-8 mx-auto p-4">
              <form id="changePasswordForm" novalidate>
              <input type="hidden" name="id" value="<?= htmlspecialchars($adminId) ?>">
<input type="hidden" name="email" value="<?= htmlspecialchars($adminEmail) ?>">

                <div class="mx-auto mb-5 text-center">
                  <img src="<?= htmlspecialchars('/stage/' . ($_SESSION['societe_logo'] ?? 'assets/img/logo.png')) ?>" class="img-fluid" alt="Logo">
                </div>

                <div class="card">
                  <div class="card-body p-4">

                    <div class="mb-4">
                      <h2>Reset Your Password</h2>
                      <p>Choose a new password to continue.</p>
                    </div>
                    <span class="ti toggle-password ti-eye-off"></span>
                    <div class="mb-3">
                      <label class="form-label">New Password</label>
                      <input type="password" name="new_password" class=" pass-input form-control" required>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Confirm Password</label>
                      <input type="password" name="confirm_password" class=" pass-input form-control" required>
                    </div>

                    <div class="mb-3">
                      <button type="submit" class="btn btn-primary w-100">Change Password</button>
                    </div>

                    <div id="resetPasswordMessage" class="text-center mt-2 text-danger"></div>

                    <div class="text-center mt-3">
                      <h6 class="fw-normal text-dark mb-0">
                        Return to <a href="login.php" class="hover-a">Login</a>
                      </h6>
                    </div>
                  </div>
                </div>

                <div class="mt-5 text-center">
                  <p class="mb-0">&copy; <?= date('Y') ?> - <?= htmlspecialchars($_SESSION['societe_nom'] ?? 'Votre Société') ?></p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- JS Includes -->
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
<script>
  const postJSON = async (url, data) => {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    return res.json();
  };

  document.getElementById('changePasswordForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = e.target;

    const adminId = form.id.value;       // hidden input with admin id
    const adminEmail = form.email.value; // hidden input with admin email

    const data = {
      action: 'changepasswordadmin',
      id: adminId,
      email: adminEmail,
      new_password: form.new_password.value,
      confirm_password: form.confirm_password.value
    };

    // Console log email and id
    console.log("Admin Email:", adminEmail);
    console.log("Admin ID:", adminId);
    console.log("Password update data:", data);

    try {
      const res = await postJSON('../../controller/adminController.php', data);
      console.log("Response:", res);

      const msgElem = document.getElementById('resetPasswordMessage');
      msgElem.textContent = res.message;
      msgElem.style.setProperty('color', res.status === 'success' ? 'green' : 'red', 'important');

      if (res.status === 'success') {
        setTimeout(() => {
          window.location.href = 'login.php';
        }, 1500);
      }
    } catch (err) {
      alert('Erreur lors de la mise à jour du mot de passe.');
      console.error(err);
    }
  });
</script>

<link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">


</body>
</html>
