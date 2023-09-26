<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    stations: {
        type: Object
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

        <div class="my-3 p-3 bg-body rounded shadow-sm">
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


    </AuthenticatedLayout>
</template>
