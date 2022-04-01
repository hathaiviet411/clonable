import * as RequestApi from '../request';

export function getAccessoryList(url) {
    return RequestApi.getAll(url);
}

export function getOneAccessory(url) {
    return RequestApi.getOne(url);
}

export function removeAccessory(url, user_id) {
    return RequestApi.deleteOne(url, user_id);
}

export function createAccessory(url, data) {
    return RequestApi.postOne(url, data);
}

export function updateAccessory(url, data) {
    return RequestApi.putOne(url, data);
}
