async function getUniqueVessels(){
    var request = {
        url: "../../controllers/ShipDischargingController.php",
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

async function getVesselData($type, $vessel){
    const $key = $type == 'discharged' ? 'vesselDataDischarged' : 'vesselDataPlanned';

    var request = {
        url: "../../controllers/ShipDischargingController.php",
        method: 'POST',
        data: [
        {
            name: 'action',
            value: $key
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

export { getUniqueVessels, getVesselData };