<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/inertia-vue3';
import BackLink from '@/Components/BackLink.vue';
import {onMounted} from "vue";
import {Inertia} from '@inertiajs/inertia';
import dayjs from "dayjs";
import SuccessToast from "@/Components/SuccessToast.vue";
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps(['attendance', 'user', 'location', 'status']);

onMounted(() => {
    const map = L.map('map').setView([props.attendance.latitude_masuk, props.attendance.longitude_masuk], 19);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const markerMasuk = L.marker([props.attendance.latitude_masuk, props.attendance.longitude_masuk]).addTo(map);
    markerMasuk.bindPopup(`Presensi masuk`).openPopup();

    if(props.attendance.latitude_pulang !== null && props.attendance.longitude_pulang !== null){
        const markerPulang = L.marker([props.attendance.latitude_pulang, props.attendance.longitude_pulang]).addTo(map);
        markerPulang.bindPopup(`Presensi pulang`).openPopup();
    }
})

const destroy = (id) => {
    if (confirm('Apakah anda yakin ingin menghapus presensi ini?')) {
        return Inertia.delete(route('attendances.destroy', id));
    }
}

const edit = (id) => {
    return Inertia.get(route('attendances.edit', id));
}

const formatTime = (time) => {
    return dayjs(time).format('DD MMMM YYYY');
}

const formatHHmmss = (time) => {
    return dayjs(time).format('HH:mm:ss');
}

const form = useForm({
    status: props.attendance.status
})

const updateStatus = () => {
    return Inertia.put(route('attendances.update', props.attendance.id), form);
}

const renderDistance = (distance) => {
    return distance === null ? '' : "Sekitar <strong>" + distance + " meter </strong> dari titik presensi."
}
</script>

<template>
    <Head>
        <title>Detail presensi</title>
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <BackLink :href="route('attendances.index')"/>

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail presensi
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="max-w-xl">
                            <div class="grid grid-cols-2 gap-6">

                                <!-- Tanggal -->
                                <div class="text-gray-600">Tanggal Presensi</div>
                                <div class="text-right">{{ formatTime(attendance.created_at) }}</div>

                                <!-- Detail pegawai -->
                                <div class="text-gray-600">Pegawai</div>
                                <div class="text-right">
                                    <div>
                                        {{ user.name }}
                                    </div>
                                    <div class="text-sm uppercase text-gray-600">
                                        {{ user.nip }}
                                    </div>
                                </div>

                                <!-- Jam masuk -->
                                <div class="text-gray-600">Jam Masuk</div>
                                <div class="text-right">
                                    <div>{{ formatHHmmss(attendance.jam_masuk) }}</div>
                                    <div class="text-sm text-gray-600" v-html="renderDistance(attendance.distance_masuk)"></div>
                                </div>

                                <!-- Jam pulang -->
                                <div class="text-gray-600">Jam Pulang</div>
                                <div class="text-right">
                                    <div>{{ attendance.jam_pulang ? formatHHmmss(attendance.jam_pulang) : ''}}</div>
                                    <div class="text-sm text-gray-600" v-html="renderDistance(attendance.distance_pulang)"></div>
                                </div>

                                <!-- Lembur -->
                                <div class="text-gray-600">Lembur</div>
                                <div class="text-right">{{ attendance.lembur ? attendance.lembur + ' jam' : '-'}}</div>

                                <!-- Status kehadiran -->
                                <div class="text-gray-600">Status Kehadiran</div>
                                <div class="text-right">{{ attendance.status }}</div>

                                <div id="map" class="col-span-2 h-[400px]"></div>

                                <div class="col-span-2">
                                    <InputLabel for="status" value="Status Kehadiran"/>
                                    <select v-model="form.status" @change="updateStatus" id="status" class="block mt-1 w-full text-gray-500 capitalize rounded border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                                        <option v-for="option in props.status" :value="option">{{ option }}</option>
                                    </select>
                                </div>

                                <div class="col-span-2">
                                    <button @click="destroy(attendance.id)" type="button"
                                            class="flex justify-center space-x-2 uppercase tracking-widest text-sm px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                             class="w-5 h-5">
                                            <path fill-rule="evenodd"
                                                  d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <SuccessToast v-if="$page.props.flash.message" :message="$page.props.flash.message"/>
</template>
