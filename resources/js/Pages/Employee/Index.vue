<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import HeadRow from '@/Components/Table/HeadRow.vue';
import Paginate from '@/Components/Table/Paginate.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { formatDate } from '@/helper';

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
    cost_center_list: {
        type: Object,
    }
});

const routeGroupName = 'employees';
const headerTitle = ref('Employees');
const form = useForm(props.filters);

const sort = (field) => {
    form.sort.field = field;
    form.sort.direction = form.sort.direction == "" || form.sort.direction == "desc" ? "asc" : "desc";
    submit();
}

const submit = () => {
    form.get(route(routeGroupName + '.index'), {
        preserveScroll: true,
    });
};

const destroy = (id, name) => {
    const c = confirm(`Delete this employee ${name} ?`);
    if (c) {
        router.delete(route(routeGroupName + '.destroy', id));
    }
};
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
                            <select v-model="form.cost_center_id" class="form-select">
                                <option :value=null>All</option>
                                <option v-for="cc in cost_center_list" :value="cc.id">{{ cc.code }}</option>
                                <option v-if="$page.props.auth.user.cost_center_id == null" :value="'is_null'">No Cost
                                    Center
                                </option>
                            </select>
                            <label for="keywordInput">Cost Center</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <PrimaryButton type="submit" :disabled="form.processing">
                            <i class="bi bi-search"></i>
                            Search
                        </PrimaryButton>
                    </div>
                </div>
            </form>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                <Link class="btn btn-outline-primary btn-sm" :href="route(routeGroupName + '.create')">
                <i class="bi bi-plus"></i>
                Create
                </Link>
            </div>

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
                            <Link :href="route(routeGroupName + '.edit', item.id)" class="btn btn-sm btn-link">
                            <i class="bi bi-pencil"></i>
                            </Link>
                            <button @click="destroy(item.id, item.name)" class="btn btn-sm btn-link">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                        <td :class="item.active == 0 ? 'text-danger' : ''">{{ item.staff_no }}</td>
                        <td>{{ item.card_id }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.cost_center?.code }}</td>
                        <td>{{ formatDate(item.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
            <Paginate :data="list" />
        </div>


    </AuthenticatedLayout>
</template>