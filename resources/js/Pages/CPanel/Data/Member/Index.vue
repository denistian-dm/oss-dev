<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Members
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <jet-button class="mb-4" @click.enter="createMember">Tambah Member Baru</jet-button>
                <jet-dialog-modal :show="creatingMember" @close="creatingMember = false">
                    <template #title>
                        Tambah Member Baru
                    </template>

                    <template #content>
                        <div>
                            <div class="grid grid-cols-2 gap-8 mt-8">
                                <div class="">
                                    <jet-label for="name" value="Name" />
                                    <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                                    <div class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</div>
                                </div>

                                <div class="">
                                    <jet-label for="id_member" value="ID Member" />
                                    <jet-input id="id_member" type="text" class="mt-1 block w-full" v-model="form.id_member" required />
                                    <div class="text-red-500" v-if="errors.id_member">{{ errors.id_member[0] }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 mt-8">
                                <div class="">
                                    <jet-label for="phone1" value="No. Telp" />
                                    <jet-input id="phone1" type="text" class="mt-1 block w-full" v-model="form.phone1" maxlength="15" />
                                    <div class="text-red-500" v-if="errors.phone1">{{ errors.phone1[0] }}</div>
                                </div>

                                <div class="">
                                    <jet-label for="phone2" value="No. Telp 2" />
                                    <jet-input id="phone2" type="text" class="mt-1 block w-full" v-model="form.phone2" maxlength="15" />
                                    <div class="text-red-500" v-if="errors.phone2">{{ errors.phone2[0] }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 mt-8">
                                <div class="">
                                    <jet-label for="email" value="Email" />
                                    <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                                    <div class="text-red-500" v-if="errors.email">{{ errors.email[0] }}</div>
                                </div>

                                <div class="">
                                    <jet-label for="client_id" value="Client" />
                                    <select id="client_id" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" v-model="form.client_id">
                                        <option value="" class="text-gray-400">-- Select Client --</option>
                                        <option v-for="client in dataClients.data" :key="client.id" :value="client.id">{{ client.name }}</option>
                                    </select>
                                    <div class="text-red-500" v-if="errors.client_id">{{ errors.client_id[0] }}</div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template #footer>
                        <jet-secondary-button @click.enter="closeModal">
                            Tutup
                        </jet-secondary-button>
                        <jet-button class="ml-4" :class="{ 'opacity-25': isSubmit }" :disabled="isSubmit" @click="submit(form)" v-show="createNewMember">
                            Register
                        </jet-button>
                        <jet-button class="ml-4" :class="{ 'opacity-25': isSubmit }" :disabled="isSubmit" @click="update(form)" v-show="!createNewMember">
                            Save
                        </jet-button>
                    </template>
                </jet-dialog-modal>

                <div class="bg-white overflow-auto shadow-xl sm:rounded-lg">
                    <table class="overflow-x-auto w-full bg-white">
                        <thead class="bg-blue-100 border-b border-gray-300">
                            <tr>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">No</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Client</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">ID Member</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Nama</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Email</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Phone 1</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Phone 2</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm divide-y divide-gray-300">
                            <tr 
                                v-for="(member, index) in dataMembers.data"
                                :key="member.id"
                                class="bg-white font-medium text-sm divide-y divide-gray-200">
                                    <td class="p-4 whitespace-nowrap">{{ index+1 }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ member.client.name }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ member.id_member }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ member.name }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ member.email }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ member.phone1 }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ member.phone2 }}</td>
                                    <td class="p-4 whitespace-nowrap text-center">
                                        <div class="flex item-center justify-center">
                                            <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 cursor-pointer" 
                                                v-tooltip.bottom="'edit'" 
                                                @click.enter="updateMember(member)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </div>
                                            <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 cursor-pointer" 
                                                v-tooltip.bottom="'delete'"
                                                @click.enter="destroy(member)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </div>
                                        </div>
                                    </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'

    import JetButton from '@/Jetstream/Button'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetLabel from '@/Jetstream/Label'
    import JetInput from '@/Jetstream/Input'

    import MyPills from '@/MyComponents/Pills'
    
    import axios from 'axios'

    export default {
        components: {
            AppLayout,
            MyPills,
            JetButton,
            JetDialogModal,
            JetSecondaryButton,
            JetLabel,
            JetInput,
        },

        data() {
            return {
                creatingMember: false,
                createNewMember: true,
                isSubmit: false,
                errors: {},
                form: {
                    id: '',
                    id_member: '',
                    name: '',
                    email: '',
                    phone1: '',
                    phone2: '',
                    client_id: '',
                },
                dataMembers: [],
                dataClients: []
            }
        },

        methods: {
            createMember() {
                this.creatingMember = true
                this.createNewMember = true
            },

            updateMember(member){
                this.form.id = member.id;
                this.form.id_member = member.id_member;
                this.form.name = member.name;
                this.form.email = member.email;
                this.form.phone1 = member.phone1;
                this.form.phone2 = member.phone2;
                this.form.client_id = member.client_id;

                this.creatingMember = true;
                this.createNewMember = false;
            },

            submit(form) {
                this.isSubmit = true;

                let data = new FormData;
                data.append('id_member', form.id_member);
                data.append('name', form.name);
                data.append('email', form.email);
                data.append('phone1', form.phone1);
                data.append('phone2', form.phone2);
                data.append('client_id', form.client_id);

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.post('http://localhost:8000/api/data/members', data)
                            .then(result => {
                                console.log(result);
                                this.getData();
                                this.closeModal();
                            })
                            .catch(err => {
                                console.log(err);
                                this.errors = err.response.data.errors;
                            })
                    }).catch(err => console.log(err));
            },

            getData() {
                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.get('http://localhost:8000/api/data/members')
                            .then(result => this.dataMembers = result.data)
                            .catch(err => console.log(err))
                    }).catch(err => console.log(err))
            },

            update(form) {
                this.isSubmit = true;

                let data = new FormData;
                data.append('_method', 'put');
                data.append('id_member', form.id_member);
                data.append('name', form.name);
                data.append('email', form.email);
                data.append('phone1', form.phone1);
                data.append('phone2', form.phone2);
                data.append('client_id', form.client_id);

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.post('http://localhost:8000/api/data/members/' + form.id, data)
                            .then(result => {
                                console.log(result);
                                this.getData();
                                this.closeModal();
                            })
                            .catch(err => {
                                console.log(err);
                                this.errors = err.response.data.errors;
                            })
                    }).catch(err => console.log(err));
            },

            destroy(member) {
                this.$swal({
                    title: 'Apakah Yakin?',
                    text: 'Menghapus data Member '+member.name,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus'
                }).then( result => {
                    if (result.isConfirmed) {
                        axios.get('http://localhost:8000/sanctum/csrf-cookie')
                            .then(response => {
                                axios.delete('http://localhost:8000/api/data/members/' + member.id)
                                    .then(result => {
                                        console.log(result);
                                        this.$swal(
                                            'Deleted!',
                                            'Data Member Berhasil Dihapus',
                                            'success'
                                        );
                                        this.getData();
                                    })
                                    .catch(err => console.log(err))
                            })
                            .catch(err => console.log(err))
                    }
                })
            },

            closeModal() {
                this.creatingMember = false;
                this.isSubmit = false;

                this.form.id = '';
                this.form.id_member = '';
                this.form.name = '';
                this.form.email = '';
                this.form.phone1 = '';
                this.form.phone2 = '';
                this.form.client_id = '';
                this.errors = {};
            }
        },

        created() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/members')
                        .then(result => this.dataMembers = result.data)
                        .catch(err => console.log(err))

                    axios.get('http://localhost:8000/api/client')
                        .then(result => this.dataClients = result.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err))
        }
    }
</script>