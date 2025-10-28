<?php
require_once __DIR__ . '/../../controller/session_config.php';  

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
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    unset($_SESSION['admin']);
    header('Location: login.php');
    exit;
}
include_once __DIR__ . '/../../database.php';

// --- CONFIGURATION OAuth ---

// Google OAuth
$google_client_id = '964101948383-ou475p8dpsjhcbfdinj208hemmfi655i.apps.googleusercontent.com';
$google_client_secret = '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';
$google_redirect_uri = 'http://localhost/stage/view/backoffice/login.php?provider=google';

// Facebook OAuth
$fb_client_id = '1263525385145438';
$fb_client_secret = '1c4d0be486900d4d327ab10e88a9c7d1';
$fb_redirect_uri = 'http://localhost/stage/view/backoffice/login.php?provider=facebook';

// Helper PDO connection
function getPDO() {
    return config::getConnexion();
}

// --- Gestion OAuth Google ---
if (isset($_GET['provider']) && $_GET['provider'] === 'google' && isset($_GET['code'])) {
    $code = $_GET['code'];

    $token_url = "https://oauth2.googleapis.com/token";
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query([
                'code' => $code,
                'client_id' => $google_client_id,
                'client_secret' => $google_client_secret,
                'redirect_uri' => $google_redirect_uri,
                'grant_type' => 'authorization_code'
            ]),
        ],
    ];
    $context = stream_context_create($options);
    $token_response = file_get_contents($token_url, false, $context);

    if ($token_response === false) {
        echo "<script>alert('Failed to get access token from Google');</script>";
        exit;
    }

    $token_data = json_decode($token_response, true);
    $access_token = $token_data['access_token'] ?? null;

    if ($access_token) {
        $user_info_url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . urlencode($access_token);
        $user_info_response = file_get_contents($user_info_url);

        if ($user_info_response === false) {
            echo "<script>alert('Failed to get user info from Google');</script>";
            exit;
        }

        $user_info = json_decode($user_info_response, true);

        $email = $user_info['email'] ?? '';

        try {
            $pdo = getPDO();
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['admin'] = $user;  // <-- Store whole user info
                header("Location: dashboard.php");
                exit;
            } else {
                echo "<script>alert('User not found'); window.location.href='login.php';</script>";
                exit;
            }
        } catch (PDOException $e) {
            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Failed to get access token from Google.');</script>";
        exit;
    }
}

// --- Gestion OAuth Facebook ---
elseif (isset($_GET['provider']) && $_GET['provider'] === 'facebook' && isset($_GET['code'])) {
    $code = $_GET['code'];

    $token_url = "https://graph.facebook.com/v15.0/oauth/access_token?" . http_build_query([
        'client_id' => $fb_client_id,
        'redirect_uri' => $fb_redirect_uri,
        'client_secret' => $fb_client_secret,
        'code' => $code
    ]);

    $response = file_get_contents($token_url);
    if ($response === false) {
        echo "<script>alert('Failed to get access token from Facebook');</script>";
        exit;
    }

    $token_data = json_decode($response, true);
    $access_token = $token_data['access_token'] ?? null;

    if ($access_token) {
        $user_info_url = "https://graph.facebook.com/me?fields=id,name,email&access_token=$access_token";
        $user_info_response = file_get_contents($user_info_url);
        if ($user_info_response === false) {
            echo "<script>alert('Failed to get user info from Facebook');</script>";
            exit;
        }

        $user_info = json_decode($user_info_response, true);

        $email = $user_info['email'] ?? '';

        try {
            $pdo = getPDO();
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['admin'] = $user;  // <-- Store whole user info
                header("Location: dashboard.php");
                exit;
            } else {
                echo "<script>alert('User not found in database'); window.location.href='login.php';</script>";
                exit;
            }
        } catch (PDOException $e) {
            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Failed to get access token from Facebook.');</script>";
        exit;
    }
}

// --- Gestion login manuel ---
// --- Gestion login manuel ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $pdo = getPDO();

        $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo 'Invalid email or password';
            exit;
        }

        // IMPORTANT: Use password_verify to check hashed password
        if (password_verify($password, $user['password'])) {
            // Save entire user record to session
            $_SESSION['admin'] = $user;

            // Optionally, you can still create shortcuts:
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'];

            echo 'redirect:dashboard.php';
            exit;
        } else {
            echo 'Invalid email or password';
            exit;
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
        exit;
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
	<meta name="description" content="Preskool - Bootstrap Admin Template" />
	<meta name="keywords" content="admin, estimates, bootstrap, business, html5, responsive, Projects" />
	<meta name="author" content="Dreams technologies - Bootstrap Admin Template" />
	<meta name="robots" content="noindex, nofollow" />
	<title>Preskool Admin Template</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png" />

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css" />

	<!-- Feather CSS -->
	<link rel="stylesheet" href="assets/plugins/icons/feather/feather.css" />

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css" />

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css" />

	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/style.css" />
</head>

<body class="account-page">

	<!-- Main Wrapper -->
	<div class="main-wrapper">
		<div class="container-fuild">
			<div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
				<div class="row">
				<div class="col-lg-6">
						<div class="d-lg-flex align-items-center justify-content-center bg-light-300 d-lg-block d-none flex-wrap vh-100 overflowy-auto bg-01">
							<div>
								<img src="assets/img/authentication/authentication-06.svg" alt="Img">
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-12 col-sm-12">
						<div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
							<div class="col-md-8 mx-auto p-4">
								<form id="loginForm" action="login.php" method="POST">
									<div>
										<div class="mx-auto mb-5 text-center">
											<img src="<?= htmlspecialchars('/stage/' . $_SESSION['societe_logo']) ?>" class="img-fluid" alt="Logo" />
										</div>
										<div class="card">
											<div class="card-body p-4">
												<div class="mb-4">
													<h2 class="mb-2">Welcome</h2>
													<p class="mb-0">Please enter your details to sign in</p>
												</div>
												<div class="mt-4">
													<div class="d-flex align-items-center justify-content-center flex-wrap">
                                                <div class="text-center me-2 flex-fill">
    <a href="https://www.facebook.com/v15.0/dialog/oauth?client_id=<?= $fb_client_id ?>&redirect_uri=<?= urlencode($fb_redirect_uri) ?>&state=secureRandomState&scope=email,public_profile"
       class="bg-primary br-10 p-2 btn btn-primary d-flex align-items-center justify-content-center">
        <img class="img-fluid m-1" src="assets/img/icons/facebook-logo.svg" alt="Facebook" />
    </a>
</div>

<!-- BOUTON GOOGLE -->
<div class="text-center me-2 flex-fill">
    <a href="https://accounts.google.com/o/oauth2/v2/auth?client_id=<?= $google_client_id ?>&redirect_uri=<?= urlencode($google_redirect_uri) ?>&response_type=code&scope=email%20profile&access_type=offline&prompt=select_account"
       class="br-10 p-2 btn btn-outline-light d-flex align-items-center justify-content-center">
        <img class="img-fluid m-1" src="assets/img/icons/google-logo.svg" alt="Google" />
    </a>
</div>

													
													</div>
												</div>
												<div class="login-or">
													<span class="span-or">Or</span>
												</div>
												<div class="mb-3">
													<label class="form-label">Email Address</label>
													<div class="input-icon mb-3 position-relative">
														<span class="input-icon-addon">
															<i class="ti ti-mail"></i>
														</span>
														<input type="text" name="email" value="" class="form-control" required />
													</div>
													<label class="form-label">Password</label>
													<div class="pass-group">
														<input type="password" name="password" class="pass-input form-control" required />
														<span class="ti toggle-password ti-eye-off"></span>
													</div>
												</div>
												<div class="form-wrap form-wrap-checkbox mb-3">
													<div class="d-flex align-items-center">
														<div class="form-check form-check-md mb-0">
															<input class="form-check-input mt-0" type="checkbox" />
														</div>
														<p class="ms-1 mb-0">Remember Me</p>
													</div>
													<div class="text-end">
														<a href="forgotpassword.php" class="link-danger">Forgot Password?</a>
													</div>
												</div>
												<div class="mb-3">
													<button type="submit" class="btn btn-primary w-100">Sign In</button>
												</div>
												<div class="text-center">
													<h6 class="fw-normal text-dark mb-0">
														Don’t have an account? <a href="register.html" class="hover-a">Create Account</a>
													</h6>
												</div>
											</div>
										</div>
										<div class="mt-5 text-center">
											<p class="mb-0">Copyright &copy; 2024 - Preskool</p>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.getElementById('loginForm').addEventListener('submit', function(e) {
			e.preventDefault();

			const formData = new FormData(this);
			console.log('Submitting login form with:', {
				email: formData.get('email'),
				password: formData.get('password')
			});

			fetch('login.php', {
				method: 'POST',
				body: formData
			})
			.then(res => res.text())
			.then(data => {
				console.log('Server response:', data);

				if (data.includes('redirect:dashboard.php')) {
					console.log('Login successful, redirecting...');
					window.location.href = 'dashboard.php';
				} else if (data.includes('Invalid email or password')) {
					alert('Invalid email or password');
				}
			})
			.catch(err => console.error('Fetch error:', err));
		});
	</script>

	<!-- jQuery -->
	<script src="assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>

	<!-- Feather Icon JS -->
	<script src="assets/js/feather.min.js"></script>

	<!-- Slimscroll JS -->
	<script src="assets/js/jquery.slimscroll.min.js"></script>

	<!-- Select2 JS -->
	<script src="assets/plugins/select2/js/select2.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">

</body>
</html>