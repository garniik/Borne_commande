<?php

class Commandes
{
    private object $pdo;
    private int $id;
    private int $phone;
    private string $heure;
    private int $num_borne;

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $this->id = filter_var($data['id'], FILTER_VALIDATE_INT);
            $this->phone = filter_var($data['phone'], FILTER_VALIDATE_INT);
            $this->date = filter_var($data['date'], FILTER_SANITIZE_STRING);
            $this->num_borne = filter_var($data['num_borne'], FILTER_VALIDATE_INT);
        }
    }
    
    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
        $this->id = 0;
        $this->phone = 0;
        $this->heure = '';
        $this->num_borne = 0;
    }

    public function create()
    {
        try{
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO commandes (phone, heure, num_borne) VALUES (:phone, NOW(), :num_borne)");
            $stmt->bindValue(':phone', $this->phone);
            $stmt->bindValue(':num_borne', $this->num_borne);
            $stmt->execute();
            $this->id = (int)$this->pdo->lastInsertId();

            $this->pdo->commit();

            $_SESSION['mesgs']['success'][] = 'Commande ajoutée avec succès';

        }catch (Exception $e){
            $this->pdo->rollBack();
            $_SESSION['mesgs']['errors'][] = $e->getMessage();
        }
    }

    public function addProduit($id_produit, $quantite)
    {
        try{
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO commandes_produits (id_commande, id_produit, quantite) VALUES (:id_commande, :id_produit, :quantite)");
            $stmt->bindValue(':id_commande', $this->id);
            $stmt->bindValue(':id_produit', $id_produit);
            $stmt->bindValue(':quantite', $quantite);
            $stmt->execute();

            $this->pdo->commit();

            $_SESSION['mesgs']['success'][] = 'Produit ajouté à la commande avec succès';

        }catch (Exception $e){
            $this->pdo->rollBack();
            $_SESSION['mesgs']['errors'][] = $e->getMessage();
        }
    }

    public function delete()
    {
        try{
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("DELETE FROM commandes WHERE id = :id");
            $stmt->bindValue(':id', $this->id);
            $stmt->execute();

            $this->pdo->commit();

            $_SESSION['mesgs']['success'][] = 'Commande supprimée avec succès';

        }catch (Exception $e){
            $this->pdo->rollBack();
            $_SESSION['mesgs']['errors'][] = $e->getMessage();
        }
    }

    public function findAll()
    {
        try{
            $stmt = $this->pdo->prepare("SELECT * FROM commandes");
            $stmt->execute();
            return $stmt->fetchAll();
        }catch (Exception $e){
            $_SESSION['mesgs']['errors'][] = $e->getMessage();
        }
    }

    public function findById($id)
    {
        try{
            $stmt = $this->pdo->prepare("SELECT c.*, p.nom, p.prix, cp.quantite
             FROM commandes c
             LEFT JOIN commande_produit cp ON c.id = cp.id_commande
             LEFT JOIN produits p ON cp.id_produit = p.id
             WHERE c.id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        }catch (Exception $e){
            $_SESSION['mesgs']['errors'][] = $e->getMessage();
        }
    }
    
}