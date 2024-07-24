var username = process.env.PBI_USERNAME;
var password = process.env.PBI_PASSWORD;
var client_id = process.env.CLIENT_ID;
var client_secret = process.env.CLIENT_SECRET;
var tenant_id = process.env.TENANT_ID;
var workspace_id = process.env.WORKSPACE_ID;
var subscription_id = process.env.SUBSCRIPTION_ID;
var resource_group = process.env.RESOURCE_GROUP;
var capacity_name = process.env.CAPACITY_NAME;

async function pegarAuthToken(){
    const params = {
        'grant_type': 'password',
        'client_id': client_id,
        'username': username,
        'password': password,
        'scope': 'https://analysis.windows.net/powerbi/api/.default',
    };

    // Convert params object to URL-encoded string
    const formBody = Object.keys(params).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(params[key])).join('&');

    const response = await fetch(`https://login.microsoftonline.com/${tenant_id}/oauth2/v2.0/token`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formBody,
    });

    const data = await response.json();

    return data.access_token;
}

async function pegarEmbedToken(token, reportId, datasetId, rlsInfo){
    
    const header = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    };

    const embedTokenParams = {
        'reports': [{
                'id': reportId
        }],
        'datasets': [{
                'id': datasetId
        }],
        'targetWorkspaces': [{
                'id': workspace_id,
        }],
        'allowSaveAs': "true",
        'accessLevel': "View",
    };

    if (rlsInfo){
        embedTokenParams['identities'] = rlsInfo;
    }

    const response = await fetch("https://api.powerbi.com/v1.0/myorg/GenerateToken", {
        method: 'POST',
        headers: header,
        body: JSON.stringify(embedTokenParams),
    });
    if (!response.ok) {
        throw new Error(`Server responded with status code ${response.status}`);
    }

    const data = await response.json();

    return data.token;
}

async function pegarEmbedParams(report_id, dataset_id, rlsInfo){
    const token = await pegarAuthToken();
    const embedToken = await pegarEmbedToken(token, report_id,  dataset_id, rlsInfo);

    const header = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer  ${token}`
    };

    const embedParamsAPI = `https://api.powerbi.com/v1.0/myorg/groups/${workspace_id}/reports/${report_id}`;

    const response = await fetch(embedParamsAPI, {
        method: 'GET',
        headers: header,
    });

    const data = await response.json();
    
    const reportDetails = {
        'reportId': report_id, 
        'reportName': data.name,
        'embedUrl': data.embedUrl
    };

    const reportEmbedConfig = {
        'reportDetails': [reportDetails],
        'embedToken': embedToken,
    }

    return reportEmbedConfig;
}

export {pegarEmbedParams};