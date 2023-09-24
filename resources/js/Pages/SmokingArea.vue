<script setup>
import { Head } from '@inertiajs/vue3';
import HeadRow from '@/Components/Table/HeadRow.vue';
import { Splide, SplideSlide } from '@splidejs/vue-splide';
import '@splidejs/vue-splide/css';
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
    banner_interval: {
        type: Number
    }
});

const list = ref([]);
const annoucement_list = ref([]);
const air = ref(0);
const temperature = ref(0);
const currentDate = ref(new Date());

onMounted(
    () => {
        //Initialize
        refresh();
        setInterval(updateDateTime, 900);
        setInterval(refresh, 5000);
    }
)

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

        for (var entry of response.data.list) {
            entry.timer = useTimer(new Date(entry.finished_at));

            entry.finish_date = new Date(new Date(entry.finished_at));
            let wa = new Date(entry.finished_at)
            entry.warning_date = new Date(wa.getTime() - props.warning_below_seconds * 1000);
        }
        list.value = response.data.list;
        annoucement_list.value = response.data.annoucement_list;
        air.value = response.data.air ?? 0;
        temperature.value = response.data.temperature ?? 0;


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
    <div class="container-fluid p-3 full-height">
        <div class="row mb-3">
            <div class="col-lg-3 text-light">
                <div class="sensor-column schott-colour text-center rounded-3" style="font-size: 2rem !important;">
                    {{ currentDate.toLocaleDateString(undefined, { weekday: 'long' }) }} <br />
                    {{ currentDate.toLocaleDateString(undefined, { day: '2-digit' }) }} {{
                        currentDate.toLocaleDateString(undefined, { month: 'short' }) }} <br />
                    {{ currentDate.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', second: '2-digit' })
                    }}
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
                    <Splide
                        :options="{ rewind: true, autoWidth: true, autoplay: true, interval: annoucement_interval, arrows: false }"
                        aria-label="Annoucements" style="height:100%">
                        <SplideSlide v-for="annoucement in annoucement_list" class="fs-4 text-light" style="width:100%">
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
                            <tr v-for="(item, index) in list" class="fs-3">
                                <td :class="warningColour(item.warning_date, item.finish_date)">{{ item.card_id }}</td>
                                <td :class="warningColour(item.warning_date, item.finish_date)">{{ item.employee?.name }}
                                </td>
                                <td class="text-center" :class="warningColour(item.warning_date, item.finish_date)">
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
            </div>
        </div>
    </div>
</template>
