
import Cookies from 'js-cookie';
import { getDateTimeList } from '@/api/modules/navbar';

function getCurrentYear() {
    const selectedYear = Cookies.get('current_year');
    if (selectedYear) {
        return selectedYear;
    }
}

function getCurrentYearMonth() {
    const selectedYearMonth = Cookies.get('current_year_month');
    if (selectedYearMonth) {
        return selectedYearMonth;
    }
}

function getListYear() {
    const listYear = Cookies.get('listYear');
    if (listYear) {
        return JSON.parse(listYear);
    }
}

function getListYearMonth() {
    const listYearMonth = Cookies.get('listYearMonth');
    if (listYearMonth) {
        return JSON.parse(listYearMonth);
    }
}

const state = {
    current_year: getCurrentYear(),
    current_year_month: getCurrentYearMonth(),
    listYear: getListYear(),
    listYearMonth: getListYearMonth(),
};

const mutations = {
    SET_CURRENT_YEAR: (state, current_year) => {
        state.current_year = current_year;
        Cookies.set('current_year', current_year);
    },
    SET_CURRENT_YEAR_MONTH: (state, current_year_month) => {
        state.current_year_month = current_year_month;
        Cookies.set('current_year_month', current_year_month);
    },
    SET_LIST_YEAR: (state, listYear) => {
        state.listYear = listYear;
        Cookies.set('listYear', JSON.stringify(listYear));
    },
    SET_LIST_YEAR_MONTH: (state, listYearMonth) => {
        state.listYearMonth = listYearMonth;
        Cookies.set('listYearMonth', JSON.stringify(listYearMonth));
    },
    SET_CLEAR_DATA: (state) => {
        state.current_year = '';
        state.current_year_month = '';
        state.listYear = [];
        state.listYearMonth = [];
        Cookies.set('current_year', '');
        Cookies.set('current_year_month', '');
        Cookies.set('listYear', []);
        Cookies.set('listYearMonth', []);
    },
};

const actions = {
    async setListYearOrYearMonth({ commit, state }) {
        const url = 'system-config/year-conf';
        const listYear = [];
        const listYearMonth = [];
        const response = await getDateTimeList(url);

        if (response.code === 200 && response.data) {
            for (let index = 0; index < response.data.listYear.length; index++) {
                listYear.push({ value: parseInt(response.data.listYear[index]), text: response.data.listYear[index], disabled: false });
            }
            await commit('SET_LIST_YEAR', listYear);

            for (let index = 0; index < response.data.listYearMonth.length; index++) {
                listYearMonth.push({ value: response.data.listYearMonth[index], text: response.data.listYearMonth[index], disabled: false });
            }
            await commit('SET_LIST_YEAR_MONTH', listYearMonth);
        }
    },
    setCurrentYear({ commit }, year) {
        commit('SET_CURRENT_YEAR', year);
    },
    setCurrentYearMonth({ commit }, year_month) {
        commit('SET_CURRENT_YEAR_MONTH', year_month);
    },
    setClearDate({ commit }) {
        commit('SET_CLEAR_DATA');
    },
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};
