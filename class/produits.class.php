<?php
    class Produits 
    {
        private object $pdo;
        private int $id;
        private string $nom;
        private string $categorie;
        private string $description;
        private float $prix;
        private string $image;
        private int $stock;

        public function hydrate(array $data)
        {
            $this->id = filter_var($data['id'] ?? 0, FILTER_VALIDATE_INT);
            $this->nom = trim($data['nom'] ?? '');
            $this->categorie = trim($data['categorie'] ?? '');
            $this->description = trim($data['description'] ?? '');
            $this->prix = filter_var($data['prix'] ?? 0, FILTER_VALIDATE_FLOAT);
            $this->image = trim($data['image'] ?? '');
            $this->stock = filter_var($data['stock'] ?? 0, FILTER_VALIDATE_INT);
        }

        function __construct(object $pdo)
        {
            $this->pdo = $pdo;
            $this->id = 0;
            $this->nom = '';
            $this->categorie = '';
            $this->description = '';
            $this->prix = 0;
            $this->image = '';
            $this->stock = 0;
        }

        public function create()
        {
            try{
                $this->pdo->beginTransaction();
                $stmt = $this->pdo->prepare("INSERT INTO produits (nom, categorie, description, prix, image, stock) VALUES (:nom, :categorie, :description, :prix, :image, :stock)");
                $stmt->bindValue(':nom', $this->nom);
                $stmt->bindValue(':categorie', $this->categorie);
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

        public function delete()
        {
            try{
                $this->pdo->exec("DELETE FROM produits WHERE id = " . $this->id);
            }
            catch (Exception $e){
                $_SESSION['mesgs']['errors'][] = $e->getMessage();
            }
        }

        public static function find($db,$data){
            try{
                $id = $data['id']??'';
                $nom = $data ['nom']??'';
                $categorie = $data['categorie']??'';
                $prix = $data ['prix']??'';
                
                $sql = "SELECT * FROM produits WHERE 1 ";

                if($id){
                    $sql .= " AND id = :id";
                }
                if($nom){
                    $sql .= " AND nom like :nom";
                }
                if($categorie){
                    $sql .= " AND categorie = :categorie";
                }
                if($prix){
                    $sql .= " AND prix = :prix";
                }

                $stm = $db->prepare($sql);

                if($id){
                    $stm->bindValue(':id', $data['id']);
                }
                if($nom){
                    $stm->bindValue(':nom', $data['nom']);
                }
                if($categorie){
                    $stm->bindValue(':categorie', $data['categorie']);
                }
                if($prix){
                    $stm->bindValue(':prix', $data['prix']);
                }

                $stm->execute();
                return $stm->fetchAll(PDO::FETCH_ASSOC);

            }catch(Exception $e){
                $_SESSION['mesgs']['errors'][] = $e->getMessage();
                return [];
            }
        }
        
        public static function findById(PDO $pdo, int $id): ?array {
            $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        }

        public function addStock(int $quantite): bool {
            if ($this->id === 0) return false;
            $stmt = $this->pdo->prepare("UPDATE produits SET stock = stock + :quantite WHERE id = :id");
            return $stmt->execute([':quantite' => $quantite, ':id' => $this->id]);
        }

        public function setStock(int $quantite): bool {
            if ($this->id === 0) return false;
            $stmt = $this->pdo->prepare("UPDATE produits SET stock = :quantite WHERE id = :id");
            return $stmt->execute([':quantite' => $quantite, ':id' => $this->id]);
        }

        public function update(): bool {
            if ($this->id === 0) return false;
            try {
                $stmt = $this->pdo->prepare("UPDATE produits SET nom = :nom, categorie = :categorie, description = :description, prix = :prix, stock = :stock WHERE id = :id");
                return $stmt->execute([
                    ':nom' => $this->nom,
                    ':categorie' => $this->categorie,
                    ':description' => $this->description,
                    ':prix' => $this->prix,
                    ':stock' => $this->stock,
                    ':id' => $this->id
                ]);
            } catch (Exception $e) {
                $_SESSION['mesgs']['errors'][] = $e->getMessage();
                return false;
            }
        }

        public static function fetchAll($db){
            try {
                $requet = "SELECT * FROM produits";
                $stmt = $db->prepare($requet);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erreur lors de la récupération des produits: " . $e->getMessage());
                return [];
            }
        }
    }


