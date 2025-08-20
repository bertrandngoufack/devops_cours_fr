<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function test()
    {
        log_message('debug', '=== TEST CONTROLLER ===');
        log_message('debug', 'Méthode HTTP: ' . $this->request->getMethod());
        log_message('debug', 'Content-Type: ' . $this->request->getHeaderLine('Content-Type'));
        
        if ($this->request->getMethod() === 'post') {
            log_message('debug', 'Méthode POST détectée');
            
            // Récupérer les données de différentes manières
            $postData = $this->request->getPost();
            $rawInput = $this->request->getBody();
            $jsonData = $this->request->getJSON();
            $allData = $this->request->getVar();
            
            log_message('debug', 'Données POST (getPost): ' . json_encode($postData));
            log_message('debug', 'Données brutes (getBody): ' . $rawInput);
            log_message('debug', 'Données JSON (getJSON): ' . json_encode($jsonData));
            log_message('debug', 'Toutes les données (getVar): ' . json_encode($allData));
            
            // Essayer de parser manuellement
            if (empty($postData) && !empty($rawInput)) {
                parse_str($rawInput, $parsedData);
                log_message('debug', 'Données parsées manuellement: ' . json_encode($parsedData));
            }
            
            return $this->response->setJSON([
                'method' => $this->request->getMethod(),
                'content_type' => $this->request->getHeaderLine('Content-Type'),
                'post_data' => $postData,
                'raw_input' => $rawInput,
                'json_data' => $jsonData,
                'all_data' => $allData
            ]);
        }
        
        return $this->response->setJSON([
            'message' => 'GET request received',
            'method' => $this->request->getMethod()
        ]);
    }
}
