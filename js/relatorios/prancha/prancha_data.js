async function getVesselInfo($vessel){
    var request = {
        url: "../Navio/NavioController.php",
        method: 'POST',
        data: [
        {
            name: 'action',
            value: 'vesselInfo'
        },
        {
            name: 'navio',
            value: $vessel
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
async function getUniqueVessels(){
    var request = {
        url: "../Prancha/PranchaController.php",
        method: 'POST',
        data: [
        {
            name: 'action',
            value: 'uniqueVessels'
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

async function getVesselData($vessel){
    var request = {
        url: "../Prancha/PranchaController.php",
        method: 'POST',
        data: [
        {
            name: 'action',
            value: 'vesselData'
        },
        {
            name: 'navio',
            value: $vessel
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