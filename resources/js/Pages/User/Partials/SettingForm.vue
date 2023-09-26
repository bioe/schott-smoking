<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm, Link } from '@inertiajs/vue3';
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    cost_center_list: {
        type: Object,
    },
    station_list: {
        type: Object,
    }
});

const routeGroupName = 'users.settings';

const form = useForm({
    cost_center_ids: props.data.cost_center_ids ?? [],
    remote_door_ids: props.data.remote_door_ids ?? []
});
</script>

<template>
    <form @submit.prevent="form.patch(route(routeGroupName + '.update', data.id))">
        <div class="row g-3">
            <div class="col-md-6">
                <InputLabel for="cost-center" value="Select Cost Centers" />
                <Multiselect v-model="form.cost_center_ids" mode="tags" :close-on-select="false" :searchable="true"
                    :create-option="true" :options="props.cost_center_list" />
                <InputError :message="form.errors.cost_center_ids" />
            </div>
            <div class="col-md-6">
                <InputLabel for="cost-center" value="Remote Door Control" />
                <Multiselect v-model="form.remote_door_ids" mode="tags" :close-on-select="false" :searchable="true"
                    :create-option="true" :options="props.station_list" />
                <InputError :message="form.errors.remote_door_ids" />
            </div>


            <div class="col-12">
                <PrimaryButton type="submit" v-html="data.id == null ? 'Create' : 'Save'" :disabled="form.processing">
                </PrimaryButton>
            </div>
        </div>
    </form>
</template>

