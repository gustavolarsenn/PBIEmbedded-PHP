<?php

require 'PowerBiReportDetails.php';
require 'EmbedConfig.php';
require 'ApiCalls.php';

function pbi($actualLink){
    
    $apiCalls = new ApiCalls();
    
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
    
    $reports = [
        "Port Statistics" => "44cece6c-3c3a-4343-8c95-5da3fc5aacf1",
        // "Port%20Statistics" => "44cece6c-3c3a-4343-8c95-5da3fc5aacf1",
        "Line Up - Forecast" => "af1d8571-368a-4016-985c-1e53bb0c9aaa",
        // "Line%20Up%20-%20Forecast" => "af1d8571-368a-4016-985c-1e53bb0c9aaa",
    ];
    
    $currentReport = $reports[$actualLink];
    // echo $actualLink;
    
    $dataset = "a0c518ac-3314-4cfa-bccb-790d7bd8297e"; // 
    $report_id = $currentReport; // Puxar do banco de dados
    // $report_id = "44cece6c-3c3a-4343-8c95-5da3fc5aacf1"; // Puxar do banco de dados
    
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
                'id' => $dataset
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
    
    // AJUNTANDO TODAS AS INFORMAÇÕES EM UMA ROTA

    return json_encode($reportEmbedConfig);
}