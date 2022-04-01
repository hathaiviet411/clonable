import store from '@/store';

const ListDepartment = store.getters.listDepartment;
const Length = ListDepartment.length;

export function toDepartmentName(department_id) {
    if (department_id) {
        for (let index = 0; index < Length; index++) {
            if (department_id === ListDepartment[index].department_id) {
                return ListDepartment[index].department_name;
            }
        }
    }
}
