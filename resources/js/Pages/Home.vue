<template>
    <v-app data-app>
        <CustomSnackbar :visible="showSnackbar" :message="snackbarMessage" :snackbar-color="snackbarColor"
                        @close="showSnackbar = false"/>
        <v-toolbar
            dark
            prominent
        >
            <v-btn color="blue darken-1" v-if="permissions.isAdmin">
                <Link :href="route('user.management')">User management</Link>
                <v-icon>mdi-account</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn icon :href="route('logout')">
                <v-icon>mdi-export</v-icon>
            </v-btn>
        </v-toolbar>
        <v-container class="fill-height">

            <v-row class="d-flex justify-center align-center">
                <v-col class="d-flex justify-center align-center">
                    <div style="width: 800px">
                        <v-card width="800">
                            <v-card-item>
                                <v-text-field v-if="uniqueLink" v-model="uniqueLink" readonly
                                              class="w-100 h-25 mt-10"></v-text-field>
                                <div class="d-flex flex-col align-center">
                                    <v-btn size="small" color="success" class="mb-2 w-25" @click="copyToBuffer">Copy
                                    </v-btn>
                                    <v-btn size="small" color="success" class="mb-2 w-25" @click="regenerate">
                                        Regenerate
                                    </v-btn>
                                    <v-btn size="small" color="error" class="mb-2 w-25" @click="deactivate">Deactivate
                                    </v-btn>
                                    <v-btn color="success" class="mb-2 w-25" @click="feelingLucky">Im feeling lucky
                                    </v-btn>
                                    <v-divider class="mt-3 mb-3"></v-divider>
                                    <v-btn color="blue darken-1" size="small" class="mb-2 w-25" @click="showHistory">
                                        History
                                    </v-btn>
                                </div>
                            </v-card-item>
                        </v-card>
                        <v-card width="800" class="mt-10" v-if="historyOpened">
                            <v-list disabled height="200">
                                <v-list-subheader>HISTORY</v-list-subheader>

                                <v-list-item
                                    v-for="(item, i) in history"
                                    :key="i"
                                >
                                    <template v-slot:prepend>
                                        <v-icon
                                            :color="`${item.game_result === 'Win' ? 'success' : 'error'}`"
                                            :icon="`${item.game_result === 'Win' ? 'mdi-check' : 'mdi-close'}`"
                                        ></v-icon>
                                    </template>

                                    <v-list-item-title v-text="item.message"></v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-card>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </v-app>
</template>

<script>
import CustomSnackbar from "@/Pages/CustomSnackbar.vue";
import {router} from "@inertiajs/vue3";
import axios from "axios";
import {Link} from "@inertiajs/vue3";

export default {
    name: 'Home',
    props: ['permissions'],
    data() {
        return {
            history: [],
            snackbarColor: 'success',
            showSnackbar: false,
            historyOpened: false,
            snackbarMessage: null,
            tokenMessage: null,
            uniqueLink: null,
        }
    },
    components: {
        CustomSnackbar,
        Link,
    },
    computed: {
        tokenMessageComputed: {
            get() {
                return this.token;
            },
            set(value) {
                this.tokenMessage = value;
            }

        },
    },
    mounted() {
        this.getToken();
    },
    methods: {
        getToken() {
            axios.post('/token/get-token')
                .then(res => {
                    this.uniqueLink = res.data?.token;
                })
                .catch(e => {
                    console.log(e.response);
                })
        },

        showHistory() {
            this.history = [];
            axios.post('/show-history')
                .then(res => {
                    this.history = res.data?.history;
                    if (this.historyOpened === false) {
                        this.historyOpened = true;
                    }
                    console.log(this.history)
                })
                .catch(e => {
                    console.log(e.response);
                })
        },

        regenerate() {
            axios.post('/token/regenerate')
                .then(res => {
                    this.uniqueLink = res.data?.token;
                    this.snackbarColor = 'success';
                    this.showSnackbarMessage(res.data?.message);
                })
                .catch(e => {
                    console.log(e.response);
                })
        },

        deactivate() {
            axios.post('/token/deactivate')
                .then(res => {
                    if (res.data.hasOwnProperty('redirect_to')) {
                        router.visit(res.data.redirect_to);
                    }
                })
                .catch(e => {
                    console.log(e.response);
                })
        },

        feelingLucky() {
            axios.post('/feeling-lucky')
                .then(res => {
                    this.snackbarColor = res.data?.result_name === 'Win' ? 'success' : 'error';
                    this.showSnackbarMessage(res.data?.message);
                    if (res.data.message) {
                        this.updateHistoryArray(res.data.message);
                    }
                    console.log(this.$page.props.auth.user.name)
                })
                .catch(e => {
                    console.log(e.response);
                })
        },

        updateHistoryArray(message) {
            if (this.history.length === 3) {
                this.history.pop();
            }
            let gameResult = message.includes('win') ? 'Win' : 'Lose';
            this.history.unshift({message: message, game_result: gameResult});
        },

        showSnackbarMessage(text) {
            this.showSnackbar = false;
            this.snackbarMessage = text;
            this.showSnackbar = true;
        },

        copyToBuffer() {
            navigator.clipboard.writeText(this.uniqueLink)
                .then(() => {
                    this.snackbarColor = 'success';
                    this.showSnackbarMessage('Copied to clipboard');
                })
                .catch((err) => {
                    this.snackbarColor = 'error';
                    this.showSnackbarMessage('And error occurred');
                });
        },
    },
}

import {Head} from '@inertiajs/vue3';
</script>
