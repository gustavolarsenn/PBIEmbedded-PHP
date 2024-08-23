<?php

require_once __DIR__ . '\\..\\..\\config.php';

require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ApiCalls{
    private const LOG_FILE = 'ApiCalls';
    private const LOG = 'api';
    private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';
    public static function apiCall($method, $url, $params, $requestHeader) {
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
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
                    throw new Exception('Unsupported method: ' . $method);
            }
    
            $response = curl_exec($ch);
    
            if (curl_errno($ch)) {
                throw new Exception('cURL error: ' . curl_error($ch) . ', URL: ' . $url . ', Method: ' . $method);
            }
    
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode >= 400) {
                throw new Exception('HTTP error: ' . $httpCode . ', URL: ' . $url . ', Method: ' . $method);
            }
    
            curl_close($ch);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON in response: ' . json_last_error_msg());
            }
    
            return $response;
        } catch (Exception $e) {
            throw new Exception('Error in apiCall: ' . $e->getMessage() . 'Line:' . $e->getLine());
        }
    }
}
