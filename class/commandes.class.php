<?php

class Commandes
{
    private object $pdo;
    private int $id;
    private string $heure;
    protected int $num_borne;

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $this->id = filter_var($data['id'] ?? 0, FILTER_VALIDATE_INT);
            $this->heure = htmlspecialchars($data['heure'] ?? '');
            $this->num_borne = filter_var($data['num_borne'] ?? 0, FILTER_VALIDATE_INT);
        }
    }
    
    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
        $this->id = 0;
        $this->heure = '';
        $this->num_borne = 0;
    }

    public function create()
    {
        try{
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO commandes (heure, num_borne) VALUES (NOW(), :num_borne)");
            $stmt->bindValue(':num_borne', $this->num_borne);
            $stmt->execute();
            $this->id = (int)$this->pdo->lastInsertId();

            $this->pdo->commit();

            $_SESSION['mesgs']['success'][] = 'Commande ajoutée avec succès';
            return $this->id > 0;

        }catch (Exception $e){
            $this->pdo->rollBack();
            $_SESSION['mesgs']['errors'][] = $e->getMessage();
            return false;
        }
    }

    public function getId(): int
{
    return $this->id;
}

    public function addProduit($id_produit, $quantite)
    {
        try{
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO produit_commander (id_commande, id_produit, quantite) VALUES (:id_commande, :id_produit, :quantite)");
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
            $stmt = $this->pdo->prepare("SELECT c.*, p.nom, p.prix, pc.quantite
             FROM commandes c
             LEFT JOIN produit_commander pc ON c.id = pc.id_commande
             LEFT JOIN produits p ON pc.id_produit = p.id
             WHERE c.id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return $stmt->fetch();
        }catch (Exception $e){
            $_SESSION['mesgs']['errors'][] = $e->getMessage();
        }
    }

    /**
     * Récupère les numéros de bornes déjà utilisés dans des commandes actives
     * @return array Liste des num_borne utilisés
     */
    public function getBornesUtilisees()
    {
        try{
            $stmt = $this->pdo->prepare("SELECT DISTINCT num_borne FROM commandes WHERE num_borne IS NOT NULL AND num_borne != ''");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return array_map('intval', $result);
        }catch (Exception $e){
            $_SESSION['mesgs']['errors'][] = $e->getMessage();
            return [];
        }
    }

    /**
     * Vérifie si une borne est disponible (non utilisée)
     * @param int $num_borne Numéro de borne à vérifier
     * @return bool True si disponible, false sinon
     */
    public function isBorneDisponible($num_borne)
    {
        try{
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM commandes WHERE num_borne = :num_borne");
            $stmt->bindValue(':num_borne', $num_borne);
            $stmt->execute();
            return $stmt->fetchColumn() == 0;
        }catch (Exception $e){
            $_SESSION['mesgs']['errors'][] = $e->getMessage();
            return false;
        }
    }

}