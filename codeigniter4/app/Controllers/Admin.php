<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    public function index()
    {
        log_message('debug', '=== DÉBUT MÉTHODE INDEX ===');
        
        $model = new UserModel();
        $data['users'] = $model->findAll();
        
        log_message('debug', 'Nombre d\'utilisateurs trouvés: ' . count($data['users']));
        
        return view('admin/users', $data);
    }

    public function create()
    {
        log_message('debug', '=== DÉBUT MÉTHODE CREATE ===');
        log_message('debug', 'Méthode HTTP: ' . $this->request->getMethod());
        log_message('debug', 'Content-Type: ' . $this->request->getHeaderLine('Content-Type'));
        
        // Comparer la méthode en minuscules
        if (strtolower($this->request->getMethod()) === 'post') {
            log_message('debug', 'Méthode POST détectée');
            
            // Récupérer les données POST de différentes manières
            $postData = $this->request->getPost();
            $rawInput = $this->request->getBody();
            
            log_message('debug', 'Données POST reçues (getPost): ' . json_encode($postData));
            log_message('debug', 'Données brutes reçues (getBody): ' . $rawInput);
            
            // Si les données POST sont vides, essayer de les parser manuellement
            if (empty($postData) && !empty($rawInput)) {
                log_message('debug', 'Données POST vides, tentative de parsing manuel');
                parse_str($rawInput, $parsedData);
                $postData = $parsedData;
                log_message('debug', 'Données parsées manuellement: ' . json_encode($postData));
            }
            
            // Si toujours vide, essayer avec $_POST
            if (empty($postData)) {
                log_message('debug', 'Tentative avec $_POST');
                $postData = $_POST;
                log_message('debug', 'Données $_POST: ' . json_encode($postData));
            }
            
            if (empty($postData)) {
                log_message('error', 'Aucune donnée POST reçue');
                $data['errors'] = ['general' => 'Aucune donnée reçue'];
                return view('admin/create_user', $data);
            }
            
            // Validation des données
            $rules = [
                'nom' => 'required|min_length[2]|max_length[50]',
                'prenom' => 'required|min_length[2]|max_length[50]',
                'email' => 'required|valid_email',
                'telephone' => 'required|min_length[10]|max_length[15]',
                'username' => 'required|min_length[3]|max_length[50]',
                'password' => 'required|min_length[6]',
                'role' => 'required|in_list[admin,user]'
            ];
            
            log_message('debug', 'Règles de validation définies');
            
            if (!$this->validate($rules)) {
                log_message('error', 'Erreurs de validation: ' . json_encode($this->validator->getErrors()));
                $data['errors'] = $this->validator->getErrors();
                $data['old'] = $postData;
                return view('admin/create_user', $data);
            }
            
            log_message('debug', 'Validation réussie');
            
            $model = new UserModel();
            
            // Préparation des données
            $data = [
                'nom' => $postData['nom'],
                'prenom' => $postData['prenom'],
                'email' => $postData['email'],
                'telephone' => $postData['telephone'],
                'username' => $postData['username'],
                'password' => $postData['password'],
                'role' => $postData['role']
            ];
            
            log_message('debug', 'Données préparées pour insertion: ' . json_encode($data));
            
            try {
                log_message('debug', 'Tentative d\'insertion en base de données');
                
                if ($model->insert($data)) {
                    $insertId = $model->getInsertID();
                    log_message('debug', 'Insertion réussie! ID: ' . $insertId);
                    
                    // Vérification immédiate en base
                    $insertedUser = $model->find($insertId);
                    if ($insertedUser) {
                        log_message('debug', 'Vérification en base réussie: ' . json_encode($insertedUser));
                    } else {
                        log_message('error', 'Vérification en base échouée - utilisateur non trouvé');
                    }
                    
                    // Rediriger vers la liste avec l'ID du nouvel utilisateur pour surbrillance
                    return redirect()->to('/admin/users?highlight=' . $insertId)->with('success', 'Utilisateur créé avec succès');
                } else {
                    log_message('error', 'Échec de l\'insertion: ' . json_encode($model->errors()));
                    $data['errors'] = $model->errors();
                    $data['old'] = $postData;
                    return view('admin/create_user', $data);
                }
            } catch (\Exception $e) {
                log_message('error', 'Exception lors de l\'insertion: ' . $e->getMessage());
                log_message('error', 'Trace: ' . $e->getTraceAsString());
                $data['errors'] = ['database' => 'Erreur de base de données: ' . $e->getMessage()];
                $data['old'] = $postData;
                return view('admin/create_user', $data);
            }
        }
        
        log_message('debug', 'Affichage du formulaire de création');
        return view('admin/create_user');
    }

    public function edit($id = null)
    {
        log_message('debug', '=== DÉBUT MÉTHODE EDIT === ID: ' . $id);
        log_message('debug', 'Méthode HTTP: ' . $this->request->getMethod());
        
        $model = new UserModel();
        
        // Comparer la méthode en minuscules
        if (strtolower($this->request->getMethod()) === 'post') {
            log_message('debug', 'Méthode POST détectée pour modification');
            
            $postData = $this->request->getPost();
            $rawInput = $this->request->getBody();
            
            log_message('debug', 'Données POST reçues (getPost): ' . json_encode($postData));
            log_message('debug', 'Données brutes reçues (getBody): ' . $rawInput);
            
            // Si les données POST sont vides, essayer de les parser manuellement
            if (empty($postData) && !empty($rawInput)) {
                log_message('debug', 'Données POST vides, tentative de parsing manuel');
                parse_str($rawInput, $parsedData);
                $postData = $parsedData;
                log_message('debug', 'Données parsées manuellement: ' . json_encode($postData));
            }
            
            // Si toujours vide, essayer avec $_POST
            if (empty($postData)) {
                log_message('debug', 'Tentative avec $_POST');
                $postData = $_POST;
                log_message('debug', 'Données $_POST: ' . json_encode($postData));
            }
            
            if (empty($postData)) {
                log_message('error', 'Aucune donnée POST reçue pour modification');
                $data['errors'] = ['general' => 'Aucune donnée reçue'];
                $data['user'] = $model->find($id);
                return view('admin/edit_user', $data);
            }
            
            // Validation des données
            $rules = [
                'nom' => 'required|min_length[2]|max_length[50]',
                'prenom' => 'required|min_length[2]|max_length[50]',
                'email' => 'required|valid_email',
                'telephone' => 'required|min_length[10]|max_length[15]',
                'username' => 'required|min_length[3]|max_length[50]',
                'role' => 'required|in_list[admin,user]'
            ];
            
            log_message('debug', 'Règles de validation définies pour modification');
            
            if (!$this->validate($rules)) {
                log_message('error', 'Erreurs de validation: ' . json_encode($this->validator->getErrors()));
                $data['errors'] = $this->validator->getErrors();
                $data['user'] = $model->find($id);
                $data['old'] = $postData;
                return view('admin/edit_user', $data);
            }
            
            log_message('debug', 'Validation réussie pour modification');
            
            // Préparation des données de mise à jour
            $updateData = [
                'nom' => $postData['nom'],
                'prenom' => $postData['prenom'],
                'email' => $postData['email'],
                'telephone' => $postData['telephone'],
                'username' => $postData['username'],
                'role' => $postData['role']
            ];
            
            // Ajouter le mot de passe seulement s'il est fourni
            if (!empty($postData['password'])) {
                $updateData['password'] = $postData['password'];
            }
            
            log_message('debug', 'Données de mise à jour: ' . json_encode($updateData));
            
            try {
                log_message('debug', 'Tentative de mise à jour en base de données');
                
                if ($model->update($id, $updateData)) {
                    log_message('debug', 'Mise à jour réussie!');
                    
                    // Vérification immédiate en base
                    $updatedUser = $model->find($id);
                    if ($updatedUser) {
                        log_message('debug', 'Vérification en base réussie: ' . json_encode($updatedUser));
                    } else {
                        log_message('error', 'Vérification en base échouée - utilisateur non trouvé');
                    }
                    
                    // Rediriger vers la liste avec l'ID de l'utilisateur modifié pour surbrillance
                    return redirect()->to('/admin/users?highlight=' . $id)->with('success', 'Utilisateur modifié avec succès');
                } else {
                    log_message('error', 'Échec de la mise à jour: ' . json_encode($model->errors()));
                    $data['errors'] = $model->errors();
                    $data['user'] = $model->find($id);
                    $data['old'] = $postData;
                    return view('admin/edit_user', $data);
                }
            } catch (\Exception $e) {
                log_message('error', 'Exception lors de la mise à jour: ' . $e->getMessage());
                log_message('error', 'Trace: ' . $e->getTraceAsString());
                $data['errors'] = ['database' => 'Erreur de base de données: ' . $e->getMessage()];
                $data['user'] = $model->find($id);
                $data['old'] = $postData;
                return view('admin/edit_user', $data);
            }
        }
        
        log_message('debug', 'Affichage du formulaire de modification');
        $data['user'] = $model->find($id);
        
        if (!$data['user']) {
            log_message('error', 'Utilisateur non trouvé pour modification: ' . $id);
            return redirect()->to('/admin/users')->with('error', 'Utilisateur non trouvé');
        }
        
        return view('admin/edit_user', $data);
    }

    public function delete($id = null)
    {
        log_message('debug', '=== DÉBUT MÉTHODE DELETE === ID: ' . $id);
        
        $model = new UserModel();
        
        // Vérifier si l'utilisateur existe
        $user = $model->find($id);
        if (!$user) {
            log_message('error', 'Utilisateur non trouvé pour suppression: ' . $id);
            return redirect()->to('/admin/users')->with('error', 'Utilisateur non trouvé');
        }
        
        log_message('debug', 'Utilisateur trouvé pour suppression: ' . json_encode($user));
        
        try {
            log_message('debug', 'Tentative de suppression en base de données');
            
            if ($model->delete($id)) {
                log_message('debug', 'Suppression réussie!');
                
                // Vérification immédiate en base
                $deletedUser = $model->find($id);
                if (!$deletedUser) {
                    log_message('debug', 'Vérification en base réussie - utilisateur supprimé');
                } else {
                    log_message('error', 'Vérification en base échouée - utilisateur toujours présent');
                }
                
                return redirect()->to('/admin/users')->with('success', 'Utilisateur supprimé avec succès');
            } else {
                log_message('error', 'Échec de la suppression');
                return redirect()->to('/admin/users')->with('error', 'Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception lors de la suppression: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return redirect()->to('/admin/users')->with('error', 'Erreur de base de données: ' . $e->getMessage());
        }
    }
}
