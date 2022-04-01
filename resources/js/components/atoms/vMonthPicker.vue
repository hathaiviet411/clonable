<template>
	<div v-click-outside="hide" class="month-picker">
		<i class="fad fa-calendar-day" />
		<input
			:id="inputId"
			ref="input"
			:class="{ 'month-picker__input': true, 'form-control': true }"
			type="text"
			readonly
			:value="showDate"
			:disabled="disabled"
			@click="show()"
			@input="updateValue()"
		>

		<div v-if="pickerVisible" class="month-picker__container" :style="{top: ($refs.input.offsetHeight + 5) + 'px'}">
			<div class="month-picker__year">
				<button class="month-picker__year-btn" :disabled="isDisabledPrevious" @click="prevYear">
					<i class="fas fa-angle-left" />
				</button>
				<span class="month-picker__show-year">{{ year }}</span>
				<button class="month-picker__year-btn" :disabled="isDisabledNext" @click="nextYear">
					<i class="fas fa-angle-right" />
				</button>
			</div>

			<div class="month-picker__monthes">
				<a
					v-for="(m, index) in months"
					:key="'month'+index"
					href=""
					class="month-picker__month"
					:class="{ 'month-picker__month_selected': (index + 1 === currentMonth), 'disabled': m.disabled ? m.disabled : false}"
					@click.prevent="pickMonth(m.text)"
				>{{ m.text }}</a>
			</div>
		</div>
	</div>
</template>

<script>

// Import const
import constMonthPicker from '@/const/monthPicker';

// Import function helper
import {
    formatMonth,
    getObjectYM,
} from '@/utils/handleDate';

import {
    validateYYYYMM,
} from '@/utils/validate';

export default {
    name: 'MonthPicker',
    directives: {
        'click-outside': {
            bind: function(el, binding, vNode) {
                const bubble = binding.modifiers.bubble;
                const handler = (e) => {
                    if (bubble || (!el.contains(e.target) && el !== e.target)) {
                        binding.value(e);
                    }
                };

                el.__vueClickOutside__ = handler;
                document.addEventListener('click', handler);
            },

            unbind: function(el, binding) {
                document.removeEventListener('click', el.__vueClickOutside__);
                el.__vueClickOutside__ = null;
            },
        },
    },
    props: {
        inputId: {
            type: String,
            default: '',
        },
        defaultYear: {
            type: Number,
            default: new Date().getFullYear(),
        },
        defaultMonth: {
            type: Number,
            default: new Date().getMonth() + 1,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        validationYear: {
            type: String,
            default: '',
            require: false,
        },
    },
    data() {
        return {
            pickerVisible: false,
            year: '',
            month: this.defaultMonth,

            currentYear: '',
            currentMonth: '',

            isDisabledPrevious: '',
            isDisabledNext: '',

            months: constMonthPicker.LIST_MONTHS,
        };
    },
    computed: {
        showDate() {
            if (this.currentYear && this.currentMonth) {
                this.$emit('input', `${this.year}/${formatMonth(this.month)}`);

                const lang = this.$store.getters.language;

                if (lang === 'en') {
                    return `${formatMonth(this.month)}/${this.year}`;
                }

                return `${this.year}/${formatMonth(this.month)}`;
            } else {
                return `YYYY/mm`;
            }
        },

        dataChange() {
            return this.$attrs.value;
        },
    },
    watch: {
        defaultMonth(val) {
            this.month = val;
        },
        defaultYear(val) {
            this.year = val;
        },
        dataChange() {
            if (validateYYYYMM(this.$attrs.value)) {
                const DATA = getObjectYM(this.$attrs.value);

                this.year = parseInt(DATA.year);
                this.month = parseInt(DATA.month);

                this.currentYear = parseInt(DATA.year);
                this.currentMonth = parseInt(DATA.month);
            } else {
                this.currentYear = '';
                this.currentMonth = '';
            }
        },
        validationYear() {
            this.setMinMaxMonthPicker(this.validationYear);
        },
    },
    created() {
        if (validateYYYYMM(this.$attrs.value)) {
            const DATA = getObjectYM(this.$attrs.value);

            this.year = parseInt(DATA.year);
            this.month = parseInt(DATA.month);

            this.currentYear = parseInt(DATA.year);
            this.currentMonth = parseInt(DATA.month);
        }
    },
    methods: {
        setMinMaxMonthPicker(validationYear) {
            let YEAR;
            let MONTH;

            if (validationYear) {
                YEAR = validationYear.slice(0, 4);
                MONTH = validationYear.slice(5, 7);

                this.year = parseInt(YEAR);

                for (let i = 0; i < this.months.length; i++) {
                    if (this.months[i].text !== MONTH) {
                        this.months[i].disabled = true;
                    }
                }

                this.isDisabledPrevious = true;
                this.isDisabledNext = true;
            }
        },
        show() {
            this.pickerVisible = !this.pickerVisible;

            if ((this.currentMonth && this.currentYear)) {
                this.year = this.currentYear;
                this.month = this.currentMonth;
            }
        },
        hide() {
            this.pickerVisible = false;
        },
        pickMonth(month) {
            this.month = parseInt(month);
            this.$emit('input', `${this.year}/${formatMonth(this.month)}`);

            this.currentYear = this.year;
            this.currentMonth = this.month;

            this.hide();
        },
        prevYear() {
            this.year = this.year - 1;

            if (this.year > 1) {
                if (this.currentMonth) {
                    this.$emit('input', `${this.year}/${formatMonth(this.month)}`);
                }
            }
        },
        nextYear() {
            this.year = this.year + 1;

            if (this.currentMonth) {
                this.$emit('input', `${this.year}/${formatMonth(this.month)}`);
            }
        },
    },
};
</script>

<style scoped lang="scss">
    @import '@/scss/variables.scss';

    .month-picker {
        position: relative;

        &__container {
            width: 310px;
            border: 1px solid $silver;
            border-radius: 4px;
            background-color: $white;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 6px -1px, rgba(0, 0, 0, 0.06) 0px 2px 4px -1px;
            z-index: 10;
            overflow: hidden;
            position: absolute;
            left: 0;
            padding: 8px;
        }

        &__input {
            background-color: $white;
            width: 100%;
            border-radius: .25rem;
            padding: 6px 12px;
            cursor: pointer;
            text-indent: 25px;

            &:disabled {
                color: #6c757d;
                cursor: default;
                background: $athens-gray;
            }
        }

        &__monthes {
            display: flex;
            flex-wrap: wrap;

            a {
                &:hover {
                    text-decoration: none;
                }
            }

            a.disabled {
                pointer-events: none;
                cursor: default;
                opacity: 0.2;
            }
        }

        &__year {
            display: flex;
            padding: 10px;
            background-color: $abbey;
            margin-bottom: 5px;
            border-radius: 5px;
        }

        &__year-btn {
            flex: 0 0 40px;
            height: 33px;
            line-height: 33px;
            background-color: transparent;
            border: none;
            border-radius: .25rem;
            background-color: $sorbus;

            i {
                font-size: 20px;
                color: $white;
            }

            &:hover {
                opacity: .8;
            }

            &:disabled {
                opacity: .8;
            }
        }

        &__show-year {
            flex: 0 0 calc(100% - 80px);
            text-align: center;
            padding-top: 7px;
            font-size: 1.1rem;
            font-weight: bold;
            color: $white;
        }

        &__month {
            box-sizing: border-box;
            flex: 0 0 calc(100% / 3);
            text-align: center;
            padding: 10px 0;
            color: $shark;
            border-radius: 5px;
            font-weight: 500;

            &_selected {
                background-color: $sorbus !important;
                color: $white;
            }

            &:nth-child(3n+3) {
                border-right: 0;
            }

            &:hover {
                background-color: #f3f3f3;
            }
        }
    }

    .month-picker > i {
        position: absolute;
        left: 0;
        padding: 10px;
        pointer-events: none;
        font-size: 20px;
        color: $sorbus;
    }
</style>
