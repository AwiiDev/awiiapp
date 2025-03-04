<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch, computed ,onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import { FirebaseError } from 'firebase/app';
import { useI18n } from 'vue-i18n';

export default {
    data() {
        return {
            rightOffcanvas: false,
        };
    },
    components: {
        Layout,
        PageHeader,
        Head,
        Pagination,
        Multiselect,
        flatPickr,
        Link,
        search,
        searchbar,

    },
    props: {
        successMessage: String,
        alertMessage: String,
        zones: Object,
        firebaseConfig: Object,
        ongoing_rides: Object,
        types: Object,
        serviceLocations: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            transport_type : 'all',
            ride_status : 'all',
            is_bid_ride : null,
            zone_id : null,
            vehicle_type_id : null,
            is_paid : null,
        });
        const zones = ref(props.zones);
        const types = ref(props.types);
        const results = ref(props.ongoing_rides);
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };


        const filterData = () => {
            modalFilter.value = true;
        };


        const clearFilter = () => {
            filter.reset();
            modalFilter.value = false;
        };


        onMounted( async ()=> {
            try{
                const firebaseConfig = props.firebaseConfig;
                if (!firebase.apps.length) {
                    firebase.initializeApp(firebaseConfig);
                }
                const database = firebase.database();
                results.value.forEach((ride) => {
                    const tripRef = database.ref(`requests/${ride.id}`);
                    tripRef.on('value',function(snapshot){
                        const index = results.value.findIndex(data => data.id === ride);
                        if (index !== -1) {
                            const val =  snapshot.val();
                            if(val.accept !== 1){
                                results.value[index].driver_id = null;
                            }
                            if(val.driver_id){
                                results.value[index].driver_id = val.driver_id;
                            }
                            if(val.hasOwnProperty('modified_by_driver')){
                                results.value[index].is_driver_started = 1;
                            }
                            if(val.trip_arrived == 1){
                                results.value[index].is_driver_arrived = true;
                            }
                            if(val.trip_start == 1){
                                results.value[index].is_trip_start = true;
                            }
                            if(val.is_completed){
                                results.value[index].is_completed = true;
                                setTimeout(() => {
                                    results.value.splice(index, 1);
                                }, 2000);
                            }
                            if(val.is_cancelled || val.is_cancel){
                                results.value[index].is_cancelled = true;
                                setTimeout(() => {
                                    results.value.splice(index, 1);
                                }, 2000);
                            }
                        }
                    });
                })
                database,ref('requests').on('child_added', function (snapshot) {
                    const val = snapshot.val();
                    setTimeout(async () => {
                        if(val.hasOwnProperty('request_number')){
                            const response = await axios.get(`/ongoing-rides/find-ride/${dataId}`);
                            console.log(response.data);
                            results.prepend(response.data.result);
                        }
                    }, 2000);
                    
                })
            } catch (error) {
                console.error(t('error_initializing_firebase_or_fetching_settings'), error);
            }
        });
        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                const response = await axios.get(`/rides-request/cancel/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value[index] = response.data.request;
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('trip_cancelled_successfully'), 'success');
            } catch (error) {
                console.log(error);
                Swal.fire(t('error'), t('failed_to_cancel_trip'), 'error');
            }
        };

        const deleteModal = async (itemId) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You want to be cancel this ride!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, Cancel!",
                cancelButtonText: "Close",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await deleteData(itemId);
                    } catch (error) {
                        console.error(t('error_deleting_data'), error);
                        Swal.fire(t('error'), t('failed_to_cancel_the_data'), "error");
                    }
                }
            });
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
            }else if(trip.is_driver_started){
                return 'Accepted';
            }else if(!trip.is_later){
                return 'Searching';
            }else{
                return 'Upcoming'
            }
        };
        const editData = async (result) =>  {
            router.get(`/rides-request/view/${result.id}`); 
        };

        return {
            results,
            modalShow,
            deleteItemId,
            successMessage,
            alertMessage,
            filterData,
            deleteModal,
            closeModal,
            deleteData,
            dismissMessage,
            searchTerm,
            modalFilter,
            clearFilter,
            filter,
            zones,
            types,
            rideStatus,
            editData
        };
    },
};
</script>

<template>
    <Layout>

        <Head :title="$t('ongoing-rides')" />
        <PageHeader :title="$t('ongoing-rides')" :pageTitle="$t('ongoing-rides')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol md="3">
                            </BCol>
                            <!-- <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <BButton variant="danger" @click="rightOffcanvas = true">
                                        <i class="ri-filter-2-line me-1 align-bottom"></i>  {{$t("filters")}}
                                    </BButton>
                                </div>
                            </BCol> -->
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col"> {{$t("request_id")}}</th>
                                        <th scope="col"> {{$t("date")}}</th>
                                        <th scope="col"> {{$t("user_name")}}</th>
                                        <th scope="col"> {{$t("driver_name")}}</th>
                                        <th scope="col"> {{$t("transport_type")}}</th>
                                        <th scope="col"> {{$t("trip_status")}}</th>
                                        <th scope="col"> {{$t("payment_option")}}</th>
                                        <th scope="col"> {{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.request_number}}</td> 
                                        <td>{{ result.converted_created_at }}</td> 
                                        <td>{{ result.user_detail ? result.user_detail.name: '----' }}</td>       
                                        <td>{{ result.driver_detail ? result.driver_detail.name: '----' }}</td>   
                                        <td>
                                            <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
                                            <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
                                            <span v-else>{{ $t('all') }}</span>
                                        </td>
                                        <td>
                                            <BBadge :class="{
                                                'text-uppercase': true,
                                                'text-bg-success': rideStatus(result) === 'Completed' || rideStatus(result) === 'Accepted' || rideStatus(result) === 'Driver Started',
                                                'text-bg-danger': rideStatus(result) === 'Cancelled',
                                                'text-bg-info': rideStatus(result) === 'On Trip',
                                                'text-bg-warning': rideStatus(result) === 'Upcoming' || rideStatus(result) === 'Driver Arrived' || rideStatus(result) === 'Searching',
                                            }">{{ rideStatus(result) }} </BBadge>
                                        </td>
                                        <td>
                                            <BBadge :class="{
                                                'text-uppercase':true,
                                                'text-bg-success': result.is_paid,
                                                'text-bg-danger': !result.is_paid,
                                                }">{{ result.payment_opt == 1 ? 'Cash' : (result.payment_opt == 2 ? 'Wallet' : 'Card') }} </BBadge>
                                        </td>                             
                                        <td>
                                            <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <div class="dropdown-item" type="button" @click.prevent="editData(result)">
                                                        <i class=" bx bx-show-alt align-center text-muted me-2"></i>  {{$t("view")}}
                                                    </div>
                                                    <div class="dropdown-item" v-if="!result.is_cancelled&&!result.is_completed" type="button" @click.prevent="deleteModal(result.id)">
                                                        <i class=" bx bx-show-alt align-center text-muted me-2"></i>  {{$t("cancel")}}
                                                    </div>
                                                    <Link class="dropdown-item" v-if="!result.driver_id&&!result.is_cancelled&&!result.is_completed" type="button" :href="`ongoing-rides/assign/${result.id}`">
                                                        <i class=" bx bx-show-alt align-center text-muted me-2"></i>  {{$t("assign")}}
                                                    </Link>
                                                </div>
                                            </div>
                                        </td>
                                     </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                            <h5> {{$t("no_data_found")}}</h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                     </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <div>
            <!-- Success Message -->
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{
                        successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
                        aria-label="Close Success Message"></button>
                </div>
            </div>

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage
                    }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                        aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>

        <!-- filter -->
        <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('filters')" header-class="bg-light"
            body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BFrom action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <label for="datepicker-range" class="form-label text-muted text-uppercase fw-semibold mb-3"> {{$t("status")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="ride_status"
                                    id="all" value="all" v-model="filter.ride_status">
                                <label class="form-check-label" for="all"> {{$t("all")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="ride_status"
                                    id="completed" value="completed" v-model="filter.ride_status">
                                <label class="form-check-label" for="completed"> {{$t("completed")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="ride_status"
                                    id="cancelled" value="cancelled" v-model="filter.ride_status">
                                <label class="form-check-label" for="cancelled"> {{$t("cancelled")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="ride_status"
                                    id="upcoming" value="upcoming" v-model="filter.ride_status">
                                <label class="form-check-label" for="upcoming"> {{$t("upcoming")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="ride_status"
                                    id="ontrip" value="ontrip" v-model="filter.ride_status">
                                <label class="form-check-label" for="ontrip"> {{$t("on_trip")}}</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3"> {{$t("transport_type")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.transport_type">
                            <option value="all"> {{$t("all")}}</option>
                            <option value="taxi"> {{$t("taxi")}}</option>
                            <option value="delivery"> {{$t("delivery")}}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="zone-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3"> {{$t("zone")}}</label>

                        <Multiselect
                            v-model="filter.zone_id"
                            id="zone_id"
                            mode="tags"
                            :options="zones.map(zone => ({ value: zone.id, label: zone.name }))"
                            :close-on-select="false" 
                            :multiple="true"
                            :searchable="true"
                            :placeholder="$t('select_a_zone')"
                            :create-option="false">
                        </Multiselect>
                    </div>
                    <div class="mb-4">
                        <label for="vehicle_type"
                            class="form-label text-muted text-uppercase fw-semibold mb-3"> {{$t("vehicle_type")}}</label>

                        <Multiselect
                            id="vehicle_type_id"
                            mode="tags"
                            v-model="filter.vehicle_type_id"
                            :options="types.map(type => ({ value: type.id, label: type.name }))"
                            :close-on-select="false" 
                            :searchable="true"
                            :placeholder="$t('select_vehicle_type')"
                            :create-option="false">
                        </Multiselect>
                    </div>
                    <div class="mb-4">
                        <label for="payment-status-select" class="form-label text-muted text-uppercase fw-semibold mb-3"> {{$t("payment_status")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_status"
                                    id="not_paid" value=1 v-model="filter.is_paid">
                                <label class="form-check-label" for="not_paid"> {{$t("paid")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_status"
                                    id="paid" value=0 v-model="filter.is_paid">
                                <label class="form-check-label" for="paid"> {{$t("not_paid")}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="dispatch-type-select" class="form-label text-muted text-uppercase fw-semibold mb-3"> {{$t("ride_type")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="dispatch-type"
                                    id="normal" value=0 v-model="filter.is_bid_ride">
                                <label class="form-check-label" for="normal"> {{$t("regural")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="dispatch-type"
                                    id="bidding" value=1 v-model="filter.is_bid_ride">
                                <label class="form-check-label" for="bidding"> {{$t("bidding")}}</label>
                            </div>
                        </div>
                    </div>
                    <div>
                    </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" @click="clearFilter"class="w-100"> {{$t("clear_filter")}}</BButton>
                    <BButton type="submit" @click="filterData" variant="success" class="w-100">
                        {{$t("apply")}}
                    </BButton>
                </div>
                <!--end offcanvas-footer-->
            </BFrom>
        </BOffcanvas>
        <!--end offcanvas-->
        <!-- filter end -->

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
