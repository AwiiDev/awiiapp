<template>
    <Layout>
        <Head title="Zones" />
        <PageHeader :title="$t('create')" :pageTitle="$t('zone')"  pageLink="/zones" />
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="handleSubmit">
                            <div class="mb-3">
                                <label for="service_location" class="form-label">{{$t("service_location")}}</label>
                                <select class="form-select" id="service_location" v-model="form.service_location_id">
                                    <option value="" disabled>{{$t('select_service_location')}}</option>
                                    <option v-for="location in serviceLocations" :key="location.id" :value="location.id">{{ location.name }}</option>
                                </select>
                                <span v-if="form.errors.service_location_id" class="text-danger">{{ form.errors.service_location_id }}</span>
                            </div>
                            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified" role="tablist">
                                <BRow v-for="language in languages" :key="language.code">
                                <BCol lg="12">
                                    <li class="nav-item" role="presentation">
                                    <a class="nav-link" @click="setActiveTab(language.label)"
                                        :class="{ active: activeTab === language.label }" role="tab" aria-selected="true">
                                        {{ language.label }}
                                    </a>
                                    </li>
                                </BCol>
                                </BRow>
                            </ul>
                            <div class="tab-content text-muted" v-for="language in languages" :key="language.code">
                                <div v-if="activeTab === language.label" class="tab-pane active show" :id="`${language.label}`"
                                role="tabpanel">
                                <div class="col-sm-6 mt-3">
                                    <div class="mb-3">
                                    <label :for="`name-${language.code}`" class="form-label">{{$t("name")}}</label>
                                    <input type="text" class="form-control" :placeholder="$t('enter_name_in', {language: `${language.label}`})"
                                        :id="`name-${language.code}`" v-model="form.languageFields[language.code]"
                                        :required="language.code === 'en'">
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="select_unit" class="form-label">{{$t("select_unit")}}</label>
                                <select id="select_unit" class="form-select" v-model="form.unit">
                                    <option disabled value="">{{$t("choose_unit")}}</option>
                                    <option value=1>{{$t("kilo_meter")}}</option>
                                    <option value=2>{{$t("miles")}}</option>
                                </select>
                                <span v-if="form.errors.unit" class="text-danger">{{ form.errors.unit }}</span>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">{{$t("save")}}</button>
                            </div>
                            <div class="mb-3">
                                <span v-if="form.errors.coordinates" class="text-danger">{{ form.errors.coordinates }}</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-10 col-sm-10">
                <div class="card">
                    <div class="card-body">
                        <input
                            id="pac-input"
                            class="form-control"
                            type="text"
                            :placeholder="$t('search_for_a_city')"
                            ref="autocompleteInput"
                        />
                        <div id="map" style="height: 400px;position: relative;z-index: 10;"></div>
                        <div class="col-lg-1 col-md-2 col-sm-2" style="position:absolute;z-index: 50;top:20%;">
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="changeDrawingMode('grab')" class="align-center"> <i class="bx bxs-hand-up fs-16"></i> </BButton>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="changeDrawingMode('draw')" class="align-center"> <i class="bx bx-plus fs-16"></i> </BButton>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="removeSelectedPolygon" class="align-center"> <i class="bx bx-x fs-16"></i> </BButton>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="removeAllPolygons" class="align-center"> <i class="bx bx-trash fs-16"></i> </BButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert" id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Success</strong> - {{ successMessage }}
          <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
        </div>
      </div>
      <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert" id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Alert</strong> - {{ alertMessage }}
          <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
        </div>
      </div>
    </Layout>
</template>


<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import axios from "axios";
import { useI18n } from 'vue-i18n';
import { polygon } from 'leaflet';

export default {
    components: {
        Layout,
        PageHeader,
        Head,
    },
    props: {
        googleMapKey: String, // Define the googleMapKey prop
        successMessage: String,
        default_lat:String,
        default_lng:String,
        alertMessage: String,
        languages: Array,
    },
    setup(props) {
        const { googleMapKey } = props;
        const { t } = useI18n();
        const { languages, fetchData } = useSharedState(); // Destructure the shared state
        const activeTab = ref('English');

        const form = useForm({
            service_location_id: "",
            languageFields:  {},
            unit: "",


        });
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const serviceLocations = ref([]);
        let polygons = [];
        const drawingManager = ref(null);
        const selectedPolygon = ref(null);

        const fetchServiceLocations = async () => {
            const response = await axios.get('list');
            serviceLocations.value = response.data.results;
        };

        const initializeMap = () => {
            const map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: parseFloat(props.default_lat), lng: parseFloat(props.default_lng) },
                zoom: 10,
            });

            drawingManager.value = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: false,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON],
                },
                polygonOptions: {
                    fillColor: "#0000FF",
                    fillOpacity: 0.3,
                    strokeWeight: 1,
                    clickable: true,
                    editable: false,
                    zIndex: 1,
                },
            });
            drawingManager.value.setMap(map);

            google.maps.event.addListener(drawingManager.value, 'overlaycomplete', (event) => {
                if (event.type === google.maps.drawing.OverlayType.POLYGON) {

                    google.maps.event.addListener(event.overlay, 'click', () => {
                        if (selectedPolygon.value === event.overlay) {
                            return;
                        }
                        selectedPolygon.value = event.overlay;
                        polygons.forEach((poly) => {
                            poly.setOptions({ fillColor: "#0000FF" , editable: false, });
                        });

                        event.overlay.setOptions({ fillColor: "#00FF00" , editable: true, });
                    });

                    polygons.push(event.overlay);
                }
            });

            const autocompleteInput = document.getElementById('pac-input');
            const autocomplete = new google.maps.places.Autocomplete(autocompleteInput);

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    console.error(t('place_details_not_found_for_the_input'), autocompleteInput.value);
                    return;
                }

                map.setCenter(place.geometry.location);
                map.setZoom(12);
            });
        };

        const changeDrawingMode = (option) => {
            if(drawingManager.value){
                let mode = option == 'draw' ? google.maps.drawing.OverlayType.POLYGON : null;
                drawingManager.value.setDrawingMode(mode);
            }
        }

        const removeSelectedPolygon = () => {
            if(selectedPolygon.value){
                const index = polygons.indexOf(selectedPolygon.value);
                changeDrawingMode('grab');
                if (index > -1) {
                    polygons[index].setMap(null);
                    polygons.splice(index, 1);  // Remove from polygons array
                }
            }
        }
        const removeAllPolygons = () => {
            polygons.forEach(polygon => {
                polygon.setMap(null);
                selectedPolygon.value = null;
            });
            polygons = [];
        }
        const handleSubmit = async () => {
            const errors = validateForm();
            if (Object.keys(errors).length === 0) {
                try {
                    if (polygons.length === 0) {
                        alertMessage.value = t('at_least_one_completed_polygon_is_required');
                        return;
                    }

                    const coordinates = polygons.map(polygon =>
                        polygon.getPath().getArray().map(latLng => [
                            latLng.lng(),
                            latLng.lat()
                        ])
                    );

                    const formData = {
                        ...form.data(),
                        coordinates: JSON.stringify(coordinates)
                    };

                    const response = await axios.post('store', formData);
                    if (response.status === 201) {
                        successMessage.value = t('zone_created_successfully');
                        form.reset();
                        router.get('/zones');
                    } else {
                        alertMessage.value = t('failed_to_create_zone');
                    }
                } catch (error) {
                    if (error.response && error.response.status === 403) {
                        alertMessage.value = error.response.data.alertMessage;
                        setTimeout(()=>{
                            router.get('/zones');
                        },5000)
                    }else{
                        console.error(t('error_creating_zone'), error);
                        alertMessage.value =t('failed_to_create_zone_catch');
                    }
                }
            } else {
                form.errors = errors;
            }
        };
        const setActiveTab = (tab) => {
        activeTab.value = tab;
        }
        onMounted(async () => {
        if (Object.keys(languages).length == 0) {
            await fetchData();
        }
        });

        const validateForm = () => {
            const { service_location_id, unit } = form;
            const errors = {};
            if (!unit) {
                errors.unit = t('unit_is_required');
            } else {
                delete errors.unit;
            }
            if (!service_location_id) {
                errors.service_location_id = t('service_location_is_required');
            }else{
                delete errors.service_location_id;
            }
            if (polygons.length === 0) {
                errors.coordinates = t('at_least_one_completed_polygon_is_required');
            }else{
                delete errors.coordinates;
            }


            return errors;
        };

        onMounted(() => {
            if (!googleMapKey) {
                console.error(t('google_map_api_key_is_null_or_undefined'));
                return;
            }

            // Load Google Maps API script dynamically
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapKey}&libraries=places,drawing`;
            script.onload = () => {
                initializeMap();
                fetchServiceLocations();
            };
            document.head.appendChild(script);
            
        });

        return {
            form,
            successMessage,
            alertMessage,
            handleSubmit,
            serviceLocations,
            setActiveTab,
            removeAllPolygons,
            removeSelectedPolygon,
            changeDrawingMode,
            activeTab,
            languages,
        };
    },
};
</script>


<style scoped>
.text-danger {
    padding-top: 5px;
}
</style>
