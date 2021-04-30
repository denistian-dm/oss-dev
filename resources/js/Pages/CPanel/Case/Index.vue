<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Case
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-auto shadow-xl sm:rounded-lg">
                    <data-table :value="dataCase.data">
                        <column header="No" class="whitespace-nowrap">
                            <template #body="slotProps">
                                {{slotProps.index + 1}}
                            </template>
                        </column>
                        <column header="Reference" class="whitespace-nowrap">
                            <template #body="slotProps">
                                <div>
                                    <span class="text-red-700 font-semibold">{{slotProps.data.reference}}</span>
                                    <br />
                                    <span class="text-sm">{{slotProps.data.created_at}}</span>
                                </div>
                            </template>
                        </column>
                        <column field="member.id_member" header="ID Member" class="whitespace-nowrap"></column>
                        <column field="user.name" header="CS" class="whitespace-nowrap"></column>
                        <column field="juklak.name" header="Case" class="whitespace-nowrap"></column>
                        <column field="category.name" header="Kategori" class="whitespace-nowrap"></column>
                        <column header="Status" class="whitespace-nowrap">
                            <template #body="slotProps">
                                <pills :theme="slotProps.data.case_status.id">{{ slotProps.data.case_status.name }}</pills>
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

        <jet-dialog-modal :show="openViewModal" @close="openViewModal = false">
            <template #title>
                Case Detail
            </template>

            <template #content>
                <div class="relative w-full">
                    <div class="border-r-2 border-gray-500 absolute opacity-50 h-full top-0" style="left: 15px"></div>
                    <ul class="list-none p-0 m-0 relative">
                        <li class="mb-2" v-for="data in dataCaseDetails.data" :key="data.id">
                            <div class="z-20 flex items-center mb-1">
                                <img class="h-8 w-8 rounded-full object-cover" :src="data.user.profile_photo_url" alt="">
                                <div class="flex-1 ml-4">
                                    <span class="font-bold">{{ data.user.name }}</span>
                                    <span class="text-xs text-gray-500"> on {{ data.created_at }}</span>
                                </div>
                            </div>
                            <div class="ml-12" v-html="data.deskripsi"></div>
                        </li>
                    </ul>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.enter="openViewModal = false">
                    Tutup
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import Pills from '@/MyComponents/Pills'

    import DataTable from 'primevue/datatable'
    import Column from 'primevue/column'
    import PrimeButton from 'primevue/button'
    import Timeline from 'primevue/timeline'
    import Card from 'primevue/card'

    export default {
        data() {
            return {
                dataCase: [],
                openViewModal: false,
                dataCaseDetails: []
            }
        },

        components: {
            AppLayout,
            JetDialogModal,
            JetSecondaryButton,
            Pills,
            DataTable,
            Column,
            PrimeButton,
            Timeline,
            Card
        },

        methods: {
            view(id) {
                this.openViewModal = true;
                this.dataCaseDetails = [];

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.get('http://localhost:8000/api/data/case-details/'+ id +'/show-by-id-case')
                            .then(result => {
                                this.dataCaseDetails = result.data
                            })
                            .catch(err => console.log(err))
                    }).catch(err => console.log(err))

            },

            show(id) {
                this.$inertia.get('http://localhost:8000/case/'+id)
            }
        },

        created() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/case')
                        .then(result => this.dataCase = result.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err))
        }
    }
</script>

<style>
    .p-datatable table{
        table-layout: auto;
    }
</style>

<style lang="scss" scoped>
    .custom-marker {
        display: flex;
        width: 2rem;
        height: 2rem;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        border-radius: 50%;
        z-index: 1;
    }

    ::v-deep(.p-timeline-event-content)
    ::v-deep(.p-timeline-event-opposite) {
        line-height: 1;
    }

    @media screen and (max-width: 960px) {
        ::v-deep(.customized-timeline) {
                .p-timeline-event:nth-child(even) {
                    flex-direction: row !important;
                    
                    .p-timeline-event-content {
                        text-align: left !important;
                    }
                }

                .p-timeline-event-opposite {
                    flex: 0;
                }

                .p-card {
                    margin-top: 1rem;
                }
            }
    }
</style>