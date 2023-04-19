<template>
    <v-app data-app>
        <CustomSnackbar
            :visible="showSnackbar"
            :message="snackbarMessage"
            :snackbar-color="snackbarColor"
            @close="showSnackbar = false"
        />
        <v-toolbar
            dark
            prominent
        >
            <v-btn color="blue darken-1">
                <Link :href="route('home')">Home</Link>
                <v-icon>mdi-home</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn icon :href="route('logout')">
                <v-icon>mdi-export</v-icon>
            </v-btn>
        </v-toolbar>
        <CreateEditUser
            :visible="openCreateEditDialog"
            :dataToEdit="dataToEdit"
            :error="error"
            @close="openCreateEditDialog = false"
            @edit="editUserOnBackend"
            @create="createUserOnBackend"
        />
        <v-container class="fill-height">
            <v-row class="d-flex justify-center align-center">
                <v-table density="compact">
                    <template v-slot:top>
                        <v-btn size="small" color="success" class="mb-5 ml-2 mt-5" @click="createUserDialog">Create
                            user
                        </v-btn>
                    </template>
                    <thead>
                    <tr>
                        <th class="text-left">
                            ID
                        </th>
                        <th class="text-left">
                            Name
                        </th>
                        <th class="text-left">
                            Role
                        </th>
                        <th class="text-left">
                            Phone number
                        </th>
                        <th class="text-left">
                            Is Active
                        </th>
                        <th class="text-left">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="item in users"
                        :key="item.phone_number"
                    >
                        <td :class="{'custom-bg-color': defineMyUserBgColor(item.id)}" class="custom-font-size">{{ item.id }}</td>
                        <td :class="{'custom-bg-color': defineMyUserBgColor(item.id)}" class="custom-font-size">{{ item.name }}</td>
                        <td :class="{'custom-bg-color': defineMyUserBgColor(item.id)}" class="custom-font-size">{{ item.role.name }}</td>
                        <td :class="{'custom-bg-color': defineMyUserBgColor(item.id)}" class="custom-font-size">{{ item.phone_number }}</td>
                        <td :class="{'custom-bg-color': defineMyUserBgColor(item.id)}" class="custom-font-size d-flex justify-center align-center">
                            <v-icon
                                size="small"
                                :color="`${item.is_active === 1 ? 'success' : 'error'}`"
                                :icon="`${item.is_active === 1 ? 'mdi-check' : 'mdi-close'}`"
                            ></v-icon>
                        </td>
                        <td class="custom-font-size"
                            :class="{'custom-bg-color': defineMyUserBgColor(item.id)}"
                        >
                            <v-icon @click="editUserDialog(item.id)" color="blue" size="small"
                                    icon="mdi-pencil"></v-icon>
                            <v-icon @click="deleteUser(item.id)" color="red" size="small" icon="mdi-delete"
                                    class="ml-3"></v-icon>
                            <v-btn
                                size="small"
                                :color="item.is_active === 1 ? 'success'
                                : ''" class="ml-3"
                                :disabled="item.is_active === 0 || $page.props.auth.user.id === item.id"
                                @click="loginAsUser(item.id)"
                            >
                                login
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-table>
            </v-row>
        </v-container>
    </v-app>
</template>


<script>
import CustomSnackbar from "@/Pages/CustomSnackbar.vue";
import CreateEditUser from "@/Dialogues/CreateEditUser.vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import {Link} from "@inertiajs/vue3";

export default {
    name: "UserManagement",
    data() {
        return {
            error: null,
            openCreateEditDialog: false,
            snackbarColor: 'success',
            showSnackbar: false,
            snackbarMessage: null,
            users: [],
            dataToEdit: {},
        }
    },
    components: {
        CustomSnackbar,
        CreateEditUser,
        Link,
    },
    mounted() {
        this.getUsers();
    },
    methods: {
        loginAsUser(userId) {
            axios.post('/admin/login-as', { id: userId })
                .then(res => {
                    if (res.data.redirect_to) {
                        router.visit(res.data.redirect_to);
                    }
                })
                .catch(e => {
                    console.log(e.response);
                })
        },
        defineMyUserBgColor(userId) {
            return this.$page.props.auth.user.id === userId;
        },
        createUserDialog() {
            this.openCreateEditDialog = true;
            this.dataToEdit = {};
        },
        editUserDialog(userId) {
            this.openCreateEditDialog = true;
            this.dataToEdit = this.users.filter(i => i.id === userId);
        },
        deleteUser(userId) {
            if (window.confirm("Do you really want to delete this user?")) {
                this.deleteUserOnBackend(userId);
            }
        },

        editUserOnBackend(data) {
            axios.post('/admin/edit-user', data)
            .then(res => {
                this.showSnackbarMessage(res.data.message, 'success');
                this.openCreateEditDialog = false;
                if (res.data.redirect_to) {
                    router.visit(res.data.redirect_to);
                }
                this.getUsers();
            })
            .catch(e => {
                this.error = '';
                if (e.response.data.message) {
                    this.error = e.response.data.message;
                }
                console.log(e.response);
            })
        },

        createUserOnBackend(data) {
            axios.post('/admin/create-user', data)
            .then(res => {
                this.showSnackbarMessage(res.data.message, 'success');
                this.getUsers();
                this.openCreateEditDialog = false;
            })
            .catch(e => {
                this.error = '';
                if (e.response.data.message) {
                    this.error = e.response.data.message;
                }
                console.log(e.response);
            })
        },

        deleteUserOnBackend(userId) {
            axios.post('/admin/delete-user', {id: userId,})
                .then(res => {
                    if (res.data.redirect_to) {
                        router.visit(res.data.redirect_to);
                    }
                    if (res.data.message) {
                        this.showSnackbarMessage(res.data.message, 'success');
                        this.getUsers();
                    }
                })
                .catch(e => {
                    console.log(e.response)
                })
        },

        getUsers() {
            this.users = [];
            axios.post('/admin/get-users')
                .then(res => {
                    this.users = res.data.users;
                })
                .catch(e => {
                    console.log(e.response)
                })
        },

        showSnackbarMessage(text, color) {
            this.snackbarMessage = text;
            this.snackbarColor = color;
            this.showSnackbar = true;
        },

    },
}
</script>

<style scoped>
.custom-font-size {
    font-size: 15px
}
.custom-bg-color {
    background-color: #004D40;
}
</style>
