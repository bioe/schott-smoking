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
    cost_center_list: {
        type: Object,
    }
});

const routeGroupName = 'employees';
const headerTitle = ref('Employee');

const form = useForm({
    staff_no: props.data.staff_no ?? '',
    card_id: props.data.card_id ?? '',
    name: props.data.name ?? '',
    cost_center_id: props.data.cost_center_id ?? null,
    active: props.data.active,
    maintenance: props.data.maintenance
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
                                    <InputLabel for="staff_no" value="Staff No" />
                                    <TextInput id="staff_no" type="text" v-model="form.staff_no"
                                        :invalid="form.errors.staff_no" required />
                                    <InputError :message="form.errors.staff_no" />
                                </div>
                                <div class="col-md-4">
                                    <InputLabel for="card_id" value="Card ID" />
                                    <TextInput id="card_id" type="text" v-model="form.card_id"
                                        :invalid="form.errors.card_id" required />
                                    <InputError :message="form.errors.card_id" />
                                </div>
                                <div class="col-md-4">
                                    <InputLabel for="name" value="Name" />
                                    <TextInput id="name" type="text" v-model="form.name" :invalid="form.errors.name"
                                        required />
                                    <InputError :message="form.errors.name" />
                                </div>

                                <div class="col-md-4">
                                    <InputLabel for="cost_center_id" value="Cost Center" />
                                    <select class="form-select" name="cost_center_id" v-model="form.cost_center_id">
                                        <option :value=null>None</option>
                                        <option v-for=" cc in cost_center_list " :value="cc.id">{{ cc.code }}</option>
                                    </select>
                                    <InputError :message="form.errors.cost_center_id" />
                                </div>

                                <div class="col-12">
                                    <InputLabel for="maintenance" value="Maintenance" />
                                    <Checkbox id="maintenance" v-model:checked="form.maintenance">
                                        No limit access
                                    </Checkbox>
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