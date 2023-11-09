<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import HeadRow from '@/Components/Table/HeadRow.vue';
import Paginate from '@/Components/Table/Paginate.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { formatDate } from '@/helper';
import axios from 'axios';

const props = defineProps({
    header: {
        type: Object
    },
    filters: {
        type: Object
    },
    list: {
        type: Object,
        default: () => ({}),
    },
    station_list: {
        type: Object,
    }
});

//Manually append for export, and use 1/0, true and false will have querystring issue
props.filters.is_export = 0;

const routeGroupName = 'entrylogs';
const headerTitle = ref('Entry Logs');
const form = useForm(props.filters);

const sort = (field) => {
    form.sort.field = field;
    form.sort.direction = form.sort.direction == "" || form.sort.direction == "desc" ? "asc" : "desc";
    submit();
}

function createQueryString(data, prefix = '') {
    return Object.keys(data).map(key => {
        let val = data[key]
        let newPrefix = prefix ? `${prefix}[${key}]` : key
        if (val !== null && typeof val === 'object') {
            return createQueryString(val, newPrefix)
        } else {
            if (val == null) val = "";
            return `${newPrefix}=${encodeURIComponent(`${val}`.replace(/\s/g, '_'))}`
        }
    }).join('&')
}

const submit = () => {
    if (form.is_export) {
        let queryString = createQueryString(form.data());
        axios.get(route(routeGroupName + '.index') + "?" + queryString, { responseType: 'blob' }).then(response => {
            const type = response.headers['content-type']

            const contentDisposition = response.headers['content-disposition'];
            let filename = 'exportfile';

            if (contentDisposition) {
                const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
                if (fileNameMatch && fileNameMatch.length === 2) {
                    filename = fileNameMatch[1];
                }
            }

            const blob = new Blob([response.data], { type: type, encoding: 'UTF-8' })
            const link = document.createElement('a')
            link.href = window.URL.createObjectURL(blob)
            link.download = filename
            link.click()
        })
    } else {
        form.get(route(routeGroupName + '.index'), {
            preserveScroll: true,
        });
    }
};

const destroy = (id, name) => {
    const c = confirm(`Delete this entry ${name} ?`);
    if (c) {
        router.delete(route(routeGroupName + '.destroy', id));
    }
};

const isExport = (cond) => {
    form.is_export = cond;
}

</script>

<template>
    <Head :title="headerTitle" />

    <AuthenticatedLayout>
        <template #header>
            {{ headerTitle }}
        </template>

        <div class="my-3 p-3 bg-body rounded shadow-sm">

            <form @submit.prevent="submit">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input v-model="form.keyword" type="text" class="form-control" id="keywordInput"
                                placeholder="Keyword" autocomplete="off">
                            <label for="keywordInput">Keyword</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input v-model="form.start" type="date" class="form-control" id="startInput" placeholder="Start"
                                autocomplete="off">
                            <label for="startInput">Entry</label>
                        </div>

                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input v-model="form.end" type="date" class="form-control" id="endInput" placeholder="End"
                                autocomplete="off">
                            <label for="endInput">Exit</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <select v-model="form.station_id" class="form-select">
                                <option :value=null>All</option>
                                <option v-for="station in station_list" :value="station.id">{{ station.name }}</option>
                            </select>
                            <label for="stationInput">Station</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <select v-model="form.overstay" class="form-select">
                                <option :value=null>All</option>
                                <option :value="'yes'">Yes</option>
                                <option :value="'no'">No</option>
                            </select>
                            <label for="stationInput">Overstay</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <select v-model="form.exit_time" class="form-select">
                                <option :value=null>All</option>
                                <option :value="'yes'">With Exit Time</option>
                                <option :value="'no'">Without Exit Time</option>
                            </select>
                            <label for="stationInput">Exit Time</label>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-between">
                        <PrimaryButton type="submit" :disabled="form.processing" @click="isExport(0)">
                            <i class="bi bi-search"></i>
                            Search
                        </PrimaryButton>

                        <PrimaryButton type="submit" :disabled="form.processing" @click="isExport(1)">
                            <i class="bi bi-filetype-csv"></i>
                            Export
                        </PrimaryButton>
                    </div>
                </div>
            </form>

            <!-- <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                <Link class="btn btn-outline-primary btn-sm" :href="route(routeGroupName + '.create')">
                <i class="bi bi-plus"></i>
                Create
                </Link>
            </div> -->

            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <HeadRow>Actions</HeadRow>
                        <HeadRow v-for="head in header" :field="head.field" :sort="head.sortable ? filters.sort : null"
                            @sortEvent="sort" :disabled="form.processing">{{ head.title }}</HeadRow>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in list.data">
                        <td width="10%">
                            <button v-if="item.can_delete_entry" @click="destroy(item.id, item.employee?.card_id)"
                                class="btn btn-sm btn-link">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                        <td>{{ item.employee?.staff_no }}</td>
                        <td>{{ item.employee?.name }}</td>
                        <td>{{ item.station?.name }}</td>
                        <td>{{ formatDate(item.enter_time) }}</td>
                        <td>{{ formatDate(item.exit_time) }}</td>
                        <td :class="item.overstay_seconds > 0 ? 'text-danger' : ''">{{ item.stay_label }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <Paginate :data="list" />
        </div>


    </AuthenticatedLayout>
</template>