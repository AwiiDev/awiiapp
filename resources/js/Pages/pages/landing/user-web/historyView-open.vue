<script>
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import axios from 'axios';
import Layout from "@/Layouts/main.vue";
import { Link, Head, useForm } from '@inertiajs/vue3';
import { ref, computed ,onMounted} from "vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import UserWebMenu from "@/Components/UserWebMenu.vue";
import Pagination from "@/Components/Pagination.vue";
import { useI18n } from 'vue-i18n';
import  { initI18n } from "@/i18n";
import Swal from "sweetalert2";
import L from "leaflet";
import 'leaflet-routing-machine';
import "leaflet/dist/leaflet.css";
import polyline from '@mapbox/polyline';

export default {
  props: {
    successMessage: String,
        alertMessage: String,

        serviceLocations: {
            type: Object,
            required: true,
        },
        pick_icon: String,
        drop_icon: String,
        stop_icon: String,
        rejected_drivers: Object,
        firebaseConfig: Object,

        request: Object,
        user: Object,
  },
  data() {
    return {
    //     selectedStatus: "", // To hold the selected filter option
    //   filteredRides: [], // Filtered rides based on the selected filter
    //   SearchQuery: '',
      searchTerm: '',
    };
  },
  components: {
    Layout,
    Multiselect,
    flatPickr,
    Head,
    searchbar,
    UserWebMenu,
    Pagination,
    Link,
    useForm
  },
  setup(props) {
        const { t } = useI18n();
        const result = ref(props.request);
        const rejected_drivers = ref(props.rejected_drivers);
        const modalShow = ref(false);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };
        const stops = ref(props.request ? props.request.requestStops.data :  []);

        const pickupMarker = ref(null);
        const dropMarker = ref(null);
        const stopMarker = ref([]);
        const calculateZoomLevel = (bounds) => {
            const GLOBE_WIDTH = 256; // a constant in Google's map projection
            const pixelWidth = document.getElementById('map').offsetWidth;
            const maxZoom = 21;

            const west = bounds.getSouthWest().lng();
            const east = bounds.getNorthEast().lng();

            let angle = east - west;
            if (angle < 0) {
                angle += 360;
            }

            return Math.floor(Math.log(pixelWidth * 360 / angle / GLOBE_WIDTH) / Math.LN2);
        };
        

        const initializeMap = async () => {
            map.value = L.map('map',{
                zoomControl: false,
                dragging: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                touchZoom: false,
            }).setView([result.value.pick_lat, result.value.pick_lng], 12);
            
            map.value.dragging.disable();
            map.value.scrollWheelZoom.disable();
            map.value.doubleClickZoom.disable();
            map.value.touchZoom.disable();

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map.value);

        
            const pickupIcon = L.icon({
                iconUrl: '/image/map/pickup.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            const dropIcon = L.icon({
                iconUrl: '/image/map/drop.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            pickupMarker.value = L.marker([result.value.pick_lat, result.value.pick_lng], { icon: pickupIcon, draggable:false } ).addTo(map.value);
            dropMarker.value = L.marker([result.value.drop_lat, result.value.drop_lng], { icon:  dropIcon, draggable:false } ).addTo(map.value);

            stops.value.forEach( (stop,index)=> {
                const stopIcon = L.icon({
                    iconUrl: '/image/map/'+index+'.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
                const marker = L.marker([stop.latitude, stop.longitude], { icon:  stopIcon, draggable:false } ).addTo(map.value);
                stopMarker.value[index] = marker;
            })
            
            if (result.value.poly_line) {
                const decodedCoordinates = polyline.decode(result.value.poly_line);
            
                let routePolyline = L.polyline(decodedCoordinates, { color: 'blue' }).addTo(map.value);
                map.value.fitBounds(routePolyline);
            }
        };

        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                const response = await axios.get(`/rides-request/cancel/${dataId}`);
                result.value = response.data.request;
                modalShow.value = false;
                Swal.fire(t('success'), t('trip_cancelled_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_cancel_trip'), 'error');
            }
        };
        const rideStatus = (trip) => {
            if(trip.is_cancelled){
                return 'Cancelled';
            }else if(trip.is_completed){
                return 'Completed';
            }else if(trip.is_trip_start){
                return 'On Trip';
            }else if(trip.is_driver_arrived){
                return 'Driver Arrived';
            }else if(trip.is_later && trip.is_driver_started){
                return 'Driver Started';
            }else if(trip.driver_id){
                return 'Accepted';
            }else if(!trip.is_later){
                return 'Searching';
            }else{
                return 'Upcoming'
            }
        };
        const formatDateTime = () => {
            const now = new Date();
            const options = { 
                weekday: 'short', 
                day: 'numeric', 
                month: 'short', 
                year: 'numeric' 
            };
            return now.toLocaleDateString('en-US', options);
        }

        const deleteModal = async (itemId) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You want to be cancel this ride!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, Cancel it!",
                cancelButtonText: "Close",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await deleteData(itemId);
                    } catch (error) {
                        console.error(t('error_deleting_data'), error);
                        Swal.fire(t('error'), t('failed_to_delete_the_data'), "error");
                    }
                }
            });
        };

        onMounted( async ()=> {
            if (!result.value.is_cancelled && !result.value.is_completed) {
        try {
            const firebaseConfig = props.firebaseConfig;
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }
            const database = firebase.database();
            const tripRef = database.ref(`requests/${result.value.id}`);
            tripRef.on('value', (snapshot) => {
                const val = snapshot.val();
                if (val) {
                    if (val.hasOwnProperty('is_completed')) {
                        result.value.is_completed = true;
                    }
                    if (val.accept !== 1) {
                        result.value.driver_id = null;
                    }
                    if (val.driver_id) {
                        fetchDriver(val.driver_id);
                    }
                    if (result.value.is_later && val.hasOwnProperty('modified_by_driver')) {
                        result.value.is_driver_started = 1;
                    }
                    if (val.trip_arrived == 1) {
                        result.value.is_driver_arrived = true;
                        if (!result.value.converted_arrived_at) {
                            result.value.converted_arrived_at = formatDateTime();
                        }
                    }
                    if (val.trip_start == 1) {
                        result.value.is_trip_start = true;
                        if (!result.value.converted_trip_start_time) {
                            result.value.converted_trip_start_time = formatDateTime();
                        }
                    }
                    if (val.is_cancelled || val.is_cancel) {
                        result.value.is_cancelled = true;
                        setTimeout(() => {
                            window.location.reload();
                         }, 2000);
                        
                    }
                }
            });
        } catch (error) {
            console.error('Error initializing Firebase or fetching settings:', error);
        }
    }
        });

    onMounted(async() => {
      await initI18n('en');
      await initializeMap();
    })
        return {
            result,
            modalShow,
            successMessage,
            alertMessage,
            rejected_drivers,
            deleteModal,
            closeModal,
            stops,
            deleteData,
            rideStatus,
            dismissMessage,
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
                            <h3>{{$t("view_details")}}</h3>
                        </BCardHeader>
                        <BCardBody class="border border-dashed border-end-0 border-start-0">  
                            <BRow>
                                <BCol lg="12">
                                    <!-- <BCard no-body id="tasksList">
                                        <BCardBody> -->
                                            <div class="row">  
                                                <div class="mt-3"></div> 
                                                <div class="col-sm-12">
                                                    <!-- <h5> {{$t("map_view")}}</h5> -->

                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div id="map" style="height: 400px;"></div>
                                                        </div>
                                                        <div class="text-center p-3 fs-18"><span class=" badge bg-success-subtle text-success p-3">{{$t("ride_otp")}} : <strong>{{ result.ride_otp ?? '-' }}</strong></span></div>
                                                        
                                                    </div>

                                                   <!-- {{$t("ride_otp")}}     {{ result.ride_otp ?? '-' }} -->

                                                </div>
                                            </div>
<!-- 
                                        </BCardBody>
                                    </BCard> -->
                                </BCol>
                            </BRow>
                        </BCardBody>
                    </BCard>
                </BCol>
            </BRow>

<!-- details cards  -->
<BRow>
    <BCol lg="4" v-if="result.driverDetail">
    <BCard class="card-animate border border-dashed border-primary">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-md flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <img :src="result.driverDetail?.data?.profile_picture" class="img-fluid avatar-md rounded-circle" alt="">
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{ result.driverDetail ? result.driverDetail.data?.name : '-' }}</h6>
                    <p class="text-muted fs-12 mb-0">
                        <i class="mdi mdi-circle-medium text-success fs-15 align-middle"></i> {{ result.driverDetail ? result.driverDetail.data?.mobile : '-' }}
                    </p>
                    <p class="text-muted fs-12 mb-0">
                        <i class="mdi mdi-circle-medium text-success fs-15 align-middle"></i> {{ result.driverDetail ? result.driverDetail.data?.email : '-' }}
                    </p>
                </div>
            </div>
    
        </BCardBody>
    </BCard>
    </BCol>
    <BCol lg="4">
    <BCard class="card-animate border border-dashed border-primary">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <i class="ri-map-pin-fill icon-dual-success fs-18"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("pickup_location")}}</h6>
                    <p class="text-muted fs-14 mb-2 mt-2">
                    {{ result.pick_address }}
                    </p>
                </div>
            </div>
    
        </BCardBody>
    </BCard>
    </BCol>
    <BCol lg="4">
    <BCard class="card-animate border border-dashed border-primary">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <i class="ri-map-pin-fill icon-dual-danger fs-18"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("drop_location")}}</h6>
                    <p class="text-muted fs-14 mb-2 mt-2">
                    {{ result.drop_address }}
                    </p>
                </div>
            </div>
    
        </BCardBody>
    </BCard>
    </BCol>
    <BCol lg="6">
    <BCard class="card-animate border border-dashed border-primary">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <i class="ri-route-fill icon-dual-primary fs-18"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("trip_status")}}{{ result.is_bid_ride ? $t('bidding') : '' }}</h6>
                    <p class="text-muted fs-14 mb-2 mt-2">
                        <div>
                            <p class="text-dark">{{ rideStatus(result) }}</p>
                            <BButton class="btn btn-danger btn-md" v-if="!result.is_cancelled&&!result.is_completed" type="button" @click.prevent="deleteModal(result.id)">
                                <i class=" bx bx-show-alt align-center text-muted me-2"></i>  {{$t("cancel")}}
                            </Bbutton>
                        </div>
                    </p>
                </div>
                <div class="flex-grow-1 ms-3" v-if="result.rental_package_name !== '-'">
                    <h6 class="fs-14 mb-1">{{$t("rental_pack")}}</h6>
                    <p class="text-muted fs-14 mb-2 mt-2">
                        <div>
                            <p class="text-dark">{{ result.rental_package_name }}</p>
                        </div>
                    </p>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("ride_type")}}</h6>
                    <p class="text-muted fs-14 mb-2 mt-2">
                        <div>
                            <p class="text-dark">{{ dispatch_type }}</p>
                        </div>
                    </p>
                    <h6 v-if="result.is_round_trip" class="fs-14 mb-1">{{$t("return_time")}}</h6>
                    <p v-if="result.is_round_trip" class="text-muted fs-14 mb-2 mt-2">
                        <div>
                            <p class="text-dark">{{ result.return_time }}</p>
                        </div>
                    </p>
                </div>
                <div class="flex-shrink-0 text-end">
                    <h6 class="mb-1 text-success">{{ result.vehicle_type_name ?? '-' }}</h6>
                    <p class="text-muted fs-13 mb-0">
                        <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
                        <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
                        <span v-else>{{ $t('all') }}</span>
                    </p>
                </div>
            </div>
            
    </BCardBody>
    </BCard>
    </BCol>
    <BCol lg="4" v-if="stops.length>0">
    <BCard class="card-animate border border-dashed border-primary">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <i class="ri-map-pin-fill icon-dual-danger fs-18"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("location")}}</h6>
                    <p class="text-muted fs-14 mb-2 mt-2" v-for="(stop, index) in stops">
                    {{ stop.address }}
                    </p>
                </div>
            </div>
    
        </BCardBody>
    </BCard>
    </BCol>
</BRow>
<BRow>
    <!-- details end -->

    <!-- <div class="text-center ">
        <a :href="`tel:{{result.driverDetail ? result.driverDetail.data?.mobile : '-'}}`"  type="button" class="btn btn-success btn-label waves-effect waves-light fs-18"><i class="ri-phone-fill label-icon align-middle fs-16 me-2"></i> {{$t("call")}}</a>
    </div> -->
    <div class="text-center">
    <a
        v-if="result.driverDetail && result.driverDetail.data?.mobile"
        :href="`tel:${result.driverDetail.data.mobile}`"
        type="button"
        class="btn btn-success btn-label waves-effect waves-light fs-18"
    >
        <i class="ri-phone-fill label-icon align-middle fs-16 me-2"></i> {{ $t("call") }}
    </a>
    <span v-else class="text-muted"></span>
</div>

    <!-- bill  -->
<BCol lg="12" v-if="result.requestBill">
<BCard>
    <BCardBody>
        <h4 class="text-center">{{ $t("fare_breakup") }}</h4>
        <div class="border-top border-top-dashed mt-2">
        <table class="table table-borderless table-nowrap align-middle mb-0 m-auto" style="width:250px">
            <tbody>
                <tr>
                    <td>{{$t("base_price")}}</td>
                    <td class="text-end">{{ result.requestBill.data.base_price }}</td>
                </tr>
                <tr>
                    <td>{{$t("distance_price")}}</td>
                    <td class="text-end">{{ result.requestBill.data.distance_price }}</td>
                </tr>
                <tr>
                    <td>{{$t("time_price")}}</td>
                    <td class="text-end">{{ result.requestBill.data.time_price }}</td>
                </tr>
                <tr v-if="result.requestBill.data.cancellation_fee > 0" >
                    <td>{{$t("cancellation_fee")}}</td>
                    <td class="text-end">{{ result.requestBill.data.cancellation_fee }}</td>
                </tr>
                <tr>
                    <td>{{$t("service_tax")}}</td>
                    <td class="text-end">{{ result.requestBill.data.service_tax }}</td>
                </tr>
                <tr>
                    <td>{{$t("convenience_fee")}}</td>
                    <td class="text-end">{{ result.requestBill.data.admin_commision }}</td>
                </tr>
                <tr v-if="result.requestBill.data.promo_discount > 0" >
                    <td>{{$t("promo_discount")}}</td>
                    <td class="text-end">{{ result.requestBill.data.promo_discount }}</td>
                </tr>
                <tr class="border-top border-top-dashed fs-15">
                    <th scope="row">{{$t("total")}}</th>
                    <th class="text-end">{{ result.requestBill.data.total_amount }}</th>
                </tr>
            </tbody>
        </table>
        <!--end table-->
    </div>
    </BCardBody>
</BCard>
</BCol>
</BRow>

                   
    </BCard>
</template>
<style>
.address {
    display: inline-block;
    width: 200px;
    white-space: nowrap;
    overflow: hidden !important;
    text-overflow: ellipsis;
}
.filter-container {
  margin-bottom: 1rem;
}
.leaflet-control-container .leaflet-routing-container-hide {
  display: none;
}
.profile-timeline .accordion-item::before {
    content: "";
    border-left: 2px dashed var(--vz-border-color);
    position: absolute;
    height: 100%;
    left: 46px;
}
</style>

