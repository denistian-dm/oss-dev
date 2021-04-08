<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Juklak Baru
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl overflow-hidden mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-200 shadow-xl sm:rounded-lg">
                    <div class="mb-8 text-2xl">
                        Form Tambah Data Juklak
                    </div>

                    <div>
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div class="col-start-1">Kategori</div>
                            <div class="col-start-1 md:col-start-2 lg:col-end-3">
                                <select id="juklak_category_id" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1" v-model="form.juklak_category_id">
                                    <option value="">-- Pilih --</option>
                                    <option v-for="jc in juklakCategory.data" :key="jc.id" :value="jc.id">{{ jc.name }}</option>
                                </select>
                                <div class="text-red-500" v-if="errors.juklak_category_id">{{ errors.juklak_category_id[0] }}</div>
                            </div>
                            <div class="col-start-1">Name</div>
                            <div class="col-start-1 md:col-start-2 md:col-span-2">
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" />
                                <div class="text-red-500" v-if="errors.name">{{ errors.name[0] }}</div>
                            </div>
                            <div class="col-start-1">Contoh</div>
                            <div class="col-start-1 md:col-start-2 md:col-span-2">
                                <ckeditor :editor="editor" v-model="form.contoh" />
                                <div class="text-red-500" v-if="errors.contoh">{{ errors.contoh[0] }}</div>
                            </div>
                            <div class="col-start-1">Jawaban ke Client</div>
                            <div class="col-start-1 md:col-start-2 md:col-span-2">
                                <ckeditor :editor="editor" v-model="form.jawaban" />
                                <div class="text-red-500" v-if="errors.jawaban">{{ errors.jawaban[0] }}</div>
                            </div>
                            <div class="col-start-1">Langkah Pengerjaan</div>
                            <div class="col-start-1 md:col-start-2 md:col-span-2">
                                <ckeditor :editor="editor" v-model="form.follow_up" />
                                <div class="text-red-500" v-if="errors.follow_up">{{ errors.follow_up[0] }}</div>
                            </div>
                            <div class="col-start-1">Indikator Penyelesaian Case</div>
                            <div class="col-start-1 md:col-start-2 md:col-span-2">
                                <ckeditor :editor="editor" v-model="form.indikator" />
                                <div class="text-red-500" v-if="errors.indikator">{{ errors.indikator[0] }}</div>
                            </div>
                            <div class="col-start-1">
                                <jet-button 
                                    class="mb-4" 
                                    :class="{ 'opacity-25': isSubmit }" 
                                    :disabled="isSubmit" 
                                    @click.enter="store(form)"
                                    v-show="!editMode">
                                    Simpan
                                </jet-button>
                                <jet-button 
                                    class="mb-4" 
                                    :class="{ 'opacity-25': isSubmit }" 
                                    :disabled="isSubmit" 
                                    @click.enter="update(form)"
                                    v-show="editMode">
                                    Update
                                </jet-button>
                                <jet-secondary-button class="ml-2" @click.enter="cancel">
                                    Batal
                                </jet-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetInput from '@/Jetstream/Input'
import JetButton from '@/Jetstream/Button'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

export default {
    props: ['id'],

    components: {
        AppLayout,
        JetInput,
        JetButton,
        JetSecondaryButton,
    },

    data() {
        return {
            juklakCategory: [],
            editor: ClassicEditor,
            form: {
                juklak_category_id: '',
                name: '',
                contoh: '',
                jawaban: '',
                follow_up: '',
                indikator: '',
                id: '',
            },
            errors: {},
            isSubmit: false,
            editMode: false,
        }
    },

    methods: {
        store(form) {
            this.isSubmit = true;
            let data = new FormData;

            data.append('name', form.name);
            data.append('contoh', form.contoh);
            data.append('jawaban', form.jawaban);
            data.append('follow_up', form.follow_up);
            data.append('indikator', form.indikator);
            data.append('juklak_category_id', form.juklak_category_id);

            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.post('http://localhost:8000/api/data/juklak', data)
                        .then(result => {
                            if (result.data.success) {
                                this.$inertia.get('http://localhost:8000/data/juklak')
                            }
                        })
                        .catch(err => {
                            this.errors = err.response.data.errors
                            this.isSubmit = false
                        })
                })
                .catch(err => console.log(err));
        },

        update(form) {
            this.isSubmit = true;
            let data = new FormData;

            data.append('_method', 'put');
            data.append('name', form.name);
            data.append('contoh', form.contoh);
            data.append('jawaban', form.jawaban);
            data.append('follow_up', form.follow_up);
            data.append('indikator', form.indikator);
            data.append('juklak_category_id', form.juklak_category_id);

            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    axios.post('http://localhost:8000/api/data/juklak/'+form.id, data)
                        .then(result => {
                            if (result.data.success) {
                                this.$inertia.get('http://localhost:8000/data/juklak')
                            }
                        })
                        .catch(err => {
                            this.errors = err.response.data.errors
                            this.isSubmit = false
                        })
                })
                .catch(err => console.log(err));
        },

        cancel() {
            this.$inertia.get('http://localhost:8000/data/juklak')
        }
    },

    created() {
        axios.get('http://localhost:8000/sanctum/csrf-cookie')
            .then(response => {
                axios.get('http://localhost:8000/api/juklak-category')
                    .then(result => this.juklakCategory = result.data)
                    .catch(err => console.log(err))

                if (this.id) {
                    this.editMode = true;
                    axios.get('http://localhost:8000/api/data/juklak/'+this.id+'/edit')
                        .then(result => this.form = result.data.data)
                        .catch(err => console.log(err))
                }
            }).catch(err => console.log(err))
    }
}
</script>

<style>
.ck-editor__editable_inline {
    min-height: 200px;
}
</style>