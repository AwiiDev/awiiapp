<script>
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Navigation, Pagination } from "swiper/modules";


import "swiper/css";
import "swiper/css/autoplay";
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export default {
    data() {
        return {
            togglePassword: false,
            Autoplay: Autoplay, Pagination: Pagination, Navigation: Navigation,
            pagination: {
                clickable: true,
                el: '.swiper-pagination',
                renderBullet: function (index, className) {
                    return '<span class=' + className + '>' + (index + 1) + '</span>';
                },
            },
        }
    },
    components: {
        Swiper,
        SwiperSlide,
    },
};
</script>

<template>
    <div class="auth-page-wrapper overflow-hidden" style="height:100vh;">
        <div class="auth-page-content overflow-hidden">
            <BContainerFluid>
                <BRow class="card">
                    <BCol lg="12" >
                            <BRow class="g-0" style="height: 100vh;">
            
                                <BCol lg="4">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card" id="leadsList">
                                                <div class="card-header border-0">
                                                    <div class="row g-4 align-items-center">
                                                        <div class="col-sm-3">
                                                            <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft" aria-controls="offcanvasLeft">
                                                                <span class="hamburger-icon">
                                                                    <span></span>
                                                                    <span></span>
                                                                    <span></span>
                                                                </span>
                                                            </button>
                                                        </div>

                                                        <div class="col-sm-3">
                                                            <div>
                                                                <img src="/images/logo.png" alt="" height="58">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3 ms-auto">
                                                            <div class="hstack gap-2">
                                                                <span class="dropdown">
                                                                    <button class="btn btn-soft-info btn-icon fs-14" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="ri-settings-4-line"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" style="">
                                                                        <li><a class="dropdown-item" href="#">Logout</a></li>
                                                                    </ul>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                </div>
                                            </div>

                                        </div>
                                        <!--end col-->
                                    </div>
                                    <div class="p-lg-4 p-4">
                                    <div class="card-body">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-pills animation-nav nav-justified gap-2 mb-3" role="tablist">
                                        <li class="nav-item waves-effect waves-light" role="presentation">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#animation-taxi" role="tab" aria-selected="true" >
                                                Taxi
                                            </a>
                                        </li>
                                        <li class="nav-item waves-effect waves-light" role="presentation">
                                            <a class="nav-link " data-bs-toggle="tab" href="#animation-delivery" role="tab" aria-selected="false" tabindex="-1">
                                                Delivery
                                            </a>
                                        </li>
                                        <li class="nav-item waves-effect waves-light" role="presentation">
                                            <a class="nav-link" data-bs-toggle="tab" href="#animation-rental" role="tab" aria-selected="false" tabindex="-1">
                                                Rental
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content text-muted">
                                        <div class="tab-pane" id="animation-taxi" role="tabpanel">
                                            <div class="mt-4">
                                            <form>
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                    <span class="input-group-text" id="inputGroup-sizing-default">From</span>
                                                    <input type="text" placeholder="Search Taxi Pickup" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                                </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="input-group">
                                                    <span class="input-group-text px-4" id="inputGroup-sizing-default">To</span>
                                                    <input type="text" placeholder="Search Destination" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                                </div>
                                                </div>

                                                <div class="mt-4">
                                                    <BButton variant="success" class="w-100" type="submit">Book</BButton>
                                                </div>
                                            </form>
                                        </div>
                                        </div>

                                        <div class="tab-pane active show" id="animation-delivery" role="tabpanel">
                                            <div class="mt-4">
                                            <form>
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                    <span class="input-group-text" id="inputGroup-sizing-default">From</span>
                                                    <input type="text" placeholder="Search Delivery Pickup" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                                </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="input-group">
                                                    <span class="input-group-text px-4" id="inputGroup-sizing-default">To</span>
                                                    <input type="text" placeholder="Search Destination" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                                </div>
                                                </div>

                                                <div class="mt-4">
                                                    <BButton variant="success" class="w-100" type="submit">Book</BButton>
                                                </div>
                                            </form>
                                        </div>
                                        </div>

                                        <div class="tab-pane" id="animation-rental" role="tabpanel">
                                            <div class="mt-4">
                                            <form>
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                    <span class="input-group-text" id="inputGroup-sizing-default">From</span>
                                                    <input type="text" placeholder="Search Rental Pickup" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                                </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="input-group">
                                                    <span class="input-group-text px-4" id="inputGroup-sizing-default">To</span>
                                                    <input type="text" placeholder="Search Destination" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                                </div>
                                                </div>

                                                <div class="mt-4">
                                                    <BButton variant="success" class="w-100" type="submit">Book</BButton>
                                                </div>
                                            </form>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                    </div>
                                </BCol>

                                <BCol lg="8">
                                    <div class="p-lg-5 p-4 h-100" style="background: url(/images/TAXI.png);background-position: center;background-size: cover;background-repeat: no-repeat;">
                                        <div class="bg-overlay"></div>
                                    </div>
                                </BCol>

                            </BRow>
                    </BCol>
                </BRow>
            </BContainerFluid>
        </div>
<!-- left offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasLeft" aria-labelledby="offcanvasLeftLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasLeftLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="list-group">
            <a href=""><li class="list-group-item"><i class="ri-bill-line align-middle me-2"></i> History</li></a>
            <a href=""><li class="list-group-item"><i class="ri-file-copy-2-line align-middle me-2"></i>Logout</li></a>
        </ul>
    </div>
</div>
    </div>
</template>