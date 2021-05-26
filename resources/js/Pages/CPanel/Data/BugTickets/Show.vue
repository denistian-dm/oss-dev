<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Case: {{ data.data.bug_ticket.juklak.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="col-start-1">
                        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg">
                            <div class="px-8 py-4 bg-indigo-500 text-gray-100">
                                Reference Case
                            </div>
                            <div class="px-8 py-4">
                                <ul>
                                    <li 
                                        class="underline text-indigo-500 cursor-pointer hover:text-indigo-900"
                                        @click.enter="redirect(data.id)"
                                        v-for="data in data.data.bug_ticket.cases" 
                                        :key="data.id">
                                        {{ data.reference }}
                                    </li>
                                </ul>
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

                        <div v-else>
                            <div v-if="data.data.details.length > 0">
                                <div class="bg-white overflow-auto shadow sm:rounded-lg mb-6" v-for="data in data.data.details" :key="data.id">
                                    <div class="relative w-full">
                                        <div class="flex items-center px-6 pt-6">
                                            <img :src="data.user.profile_photo_url" alt="" class="w-8 h-8 rounded-full object-cover">
                                            <div class="flex-1 ml-4">
                                                <span class="font-bold">{{ data.user.name }}</span>
                                                <span class="text-xs text-gray-500"> on {{ data.created_at }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-12 px-6 pb-6" v-html="data.comment"></div>
                                </div>
                            </div>

                            <div v-else>
                                <div class="p-6 bg-white overflow-auto shadow sm:rounded-lg mb-6">
                                    Belum ada komentar
                                </div>
                            </div>
                        </div>

                        <jet-button @click.enter="createRespon" v-if="data.data.bug_ticket.bug_ticket_status_id != 2" >RESPON</jet-button>
                    </div>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="openModal" @close="openModal = false">
            <template #title>
                Comment
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
                            <option v-for="status in status.data" :key="status.id" :value="status.id">{{ status.name }}</option>
                        </select>
                        <div class="text-red-500"></div>
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
    import Editor from 'primevue/editor'
    import Skeleton from 'primevue/skeleton'

    export default {
        props: ['id'],

        components: {
            AppLayout,
            JetButton,
            JetSecondaryButton,
            JetDialogModal,
            JetLabel,
            Editor,
            Skeleton
        },

        data() {
            return {
                openModal: false,
                isSubmit: false,
                loadCompleted: false,
                status: [],
                form: {
                    status: '',
                    deskripsi: '',
                },
                data: {
                    data: {
                        bug_ticket: {
                            juklak: {
                                name: null
                            }
                        },
                        details: []
                    }
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

            redirect(id) {
                this.$inertia.get('http://localhost:8000/case/'+id)
            },

            submit(form) {
                this.isSubmit = true;

                let data = new FormData;
                data.append('status', form.status);
                data.append('comment', form.deskripsi);
                data.append('bug_ticket_id', this.id);

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.post('http://localhost:8000/api/data/bug-ticket-details', data)
                            .then(result => {
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
                        axios.get('http://localhost:8000/api/data/case-resolution/'+this.id)
                            .then(result => this.data = result.data)
                            .catch(err => console.log(err))
                    }).catch(err => console.log(err))
            },

            reset() {
                this.isSubmit = false;
                this.openModal = false;
                this.form.status = '';
                this.form.deskripsi = '';
            }
        },

        created() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/case-resolution/'+this.id)
                        .then(result => {
                            this.data = result.data;
                            this.loadCompleted = true;
                        }).catch(err => console.log(err))

                    axios.get('http://localhost:8000/api/data/case-resolution-status/')
                        .then(result => this.status = result.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err))
        }
    }
</script>