import Cookies from 'js-cookie';

function getScheduleAndResultTableStatus() {
    const isScheduleAndResultTable = Cookies.get('isScheduleAndResultTable');
    if (isScheduleAndResultTable) {
        return isScheduleAndResultTable;
    }
}

const state = {
    isScheduleAndResultTable: getScheduleAndResultTableStatus() || false,
};

const mutations = {
    SET_IS_SCHEDULE_AND_RESULT_TABLE: (state, isScheduleAndResultTable) => {
        state.isScheduleAndResultTable = isScheduleAndResultTable;
        Cookies.set('isScheduleAndResultTable', isScheduleAndResultTable);
    },
};

const actions = {
    setScheduleAndResultTable({ commit }, isScheduleAndResultTable) {
        commit('SET_IS_SCHEDULE_AND_RESULT_TABLE', isScheduleAndResultTable);
    },
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};
