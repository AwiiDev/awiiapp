<script>
import { Head, useForm } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, watch } from "vue";
import axios from "axios";
import { debounce } from 'lodash';

export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Pagination,
    },
    props: {
        successMessage: String,
        alertMessage: String,
    },
    setup(props) {
        const searchTerm = ref("");
        const form = useForm({
            name: "",
            description: "",
        });
        const filter = useForm({
            all: "",
            locked: "",
        });
        const items = ref([]); // Spread the items to make them reactive
        const paginator = ref({}); // Spread the items to make them reactive
        const modalShow = ref(false);
        const modalForm = ref(false);
        const modalFilter = ref(false);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };


        const filterItem = () => {
            modalFilter.value = true;
        };

       
        
        const clearFilter = () => {
            filter.reset();
            fetchItems();
            modalFilter.value = false;
        };


        const closeModal = () => {
            modalShow.value = false;
        };
      

        watch(searchTerm,debounce(function(value){
            fetchItems(searchTerm.value);

        },300));

        const fetchItems = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/countries-list`, { params });
                items.value = response.data.items;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
            } catch (error) {
                // @TODO 
                // return exceptions
                console.error("Error fetching items:", error);
            }
        };

        const handlePageChanged = async (page) => {

            fetchItems(page);
        };

        return {
            form,
            items,
            modalShow,
            modalForm,
            successMessage,
            alertMessage,
            filterItem,
            closeModal,
            dismissMessage,
            searchTerm,
            paginator,
            modalFilter,
            clearFilter,
            fetchItems,
            filter,
            handlePageChanged

        };
    },
    mounted() {
        this.fetchItems();
    },
};
</script>

<template>
    <Layout>

        <Head title="items" />
        <PageHeader title="countries" pageTitle="Masters" />

        
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">

<BRow class="g-2">
    <BCol md="3">
        <div class="search-box">
            <input type="text" id="name" class="form-control search"
                placeholder="Search Countries..." v-model="searchTerm"
                @keyup.enter="fetchItems" />
            <i class="ri-search-line search-icon"></i>
        </div>
    </BCol>
    
    <BCol md="auto" class="ms-auto">
        <div class="d-flex align-items-center gap-2">
            
            <BButton variant="danger" @click="filterItem"><i
                    class="ri-filter-2-line me-1 align-bottom"></i> Filters</BButton>
        <BButton variant="primary" href="/countries-create" class="float-end"> <i 
            class="ri-add-line align-bottom me-1"></i> Create Country</BButton>

            
        </div>
    </BCol>
</BRow>
</BCardHeader>
<BCardBody class="border border-dashed border-end-0 border-start-0">
<table class="table table-bordered align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Country Name</th>
                                    <th>Dial Code</th>
                                    <th>Action</th>
                                    <th>Currency Code</th>
                                    <th>Currency Symbol</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="country in items" :key="country.id">
                                    <tr>
                                        <td>{{ country.name }}</td>
                                        <td>{{ country.dial_code }}</td>
                                        <td>{{ country.currency_code }}</td>
                                        <td>{{ country.currency_symbol }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <BButton
                                                    class="btn btn-soft-warning btn-sm m-2">
                                                    <i class='bx bxs-edit-alt'></i>
                                                </BButton>
                                                <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" type="button"
                                                    id="sa-warning">
                                                    <i class='bx bx-trash'></i>
                                                </BButton>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

      
        <BModal v-model="modalFilter" hide-footer dialog-class="modal-dialog-right" title="Filter"
            class="v-modal-custom " size="sm">
            <form >
                <div class="input-group">
                    <select class="form-select mb-3" aria-label="Default select example" v-model="filter.all">
                        <option selected>Select Status</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                        
                    </select>

                    <select class="form-select mb-3" aria-label="Default select example" v-model="filter.locked">
                        <option selected>Select Status</option>
                        <option value="0">Inactive</option>
                        <option value="1">Active</option>
                    </select>
                </div>
                <BButton variant="primary" class="float-end" @click="fetchItems"> Apply</BButton>
                <BButton variant="outline-primary" class="float-end mx-2" @click="clearFilter">Clear</BButton>
                
            </form>
        </BModal>

        <!-- Pagination -->
        <Pagination :paginator=paginator @page-changed="handlePageChanged"/>
        
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

.text-danger {
    padding-top: 5px;
}
</style>
