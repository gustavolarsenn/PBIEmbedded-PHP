async function getVesselInfo($id_viagem){
    var request = {
        url: "../../controllers/NavioController.php",
        method: 'POST',
        data: [
        {
            name: 'action',
            value: 'pegarInfoNavio'
        },
        {
            name: 'id_viagem',
            value: $id_viagem
        }
    ],
        dataType: 'json'
    };

    // Return a new Promise
    return new Promise((resolve, reject) => {
        $.ajax(request).done(function(response) {
            const error = document.getElementById('error-message');
            if(response.error) {
                error.innerHTML = response.error;
                console.log(response.message)
                reject(response.error);
            } else {
                resolve(response.data);
            }
        }).fail(function(response) {
            console.log(response.erro)
            reject(response.erro);
        })
    });
}
async function getUniqueVessels(){
    var request = {
        url: "../../controllers/NavioController.php",
        method: 'POST',
        data: [
        {
            name: 'action',
            value: 'pegarNaviosUnicos'
        }
    ],
        dataType: 'json'
    };

    // Return a new Promise
    return new Promise((resolve, reject) => {
        $.ajax(request).done(function(response) {
            const error = document.getElementById('error-message');
            if(response.error) {
                error.innerHTML = response.error;
                console.log(response.message)
                reject(response.error);
            } else {
                resolve(response.data);
            }
        }).fail(function(response) {
            console.log(response.error)
            reject(response.error);
        })
    });
}

async function getVesselData($id_viagem){
    var request = {
        url: "../../controllers/PranchaController.php",
        method: 'POST',
        data: [
        {
            name: 'action',
            value: 'pegarDadosNavio'
        },
        {
            name: 'id_viagem',
            value: $id_viagem
        }
    ],
        dataType: 'json'
    };

    // Return a new Promise
    return new Promise((resolve, reject) => {
        $.ajax(request).done(function(response) {
            const error = document.getElementById('error-message');
            if(response.error) {
                error.innerHTML = response.error;
                console.log(response.message)
                reject(response.error);
            } else {
                resolve(response.data);
            }
        }).fail(function(response) {
            console.log(response.error);
            reject(response.error);
        })
    });
}

export { getVesselInfo, getUniqueVessels, getVesselData };