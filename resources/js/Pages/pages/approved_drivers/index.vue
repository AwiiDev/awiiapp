<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
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
        app_for:String,
        types:Object,
        serviceLocations: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const { t } = useI18n();
        // const {selectedLocation } = useSharedState();
        const searchTerm = ref("");
        const filter = useForm({
            approve: 1,
            transport_type: 'all',
            service_location_id: "all",
            vehicle_type: [],
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const types = ref(props.types);
        const serviceLocations = ref(props.serviceLocations);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const mobileFromUser = (user) => {
            if(props.app_for && props.app_for == "demo"){
                return "***********";
            }
            return user.mobile
        }


        const rightOffcanvas = ref(false);
        const filterData = () => {
            fetchDatas();
            modalFilter.value = true;
            rightOffcanvas.value = false;
        };


        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            // modalFilter.value = false;
            rightOffcanvas.value = false;
        };
        const deleteModal = async (itemId) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, delete it!",
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
        // const location = () => {
        //     return serviceLocations.value.find(location => location.id === selectedLocation.value);
        // };
        // watch(location, (newValue) => {
        //     filter.service_location_id = newValue;
        //     fetchDatas();
        // });

        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/approved-drivers/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('approved_drivers_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_approved_drivers'), 'error');
            }
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                if(searchTerm.value.length > 0){
                    params.search = searchTerm.value;
                }
                params.page = page;
                const response = await axios.get(`/approved-drivers/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_approved_drivers'), error);
            }
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/approved-drivers/edit/${result.id}`); 
        };
        const viewProfile = async (result) =>  {
            router.get(`/approved-drivers/view-profile/${result.id}`); 
        };  
        
        
        const documentView = async (driver) => {

                    router.get(`/approved-drivers/document/${driver.id}`);

        };

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };
        const SerialNumber = (index) => {
            return ((paginator.value.current_page-1)*paginator.value.per_page)+index+1;
        };
        const editPassData = async (result) =>  {
            router.get(`/approved-drivers/password/edit/${result.id}`); 
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
            SerialNumber,
            fetchSearch,
            dismissMessage,
            serviceLocations,
            mobileFromUser,
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            types,
            handlePageChanged,
            editData,
            documentView,
            viewProfile,
            rightOffcanvas,
            editPassData
        };
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
    mounted() {
        this.fetchDatas();
    },
};
</script>

<template>
    <Layout>

        <Head title="Approved Drivers" />
        <PageHeader :title="$t('approved_drivers')" :pageTitle="$t('approved_drivers')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar @search="fetchSearch"></searchbar>

                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}</BButton>

                                    <Link href="/approved-drivers/create" v-if="permissions.includes('add-driver')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{$t("add_drivers")}}</BButton>
                                    </Link>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("name")}}</th>
                                        <th scope="col">{{$t("service_location")}}</th>
                                        <th scope="col">{{$t("mobile_number")}}</th>
                                        <th scope="col">{{$t("transport_type")}}</th>
                                        <th scope="col">{{$t("document_view")}}</th>
                                        <th scope="col">{{$t("approved_status")}}</th>
                                        <th scope="col">{{$t("declined_reason")}}</th>
                                        <th scope="col">{{$t("rating")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>
                                        <a @click.prevent="viewProfile(result)">{{ result.name }}</a>
                                    </td> 
                                        <td> {{ result.service_location_name }}</td>
                                        <td>{{ mobileFromUser(result) }}</td>

                                        <td>
                                        <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }}</span>
                                        <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }}</span>
                                        <span v-else>{{ $t('all') }}</span>
                                        </td>

                                        <td v-if="permissions.includes('driver-upload-documents')">
                                            <Link href="#" @click.prevent="documentView(result)">
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </Link>
                                        </td>
  
                                        <td>
                                            <template v-if="result.approve == 1">
                                                <BBadge variant="success" class="text-uppercase">{{$t("approved")}}</BBadge>
                                            </template>
                                            <template v-else>
                                                <BBadge variant="danger" class="text-uppercase">{{$t("disapproved")}}</BBadge>
                                            </template>
                                        </td>   
                                        <td> {{ result.declined_reason }}</td> 
                                        <td>
                                            <i v-for="n in 5" :key="n"
                                                :class="{
                                                'bx bxs-star': n <= Math.floor(result.rating),
                                                'bx bxs-star-half': n === Math.ceil(result.rating) && result.rating % 1 !== 0,
                                                'bx bx-star': n > result.rating
                                                }"
                                                class="align-center text-muted me-2"></i>
                                        </td>                                            
                                        <td>
                                            <BButton class="btn btn-soft-info btn-sm m-2" size="sm" type="button">
                                            <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#" @click.prevent="documentView(result)" v-if="permissions.includes('driver-approval')">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> 
                                                        {{ result.approve ?'Disapprove' : "Approve"}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-driver')">
                                                        <i class='bx bxs-edit-alt bx-xs text-muted me-2'></i>{{$t("edit")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="editPassData(result)" v-if="permissions.includes('edit-driver')">
                                                        <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("update_password")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="viewProfile(result)" v-if="permissions.includes('view-driver-profile')">
                                                        <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("view_profile")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)" v-if="permissions.includes('delete-driver') && app_for !== 'demo'">
                                                        <i class='bx bx-trash me-2 bx-xs text-muted'></i>{{$t("delete")}}
                                                    </a>
                                                </div>
                                            </div>
                                            </BButton>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="12" class="text-center">
                                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                            <h5>{{$t("no_data_found")}}</h5>
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
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("transport_type")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.transport_type">
                            <option value="all">{{$t("all")}}</option>
                            <option value="both">{{$t("both")}}</option>
                            <option value="taxi">{{$t("taxi")}}</option>
                            <option value="delivery">{{$t("delivery")}}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="service-location"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("service_locations")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="service-location" v-model="filter.service_location_id">
                            <option value="all">{{$t("all")}}</option>
                            <option v-for="(location, index) in serviceLocations" :key="location.id" :value="location.id">{{ location.name }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="country-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("vehicle_type")}}</label>

                        <Multiselect class="form-control" v-model="filter.vehicle_type" 
                        :close-on-select="false" :searchable="true"
                        multiple placeholder="Select Vehicle Type"
                        mode="tags"
                            :create-option="false" :options="types.map(type => ({value:type.id,label:type.name}))" />
                    </div>
                    <div>
                    </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton @click="clearFilter" variant="light" class="w-100">{{$t("clear_filter")}}</BButton>
                    <BButton @click="filterData" type="submit"  variant="success" class="w-100">
                        {{$t("apply")}}
                    </BButton>
                </div>
                <!--end offcanvas-footer-->
            </BFrom>
        </BOffcanvas>
        <!--end offcanvas-->
        <!-- filter end -->

        <!-- Pagination -->
        <Pagination :paginator="paginator" @page-changed="handlePageChanged" />
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
/* .text-danger {
    padding-top: 5px;
} */

</style>
