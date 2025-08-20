<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['nom', 'prenom', 'email', 'telephone', 'username', 'password', 'role'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nom' => 'required|min_length[2]|max_length[50]',
        'prenom' => 'required|min_length[2]|max_length[50]',
        'email' => 'required|valid_email',
        'telephone' => 'required|min_length[10]|max_length[15]',
        'username' => 'required|min_length[3]|max_length[50]',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[admin,user]'
    ];

    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom est requis',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
            'max_length' => 'Le nom ne peut pas dépasser 50 caractères'
        ],
        'prenom' => [
            'required' => 'Le prénom est requis',
            'min_length' => 'Le prénom doit contenir au moins 2 caractères',
            'max_length' => 'Le prénom ne peut pas dépasser 50 caractères'
        ],
        'email' => [
            'required' => 'L\'email est requis',
            'valid_email' => 'L\'email doit être valide'
        ],
        'telephone' => [
            'required' => 'Le téléphone est requis',
            'min_length' => 'Le téléphone doit contenir au moins 10 caractères',
            'max_length' => 'Le téléphone ne peut pas dépasser 15 caractères'
        ],
        'username' => [
            'required' => 'Le nom d\'utilisateur est requis',
            'min_length' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères',
            'max_length' => 'Le nom d\'utilisateur ne peut pas dépasser 50 caractères'
        ],
        'password' => [
            'required' => 'Le mot de passe est requis',
            'min_length' => 'Le mot de passe doit contenir au moins 6 caractères'
        ],
        'role' => [
            'required' => 'Le rôle est requis',
            'in_list' => 'Le rôle doit être admin ou user'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        log_message('debug', '=== CALLBACK HASH PASSWORD ===');
        log_message('debug', 'Données reçues: ' . json_encode($data));

        if (isset($data['data']['password'])) {
            log_message('debug', 'Hachage du mot de passe');
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            log_message('debug', 'Mot de passe hashé: ' . $data['data']['password']);
        } else {
            log_message('debug', 'Aucun mot de passe à hasher');
        }

        log_message('debug', 'Données après hachage: ' . json_encode($data));
        return $data;
    }

    public function insert($data = null, bool $returnID = true)
    {
        log_message('debug', '=== MÉTHODE INSERT ===');
        log_message('debug', 'Données à insérer: ' . json_encode($data));
        log_message('debug', 'ReturnID: ' . ($returnID ? 'true' : 'false'));

        try {
            $result = parent::insert($data, $returnID);
            log_message('debug', 'Résultat de l\'insertion: ' . json_encode($result));
            
            if ($result) {
                log_message('debug', 'Insertion réussie');
                if ($returnID) {
                    $insertId = $this->getInsertID();
                    log_message('debug', 'ID inséré: ' . $insertId);
                }
            } else {
                log_message('error', 'Échec de l\'insertion');
                log_message('error', 'Erreurs du modèle: ' . json_encode($this->errors()));
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Exception lors de l\'insertion: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function update($id = null, $data = null): bool
    {
        log_message('debug', '=== MÉTHODE UPDATE ===');
        log_message('debug', 'ID: ' . $id);
        log_message('debug', 'Données à mettre à jour: ' . json_encode($data));

        try {
            $result = parent::update($id, $data);
            log_message('debug', 'Résultat de la mise à jour: ' . ($result ? 'true' : 'false'));
            
            if ($result) {
                log_message('debug', 'Mise à jour réussie');
            } else {
                log_message('error', 'Échec de la mise à jour');
                log_message('error', 'Erreurs du modèle: ' . json_encode($this->errors()));
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Exception lors de la mise à jour: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function delete($id = null, bool $purge = false)
    {
        log_message('debug', '=== MÉTHODE DELETE ===');
        log_message('debug', 'ID: ' . $id);
        log_message('debug', 'Purge: ' . ($purge ? 'true' : 'false'));

        try {
            $result = parent::delete($id, $purge);
            log_message('debug', 'Résultat de la suppression: ' . ($result ? 'true' : 'false'));
            
            if ($result) {
                log_message('debug', 'Suppression réussie');
            } else {
                log_message('error', 'Échec de la suppression');
                log_message('error', 'Erreurs du modèle: ' . json_encode($this->errors()));
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Exception lors de la suppression: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function findByUsername($username)
    {
        log_message('debug', '=== MÉTHODE FIND BY USERNAME ===');
        log_message('debug', 'Username recherché: ' . $username);

        try {
            $result = $this->where('username', $username)->first();
            log_message('debug', 'Résultat de la recherche: ' . json_encode($result));
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Exception lors de la recherche par username: ' . $e->getMessage());
            return null;
        }
    }

    public function findByEmail($email)
    {
        log_message('debug', '=== MÉTHODE FIND BY EMAIL ===');
        log_message('debug', 'Email recherché: ' . $email);

        try {
            $result = $this->where('email', $email)->first();
            log_message('debug', 'Résultat de la recherche: ' . json_encode($result));
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Exception lors de la recherche par email: ' . $e->getMessage());
            return null;
        }
    }

    public function verifyPassword($password, $hash)
    {
        log_message('debug', '=== MÉTHODE VERIFY PASSWORD ===');
        log_message('debug', 'Vérification du mot de passe');

        $result = password_verify($password, $hash);
        log_message('debug', 'Résultat de la vérification: ' . ($result ? 'true' : 'false'));
        
        return $result;
    }
}
