<script>
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from "vue";
import { useI18n } from 'vue-i18n';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Layout from "@/Layouts/dispatchmain.vue";
import PageHeader from "@/Components/page-header.vue";

export default {
    components: {
        Layout,
        PageHeader,
        Multiselect,
    },
    props: {
        firebaseSettings: Object,
        map_key: String,
        service_location: Array,
        vehicle_type: Array,
        default_lat:String,
        app_for:String,
        default_lng:String,
    },
    setup(props) {
        const { t } = useI18n();
        const map = ref(null);
        // Reactive data for filters
        const selectedServiceLocations = ref(null);
        const selectedVehicleTypes = ref(null);
        const selectedDriverModes = ref([]);
        const drivers_list = ref([]);

        // Options for filters
        const serviceLocations = ref(props.service_location.map(loc => ({ value: loc.id, label: loc.name })));
        const vehicleTypes = ref(props.vehicle_type.map(type => ({ value: type.id, label: type.name })));

        // Map and related variables
        let driverMarkers = {};
        let infoWindow;

        const clearFilters = () => {
            selectedServiceLocations.value = null;
            selectedVehicleTypes.value = null;
            selectedDriverModes.value = [];
            fetchNearbyDrivers();
        }
        const applyFilters = () => {
            fetchNearbyDrivers();
        }
        // Initialize Map
        const initializeMap = async() => {
            if(!map.value){
                let mapElement = document.getElementById('map');
                map.value = new google.maps.Map(mapElement, {
                    center: { lat: parseFloat(props.default_lat), lng: parseFloat(props.default_lng) },
                    zoom: 15,
                });
            }

            infoWindow = new google.maps.InfoWindow();

            // Fetch drivers initially
            fetchNearbyDrivers();
        };

        // Fetch drivers and update markers
        const fetchNearbyDrivers = async() => {
            const driversRef = firebase.database().ref('drivers');

            driversRef.once('value', (snapshot) => {
                const drivers = snapshot.val();

                if (!drivers) return;

                // Remove existing markers
                for (let id in driverMarkers) {
                    if(driverMarkers[id]){
                        driverMarkers[id].setMap(null);
                    }
                }
                driverMarkers = {};
                drivers_list.value = [];

                Object.keys(drivers).forEach(driverId => {
                    const driver = drivers[driverId];
                    const driverGeoHash = driver.g;
                    const decodedDriver = decodeGeohash(driverGeoHash);

                    if (decodedDriver && driver.service_location_id) {
                        const driverLatLng = new google.maps.LatLng(decodedDriver.lat, decodedDriver.lon);
                        // Filter based on selected service location and vehicle types
                        let matchesServiceLocation = driver.service_location_id ? true : false;
                        if(selectedServiceLocations.value){
                            matchesServiceLocation = selectedServiceLocations.value == driver.service_location_id ?? false;
                        }

                        let matchesVehicleType = Array.isArray(driver.vehicle_types);
                        if(selectedVehicleTypes.value && Array.isArray(driver.vehicle_types) && driver.vehicle_types.length>0){
                            matchesVehicleType = (Array.isArray(driver.vehicle_types) && driver.vehicle_types.some(type => selectedVehicleTypes.value === type)) ?? false;
                        }

                        let active = selectedDriverModes.value.length>0 ? selectedDriverModes.value.includes("online") : true;
                        let onride = selectedDriverModes.value.length>0 ? selectedDriverModes.value.includes("onride") : true;
                        let offline = selectedDriverModes.value.length>0 ? selectedDriverModes.value.includes("offline") : true;

                        let vehicleTypeIconUrl="";
                        if(driver.hasOwnProperty('is_active') && driver.hasOwnProperty('is_available')){
                            if (driver.is_active == 1 && driver.is_available==true && active) {
                                vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
                            } else if (driver.is_active == 1 && driver.is_available==false && onride) {
                                vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
                            } else {
                                if(offline){
                                    vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
                                }
                            }
                        }
                        if (matchesServiceLocation && matchesVehicleType && vehicleTypeIconUrl.length > 0) {


                            // Create new marker
                            const newDriverMarker = new google.maps.Marker({
                                position: driverLatLng,
                                map: map.value,
                                icon: {
                                    url: vehicleTypeIconUrl,
                                    scaledSize: new google.maps.Size(30, 30),
                                },
                            });
                            drivers_list.value.push(driver);

                            newDriverMarker.addListener('mouseover', () => {
                                infoWindow.setContent(`<div><strong>${driver.name}</strong><br>Mobile: ${props.app_for == 'demo' ? '**********' :driver.mobile}</div>`);
                                infoWindow.open(map.value, newDriverMarker);
                            });

                            newDriverMarker.addListener('mouseout', () => {
                                infoWindow.close();
                            });

                            driverMarkers[driver.id] = newDriverMarker;
                        }
                    }
                });
            });
        };

        // Decode geohash to latitude and longitude
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

        // Watch filters and update map when they change
        watch([selectedServiceLocations, selectedVehicleTypes], () => {
            fetchNearbyDrivers();
        }),
        onMounted(async() => {

            const map_key = props.map_key;

            if (!document.querySelector(`script[src*="maps.googleapis.com/maps/api/js?key=${map_key}&libraries=places,visualization&loading=async"]`)) {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${map_key}&libraries=places,visualization&loading=async`;
                script.async = true;
                script.defer = true;

                script.onload = () => {
                    initializeMap();
                };

                script.onerror = () => {
                    console.error(t('google_maps_script_failed_to_load'));
                };

                document.head.appendChild(script);
            } else {
                // If script is already loaded, initialize the heatmap directly
                initializeMap();
            }

            var firebaseConfig = {
                apiKey: props.firebaseSettings['firebase_api_key'],
                authDomain: props.firebaseSettings['firebase_auth_domain'],
                databaseURL: props.firebaseSettings['firebase_database_url'],
                projectId: props.firebaseSettings['firebase_project_id'],
                storageBucket: props.firebaseSettings['firebase_storage_bucket'],
                messagingSenderId: props.firebaseSettings['firebase_messaging_sender_id'],
                appId: props.firebaseSettings['firebase_app_id'],
                measurementId: props.firebaseSettings['firebase_measurement_id'],
            };
            if(!firebase.apps.length){
                firebase.initializeApp(firebaseConfig);
            }
        });

        return {
            serviceLocations,
            vehicleTypes,
            selectedServiceLocations,
            selectedDriverModes,
            drivers_list,
            clearFilters,
            applyFilters,
            selectedVehicleTypes,
        };
    },
};
</script>



<template>
    <Layout>

        <Head :title="$t('gods-eye')" />
        <PageHeader :title="$t('gods-eye')" :pageTitle="$t('dispatch')" />
        <BRow>
    <BCol lg="12">
      <BCard no-body id="tasksList">
        <BCardHeader class="border-0">
          <h4>{{$t("filters")}}</h4>
          <div class="row">
            <div class="col-12 col-lg-3">
              <div class="mb-3">
                <label for="select_location" class="form-label">{{$t("service_location")}}</label>
                <Multiselect
                    v-model="selectedServiceLocations"
                    :options="serviceLocations"
                    label="label"
                    track-by="value"
                    multiple
                    close-on-select
                    :placeholder="$t('select_service_locations')"
                />
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="mb-3">
                <label for="select_driver_mode" class="form-label">{{$t("drivers")}}</label>
                <Multiselect 
                  v-model="selectedDriverModes"
                  id="select_driver_mode" 
                  mode="tags" 
                  :close-on-select="false"
                  :searchable="true" 
                  :create-option="false"
                  :options="[
                    { value: 'offline', label: $t('offline') },
                    { value: 'online', label: $t('online') },
                    { value: 'onride', label: $t('onride') },
                  ]"
                  :placeholder="$t('select_mode')"
                />
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="mb-3">
                <label for="vehicle_type" class="form-label">{{$t("vehicle_types")}}</label>
                <Multiselect
                    v-model="selectedVehicleTypes"
                    :options="vehicleTypes"
                    label="label"
                    track-by="value"
                    multiple
                    close-on-select
                    :placeholder="$t('select_vehicle_types')"
                />
              </div>
            </div>
            <div class="col-12 col-lg-6 d-flex gap-1">
              <BButton type="button" variant="success" class="btn btn-md" @click="applyFilters">{{$t("apply")}}</BButton>
              <BButton type="button" variant="danger" class="btn btn-md" @click="clearFilters">{{$t("clear")}}</BButton>
            </div>
          </div>
        </BCardHeader>
        <BCardBody class="border border-dashed border-end-0 border-start-0">
            <BRow>
                <BCol lg="4">
                    <div v-if="drivers_list.length>0" class="overflow-auto" style="height: 400px;">
                        <BCard class="col-lg-12" v-for="(driver) in drivers_list">
                            <div class="accordion-item border-0">
                                <div class="accordion-header" id="headingThree">
                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#" aria-expanded="false" aria-controls="collapseThree">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 border border-4 rounded-circle" style="border: var(--landing_header_act_text);">
                                                <img :src="driver.profile_picture" alt="" class="avatar-md rounded-circle">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="fs-15 mb-1 fw-semibold" style="color: var(--landing_header_act_text);">{{ driver.name }}</h4>
                                                <h6 class="fs-15 mb-1 fw-normal" style="color: var(--landing_header_act_text);">{{ app_for == 'demo' ? "*********" :  driver.mobile }}</h6>
                                                <h6 class="fs-15 mb-1 fw-normal">{{ driver.rating }} <i class="ri-star-fill text-warning align-bottom me-1" /></h6>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </BCard>
                    </div>
                    <div v-else style="height: 400px;">
                        <img src="/image/map/no-drivers.png" style="width: 100%;" alt="No Drivers Found">
                    </div>
                </BCol>
                <BCol lg="8">
                    <div id="map" style="height: 400px;">{{$t("map_loading")}}</div>
                </BCol>
            </BRow>
        </BCardBody>
      </BCard>
    </BCol>
  </BRow>
   </Layout>
</template>
<style>
.custom-alert {
    max-width: 600px;
    float: right;
    position: fixed;
    top: 90px;
    right: 20px;
}
.rtl .custom-alert {
  max-width: 600px;
  float: left;
  top: -300px;
  right: 10px;
}
@media only screen and (max-width: 1024px) {
  .custom-alert {
  max-width: 600px;
  float: right;
  position: fixed;
  top: 90px;
  right: 20px;
}
.rtl .custom-alert {
  max-width: 600px;
  float: left;
  top: -230px;
  right: 10px;
}
}

</style>
