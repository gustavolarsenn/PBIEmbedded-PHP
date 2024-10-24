<?php

require_once __DIR__ . '/config.php';
require_once CAMINHO_BASE . '/models/API/ApiCalls.php';

class RastreioIP{
    public static function pegarIPUsuario()
    {
        $log = AppLogger::getInstance('RastreioIP');
        try {
            
            if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            }
            
            $client  = $_SERVER['HTTP_CLIENT_IP'] ?? null;
            $forward = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
            $remote  = $_SERVER['REMOTE_ADDR'] ?? null;
            
            if(filter_var($client, FILTER_VALIDATE_IP))
            {
                $ip = $client;
            }
            elseif(filter_var($forward, FILTER_VALIDATE_IP))
            {
                $ip = $forward;
            }
            else
            {
                $ip = $remote;
            }
            $log->info('IP coletado pelo PHP', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'HTTP_CLIENT_IP' => $_SERVER['HTTP_CLIENT_IP'] ?? null, 'HTTP_X_FORWARDED_FOR' => @$_SERVER['HTTP_X_FORWARDED_FOR'] ?? null, 'REMOTE_ADDR' => @$_SERVER['REMOTE_ADDR'] ?? null, 'HTTP_CF_CONNECTING_IP' => @$_SERVER['HTTP_CF_CONNECTING_IP'] ?? null]);
            
            return $ip;
        } catch (Exception $e) {
            $log->error('Erro ao pegar IP do usuário', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'erro' => $e->getMessage()]);
        }
    }
    
    public static function pegarIPeLocUsuarioAPI(){
        $log = AppLogger::getInstance('RastreioIP');
        try {
            $infos_usuario = ApiCalls::apiCall('GET', 'https://ipapi.co/jsonp/', [], []);
            
            // Corrige o formato do JSONP para JSON
            $infos_usuario = trim($infos_usuario, 'callback(');
            $infos_usuario = rtrim($infos_usuario, ');');
            
            // Transforma o JSON para PHP Array
            $infos_usuario = json_decode($infos_usuario, true);
            $log->info('IP coletado usando API', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'infos_usuario' => $infos_usuario]);
            
            return $infos_usuario;
        } catch (Exception $e) {
            $log->error('Erro ao pegar IP e localização do usuário usando API', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'erro' => $e->getMessage()]);
        }
    }
}
?>