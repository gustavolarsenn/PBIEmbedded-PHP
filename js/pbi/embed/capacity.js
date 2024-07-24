var username = process.env.PBI_USERNAME;
var password = process.env.PBI_PASSWORD;
var client_id = process.env.CLIENT_ID;
var client_secret = process.env.CLIENT_SECRET;
var tenant_id = process.env.TENANT_ID;
var workspace_id = process.env.WORKSPACE_ID;
var subscription_id = process.env.SUBSCRIPTION_ID;
var resource_group = process.env.RESOURCE_GROUP;
var capacity_name = process.env.CAPACITY_NAME;

async function pegarTokenAzureCapacity(){
    const grant_type = "client_credentials";
    const resource = "https://management.core.windows.net";
    const url = `https://login.windows.net/${tenant_id}/oauth2/token`;

    const params = {
        'grant_type': grant_type,
        'client_id': client_id,
        'client_secret': client_secret,
        'resource': resource,
    };

    const header = {
        'Content-Type': 'application/x-www-form-urlencoded'
    };

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: header,
            body: new URLSearchParams(params),
        });

        const data = await response.json();

        return data.access_token;
    } catch (e) {
        return e;
    }
}

async function gerenciarCapacity(action){
    const actionApi = action ? 'resume' : 'suspend';

    const token = await pegarTokenAzureCapacity();

    const url = `https://management.azure.com/subscriptions/${subscription_id}/resourceGroups/${resource_group}/providers/Microsoft.PowerBIDedicated/capacities/${capacity_name}/${actionApi}?api-version=2021-01-01`;
    const header = {
        'Authorization': `Bearer ${token}`,
    };

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: header,
        });

        return response.status
    } catch (e) {
        return e;
    }
}

async function pegarStatusCapacity(){
        
    const token = await pegarTokenAzureCapacity();

    const url = `https://management.azure.com/subscriptions/${subscription_id}/resourceGroups/${resource_group}/providers/Microsoft.PowerBIDedicated/capacities/${capacity_name}?api-version=2021-01-01`;

    const header = {
        'Authorization': `Bearer ${token}`
    };
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: header,
        });

        const data = await response.json();

        return data;
    } catch (e) {
        return e;
    }
}

export {gerenciarCapacity, pegarStatusCapacity};