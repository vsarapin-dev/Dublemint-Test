<template>
    <v-app data-app>
        <CustomSnackbar :visible="showSnackbar" :message="snackbarMessage" snackbarColor="success" @close="showSnackbar = false" />
        <v-container class="fill-height">
            <v-row class="d-flex justify-center align-center">
                <div style="width: 500px;">
                    <v-card>
                        <v-card-title>
                            Sign Up
                        </v-card-title>
                        <v-card-text>
                            <v-text-field
                                v-model="userName"
                                @input="resetError"
                                label="Username"
                                class="caption"/>
                            <v-text-field
                                v-model="phoneNumber"
                                @input="resetError"
                                label="Phone number"
                                type="number"
                                prefix="+3 80"
                                class="caption"/>
                            <div class="text-center">
                                <span style="font-size: 13px" class="text-red">{{ error }}</span>
                            </div>
                            <v-btn color="success" class="w-100 mt-2" @click="register">Sign Up</v-btn>
                        </v-card-text>
                    </v-card>
                    <v-text-field v-if="uniqueLink" v-model="uniqueLink" readonly append-inner-icon="mdi-content-copy" @click:appendInner="copyToBuffer" class="w-100 h-25 mt-10"></v-text-field>
                </div>
            </v-row>
        </v-container>
    </v-app>
</template>

<script>
import CustomSnackbar from "@/Pages/CustomSnackbar.vue";
import axios from "axios";

export default {
    name: 'Welcome',
    data() {
        return {
            showSnackbar: false,
            snackbarMessage: null,
            phonePrefix: '+380',
            error: null,
            uniqueLink: null,
            userName: null,
            phoneNumber: null,
        }
    },
    components: {
        CustomSnackbar,
    },
    methods: {
        register() {
            let phoneNumberCombined = `${this.phonePrefix}${this.phoneNumber ? this.phoneNumber : ''}`;
            console.log({
                user_name: this.userName,
                phone_number: phoneNumberCombined,
            })
            axios.post('/auth/sign-up', {
                user_name: this.userName,
                phone_number: phoneNumberCombined,
            })
                .then(res => {
                    this.uniqueLink = res.data.codeString;
                })
                .catch(e => {
                    console.log(e.response)
                    this.error = e?.response?.data?.message;
                    this.error = this.error.substring(0, this.error.indexOf("."));
                })
        },
        showSnackbarMessage(text)
        {
            this.snackbarMessage = text;
            this.showSnackbar = true;
        },
        copyToBuffer()
        {
            navigator.clipboard.writeText(this.uniqueLink)
                .then(() =>
                {
                    this.showSnackbarMessage('Copied to clipboard');
                })
                .catch((err) => {
                    this.showSnackbarMessage('And error occurred');
                });
        },
        resetError() {
            this.error = null;
        },
    },
}
</script>
