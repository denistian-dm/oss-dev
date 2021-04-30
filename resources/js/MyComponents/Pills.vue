<template>
    <span 
        class="text-xs font-semibold rounded-2xl py-1 px-4"
        :class="[themeClasses]">
        <slot />
    </span>
</template>

<script>
    export default {
        props: {
            theme: {
                default: 'indigo'
            }
        },

        computed: {
            themeClasses() {
                if (this.theme === 'indigo' || this.theme === 4) {
                    return 'bg-indigo-100 text-indigo-600'
                } else if (this.theme === 'red' || this.theme === 3) {
                    return 'bg-red-100 text-red-600'
                } else if (this.theme === 'green' || this.theme === 2) {
                    return 'bg-green-100 text-green-600'
                } else if (this.theme === 'yellow' || this.theme === 1) {
                    return 'bg-yellow-100 text-yellow-600'
                } else {
                    return 'border border-gray-300 text-gray-600'
                }
            }
        }
    }
</script>