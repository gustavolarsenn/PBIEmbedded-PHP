<?php

require_once 'ApiCalls.php';

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
        $params = [
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'username' => $this->username,
            'password' => $this->password,
            'scope' => 'https://analysis.windows.net/powerbi/api/.default',
        ];

        $response = ApiCalls::apiCall('POST', 'https://login.microsoftonline.com/' . $this->tenant_id . '/oauth2/v2.0/token', $params, []);

        return json_decode($response)->access_token;
    }

    public function pegarEmbedToken($token, $report_id, $dataset_id, $rlsInfo){
        
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

        return $embedToken;
    }

    public function pegarEmbedParams($report_id, $dataset_id, $rlsInfo){
        $token = $this->pegarAuthToken();
        $embedToken = $this->pegarEmbedToken($token, $report_id,  $dataset_id, $rlsInfo);

        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ];

        $embedParamsAPI = "https://api.powerbi.com/v1.0/myorg/groups/" . $this->workspace_id . "/reports/" . $report_id;

        $parametrosEmbed = ApiCalls::apiCall('GET', $embedParamsAPI, [], $header);

        $reportDetails = new PowerBiReportDetails(
            $report_id, 
            json_decode($parametrosEmbed)->name, 
            json_decode($parametrosEmbed)->embedUrl
        );
        $reportEmbedConfig = new EmbedConfig();
        
        $reportEmbedConfig->reportsDetail = [$reportDetails];
        $reportEmbedConfig->embedToken = $embedToken;
    
        return $reportEmbedConfig;
    }

    public function pegarTokenAzureCapacity(){
        $grant_type = "client_credentials";
        $resource = "https://management.core.windows.net";
        $url = "https://login.windows.net/" . $this->tenant_id . "/oauth2/token";

        $params = [
            'grant_type' => $grant_type,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'resource' => $resource,
        ];

        $header = [
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $response = ApiCalls::apiCall('POST', $url, $params, $header);

        return json_decode($response)->access_token;
    }

    public function gerenciarCapacity($action){

        $actionApi = $action ? 'resume' : 'suspend';

        $token = $this->pegarTokenAzureCapacity();

        $url = "https://management.azure.com/subscriptions/" . $this->subscription_id . 
                "/resourceGroups/" . $this->resource_group . 
                "/providers/Microsoft.PowerBIDedicated/capacities/" . 
                $this->capacity_name . "/" . $actionApi . "?api-version=2021-01-01";

        $header = [
            'Authorization: Bearer ' . $token,
        ];

        $response = ApiCalls::apiCall('POST', $url, [], $header);

        return $response;
    }

    public function pegarStatusCapacity(){
            
            $token = $this->pegarTokenAzureCapacity();
    
            $url = "https://management.azure.com/subscriptions/" . $this->subscription_id . 
                    "/resourceGroups/" . $this->resource_group . 
                    "/providers/Microsoft.PowerBIDedicated/capacities/" . 
                    $this->capacity_name . "?api-version=2021-01-01";
    
            $header = [
                'Authorization: Bearer ' . $token,
            ];
    
            $response = ApiCalls::apiCall('GET', $url, [], $header);
    
            return json_decode($response)->properties->state;
    }
}