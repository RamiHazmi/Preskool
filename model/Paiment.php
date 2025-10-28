<?php
include_once __DIR__ . '/../database.php';

class Paiment {
    public int $id;
    public string $id_student;
    public string $id_level;
    public float $montant;
    public string $methode; // ENUM value
    public string $date_paiement;
    public ?string $date_versement;

    public function __construct($id_student, $id_level, $montant, $methode, $date_paiement, $date_versement = null) {
        $this->id_student = $id_student;
        $this->id_level = $id_level;
        $this->montant = $montant;
        $this->methode = $methode;
        $this->date_paiement = $date_paiement;
        $this->date_versement = $date_versement;
    }

    public function save(): bool {
        try {
            $pdo = config::getConnexion();
    
            $pdo->beginTransaction();
    
            $stmt = $pdo->prepare("
                INSERT INTO paiement (id_student, id_level, montant, methode, date_paiement, date_versement)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
    
            $result = $stmt->execute([
                $this->id_student,
                $this->id_level,
                $this->montant,
                $this->methode,
                $this->date_paiement,
                $this->date_versement
            ]);
    
            if ($result) {
                // Update student's etat to 'Payé'
                $updateStudent = $pdo->prepare("UPDATE student SET etat = 'Payé' WHERE id = ?");
                $updateStudent->execute([$this->id_student]);
            }
    
            $pdo->commit();
    
            return $result;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error in Paiment::save - " . $e->getMessage());
            return false;
        }
    }
    

    public static function delete($id): bool {
        try {
            $pdo = config::getConnexion();
    
            // First, find the student ID linked to this payment
            $stmt = $pdo->prepare("SELECT id_student FROM paiement WHERE id = ?");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$student) {
                return false;
            }
    
            $studentId = $student['id_student'];
    
            $stmt = $pdo->prepare("DELETE FROM paiement WHERE id = ?");
            $result = $stmt->execute([$id]);
    
            if ($result) {
                $updateStudent = $pdo->prepare("UPDATE student SET etat = 'Non Payé' WHERE id = ?");
                $updateStudent->execute([$studentId]);
            }
    
            return $result;
        } catch (PDOException $e) {
            error_log("Error in Paiment::delete - " . $e->getMessage());
            return false;
        }
    }
    

    public static function getAll(): array {
        try {
            $conn = config::getConnexion();
            $stmt = $conn->prepare("
                SELECT 
                    s.id AS student_id,
                    s.name, 
                    s.username, 
                    s.methode_paiement,
                    s.joined_at,
                    s.etat,
                    MAX(p.date_paiement) AS last_payment_date,
                    COUNT(p.id) AS total_paiements,
                    COALESCE(SUM(p.montant), 0) AS total_montant
                FROM student s
                LEFT JOIN paiement p ON p.id_student = s.id
                GROUP BY s.id, s.name, s.username, s.methode_paiement, s.joined_at, s.etat
                ORDER BY s.joined_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Paiment::getAll - " . $e->getMessage());
            return [];
        }
    }
    
    public static function getByStudent($id_student): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM paiement WHERE id_student = ?");
            $stmt->execute([$id_student]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Paiment::getByStudent - " . $e->getMessage());
            return [];
        }
    }

    public static function update($id, $montant, $methode, $date_paiement, $date_versement = null): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                UPDATE paiement 
                SET montant = ?, methode = ?, date_paiement = ?, date_versement = ?
                WHERE id = ?
            ");
            return $stmt->execute([$montant, $methode, $date_paiement, $date_versement, $id]);
        } catch (PDOException $e) {
            error_log("Error in Paiment::update - " . $e->getMessage());
            return false;
        }
    }
    public static function getPaiementsByStudent($studentId) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                SELECT 
                    p.*, 
                    c.class_name
                FROM paiement p
                INNER JOIN student s ON s.id = p.id_student
                LEFT JOIN class c ON s.class_id = c.id
                WHERE p.id_student = :id_student
                ORDER BY p.date_paiement DESC
            ");
            $stmt->bindParam(':id_student', $studentId, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getPaiementsByStudent: " . $e->getMessage());
            return [];
        }
    }
    
    
  
    
}
