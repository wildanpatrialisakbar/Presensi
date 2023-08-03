<script setup>
import Header from "./partials/Header.vue";
import Hero from "./partials/Hero.vue";
//import Feature from "./partials/Feature.vue";
//import HowItWork from "./partials/HowItWork.vue";
//import Test from "./partials/Test.vue";
// import Offer from './partials/Offer.vue';
//import Testimonial from "./partials/Testimonial.vue";
//import Faq from "./partials/Faq.vue";
import Footer from "./partials/Footer.vue";
import { Head } from "@inertiajs/inertia-vue3";
</script>

<template>
    <Head>
        <title>Sistem Presensi</title>
        <meta name="title" content="Presensi" />
        <meta
            name="description"
            content="Sistem informasi presensi.
        Dilengkapi dengan fitur presensi menggunakan lokasi, dan permohonan pengajuan cuti. Cobain sekarang!"
        />
    </Head>

    <div class="bg-white dark:bg-gray-900 ">
        <Header />
        <Hero />
        <!-- <Feature /> -->
        <!-- <HowItWork /> -->
        <!-- <Test /> -->
        <!--        <Offer/>-->
        <!-- <Testimonial /> -->
        <!-- <Faq /> -->
        <Footer />
    </div>
</template>
