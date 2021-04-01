<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <jet-button class="mb-4" @click.enter="createUser">Tambah User Baru</jet-button>
                <jet-dialog-modal :show="creatingUser" @close="creatingUser = false">
                    <template #title>
                        Tambah User Baru
                    </template>

                    <template #content>
                        <div>
                            <div>
                                <jet-label for="name" value="Name" />
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                                <div class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 mt-8">
                                <div class="">
                                    <jet-label for="email" value="Email" />
                                    <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                                    <div class="text-red-500" v-if="errors.email">{{ errors.email[0] }}</div>
                                </div>

                                <div class="">
                                    <jet-label for="username" value="Username" />
                                    <jet-input id="username" type="text" class="mt-1 block w-full" v-model="form.username" required />
                                    <div class="text-red-500" v-if="errors.username">{{ errors.username[0] }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 mt-8">
                                <div class="">
                                    <jet-label for="division" value="Division" />
                                    <select id="division" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" v-model="form.division">
                                        <option value="" class="text-gray-400">-- Select Division --</option>
                                        <option v-for="div in dataDivision.data" :key="div.id" :value="div.id">{{ div.name }}</option>
                                    </select>
                                    <div class="text-red-500" v-if="errors.division">{{ errors.division[0] }}</div>
                                </div>

                                <div class="">
                                    <jet-label for="level" value="Level" />
                                    <select id="level" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" v-model="form.level">
                                        <option value="" class="text-gray-400">-- Select Level --</option>
                                        <option v-for="level in dataLevel.data" :key="level.id" :value="level.id">{{ level.name }}</option>
                                    </select>
                                    <div class="text-red-500" v-if="errors.level">{{ errors.level[0] }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8 mt-8" v-show="createNewUser">
                                <div class="">
                                    <jet-label for="password" value="Password" />
                                    <jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
                                    <div class="text-red-500" v-if="errors.password">{{ errors.password[0] }}</div>
                                </div>

                                <div class="">
                                    <jet-label for="password_confirmation" value="Confirm Password" />
                                    <jet-input id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
                                    <div class="text-red-500" v-if="errors.password_confirmation">{{ errors.password_confirmation[0] }}</div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template #footer>
                        <jet-secondary-button @click.enter="closeModal">
                            Tutup
                        </jet-secondary-button>
                        <jet-button class="ml-4" :class="{ 'opacity-25': isSubmit }" :disabled="isSubmit" @click="submit(form)" v-show="createNewUser">
                            Register
                        </jet-button>
                        <jet-button class="ml-4" :class="{ 'opacity-25': isSubmit }" :disabled="isSubmit" @click="update(form)" v-show="!createNewUser">
                            Save
                        </jet-button>
                    </template>
                </jet-dialog-modal>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <table class="overflow-x-auto w-full bg-white">
                        <thead class="bg-blue-100 border-b border-gray-300">
                            <tr>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">No</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Email</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Status</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Username</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Nama</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Level</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Section</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500 text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody class="text-gray-600 text-sm divide-y divide-gray-300">
                            <tr 
                                v-for="(user, index) in dataUsers.data"
                                :key="user.id"
                                class="bg-white font-medium text-sm divide-y divide-gray-200">
                                    <td class="p-4 whitespace-nowrap">{{ index+1 }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ user.email }}</td>
                                    <td class="p-4 whitespace-nowrap">
                                        <my-pills v-if="user.status==='AKTIF'" theme="indigo">{{ user.status }}</my-pills>
                                        <my-pills v-else theme="red">{{ user.status }}</my-pills>
                                    </td>
                                    <td class="p-4 whitespace-nowrap">{{ user.username }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ user.name }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ user.level.name }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ user.division.name }}</td>
                                    <td class="p-4 whitespace-nowrap text-center">
                                        <div class="flex item-center justify-center">
                                            <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 cursor-pointer" 
                                                v-tooltip.bottom="'edit'" 
                                                @click.enter="updateUser(user)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </div>
                                            <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 cursor-pointer" 
                                                v-tooltip.bottom="'delete'"
                                                @click.enter="destroy(user)">
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
                dataUsers: [],
                creatingUser: false,
                createNewUser: true,
                form: {
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    username: '',
                    division: '',
                    level: '',
                    terms: false,
                },
                dataDivision: [],
                dataLevel: [],
                isSubmit: false,
                errors: {},
            }
        },

        methods: {
            createUser() {
                this.creatingUser = true;
                this.createNewUser = true;
            },

            updateUser(user){
                this.form.name = user.name;
                this.form.email = user.email;
                this.form.username = user.username;
                this.form.division = user.division.id;
                this.form.level = user.level.id;
                this.form.id = user.id;
                
                this.createNewUser = false;
                this.creatingUser = true;
            },

            submit(form) {
                this.isSubmit = true;

                let data = new FormData;
                data.append('name', form.name);
                data.append('email', form.email);
                data.append('password', form.password);
                data.append('password_confirmation', form.password_confirmation);
                data.append('username', form.username);
                data.append('division', form.division);
                data.append('level', form.level);
                data.append('terms', form.terms);

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.post('http://localhost:8000/api/data/users', data)
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
                        axios.get('http://localhost:8000/api/data/users')
                            .then(result => this.dataUsers = result.data)
                            .catch(err => console.log(err.response))
                }).catch(err => console.log(err.response))
            },

            update(form) {
                this.isSubmit = true;

                let data = new FormData;
                data.append('_method', 'put');
                data.append('name', form.name);
                data.append('email', form.email);
                data.append('username', form.username);
                data.append('division', form.division);
                data.append('level', form.level);
                data.append('terms', form.terms);

                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        axios.post('http://localhost:8000/api/data/users/' + form.id, data)
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

            destroy(user) {
                this.$swal({
                    title: 'Apakah Yakin?',
                    text: 'Menghapus data user '+user.name,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus'
                }).then( result => {
                    if (result.isConfirmed) {
                        axios.get('http://localhost:8000/sanctum/csrf-cookie')
                            .then(response => {
                                axios.delete('http://localhost:8000/api/data/users/' + user.id)
                                    .then(result => {
                                        console.log(result);
                                        this.$swal(
                                            'Deleted!',
                                            'Data User Berhasil Dihapus',
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
                this.creatingUser = false;
                this.isSubmit = false;

                this.form.name = '';
                this.form.email = '';
                this.form.password = '';
                this.form.password_confirmation = '';
                this.form.username = '';
                this.form.division = '';
                this.form.level = '';
                this.form.terms = false;
            }
        },

        created() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/users')
                        .then(result => {
                            if (result.data.success) {
                                this.dataUsers = result.data
                            } else {
                                this.$swal({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Unauthorized User!'
                                });

                                this.$inertia.visit('http://localhost:8000/dashboard', {method: 'get'})
                            }
                        })
                        .catch(err => console.log(err.response))

                    axios.get('http://localhost:8000/api/division')
                        .then(result => this.dataDivision = result.data)
                        .catch( err => console.log(err))

                    axios.get('http://localhost:8000/api/level')
                        .then(result => this.dataLevel = result.data)
                        .catch( err => console.log(err))
                }).catch(err => console.log(err.response))
        }
    }
</script>