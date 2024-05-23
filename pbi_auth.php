<?php

require 'PowerBiReportDetails.php';
require 'EmbedConfig.php';

//TOKEN PARA AUTORIZAR BUSCA DO EMBED TOKEN
// Parâmetros para a solicitação de token
$params = [
    'grant_type' => 'password',
    'client_id' => getenv('CLIENT_ID'),
    'username' => getenv('PBI_USERNAME'), // Substitua por seu nome de usuário do Power BI
    'password' => getenv('PBI_PASSWORD'), // Substitua por sua senha do Power BI
    'scope' => 'https://analysis.windows.net/powerbi/api/.default',
];

// Inicialize o cURL
$ch = curl_init();

// Defina as opções do cURL
curl_setopt($ch, CURLOPT_URL, 'https://login.microsoftonline.com/' . getenv('TENANT_ID') . '/oauth2/v2.0/token');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute a solicitação e obtenha a resposta
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

curl_close($ch);

//BUSCA DO EMBED TOKEN

$requestHeader = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . json_decode($response)->access_token,
];

$embedTokenAPI = "https://api.powerbi.com/v1.0/myorg/GenerateToken";


$dataset = "a0c518ac-3314-4cfa-bccb-790d7bd8297e"; // 
$report_id = "44cece6c-3c3a-4343-8c95-5da3fc5aacf1"; // Puxar do banco de dados

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

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $embedTokenAPI);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($embedTokenParams));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);

$response = curl_exec($ch);

curl_close($ch);

$embedToken = json_decode($response)->token;

// BUSCA DE PARAMETROS DE EMBED

$embedParamsAPI = "https://api.powerbi.com/v1.0/myorg/groups/" . getenv('WORKSPACE_ID') . "/reports/" . $report_id; // Puxar do banco de dados

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $embedParamsAPI);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);

$response = curl_exec($ch);

curl_close($ch);

$reportDetails = new PowerBiReportDetails(
    $report_id, 
    json_decode($response)->name, 
    json_decode($response)->embedUrl
);
$reportEmbedConfig = new EmbedConfig();

$reportEmbedConfig->reportsDetail = [$reportDetails];
$reportEmbedConfig->embedToken = $embedToken;

// AJUNTANDO TODAS AS INFORMAÇÕES EM UMA ROTA
echo json_encode($reportEmbedConfig);
