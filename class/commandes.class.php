<?php

class Commandes
{
    private object $pdo;
    private int $id;
    private int $phone;
    private string $date;

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $this->id = filter_var($data['id'], FILTER_VALIDATE_INT);
            $this->phone = filter_var($data['phone'], FILTER_VALIDATE_INT);
            $this->date = filter_var($data['date'], FILTER_SANITIZE_STRING);
        }
    }
    
    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
        $this->id = 0;
        $this->phone = 0;
        $this->date = '';
    }
    
}