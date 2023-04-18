<template>
    <v-snackbar
        location="top"
        :timeout="4000"
        :color="snackbarColor"
        variant="outlined"
        v-model="show"
    >
        {{ message }}

        <template v-slot:actions>
            <v-btn
                :color="snackbarColor"
                variant="text"
                @click="show = false"
            >
                Close
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
export default {
    name: "CustomSnackbar",
    props: ['visible', 'message', 'snackbarColor'],
    data() {
        return {
            show: false,
        }
    },
    computed: {
        show: {
            get() {
                return this.visible
            },
            set(value) {
                if (!value) {
                    this.$emit('close')
                }
            }
        },
    },
}
</script>
