<?php
require_once __DIR__ . '/session_config.php';
include_once __DIR__ . '/../database.php';
include_once __DIR__ . '/../model/Admin.php';
require_once __DIR__ . '/..//vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$controller = new ControllerAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($data['action'])) {
        switch ($data['action']) {
            case 'deleteAdmin':
                echo $controller->deleteAdmin($data);
                break;
            case 'updateAdmin':
                echo $controller->updateAdmin($data);
                break;
                case 'changepasswordadmin':
                    echo $controller->changepasswordadmin($data);
                    break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Action JSON inconnue.']);
        }
        exit;
    }

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                echo $controller->login();
                break;
            case 'register':
                echo $controller->register();
                break;
            case 'updateAdmin':
                echo $controller->updateAdmin($_POST);
                break;
            case 'sendResetCode':
                echo $controller->sendResetCode($_POST['email'] ?? '');
                break;
                case 'changepasswordadmin':
                    echo $controller->changepasswordadmin($data);
                    break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Action POST inconnue.']);
        }
        exit;
    }
    

    echo json_encode(['status' => 'error', 'message' => 'Aucune action détectée.']);
}


class ControllerAdmin {

    public function login() {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$email || !$password) {
            return "<p style='color:red;'>Email et mot de passe sont requis.</p>";
        }

        $conn = config::getConnexion();
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = :email AND provider = 'manual'");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = [
                'id' => $admin['id'],
                'first_name' => $admin['first_name'],
                'last_name' => $admin['last_name'],
                'email' => $admin['email'],
                'image' => $admin['image'],
                'provider' => $admin['provider']
            ];

            return "<p style='color:green;'>Connexion réussie.</p>";
        } else {
            return "<p style='color:red;'>Identifiants invalides.</p>";
        }
    }

    public function register() {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $phone = $_POST['phone'] ?? null;
        $address = $_POST['address'] ?? null;
        $country = $_POST['country'] ?? null;
        $state = $_POST['state'] ?? null;
        $city = $_POST['city'] ?? null;
        $postal_code = $_POST['postal_code'] ?? null;
        $provider = 'manual';
        $imagePath = null;

        $errors = [];
        if (!$first_name || !$last_name || !$email || !$password) {
            $errors[] = "Champs obligatoires manquants.";
        }

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../view/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $imageName = uniqid("admin_", true) . "." . strtolower($extension);
            $targetPath = $uploadDir . $imageName;
            $relativePath = 'view/images/' . $imageName;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                $imagePath = $relativePath;
            } else {
                $errors[] = "Erreur lors de l'upload de l'image.";
            }
        }

        if ($errors) {
            return "<p style='color:red;'>" . implode("<br>", $errors) . "</p>";
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $admin = new Admin(
            $first_name,
            $last_name,
            $email,
            $phone,
            $address,
            $country,
            $state,
            $city,
            $postal_code,
            $hashedPassword,
            $imagePath,
            $provider
        );

        if ($admin->register()) {
            return "<p style='color:green;'>Admin enregistré avec succès.</p>";
        } else {
            return "<p style='color:red;'>Erreur lors de l'enregistrement.</p>";
        }
    }

    // Now updateAdmin accepts array $data (POST or JSON)
    public function updateAdmin($data) {
        $id = $data['id'] ?? null;
        if (!$id) return json_encode(['status' => 'error', 'message' => 'ID manquant.']);
    
        $adminData = Admin::getAdminById($id);
        if (!$adminData) return json_encode(['status' => 'error', 'message' => 'Admin non trouvé.']);
    
        $errors = [];
        $existingImage = $adminData['image'];
    
        // Use submitted values or fallback to current values
        $first_name = trim($data['first_name'] ?? $adminData['first_name']);
        $last_name = trim($data['last_name'] ?? $adminData['last_name']);
        $email = trim($data['email'] ?? $adminData['email']);
        $phone = $data['phone'] ?? $adminData['phone'];
        $address = $data['address'] ?? $adminData['address'];
        $country = $data['country'] ?? $adminData['country'];
        $state = $data['state'] ?? $adminData['state'];
        $city = $data['city'] ?? $adminData['city'];
        $postal_code = $data['postal_code'] ?? $adminData['postal_code'];
        $image = $existingImage; // start with current image path
    
        // === Password update with validation ===
        if (!empty($data['current_password']) || !empty($data['new_password']) || !empty($data['confirm_password'])) {

            // Validate current password using password_verify
            if (empty($data['current_password']) || !password_verify($data['current_password'], $adminData['password'])) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Mot de passe actuel incorrect.',
                    'input_password' => $data['current_password'] ?? '',
                    'db_password' => $adminData['password'] ?? ''
                ]);
            }
        
            // Validate new password
            if (empty($data['new_password'])) {
                return json_encode(['status' => 'error', 'message' => 'Le nouveau mot de passe ne peut pas être vide.']);
            }
        
            if ($data['new_password'] !== $data['confirm_password']) {
                return json_encode(['status' => 'error', 'message' => 'Les nouveaux mots de passe ne correspondent pas.']);
            }
        
            // Hash the new password before storing
            $password = password_hash($data['new_password'], PASSWORD_DEFAULT);
        
        } else {
            // Keep the existing hashed password if no change
            $password = $adminData['password'];
        }
        
        
        
    
        // Handle picture upload if $_FILES available
        if (!empty($_FILES["picture"]["name"])) {
            $targetDir = __DIR__ . '/view/images/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    
            $imageName = uniqid() . "_" . basename($_FILES["picture"]["name"]);
            $targetFile = $targetDir . $imageName;
            $imageUrl = 'view/images/' . $imageName; // Relative path for DB & frontend
    
            if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
                // Delete old image file (if exists and not default)
                if (!empty($existingImage) && $existingImage !== 'default.png') {
                    $oldImagePath = __DIR__ . '/../' . $existingImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                // Set new image path for DB update and session
                $image = $imageUrl;
            } else {
                $errors[] = "Erreur lors de l'upload de l'image.";
            }
        }
    
        if (count($errors)) {
            return json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
        }
    
        $updatedAdmin = new Admin(
            $first_name,
            $last_name,
            $email,
            $phone,
            $address,
            $country,
            $state,
            $city,
            $postal_code,
            $password,
            $image,
            $adminData['provider'],
            $adminData['provider_id'] ?? null
        );
    
        $success = $updatedAdmin->updateAdmin($id);
    
        if ($success) {
            // Update session info if current user
            if (isset($_SESSION['admin']) && $_SESSION['admin']['id'] == $id) {
                $_SESSION['admin']['first_name'] = $first_name;
                $_SESSION['admin']['last_name'] = $last_name;
                $_SESSION['admin']['email'] = $email;
                $_SESSION['admin']['image'] = $image;
                $_SESSION['admin']['phone'] = $phone;
                $_SESSION['admin']['address'] = $address;
                $_SESSION['admin']['postal_code'] = $postal_code;
                $_SESSION['admin']['password'] = $password;
                $_SESSION['admin']['state'] = $state;
                $_SESSION['admin']['country'] = $country;
                $_SESSION['admin']['city'] = $city;
            }
            return json_encode(['status' => 'success', 'message' => 'Admin mis à jour.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour.']);
        }
    }
    
    
    

    public function deleteAdmin($data) {
        if (!isset($data['id'])) {
            return json_encode(['status' => 'error', 'message' => 'ID requis.']);
        }

        $id = trim($data['id']);
        $deleted = Admin::deleteAdmin($id);

        if ($deleted && isset($_SESSION['admin']) && $_SESSION['admin']['id'] == $id) {
            unset($_SESSION['admin']);
        }

        return json_encode([
            'status' => $deleted ? 'success' : 'error',
            'message' => $deleted ? 'Admin supprimé.' : 'Erreur lors de la suppression.'
        ]);
    }
    public function sendResetCode($email) {
        $conn = config::getConnexion();
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$admin) {
            return json_encode(['status' => 'error', 'message' => 'Email introuvable.']);
        }
    
        $username = $admin['first_name'] . ' ' . $admin['last_name'];
        $code = rand(100000, 999999); // 6-digit code
    
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_email'] = $email;
    
        $mail = new PHPMailer(true);
    
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '5ramihazmi@gmail.com';
            $mail->Password = 'cnxa hgaw pxat uyai'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('5ramihazmi@gmail.com', $_SESSION['societe_nom'] ?? 'Nom Société');
            $mail->addAddress($email, $username);
            $mail->isHTML(true);
            $mail->Subject = "🔐 Code de réinitialisation - " . ($_SESSION['societe_nom'] ?? 'Votre Centre');
    
            // Logo embedding logic
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
            $relativeLogoPath = $_SESSION['societe_logo'] ?? 'view/backoffice/assets/img/preskool.png';
            if (!str_starts_with($relativeLogoPath, 'stage/')) {
                $relativeLogoPath = 'stage/' . $relativeLogoPath;
            }
            $absoluteLogoPath = realpath($documentRoot . '/' . $relativeLogoPath);
            if (!$absoluteLogoPath || !file_exists($absoluteLogoPath)) {
                $absoluteLogoPath = realpath($documentRoot . '/stage/view/backoffice/assets/img/preskool.png');
            }
    
            $mail->addEmbeddedImage($absoluteLogoPath, "schoollogo", basename($absoluteLogoPath), "base64", "image/png");
    
            // Email body
            $mail->Body = "
            <html>
            <head><style>
                body { font-family: Arial; background: #f8f8f8; padding: 20px; color: #333; }
                .container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ddd; max-width: 600px; margin: auto; }
                .code { font-size: 24px; font-weight: bold; color: #2c3e50; background: #eef; padding: 10px; text-align: center; margin: 20px 0; border-radius: 6px; }
            </style></head>
            <body>
            <div class='container'>
                <div style='text-align: center;'><img src='cid:schoollogo' alt='Logo' height='60'></div>
                <h2>Réinitialisation du mot de passe</h2>
                <p>Bonjour <strong>$username</strong>,</p>
                <p>Voici votre code de réinitialisation :</p>
                <div class='code'>$code</div>
                <p>Entrez ce code dans le formulaire pour réinitialiser votre mot de passe.</p>
                <p>Merci,<br>L'équipe de " . ($_SESSION['societe_nom'] ?? 'Votre Centre') . "</p>
            </div>
            </body>
            </html>
            ";
    
            $mail->send();
            return json_encode(['status' => 'success', 'message' => 'Code envoyé avec succès à votre email.']);
        } catch (Exception $e) {
            return json_encode(['status' => 'error', 'message' => "Erreur d'envoi: " . $mail->ErrorInfo]);
        }
    }
    public function changepasswordadmin($data) {
        $conn = config::getConnexion();
    
        $id = $data['id'] ?? null;
        $email = $data['email'] ?? null;
        $new_password = $data['new_password'] ?? '';
        $confirm_password = $data['confirm_password'] ?? '';
    
        if (!$id || !$email) {
            return json_encode(['status' => 'error', 'message' => 'ID ou email manquant.']);
        }
    
        if (empty($new_password) || empty($confirm_password)) {
            return json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs de mot de passe.']);
        }
    
        if ($new_password !== $confirm_password) {
            return json_encode(['status' => 'error', 'message' => 'Les mots de passe ne correspondent pas.']);
        }
    
        // Optional: enforce password policy here (length, complexity...)
    
        // Hash the new password
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
    
        // Verify that the user with given id and email exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM admin WHERE id = :id AND email = :email");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $exists = $stmt->fetchColumn();
    
        if (!$exists) {
            return json_encode(['status' => 'error', 'message' => 'Utilisateur non trouvé.']);
        }
    
        // Update password
        $stmt = $conn->prepare("UPDATE admin SET password = :password WHERE id = :id AND email = :email");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':email', $email);
    
        if ($stmt->execute()) {
            return json_encode(['status' => 'success', 'message' => 'Mot de passe mis à jour avec succès.']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Échec de la mise à jour du mot de passe.']);
        }
    }
    
    
    
}
