var lng;
var lat;

var adress = document.querySelector('#adressToMap').textContent;
var theUrl = "https://api-adresse.data.gouv.fr/search/?q="+adress+"&type=street&autocomplete=0";
fetch(theUrl)
    .then(resp => resp.json())
    .then( data => {
        lng = data.features[0].geometry.coordinates[0];
        lat = data.features[0].geometry.coordinates[1];
        myMap(lng, lat)

    })
    .catch(function(error) {
        console.log(error);
    });
function myMap(lng, lat){
    var mymap = L.map('mapid').setView([lat, lng], 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mymap);
    var circle = L.circle([lat, lng], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.5,
        radius: 30
    }).addTo(mymap);

}


