<?php
include_once __DIR__ . '/../database.php';

class PresenceModel {
    public string $id_presence;
    public string $id_emploi;
    public string $id_student;
    public string $status;
    public string $presence_date;

    public function __construct($id_emploi, $id_student, $status = 'Absent') {
        $this->id_presence = $this->generateId();
        $this->id_emploi = $id_emploi;
        $this->id_student = $id_student;
        $this->status = $status;
        $this->presence_date = date('Y-m-d');
    }

    private function generateId(): string {
        return 'PR' . str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function save(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                INSERT INTO presence (id_presence, id_emploi, id_student, status, presence_date)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $this->id_presence,
                $this->id_emploi,
                $this->id_student,
                $this->status,
                $this->presence_date
            ]);
        } catch (PDOException $e) {
            error_log("PresenceModel::save error - " . $e->getMessage());
            return false;
        }
    }

    public static function updateStatus($id_emploi, $id_student, $status): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("UPDATE presence SET status = ? WHERE id_emploi = ? AND id_student = ?");
            $stmt->execute([$status, $id_emploi, $id_student]);
            // Vérifie qu'au moins une ligne a été mise à jour
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("PresenceModel::updateStatus error - " . $e->getMessage());
            return false;
        }
    }
    

    public static function getByEmploi($id_emploi): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
            SELECT p.*, s.name AS student_name, s.username
            FROM presence p
            JOIN student s ON p.id_student = s.id
            WHERE p.id_emploi = ?
            
            ");
            $stmt->execute([$id_emploi]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PresenceModel::getByEmploi error - " . $e->getMessage());
            return [];
        }
    }

    public static function exists($id_emploi, $id_student): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM presence WHERE id_emploi = ? AND id_student = ?");
            $stmt->execute([$id_emploi, $id_student]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("PresenceModel::exists error - " . $e->getMessage());
            return false;
        }
    }

    public static function initPresenceForEmploi($id_emploi): void {
        try {
            $pdo = config::getConnexion();

            // 1. Get the class ID for this emploi
            $stmt = $pdo->prepare("SELECT id_classe FROM emploi WHERE id_emploi = ?");
            $stmt->execute([$id_emploi]);
            $id_classe = $stmt->fetchColumn();

            if (!$id_classe) return;

            // 2. Get all students for this class
            $stmt = $pdo->prepare("SELECT id FROM student WHERE class_id = ?");
            $stmt->execute([$id_classe]);
            $students = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // 3. For each student, check if presence exists, if not insert default Absent
            foreach ($students as $student_id) {
                $stmtExists = $pdo->prepare("SELECT COUNT(*) FROM presence WHERE id_emploi = ? AND id_student = ?");
                $stmtExists->execute([$id_emploi, $student_id]);
                $exists = $stmtExists->fetchColumn();

                if (!$exists) {
                    $presence = new PresenceModel($id_emploi, $student_id, 'Absent');
                    $presence->save();
                }
            }
        } catch (PDOException $e) {
            error_log("PresenceModel::initPresenceForEmploi error - " . $e->getMessage());
        }
    }
    public static function getPresenceHistoryByStudent($studentId): array {
        try {
            $pdo = config::getConnexion();
    
            $stmt = $pdo->prepare("
                SELECT 
                    p.presence_date, p.status,
                    e.date_debut AS start_time,
                    e.date_fin AS end_time,
                    m.nom AS subject_name
                FROM presence p
                JOIN emploi e ON p.id_emploi = e.id_emploi
                JOIN matiere m ON e.id_matiere = m.id_matiere
                WHERE p.id_student = ?
                ORDER BY p.presence_date DESC, e.date_debut DESC
            ");
            $stmt->execute([$studentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PresenceModel::getPresenceHistoryByStudent error - " . $e->getMessage());
            return [];
        }
    }
    
    
}
?>
