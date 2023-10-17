<script setup>
import { Head } from '@inertiajs/vue3';
import HeadRow from '@/Components/Table/HeadRow.vue';
import { Splide, SplideSlide } from '@splidejs/vue-splide';
import '@splidejs/vue-splide/css';
import { Video } from '@splidejs/splide-extension-video';
import '@splidejs/splide-extension-video/dist/css/splide-extension-video.min.css';
import { useTimer } from 'vue-timer-hook';
import { onMounted, ref, computed } from 'vue'
import axios from 'axios';

const props = defineProps({
    station_code: { type: String },
    warning_below_seconds: { type: Number },
    station_last_update: { type: String },
    max_pax: { type: Number },
    annoucement_interval: {
        type: Number
    },
    annoucement_list: {
        type: Object
    },
    annoucement_last_update: { type: String },
    banner_interval: {
        type: Number
    },
    banner_list: {
        type: Object
    },
    banner_last_update: { type: String },
    polling_interval: { type: Number }
});

const list = ref([]);
const air = ref(0);
const temperature = ref(0);
const currentDate = ref(new Date());
const messages = ref([]);
let msgModal = null;

const splide_annoucement = ref();
const splide_banner = ref();
const extensions = { Video }
//Annoucement
const annoucement_options = {
    rewind: true,
    autoWidth: true,
    autoplay: true,
    interval: props.annoucement_interval,
    arrows: false, pagination: false,
    heightRatio: 0.04,
}

//Banner
const options = {
    type: "loop",
    focus: 'center',
    arrows: false,
    pagination: false,
    video: {
        loop: false,
        mute: true,
        autoplay: true,
    },
    heightRatio: 0.76, //Chrome full screen in TV
};


onMounted(
    () => {
        //Initialize Modal
        msgModal = new bootstrap.Modal('#msgModal');

        //Mix of Image and Video, have to manually control the slider
        if (splide_banner.value) {
            splide_banner.value.splide.on('video:ended', (rate) => {
                splide_banner.value.splide.go('>');
            });

            splide_banner.value.splide.on('moved', (index) => {
                bannerNextSlide(index);
            });

            //Start first check, if first is image need manual slide
            bannerNextSlide(0);
        }

        //Initialize
        refresh();
        setInterval(updateDateTime, 900);
        setInterval(refresh, props.polling_interval);


    }
)

function bannerNextSlide(index) {
    //Not video then wait for banner_interval
    let data = splide_banner.value.splide.Components.Elements.slides[index]?.getAttribute('data-isvideo');
    if (data == "false") {
        setTimeout(() => {
            splide_banner.value.splide.go('>');
        }, props.banner_interval);
    }
}

function updateDateTime() {
    currentDate.value = new Date();
}

function refresh() {
    axios.get(route('area.latest', props.station_code)).then(function (response) {

        if (props.station_last_update != response.data.station_last_update) {
            //Got new setting update refresh whole page
            reloadPage();
            return;
        }

        if (props.annoucement_last_update != response.data.annoucement_last_update) {
            //New Annoucement
            reloadPage();
            return;
        }

        if (props.banner_last_update != response.data.banner_last_update) {
            //New Banner
            reloadPage();
            return;
        }

        for (var entry of response.data.list) {
            entry.timer = useTimer(new Date(entry.finished_at));

            entry.finish_date = new Date(new Date(entry.finished_at));
            let wa = new Date(entry.finished_at)
            entry.warning_date = new Date(wa.getTime() - props.warning_below_seconds * 1000);
        }
        list.value = response.data.list;
        air.value = response.data.air ?? 0;
        temperature.value = response.data.temperature ?? 0;

        if (response.data.messages != null && response.data.messages.length > 0) {
            messages.value = response.data.messages;
            msgModal.show();
        } else {
            msgModal.hide();
        }
    });
}

function reloadPage() {
    location.reload();
}

function warningColour(warning_date, finish_date) {
    if (currentDate.value > finish_date) {
        return 'text-danger'
    }
    else if (currentDate.value > warning_date) {
        return 'text-warning'
    }
    return '';
}

//Compute
const current_pax = computed(() => list.value.length);
const max_pax_bg = computed(() => list.value.length >= props.max_pax ? 'bg-danger' : 'bg-success');
const air_quality_bg = computed(() => {
    if (air.value > 200) return 'bg-danger'
    else if (air.value > 100) return 'bg-warning'
    else return 'bg-success'
});

</script>

<template>
    <Head title="Dashboard" />

    <div class="d-flex flex-column min-vh-100">
        <main class="flex-grow-1 p-2">
            <div class="row mb-3">
                <div class="col-lg-3 text-light">
                    <div class="sensor-column schott-colour text-center rounded-3"
                        style="min-height: 142.4px; max-height: 142.4px;">
                        <img src="/assets/schott-logo.png" style="height: 80px; margin-top: 2rem; margin-bottom: 2rem;"
                            alt="logo" />

                        <!-- <object type="image/svg+xml" data="/assets/schott-logo.svg" height="121px"></object> -->
                        <!-- {{ currentDate.toLocaleDateString(undefined, { weekday: 'long' }) }} <br />
                    {{ currentDate.toLocaleDateString(undefined, { day: '2-digit' }) }} {{
                        currentDate.toLocaleDateString(undefined, { month: 'short' }) }} <br />
                    {{ currentDate.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', second: '2-digit' })
                    }} -->
                    </div>
                </div>
                <div class="col-lg-3 text-light">
                    <div class="sensor-column text-center rounded-3" :class="max_pax_bg">
                        <h2>Pax</h2>
                        {{ current_pax }}
                    </div>
                </div>
                <div class="col-lg-3 text-light">
                    <div class="sensor-column text-center rounded-3" :class="air_quality_bg">
                        <h2>Air Index</h2>
                        {{ air }}
                    </div>
                </div>
                <div class="col-lg-3 text-light">
                    <div class="sensor-column text-center rounded-3 bg-success">
                        <h2>Temperature</h2>
                        {{ temperature }}
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="h-100 p-3 box-bg border rounded-3">
                        <Splide ref="splide_annoucement" :options="annoucement_options" aria-label="Annoucements"
                            style="height:100%">
                            <SplideSlide v-for="annoucement in props.annoucement_list" class="fs-4 text-light"
                                style="width:100%">
                                <span v-html="annoucement.html_content"></span>
                            </SplideSlide>
                        </Splide>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="h-100 p-3 box-bg border rounded-3">
                        <table class="table table-bordered table-striped table-dark">
                            <thead>
                                <tr class="schott-colour-dark text-light fs-3">
                                    <HeadRow width="20%">ID</HeadRow>
                                    <HeadRow>Name</HeadRow>
                                    <HeadRow width="10%">Remaining</HeadRow>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in list">
                                    <td class="fs-3" :class="warningColour(item.warning_date, item.finish_date)">{{
                                        item.card_id }}</td>
                                    <td class="fs-4 align-middle text-truncate" style="max-width: 150px;"
                                        :class="warningColour(item.warning_date, item.finish_date)">{{
                                            item.employee?.name
                                        }}
                                    </td>
                                    <td class="text-center fs-3"
                                        :class="warningColour(item.warning_date, item.finish_date)">
                                        {{ item.timer?.minutes }}:{{
                                            item.timer?.seconds.toLocaleString(undefined, {
                                                minimumIntegerDigits: 2
                                            }) }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <Splide ref="splide_banner" aria-labelledby="video-image-slider" :options="options"
                        :extensions="extensions">
                        <template v-for="banner in banner_list">
                            <SplideSlide v-if="banner.type == 'video'" :data-splide-html-video="banner.full_path"
                                data-isvideo="true">
                                <img :src="`/assets/play-icon.png`" />
                            </SplideSlide>
                            <SplideSlide v-else data-isvideo="false">
                                <img :src="banner.full_path">
                            </SplideSlide>
                        </template>
                    </Splide>
                </div>
            </div>

        </main>
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-2 my-1 border-top">
            <div class="col-md-4 d-flex align-items-center ms-2">
                <img src="/assets/bio-blue.png" style="width:15%" />
                <span class="mb-3 mb-md-0 text- 
                body-secondary">© 2023 Bionergy Project</span>
            </div>

            <div class="nav col-md-4 justify-content-end list-unstyled d-flex me-2">
                Smoking Monitoring System v{{ $page.props.version }}
            </div>
        </footer>
    </div>

    <div class="modal fade" id="msgModal" tabindex="-1" aria-labelledby="msgModalLabel" aria-hidden="true">
        <!-- Vertically centered modal -->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <ul>
                        <li v-for="msg in messages" class="fs-3">
                            {{ msg.msg }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
