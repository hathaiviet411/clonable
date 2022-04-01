import * as RequestApi from '../request';

export function getListDepartment(url) {
    return RequestApi.getAll(url);
}
