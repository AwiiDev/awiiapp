<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import getChartColorsArray from "@/common/getChartColorsArray";
import { useI18n } from 'vue-i18n';
import { BCard, BCardBody } from 'bootstrap-vue-next';

export default {
 
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
        getChartColorsArray,
    },
    props: {
        successMessage: String,
        alertMessage: String,
        driver: Object,
        currency: Array,
        driver_date: String,
        completed_ride_count: Number,
        canceled_ride_count: Number,
        app_for:String,
        map_key: String,
        earnings_data: Object,
        default_lat:String,
        default_lng:String,
        trip_data: Object,
        firebaseSettings:Object,
        earningsChartData:Object,
        disable_options:String,

        tripsChartData: {
            type: Object,
            default: () => ({
                months: [],
                completed: [],
                cancelled: [],
            }),
        },


    },
    setup(props) {
        const map = ref(null);
        const { t } = useI18n();
        const selectedServiceLocations = ref([]);
        const selectedVehicleTypes = ref([]);

        const disable_options = props.disable_options;

console.log("disable_options");
console.log(disable_options);

        // Calculate the maximum value from the earnings data
        const maxValue = Math.max(...(props.earningsChartData.values || []));
        
        // Set a margin by multiplying with a factor (e.g., 1.2) and round to 2 decimal places
        const maxYValue = (maxValue * 1.2).toFixed(2);
    // Earning Chart Data and Options
    const earning = ref([
      {
        name: t('earnings'),
        data: props.earningsChartData.values || [],
      },
    ]);

    const earningOptions = ref({
      chart: {
        height: 100,
        type: "area",
        toolbar: "false",
      },
      dataLabels: {
        enabled: false,
      },
      stroke: {
        curve: "smooth",
        width: 3,
      },
      xaxis: {
        categories: props.earningsChartData.months || [],
      },
      yaxis: {
        labels: {
          formatter: function (value) {
             return value.toFixed(2);
          },
        },
        tickAmount: 5,
        min: 0,
        max: Number(maxYValue), // Use the computed max value
      },
      colors: getChartColorsArray('[ "--vz-success"]'),
      fill: {
        opacity: 0.5,
        colors: ["#0AB39C", "#F06548"],
        type: "solid",
      },
    });

    
    
        const mobileFromUser = (user) => {
            if(props.app_for && props.app_for == "demo"){
                return "***********";
            }
            return user.mobile
        }

        const emailFromUser = (user) => {
            if(props.app_for && props.app_for == "demo"){
                return "***********";
            }
            return user.mobile
        }

    
    
    const series = ref([
            {
                name: t('completed'),
                type: "bar",
                data: props.tripsChartData.completed || [],
            },
            {
                name: t('cancelled'),
                type: "bar",
                data: props.tripsChartData.cancelled || [],
            },
        ]);

        const tripOptions = ref({
            chart: {
                height: 374,
                type: "line",
                toolbar: {
                    show: false,
                },
            },
            stroke: {
                curve: "smooth",
                dashArray: [0, 3, 0],
                width: [0, 1, 0],
            },
            fill: {
                opacity: [1, 1, 1],
            },
            markers: {
                size: [0, 4, 0],
                strokeWidth: 2,
                hover: {
                    size: 4,
                },
            },
            xaxis: {
                categories: props.tripsChartData.months || [],
                axisTicks: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
            },
            grid: {
                show: true,
                xaxis: {
                    lines: {
                        show: true,
                    },
                },
                yaxis: {
                    lines: {
                        show: false,
                    },
                },
                padding: {
                    top: 0,
                    right: -2,
                    bottom: 15,
                    left: 10,
                },
            },
            legend: {
                show: true,
                horizontalAlign: "center",
                offsetX: 0,
                offsetY: -5,
                markers: {
                    width: 9,
                    height: 9,
                    radius: 6,
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 0,
                },
            },
            plotOptions: {
                bar: {
                    columnWidth: "30%",
                    barHeight: "70%",
                },
            },
            colors: getChartColorsArray('["--vz-success", "--vz-danger"]'),
        });







 const initializeMap = () => {
        const map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: parseFloat(props.default_lat), lng: parseFloat(props.default_lng) },
            zoom: 15,
        });

        let driverMarker = null;

        const driversRef = firebase.database().ref('drivers/driver_' + props.driver.id);

        // Listen for location changes in Firebase
        driversRef.on('value', (snapshot) => {
            const driverData = snapshot.val();

            if (driverData && driverData.l && driverData.l[0] && driverData.l[1]) {
                const position = { lat: driverData.l[0], lng: driverData.l[1] };
// console.log("ddddd");
// console.log(driverData.is_active);
                // Determine the correct icon URL based on driver's status
                let vehicleTypeIconUrl;
                if (driverData.is_active==1 && driverData.is_available) {
                    vehicleTypeIconUrl = `/image/map/${driverData.vehicle_type_icon}-online.png`;
                } else if (driverData.is_active==1 && !driverData.is_available) {
                    vehicleTypeIconUrl = `/image/map/${driverData.vehicle_type_icon}-onride.png`;
                } else {
                    vehicleTypeIconUrl = `/image/map/${driverData.vehicle_type_icon}.png`;
                }

                // If marker doesn't exist, create one
                if (!driverMarker) {
                    driverMarker = new google.maps.Marker({
                        position: position,
                        map: map,
                        icon: {
                            url: vehicleTypeIconUrl,
                            scaledSize: new google.maps.Size(30, 30),
                        },
                        title: 'Driver Location',
                    });
                } else {
                    // If marker already exists, update its position and icon
                    driverMarker.setPosition(position);
                    driverMarker.setIcon({
                        url: vehicleTypeIconUrl,
                        scaledSize: new google.maps.Size(30, 30),
                    });
                }

                // Optionally, center the map on the driver's new position
                map.setCenter(position);
            }
        });
    };


        const searchTerm1 = ref("");
        const searchTerm2 = ref("");
        const filter1 = useForm({ all: "", locked: "" });
        const filter2 = useForm({ all: "", locked: "" });

        const ride_count = props.completed_ride_count;

        const cancel_ride_count = props.canceled_ride_count;


// console.log(props.completed_ride_count);

        const form = ref({
            amount: '',
            operation: 'add', // Default to 'add'
        });
        const validationMessage = ref('');
        const isAmountValid = ref(false);


        onMounted(() => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${props.map_key}&libraries=visualization`;
            script.async = true;
            script.defer = true;

            script.onload = initializeMap;
            document.head.appendChild(script);


            script.onerror = () => {
                console.error(t('google_maps_script_failed_to_load'));
            };
          
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
                if(firebase.apps.length == 0){
                    firebase.initializeApp(firebaseConfig);
                }
        });

        const results2 = ref([]);
        const paginator2 = ref({});
        const requests = ref([]); // Spread the results to make them reactive

        const fetchRequestDatas = async (page = 1) => {
                const params = filter2.data();
                params.search = searchTerm2.value;
                params.page = page;
                const response = await axios.get(`/approved-drivers/request/list/${props.driver.id}`, { params });
                requests.value = response.data.requests;
                paginator2.value = response.data.paginator;

        };


        const handlePageChanged2 = async (page) => {
            fetchRequestDatas(page);
        };

        return {
            form,
            validationMessage,
            searchTerm1,
            searchTerm2,
            mobileFromUser,
            emailFromUser,
            results2,
            paginator2,
            handlePageChanged2,
            ride_count,
            cancel_ride_count,
            map,
            selectedServiceLocations,
            selectedVehicleTypes,
            fetchRequestDatas,
            requests,
            earning,
            earningOptions,
            series,
            tripOptions,
            disable_options

        };
    },
    mounted() {
        this.fetchRequestDatas();
    },
};
</script>


<template>
    <Layout>

        <Head title="Driver Profile" />
        <PageHeader :title="$t('driver_profile')" :pageTitle="$t('driver_profile')" pageLink="/fleet-drivers"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <div class="row">
                            <div class="col-sm-4 mt-3 profile-border">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <img class="rounded-circle avatar-xl" alt="200x200" :src="driver.profile_picture">
                                    </div>
                                    <div class="ms-4">
                                        <h5>{{driver.name}}</h5> 
                                    </div>
                                 </div>                                
                            </div>
                            <div class="col-sm-4 mt-4 profile-border">                               
                                <div class=" d-flex align-items-center ">
                                    <i class=" ri-phone-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{mobileFromUser(driver)}}</span>
                                </div>                                
                                <div class=" d-flex align-items-center ">
                                    <i class="ri-mail-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{emailFromUser(driver)}}</span>
                                </div>  
                                <div class=" d-flex align-items-center ">
                                    <i class="  ri-logout-box-r-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{driver_date}}</span>
                                </div>  
                            </div>
                            <div class="col-sm-4 mt-3 ">
                                <div class="d-flex align-items-center ">
                                    <div>
                                        <img class="rounded-circle avatar-xl" alt="200x200" :src="driver.vehicle_type_image">

                                    </div>
                                    <div class="ms-1">
                                        <h5>{{driver.vehicle_type_name}}</h5> 
                                        <p>{{driver.car_make_name}}</p>
                                        <p>{{driver.car_model_name}}</p>
                                        <p>{{driver.car_number}}</p>
                                    </div>
                                 </div>                                
                            </div>
                        </div>
                        <div class="border-bottom mt-4"></div>
                        <div>
                            <!-- Nav tabs -->
                                <ul class="nav nav-tabs  mt-4" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#driver-profile" role="tab" aria-selected="false">
                                            {{$t("driver_profile")}}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#request-list" role="tab" aria-selected="false">
                                            {{$t("request_list")}}
                                        </a>
                                    </li>
                                </ul>
                        </div>

                    </BCardHeader>
                </BCard>
                        <!-- Tab panes -->
                        <div class="tab-content  text-muted">
                            <div class="tab-pane active  p-3" id="driver-profile" role="tabpanel">                                            
                                <BCard>
                                    <BCardBody>
                                        <h5 class="mb-4 mt-4">{{$t("general_report")}}</h5>

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="row  row-cols-lg-4 row-cols-1">
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="ri-group-line" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                                <i class=" bx bx-car text-success icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ trip_data.completed_today }} </h1>
                                                                <h5 class="card-text text-muted">{{$t("today_trips")}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="ri-group-line" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-success icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ earnings_data.today_total }}</h1>
                                                                <h5 class="card-text text-muted">{{$t("today_earnings")}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="ri-group-line" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                                                <i class=" bx bx-car text-primary icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ trip_data.completed }}</h1>
                                                                <h5 class="card-text text-muted">{{$t('total_trips')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="bx bx-user-check" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-primary icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ earnings_data.overall_total }}</h1>
                                                                <h5 class="card-text text-muted">{{$t('total_earnings')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="ri-group-line" style="font-size: 30px;color:#91c714"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                                                                <i class=" bx bx-car text-danger icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ trip_data.cancelled_today }}</h1>
                                                                <h5 class="card-text text-muted">{{$t('today_cancelled')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        
                                                    </div><!-- end row -->
                                                </div><!-- end col -->
                                            </div><!-- end row -->

                                            <h5 class="mb-4 mt-4">{{$t('driver_location')}}</h5>
                                             <!-- map  -->
                                             <div class="col-12 col-lg-12">
                                            <div class="mb-3 text-center m-auto">
                                            <div id="map" style="height: 500px;"> {{$t('map_loading')}}</div>
                                            </div>
                                            </div>  

                                            <h5 class="mb-4 mt-4">{{$t('earnings')}}</h5>
                                            <div class="row">
                                                <div class="col-sm-6 col-md-12 col-lg-12 col-xl-6">
                                                <apexchart
                                                    class="apex-charts"
                                                    height="350"
                                                    dir="ltr"
                                                    :series="earning"
                                                    :options="earningOptions"
                                                ></apexchart>           
                                             </div>
                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-6">
                                                    <div class="row  row-cols-lg-3 row-cols-1">
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="bx bx-rupee" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-success icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ currency[0] }} {{ earnings_data.today_total }}</h3>
                                                                </div>                                                                
                                                                <h5 class="card-text text-muted">{{$t('today_earnings')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-rupee" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-primary icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ currency[0] }} {{ earnings_data.admin_commission }}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t('admin_commission')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-rupee" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-warning icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ currency[0] }} {{ earnings_data.driver_commission }}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t('drivers_earnings')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-rupee" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-success icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ earnings_data.overall_cash }}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t('by_cash')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-rupee" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-primary icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ earnings_data.overall_wallet }}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t('by_wallet')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-credit-card" style="font-size: 30px;color:#91c714"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-credit-card text-warning icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="border p-1 rounded ms-3">{{ earnings_data.overall_card }}</div>
                                                                </div>
                                                                <!-- <div class="d-flex">
                                                                    <i class="bx bx-rupee" style="font-size: 25px;"></i>
                                                                    <h3 class="mb-1">0</h3>
                                                                </div> -->
                                                                <h5 class="card-text text-muted">{{$t('by_card')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->                                                        
                                                    </div><!-- end row -->
                                                    
                                                </div>
                                            </div>

                                            <h5 class="mb-4 mt-4">{{$t("trips")}}</h5>
                                            <div class="row">
                                                <div class="col-sm-6 col-md-12 col-xl-6 col-lg-6">
                                                <apexchart
                                                    class="apex-charts"
                                                    height="350"
                                                    dir="ltr"
                                                    :series="series"
                                                    :options="tripOptions"
                                                ></apexchart>
                                                </div>
                                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                                    <div class="row  row-cols-lg-2 row-cols-1">
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="las la-check-circle" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                        <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                            <i class=" bx bx-car text-success icon-lg"></i>
                                                                        </span>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ ride_count }}</h3>
                                                                </div>                                                                
                                                                <h5 class="card-text text-muted">{{$t("completed_trips")}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="bx bx-box" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                                                                <i class=" bx bx-car text-danger icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ cancel_ride_count}}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t("cancelled_trips")}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->                                                     
                                                    </div><!-- end row -->
                                                    
                                                </div>
                                            </div>

                                    </BCardBody>
                                </BCard>
                            </div>
                                        
                            <div class="tab-pane  p-3" id="request-list" role="tabpanel">
                                <BCard>
                                    <BCardBody>
                                        <div class="row  row-cols-lg-2 row-cols-1">
                                            <div class="col">
                                                <div class="card card-body border card-hover">
                                                    <div class="d-flex mb-4 align-items-center">
                                                        <div>
                                                            <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                            <!-- <i class="bx bxs-flag-checkered" style="font-size: 30px;color:#3160d8"></i> -->
                                                            <div class="avatar-sm flex-shrink-0">
                                                                <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                    <i class=" bx bx-car text-success icon-lg"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                        <h3 class="mb-1">{{ ride_count }}</h3>
                                                    </div>                                                                
                                                    <h5 class="card-text text-muted">{{$t("completed_rides")}}</h5>                                                                
                                                    <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col">
                                                <div class="card card-body border card-hover">
                                                    <div class="d-flex mb-4 align-items-center">
                                                        <div>
                                                            <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                            <!-- <i class="las la-ban" style="font-size: 30px;color:#3160d8"></i> -->
                                                            <div class="avatar-sm flex-shrink-0">
                                                                <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                                                    <i class=" bx bx-car text-danger icon-lg"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                        <h3 class="mb-1">{{ cancel_ride_count}}</h3>
                                                    </div>
                                                    <h5 class="card-text text-muted">{{$t("cancelled_rides")}}</h5>                                                                
                                                    <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                </div>
                                            </div><!-- end col -->
                                                                                                    
                                </div><!-- end row -->

                                        <div class="table-responsive">
                                            <table class="table align-middle position-relative table-nowrap">
                                                <thead class="table-active">
                                                    <tr>
                                                        <!-- <th scope="col">{{$t("s_no")}}</th> -->
                                                        <th scope="col">{{ $t("request_id") }}</th>
                                                        <th scope="col">{{ $t("date") }}</th>
                                                        <th scope="col">{{ $t("user_name") }}</th>
                                                        <th scope="col">{{ $t("driver_name") }}</th>
                                                        <th scope="col">{{ $t("trip_Status") }}</th>
                                                        <th scope="col">{{ $t("paid") }}</th>
                                                        <th scope="col">{{$t("payment_option")}}</th>
                                                        <!-- <th scope="col">{{ $t("action") }}</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody v-if="requests.length > 0">
                                                    <tr v-for="(request, index) in requests" :key="index">
                                                        <!-- <td>{{ index+1 }}</td> -->
                                                        <td>{{ request.request_number }}</td>
                                                        <td>{{ request.converted_created_at }}</td>
                                                        <td>{{ request.user_name }}</td>
                                                        <td>{{ request.driver_name }}</td>
                                                        <td>{{ request.trip_status }}</td>
                                                        <td>{{ request.trip_payment }}</td>
                                                        <!-- <td>{{ request.payment_opt }}</td>  -->
                                                        <td>
                                                            <BBadge :class="{
                                                                'text-uppercase':true,
                                                                'text-bg-success': request.is_paid,
                                                                'text-bg-danger': !request.is_paid,
                                                                }">{{ request.payment_opt == 1 ? 'Cash' : (request.payment_opt == 2 ? 'Wallet' : 'Card') }} </BBadge>
                                                        </td>                                       
                                                        <!-- <td>
                                                            <div class="dropdown">
                                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <a class="dropdown-item" href="#" @click.prevent="viewData(result)"><i class="bx bxs-edit-alt align-center text-muted me-2">
                                                                    </i>{{ $t("view") }}</a>
                                                                </div>
                                                            </div>
                                                        </td> -->
                                                    </tr>
                                                </tbody>
                                                <tbody v-else>
                                                    <tr>
                                                        <td colspan="7" class="text-center">
                                                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                                            <h5>{{$t("no_data_found")}}</h5>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                                        <Pagination :paginator="paginator2" @page-changed="handlePageChanged2" />
                                        </div>
                                    </BCardBody>
                                </BCard>
                                
                            </div>
                        </div>  
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
           <!-- Pagination -->
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
    float: right;
    position: fixed;
    top: 100px;
    right: 80px;
}
/* .text-danger {
    padding-top: 5px;
} */
.card-hover:hover{
    box-shadow: 0 5px 15px;
    transition: box-shadow 0.3s ease-in-out;
}
.ltr .profile-border{
    border-right:1px solid #e9ebec;
}
.rtl .profile-border{
    border-left:1px solid #e9ebec;
}


@media only screen and (max-width: 426px) {
    .profile-border{
        border-right:0px;
    }
}
</style>