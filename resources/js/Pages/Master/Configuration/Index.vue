<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/inertia-vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SuccessToast from '@/Components/SuccessToast.vue';
import {onMounted} from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import {Inertia} from "@inertiajs/inertia";
import InputError from '@/Components/InputError.vue';

const props = defineProps(['configuration', 'days', 'locations']);

const form = useForm({
    location_id: props.configuration.location_id,
    accepted_distance: props.configuration.accepted_distance,
    presensi_masuk_dari: props.configuration.presensi_masuk_dari,
    presensi_masuk_sampai: props.configuration.presensi_masuk_sampai,
    presensi_pulang_dari: props.configuration.presensi_pulang_dari,
    presensi_pulang_sampai: props.configuration.presensi_pulang_sampai,
    toleransi_keterlambatan: props.configuration.toleransi_keterlambatan,
    maksimal_terlambat: props.configuration.maksimal_terlambat,
    hari_libur: props.configuration.hari_libur
})

function locationWatcher(){
    if (form.location_id === "Tambah lokasi"){
        return Inertia.get(route('locations.create'));
    }
}

onMounted(() => {
    const map = L.map('map').setView([props.configuration.location.latitude, props.configuration.location.longitude], 16);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const circle = L.circle([props.configuration.location.latitude, props.configuration.location.longitude], {
        color: '#22c55e',
        fillColor: '#bbf7d0',
        fillOpacity: 0.5,
        radius: props.configuration.accepted_distance
    }).addTo(map);

    circle.bindPopup(`
    <b>${props.configuration.location.name}</b><br>
    Radius presensi: ${props.configuration.accepted_distance} meter
    `).openPopup();
})
</script>

<template>
    <Head>
        <title>Pengaturan Presensi</title>
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pengaturan Presensi
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="max-w-xl">
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    Pengaturan Presensi
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    Perbarui pengaturan titik lokasi presensi, radius, dan lain-lain sesuai dengan kebutuhan perusahaan.
                                </p>
                            </header>

                            <form @submit.prevent="form.put(route('configurations.update', configuration.id))" class="space-y-6 mt-6">
                                <!-- Tempat presensi -->
                                <div>
                                    <InputLabel for="location_id" value="Lokasi titik presensi"/>
                                    <select v-model="form.location_id" id="location_id" @change="locationWatcher" class="mt-1 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option v-for="location in locations" :value="location.id">
                                            {{ location.name }}
                                        </option>
                                        <option>Tambah lokasi</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.location_id"/>
                                </div>

                                <!-- Radius presensi -->
                                <div>
                                    <InputLabel value="Radius presensi"/>
                                    <TextInput type="number" id="accepted_distance" class="mt-1 block w-full" v-model="form.accepted_distance"/>
                                    <InputError class="mt-2" :message="form.errors.accepted_distance"/>
                                </div>

                                <!-- Presensi Masuk -->
                                <div>
                                    <InputLabel value="Presensi masuk" class="mb-2"/>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Dari -->
                                        <div>
                                            <InputLabel for="presensi_masuk_dari" value="Dari"/>
                                            <TextInput type="time" id="presensi_masuk_dari" class="mt-1 block w-full" v-model="form.presensi_masuk_dari" step="2"/>
                                            <InputError class="mt-2" :message="form.errors.presensi_masuk_dari"/>
                                        </div>

                                        <!-- Sampai -->
                                        <div>
                                            <InputLabel for="presensi_masuk_sampai" value="Sampai"/>
                                            <TextInput type="time" id="presensi_masuk_sampai" class="mt-1 block w-full" v-model="form.presensi_masuk_sampai" step="2"/>
                                            <InputError class="mt-2" :message="form.errors.presensi_masuk_sampai"/>
                                        </div>

                                        <!-- Toleransi Keterlambatan -->
                                        <div>
                                            <InputLabel for="toleransi_keterlambatan" value="Toleransi keterlambatan"/>
                                            <TextInput type="time" id="toleransi_keterlambatan" class="mt-1 block w-full" v-model="form.toleransi_keterlambatan" step="2"/>
                                            <InputError class="mt-2" :message="form.errors.toleransi_keterlambatan"/>
                                        </div>

                                        <!-- Maksimal Terlambat -->
                                        <div>
                                            <InputLabel for="maksimal_terlambat" value="Maksimal terlambat"/>
                                            <TextInput type="time" id="maksimal_terlambat" class="mt-1 block w-full" v-model="form.maksimal_terlambat" step="2"/>
                                            <InputError class="mt-2" :message="form.errors.maksimal_terlambat"/>
                                        </div>
                                    </div>
                                </div>

                                <!-- Presensi Pulang -->
                                <div>
                                    <InputLabel value="Presensi pulang" class="mb-2"/>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel for="presensi_pulang_dari" value="Dari"/>
                                            <TextInput type="time" id="presensi_pulang_dari" class="mt-1 block w-full" v-model="form.presensi_pulang_dari" step="2"/>
                                            <InputError class="mt-2" :message="form.errors.presensi_pulang_dari"/>
                                        </div>
                                        <div>
                                            <InputLabel for="presensi_pulang_sampai" value="Sampai"/>
                                            <TextInput type="time" id="presensi_pulang_sampai" class="mt-1 block w-full" v-model="form.presensi_pulang_sampai" step="2"/>
                                            <InputError class="mt-2" :message="form.errors.presensi_pulang_sampai"/>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hari libur -->
                                <div>
                                    <InputLabel for="hari_libur" value="Hari libur"/>
                                    <select v-model="form.hari_libur" id="hari_libur" class="mt-1 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option v-for="day in days">
                                            {{ day }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.hari_libur"/>
                                </div>

                                <PrimaryButton>Simpan</PrimaryButton>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                Lokasi Titik Presensi
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Pegawai dapat melakukan presensi di dalam area yang berwarna hijau.
                            </p>
                        </header>

                        <div id="map" class="bg-gray-200 rounded h-[400px] mt-6"></div>
                    </div>
                </div>
            </div>
        </div>


        <SuccessToast v-if="$page.props.flash.message" :message="$page.props.flash.message"/>
    </AuthenticatedLayout>
</template>
