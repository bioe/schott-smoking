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

const routeGroupName = 'hods';
const headerTitle = ref('HOD');

const form = useForm({
    name: props.data.name ?? '',
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
                                <div class="col-md-4">
                                    <InputLabel for="name" value="Name" />
                                    <TextInput id="name" type="text" v-model="form.name" :invalid="form.errors.name"
                                        required />
                                    <InputError :message="form.errors.name" />
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