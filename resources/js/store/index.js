import Vue from 'vue';
import Vuex from 'vuex';

// Import modules
import app from './modules/app';
import user from './modules/user';
import time from './modules/time';
import department from './modules/department';
import permissions from './modules/permissions';
import accessory from './modules/accessory';
import userManagement from './modules/user-management';
import maintenanceCost from './modules/maintenance-cost';
import maintenanceScheduleResults from './modules/maintenance-schedule-results';

// Import getters
import getters from './getters';

Vue.use(Vuex);

const modules = {
    app,
    user,
    time,
    department,
    permissions,
    accessory,
    userManagement,
    maintenanceCost,
    maintenanceScheduleResults,
};

const store = new Vuex.Store({
    modules,
    getters,
});

export default store;
