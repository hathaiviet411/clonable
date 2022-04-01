<template>
	<div class="date-selector">
		<b-button-group>
			<b-button :class="isDisabledMinus === false ? 'minus-btn' : 'disabled-btn'" @click="minus(currentYear)">
				<i class="fas fa-caret-left" />
			</b-button>
			<b-button class="date">
				{{ currentYear }}
			</b-button>
			<b-button :class="isDisabledPlus === false ? 'plus-btn' : 'disabled-btn'" @click="plus(currentYear)">
				<i class="fas fa-caret-right" />
			</b-button>
		</b-button-group>
	</div>
</template>

<script>

import { getDateTimeList } from '@/api/modules/navbar';

export default {
    name: 'DateSelector',
    data() {
        return {
            ListDateTime: [],
            currentDateTime: '',
            listYear: this.$store.getters.listYear,
            currentYear: '',
            isDisabledPlus: false,
            isDisabledMinus: false,
        };
    },
    watch: {
        $route() {
            this.updateListYearMonthPicker();
            this.handleGetListDateTime();
        },
        currentYear() {
            if (this.currentYear === parseInt(this.listYear[0].text)) {
                this.isDisabledMinus = true;
            } else if (this.currentYear === parseInt(this.listYear[this.listYear.length - 1].text)) {
                this.isDisabledPlus = true;
            } else {
                this.isDisabledPlus = false;
                this.isDisabledMinus = false;
            }

            this.updateListYearMonthPicker();
        },
    },
    created() {
        this.updateListYearMonthPicker();
        this.handleGetListDateTime();
    },
    methods: {
        async updateListYearMonthPicker() {
            await this.$store.dispatch('time/setListYearOrYearMonth');
        },
        async getListYearMonth() {
            const url = 'system-config/year-conf';
            const response = await getDateTimeList(url);
            if (response.code === 200) {
                for (let index = 0; index < response.data.listYearMonth.length; index++) {
                    this.listYearMonth.push({ value: response.data.listYearMonth[index], text: response.data.listYearMonth[index], disabled: false });
                }
            }
            this.ListDateTime = this.listYearMonth;
        },

        async handleGetListDateTime() {
            this.ListDateTime = [];
            this.isSpecialCase = true;
            this.ListDateTime = this.listYear;
            this.currentYear = parseInt(this.$store.getters.current_year);
        },

        async handleSelectDateTime(value) {
            this.$store.dispatch('time/setCurrentYear', value);
            this.currentDateTime = value;
        },

        async minus(time) {
            this.isDisabledPlus = false;
            const ListYear = this.listYear;

            if (ListYear[0].text === time) {
                this.currentYear = ListYear[0].text;
                await this.handleSelectDateTime(this.currentYear);
            } else {
                for (let i = 1; i < ListYear.length; i++) {
                    if (ListYear[i].text === time) {
                        this.currentYear = ListYear[i - 1].text;
                        await this.handleSelectDateTime(this.currentYear);
                    }
                }
            }
        },

        async plus(current_time) {
            this.isDisabledMinus = false;
            const ListYear = this.listYear;

            if (ListYear[ListYear.length - 1].text === current_time) {
                this.currentYear = ListYear[ListYear.length - 1].text;
                await this.handleSelectDateTime(this.currentYear);
            } else {
                for (let i = 0; i < ListYear.length - 1; i++) {
                    if (ListYear[i].text === current_time) {
                        this.currentYear = ListYear[i + 1].text;
                        await this.handleSelectDateTime(this.currentYear);
                    }
                }
            }
        },
    },
};
</script>

<style lang="scss" scoped>
    @import '@/scss/variables';

    .date-selector {

        button {
            background-color: $west-side;

            &:active {
                background-color: $west-side;
            }

            &:focus {
                background-color: $west-side;
            }
        }

        button.date {
            cursor: default;
            min-width: 120px;
            font-weight: 600;
        }

        button.disabled-btn {
            background-color: $abbey;
            pointer-events: none;
        }

        button.minus-btn,
        button.plus-btn {
            &:hover {
                opacity: .8 !important;
                background-color: $west-side;
            }
        }
    }
</style>
