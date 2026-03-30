<?php
    class Produits 
    {
        private object $pdo;
        private int $id;
        private string $nom;
        private string $description;
        private float $prix;
        private string $image;
        private int $stock;

        public function hydrate(array $data)
        {
            foreach ($data as $key => $value) {
                $this->id = filter_var($data['id'], FILTER_VALIDATE_INT);
                $this->nom = filter_var($data['nom'], FILTER_SANITIZE_STRING);
                $this->description = filter_var($data['description'], FILTER_SANITIZE_STRING);
                $this->prix = filter_var($data['prix'], FILTER_VALIDATE_FLOAT);
                $this->image = filter_var($data['image'], FILTER_SANITIZE_STRING);
                $this->stock = filter_var($data['stock'], FILTER_VALIDATE_INT);
            }
        }

        function __construct(object $pdo)
        {
            $this->pdo = $pdo;
            $this->id = 0;
            $this->nom = '';
            $this->description = '';
            $this->prix = 0;
            $this->image = '';
            $this->stock = 0;
        }

        public static function create()
        {
            try{
                $this->pdo->beginTransaction();
                $stmt = $this->pdo->prepare("INSERT INTO produits (nom, description, prix, image, stock) VALUES (:nom, :description, :prix, :image, :stock)");
                $stmt->bindValue(':nom', $this->nom);
                $stmt->bindValue(':description', $this->description);
                $stmt->bindValue(':prix', $this->prix);
                $stmt->bindValue(':image', $this->image);
                $stmt->bindValue(':stock', $this->stock);
                $stmt->execute();
                
                $this->pdo->commit();

                $_SESSION['mesgs']['success'][] = 'Produit ajouté avec succès';

            }catch (Exception $e){
                $this->pdo->rollBack();
                $_SESSION['mesgs']['errors'][] = $e->getMessage();
            }
        }

        public static function delete()
        {
            pass;
        }

        public static function update()
        {
            pass;
        }

        public static function find(){
            pass;
        }
        
        public static function fetch(){
            pass;
        }

        public static function fetchAll($db){
            try {
                $stmt = $db->query("SELECT * FROM produits");
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                error_log("Erreur lors de la récupération des produits: " . $e->getMessage());
                return [];
            }
        }
    }


