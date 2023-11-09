<script setup>
import { Head } from '@inertiajs/vue3';
import HeadRow from '@/Components/Table/HeadRow.vue';
import { useTimer } from 'vue-timer-hook';
import { onMounted, ref, computed } from 'vue'
import axios from 'axios';

const props = defineProps({
    data: {
        type: Object
    },
    polling_interval: { type: Number }
});

const list = ref([]);

onMounted(
    () => {
        list.value = props.data.list;

        //Initialize
        refresh();
        setInterval(refresh, props.polling_interval);
    }
)

function refresh() {
    axios.get(route('pax.latest')).then(function (response) {
        list.value = response.data.list;
    });
}

function reloadPage() {
    location.reload();
}

function max_pax_bg(current, max) {
    return current >= max ? 'bg-danger' : 'bg-success';
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="d-flex flex-column min-vh-100">
        <main class="flex-grow-1 p-2">
            <div class="row mb-3">
                <div class="col-lg-12 text-light">
                    <div class="sensor-column schott-colour text-center rounded-3"
                        style="min-height: 142.4px; max-height: 142.4px;">
                        <img src="/assets/schott-logo.png" style="height: 80px; margin-top: 2rem; margin-bottom: 2rem;"
                            alt="logo" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div v-for="l in list" class="col-lg-6 text-light">
                    <!-- <div class="card text-center">
                        <div class="card-header">
                            <h1>{{ l.station }} Pax</h1>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ l.total }}</h5>
                        </div>
                    </div> -->
                    <div class="station-pax text-center rounded-3" :class="max_pax_bg(l.total, l.max_pax)">
                        <h1>{{ l.station }} Pax</h1>
                        {{ l.total }}
                    </div>
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
                        <li v-for="msg in messages" class="fs-1">
                            {{ msg.msg }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
