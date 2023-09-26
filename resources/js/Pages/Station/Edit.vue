<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
});

const routeGroupName = 'stations';
const headerTitle = ref('Station');

const form = useForm({
    code: props.data.code ?? '',
    name: props.data.name ?? '',
    max_pax: props.data.max_pax ?? 0,
    stay_duration_seconds: props.data.stay_duration_seconds ?? 0,
    warning_below_seconds: props.data.warning_below_seconds ?? 0,
    disable_next_entry_seconds: props.data.disable_next_entry_seconds ?? 0,
    door_open_seconds: props.data.door_open_seconds ?? 5,
    annoucement_interval: props.data.annoucement_interval ?? 5,
    banner_interval: props.data.banner_interval ?? 5,
    ip: props.data.ip ?? '',
    active: props.data.active,
});

// const submit = () => {
//     form.get(route('users.index'), {
//         preserveScroll: true,
//     });
// };
</script>

<template>
    <Head :title="headerTitle" />

    <AuthenticatedLayout>
        <template #header>
            {{ headerTitle }}
        </template>

        <form
            @submit.prevent="data.id == null ? form.post(route(routeGroupName + '.store')) : form.patch(route(routeGroupName + '.update', data.id))">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_1">Details</a>
                        </li>
                        <!-- <li v-if="data.id" class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2">Permissions</a>
                        </li> -->
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade pt-10 show active" id="tab_1" role="tabpanel" aria-labelledby="tab_1">
                            <div class="row g-3">
                                <div class="col-12">
                                    <Checkbox id="checkActive" v-model:checked="form.active">
                                        Active
                                    </Checkbox>
                                </div>
                                <div class="col-md-4">
                                    <InputLabel for="code" value="Code" />
                                    <TextInput id="code" type="text" v-model="form.code" :invalid="form.errors.code"
                                        :disabled="data.id != null" required />
                                    <InputError :message="form.errors.code" />
                                </div>
                                <div class="col-md-4">
                                    <InputLabel for="name" value="Name" />
                                    <TextInput id="name" type="text" v-model="form.name" :invalid="form.errors.name"
                                        required />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="col-md-4">
                                    <InputLabel for="ip" value="Station IP" />
                                    <TextInput id="ip" type="text" v-model="form.ip" :invalid="form.errors.ip"
                                        placeholder="http://192.168.1.10:80" />
                                    <InputError :message="form.errors.ip" />
                                </div>

                                <div class="col-md-3">
                                    <InputLabel for="max-pax" value="Max Pax" />
                                    <TextInput id="max-pax" type="number" v-model="form.max_pax"
                                        :invalid="form.errors.max_pax" />
                                    <InputError :message="form.errors.max_pax" />
                                </div>

                                <div class="col-md-3">
                                    <InputLabel for="stay" value="Stay duration" />
                                    <div class="input-group">
                                        <TextInput id="stay" type="number" v-model="form.stay_duration_seconds"
                                            :invalid="form.errors.stay_duration_seconds" required
                                            aria-describedby="addon1" />
                                        <span class="input-group-text" id="addon1">seconds</span>
                                        <InputError :message="form.errors.stay_duration_seconds" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <InputLabel for="remaining-time" value="Remaining time warning" />
                                    <div class="input-group">
                                        <TextInput id="remaining-time" type="number" v-model="form.warning_below_seconds"
                                            :invalid="form.errors.warning_below_seconds" required
                                            aria-describedby="addon2" />
                                        <span class="input-group-text" id="addon2">seconds</span>
                                        <InputError :message="form.errors.warning_below_seconds" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <InputLabel for="disable-entry" value="Disable next entry" />
                                    <div class="input-group">
                                        <TextInput id="disable-entry" type="number"
                                            v-model="form.disable_next_entry_seconds"
                                            :invalid="form.errors.disable_next_entry_seconds" required
                                            aria-describedby="addon3" />
                                        <span class="input-group-text" id="addon3">seconds</span>
                                        <InputError :message="form.errors.disable_next_entry_seconds" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <InputLabel for="door-open" value="Door Open Duration" />
                                    <div class="input-group">
                                        <TextInput id="door-open" type="number" v-model="form.door_open_seconds"
                                            :invalid="form.errors.door_open_seconds" required aria-describedby="addon4" />
                                        <span class="input-group-text" id="addon4">seconds</span>
                                        <InputError :message="form.errors.door_open_seconds" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <InputLabel for="annoucement-interval" value="Annoucement Interval" />
                                    <div class="input-group">
                                        <TextInput id="annoucement-interval" type="number"
                                            v-model="form.annoucement_interval" :invalid="form.errors.annoucement_interval"
                                            required aria-describedby="addon5" />
                                        <span class="input-group-text" id="addon5">seconds</span>
                                        <InputError :message="form.errors.annoucement_interval" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <InputLabel for="banner-interval" value="Banner Interval" />
                                    <div class="input-group">
                                        <TextInput id="door-open" type="number" v-model="form.banner_interval"
                                            :invalid="form.errors.banner_interval" required aria-describedby="addon6" />
                                        <span class="input-group-text" id="addon6">seconds</span>
                                        <InputError :message="form.errors.banner_interval" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div v-if="data.id" class="tab-pane fade pt-10" id="tab_2" role="tabpanel" aria-labelledby="tab_2">
                            Coming Soon
                        </div> -->
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <Link class="btn btn-secondary me-2" :href="route(routeGroupName + '.index')">Back</Link>
                        <PrimaryButton type="submit" v-html="data.id == null ? 'Create' : 'Save'"
                            :disabled="form.processing"></PrimaryButton>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>