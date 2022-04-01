<template>
	<div class="form-login">
		<div class="item-input">
			<vLabel :text-label="$t('LOGIN.ID')" />
			<vInput :id="'user_id'" v-model="Account.id" :type="'text'" :placeholder="$t('LOGIN.ID')" :disabled="disableInput" />
		</div>

		<div class="item-input">
			<vLabel :text-label="$t('LOGIN.PASSWORD')" />
			<vInput :id="'password'" v-model="Account.password" :type="'password'" :placeholder="$t('LOGIN.PASSWORD')" :disabled="disableInput" />
		</div>
	</div>
</template>

<script>
import vLabel from '@/components/atoms/vLabel';
import vInput from '@/components/atoms/vInput';

export default {
    name: 'VFormLogin',
    components: {
        vLabel,
        vInput,
    },
    props: {
        disableInput: {
            type: Boolean,
            require: false,
            default: false,
        },
    },
    data() {
        return {
            Account: {
                id: '',
                password: '',
            },
        };
    },
    computed: {
        idUserChange() {
            return this.Account.id;
        },
        passswordUserChange() {
            return this.Account.password;
        },
    },
    watch: {
        idUserChange() {
            this.emitData();
        },
        passswordUserChange() {
            this.emitData();
        },
    },
    created() {
        this.emitData();
    },
    methods: {
        emitData() {
            this.$bus.emit('emitFormLogin', this.Account);
        },
    },
};
</script>

<style lang="scss" scoped>
.form-login {
    .item-input {
        margin-bottom: 10px;
    }
}
</style>
