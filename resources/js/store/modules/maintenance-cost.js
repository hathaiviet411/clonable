import Cookies from 'js-cookie';

function getCurPage() {
    return 1;
}

function getPerPage() {
    return 20;
}

function getNoNumberPlate() {
    return {
        status: false,
        value: '',
    };
}

function getMaintenanceScheduleDate() {
    return {
        status: false,
        from: '',
        to: '',
    };
}

function getMaintenanceDate() {
    return {
        status: false,
        from: '',
        to: '',
    };
}

function getStatus() {
    return {
        status: false,
        value: '',
    };
}

function getMaintenanceType() {
    return {
        status: false,
        value: '',
    };
}

function getGarage() {
    return {
        status: false,
        value: '',
    };
}

const state = {
    cur_page: getCurPage(),
    per_page: getPerPage(),
    no_number_plate_filter: getNoNumberPlate(),
    maintenance_schedule_date_filter: getMaintenanceScheduleDate(),
    maintenance_date_filter: getMaintenanceDate(),
    status_filter: getStatus(),
    maintenance_type_filter: getMaintenanceType(),
    garage_filter: getGarage(),
};

const mutations = {
    SET_CURRENT_PAGE: (state, page) => {
        state.cur_page = page;
        Cookies.set('cur_page_maintenance_cost_list', page);
    },
    SET_PER_PAGE: (state, per_page) => {
        state.per_page = per_page;
        Cookies.set('per_page_maintenance_cost_list', per_page);
    },
    SET_NO_NUMBER_PLATE_FILTER: (state, no_number_plate_filter) => {
        state.no_number_plate_filter = no_number_plate_filter;
        Cookies.set('no_number_plate_filter', JSON.stringify(no_number_plate_filter));
    },
    SET_MAINTENANCE_SCHEDULE_DATE_FILTER: (state, maintenance_schedule_date_filter) => {
        state.maintenance_schedule_date_filter = maintenance_schedule_date_filter;
        Cookies.set('maintenance_schedule_date_filter', JSON.stringify(maintenance_schedule_date_filter));
    },
    SET_MAINTENANCE_DATE_FILTER: (state, maintenance_date_filter) => {
        state.maintenance_date_filter = maintenance_date_filter;
        Cookies.set('maintenance_date_filter', JSON.stringify(maintenance_date_filter));
    },
    SET_STATUS_FILTER: (state, status_filter) => {
        state.status_filter = status_filter;
        Cookies.set('status_filter', JSON.stringify(status_filter));
    },
    SET_MAINTENANCE_TYPE_FILTER: (state, maintenance_type_filter) => {
        state.maintenance_type_filter = maintenance_type_filter;
        Cookies.set('maintenance_type_filter', JSON.stringify(maintenance_type_filter));
    },
    SET_GARAGE_FILTER: (state, garage_filter) => {
        state.garage_filter = garage_filter;
        Cookies.set('garage_filter', JSON.stringify(garage_filter));
    },
};

const actions = {
    setCurrentPage({ commit }, page) {
        commit('SET_CURRENT_PAGE', page);
    },
    setPerPage({ commit }, per_page) {
        commit('SET_PER_PAGE', per_page);
    },
    setNoNumberPlateFilter({ commit }, no_number_plate_filter) {
        commit('SET_NO_NUMBER_PLATE_FILTER', no_number_plate_filter);
    },
    setMaintenanceScheduleDateFilter({ commit }, maintenance_schedule_date_filter) {
        commit('SET_MAINTENANCE_SCHEDULE_DATE_FILTER', maintenance_schedule_date_filter);
    },
    setMaintenanceDateFilter({ commit }, maintenance_date_filter) {
        commit('SET_MAINTENANCE_DATE_FILTER', maintenance_date_filter);
    },
    setStatusFilter({ commit }, status_filter) {
        commit('SET_STATUS_FILTER', status_filter);
    },
    setMaintenanceTypeFilter({ commit }, maintenance_type_filter) {
        commit('SET_MAINTENANCE_TYPE_FILTER', maintenance_type_filter);
    },
    setGarageFilter({ commit }, garage_filter) {
        commit('SET_GARAGE_FILTER', garage_filter);
    },
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};
