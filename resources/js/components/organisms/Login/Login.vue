<template>
	<div class="container-fluid login">
		<div class="container">
			<div class="col-xl-6 col-lg-6 mx-auto login-container">
				<div class="login-form">
					<vFormLogin :disable-input="isProcess" />
				</div>
				<div class="login-button-submit">
					<vButton :text-button="$t('LOGIN.BUTTON_TEXT')" :class-name="'v-izumi-btn login-btn'" :disabled="isProcess" @click.native="handleLogin()" />
				</div>
			</div>
		</div>
	</div>
</template>

<script>
const urlAPI = {
    urlLogin: `/auth/login`,
};

import { resetRouter } from '@/router';
import { postLogin } from '@/api/modules/login';
import { validateNumberMoreThanZero, validEmptyOrWhiteSpace } from '@/utils/validate';
import { parseToken } from '@/utils/handleToken';
import { handleListPermissions } from '@/utils/handleLogin';

import vFormLogin from '@/components/molecules/vFormLogin';
import vButton from '@/components/atoms/vButton';

export default {
    name: 'LoginOrganisms',
    components: {
        vFormLogin,
        vButton,
    },
    data() {
        return {
            isProcess: false,
            Account: {
                id: '',
                password: '',
            },
        };
    },
    created() {
        this.$bus.on('emitFormLogin', (value) => {
            this.Account = value;
        });
    },
    destroyed() {
        this.$bus.off('emitFormLogin');
    },
    methods: {
        async handleLogin() {
            this.isProcess = true;

            if (validateNumberMoreThanZero(this.Account.id) && !validEmptyOrWhiteSpace(this.Account.password)) {
                const URL = urlAPI.urlLogin;

                const DATA = {
                    user_code: this.Account.id,
                    password: this.Account.password,
                };

                await this.callApiLogin(URL, DATA);
            } else {
                this.$toast.warning({
                    content: this.$t('TOAST.CONTENT.LOGIN.WRONG_ID_PASSWORD'),
                });
            }

            this.isProcess = false;
        },

        async callApiLogin(URL, DATA) {
            await postLogin(URL, DATA)
                .then(async(response) => {
                    if (response.code === 200) {
                        const TOKEN = response.data.access_token;
                        const PROFILE = response.data.profile;
                        const EXP_TOKEN = parseToken(TOKEN);
                        const ROLES = response.data['roles'];
                        const PERMISSIONS = handleListPermissions(response.data['permissions']);

                        const USER = {
                            id: PROFILE.id || '',
                            name: PROFILE.user_name || '',
                            department_id: PROFILE.department_id || '',
                            current_year: PROFILE.current_year || '',
                            current_year_month: PROFILE.current_year_month || '',
                            expToken: EXP_TOKEN.exp || '',
                            roles: ROLES || [],
                            permissions: PERMISSIONS || [],
                        };

                        await this.$store.dispatch('time/setListYearOrYearMonth').then(async() => {
                            const PRESENT_YEAR = new Date().getFullYear();
                            const LIST_YEAR = this.$store.getters.listYear;

                            var DEFAULT_DISPLAY_YEAR = '';

                            for (let i = 0; i < LIST_YEAR.length; i++) {
                                if (LIST_YEAR[i].text === PRESENT_YEAR) {
                                    DEFAULT_DISPLAY_YEAR = PRESENT_YEAR;
                                    break;
                                } else {
                                    DEFAULT_DISPLAY_YEAR = LIST_YEAR[0].value;
                                }
                            }

                            await this.$store.dispatch('time/setCurrentYear', parseInt(DEFAULT_DISPLAY_YEAR));

                            const DATE = new Date().getMonth() + 1;
                            const PRESENT_MONTH = ('0' + DATE).slice(-2);
                            const PRESENT_YEAR_MONTH = `${PRESENT_YEAR}-${PRESENT_MONTH}`;
                            const LIST_YEAR_MONTH = this.$store.getters.listYearMonth;

                            var DEFAULT_DISPLAY_YEAR_MONTH = '';

                            for (let i = 0; i < LIST_YEAR_MONTH.length; i++) {
                                if (LIST_YEAR_MONTH[i].text === PRESENT_YEAR_MONTH) {
                                    DEFAULT_DISPLAY_YEAR_MONTH = PRESENT_YEAR_MONTH;
                                    break;
                                } else {
                                    DEFAULT_DISPLAY_YEAR_MONTH = LIST_YEAR_MONTH[0].value;
                                }
                            }

                            await this.$store.dispatch('time/setCurrentYearMonth', DEFAULT_DISPLAY_YEAR_MONTH);
                        });

                        await this.$store.dispatch('department/setListDepartment');

                        await this.$store.dispatch('user/saveLogin', { USER, TOKEN })
                            .then(async() => {
                                this.$toast.success({
                                    content: this.$t('TOAST.CONTENT.LOGIN.SUCCESS'),
                                });

                                resetRouter();

                                const ROLES = this.$store.getters.roles;
                                const PERMISSIONS = this.$store.getters.permissions;

                                await this.$store.dispatch('permissions/generateRoutes', { roles: ROLES, permissions: PERMISSIONS })
                                    .then((routes) => {
                                        for (let route = 0; route < routes.length; route++) {
                                            this.$router.addRoute(routes[route]);
                                        }

                                        this.$router.push('/');
                                    });

                                const USER_DEPARTMENT_ID = this.$store.getters.profile.department_id;
                                const LIST_DEPARTMENT = this.$store.getters.listDepartment;
                                let DEPARTMENT = [];
                                let DEPARTMENT_ID = '';
                                let DEPARTMENT_NAME = '';

                                if (USER_DEPARTMENT_ID) {
                                    for (let i = 0; i < LIST_DEPARTMENT.length; i++) {
                                        if (USER_DEPARTMENT_ID === LIST_DEPARTMENT[i].department_id) {
                                            DEPARTMENT_ID = LIST_DEPARTMENT[i].department_id;
                                            DEPARTMENT_NAME = LIST_DEPARTMENT[i].department_name;
                                        }
                                    }

                                    DEPARTMENT = {
                                        department_id: DEPARTMENT_ID,
                                        department_name: DEPARTMENT_NAME,
                                    };
                                } else {
                                    DEPARTMENT = {
                                        department_id: LIST_DEPARTMENT[0].department_id,
                                        department_name: LIST_DEPARTMENT[0].department_name,
                                    };
                                }

                                await this.$store.dispatch('department/setDepartment', DEPARTMENT);
                            })
                            .catch(() => {
                                this.$toast.error({
                                    content: this.$t('TOAST.CONTENT.OTHER.ERROR'),
                                });
                            });
                    } else {
                        this.$toast.error({
                            content: this.$t('TOAST.CONTENT.OTHER.ERROR'),
                        });
                    }
                })
                .catch((error) => {
                    this.isProcess = false;

                    if (error['response']['status'] !== 403) {
                        this.$toast.error({
                            content: error['response']['data']['message'],
                        });
                    }
                });
        },
    },
};
</script>

<style lang="scss" scoped>
@import "@/scss/variables.scss";
.login {
    margin-top: 150px;

    .login-button-submit {
        text-align: center;
        margin-top: 40px;
    }
}
</style>
