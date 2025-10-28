<?php
include_once __DIR__ . '/../database.php';

class Enseignant {
    public string $id_enseignant;
    public string $nom;
    public string $id_matiere;
    public string $email;
    public string $phone;

    public function __construct(string $nom, string $id_matiere, string $email, string $phone) {
        $this->id_enseignant = $this->generateId();
        $this->nom = $nom;
        $this->id_matiere = $id_matiere;
        $this->email = $email;
        $this->phone = $phone;
    }

    private function generateId(): string {
        return 'E' . str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    public function save(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                INSERT INTO enseignant (id_enseignant, nom, id_matiere, email, phone)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $this->id_enseignant,
                $this->nom,
                $this->id_matiere,
                $this->email,
                $this->phone
            ]);
        } catch (PDOException $e) {
            error_log("Error in Enseignant::save - " . $e->getMessage());
            return false;
        }
    }

    public static function delete(string $id): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM enseignant WHERE id_enseignant = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Enseignant::delete - " . $e->getMessage());
            return false;
        }
    }

    public static function update(string $id, string $nom, string $id_matiere, string $email, string $phone): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("
                UPDATE enseignant 
                SET nom = ?, id_matiere = ?, email = ?, phone = ?
                WHERE id_enseignant = ?
            ");
            return $stmt->execute([$nom, $id_matiere, $email, $phone, $id]);
        } catch (PDOException $e) {
            error_log("Error in Enseignant::update - " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->query("
                SELECT 
                    e.*, 
                    m.nom AS matiere_name,
                    DATE_FORMAT(e.joined_at, '%Y-%m-%d %H:%i:%s') AS formatted_joined_at
                FROM enseignant e
                LEFT JOIN matiere m ON e.id_matiere = m.id_matiere
                ORDER BY e.nom ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Enseignant::getAll - " . $e->getMessage());
            return [];
        }
        
    }
    
}
?>
