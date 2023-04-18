<template>
    <v-row justify="center">
        <v-dialog
            v-model="showDialog"
            persistent
            width="500"
        >
            <v-card>
                <v-card-title>
                    <span class="text-h5 ml-4">User Profile</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col
                                cols="12"
                            >
                                <v-text-field
                                    v-model="editUserData.name"
                                    density="compact"
                                    label="Username"
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col
                                cols="12"
                            >
                                <v-text-field
                                    v-model="editUserData.phoneNumber"
                                    density="compact"
                                    label="Phone number"
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col
                                cols="12"
                            >
                                <v-row class="d-flex justify-start ml-2 mr-2">
                                    <v-radio-group inline v-model="editUserData.role">
                                        <template v-slot:label>
                                            <div>User role</div>
                                        </template>
                                        <div v-for="role in roles" :key="role.name">
                                            <v-radio :label="capitalizeFirstLetter(role.name)" :value="role.name" />
                                        </div>
                                    </v-radio-group>
                                </v-row>
                            </v-col>
                            <v-col
                                cols="12"
                            >
                                <v-checkbox v-model="editUserData.isActive" density="compact" color="red"
                                            label="Is Active" class="ml-3"></v-checkbox>
                            </v-col>
                            <v-col
                                v-if="errorMessage && errorMessage.length > 0"
                                cols="12"
                            >
                                <span style="color: red; font-size: 14px">{{ errorMessage }}</span>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="text"
                        @click="showDialog = false"
                    >
                        Close
                    </v-btn>
                    <v-btn
                        color="green-darken-1"
                        variant="text"
                        @click="createOrUpdateUser"
                    >
                        Save
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<script>
import axios from "axios";

export default {
    name: "CreateEditUser",
    props: ['visible', 'dataToEdit', 'error'],
    data() {
        return {
            errorFormBackend: null,
            roles: [],
            userData: {
                id: null,
                name: null,
                isActive: true,
                role: null,
                phoneNumber: null,
            },
            shouldCreateUser: true,
        }
    },
    computed: {
        errorMessage: {
            get() {
                if (this.error && this.error.length > 0) {
                    this.errorFormBackend = [];
                    this.errorFormBackend = this.error;
                }
                return this.errorFormBackend;
            },
            set(value) {
                this.errorFormBackend = [];
                this.errorFormBackend = value;
            },
        },
        showDialog: {
            get() {
                return this.visible
            },
            set(value) {
                if (!value) {
                    this.$emit('close');
                }
            },
        },
        editUserData: {
            get() {
                if (this.dataToEdit.length > 0) {
                    this.userData.role = this.dataToEdit[0].role.name;
                    this.userData.id = this.dataToEdit[0].id;
                    this.userData.name = this.dataToEdit[0].name;
                    this.userData.phoneNumber = this.dataToEdit[0].phone_number;
                    this.userData.isActive = this.dataToEdit[0].is_active === 1;

                    this.shouldCreateUser = false;
                } else {
                    this.resetUserDataAndDialogStatus();
                }
                return this.userData;
            },
            set(value) {
                this.userData = value;
            },
        },
    },
    mounted() {
        this.fetchAllRoles();
    },
    methods: {
        capitalizeFirstLetter(word) {
            return word.charAt(0).toUpperCase() + word.slice(1);
        },
        fetchAllRoles() {
            this.roles = [];
            axios.post('/admin/get-roles')
                .then(res => {
                    this.roles = res.data?.roles;
                })
                .catch(e => {
                    console.log(e.response)
                })
        },
        resetUserDataAndDialogStatus() {
            this.errorFormBackend = [];
            this.shouldCreateUser = true;
            this.userData = {
                id: null,
                name: null,
                isActive: true,
                role: null,
                phoneNumber: null,
            };
        },
        createOrUpdateUser() {
            let formattedDataForBackend = this.formatDataForBackend();
            this.errorFormBackend = [];

            if (this.shouldCreateUser) {
                this.$emit('create', formattedDataForBackend);
            } else {
                this.$emit('edit', formattedDataForBackend);
            }
        },
        formatDataForBackend() {
            return {
                id: this.userData.id,
                name: this.userData.name,
                is_active: this.userData.isActive,
                phone_number: this.userData.phoneNumber,
                role: this.userData.role,
            };
        },
    },

}
</script>

<style scoped>

</style>
