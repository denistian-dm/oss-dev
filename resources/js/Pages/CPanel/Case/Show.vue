<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Case Details: {{ dataCase.data.reference }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="col-start-1">
                        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg">
                            <div v-if="dataCaseDetail.has_attachment" class="py-6">
                                <carousel :value="dataCaseDetail.path" :numVisible="1" :numScroll="1">
                                    <template #item="slotProps">
                                        <div>
                                            <img :src="slotProps.data.lokasi" :alt="slotProps.data.name" class="w-full">
                                        </div>
                                        <div class="flex justify-center">
                                            <h4 class="mt-2 text-sm uppercase font-bold">{{ slotProps.data.name }}</h4>
                                        </div>
                                    </template>
                                </carousel>
                            </div>

                            <div class="p-6" v-else>
                                <span>Tidak Ada Lampiran Gambar</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-start-1 md:col-start-2 md:col-span-2">
                        <div class="bg-white overflow-auto shadow sm:rounded-lg mb-6" v-if="!loadCompleted">
                            <div class="relative w-full">
                                <div class="flex items-center px-6 pt-6">
                                    <skeleton shape="circle" width="2rem" height="2rem" />
                                    <div class="flex-1 ml-4">
                                        <skeleton width="12rem" />
                                    </div>
                                </div>
                            </div>

                            <div class="ml-12 px-6 pb-6">
                                <skeleton height="2rem" />
                                <skeleton class="mt-2" height="3rem" />
                            </div>
                        </div>

                        <div class="bg-white overflow-auto shadow sm:rounded-lg mb-6" v-for="data in dataCaseDetail.data" :key="data.id">
                            <div class="relative w-full">
                                <div class="flex items-center px-6 pt-6">
                                    <img :src="data.user.profile_photo_url" alt="" class="w-8 h-8 rounded-full object-cover">
                                    <div class="flex-1 ml-4">
                                        <span class="font-bold">{{ data.user.name }}</span>
                                        <span class="text-xs text-gray-500"> on {{ data.created_at }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-12 px-6 pb-6" v-html="data.deskripsi"></div>
                            <accordion v-if="data.attachment.length>0">
                                <accordion-tab>
                                    <template #header>
                                        <span class="text-sm">Lampiran</span>
                                    </template>
                                    <ul class="list-disc list-inside ml-6">
                                        <li v-for="attch in data.attachment" :key="attch.id">
                                            <a class="text-blue-500 underline" :href="attch.lokasi" target="_blank">{{ attch.name }}</a>
                                        </li>
                                    </ul>
                                </accordion-tab>
                            </accordion>
                        </div>

                        <jet-button @click.enter="createRespon" v-if="dataCase.data.case_status_id != 2" >RESPON</jet-button>
                    </div>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="openModal" @close="openModal = false">
            <template #title>
                Form Update Case
            </template>

            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="">
                        <jet-label for="status" value="Status" />
                        <select 
                            id="status"
                            v-model="form.status" 
                            class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1">
                            <option value="" class="text-gray-400">-- Select Status --</option>
                            <option v-for="status in dataCaseStatus.data" :key="status.id" :value="status.id">{{ status.name }}</option>
                        </select>
                        <div class="text-red-500"></div>
                    </div>

                    <div>
                        <jet-label for="attch" value="Lampiran" />
                        <input 
                            type="file" 
                            id="attch"
                            ref="file"
                            @change="selectedFile"
                            multiple
                            class="w-full py-1 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1">
                    </div>
                </div>
                <div class="mt-4">
                    <jet-label for="deskripsi" value="Deskripsi" />
                    <editor id="deskripsi" v-model="form.deskripsi" editorStyle="height: 320px" />
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.enter="closeModal">
                    Tutup
                </jet-secondary-button>
                <jet-button class="ml-2" :class="{ 'opacity-25' : isSubmit }" :disabled="isSubmit" @click.enter="submit(form)">
                    Update
                </jet-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetButton from '@/Jetstream/Button'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetLabel from '@/Jetstream/Label'
    import Carousel from 'primevue/carousel'
    import Editor from 'primevue/editor'
    import Skeleton from 'primevue/skeleton'
    import Accordion from 'primevue/accordion'
    import AccordionTab from 'primevue/accordiontab'

    export default {
        props: ['id'],

        components: {
            AppLayout,
            JetButton,
            JetSecondaryButton,
            JetDialogModal,
            JetLabel,
            Carousel,
            Editor,
            Skeleton,
            Accordion,
            AccordionTab
        },

        data() {
            return {
                openModal: false,
                isSubmit: false,
                loadCompleted: false,
                dataCaseStatus: [],
                dataCaseDetail: [],
                dataCase: {
                    data: {
                        reference: null
                    }
                },
                form: {
                    status: '',
                    attachment: null,
                    deskripsi: null,
                }
                
            }
        },

        methods: {
            createRespon() {
                this.openModal = true;
            },

            closeModal() {
                this.openModal = false;
            },

            selectedFile(event) {
                this.form.attachment = event.target.files;
            },

            submit(form) {
                this.isSubmit = true;
                let data = new FormData;

                data.append('status', form.status);
                data.append('deskripsi', form.deskripsi);
                data.append('case_id', this.dataCase.data.id);

                for (let i = 0; i < this.$refs.file.files.length; i++) {
                    let file = this.$refs.file.files[i];
                    data.append('files[' + i + ']', file);
                }

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.post('http://localhost:8000/api/data/case-details', data, {headers: {'Content-Type': 'multipart/form-data'}})
                            .then(result => {
                                console.log(result.data)
                                this.$swal(
                                    'Updated!',
                                    'Berhasil Respon Case',
                                    'success'
                                );
                                this.getData();
                                this.reset();
                            })
                            .catch(err => console.log(err))
                    }).catch(err => console.log(err));
            },

            getData() {
                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.get('http://localhost:8000/api/data/case-details/'+this.id+'/show-by-id-case')
                            .then(result => this.dataCaseDetail = result.data)
                            .catch(err => console.log(err))
                    }).catch(err => console.log(err))
            },

            reset() {
                this.isSubmit = false;
                this.openModal = false;
                this.form.status = '';
                this.form.attachment = null;
                this.form.deskripsi = null;
            }
        },

        created() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/case-details/'+this.id+'/show-by-id-case')
                        .then(result => {
                            this.dataCaseDetail = result.data;
                            this.loadCompleted = true;
                        }).catch(err => console.log(err))

                    axios.get('http://localhost:8000/api/data/case/'+this.id)
                        .then(result => this.dataCase = result.data)
                        .catch(err => console.log(err))

                    axios.get('http://localhost:8000/api/data/case-status/')
                        .then(result => this.dataCaseStatus = result.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err))
        }
    }
</script>