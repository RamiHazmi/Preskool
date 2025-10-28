<?php
include_once __DIR__ . '/../database.php';

class Societe {
    private $id;
    private $nom_centre;
    private $matricule_fiscale;
    private $adresse;
    private $numero_telephone;
    private $image_path;

    // Constructor accepts optional $id (for updates)
    public function __construct($nom_centre, $matricule_fiscale, $adresse, $numero_telephone, $image_path, $id = null) {
        if ($id === null) {
            $this->id = $this->generateId();
        } else {
            $this->id = $id;
        }
        $this->nom_centre = $nom_centre;
        $this->matricule_fiscale = $matricule_fiscale;
        $this->adresse = $adresse;
        $this->numero_telephone = $numero_telephone;
        $this->image_path = $image_path;
    }

    // Generates a new unique ID
    private function generateId(): string {
        return 'SC' . str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    // Insert a new societe in DB
    public function register() {
        $conn = config::getConnexion();
        $sql = "INSERT INTO societe (
            id_societe, nom_centre, matricule_fiscale, adresse, numero_telephone, image_path
        ) VALUES (
            :id, :nom_centre, :matricule_fiscale, :adresse, :numero_telephone, :image_path
        )";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':nom_centre', $this->nom_centre);
            $stmt->bindParam(':matricule_fiscale', $this->matricule_fiscale);
            $stmt->bindParam(':adresse', $this->adresse);
            $stmt->bindParam(':numero_telephone', $this->numero_telephone);
            $stmt->bindParam(':image_path', $this->image_path);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Societe register error: " . $e->getMessage());
            return false;
        }
    }

    // Fetch all companies
    public static function getAll() {
        $conn = config::getConnexion();
        $stmt = $conn->prepare("SELECT * FROM societe");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch one company by ID
    public static function getById($id) {
        $conn = config::getConnexion();
        $stmt = $conn->prepare("SELECT * FROM societe WHERE id_societe = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update existing company
    public function update() {
        $conn = config::getConnexion();
        $sql = "UPDATE societe SET 
                    nom_centre = :nom_centre,
                    matricule_fiscale = :matricule_fiscale,
                    adresse = :adresse,
                    numero_telephone = :numero_telephone,
                    image_path = :image_path
                WHERE id_societe = :id";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':nom_centre', $this->nom_centre);
            $stmt->bindParam(':matricule_fiscale', $this->matricule_fiscale);
            $stmt->bindParam(':adresse', $this->adresse);
            $stmt->bindParam(':numero_telephone', $this->numero_telephone);
            $stmt->bindParam(':image_path', $this->image_path);
            $result = $stmt->execute();
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Update error: " . print_r($errorInfo, true));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Societe update error: " . $e->getMessage());
            return false;
        }
    }
    

    // Delete company by ID
    public static function delete($id) {
        $conn = config::getConnexion();
        try {
            $stmt = $conn->prepare("DELETE FROM societe WHERE id_societe = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Societe delete error: " . $e->getMessage());
            return false;
        }
    }

    // Getter for ID
    public function getId() {
        return $this->id;
    }
}
?>
