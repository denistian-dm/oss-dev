<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Data Juklak
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <jet-button class="mb-4" @click.enter="createJuklak">Tambah Juklak Baru</jet-button>

                <div class="bg-white overflow-auto shadow-xl sm:rounded-lg">
                    <table class="overflow-x-auto w-full bg-white">
                        <thead class="bg-blue-100 border-b border-gray-300">
                            <tr>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">No</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Kategori</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500">Nama</th>
                                <th class="p-4 text-left text-sm font-medium text-gray-500 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm divide-y divide-gray-300">
                            <tr 
                                v-for="(juklak, index) in dataJuklak.data"
                                :key="juklak.id"
                                class="bg-white font-medium text-sm divide-y divide-gray-200">
                                    <td class="p-4 whitespace-nowrap">{{ index+1 }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ juklak.juklak_category.name }}</td>
                                    <td class="p-4 whitespace-nowrap">{{ juklak.name }}</td>
                                    <td class="p-4 whitespace-nowrap text-center">
                                        <div class="flex item-center justify-center">
                                            <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 cursor-pointer"
                                                v-tooltip.bottom="'view'"
                                                @click.enter="view(juklak.id)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </div>

                                            <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 cursor-pointer" 
                                                v-tooltip.bottom="'edit'" 
                                                @click.enter="update(juklak)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </div>

                                            <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 cursor-pointer" 
                                                v-tooltip.bottom="'delete'"
                                                @click.enter="destroy(juklak)">
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
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetButton from '@/Jetstream/Button'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetDialogModal from '@/Jetstream/DialogModal'

export default {
    components: {
        AppLayout,
        JetButton,
        JetSecondaryButton,
        JetDialogModal
    },

    data() {
        return {
            dataJuklak: [],
            openModal: false,
            juklak: {
                name: '',
                contoh: '',
                jawaban: '',
                follow_up: '',
                indikator: '',
            },
        }
    },

    methods: {
        createJuklak() {
            this.$inertia.get('http://localhost:8000/data/juklak/create')
        },

        update(juklak) {
            this.$inertia.get('http://localhost:8000/data/juklak/' + juklak.id + '/edit')
        },

        destroy(juklak) {
            this.$swal({
                    title: 'Apakah Yakin?',
                    text: 'Menghapus Juklak '+juklak.name,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus'
                }).then( result => {
                    if (result.isConfirmed) {
                        axios.get('http://localhost:8000/sanctum/csrf-cookie')
                            .then(response => {
                                axios.delete('http://localhost:8000/api/data/juklak/' + juklak.id)
                                    .then(result => {
                                        console.log(result);
                                        this.$swal(
                                            'Deleted!',
                                            'Data Juklak Berhasil Dihapus',
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

        view(id) {
            this.openModal = true;
            this.clearData();
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/juklak/'+id)
                        .then(result => this.juklak = result.data.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err))
        },

        getData() {
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/juklak')
                        .then(result => this.dataJuklak = result.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err))
        },

        clearData() {
            this.juklak.name = '';
            this.juklak.contoh = '';
            this.juklak.jawaban = '';
            this.juklak.follow_up = '';
            this.juklak.indikator = '';
        }
    },

    created() {
        axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.get('http://localhost:8000/api/data/juklak')
                        .then(result => this.dataJuklak = result.data)
                        .catch(err => console.log(err))
                }).catch(err => console.log(err))
    }
}
</script>