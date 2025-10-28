<?php
include_once __DIR__ . '/../database.php';

class Classroom {
    public string $id;
    public string $nom;

    public function __construct(string $nom) {
        $this->id = $this->generateId();
        $this->nom = $nom;
    }

    private function generateId(): string {
        // Generates an ID like C000001, C000002, etc.
        return 'CR' . str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    public function save(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("INSERT INTO classroom (id, nom) VALUES (?, ?)");
            return $stmt->execute([$this->id, $this->nom]);
        } catch (PDOException $e) {
            error_log("Error in Classroom::save - " . $e->getMessage());
            return false;
        }
    }

    public static function delete(string $id): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM classroom WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Classroom::delete - " . $e->getMessage());
            return false;
        }
    }

    public static function update(string $id, string $nom): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("UPDATE classroom SET nom = ? WHERE id = ?");
            return $stmt->execute([$nom, $id]);
        } catch (PDOException $e) {
            error_log("Error in Classroom::update - " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->query("SELECT * FROM classroom ORDER BY id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Classroom::getAll - " . $e->getMessage());
            return [];
        }
    }
}
?>
