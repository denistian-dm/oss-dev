<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Case
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- filter panel -->
                <div class="px-8 pb-8 pt-6 bg-white overflow-auto shadow sm:rounded-lg mb-8">
                    <span>Pencarian</span>
                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-6 gap-4">
                        <div class="col-span-2">
                            <div class="flex">
                                <calendar 
                                    placeholder="Tanggal Awal"
                                    v-model="filter.dateStart" 
                                    dateFormat="yy-mm-dd"
                                    :input-class="'focus:ring-indigo-200 focus:border-indigo-300 flex-1 rounded-l-md block w-full sm:text-sm border-gray-300'" 
                                    :input-style="'border-radius: 0.375rem 0px 0px 0.375rem;'"/>
                                <span 
                                    class="inline-flex items-center px-3 rounded-none border border-l-0 border-r-0 border-gray-300 bg-gray-50 text-indigo-500 hover:text-indigo-700 text-sm">
                                    TO
                                </span>
                                <calendar 
                                    placeholder="Tanggal Akhir"
                                    v-model="filter.dateEnd" 
                                    dateFormat="yy-mm-dd"
                                    :input-class="'focus:ring-indigo-200 focus:border-indigo-300 flex-1 rounded-l-md block w-full sm:text-sm border-gray-300'" 
                                    :input-style="'border-radius: 0rem 0.375rem 0.375rem 0rem;'"/>
                            </div>
                            <div class="text-red-500" v-if="filter.errors.dateStart">{{ filter.errors.dateStart[0] }}</div>
                            <div class="text-red-500" v-if="filter.errors.dateEnd">{{ filter.errors.dateEnd[0] }}</div>
                        </div>

                        <div class="col-span-2">
                            <jet-input type="text" class="block w-full" placeholder="No. Referece" v-model="filter.reference" />
                        </div>
                    </div>

                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-6 gap-4">
                        <div>
                            <select id="case_status" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" v-model="filter.caseStatus">
                                <option value="" class="text-gray-400">-- Select Status --</option>
                                <option v-for="status in filter.dataCaseStatus.data" :key="status.id" :value="status.id">{{ status.name }}</option>
                            </select>
                        </div>

                        <div>
                            <select id="client" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" v-model="filter.client">
                                <option value="" class="text-gray-400">-- Select Client --</option>
                                <option v-for="client in filter.dataClient.data" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>

                        <div>
                            <select id="category" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" v-model="filter.caseCategory">
                                <option value="" class="text-gray-400">-- Select Category --</option>
                                <option v-for="category in filter.dataCaseCategory.data" :key="category.id" :value="category.id">{{ category.name }}</option>
                            </select>
                        </div>

                        <div>
                            <select id="sub-category" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" v-model="filter.juklakCategory" @change="findJuklak()">
                                <option value="" class="text-gray-400">-- Select Sub Category --</option>
                                <option v-for="jc in filter.dataJuklakCategory.data" :key="jc.id" :value="jc.id">{{ jc.name }}</option>
                            </select>
                        </div>

                        <div>
                            <select id="case" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" v-model="filter.juklak">
                                <option value="" class="text-gray-400">-- Select Case --</option>
                                <option v-for="juklak in filter.dataJuklak.data" :key="juklak.id" :value="juklak.id">{{ juklak.name }}</option>
                            </select>
                        </div>

                        <div>
                            <jet-button class="mt-1 text-base" @click.enter="filterCase(filter)">Cari</jet-button>
                        </div>
                    </div>
                </div>

                <!-- datatables -->
                <div class="bg-white overflow-auto shadow-xl sm:rounded-lg">
                    <data-table :value="dataCase.data" :paginator="true" :rows="50" ref="dt">
                        <template #header>
                            <div style="text-align: left">
                                <jet-button icon="pi pi-external-link" label="Export" @click="downloadAsExcel()">Export Excel</jet-button>
                            </div>
                        </template>

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
                        <column class="whitespace-nowrap">
                            <template #header>
                                <div class="flex justify-center">Status</div>
                            </template>

                            <template #body="slotProps">
                                <div class="flex justify-center">
                                    <pills :theme="slotProps.data.case_status.id">{{ slotProps.data.case_status.name }}</pills>
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

        <jet-dialog-modal :show="openViewModal" @close="openViewModal = false">
            <template #title>
                Case Detail
            </template>

            <template #content>
                <div class="flex justify-center" v-if="!loadCompleted">
                    <progress-spinner style="width:50px;height:50px" strokeWidth="4" fill="#EEEEEE" animationDuration="1.5s" />
                </div>

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
    import JetButton from '@/Jetstream/Button'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetInput from '@/Jetstream/Input'
    import JetLabel from '@/Jetstream/Label'
    import Pills from '@/MyComponents/Pills'

    import DataTable from 'primevue/datatable'
    import Column from 'primevue/column'
    import PrimeButton from 'primevue/button'
    import Timeline from 'primevue/timeline'
    import Card from 'primevue/card'
    import ProgressSpinner from 'primevue/progressspinner'
    import Calendar from 'primevue/calendar'
    import XLSX from 'xlsx'
    import { saveAs } from 'file-saver'

    export default {
        props: ['juklak_category', 'juklak', 'status', 'dateStart', 'dateEnd'],

        data() {
            return {
                loadCompleted: false,
                dataCase: [],
                openViewModal: false,
                dataCaseDetails: [],
                filter: {
                    dateStart: '',
                    dateEnd: '',
                    reference: '',
                    caseStatus: '',
                    client: '',
                    caseCategory: '',
                    juklakCategory: '',
                    juklak: '',
                    dataCaseStatus: [],
                    dataClient: [],
                    dataCaseCategory: [],
                    dataJuklakCategory: [],
                    dataJuklak: [],
                    errors: {}
                }
            }
        },

        components: {
            AppLayout,
            JetDialogModal,
            JetButton,
            JetSecondaryButton,
            JetInput,
            JetLabel,
            Pills,
            DataTable,
            Column,
            PrimeButton,
            Timeline,
            Card,
            ProgressSpinner,
            Calendar
        },

        methods: {
            view(id) {
                this.loadCompleted = false;
                this.openViewModal = true;
                this.dataCaseDetails = [];

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.get('http://localhost:8000/api/data/case-details/'+ id +'/show-by-id-case')
                            .then(result => {
                                this.dataCaseDetails = result.data
                                this.loadCompleted = true
                            })
                            .catch(err => console.log(err))
                    }).catch(err => console.log(err))

            },

            show(id) {
                this.$inertia.get('http://localhost:8000/case/'+id)
            },

            findJuklak() {
                this.filter.juklak = ''
                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.get('http://localhost:8000/api/data/juklak/search-by-category/' + this.filter.juklakCategory)
                            .then(result => this.filter.dataJuklak = result.data)
                            .catch(err => console.log(err))
                    }).catch(err => console.log(err))
            },

            filterCase(filter) {
                this.filter.errors = {}
                let data = new FormData;

                data.append('dateStart', filter.dateStart);
                data.append('dateEnd', filter.dateEnd);
                data.append('reference', filter.reference);
                data.append('caseStatus', filter.caseStatus);
                data.append('client', filter.client);
                data.append('caseCategory', filter.caseCategory);
                data.append('juklakCategory', filter.juklakCategory);
                data.append('juklak', filter.juklak);

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.post('http://localhost:8000/api/data/case/filter', data)
                            .then(result => this.dataCase = result.data)
                            .catch(err => this.filter.errors = err.response.data.errors)
                    }).catch(err => console.log(err))
            },

            exportCSV() {
                this.$refs.dt.exportCSV();
            },

            downloadAsExcel() {
                let data = [];

                for (let index = 0; index < this.dataCase.data.length; index++) {
                    const element = {
                        "No": index+1,
                        "Reference": this.dataCase.data[index].reference,
                        "ID Member": this.dataCase.data[index].member.id_member,
                        "CS": this.dataCase.data[index].user.name,
                        "Case": this.dataCase.data[index].juklak.name,
                        "Kategori": this.dataCase.data[index].category.name,
                        "Status": this.dataCase.data[index].case_status.name
                    };

                    data.push(element);
                }

                let worksheet = XLSX.utils.json_to_sheet(data);

                let workbook = {
                    Sheets: { 'data': worksheet},
                    SheetNames: ['data']
                }

                let excelBuffer = XLSX.write(workbook, {bookType: 'xlsx', type: 'array'});
                this.saveAsExcel(excelBuffer, 'oss_report');
            },

            saveAsExcel(buffer, filename) {
                const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
                const EXCEL_EXTENSION = '.xlsx';

                let data = new Blob([buffer], {type: EXCEL_TYPE});
                saveAs(data, filename+EXCEL_EXTENSION);
            },

            gettingFromStatistik() {
                this.filter.juklakCategory = this.juklak_category != null ? this.juklak_category : '';
                this.filter.juklak = this.juklak != null ? this.juklak : '';
                this.filter.caseStatus = this.status != null ? this.status : '';
                this.filter.dateStart = this.dateStart != null ? new Date(this.dateStart) : '';
                this.filter.dateEnd = this.dateEnd != null ? new Date(this.dateEnd) : '';

                this.filterCase(this.filter);
            }
        },

        created() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    if (this.juklak_category) {
                        this.gettingFromStatistik();
                    } else {
                        axios.get('http://localhost:8000/api/data/case')
                            .then(result => this.dataCase = result.data)
                            .catch(err => console.log(err))
                    }

                    axios.get('http://localhost:8000/api/data/case-status')
                        .then(result => this.filter.dataCaseStatus = result.data)
                        .catch(err => console.log(err))

                    axios.get('http://localhost:8000/api/client')
                        .then(result => this.filter.dataClient = result.data)
                        .catch(err => console.log(err))

                    axios.get('http://localhost:8000/api/case-category')
                        .then(result => this.filter.dataCaseCategory = result.data)
                        .catch(err => console.log(err))

                    axios.get('http://localhost:8000/api/juklak-category')
                        .then(result => this.filter.dataJuklakCategory = result.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err));
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