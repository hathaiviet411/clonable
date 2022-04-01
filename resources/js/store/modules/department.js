import Cookies from 'js-cookie';
import { getDepartmentList } from '@/api/modules/navbar';

function getDepartmentID() {
    const selectedDepartment = parseInt(Cookies.get('department_id'));
    if (selectedDepartment) {
        return parseInt(selectedDepartment);
    }
}

function getDepartmentName() {
    const selectedDepartment = Cookies.get('department_name');
    if (selectedDepartment) {
        return selectedDepartment;
    }
}

function getListDepartment() {
    const listDepartment = Cookies.get('listDepartment');
    if (listDepartment) {
        return JSON.parse(listDepartment);
    }
}

const state = {
    department_id: getDepartmentID() || 0,
    department_name: getDepartmentName() || '',
    listDepartment: getListDepartment() || [],
};

const mutations = {
    SET_DEPARTMENT_ID: (state, department_id) => {
        state.department_id = department_id;
        Cookies.set('department_id', department_id);
    },
    SET_DEPARTMENT_NAME: (state, department_name) => {
        state.department_name = department_name;
        Cookies.set('department_name', department_name);
    },
    SET_LIST_DEPARTMENT: (state, listDepartment) => {
        state.listDepartment = listDepartment;
        Cookies.set('listDepartment', JSON.stringify(listDepartment));
    },
    SET_CLEAR_DATA: (state) => {
        state.department_id = '';
        state.department_name = '';
        state.listDepartment = [];
        Cookies.set('department_id', '');
        Cookies.set('department_name', '');
        Cookies.set('listDepartment', []);
    },
};

const actions = {
    async setListDepartment({ commit, state }) {
        const url = 'department';
        const listDepartment = [];
        const response = await getDepartmentList(url);

        if (response.code === 200 && response.data) {
            for (let index = 0; index < response.data.length; index++) {
                listDepartment.push({ department_id: parseInt(response.data[index].id), department_name: response.data[index].name, disabled: false });
            }
            commit('SET_LIST_DEPARTMENT', listDepartment);
        }
    },
    setDepartment({ commit }, department) {
        commit('SET_DEPARTMENT_ID', department.department_id);
        commit('SET_DEPARTMENT_NAME', department.department_name);
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
