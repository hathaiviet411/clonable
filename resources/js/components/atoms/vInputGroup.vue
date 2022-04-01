<template>
	<b-input-group>
		<b-input-group-prepend is-text>
			<input v-model="isChecked" class="chk_filter" type="checkbox" :dusk="id">
		</b-input-group-prepend>
		<b-input-group-prepend is-text>
			<span style="min-width: 125px;">{{ textPrepend }}</span>
		</b-input-group-prepend>
		<vInput
			:id="id"
			v-model="data"
			:name="name"
			:type="type"
			:class="className"
			:style="styleEl"
			:placeholder="placeholder"
			:validate="validate"
			:disabled="!isChecked"
		/>
	</b-input-group>
</template>

<script>
import vInput from './vInput.vue';

export default {
    name: 'VInputGroup',
    components: {
        vInput,
    },
    props: {
        id: {
            type: String,
            require: false,
            default: '',
        },
        type: {
            type: String,
            require: false,
            default: 'text',
            validate: value => {
                return ['text', 'number', 'email', 'password', 'search', 'url', 'tel', 'date', 'time', 'range', 'range'].includes(value);
            },
        },
        className: {
            type: String,
            require: false,
            default: 'v-custom-input',
        },
        styleEl: {
            type: String,
            require: false,
            default: '',
        },
        value: {
            type: String,
            require: true,
            default: '',
        },
        placeholder: {
            type: String,
            require: true,
            default: function() {
                return '';
            },
        },
        validate: {
            type: Boolean,
            require: false,
            default: false,
        },
        name: {
            type: String,
            require: false,
            default: '',
        },
        textPrepend: {
            type: String,
            require: false,
            default: '',
        },
        isCheck: {
            type: Boolean,
            require: true,
            default: function() {
                return false;
            },
        },
    },
    data() {
        return {
            isChecked: false,
            data: '',
        };
    },
    computed: {
        dataChange() {
            return this.data;
        },
        isCheckedChange() {
            return this.isChecked;
        },
        setDataChange() {
            return this.value;
        },
    },
    watch: {
        dataChange() {
            this.setEmit();
        },
        isCheckedChange() {
            this.setEmitIsChecked();
        },
        setDataChange() {
            this.setData();
        },
        isCheck() {
            this.isChecked = this.isCheck;
        },
    },
    created() {
        this.setData();
        this.setEmit();
    },
    methods: {
        setData() {
            this.data = this.value;
        },
        setEmit() {
            this.$emit('input', this.data);
        },
        setEmitIsChecked() {
            this.$emit('isChecked', this.isChecked);
        },
    },
};
</script>
