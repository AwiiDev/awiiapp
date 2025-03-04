<script>
import { Link,Head, useForm } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref, watch,reactive,computed,onMounted } from "vue";
import axios from "axios";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { Scrollbar } from 'swiper/modules';
import FormValidation from "@/Components/FormValidation.vue";
import debounce from 'lodash/debounce';
import { mapGetters } from 'vuex';
import { useI18n } from 'vue-i18n';
import  { initI18n } from "@/i18n";
import UserWebMenu from "@/Components/UserWebMenu.vue";

export default {
data() {
return {
rightOffcanvas: false,
isPocSame: false, // checkbox state
form: {
is_later: '',
assign_method: 1,
name: '',
mobile: '',
drop_poc_name: '',
drop_poc_mobile: '',
},
dateTimeConfig: {
enableTime: true,
dateFormat: "Y-m-d H:i:ss",
},
};
},

watch: {
    isPocSame(newVal) {
      if (newVal) {
        this.form.drop_poc_name = this.form.name;
        this.form.drop_poc_mobile = this.form.mobile;
      } else {
        this.form.drop_poc_name = '';
        this.form.drop_poc_mobile = '';
      }
    },
    // Watch for changes in personal info to update POC if the checkbox is checked
    'form.name'(newVal) {
      if (this.isPocSame) {
        this.form.drop_poc_name = newVal;
      }
    },
    'form.mobile'(newVal) {
      if (this.isPocSame) {
        this.form.drop_poc_mobile = newVal;
      }
    },
  },

components: {
Layout,
PageHeader,
Head,
Link,
Scrollbar,
FormValidation,
flatPickr,
UserWebMenu
},
computed: {
    ...mapGetters(['permissions']),
    
  },
props: {
countries:Array,
default_flag:String,
default_dial_code:String,
successMessage: String,
default_lat:String,
default_lng:String,
alertMessage: String,
validate: Function, // Define the prop to receive the method,
transport_type_for_ride:Array,
ride_type_for_ride:Array,
is_rental:Boolean,
goodsTypes:Array,
user: Object,
firebaseSettings:Array,
map_key:String,
},

methods: {
    timer() {
      let timerInterval;
      Swal.fire({
        title: "Booking alert!",
        html: "Your Ride has been Booked <b></b> Successfully.",
        timer: 2000,
        timerProgressBar: true,
        onBeforeOpen: () => {
          Swal.showLoading();
          timerInterval = setInterval(() => {
            Swal.getContent().querySelector("b").textContent =
              Swal.getTimerLeft();
          }, 100);
        },
        onClose: () => {
          clearInterval(timerInterval);
        },
      }).then((result) => {
        if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.timer
        ) {
          console.log("I was closed by the timer"); // eslint-disable-line
        }
      });
    },
  },

setup(props) {
  const { t } = useI18n();
const selectedCountry = ref({
dial_code: props.default_dial_code || '',
flag: props.default_flag || ''
});

  const form = useForm({
  mobile:props.user.mobile,
  name:props.user.name,
  user_id:props.user.id,
  pick_address: "",
  drop_address: "",
  terminal:null,
  enterance:null,
  pick_lat:"",
  pick_lng:"",
  drop_lat:null,
  drop_lng:null,
  vehicle_type:null,
  payment_opt:1,
  goods_type_id:null,
  transport_type:'',
  pickup_poc_name:null,
  drop_poc_name:null,
  goods_type_quantity:null,
  is_later:0,
  assign_method:1,
  is_rental:0,
  rental_package_id:null,
  ride_type:'regular',
  stops:null,
  trip_start_time:null,
  country:props.default_dial_code,
  drop_poc_mobile:null,


  });

// For Airport
const showAiportTerminal = ref(false);
const showTrainStationEnterance = ref(false);


const validationRules = {
name: { required: true },
pick_address: { required: true },
mobile: { required: true },
vehicle_type: { required: true }
};
const errors = ref({});
const validationRef = ref(null);
const vehicleTypes = ref([]);
const stopMarkers = ref([]);

const etaParam = useForm({
pick_lat:"",
pick_lng:"",
drop_lat:"",
drop_lng:"",
distance:0,
duration:0
});


const modalShow = ref(false);

const successMessage = ref(props.successMessage || '');
const alertMessage = ref(props.alertMessage || '');

const dismissMessage = () => {
successMessage.value = "";
alertMessage.value = "";
};

const stopovers = reactive([]);
const maxStopovers = 5;

const addStopover = () => {

console.log("stopovers-length");
console.log(stopovers.length);

if (stopovers.length < maxStopovers) {


stopovers.push({ address: "", latitude: null, longitude: null });
if(stopovers.length==0){
addStopMarker(0);
}
stopovers.forEach((_, i) => {
addStopMarker(i);
});

} else {
Swal.fire(t('maximum_stopovers_reached'), t('you_can_add_up_to_5_stopovers_only'), 'warning');
}
};

const removeStopover = (index) => {
// Remove marker from map
if (stopMarkers[index]) {
console.log("removing");
console.log(stopMarkers[index]);
stopMarkers[index].setMap(null);
console.log(stopMarkers[index]);
// stopMarkers.splice(index, 1);
}

console.log(stopMarkers);

// Remove stopover from array
stopovers.splice(index, 1);

// Reinitialize autocomplete for remaining stops

stopovers.forEach((_, i) => {
addStopMarker(i);
});

// Redraw the route
if(index>0){
drawRoute();
}
};

// Initialize Map
const initializeMap = () => {
const map = new google.maps.Map(document.getElementById('map'), {
center: { lat: parseFloat(props.default_lat), lng: parseFloat(props.default_lng) },
zoom: 15,
});

const pickupIconUrl = '/image/map/pickup.png';
const dropIconUrl = '/image/map/drop.png';
const stopIconUrl = '/image/map/stop.png';

const pickupMarker = new google.maps.Marker({
map,
draggable: true,
icon: {
url: pickupIconUrl,
scaledSize: new google.maps.Size(30, 30),
},
});

const dropMarker = new google.maps.Marker({
map,
draggable: true,
icon: {
url: dropIconUrl,
scaledSize: new google.maps.Size(30, 30),
},
});

let stopMarkers = [];
let routePolyline;
let driverMarkers = {};

const autocompletePickup = new google.maps.places.Autocomplete(document.getElementById('pickup'));
const autocompleteDrop = new google.maps.places.Autocomplete(document.getElementById('drop'));

autocompletePickup.addListener('place_changed', () => {
const place = autocompletePickup.getPlace();
if (!place.geometry) {
console.error(t('place_details_not_found_for_the_input'), autocompletePickup);
return;
}

pickupMarker.setPosition(place.geometry.location);

form.pick_address = place.formatted_address;

// document.getElementById('pickup').value = place.formatted_address;

map.setCenter(place.geometry.location);
map.setZoom(15);

const placeTypes = place.types;

console.log(placeTypes);


if (placeTypes.includes('airport')) {
showAiportTerminal.value = true;
} else if (placeTypes.includes('train_station')) {
showTrainStationEnterance.value = true;
}


let picklocation = place.geometry.location;

etaParam.pick_lat = picklocation.lat();
etaParam.pick_lng = picklocation.lng();


if (dropMarker.getPosition()) {
drawRoute();
}
});

autocompleteDrop.addListener('place_changed', () => {
const place = autocompleteDrop.getPlace();
if (!place.geometry) {
console.error(t('place_details_not_found_for_the_input'), autocompleteDrop);
return;
}

dropMarker.setPosition(place.geometry.location);

form.drop_address = place.formatted_address;

// document.getElementById('drop').value = place.formatted_address;

map.setCenter(place.geometry.location);
map.setZoom(15);

const placeTypes = place.types;

if (placeTypes.includes('airport')) {
showAiportTerminal.value = true;
} else if (placeTypes.includes('train_station')) {
showTrainStationEnterance.value = true;
}

let droplocation = place.geometry.location;

etaParam.drop_lat = droplocation.lat();
etaParam.drop_lng = droplocation.lng();

// loadVehicleTypes();


if (pickupMarker.getPosition()) {
drawRoute();
}
});

google.maps.event.addListener(pickupMarker, 'dragend', () => {
reverseGeocodeAndUpdateInput(pickupMarker.getPosition(), 'pickup');
fetchNearbyDrivers();


let pickupdraglocation = pickupMarker.getPosition();

etaParam.pick_lat = pickupdraglocation.lat();
etaParam.pick_lng = pickupdraglocation.lng();

// loadVehicleTypes();

if (dropMarker.getPosition()) {
drawRoute();
}
});

google.maps.event.addListener(dropMarker, 'dragend', () => {
reverseGeocodeAndUpdateInput(dropMarker.getPosition(), 'drop');


let dropdraglocation = pickupMarker.getPosition();

etaParam.drop_lat = dropdraglocation.lat();
etaParam.drop_lng = dropdraglocation.lng();

// loadVehicleTypes();

if (pickupMarker.getPosition()) {
drawRoute();
}
});


watch(() => etaParam.pick_lat,async ( pickLat) => {
if (pickLat) {
  loadVehicleTypes();
}
});
const drawRoute = () => {

loadVehicleTypes(false);

if (!pickupMarker.getPosition() || !dropMarker.getPosition()) {
return;
}

const directionsService = new google.maps.DirectionsService();
const waypoints = stopovers.map(stop => ({
location: new google.maps.LatLng(stop.latitude, stop.longitude),
stopover: true
}));

directionsService.route({
origin: pickupMarker.getPosition(),
destination: dropMarker.getPosition(),
waypoints: waypoints,
travelMode: google.maps.TravelMode.DRIVING,
}, (response, status) => {
if (status === 'OK') {

  let totalDistance = 0;

// Assuming response.routes[0].legs is an array
response.routes[0].legs.forEach(leg => {
  totalDistance += leg.distance.value;
});

let totalDuration = 0;
// Assuming response.routes[0].legs is an array
response.routes[0].legs.forEach(leg => {
  totalDuration += leg.duration.value;
});

etaParam.distance = totalDistance;
etaParam.duration = Math.round(totalDuration);

  console.log(etaParam);

const route = response.routes[0];

if (routePolyline) {
routePolyline.setMap(null);
}

routePolyline = new google.maps.Polyline({
path: route.overview_path,
strokeColor: '#FF0000',
strokeOpacity: 0.8,
strokeWeight: 4,
map: map,
});

const bounds = new google.maps.LatLngBounds();
route.overview_path.forEach(point => bounds.extend(point));
map.fitBounds(bounds);
} else {
console.error(t('directions_request_failed_due_to') + status);
}
});
};


const addStopMarker = (index) => {
const autocompleteStop = new google.maps.places.Autocomplete(document.getElementById(`stop-${index}`));

autocompleteStop.addListener('place_changed', () => {
const place = autocompleteStop.getPlace();
if (!place.geometry) {
console.error(t('place_details_not_found_for_the_input'), autocompleteStop);
return;
}

if (stopMarkers[index]) {
stopMarkers[index].setMap(null);
}

const stopMarker = new google.maps.Marker({
map,
position: place.geometry.location,
draggable: true,
icon: {
url: '/image/map/'+index+'.png',
scaledSize: new google.maps.Size(30, 30),
},
});

google.maps.event.addListener(stopMarker, 'dragend', () => {
const position = stopMarker.getPosition();
reverseGeocodeAndUpdateInput(position, `stop-${index}`);
stopovers[index].latitude = position.lat();
stopovers[index].longitude = position.lng();
drawRoute();

});

stopMarkers[index] = stopMarker;
stopovers[index].address = place.formatted_address;
stopovers[index].latitude = place.geometry.location.lat();
stopovers[index].longitude = place.geometry.location.lng();

map.setCenter(place.geometry.location);
map.setZoom(15);

if (pickupMarker.getPosition() && dropMarker.getPosition()) {
drawRoute();
}
});
};

watch(stopovers, (newVal) => {
newVal.forEach((stop, index) => {

if (!stopMarkers[index]) {
console.log("addstopMarker-Calling");
addStopMarker(index);
}
});
}, { deep: true });

const loadVehicleTypes  = async (fetch = true) =>{

const { pick_lat, pick_lng, drop_lat, drop_lng,distance, duration } = etaParam.data();

const payload = {
pick_lat,
pick_lng,
distance,
duration,
};

payload.transport_type = form.transport_type;

if (drop_lat) {
payload.drop_lat = drop_lat;
}

if (drop_lng) {
payload.drop_lng = drop_lng;
}

if(stopovers.length>0){
    payload.stops=JSON.stringify(stopovers);
}

const response = await axios.post(`/dispatch/request/eta`, payload);

vehicleTypes.value = response.data.data;

  fetchNearbyDrivers();

};
const setEtaTime = (driver,distance) => {

const time = calculateTime(distance);
vehicleTypes.value.forEach((vehicleType) => {
if (driver.vehicle_types && driver.vehicle_types.includes(vehicleType.type_id) && (!vehicleType.driver_distance || vehicleType.driver_distance > distance)) {
    vehicleType.driver_time = time;
  }
});
return true;
}


watch(() => form.vehicle_type,async ( value) => {
      fetchNearbyDrivers();
});
const fetchNearbyDrivers = () => {
  let pickupLocation = pickupMarker.getPosition();
console.log(pickupLocation.lat(), pickupLocation.lng());
    vehicleTypes.value.forEach((vehicleType) => {
      vehicleType.driver_time = null;
    });

const driversRef = firebase.database().ref('drivers');

driversRef.on('value', (snapshot) => {
snapshot.forEach((childSnapshot) => {
const driver = childSnapshot.val();
const driverGeoHash = driver.g;
const driverLocation = decodeGeohash(driverGeoHash);

if (driverLocation) {
const distance = calculateDistance(pickupLocation.lat(), pickupLocation.lng(), driverLocation.lat, driverLocation.lon);
if(distance <= 10){
  setEtaTime(driver,distance);
}

var flag = true;
if(form.vehicle_type){
  flag = false;
  var type_id = vehicleTypes.value?.find((type)=> {
    return (type.zone_type_id === form.vehicle_type);
  })?.type_id;
  if(type_id && driver.vehicle_types && driver.vehicle_types.includes(type_id)){
    flag = true;
  }
}

if (distance <= 10 && driver.is_available && driver.is_active && flag) {
const driverLatLng = new google.maps.LatLng(driverLocation.lat, driverLocation.lon);
const vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;

if (driverMarkers[driver.id]) {
animateDriverMovement(driverMarkers[driver.id], driverMarkers[driver.id].getPosition(), driverLatLng, driver.bearing);
driverMarkers[driver.id].setIcon({
url: vehicleTypeIconUrl,
scaledSize: new google.maps.Size(30, 30),
});
} else {
const driverMarker = new google.maps.Marker({
position: driverLatLng,
map: map,
icon: {
url: vehicleTypeIconUrl,
scaledSize: new google.maps.Size(30, 30),
},
});
driverMarkers[driver.id] = driverMarker;
animateDriverMovement(driverMarker, driverMarker.getPosition(), driverLatLng, driver.bearing);
}




} else if (driverMarkers[driver.id]) {

driverMarkers[driver.id].setMap(null);
delete driverMarkers[driver.id];


}




}



});
});

driversRef.on('child_changed', (childSnapshot) => {
const driver = childSnapshot.val();
if (!driver.is_active) {
vehicleTypes.value.forEach((vehicleType) => {
if (driver.vehicle_types.some(type => type === vehicleType.type_id)) {
  fetchNearbyDrivers();
}
});

removeDriverMarker(driver.id);
} else {             
updateDriverMarker(driver);
}
});

driversRef.on('child_removed', (childSnapshot) => {
const driver = childSnapshot.val();

removeDriverMarker(driver.id);
});
};

const calculateTime = (distance) => {

if(distance<2){
return '3 Mins';
}else if(distance > 2 && distance <5){
return '5 Mins';
}
else if(distance > 5 && distance <7){
return '10 Mins';
}else{
return '15 mins';
}
};


const animateDriverMovement = (marker, prevLatLng, currentLatLng, currentBearing) => {
const numSteps = 60;
const timeInterval = 2000;
const delay = timeInterval / numSteps;

let startLat = prevLatLng.lat();
let startLng = prevLatLng.lng();

let endLat = currentLatLng.lat();
let endLng = currentLatLng.lng();

const easeInOutQuad = (t) => t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;

const moveMarker = (step) => {
let t = easeInOutQuad(step / numSteps);
let newPosition = new google.maps.LatLng(startLat + (endLat - startLat) * t, startLng + (endLng - startLng) * t);
marker.setPosition(newPosition);

if (currentBearing !== undefined) {
const prevBearing = marker.prevBearing || currentBearing;
const bearingDiff = currentBearing - prevBearing;
const interpolatedBearing = prevBearing + (bearingDiff * t);
marker.setIcon({
...marker.getIcon(),
rotation: interpolatedBearing,
});
marker.prevBearing = currentBearing;
}

if (step < numSteps) {
setTimeout(() => moveMarker(step + 1), delay);
}
};

moveMarker(0);
};

const updateDriverMarker = (driver) => {
const driverGeoHash = driver.g;
const driverLocation = decodeGeohash(driverGeoHash);

if (driverLocation) {
const driverLatLng = new google.maps.LatLng(driverLocation.lat, driverLocation.lon);
const vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;

if (driverMarkers[driver.id]) {
animateDriverMovement(driverMarkers[driver.id], driverMarkers[driver.id].getPosition(), driverLatLng, driver.bearing);
driverMarkers[driver.id].setIcon({
url: vehicleTypeIconUrl,
scaledSize: new google.maps.Size(30, 30),
});
} else {
const driverMarker = new google.maps.Marker({
position: driverLatLng,
map: map,
icon: {
url: vehicleTypeIconUrl,
scaledSize: new google.maps.Size(30, 30),
},
});
driverMarkers[driver.id] = driverMarker;
}
} else {
console.error(`Failed to decode geohash for driver ID: ${driver.id}`);
}
};

const removeDriverMarker = (driverId) => {
if (driverMarkers[driverId]) {
driverMarkers[driverId].setMap(null);
delete driverMarkers[driverId];
}
};

const decodeGeohash = (geohash) => {
const BASE32 = '0123456789bcdefghjkmnpqrstuvwxyz';
const BITS = [16, 8, 4, 2, 1];
let isEven = true;
let latMin = -90,
latMax = 90;
let lonMin = -180,
lonMax = 180;
let lat, lon;

if (geohash) {
for (let i = 0; i < geohash.length; i++) {
let c = geohash.charAt(i);
let cd = BASE32.indexOf(c);
for (let j = 0; j < 5; j++) {
let mask = BITS[j];
if (isEven) {
let lonMid = (lonMin + lonMax) / 2;
if (cd & mask) {
lonMin = lonMid;
} else {
lonMax = lonMid;
}
} else {
let latMid = (latMin + latMax) / 2;
if (cd & mask) {
latMin = latMid;
} else {
latMax = latMid;
}
}
isEven = !isEven;
}
}
lat = (latMin + latMax) / 2;
lon = (lonMin + lonMax) / 2;
return { lat: lat, lon: lon };
}
};

const calculateDistance = (lat1, lon1, lat2, lon2) => {
const R = 6371;
const dLat = (lat2 - lat1) * Math.PI / 180;
const dLon = (lon2 - lon1) * Math.PI / 180;
const a =
Math.sin(dLat / 2) * Math.sin(dLat / 2) +
Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
Math.sin(dLon / 2) * Math.sin(dLon / 2);
const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
const distance = R * c;
return distance;
};

const reverseGeocodeAndUpdateInput = (position, inputId) => {
const geocoder = new google.maps.Geocoder();
geocoder.geocode({ location: position }, (results, status) => {
if (status === 'OK') {
if (results[0]) {
document.getElementById(inputId).value = results[0].formatted_address;
} else {
console.error(t('no_reverse_geocode_results_found'));
}
} else {
console.error(t('geocoder_failed_due_to') + status);
}
});
};


};

// Submit a Booking

const makeBooking = async ()=>{
console.log("boooking-calling");
console.log(form.vehicle_type);
errors.value = validationRef.value.validate();
if (Object.keys(errors.value).length > 0) {
  console.log("yes-error-coming");
  console.log(errors.value);
    return;
}

const { pick_lat, pick_lng, drop_lat, drop_lng } = etaParam.data();

if (pick_lat) {
form.pick_lat = pick_lat;
}
if (pick_lng) {
form.pick_lng = pick_lng;
}
if (drop_lat) {
form.drop_lat = drop_lat;
}
if (drop_lng) {
form.drop_lng = drop_lng;
}

if(stopovers.length>0){
form.stops = JSON.stringify(stopovers);
}

console.log("booking-info");
console.log(form.data());


try {
        let response;
        response = await axios.post(`/web-create-request`, form.data());

  const createdRequestId = response.data.data.id; // Adjust this based on the structure of your response
        if (response.data.success === true) {


      let timerInterval;
      Swal.fire({
        title: "Booking Successfull",
        html: "Your Ride has been Booked <b></b> Successfully.",
        timer: 2000,
        timerProgressBar: true,
        onBeforeOpen: () => {
          Swal.showLoading();
          timerInterval = setInterval(() => {
            Swal.getContent().querySelector("b").textContent =
              Swal.getTimerLeft();
          }, 100);
        },
        onClose: () => {
          clearInterval(timerInterval);
        },
      }).then((result) => {
        if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.timer
        ) {
            window.location.href = `/history/view/${createdRequestId}`;
        }
      });
          
          form.reset();
          etaParam.reset();
          initializeMap();
          vehicleTypes.value = [];

        } else {
          alertMessage.value = t('failed_to_make_booking_contact_admin');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_vehicle_price'), error);
          alertMessage.value = t('failed_to_make_booking_contact_admin');
        }
      }

}

onMounted(async() => {
  await initI18n('en');
})
onMounted(() => {
           const map_key = props.map_key;

const script = document.createElement('script');
script.src = `https://maps.googleapis.com/maps/api/js?key=${map_key}&libraries=places`;
script.async = true;
script.defer = true;
script.onload = initializeMap;
document.head.appendChild(script);

        var firebaseConfig = {
            apiKey: props.firebaseSettings['firebase_api_key'],
            authDomain: props.firebaseSettings['firebase_auth_domain'],
            databaseURL: props.firebaseSettings['firebase_database_url'],
            projectId: props.firebaseSettings['firebase_project_id'],
            storageBucket:  props.firebaseSettings['firebase_storage_bucket'],
            messagingSenderId: props.firebaseSettings['firebase_messaging_sender_id'],
            appId: props.firebaseSettings['firebase_app_id'],
            measurementId:  props.firebaseSettings['firebase_measurement_id'],
            };
        firebase.initializeApp(firebaseConfig);

if (props.transport_type_for_ride.length > 0) {
form.transport_type = props.transport_type_for_ride[0];
}

if (props.ride_type_for_ride.length > 0) {
form.ride_type = props.ride_type_for_ride[0];
}

});


const mobile = ref('');

const searchQuery = ref('');

const filteredCountries = computed(() => {
});

const selectCountry = (country) => {
selectedCountry.value = country;
form.country=country.dial_code;
};

const validateNumber = debounce(async () => {
// event.target.value = event.target.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
const response = await axios.get(`/dispatch/fetch-user-detail`, { params:{mobile:form.mobile} });
console.log(response.data.data);

if(response.data.data){
console.log("if")
form.name = response.data.data.name;
form.user_id = response.data.data.id;
}else{
console.log("else")
form.user_id=null;
form.name=null;
}

}, 300); // Adjust the debounce delay as needed

return {
form,
modalShow,
successMessage,
alertMessage,
dismissMessage,
validationRef,
validationRules,
errors,
mobile,
selectedCountry,
searchQuery,
filteredCountries,
selectCountry,
validateNumber,
showAiportTerminal,
showTrainStationEnterance,
etaParam,
vehicleTypes,
makeBooking,
stopovers,
addStopover,
removeStopover,
};
},


};
</script>

<template>
<BCard>
  <Head title="Taxi Ride" />
    <BCardHeader class="border-0">
    <!-- menu Offcanvas -->
       <UserWebMenu :user="user" />
    <!-- menu end -->
    </BCardHeader>

<BRow>
  <BCol lg="12">
    <BCard no-body id="tasksList">
      <BCardHeader class="border-0">
        </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
              <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
        <div class="row">
          <div class="col-12 col-lg-6" style="max-height:500px;overflow-y: auto;">
            <div class="row">
            <!-- Ride info -->
              <div class="d-flex align-items-center mt-4">
                 <h4 class="card-title mb-3 flex-grow-1">{{$t("ride_info")}}</h4>
              </div>
          <div class="col-6">
            <div class="mb-3">
              <label for="type" class="form-label">{{$t("booking_type")}}</label>
                <select id="type" class="form-select" v-model="form.is_later">
                  <option disabled value="">{{$t("choose_type")}}</option>
                  <option value=0>{{$t("instant_booking")}}</option>
                  <option value=1>{{$t("book_later")}}</option>
                </select>
              <span v-for="(error, index) in errors.is_later" :key="index" class="text-danger">{{ error }}</span>
            </div>
          </div>
        <div class="col-6">
          <div class="col-12">
            <div class="mb-3">
              <label for="type" class="form-label">{{$t("transport_type")}}</label>
                <select id="type" class="form-select" v-model="form.transport_type">
                  <option disabled value="">{{$t("choose_transport_type")}}</option>
                    <option 
                    v-for="(type, index) in transport_type_for_ride" 
                    :key="index" 
                    :value="type"
                    >
                    {{ type.charAt(0).toUpperCase() + type.slice(1) }}
                    </option>
                    </select>
                    <span 
                    v-for="(error, index) in errors.transport_type" 
                    :key="index" 
                    class="text-danger"
                    >
                    {{ error }}
              </span>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="mb-3">
          <div class="d-flex align-items-center justify-content-between">
            <label for="pickup" class="form-label">{{ $t("pickup_location") }}</label>
            <!-- <a v-if="form.ride_type=='regular' && etaParam.drop_lat" class="btn btn-primary mb-3" @click="addStopover">{{$t("add_stops")}}</a> -->
            </div>
            <input type="text" class="form-control" v-model="form.pick_address" :placeholder="$t('enter_pickup')" id="pickup" >
            <span v-for="(error, index) in errors.pick_address" :key="index" class="text-danger">{{ error }}</span>
          </div>
        </div>
        <div class="col-12" v-if="form.ride_type=='regular'" v-for="(stop, index) in stopovers" :key="index">
            <div class="mb-3">
              <label :for="`stop-${index}`" class="form-label">{{$t("stop_location")}}</label>
              <div class="d-flex align-items-center">
              <input type="text" class="form-control mx-1" v-model="stop.address" :placeholder="'Enter stop ' + (index + 1)" :id="`stop-${index}`" >
              <i class="bx bx-trash text-danger fs-22 btn" @click="removeStopover(index)"></i>
            </div>
          <span v-if="form.errors[`stop-${index}`]" class="text-danger">{{ form.errors[`stop-${index}`] }}</span>
        </div>
    </div>
    <div class="col-12" v-if="showAiportTerminal">
      <div class="mb-3">
        <label for="terminal" class="form-label">{{$t("aiport_terminal_info")}}</label>
        <input type="text" class="form-control" v-model="form.terminal" placeholder="$t('enter_terminal_info')" id="terminal" >
      </div>
    </div>
    <div class="col-12" v-if="showTrainStationEnterance">
      <div class="mb-3">
      <label for="enterance" class="form-label">{{$t("station_enterance_info")}}</label>
      <input type="text" class="form-control" v-model="form.enterance" placeholder="$t('enter_enterance_info')" id="enterance" >
      </div>
    </div>
  <div class="col-12" v-if="form.ride_type=='regular'">
    <div class="mb-3">
      <label for="drop" class="form-label">{{$t("drop_location")}}</label>
      <input type="text" class="form-control" v-model="form.drop_address" :placeholder="$t('enter_drop')" id="drop" >
      <span v-for="(error, index) in errors.drop_address" :key="index" class="text-danger">{{ error }}</span>
    </div>
  </div>
<!-- POC info -->
<h4 v-if="form.transport_type=='delivery'" class="card-title mb-2 flex-grow-1 mt-4">{{$t("poc_info")}}</h4>
  <div v-if="form.transport_type=='delivery'" class="px-2 mb-2">
    <span class="badge bg-dark-subtle text-dark p-1 fs-12 text-center">
      <div class="form-check m-2 mx-3">
          
          <input class="form-check-input" type="checkbox" id="poc" v-model="isPocSame">
          <label class="form-check-label mt-1" for="poc">
            {{$t("check_if_poc_info_as_same_as_personal_info")}}
          </label>
      </div>
    </span>
  </div>
<div v-if="form.transport_type=='delivery'" class="col-6">
  <div class="mb-3">
     <label for="poc_name" class="form-label">{{$t("drop_poc_name")}}</label>
      <input type="text" class="form-control" v-model="form.drop_poc_name" :placeholder="$t('enter_poc_name')" id="poc_name" :disabled="isPocSame" >
    <span v-for="(error, index) in errors.drop_poc_name" :key="index" class="text-danger">{{ error }}</span>
  </div>
</div>
<div v-if="form.transport_type=='delivery'" class="col-6">
  <div class="mb-3">
  <label for="poc_mobile" class="form-label">{{$t("drop_poc_mobile")}}</label>
  <input type="text" class="form-control" v-model="form.drop_poc_mobile" :placeholder="$t('enter_poc_mobile')" id="poc_mobile" :disabled="isPocSame" >
  <span v-for="(error, index) in errors.poc_mobile" :key="index" class="text-danger">{{ error }}</span>
  </div>
</div>
<h4 v-if="form.transport_type=='delivery'" class="card-title mb-2 flex-grow-1 mt-4">{{$t("select_goods")}}</h4>
   <div v-if="form.transport_type=='delivery'" class="col-6">
      <div class="mb-3">
        <label for="goods_type" class="form-label">{{$t("goods_type")}}</label>
          <select id="goods_type" class="form-select" v-model="form.goods_type_id">
          <option disabled value="">{{$t("choose_type")}}</option>
          <option v-for="goodsType in goodsTypes" :key="goodsType.id" :value="goodsType.id">{{ goodsType.goods_type_name }}</option>
          </select>
        <span v-for="(error, index) in errors.goods_type_id" :key="index" class="text-danger">{{ error }}</span>
      </div>
</div>
<div v-if="form.transport_type=='delivery'" class="col-6">
  <div class="mb-3">
    <label for="goods_type_quantity" class="form-label">{{$t("goods_quantity")}}</label>
    <input type="text" class="form-control" v-model="form.goods_type_quantity" :placeholder="$t('enter_quantity')" id="goods_type_quantity" >
    <span v-for="(error, index) in errors.goods_type_quantity" :key="index" class="text-danger">{{ error }}</span>
  </div>
</div>
<div class="col-12" v-if="form.is_later === '1'">
  <div class="mb-3">
    <label for="dispatch-datepicker" class="form-label">{{$t("date")}}</label>
    <flat-pickr :placeholder="$t('select_date')" v-model="form.trip_start_time" :config="dateTimeConfig"
    class="form-control flatpickr-input" id="dispatch-datepicker"></flat-pickr>
  </div>
</div>

<!-- Vehicle Select -->
        <div v-if="vehicleTypes.length > 0" style="max-width:600px; overflow-x: auto;" class="col-12">
      <h4 class="card-title mb-3 flex-grow-1 mt-4">{{$t("select_vehicle")}}</h4>

          <div class="mb-3">
            <div class="d-flex mt-5">
              <div v-for="(vehicleType, index) in vehicleTypes" :key="index" class="select-checkbox-btn text-center">
                  <label :for="'vehicle_' + vehicleType.zone_type_id" class="select-checkbox-btn-wrapper">
                  <input
                  :id="'vehicle_' + vehicleType.zone_type_id"
                  name="types"
                  type="radio"
                  :value="vehicleType.zone_type_id"
                  v-model="form.vehicle_type"
                  class="select-checkbox-btn-input"
                  />
                  <span class="select-checkbox-btn-content">
              <a class="w-32 me-4 cursor-pointer">
            <div class="text-center mt-2 ms-4"><i class="bx bx-time-five mx-1"></i>{{ vehicleType.driver_time || '--' }}
            </div>
          <div class="w-32 h-32 flex-none image-fit rounded-circle">
            <img alt="" class="rounded-circle img-fluid" :src="vehicleType.vehicle_icon" />
          </div>
          <div class="text-center mt-2 amount">{{ vehicleType.total_with_currency }}</div>
          <div class="text-center mt-2">{{ vehicleType.name }}</div>
          </a>
          </span>
        </label>
    </div>
   </div>
  </div>
</div>

<div class="col-lg-12">
  <div v-if="form.vehicle_type" class="text-center">
  <button type="submit"  @click="makeBooking" class="btn btn-primary">{{$t("make_booking")}}</button>
  </div>
 </div>
 </div>
</div>

<!-- map  -->
<div class="col-12 col-lg-6">
<div class="mb-3 text-center m-auto">
<div id="map" style="height: 500px;"> {{$t("map_loading")}}</div>
</div>
</div>  
</div>
</FormValidation>

</form>
</BCardBody>
</BCard>
</BCol>
</BRow>
</BCard>
</template>

<style>
.custom-alert {
max-width: 600px;
float: right;
position: fixed;
top: 90px;
right: 20px;
}

.text-danger {
padding-top: 5px;
}

:root {
--primary: #222222;
--primary-hover: {{ $side -> value }};
}
/* payments */
.select-form{
position: relative;
width: 100%;
margin-bottom: 18px;
}

.select-checkbox-group {
display: flex;
align-items: center;
position: relative;
}
.select-checkbox-btn {
margin-right: 15px;
margin-bottom: 15px;
}
.select-checkbox-btn-wrapper {
display: flex;
align-items: center;
justify-content: center;
width: fit-content;
position: relative;
}
.select-checkbox-btn-input {
clip: rect(0 0 0 0);
-webkit-clip-path: inset(100%);
clip-path: inset(100%);
height: 1px;
overflow: hidden;
position: absolute;
white-space: nowrap;
width: 1px;
}
.select-checkbox-btn-input:checked + .select-checkbox-btn-content {
border-color: var(--primary);
color: var(--primary);
}
.select-checkbox-btn-input:checked + .select-checkbox-btn-content:before {
transform: scale(1);
opacity: 1;
background-color: var(--primary);
border-color: var(--primary);
}
.select-checkbox-btn-input:checked
+ .select-checkbox-btn-content
.select-checkbox-btn-icon,
.select-checkbox-btn-input:checked
+ .select-checkbox-btn-content
.select-checkbox-btn-label {
color: var(--primary);
}
.select-checkbox-btn-input:focus + .select-checkbox-btn-content {
border-color: var(--primary);
}
.select-checkbox-btn-input:focus + .select-checkbox-btn-content:before {
transform: scale(1);
opacity: 1;
}

.select-checkbox-btn-content {
display: flex;
flex-direction: column;
align-items: center;
justify-content: center;
width: 140px;
min-height: 140px;
border-radius: 10px;
border: 0.1rem solid #dfe2e6;
background-color: #fff;
transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s,
-webkit-box-shadow ease-in-out 0.15s;
cursor: pointer;
position: relative;
user-select: none;
appearance: none;
}
.select-checkbox-btn-content:before {
content: "";
position: absolute;
width: 22px;
height: 22px;
border: 0.1rem solid #bbc1e1;
background-color: #fff;
border-radius: 9999px;
top: 5px;
left: 5px;
opacity: 0;
transform: scale(0);
transition: 0.25s ease;
background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='192' height='192' fill='%23FFFFFF' viewBox='0 0 256 256'%3E%3Crect width='256' height='256' fill='none'%3E%3C/rect%3E%3Cpolyline points='216 72.005 104 184 48 128.005' fill='none' stroke='%23FFFFFF' stroke-linecap='round' stroke-linejoin='round' stroke-width='32'%3E%3C/polyline%3E%3C/svg%3E");
background-size: 12px;
background-repeat: no-repeat;
background-position: 50% 50%;
display: flex;
align-items: center;
justify-content: center;
}
.select-checkbox-btn-content:hover {
border-color: var(--primary);
}
.select-checkbox-btn-content:hover:before {
transform: scale(1);
opacity: 1;
}

.select-checkbox-btn-icon {
transition: 0.375s ease;
color: #3c3c3cc7;
}
.select-checkbox-btn-icon svg {
width: 50px;
height: 50px;
}

.select-checkbox-btn-label {
color: #3c3c3cc7;
transition: 0.375s ease;
text-align: center;
}
.marker {
transition: transform 0.5s ease-out;
}

.amount {
font-family: Arial, sans-serif; /* Ensure the font supports the rupee symbol */
font-size: 16px; /* Adjust the size as needed */
white-space: nowrap; /* Prevent text from wrapping */
letter-spacing: 0.1em; /* Adjust spacing if needed */
}
/* end */
</style>
