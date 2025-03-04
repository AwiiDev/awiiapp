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

        serviceLocations: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            user_type: 'all',
            complaint_type: 'all',
            status: 'all',
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };


        const rightOffcanvas = ref(false);
        const filterData = () => {
            fetchDatas();
            modalFilter.value = true;
            rightOffcanvas.value = false;
        };


        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            modalFilter.value = false;
            rightOffcanvas.value = false;
        };

        // const toggleActiveStatus = async (id, status) => {
        //     try {
        //         await axios.post(`/complaint-title/update-status`, { id: id, status: status });
        //         const index = results.value.findIndex(item => item.id === id);
        //         if (index !== -1) {
        //             results.value[index].active = status; // Update the active status locally
        //         }
        //     } catch (error) {
        //         console.error(t('error_updating_status'), error);
        //     }
        // };

        const toggleActiveStatus = async (id, status) => {
    Swal.fire({
        title: t('are_you_sure'),
        text: t('you_are_about_to_change_status'),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: t('yes'),
        cancelButtonText: t('cancel')
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.post(`/complaint-title/update-status`, { id: id, status: status });
                const index = results.value.findIndex(item => item.id === id);
                if (index !== -1) {
                    results.value[index].active = status; // Update the active status locally
                }
                Swal.fire(t('changed'), t('status_updated_successfully'), "success");
            } catch (error) {
                console.error(t('error_updating_status'), error);
                Swal.fire(t('error'), t('failed_to_update_status'), "error");
            }
        }
    });
};


        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (title) => {
            console.log(title);
            try {
                await axios.delete(`/complaint-title/delete/${title.id}`);
                const index = results.value.findIndex(data => data.id === title.id);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('complaint_title_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_complaint_title'), 'error');
            }
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

        watch(searchTerm, debounce(function (value) {
            fetchDatas(searchTerm.value);

        }, 300));

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.page = page;
                const response = await axios.get(`/complaint-title/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_complaint_titles'), error);
            }
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/complaint-title/edit/${result.id}`); 
        };


        const SerialNumber = (index) => {
            return ((paginator.value.current_page-1)*paginator.value.per_page)+index+1;
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
            dismissMessage,
            searchTerm,
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            SerialNumber,
            filter,
            handlePageChanged,
            toggleActiveStatus,
            editData,
            rightOffcanvas
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

        <Head title="Complaint Title" />
        <PageHeader :title="$t('complaint_title')" :pageTitle="$t('complaint_title')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol md="3">
                            </BCol>
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}</BButton>

                                    <Link href="/complaint-title/create" v-if="permissions.includes('add-complaint-title')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i>{{$t("add_complaint_title")}}</BButton>
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
                                        <th scope="col">{{$t("complaint_title")}}</th>
                                        <th scope="col">{{$t("complaint_type")}}</th>
                                        <th scope="col">{{$t("user_type")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td> {{ result.title }}</td> 
                                        <td> {{ result.complaint_type == "request_help" ? 'Request Help': 'General' }}</td> 
                                        <td>{{ result.user_type ? result.user_type.charAt(0).toUpperCase() + result.user_type.slice(1) : '' }}</td>
                                        <td v-if="permissions.includes('toggle-complaint-title')">
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.active,
                                                }">
                                                <input class="form-check-input" type="checkbox" role="switch" @click.prevent="toggleActiveStatus(result.id, !result.active)" :id="'status_'+result.id" :checked="result.active">
                                            </div>
                                        </td>                                      
                                        <td>
                                            <BButton @click.prevent="editData(result)" v-if="permissions.includes('edit-complaint-title')"
                                                class="btn btn-soft-warning btn-sm m-2"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                                                <i class='bx bxs-edit-alt bx-xs'></i>
                                            </BButton>
                                            <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" v-if="permissions.includes('delete-complaint-title')"
                                                type="button" @click.prevent="deleteModal(result)"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('delete')">
                                                <i class='bx bx-trash bx-xs'></i>
                                            </BButton>
                                        </td>
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
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("user_type")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.user_type">
                            <option value="all">{{$t("all")}}</option>
                            <option value="user">{{$t("user")}}</option>
                            <option value="driver">{{$t("driver")}}</option>
                            <option value="owner">{{$t("owner")}}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("complaint_type")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.complaint_type">
                            <option value="all">{{$t("all")}}</option>
                            <option value="request_help">{{$t("request_help")}}</option>
                            <option value="general">{{$t("general")}}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("payment")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="all" value="all" v-model="filter.status">
                                <label class="form-check-label" for="all">{{$t("all")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="active" value="1" v-model="filter.status">
                                <label class="form-check-label" for="active">{{$t("active")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="inactive" value="0" v-model="filter.status">
                                <label class="form-check-label" for="inactive">{{$t("inactive")}}</label>
                            </div>
                        </div>
                    </div>
                    <div>
                    </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" @click.prevent="clearFilter()" class="w-100">{{$t("clear_filter")}}</BButton>
                    <BButton type="submit" @click.prevent="filterData()" variant="success" class="w-100">
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
</style>
