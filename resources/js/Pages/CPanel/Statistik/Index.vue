<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Statistik Case
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-8 pb-8 pt-6 bg-white overflow-auto shadow sm:rounded-lg mb-8">
                    <span>Filter</span>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-6 gap-4">
                        <div class="col-span-2">
                            <div class="flex">
                                <calendar 
                                    placeholder="Tanggal Awal"
                                    v-model="filter.dateStart" 
                                    dateFormat="yy-mm-dd"
                                    :input-class="'focus:ring-indigo-200 text-base focus:border-indigo-300 flex-1 rounded-l-md block w-full sm:text-sm border-gray-300'" 
                                    :input-style="'border-radius: 0.375rem 0px 0px 0.375rem;'"/>
                                <span 
                                    class="inline-flex items-center px-3 rounded-none border border-l-0 border-r-0 border-gray-300 bg-gray-50 text-indigo-500 hover:text-indigo-700 text-sm">
                                    TO
                                </span>
                                <calendar 
                                    placeholder="Tanggal Akhir"
                                    v-model="filter.dateEnd" 
                                    dateFormat="yy-mm-dd"
                                    :input-class="'focus:ring-indigo-200 text-base focus:border-indigo-300 flex-1 rounded-l-md block w-full sm:text-sm border-gray-300'" 
                                    :input-style="'border-radius: 0rem 0.375rem 0.375rem 0rem;'"/>
                            </div>
                            <div class="text-red-500" v-if="filter.errors.dateStart">{{ filter.errors.dateStart[0] }}</div>
                            <div class="text-red-500" v-if="filter.errors.dateEnd">{{ filter.errors.dateEnd[0] }}</div>
                        </div>

                        <div class="col-span-2">
                            <jet-button class="text-sm" @click.enter="filterData(filter)">
                                Cari
                            </jet-button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white overflow-auto shadow sm:rounded-lg mb-8">
                        <div class="px-8 py-4 bg-gray-200">
                            Top Categories
                        </div>
                        <div>
                            <ul>
                                <li 
                                    v-for="tc in statistik.top_categories" 
                                    :key="tc.id" 
                                    @click.enter="passingToCase(tc.id)"
                                    class="flex justify-between px-8 py-4 border-b-2 last:border-b-0 cursor-pointer hover:bg-gray-100">
                                    {{ tc.name }}
                                    <badge :value="tc.cases_count ? tc.cases_count : '0'"></badge>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-white overflow-auto shadow sm:rounded-lg mb-8">
                        <div class="px-8 py-4 bg-gray-200">
                            Top Cases
                        </div>
                        <div>
                            <ul>
                                <li 
                                    v-for="topCase in statistik.top_cases" 
                                    :key="topCase.juklak_id"
                                    @click.enter="passingToCase(topCase.juklak.juklak_category_id, topCase.juklak_id)" 
                                    class="flex justify-between px-8 py-4 border-b-2 last:border-b-0 cursor-pointer hover:bg-gray-100">
                                    {{ topCase.juklak.name }}
                                    <badge :value="topCase.total ? topCase.total : '0'"></badge>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-white overflow-auto shadow sm:rounded-lg mb-8">
                        <div class="px-8 py-4 bg-gray-200">
                            Perlu Penyelesaian IT
                        </div>
                        <div>
                            <ul>
                                <li 
                                    v-for="ppi in statistik.ppi" 
                                    :key="ppi.juklak_id" 
                                    @click.enter="passingToCase(ppi.juklak.juklak_category_id, ppi.juklak_id, 4)" 
                                    class="flex justify-between px-8 py-4 border-b-2 last:border-b-0 cursor-pointer hover:bg-gray-100">
                                    {{ ppi.juklak.name }}
                                    <badge :value="ppi.total ? ppi.total : '0'"></badge>
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
    import JetButton from '@/Jetstream/Button'
    import Badge from 'primevue/badge'
    import Calendar from 'primevue/calendar'

    export default {
        components: {
            AppLayout,
            JetButton,
            Badge,
            Calendar
        },

        data() {
            return {
                statistik: [],
                filter: {
                    dateStart: '',
                    dateEnd: '',
                    errors: {},
                }
            }
        },

        methods: {
            passingToCase(juklak_category, juklak = null, status = null) {
                this.$inertia.get('http://localhost:8000/case', {
                        data: {
                            juklak_category, 
                            juklak, 
                            status,
                            dateStart: this.filter.dateStart,
                            dateEnd: this.filter.dateEnd
                        }
                    }
                );
            },

            filterData(filter) {
                this.filter.errors = {};
                let data = new FormData;

                data.append('dateStart', filter.dateStart);
                data.append('dateEnd', filter.dateEnd);

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.post('http://localhost:8000/api/data/case-statistik/filter', data)
                            .then(result => this.statistik = result.data)
                            .catch(err => this.filter.errors = err.response.data.errors)
                    }).catch(err => console.log(err))
            },

            show() {
                console.log(this.filter.dateEnd);
            }
        },

        created() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/case-statistik')
                        .then(result => this.statistik = result.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err))
        }
    }
</script>