<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-3 gap-12">
                    <div>
                        <div 
                            @click.enter="passingToCase(1)"
                            class="p-8 mb-6 bg-white overflow-hidden shadow hover:shadow-md cursor-pointer sm:rounded-lg flex items-center justify-between">
                            <div class="flex">
                                <i class="pi pi-comments text-yellow-500 shadow rounded-full p-3 mr-3" style="fontSize: 1.5rem"></i>
                                <div>
                                    <span class="text-xl font-semibold">{{ pending }} Case Pending</span>
                                    <br>
                                    <span class="text-sm text-gray-500">Lihat Detail</span>
                                </div>
                            </div>
                            <div>
                                <i class="pi pi-angle-right"></i>
                            </div>
                        </div>

                        <div 
                            @click.enter="passingToCase(3)"
                            class="p-8 mb-6 bg-white overflow-hidden shadow hover:shadow-md cursor-pointer sm:rounded-lg flex items-center justify-between">
                            <div class="flex">
                                <i class="pi pi-comments text-red-500 shadow rounded-full p-3 mr-3" style="fontSize: 1.5rem"></i>
                                <div>
                                    <span class="text-xl font-semibold">{{ urgent }} Case Urgent</span>
                                    <br>
                                    <span class="text-sm text-gray-500">Lihat Detail</span>
                                </div>
                            </div>
                            <div>
                                <i class="pi pi-angle-right"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="px-8 py-4 bg-indigo-500 text-gray-100">
                            New Cases
                        </div>
                        <div>
                            <ul>
                                <li 
                                    v-for="data in dataCase.data" 
                                    :key="data.id" 
                                    @click.enter="show(data.id)"
                                    class="flex justify-between items-center px-8 py-2 border-b-2 border-gray-100 last:border-b-0 cursor-pointer hover:bg-gray-100 text-gray-800">
                                    <div class="flex items-center">
                                        <img :src="data.user.profile_photo_url" class="h-8 w-8 rounded-full mr-3">
                                        <span>{{ data.juklak.name }}</span>
                                    </div>
                                    <i class="pi pi-angle-right"></i>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'
    import PrimeButton from 'primevue/button'

    export default {
        components: {
            AppLayout,
            Welcome,
            PrimeButton
        },

        data() {
            return {
                dataCase: [],
                pending: '',
                urgent: '',
            }
        },

        methods: {
            show(id) {
                this.$inertia.get('http://localhost:8000/case/'+id)
            },

            passingToCase(status) {
                this.$inertia.get('http://localhost:8000/case', {
                        data: {
                            status
                        }
                    }
                );
            },
        },

        created() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/case-new')
                        .then(result => {
                            this.dataCase = result.data;
                            this.pending = result.data.pending
                            this.urgent = result.data.urgent
                        }).catch(err => console.log(err))
                }).catch(err => console.log(err))
        }
    }
</script>
