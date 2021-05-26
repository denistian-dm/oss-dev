<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Perlu Penyelesaian IT
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filter panel -->
                <div class="px-8 pb-8 pt-6 bg-white overflow-auto shadow sm:rounded-lg mb-8">
                    <span>Pencarian</span>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-6 gap-4">
                        <div class="col-span-2">
                            <div class="flex">
                                <calendar 
                                    placeholder="Tanggal Awal"
                                    v-model="filter.dateStart" 
                                    dateFormat="yy-mm-dd"
                                    :maxDate="filter.dateEnd"
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
                                    :minDate="filter.dateStart"
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

                <!-- datatables -->
                <div class="bg-white overflow-auto shadow-xl sm:rounded-lg">
                    <div class="p-datatable p-component p-datatable-responsive-stack" v-if="!isLoadDataCompleted">
                        <div class="p-datatable-wrapper">
                            <table class="p-datatable-table" role="table">
                                <thead class="p-datatable-thead">
                                    <tr role="row">
                                        <th role="cell">No</th>
                                        <th role="cell">Tanggal</th>
                                        <th role="cell">Case</th>
                                        <th role="cell">Status</th>
                                        <th role="cell">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="p-datatable-tbody">
                                    <tr role="row" v-for="n in 5" :key="n">
                                        <td role="cell"><skeleton borderRadius="0.375rem" /></td>
                                        <td role="cell"><skeleton borderRadius="0.375rem" /></td>
                                        <td role="cell"><skeleton borderRadius="0.375rem" /></td>
                                        <td role="cell"><skeleton borderRadius="0.375rem" /></td>
                                        <td role="cell"><skeleton borderRadius="0.375rem" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <data-table :value="data.data" :paginator="true" :rows="20" ref="dt" v-if="isLoadDataCompleted">
                        <column header="No" class="whitespace-nowrap">
                            <template #body="slotProps">
                                {{slotProps.index + 1}}
                            </template>
                        </column>

                        <column field="created_at" header="Tanggal" class="whitespace-nowrap"></column>
                        <column field="juklak.name" header="Case" class="whitespace-nowrap"></column>

                        <column class="whitespace-nowrap">
                            <template #header>
                                <div class="flex justify-center">Status</div>
                            </template>

                            <template #body="slotProps">
                                <div class="flex justify-center">
                                    <pills :theme="slotProps.data.bug_ticket_status.id">{{ slotProps.data.bug_ticket_status.name }}</pills>
                                </div>
                            </template>
                        </column>

                        <column header="Action" class="whitespace-nowrap">
                            <template #body="slotProps">
                                <div class="hidden md:inline-flex">
                                    <prime-button icon="pi pi-eye" class="p-button-rounded p-button-text p-button-plain" @click.enter="view(slotProps.data.id)" v-tooltip.bottom="'Lihat Case'"/>
                                    <prime-button icon="pi pi-comments" class="p-button-rounded p-button-text" @click.enter="show(slotProps.data.id)" v-tooltip.bottom="'Respon Case'"/>
                                </div>
                                <div class="md:hidden">
                                    <prime-button icon="pi pi-comments" class="p-button-rounded p-button-text" @click.enter="show(slotProps.data.id)" v-tooltip.bottom="'Respon Case'"/>
                                </div>
                            </template>
                        </column>
                    </data-table>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="viewModal" @close="viewModal = false">
            <template #title>
                Comments
            </template>

            <template #content>
                <div class="flex justify-center" v-if="!loadCompleted">
                    <progress-spinner style="width:50px;height:50px" strokeWidth="4" fill="#EEEEEE" animationDuration="1.5s" />
                </div>

                <div class="relative w-full">
                    <div class="border-r-2 border-gray-500 absolute opacity-50 h-full top-0" style="left: 15px"></div>
                    <ul class="list-none p-0 m-0 relative">
                        <li class="mb-2" v-for="data in bugTicketDetails" :key="data.id">
                            <div class="z-20 flex items-center mb-1">
                                <img class="h-8 w-8 rounded-full object-cover" :src="data.user.profile_photo_url" alt="">
                                <div class="flex-1 ml-4">
                                    <span class="font-bold">{{ data.user.name }}</span>
                                    <span class="text-xs text-gray-500"> on {{ data.created_at }}</span>
                                </div>
                            </div>
                            <div class="ml-12" v-html="data.comment"></div>
                        </li>
                    </ul>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.enter="viewModal = false">
                    Tutup
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetButton from '@/Jetstream/Button'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import Pills from '@/MyComponents/Pills'

    import Calendar from 'primevue/calendar'
    import DataTable from 'primevue/datatable'
    import Column from 'primevue/column'
    import PrimeButton from 'primevue/button'
    import Skeleton from 'primevue/skeleton'
    import ProgressSpinner from 'primevue/progressspinner'

    export default {
        components: {
            AppLayout,
            JetButton,
            JetSecondaryButton,
            JetDialogModal,
            Pills,
            Calendar,
            DataTable,
            Column,
            PrimeButton,
            Skeleton,
            ProgressSpinner
        },

        data() {
            return {
                isLoadDataCompleted: false,
                loadCompleted: false,
                viewModal: false,
                filter: {
                    dateStart: new Date(),
                    dateEnd: new Date(),
                    errors: {},
                },

                data: [],
                bugTicketDetails: []
            }
        },

        methods: {
            view(id) {
                this.loadCompleted = false;
                this.viewModal = true;
                this.bugTicketDetails = [];

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.get('http://localhost:8000/api/data/case-resolution/'+ id)
                            .then(result => {
                                this.bugTicketDetails = result.data.data.details
                                this.loadCompleted = true
                            })
                            .catch(err => console.log(err))
                    }).catch(err => console.log(err))
            },

            show(id) {
                this.$inertia.get('http://localhost:8000/case-resolution/'+id)
            },

            filterData(filter) {
                this.filter.errors = {}

                let data = new FormData;
                data.append('dateStart', filter.dateStart);
                data.append('dateEnd', filter.dateEnd);

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.post('http://localhost:8000/api/data/case-resolution/filter', data)
                            .then(result => {
                                this.data = result.data;
                                this.isLoadDataCompleted = true;
                            }).catch(err => this.filter.errors = err.response.data.errors)
                    }).catch(err => console.log(err))
            },
        },

        created() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/case-resolution')
                        .then(result => {
                            this.data = result.data
                            this.isLoadDataCompleted = true
                        }).catch(err => console.log(err))
                }).catch(err => console.log(err))
        }
    }
</script>