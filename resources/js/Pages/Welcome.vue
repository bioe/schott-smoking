<script setup>
import { Head } from '@inertiajs/vue3';
import HeadRow from '@/Components/Table/HeadRow.vue';
import { Splide, SplideSlide } from '@splidejs/vue-splide';
import '@splidejs/vue-splide/css';
import { useTimer } from 'vue-timer-hook';
import { onMounted, ref } from 'vue'

const timers = ref([]);

const restartTimer = (index) => {
    const time = new Date();
    time.setSeconds(time.getSeconds() + 600); // 10 minutes timer
    timers.value[index].restart(time);
};

onMounted(
    () => {
        for (let i = 0; i < 20; i++) {
            const time = new Date();
            time.setSeconds(time.getSeconds() + 600); // 10 minutes timer for each row
            timers.value.push(useTimer(time));
        }
    }
)

</script>

<template>
    <Head title="Dashboard" />
    <div class="container-fluid p-3 full-height">
        <div class="row">
            <div class="col-lg-4">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="schott-colour-dark text-light">
                            <HeadRow width="20%">ID</HeadRow>
                            <HeadRow>Name</HeadRow>
                            <HeadRow width="10%">Remaining</HeadRow>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(timer, index) in timers">
                            <td>11111110101{{ index }}</td>
                            <td>John {{ index }}</td>
                            <td class="text-center">{{ timer.minutes }}:{{ timer.seconds.toLocaleString(undefined, {
                                minimumIntegerDigits: 2
                            }) }} </td>
                        </tr>

                    </tbody>
                </table>

            </div>
            <div class="col-lg-8">
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-1 me-2">
                                <div class="square-badge schott-colour">
                                    15
                                </div>
                            </div>
                            <div class="flex-fill">
                                <Splide
                                    :options="{ rewind: true, autoWidth: true, autoplay: true, interval: 1000, arrows: false }"
                                    aria-label="My Favorite Images" style="height:100%">
                                    <SplideSlide class="fs-3" style="width:100%">
                                        Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing
                                        industries for previewing layouts and visual mockups.
                                    </SplideSlide>
                                    <SplideSlide class="fs-3" style="width:100%;">
                                        Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing
                                        industries for previewing layouts and visual mockups.
                                    </SplideSlide>
                                    <SplideSlide class="fs-3" style="width:100%">
                                        Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing
                                        industries for previewing layouts and visual mockups.
                                    </SplideSlide>
                                </Splide>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row schott-bg">
                    <div class="col-12">
                        <Splide :options="{
                            type: 'loop',
                            direction: 'ttb',
                            focus: 'center',
                            autoHeight: true,
                            autoplay: true,
                            interval: 2000,
                            width: '100vw',
                            height: '80vh',
                        }" aria-label="My Favorite Images">
                            <SplideSlide>
                                <img src="storage/image1.jpg" class="p-2">
                            </SplideSlide>
                            <SplideSlide>
                                <div>
                                    <img src="storage/image2.jpg" class="p-2">
                                </div>
                            </SplideSlide>
                            <SplideSlide>
                                <img src="storage/image3.jpg" class="p-2">
                            </SplideSlide>
                        </Splide>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
