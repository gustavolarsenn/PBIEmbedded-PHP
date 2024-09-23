<?php

require_once __DIR__ . '/../../config/config.php';
require_once CAMINHO_BASE . '/models/API/ApiCalls.php';
require_once CAMINHO_BASE . '/models/PBI/PowerBiReportDetails.php';
require_once CAMINHO_BASE . '/models/PBI/EmbedConfig.php';
class AzureAPI {
    private $username;
    private $password;
    private $client_id;
    private $client_secret;
    private $tenant_id;
    private $workspace_id;
    private $subscription_id;
    private $resource_group;
    private $capacity_name;
    private const LOG_FILE = 'AzureAPI';

    public function __construct() {
        $this->username = getenv('PBI_USERNAME');
        $this->password = getenv('PBI_PASSWORD');
        $this->client_id = getenv('CLIENT_ID');
        $this->client_secret = getenv('CLIENT_SECRET');
        $this->tenant_id = getenv('TENANT_ID');
        $this->workspace_id = getenv('WORKSPACE_ID');
        $this->subscription_id = getenv('SUBSCRIPTION_ID');
        $this->resource_group = getenv('RESOURCE_GROUP');
        $this->capacity_name = getenv('CAPACITY_NAME');
    }

    public function pegarAuthToken(){
        /* 
        Pega token para fazer chamadas em API do PowerBI.
        Utiliza informações de login, senha e id do cliente (Azure).
        */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $params = [
                'grant_type' => 'password',
                'client_id' => $this->client_id,
                'username' => $this->username,
                'password' => $this->password,
                'scope' => 'https://analysis.windows.net/powerbi/api/.default',
            ];
    
            $response = ApiCalls::apiCall('POST', 'https://login.microsoftonline.com/' . $this->tenant_id . '/oauth2/v2.0/token', $params, []);
    
            $log->info('Token de autenticação do PowerBI obtido com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);

            return json_decode($response)->access_token;
        } catch (Exception $e) {
            $log->error('Erro ao obter token de autenticação do PowerBI', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_decode($e->getMessage());
        }
    }

    public function pegarEmbedToken($token, $report_id, $dataset_id, $rlsInfo){
        /* 
        Pega token que identifica e permite a visualização de um relatório específico.
        Utiliza token de autenticação e informações do relatório e dataset.

        TODO: Adicionar informações de RLS (Row Level Security) para permitir visualização de dados específicos.
        */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $header = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ];
    
            $embedTokenParams = [
                'reports' => [
                    [
                        'id' => $report_id
                    ]
                ],
                'datasets' => [
                    [
                        'id' => $dataset_id
                    ]
                ],
                'targetWorkspaces' => [
                    [
                        'id' => $this->workspace_id,
                    ]
                ],
                'allowSaveAs' => "true",
                'accessLevel' => "View",
            ];
    
            if ($rlsInfo){
                $embedTokenParams['identities'] = $rlsInfo;
            }
    
            $embedToken = json_decode(ApiCalls::apiCall('POST', "https://api.powerbi.com/v1.0/myorg/GenerateToken", $embedTokenParams, $header))->token;
    
            $log->info('Token de Embed do PowerBI obtido com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);

            return $embedToken;
        } catch (Exception $e) {
            $log->error('Erro ao obter token de Embed do PowerBI', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_decode($e->getMessage());
        }
    }

    public function pegarEmbedParams($report_id, $dataset_id, $rlsInfo){
        /*
        Gera link para realizar o "embed" e permitir a visualizar o relatório.
        Usa o embed token e id do relatório e dataset.
        */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $token = $this->pegarAuthToken();
            $embedToken = $this->pegarEmbedToken($token, $report_id,  $dataset_id, $rlsInfo);
    
            $header = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ];
    
            $embedParamsAPI = "https://api.powerbi.com/v1.0/myorg/groups/" . $this->workspace_id . "/reports/" . $report_id;
    
            $parametrosEmbed = ApiCalls::apiCall('GET', $embedParamsAPI, [], $header);
    
            $log->info('Parâmetros para Embed do PowerBI obtido com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);

            $parametros_json = json_decode($parametrosEmbed);

            $reportDetails = new PowerBiReportDetails(
                $report_id, 
                $parametros_json->name, 
                $parametros_json->embedUrl
            );
            $reportEmbedConfig = new EmbedConfig();
            
            $reportEmbedConfig->reportsDetail = [$reportDetails];
            $reportEmbedConfig->embedToken = $embedToken;
        
            return $reportEmbedConfig;
        } catch (Exception $e) {
            $log->error('Erro ao obter parâmetros para Embed do PowerBI', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_decode($e->getMessage());
        }
    }

    #TODO - Implementar
    public function exportarPDF(){
        //
    }
    public function pegarTokenAzureCapacity(){
        /*
        Gera token para poder usar API da Azure e manipular capacidade (cluster) de PowerBI.
        Usando id do cliente, segredo do cliente.
        */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $url = "https://login.windows.net/" . $this->tenant_id . "/oauth2/token";
    
            $params = [
                'grant_type' => "client_credentials",
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'resource' => "https://management.core.windows.net",
            ];
    
            $header = [
                'Content-Type: application/x-www-form-urlencoded',
            ];
    
            $response = ApiCalls::apiCall('POST', $url, $params, $header);

            $log->info('Token de autenticação da Azure obtido com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);

            return json_decode($response)->access_token;
        } catch (Exception $e) {
            $log->error('Erro ao obter token de autenticação da Azure', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_decode($e->getMessage());
        }
    }

    public function ligarDesligarCapacity($action){
        /* 
        Liga ou desliga a capacidade (cluster) de PowerBI.
        Utiliza token da Azure para fazer chamada na API da Azure e informações da capacidade.
        */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $actionApi = $action ? 'resume' : 'suspend';

            $token = $this->pegarTokenAzureCapacity();

            $url = "https://management.azure.com/subscriptions/" . $this->subscription_id . 
                    "/resourceGroups/" . $this->resource_group . 
                    "/providers/Microsoft.PowerBIDedicated/capacities/" . 
                    $this->capacity_name . "/" . $actionApi . "?api-version=2021-01-01";

            $header = [
                "Authorization: Bearer " . $token,
            ];

            $response = ApiCalls::apiCall('POST', $url, [], $header);

            $log->info('Capacidade do PowerBI ' . ($action ? 'ligada' : 'desligada') . ' com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);

            return $response;
        } catch (Exception $e) {
            $log->error('Erro ao ' . ($action ? 'ligar' : 'desligar') . ' capacidade do PowerBI', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_decode($e->getMessage());
        }
    }

    public function pegarStatusCapacity(){
        /*
        Pega status atual da capacidade (cluster) de PowerBI.
        Utiliza token da Azure para fazer chamada na API da Azure e informações da capacidade.
        */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $token = $this->pegarTokenAzureCapacity();
    
            $url = "https://management.azure.com/subscriptions/" . $this->subscription_id . 
                    "/resourceGroups/" . $this->resource_group . 
                    "/providers/Microsoft.PowerBIDedicated/capacities/" . 
                    $this->capacity_name . "?api-version=2021-01-01";
    
            $header = [
                "Authorization: Bearer " . $token,
            ];
                $response = ApiCalls::apiCall('GET', $url, [], $header);
    
            $log->info('Status da capacidade do PowerBI obtido com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);

            return json_decode($response)->properties->state;
        } catch (Exception $e) {
            $log->error('Erro ao obter status da capacidade do PowerBI', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_decode($e->getMessage());
        }
    }
}