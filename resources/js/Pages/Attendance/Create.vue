<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/inertia-vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import BackLink from '@/Components/BackLink.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps(['users', 'status', 'errors']);

const form = useForm({
    user_id: null,
    tanggal: null,
    status: "Belum ditentukan",
    lembur: null
})

</script>

<template>
    <Head>
        <title>Formulir Permohonan Pengajuan Cuti</title>
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <BackLink :href="route('attendances.index')" />

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Formulir Tambah Presensi</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="w-full md:max-w-md md:mx-auto px-4 py-2">
                            <div class="text-base text-gray-500 mb-2">
                                <span class="text-red-500">*</span> <span>Wajib diisi</span>
                            </div>

                            <form @submit.prevent="form.post(route('attendances.store'))">

                                <!-- Pegawainya -->
                                <div class="mt-4">
                                    <InputLabel for="user_id" value="Nama Pegawai" :required-data="true"/>
                                    <select v-model="form.user_id" id="user_id" class="mt-1 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option v-for="user in users" :value="user.id">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.user_id"/>
                                    <InputError class="mt-2" :message="errors.message"/>
                                </div>

                                <!-- Statusnya -->
                                <div class="mt-4">
                                    <InputLabel for="status" value="Status Kehadiran" :required-data="true"/>
                                    <select v-model="form.status" id="status" class="mt-1 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                        <option v-for="s in status" :value="s">
                                            {{ s }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.status"/>
                                </div>

                                <!-- Tanggalnya -->
                                <div class="mt-4">
                                    <InputLabel for="tanggal" value="Tanggal Presensi" :required-data="true" />
                                    <TextInput id="status" v-model="form.tanggal" type="date" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.tanggal"/>
                                </div>

                                <!-- Lemburnya -->
                                <div class="mt-4">
                                    <InputLabel for="lembur" value="Jam Lembur" />
                                    <TextInput id="lembur" v-model="form.lembur" type="number" class="mt-1 block w-full" min="0" max="6" />
                                    <InputError class="mt-2" :message="form.errors.lembur"/>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Simpan
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
