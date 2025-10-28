<?php
require_once __DIR__ . '/session_config.php';  // adjust path accordingly

include_once __DIR__ . '/../database.php';  
include(__DIR__ . '/../model/student.php');
require_once __DIR__ . '/..//vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$controller = new ControllerStudent();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle JSON payloads
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'addStudent':
                echo $controller->addStudent();
                break;
            case 'updateStudent':
                echo $controller->updateStudent();
                break;
            case 'deleteStudent':
                echo $controller->deleteStudent($data);  // Pass the data
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Action JSON inconnue.']);
        }
        exit;
    }

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'addStudent':
                echo $controller->addStudent();
                break;
            case 'updateStudent':
                echo $controller->updateStudent();
                break;
            case 'deleteStudent':
                echo $controller->deleteStudent($_POST);
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Action POST inconnue.']);
        }
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Aucune action trouvée.']);
}

class ControllerStudent {
    public function addStudent() {
        $conn = config::getConnexion();
        $errors = [];
    
        // Sanitize and validate inputs
        $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : null;
        $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : null;
        $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
        $date_of_birth = isset($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : null;
        $moyenne = isset($_POST['moyenne']) ? filter_var($_POST['moyenne'], FILTER_VALIDATE_FLOAT) : null;
        $class_id = isset($_POST['class_id']) ? trim($_POST['class_id']) : null;
        
        // Added method paiement
        $methode_paiement = isset($_POST['methode_paiement']) ? $_POST['methode_paiement'] : 'par mois';
    
        // Optional fields
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
    
        // Basic validation
        if (!$name) $errors[] = "Le nom est obligatoire.";
        if (!$username) $errors[] = "Le nom d'utilisateur est obligatoire.";
        if (!$gender) $errors[] = "Le genre est obligatoire.";
        if (!$date_of_birth) $errors[] = "La date de naissance est obligatoire.";
        if ($moyenne === false || $moyenne < 0 || $moyenne > 20) $errors[] = "La moyenne doit être un nombre entre 0 et 20.";
        if (!$class_id) $errors[] = "La classe est obligatoire.";
        if ($email) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM student WHERE email = ?");
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                $errors[] = "Cet email est déjà utilisé.";
            }
        }
    
        // Handle picture upload
        $imageUrl = "";
        if (isset($_FILES["picture"]) && $_FILES["picture"]["error"] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/view/images/";
            $relativePath = "view/images/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    
            $extension = pathinfo($_FILES["picture"]["name"], PATHINFO_EXTENSION);
            $imageName = uniqid("student_", true) . "." . strtolower($extension);
            $targetPath = $uploadDir . $imageName;
            $relativeImagePath = $relativePath . $imageName;
    
            if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetPath)) {
                $imageUrl = $relativeImagePath;
            } else {
                $errors[] = "Erreur lors de l'upload de l'image.";
            }
        }
    
        // Generate avatar if no picture
        if (empty($imageUrl)) {
            $initial = strtoupper(substr($username, 0, 1));
            $color = isset($_POST['avatar_color']) && in_array($_POST['avatar_color'], ['#1abc9c', '#3498db', '#e74c3c', '#9b59b6', '#e67e22', '#2ecc71']) ? $_POST['avatar_color'] : '#1abc9c';
    
            $canvasSize = 300;
            $avatarImg = imagecreatetruecolor($canvasSize, $canvasSize);
            $bg = sscanf($color, "#%02x%02x%02x");
            $bgColor = imagecolorallocate($avatarImg, $bg[0], $bg[1], $bg[2]);
            imagefill($avatarImg, 0, 0, $bgColor);
    
            $textColor = imagecolorallocate($avatarImg, 255, 255, 255);
            $fontPath = __DIR__ . '/../view/backoffice/assets/fonts/ARIAL.ttf';
            if (!file_exists($fontPath)) die("Font file not found at $fontPath");
    
            $fontSize = 180;
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $initial);
            $x = ($canvasSize - ($bbox[2] - $bbox[0])) / 2;
            $y = $canvasSize / 2 + abs($bbox[5]) / 2;
            imagettftext($avatarImg, $fontSize, 0, $x, $y, $textColor, $fontPath, $initial);
    
            $imageName = uniqid("avatar_", true) . ".png";
            $uploadDir = __DIR__ . "/view/images/";
            $relativePath = "view/images/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    
            $targetPath = $uploadDir . $imageName;
            $relativeImagePath = $relativePath . $imageName;
            imagepng($avatarImg, $targetPath);
            imagedestroy($avatarImg);
    
            $imageUrl = $relativeImagePath;
        }
    
        // Generate random password
        $password = bin2hex(random_bytes(4));
    
        if (empty($errors)) {
            $student = new Student(
                $name, $username, $class_id, $gender, $date_of_birth, $moyenne,
                $imageUrl, $address, $email, $phone, $password, 'Active', $methode_paiement
            );
    
            if ($student->register()) {
                $updateStmt = $conn->prepare("UPDATE class SET number_of_students = number_of_students + 1 WHERE id = ?");
$updateStmt->execute([$class_id]);
                // Fetch class name for welcome message
                $className = '';
                if ($class_id) {
                    $stmt = $conn->prepare("SELECT class_name FROM class WHERE id = ?");
                    $stmt->execute([$class_id]);
                    $className = $stmt->fetchColumn();
                }
    
                // Send welcome email
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
                    $mail->Subject = "🎓 Welcome to " . ($_SESSION['societe_nom'] ?? 'Votre Centre') . " - Your Learning Journey Starts Here!";
                
                    // Get absolute path for logo from session
                    $documentRoot = $_SERVER['DOCUMENT_ROOT']; // e.g. C:/xampp/htdocs
                    $relativeLogoPath = $_SESSION['societe_logo'] ?? 'view/backoffice/assets/img/preskool.png';
                
                    // If session path does not start with 'stage/', add it (adjust as needed)
                    if (!str_starts_with($relativeLogoPath, 'stage/')) {
                        $relativeLogoPath = 'stage/' . $relativeLogoPath;
                    }
                
                    // Build absolute path
                    $absoluteLogoPath = realpath($documentRoot . '/' . $relativeLogoPath);
                
                    // Fallback if file missing
                    if (!$absoluteLogoPath || !file_exists($absoluteLogoPath)) {
                        $absoluteLogoPath = realpath($documentRoot . '/stage/view/backoffice/assets/img/preskool.png');
                    }
                
                    // Embed image using absolute path
                    $mail->addEmbeddedImage($absoluteLogoPath, "schoollogo", basename($absoluteLogoPath), "base64", "image/png");
                
                    // Email body with embedded image cid
                    $mail->Body = "
                    <html>
                    <head>
                      <style>
                        body {
                          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                          background-color: #f2f4f8;
                          margin: 0;
                          padding: 30px;
                          color: #333;
                        }
                        .container {
                          background-color: #ffffff;
                          max-width: 600px;
                          margin: 0 auto;
                          padding: 30px;
                          border-radius: 12px;
                          box-shadow: 0 0 20px rgba(0,0,0,0.08);
                        }
                        .header {
                          text-align: center;
                          margin-bottom: 25px;
                        }
                        .header img {
                          height: 80px;
                          margin-bottom: 10px;
                        }
                        .header h2 {
                          color: #2c3e50;
                          margin-top: 0;
                        }
                        .credentials {
                          background-color: #e8f4ff;
                          border-left: 5px solid #3498db;
                          padding: 20px;
                          font-size: 16px;
                          margin: 20px 0;
                          border-radius: 6px;
                        }
                        .credentials strong {
                          display: inline-block;
                          width: 100px;
                        }
                        .footer {
                          font-size: 13px;
                          color: #888;
                          text-align: center;
                          margin-top: 30px;
                        }
                        .footer a {
                          color: #3498db;
                          text-decoration: none;
                        }
                      </style>
                    </head>
                    <body>
                      <div class='container'>
                        <div class='header'>
                          <img src='cid:schoollogo' alt='preSkool Logo'>
                          <h2>Welcome to " . ($_SESSION['societe_nom'] ?? 'Votre Centre') . "!</h2>
                          </div>
                        <p>Hi <strong>$username</strong>,</p>
                        <p>We're thrilled to have you as part of our student community. Your account has been created successfully! 🎉</p>
                        
                        <div class='credentials'>
                          <strong>Email:</strong> $email<br>
                          <strong>Password:</strong> $password
                        </div>
                
                        <div class='credentials'>
                          🎓 <strong>Welcome to the class:</strong> $className
                        </div>
                
                        <p>You can now log in and start your learning journey with us.</p>
                        <p>If you need help, feel free to reach out at any time.</p>
                
                        <div class='footer'>
                        &copy; " . date('Y') . " " . ($_SESSION['societe_nom'] ?? 'Votre Centre') . ". All rights reserved.
                        <a href='https://" . strtolower(preg_replace("/\s+/", "", $_SESSION['societe_nom'] ?? 'societe')) . ".com'>Visit our website</a>
                        </div>
                      </div>
                    </body>
                    </html>
                    ";
                
                    $mail->send();
                    return "<p style='color:green;'>Étudiant ajouté avec succès !</p>";
                } catch (Exception $e) {
                    return "<p style='color:green;'>Étudiant ajouté avec succès ! Échec de l'envoi de l'email. Mot de passe généré : $password</p>";
                }
                
            } else {
                return "<p style='color:red;'>Erreur lors de l'ajout de l'étudiant.</p>";
            }
        } else {
            return "<p style='color:red;'>" . implode("<br>", $errors) . "</p>";
        }
    }
    
    public function deleteStudent($data) {
        header('Content-Type: application/json');
    
        if (!$data) {
            echo json_encode(['status' => 'error', 'message' => 'Données JSON invalides ou manquantes.']);
            exit;
        }
    
        if (!isset($data['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant.']);
            exit;
        }
    
        $studentId = htmlspecialchars(trim($data['id']));
    
        $student = new Student("", "", "", "", "", "", "", "", "", "", "", "");
    
        $deleted = $student->deleteStudent($studentId);
    
        if ($deleted) {
            $conn = config::getConnexion();
            $sql = "
                UPDATE class c
                SET c.number_of_students = (
                    SELECT COUNT(*) FROM student s
                    WHERE s.class_id = c.id
                )
            ";
            $conn->exec($sql);
            echo json_encode(['status' => 'success', 'message' => 'Étudiant supprimé avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Échec de la suppression.']);
        }
        exit;
    }
    
    
    public function updateStudent() {
        header('Content-Type: application/json; charset=UTF-8');
    
        $conn = config::getConnexion();
        $errors = [];
    
        try {
          
    
            // Sanitize inputs
            $id = $_POST['id'] ?? null;
            $name = htmlspecialchars(trim($_POST['name'] ?? ''));
            $username = htmlspecialchars(trim($_POST['username'] ?? ''));
            $gender = $_POST['gender'] ?? null;
            $date_of_birth = trim($_POST['date_of_birth'] ?? '');
            $moyenne = filter_var($_POST['moyenne'] ?? null, FILTER_VALIDATE_FLOAT);
            $class_id = trim($_POST['class_id'] ?? '');
            $status = $_POST['status'] ?? '';
            
            // Added methode_paiement on update
            $methode_paiement = $_POST['methode_paiement'] ?? 'par mois';
    
            $address = htmlspecialchars(trim($_POST['address'] ?? ''));
            $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $phone_number = htmlspecialchars(trim($_POST['phone_number'] ?? ''));
    
            // Validate inputs
            if (!$id) $errors[] = "L'ID est requis.";
            if (!$name) $errors[] = "Le nom est requis.";
            if (!$username) $errors[] = "Le nom d'utilisateur est requis.";
            if (!in_array($gender, ['Male', 'Female'])) $errors[] = "Genre invalide.";
            if ($moyenne === false || $moyenne < 0 || $moyenne > 20) $errors[] = "Moyenne invalide.";
            if (!$class_id) $errors[] = "Classe invalide.";
            if (!in_array($status, ['Active', 'Graduated', 'Suspended'])) $errors[] = "Statut invalide.";
    
            if ($email === false) {
                $errors[] = "Email invalide.";
            }
    
            if ($phone_number !== '' && !preg_match('/^\+?[0-9\s\-]{7,15}$/', $phone_number)) {
                $errors[] = "Numéro de téléphone invalide.";
            }
    
            // Fetch existing picture from DB
            $stmtImg = $conn->prepare("SELECT picture FROM student WHERE id = ?");
            $stmtImg->execute([$id]);
            $existingImage = $stmtImg->fetchColumn();
            $imageUrl = $existingImage;
    
            // Handle picture upload
            if (!empty($_FILES["picture"]["name"])) {
                $targetDir = "view/images/";
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    
                $imageName = uniqid() . "_" . basename($_FILES["picture"]["name"]);
                $targetFile = $targetDir . $imageName;
    
                if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
                    // Delete old image
                    if (!empty($existingImage) && file_exists($existingImage)) {
                        unlink($existingImage);
                    }
                    $imageUrl = $targetFile;
                } else {
                    $errors[] = "Erreur lors de l'upload de l'image.";
                }
            }
    
            if (!empty($errors)) {
                echo json_encode(["status" => "error", "message" => implode(" ", $errors)]);
                exit;
            }
    
            // Update student without modifying password
            $stmt = $conn->prepare("UPDATE student SET name = ?, username = ?, gender = ?, date_of_birth = ?, moyenne = ?, picture = ?, class_id = ?, status = ?, address = ?, email = ?, phone_number = ?, methode_paiement = ? WHERE id = ?");
            $stmt->execute([$name, $username, $gender, $date_of_birth, $moyenne, $imageUrl, $class_id, $status, $address, $email, $phone_number, $methode_paiement, $id]);
    
            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "Étudiant modifié avec succès."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Aucune modification effectuée ou étudiant introuvable."]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Erreur DB : " . $e->getMessage()]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Erreur : " . $e->getMessage()]);
        }
    }
    
    public function listStudents() {
        return Student::getAllStudents();
    }
    public function getStudentsByClass($classId): array {
        return Student::getStudentsByClass($classId);
    }
}
?>
