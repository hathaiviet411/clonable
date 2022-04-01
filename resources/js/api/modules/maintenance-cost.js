import * as RequestApi from '../request';

export function getMaintenanceCostList(url) {
    return RequestApi.getAll(url);
}

export function getOneMaintenanceCost(url) {
    return RequestApi.getOne(url);
}

export function createMaintenanceCost(url, data) {
    return RequestApi.postOne(url, data);
}

export function updateMaintenanceCost(url, data) {
    return RequestApi.putOne(url, data);
}

export function removeMaintenanceCost(url) {
    return RequestApi.deleteOne(url);
}

export function getFilterOptions(url) {
    return RequestApi.getAll(url);
}

export function getVehicleInfoByPlate(url) {
    return RequestApi.getOne(url);
}

export function getAllAccessory(url) {
    return RequestApi.getAll(url);
}

export function getAllVehiclePlates(url) {
    return RequestApi.getAll(url);
}

export function getMaintenanceCostVehicleData(url) {
    return RequestApi.getOne(url);
}

export function getMaintenanceCostMaintenanceData(url) {
    return RequestApi.getOne(url);
}
