<?php

require_once __DIR__ . '/../../utils/config/config.php';
class ApiCalls{
    private const LOG_FILE = 'ApiCalls';
    public static function apiCall($method, $url, $params, $requestHeader) {
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $log->info("Inicio de chamada em API", ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'method' => $method, 'url' => $url]);
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            if ($requestHeader){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);
                if($requestHeader[0] == 'Content-Type: application/json')
                    $params = json_encode($params);
                else
                    $params = http_build_query($params);
            }

            switch($method){
                case 'GET':
                    break;
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    break;
                case 'PUT':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    break;
                default:
                    $log->error("Tipo de requisição não suportada", ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'method' => $method, 'url' => $url]);
                    throw new Exception('Método não suportado: ' . $method);
            }
    
            $response = curl_exec($ch);
    
            if (curl_errno($ch)) {
                $log->error("Erro ao fazer exceção", ['user' => $_SESSION['id_usuario'], 'response' => $response, 'page' => $_SERVER['REQUEST_URI'], 'method' => $method, 'url' => $url, 'error' => curl_error($ch)]);
                throw new Exception('Erro cURL: ' . curl_error($ch) . ', URL: ' . $url . ', Method: ' . $method);
            }
    
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode >= 400) {
                $log->error("Requisição não pôde ser finalizada", ['user' => $_SESSION['id_usuario'], 'response' => $response, 'page' => $_SERVER['REQUEST_URI'], 'method' => $method, 'url' => $url, 'httpCode' => $httpCode]);
                throw new Exception('Erro HTTP: ' . $httpCode . ', URL: ' . $url . ', Method: ' . $method);
            }
    
            curl_close($ch);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                $log->error("JSON inválido na resposta da requisição", ['user' => $_SESSION['id_usuario'], 'response' => $response, 'page' => $_SERVER['REQUEST_URI'], 'method' => $method, 'url' => $url, 'json' => json_last_error_msg()]);
                throw new Exception('JSON inválido na resposta da requisição: ' . json_last_error_msg());
            }
    
            return $response;
        } catch (Exception $e) {
            $log->error("Exceção encontrado ao fazer chamada em API", ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'method' => $method, 'url' => $url, 'error' => $e->getMessage(), 'line' => $e->getLine()]);
            throw new Exception('Error in apiCall: ' . $e->getMessage() . 'Line:' . $e->getLine());
        }
    }
}
