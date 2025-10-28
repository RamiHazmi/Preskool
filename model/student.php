<?php
include_once __DIR__ . '/../database.php';

class Student {
    private $id;
    private $name;
    private $username;
    private $class_id;
    private $gender;
    private $date_of_birth;
    private $moyenne;
    private $picture;
    private $address;
    private $email;
    private $phone_number;
    private $status;
    private $password;
    private $methode_paiement;

    public function __construct(
        $name, 
        $username, 
        $class_id, 
        $gender, 
        $date_of_birth, 
        $moyenne, 
        $picture, 
        $address, 
        $email, 
        $phone_number, 
        $password,
        $status = 'Active',
        $methode_paiement = 'par mois'
    ) {
        $this->id = $this->generateId();
        $this->name = $name;
        $this->username = $username;
        $this->class_id = $class_id;
        $this->gender = $gender;
        $this->date_of_birth = $date_of_birth;
        $this->moyenne = $moyenne;
        $this->picture = $picture;
        $this->address = $address;
        $this->email = $email;
        $this->phone_number = $phone_number;
        $this->status = $status;
        $this->password = $password;
        $this->methode_paiement = $methode_paiement;
    }

    private function generateId(): string {
        return 'S' . str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    public function register() {
        $conn = config::getConnexion();
        $sql = "INSERT INTO student (
            id, name, username, class_id, gender, date_of_birth, moyenne, picture, address, email, phone_number, status, password, methode_paiement
        ) VALUES (
            :id, :name, :username, :class_id, :gender, :date_of_birth, :moyenne, :picture, :address, :email, :phone_number, :status, :password, :methode_paiement
        )";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':class_id', $this->class_id);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':date_of_birth', $this->date_of_birth);
            $stmt->bindParam(':moyenne', $this->moyenne);
            $stmt->bindParam(':picture', $this->picture);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':phone_number', $this->phone_number);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':methode_paiement', $this->methode_paiement);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Student register error: " . $e->getMessage());
            return false;
        }
    }

    public static function getAllStudents() {
        $conn = config::getConnexion();
        $stmt = $conn->prepare("
            SELECT 
                student.*, 
                student.joined_at,
                class.class_name,
                class.level_id,
                level.name AS level_name,
                level.number_of_seances
            FROM student 
            LEFT JOIN class ON student.class_id = class.id
            LEFT JOIN level ON class.level_id = level.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteStudent($studentId) {
        $conn = config::getConnexion();
    
        try {
            $conn->beginTransaction();
    
            $sqlPresence = "DELETE FROM presence WHERE id_student = :id";
            $stmtPresence = $conn->prepare($sqlPresence);
            $stmtPresence->bindParam(':id', $studentId, PDO::PARAM_STR);
            $stmtPresence->execute();
    
            $sqlPaiment = "DELETE FROM paiement WHERE id_student = :id";
            $stmtPaiment = $conn->prepare($sqlPaiment);
            $stmtPaiment->bindParam(':id', $studentId, PDO::PARAM_STR);
            $stmtPaiment->execute();
    
            $sqlStudent = "DELETE FROM student WHERE id = :id";
            $stmtStudent = $conn->prepare($sqlStudent);
            $stmtStudent->bindParam(':id', $studentId, PDO::PARAM_STR);
            $stmtStudent->execute();
    
            $conn->commit();
    
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            error_log('Erreur lors de la suppression : ' . $e->getMessage());
            return false;
        }
    }
    

    public function updateStudent() {
        $conn = config::getConnexion();
        $sql = "UPDATE student SET 
                    name = :name,
                    username = :username,
                    class_id = :class_id,
                    gender = :gender,
                    date_of_birth = :date_of_birth,
                    moyenne = :moyenne,
                    picture = :picture,
                    address = :address,
                    email = :email,
                    phone_number = :phone_number,
                    status = :status,
                    password = :password,
                    methode_paiement = :methode_paiement
                WHERE id = :id";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':class_id', $this->class_id);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':date_of_birth', $this->date_of_birth);
            $stmt->bindParam(':moyenne', $this->moyenne);
            $stmt->bindParam(':picture', $this->picture);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':phone_number', $this->phone_number);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':methode_paiement', $this->methode_paiement);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Student update error: " . $e->getMessage());
            return false;
        }
    }

    public static function getStudentById($id) {
        $conn = config::getConnexion();
        $stmt = $conn->prepare("SELECT * FROM student WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getStudentsByClass($classId): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM student WHERE class_id = :class_id");
            $stmt->bindParam(':class_id', $classId, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("StudentModel::getStudentsByClass error - " . $e->getMessage());
            return [];
        }
    }
}
?>
