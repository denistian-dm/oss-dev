<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Buat Case Baru
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-auto shadow sm:rounded-lg">
                    <div class="p-8" id="container">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="">
                                <jet-label for="id_member" value="ID Member" />
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <auto-complete 
                                        id="id_member" 
                                        class="w-full"
                                        :input-class="'focus:ring-indigo-200 focus:border-indigo-300 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300'" 
                                        v-model="member.selectedMember" 
                                        :suggestions="member.filteredMember" 
                                        @complete="searchMember($event)" 
                                        field="suggestion" />
                                    <span 
                                        @click.enter="creatingMember = true"
                                        class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-indigo-500 hover:text-indigo-700 text-sm cursor-pointer">
                                        Tambah
                                    </span>
                                </div>
                                <div class="text-red-500"></div>
                            </div>

                            <div class="">
                                <jet-label for="radio" value="Kategori" />
                                <div class="flex flex-wrap mt-0 md:mt-3">
                                    <div class="flex items-center mr-4" v-for="cc in caseCategories.data" :key="cc.id">
                                        <input name="radio" :id="'radio-'+cc.id" type="radio" class="hidden" :value="cc.id" v-model="newCase.category_id" />
                                        <label :for="'radio-'+cc.id" class="flex items-center cursor-pointer">
                                            <span class="w-4 h-4 inline-block mr-1 rounded-full border border-grey"></span>
                                            {{ cc.name }}
                                        </label>
                                    </div>
                                </div>
                                <div class="text-red-500"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                            <div class="">
                                <jet-label for="sub-kategori" value="Sub Kategori" />
                                <select id="sub-kategori" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" @change="findJuklak()" v-model="subCategory">
                                    <option value="" class="text-gray-400">-- Select Sub Kategori --</option>
                                    <option v-for="category in categories.data" :key="category.id" :value="category.id">{{ category.name }}</option>
                                </select>
                                <div class="text-red-500"></div>
                            </div>

                            <div class="">
                                <jet-label for="case" value="Case" />
                                <select id="case" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" @change="showJuklak()" v-model="juklakId">
                                    <option value="" class="text-gray-400">-- Select Case --</option>
                                    <option v-for="juklak in dataJuklak.data" :key="juklak.id" :value="juklak.id">{{ juklak.name }}</option>
                                </select>
                                <div class="text-red-500"></div>
                            </div>

                            <div class="flex flex-wrap content-end">
                                <div class="mt-1 flex rounded-md shadow-sm w-full" 
                                    :class="isCaseSelected ? 'cursor-pointer' : 'cursor-not-allowed' " 
                                    @click.enter="isCaseSelected ? openModal = true : openModal = false">
                                    <div 
                                        class="px-4 py-2 flex-1 block w-full rounded-none rounded-l-md border border-gray-300 text-gray-500"
                                        :class="isCaseSelected ? 'bg-white' : 'bg-gray-50' ">Lihat Detail</div>
                                    <span 
                                        class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 hover:text-gray-700 text-sm"
                                        :class="isCaseSelected ? 'bg-gray-800 text-white' : 'bg-gray-50 text-gray-500' ">
                                        <div class="w-4 transform hover:scale-110">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="desktop" class="svg-inline--fa fa-desktop fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M528 0H48C21.5 0 0 21.5 0 48v320c0 26.5 21.5 48 48 48h192l-16 48h-72c-13.3 0-24 10.7-24 24s10.7 24 24 24h272c13.3 0 24-10.7 24-24s-10.7-24-24-24h-72l-16-48h192c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48zm-16 352H64V64h448v288z"></path></svg>
                                        </div>
                                    </span>
                                </div>
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
                    </div>
                </div>

                <div class="pt-8">
                    <jet-label value="Deskripsi" class="font-bold" />
                    <ckeditor :editor="editor" v-model="newCase.deskripsi" />
                    <div class="text-red-500"></div>
                </div>

                <div class="pt-4">
                    <button 
                        @click.enter="submit()"
                        class="inline-block align-middle px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 mb-4 w-1/2 md:w-1/5 ">
                        Submit
                    </button>
                </div>
            </div>
        </div>

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
                <jet-button class="ml-4" :class="{ 'opacity-25': isSubmit }" :disabled="isSubmit" @click.enter="createMember(form)" >
                    Save
                </jet-button>
            </template>
        </jet-dialog-modal>

        <jet-dialog-modal :show="openModal" @close="openModal = false">
            <template #title>
                {{ juklak.name }}
            </template>

            <template #content>
                <div>
                    <div>
                        <h5 class="text-xl text-gray-800 mb-2 font-bold">Contoh Case</h5>
                        <p class="text-gray-600" v-html="juklak.contoh"></p>
                    </div>

                    <div class="border-t border-gray-200 my-4"></div>

                    <div>
                        <h5 class="text-xl text-gray-800 mb-2 font-bold">Jawaban ke Client</h5>
                        <p class="text-gray-600" v-html="juklak.jawaban"></p>
                    </div>

                    <div class="border-t border-gray-200 my-4"></div>

                    <div>
                        <h5 class="text-xl text-gray-800 mb-2 font-bold">Follow Up</h5>
                        <p class="text-gray-600" v-html="juklak.follow_up"></p>
                    </div>

                    <div class="border-t border-gray-200 my-4"></div>

                    <div>
                        <h5 class="text-xl text-gray-800 mb-2 font-bold">Indikator Penyelesaian</h5>
                        <p class="text-gray-600" v-html="juklak.indikator"></p>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.enter="openModal = false">
                    Tutup
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

import JetLabel from '@/Jetstream/Label'
import JetInput from '@/Jetstream/Input'
import JetButton from '@/Jetstream/Button'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetDialogModal from '@/Jetstream/DialogModal'

import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
import AutoComplete from 'primevue/autocomplete'

export default {
    components: {
        AppLayout,
        JetLabel,
        JetInput,
        JetButton,
        JetSecondaryButton,
        JetDialogModal,
        AutoComplete
    },

    data() {
        return {
            creatingMember: false,
            openModal: false,
            editor: ClassicEditor,
            isSubmit: false,
            isCaseSelected: false,
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
            newCase: {
                category_id: '',
                deskripsi: '',
                attchment: null,
                errors: {},
            },
            dataClients: [],
            caseCategories: [],
            categories: [],
            subCategory: '',
            dataJuklak: [],
            juklakId: '',
            juklak: {},
            member: {
                members: [],
                selectedMember: null,
                filteredMember: null,
            }
        }
    },

    methods: {
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
        },

        findJuklak() {
            this.juklakId = '';
            this.isCaseSelected = false;
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/juklak/search-by-category/' + this.subCategory)
                        .then(result => this.dataJuklak = result.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err))
        },

        showJuklak() {
            if (this.juklakId !== '') {
                this.isCaseSelected = true;
                let jk = this.dataJuklak.data.filter(juklak => juklak.id == this.juklakId);
                this.juklak = jk[0];
            } else {
                this.juklak = {}
                this.isCaseSelected = false;
            }
        },

        createMember(form) {
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
                            this.member.selectedMember = result.data.data;
                            this.closeModal();
                        })
                        .catch(err => {
                            console.log(err);
                            this.errors = err.response.data.errors;
                        })
                }).catch(err => console.log(err));
        },

        searchMember(event) {
            setTimeout(() => {
                if (!event.query.trim().length) {
                    this.member.filteredMember = [...this.member.members]
                } else {
                    this.member.filteredMember = this.member.members.filter((mbr) => {
                        return mbr.name.toLowerCase().startsWith(event.query.toLowerCase()) || mbr.id_member.toLowerCase().startsWith(event.query.toLowerCase());
                    });
                }
            }, 250);
        },

        submit() {
            let data = new FormData;

            data.append('ref_code', this.member.selectedMember.client.code);
            data.append('member_id', this.member.selectedMember.id);
            data.append('category_id', this.newCase.category_id);
            data.append('juklak_id', this.juklakId);
            data.append('deskripsi', this.newCase.deskripsi);
            
            for (let i = 0; i < this.$refs.file.files.length; i++) {
                let file = this.$refs.file.files[i];
                data.append('files[' + i + ']', file);
            }

            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.post('http://localhost:8000/api/data/case', data, {headers: {'Content-Type': 'multipart/form-data'}})
                        .then(result => {
                            console.log(result.data)
                            this.$swal(
                                'Created!',
                                'Tiket Case Berhasil Dibuat',
                                'success'
                            );
                            this.$inertia.get('http://localhost:8000/case/') 
                        }).catch(err => console.log(err))
                }).catch(err => console.log(err));
        },

        selectedFile(event) {
            this.newCase.attchment = event.target.files;
        }
    },

    created() {
        axios.get('http://localhost:8000/sanctum/csrf-cookie')
            .then(response => {
                axios.get('http://localhost:8000/api/case-category')
                    .then(result => this.caseCategories = result.data)
                    .catch(err => console.log(err))

                axios.get('http://localhost:8000/api/client')
                    .then(result => this.dataClients = result.data)
                    .catch(err => console.log(err))

                axios.get('http://localhost:8000/api/juklak-category')
                    .then(result => this.categories = result.data)
                    .catch(err => console.log(err))

                axios.get('http://localhost:8000/api/data/members')
                    .then(result => this.member.members = result.data.data)
                    .catch(err => console.log(err))
            }).catch(err => console.log(err));
    },
}
</script>

<style>
    input[type="radio"] + label span {
        transition: background .2s,
        transform .2s;
    }

    input[type="radio"] + label span:hover,
    input[type="radio"] + label:hover span{
    transform: scale(1.2);
    } 

    input[type="radio"]:checked + label span {
    background-color: #3490DC; 
    box-shadow: 0px 0px 0px 2px white inset;
    }

    input[type="radio"]:checked + label{
    color: #3490DC; 
    }

    .ck-editor__editable_inline {
        min-height: 200px;
    }

    .p-inputtext {
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
    }
</style>