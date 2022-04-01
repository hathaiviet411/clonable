import Cookies from 'js-cookie';

function getCurPage() {
    const cur_page = Cookies.get('cur_page_user_list');

    if (cur_page) {
        return parseInt(cur_page);
    }

    return 1;
}

function getPerPage() {
    const per_page = Cookies.get('per_page_user_list');

    if (per_page) {
        return parseInt(per_page);
    }

    return 20;
}

const state = {
    cur_page: getCurPage(),
    per_page: getPerPage(),
};

const mutations = {
    SET_CURRENT_PAGE: (state, page) => {
        state.cur_page = page;
        Cookies.set('cur_page_user_list', page);
    },
    SET_PER_PAGE: (state, per_page) => {
        state.per_page = per_page;
        Cookies.set('per_page_user_list', per_page);
    },
};

const actions = {
    setCurrentPage({ commit }, page) {
        commit('SET_CURRENT_PAGE', page);
    },
    setPerPage({ commit }, per_page) {
        commit('SET_PER_PAGE', per_page);
    },
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};
