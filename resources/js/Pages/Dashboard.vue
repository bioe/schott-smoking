<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    stations: {
        type: Object
    },
    total_entry: {
        type: Number
    },
    total_duration: {
        type: String,
    },
    total_overstay_duration: {
        type: String,
    },
    total_employee: {
        type: Number,
    }
});

const disable_door_btn = ref(false);

function openDoor(id, direction) {
    disable_door_btn.value = true;

    axios.post(route('dashboard.opendoor'), {
        'station_id': id,
        'direction': direction
    }).then(function (response) {
        alert(response.data.message);
        disable_door_btn.value = false;
    }).catch((e) => {
        alert(e.response?.data?.message ?? e);
        disable_door_btn.value = false;
    });
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            Dashboard
        </template>

        <div v-if="stations?.length > 0" class="my-3 p-3 bg-body rounded shadow-sm">
            <h3>Remote Door Control</h3>
            <div class="row">
                <div v-for="station in stations" class="col-sm-6 mb-3 mb-sm-0">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><a :href="route('area', station.code)" target="_blank">{{ station.name
                            }}</a></h5>
                            <button class="btn btn-success me-3" :disabled="disable_door_btn"
                                @click="openDoor(station.id, 'in')">Open Enter Door</button>
                            <button class="btn btn-warning" :disabled="disable_door_btn"
                                @click="openDoor(station.id, 'out')">Open Exit Door</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <h3>Today Summary</h3>
            <div class="row">
                <div class="col-lg-3 text-light">
                    <div class="sensor-column text-center rounded-3">
                        <h2>Total Entry</h2>
                        {{ total_entry }}
                    </div>
                </div>
                <div class="col-lg-3 text-light">
                    <div class="sensor-column text-center rounded-3">
                        <h2>Total Employee</h2>
                        {{ total_employee }}
                    </div>
                </div>
                <div class="col-lg-3 text-light">
                    <div class="sensor-column text-center rounded-3 bg-success">
                        <h2>Total Time</h2>
                        <span style="font-size:3rem;">{{ total_duration }}</span>
                    </div>
                </div>
                <div class="col-lg-3 text-light">
                    <div class="sensor-column text-center rounded-3 bg-warning">
                        <h2>Total Overstay</h2>
                        <span style="font-size:3rem;">{{ total_overstay_duration }}</span>
                    </div>
                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>
