<?php
include_once __DIR__ . '/../database.php';

class Charge {
    public string $id;
    public string $charge_type;
    public float $amount;
    public string $charge_date;       // format 'YYYY-MM-DD'
    public string $date_versement;    // format 'YYYY-MM-DD'
    public string $reception_date;    // format 'YYYY-MM-DD'
    public string $payment_method;

    public function __construct(
        string $charge_type,
        float $amount,
        string $charge_date,
        string $date_versement,
        string $reception_date,
        string $payment_method
    ) {
        $this->id = $this->generateId();
        $this->charge_type = $charge_type;
        $this->amount = $amount;
        $this->charge_date = $charge_date;
        $this->date_versement = $date_versement;
        $this->reception_date = $reception_date;
        $this->payment_method = $payment_method;
    }

    private function generateId(): string {
        // Generate a random 8-char ID (alphanumeric)
        return 'CH' . str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    public function save(): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("INSERT INTO charge (id, charge_type, amount, charge_date, date_versement, reception_date, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $this->id,
                $this->charge_type,
                $this->amount,
                $this->charge_date,
                $this->date_versement,
                $this->reception_date,
                $this->payment_method
            ]);
        } catch (PDOException $e) {
            error_log("Error in Charge::save - " . $e->getMessage());
            return false;
        }
    }

    public static function delete(string $id): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM charge WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Charge::delete - " . $e->getMessage());
            return false;
        }
    }

    public static function update(
        string $id,
        string $charge_type,
        float $amount,
        string $charge_date,
        string $date_versement,
        string $reception_date,
        string $payment_method
    ): bool {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("UPDATE charge SET charge_type = ?, amount = ?, charge_date = ?, date_versement = ?, reception_date = ?, payment_method = ? WHERE id = ?");
            return $stmt->execute([
                $charge_type,
                $amount,
                $charge_date,
                $date_versement,
                $reception_date,
                $payment_method,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error in Charge::update - " . $e->getMessage());
            return false;
        }
    }

    public static function getAll(): array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->query("SELECT * FROM charge ORDER BY charge_date DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Charge::getAll - " . $e->getMessage());
            return [];
        }
        
    }

    public static function findById(string $id): ?array {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM charge WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error in Charge::findById - " . $e->getMessage());
            return null;
        }
    }
}
