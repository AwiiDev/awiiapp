<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
// import Pagination from "@/Components/Pagination.vue";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import '@vueform/multiselect/themes/default.css';
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
import { Autoplay, Thumbs, Navigation, Pagination,EffectCoverflow, Mousewheel, Scrollbar, EffectFade, EffectFlip, EffectCreative } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/autoplay";
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';
import 'swiper/css/mousewheel';
import 'swiper/css/effect-fade';
import 'swiper/css/effect-creative';
import 'swiper/css/effect-flip';
import 'swiper/css/effect-coverflow';

export default {
  data() {
    return {
      priceperdistance: false,
      Autoplay: Autoplay, Thumbs: Thumbs, Pagination: Pagination, Navigation: Navigation, EffectCreative: EffectCreative,
      coverflowModules: [EffectCoverflow, Pagination],
      mousewheelModules: [Mousewheel, Pagination],
      scrollbarModules: [Scrollbar, Navigation, Pagination],
      effectFadeModules: [EffectFade, Pagination],
      effectFlipModules: [EffectFlip, Pagination],
    }
  },
  components: {
    Layout,
    PageHeader,
    Head,
    // Pagination,
    Multiselect,
    FormValidation,
    Swiper,
    SwiperSlide,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    zones: Array,
    vehicleTypes: Array,
    zoneTypePrice: Object,
    zoneType: Object,
  },
  setup(props) {
    const { t } = useI18n();
    const form = useForm({
      zone_id: props.zoneType ? props.zoneType.zone_id || "" : "",
      transport_type: props.zoneType ? props.zoneType.transport_type || "" : "",
      vehicle_type: props.zoneType ? props.zoneType.type_id || "" : "",
      payment_type: props.zoneType ? props.zoneType.payment_type.split(',') || [] : [],
      admin_commision_type: props.zoneType ? props.zoneType.admin_commision_type || "" : "",
     admin_commision: props.zoneType ? props.zoneType.admin_commision ?? 0 : "",
      admin_commission_type_from_driver: props.zoneType ? props.zoneType.admin_commission_type_from_driver || "" : "",
      admin_commission_from_driver: props.zoneType ? props.zoneType.admin_commission_from_driver ||  0 : "",
      admin_commission_type_for_owner: props.zoneType ? props.zoneType.admin_commission_type_for_owner || "" : "",
      admin_commission_for_owner: props.zoneType ? props.zoneType.admin_commission_for_owner ||  0 : "",
      service_tax: props.zoneType ? props.zoneType.service_tax ||  0 : "",
      base_price: props.zoneTypePrice ? props.zoneTypePrice.base_price ||  0 : "",
      airport_surge: props.zoneType ? props.zoneType.airport_surge ||  0 : "",
      price_per_distance: props.zoneTypePrice ? props.zoneTypePrice.price_per_distance ||  0 : "",
      waiting_charge: props.zoneTypePrice ? props.zoneTypePrice.waiting_charge ||  0 : "",
      price_per_time: props.zoneTypePrice ? props.zoneTypePrice.price_per_time ||  0 : "",
      base_distance: props.zoneTypePrice ? props.zoneTypePrice.base_distance ||  0 : "",
      free_waiting_time_in_mins_before_trip_start: props.zoneTypePrice ? props.zoneTypePrice.free_waiting_time_in_mins_before_trip_start ||  0 : "",
      free_waiting_time_in_mins_after_trip_start: props.zoneTypePrice ? props.zoneTypePrice.free_waiting_time_in_mins_after_trip_start ||  0 : "",
    });

    const validationRules = {
      zone_id: { required: true },
      transport_type: { required: true },
      vehicle_type: { required: true },
      payment_type: { required: true },
      admin_commision_type: { required: true },
      admin_commision: { required: true },
      admin_commission_type_from_driver: { required: true },
      admin_commission_from_driver: { required: true },
      admin_commission_type_for_owner: { required: true },
      admin_commission_for_owner: { required: true },
      service_tax: { required: true },
      base_price: { required: true },
      airport_surge: { required: true },
      price_per_distance: { required: true },
      waiting_charge: { required: true },
      price_per_time: { required: true },
      base_distance: { required: true },
      free_waiting_time_in_mins_before_trip_start: { required: true },
      free_waiting_time_in_mins_after_trip_start: { required: true },
    };

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    // const transportTypes = ['taxi', 'delivery', 'both'];


    const capitalizeFirstLetter = (word) => {
      return word.charAt(0).toUpperCase() + word.slice(1);
    };
    const vehicleTypesList = ref([]);

const fetchVehicleTypes = async () => {
  if (form.zone_id && form.transport_type) {
    try {
      const response = await axios.get('/set-prices/vehicle_types', {
        params: {
          zone: form.zone_id,
          transportType: form.transport_type,
          zone_type_id: form.vehicle_type,
        },
      });
      vehicleTypesList.value = response.data.results;

      // Set the selected vehicle type if not already set
      if (form.vehicle_type === "") {
        form.vehicle_type = props.zoneType?.type_id || ""; // Set the default selected vehicle type
      }
    } catch (error) {
      console.error(t('error_fetching_vehicle_types'), error);
    }
  }

};

  watch(() => form.transport_type, (newTransportType, oldTransportType) => {
    // Reset vehicle type when transport type changes
    form.vehicle_type = "";
    fetchVehicleTypes(); // Fetch the vehicle types after resetting
  });


  onMounted(() => {
    if (form.zone_id && form.transport_type) {
      fetchVehicleTypes();
    }
  });

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const handleSubmit = async () => {
      errors.value = validationRef.value.validate(form);
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {
        let response;
        if (props.zoneTypePrice && props.zoneTypePrice.id) {
          response = await axios.post(`/set-prices/update/${props.zoneTypePrice.id}`, form);
        } else {
          response = await axios.post('/set-prices/store', form);
        }
        if (response.status === 201) {
          successMessage.value = t('vehicle_price_created_successfully');
          form.reset();
          router.get('/set-prices');
        } else {
          alertMessage.value = t('failed_to_create_vehicle_price');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else if (error.response && error.response.status == 403) {
          alertMessage.value = error.response.data.alertMessage;
          setTimeout(()=>{
            router.get('/set-prices');
          },5000)
        } else {
          console.error(t('error_creating_vehicle_price'), error);
          alertMessage.value = t('failed_to_create_vehicle_price');
        }
      }
    };

    return {
      form,
      zones: props.zones,
      // transportTypes,
      fetchVehicleTypes,
      vehicleTypes: vehicleTypesList,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      errors,
      capitalizeFirstLetter
    };
  },
};
</script>



<template>
  <Layout>
    <Head title="Set Prices" />
    <PageHeader :title="zoneTypePrice ? $t('edit') : $t('create')"  :pageTitle="$t('set_prices')"  pageLink="/set-prices" />
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0">
            <BLink @click="priceperdistance = !priceperdistance">
              <h6 class="text-success text-decoration-underline text-decoration-underline-success float-end heart">{{$t('how_it_works')}}</h6>
            </BLink>
          </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_zone" class="form-label">{{$t("zone")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_zone" class="form-select" v-model="form.zone_id">
                        <option disabled value="">{{$t('select_zone')}}</option>
                        <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                      </select>
                      <span v-for="(error, index) in errors.zone_id" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
<div class="col-sm-6">
  <div class="mb-3">
    <label for="select_transport_type" class="form-label">{{$t("transport_type")}}
      <span class="text-danger">*</span>
    </label>
    <select id="transport_type" class="form-select" v-model="form.transport_type">
      <option disabled value="">{{$t('select_transport_type')}}</option>
        <option  value="taxi">{{$t('taxi')}}</option>
        <option value="delivery">{{$t('delivery')}}</option>
        <option value="both">{{$t('all')}}</option>
    </select>
    <span v-for="(error, index) in errors.transport_type" :key="index" class="text-danger">{{ error }}</span>
  </div>
</div>

<div class="col-sm-6">
  <div class="mb-3">
    <label for="select_vehicle_type" class="form-label">{{$t("vehicle_type")}}
      <span class="text-danger">*</span>
    </label>
    <Multiselect
      id="select_vehicle_type"
      v-model="form.vehicle_type"
      :options="vehicleTypes.map(type => ({ value: type.id, label: type.name }))"
      :multiple="false"
      :close-on-select="false"
      :searchable="true"
      :create-option="false"
      :placeholder="$t('select_vehicle_type')"
    />
    <span v-for="(error, index) in errors.vehicle_type" :key="index" class="text-danger">{{ error }}</span>
  </div>
</div>

                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_payment_type" class="form-label">{{$t("payment_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <Multiselect 
                        id="select_payment_type" 
                        mode="tags" 
                        v-model="form.payment_type" 
                        :close-on-select="false"
                        :searchable="true" 
                        :create-option="false"
                        :options="[
                          { value: 'cash', label: $t('cash') },
                          { value: 'online', label: $t('online') },
                          { value: 'wallet', label: $t('wallet') },
                        ]"
                        :placeholder="$t('select_payment_type')"
                      />
                      <span v-for="(error, index) in errors.payment_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="admin_commision_type" class="form-label">{{$t("admin_commission_type_from_customer")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="admin_commision_type" class="form-select" v-model="form.admin_commision_type">
                        <option disabled value="">{{$t('select_admin_commission_type_from_customer')}}</option>
                        <option value="1">{{$t('percentage')}}</option>
                        <option value="2">{{$t('fixed_amount')}}</option>
                      </select>
                      <span v-for="(error, index) in errors.admin_commision_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="admin_commision" class="form-label">{{$t("admin_commission_from_customer")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" step="any" class="form-control" :placeholder="$t('enter_admin_commission_from_customer')" id="admin_commision" v-model.number="form.admin_commision">
                      <span v-for="(error, index) in errors.admin_commision" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="admin_commission_type_from_driver" class="form-label">{{$t("admin_commission_type_from_driver")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="admin_commission_type_from_driver" class="form-select" v-model="form.admin_commission_type_from_driver">
                        <option disabled value="">{{$t('select_admin_commission_type_from_driver')}}</option>
                        <option value="1">{{$t('percentage')}}</option>
                        <option value="2">{{$t('fixed_amount')}}</option>
                      </select>
                      <span v-for="(error, index) in errors.admin_commission_type_from_driver" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="admin_commission_from_driver" class="form-label">{{$t("admin_commission_from_driver")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" step="any" class="form-control" :placeholder="$t('enter_admin_commission_from_driver')" id="admin_commission_from_driver" v-model.number="form.admin_commission_from_driver">
                      <span v-for="(error, index) in errors.admin_commission_from_driver" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="admin_commission_type_for_owner" class="form-label">{{$t("admin_commission_type_for_owner")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="admin_commission_type_for_owner" class="form-select" v-model="form.admin_commission_type_for_owner">
                        <option disabled value="">{{$t('select_admin_commission_type_for_owner')}}</option>
                        <option value="1">{{$t('percentage')}}</option>
                        <option value="2">{{$t('fixed_amount')}}</option>
                      </select>
                      <span v-for="(error, index) in errors.admin_commission_type_for_owner" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="admin_commission_for_owner" class="form-label">{{$t("admin_commission_for_owner")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" step="any" class="form-control" :placeholder="$t('enter_admin_commission_for_owner')" id="admin_commission_for_owner" v-model.number="form.admin_commission_for_owner">
                      <span v-for="(error, index) in errors.admin_commission_for_owner" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="service_tax" class="form-label">{{$t("service_tax")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" step="any" class="form-control":placeholder="$t('enter_service_tax')" id="service_tax" v-model.number="form.service_tax">
                      <span v-for="(error, index) in errors.service_tax" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="base_price" class="form-label">{{$t("base_price")}}
                        <span class="text-danger">*  </span>({{$t("kilo_meter")}})
                      </label>
                      <input type="number" step="any" class="form-control"  :placeholder="$t('enter_base_price')" id="base_price" v-model.number="form.base_price">
                      <span v-for="(error, index) in errors.base_price" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="base_distance" class="form-label">{{$t("base_distance")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" step="any" class="form-control" :placeholder="$t('enter_base_distance')"  id="base_distance" v-model.number="form.base_distance">
                      <span v-for="(error, index) in errors.base_distance" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>                  
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <div class="d-flex align-items-center"><label for="price_per_distance" class="form-label mx-2">{{$t("price_per_distance")}} 
                        <span class="text-danger">*</span>
                      </label></div>
                      <input type="number" step="any" class="form-control" :placeholder="$t('enter_price_per_distance')" id="price_per_distance" v-model.number="form.price_per_distance">
                      <span v-for="(error, index) in errors.price_per_distance" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="price_per_time" class="form-label">{{$t("time_price")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" step="any" class="form-control" :placeholder="$t('enter_time_price')"  id="price_per_time" v-model.number="form.price_per_time">
                      <span v-for="(error, index) in errors.price_per_time" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>                  
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="waiting_charge" class="form-label">{{$t("waiting_charge")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" step="any" class="form-control" :placeholder="$t('enter_waiting_charge')" id="waiting_charge" v-model.number="form.waiting_charge">
                      <span v-for="(error, index) in errors.waiting_charge" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="free_waiting_time_in_mins_before_trip_start" class="form-label">{{$t("free_waiting_time_In_minutes_before_start_a_ride")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" class="form-control"  :placeholder="$t('free_waiting_time_In_minutes_before_start_a_ride')"  id="free_waiting_time_in_mins_before_trip_start" v-model.number="form.free_waiting_time_in_mins_before_trip_start">
                      <span v-for="(error, index) in errors.free_waiting_time_in_mins_before_trip_start" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="free_waiting_time_in_mins_after_trip_start" class="form-label">{{$t("free_waiting_time_In_minutes_after_start_a_ride")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" class="form-control" :placeholder="$t('free_waiting_time_In_minutes_after_start_a_ride')" id="free_waiting_time_in_mins_after_trip_start" v-model.number="form.free_waiting_time_in_mins_after_trip_start">
                      <span v-for="(error, index) in errors.free_waiting_time_in_mins_after_trip_start" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="airport_surge" class="form-label">{{$t("airport_surge_fee")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" step="any" class="form-control"  :placeholder="$t('enter_airport_surge_fee')" id="airport_surge" v-model.number="form.airport_surge">
                      <span v-for="(error, index) in errors.airport_surge" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>                  
                  <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">{{$t("save")}}</button>
                  </div>
                </div>
              </FormValidation>
            </form>
            <!-- <div v-if="successMessage" class="alert alert-success alert-dismissible mt-3" role="alert">
              <button type="button" class="btn-close" aria-label="Close" @click="dismissMessage"></button>
              {{ successMessage }}
            </div>
            <div v-if="alertMessage" class="alert alert-danger alert-dismissible mt-3" role="alert">
              <button type="button" class="btn-close" aria-label="Close" @click="dismissMessage"></button>
              {{ alertMessage }}
            </div> -->
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
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
<!-- modal -->
    <BModal v-model="priceperdistance" hide-footer :title="$t('price_calculation')" class="v-modal-custom" size="lg">

      <swiper class="dynamic-pagination rounded" :loop="true"
      :autoplay="{ delay: 1500, disableOnInteraction: false }"
      :pagination="{ clickable: true, el: '.swiper-pagination', dynamicBullets: true }"
      :modules="[Pagination]">
        <swiper-slide>
          <p class="text-muted mb-0"><strong>Here's a simplified explanation of how pricing works:</strong></p>
          <h5 class="text-muted mb-2 mt-3">How the pricing is calculated:</h5>
          <div class="d-flex mt-3">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    The base price applies until you reach the base distance.
                  </p>
              </div>
          </div>
          <div class="d-flex mt-4">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    After the base distance, an additional distance price is charged for every kilometer or mile.
                  </p>
              </div>
          </div>
          <div class="d-flex mt-4">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    The time price is calculated based on how long the ride lasts.
                  </p>
              </div>
          </div>
          <div class="d-flex mt-4">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    If there is waiting time during the trip, the waiting charge applies, 
                    except for the free waiting time which is subtracted from the total waiting time.
                  </p>
              </div>
          </div>
        </swiper-slide>
        <swiper-slide>
          <h5 class="text-muted mb-0">Sample Calculation:</h5>
          <div class="d-flex mt-2">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0"> Base price = a fixed amount for the ride.</p>
              </div>
          </div>
          <div class="d-flex mt-2">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">Distance Price:
                    <ul class="mt-2">
                      <li>Calculate the chargeable distance: `total distance - base distance`.</li>
                      <li>Distance price = chargeable distance × price per distance.</li>
                    </ul>
                  </p>
              </div>
          </div>
          <div class="d-flex">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">Time Price:
                    <ul class="mt-2">
                      <li>Time price = total ride duration (in minutes) × time price per minute.</li>
                    </ul>
                  </p>
              </div>
          </div>
          <div class="d-flex">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">Subtotal = Base price + Distance price + Time price + Waiting Charge.</p>
              </div>
          </div>
          <div class="d-flex mt-2">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">Admin Commission:
                    <ul class="mt-2">
                      <li>Admin commission = Subtotal × (admin commission percentage / 100).</li>
                    </ul>
                  </p>
              </div>
          </div>
          <div class="d-flex">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    Tax: Tax = Subtotal × (tax percentage / 100).
                  </p>
              </div>
          </div>
          <div class="d-flex mt-2">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    Total cost = Subtotal + Admin commission + Tax.
                  </p>
              </div>
          </div>
        </swiper-slide>
        <swiper-slide>
          <h5 class="text-muted mb-0">How Cancellation Fee Works?</h5>
          <p class="text-muted mb-2 mt-2"><strong>Here's a simplified version of that text:</strong> </p>
          <div class="d-flex mt-3">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    The cancellation fee can be set for each vehicle type in the set price menu.
                  </p>
              </div>
          </div>
          <div class="d-flex mt-3">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    A cancellation fee will be charged based on the reason the customer gives for canceling. 
                    If the reason is one that should have a fee, the system will
                    apply the charge for the canceled ride.
                  </p>
              </div>              
          </div> 
          <div class="d-flex mt-3">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    If the payment method for the canceled ride is "cash," the cancellation fee will be
                     collected on the customer's next ride. So, if the customer cancels the first ride 
                     and completes the second ride, the fee will be charged at the end of the second ride.
                  </p>
              </div>              
          </div> 
          <div class="d-flex mt-3">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    If the payment method is the wallet, the cancellation fee will be taken from the 
                    customer's wallet, as long as they have enough money in it.
                  </p>
              </div>              
          </div> 
          <div class="d-flex mt-3">
              <div class="flex-shrink-0">
                  <i class="ri-checkbox-circle-fill text-success"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                  <p class="text-muted mb-0">
                    The cancellation fee goes to the admin, not the driver. 
                    It will be deducted from the driver's earnings for the current ride, 
                    including the admin's commission.
                  </p>
              </div>              
          </div>
        </swiper-slide>
        <div class="swiper-pagination dynamic-pagination"></div>
          <div class="modal-footer v-modal-footer">
            <!-- <BLink href="javascript:void(0);" class="btn btn-link link-success fw-medium"
                @click="priceperdistance = false">
                <i class="ri-close-line me-1 align-middle"></i> Close
            </BLink> -->
        </div>
    </swiper>
    </BModal>
<!-- modal end -->
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

.heart {
	animation: beat .25s infinite alternate;
	transform-origin: center;
}
@keyframes beat{
	to { transform: scale(1.2); }
}

</style>
