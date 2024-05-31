<?php
require_once 'AzureAPI.php';
require_once 'PowerBiReportDetails.php';
require_once 'EmbedConfig.php';
require_once 'ApiCalls.php';
require_once 'config/database.php';
require_once 'controller/pbi_reports.php';
require_once 'PowerBISession.php';

function pbi($actualLink){
    
    $apiCalls = new ApiCalls();
    $conn = (new Database())->getConnection();
    $pbiReports = new PbiReports($conn);
    
    $reports = $pbiReports->getActiveReports();
    
    $currentReport = null;
    if (isset($reports[$actualLink])) {
        $currentReport = $reports[$actualLink];
    } 
    else {
        header('Location: /index.php');
        exit;
    }
    
    $powerBISession = new PowerBISession($conn, $_SESSION['id_usuario']);
    $powerBISession->criarSessaoPBI();
    
    $azureAPI = new AzureAPI();

    $statusCapacity = $azureAPI->pegarStatusCapacity();

    if ($powerBISession->sessoesAtivasPBI()){
        if ($statusCapacity == 'Paused' || $statusCapacity == 'Pausing'){
            $azureAPI->gerenciarCapacity(true);
        }
    } else {
        if ($statusCapacity == 'Succeeded' || $statusCapacity == 'Resuming'){
            $azureAPI->gerenciarCapacity(false);
        }
    }

    $conn = null;

    $params = [
        'grant_type' => 'password',
        'client_id' => getenv('CLIENT_ID'),
        'username' => getenv('PBI_USERNAME'),
        'password' => getenv('PBI_PASSWORD'),
        'scope' => 'https://analysis.windows.net/powerbi/api/.default',
    ];
    
    function getAuthHeader($apiCaller, $reqParams){
    
        $response = $apiCaller->apiCall('POST', 'https://login.microsoftonline.com/' . getenv('TENANT_ID') . '/oauth2/v2.0/token', $reqParams, []);
    
        $requestHeader = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . json_decode($response)->access_token,
        ];
    
        return $requestHeader;
    }
    $reqHeader = getAuthHeader($apiCalls, $params);
    
    // BUSCA DE EMBED TOKEN
    $embedTokenAPI = "https://api.powerbi.com/v1.0/myorg/GenerateToken";

    $report_id = $currentReport['report_id'];
    $dataset_id = $currentReport['dataset_id'];

    // Caso não houver RLS, retirar a linha de 'identities'
    $embedTokenParams = [
        'reports' => [
            [
                'id' => $report_id
            ]
        ],
        // 'identities' => [],
        'datasets' => [
            [
                'id' => $dataset_id
            ]
        ],
        'targetWorkspaces' => [
            [
                'id' => getenv('WORKSPACE_ID')
            ]
        ],
        'allowSaveAs' => "true",
        'accessLevel' => "View",
    ];
    
    $embedToken = json_decode($apiCalls->apiCall('POST', $embedTokenAPI, $embedTokenParams, $reqHeader))->token;
    
    // BUSCA DE PARAMETROS DE EMBED
    function getEmbedParams($apiCaller, $requestHeader, $report_id, $embedToken){
        $embedParamsAPI = "https://api.powerbi.com/v1.0/myorg/groups/" . getenv('WORKSPACE_ID') . "/reports/" . $report_id; // Puxar do banco de dados
        
        $embedParams = $apiCaller->apiCall('GET', $embedParamsAPI, [], $requestHeader);
        
        $reportDetails = new PowerBiReportDetails(
            $report_id, 
            json_decode($embedParams)->name, 
            json_decode($embedParams)->embedUrl
        );
        $reportEmbedConfig = new EmbedConfig();
        
        $reportEmbedConfig->reportsDetail = [$reportDetails];
        $reportEmbedConfig->embedToken = $embedToken;
    
        return $reportEmbedConfig;
    }
    
    $reportEmbedConfig = getEmbedParams($apiCalls, $reqHeader, $report_id, $embedToken);
    
    // $reportEmbedConfig = $azureAPI->pegarEmbedParams($currentReport['report_id'], $currentReport['dataset_id'], null);

    // AJUNTANDO TODAS AS INFORMAÇÕES EM UMA ROTA

    return json_encode($reportEmbedConfig);
}