import { mount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import router from '@/router';
import UserManagementIndex from '@/pages/UserManagement/Index';
import UserManagementCreate from '@/pages/UserManagement/Create';
import UserManagementEdit from '@/pages/UserManagement/Edit';

describe('TEST COMPONENT USER MANAGEMENT', () => {
    test('Test render User Management Index', () => {
        const localVue = createLocalVue();
        const wrapper = mount(UserManagementIndex, {
            localVue,
            router,
            store,
        });

        const UserManagementIndexScreen = wrapper.find('.user-management');
        expect(UserManagementIndexScreen.exists()).toBe(true);

        wrapper.destroy();
    });

    test('Test render Table User Management', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(UserManagementIndex, {
            localVue,
            router,
            store,
        });

        const DATA = [
            {
                id: 1,
                user_code: 111111,
                department_id: 1,
                user_name: 'Super Admin',
                role_name: 'headquarter',
                departments_name: 'Tokyo',
                model_type: 'App\\Models\\User',
            },
            {
                id: 2,
                user_code: 666666,
                department_id: 1,
                user_name: 'User Team',
                role_name: 'team',
                departments_name: 'Tokyo',
                model_type: 'App\\Models\\User',
            },
            {
                id: 8,
                user_code: 0,
                department_id: 1,
                user_name: 'Dean.Macejkovic9',
                role_name: 'team',
                departments_name: 'Tokyo',
                model_type: 'App\\Models\\User',
            },
            {
                id: 9,
                user_code: 187,
                department_id: 1,
                user_name: 'Reynold49',
                role_name: 'operator',
                departments_name: 'Tokyo',
                model_type: 'App\\Models\\User',
            },
        ];

        const TableUserManagement = wrapper.find('#table-user-management');
        expect(TableUserManagement.exists()).toBe(true);

        await wrapper.setData({ vItems: DATA });

        const TableHeader = wrapper.find('thead');
        const ListHeader = TableHeader.findAll('th');
        expect(ListHeader.length).toEqual(6);

        const ListHeaderText = [
            'USER_MANAGEMENT.USER_ROLE (Click to sort Ascending)',
            'USER_MANAGEMENT.BASE (Click to sort Ascending)',
            'USER_MANAGEMENT.USER_ID (Click to sort Ascending)',
            'USER_MANAGEMENT.USER_NAME (Click to sort Ascending)',
            '',
            '',
        ];

        for (let th = 0; th < ListHeader.length; th++) {
            expect(ListHeader.at(th).text()).toEqual(ListHeaderText[th]);
        }

        const TableBody = wrapper.find('tbody');
        const ListRow = TableBody.findAll('tr');
        expect(ListRow.length).toEqual(4);

        for (let tr = 0; tr < ListRow.length; tr++) {
            const TR = ListRow.at(tr);
            const ListTD = TR.findAll('td');

            expect(ListTD.length).toEqual(6);
            expect(ListTD.at(0).text()).toEqual(DATA[tr].user_role + '');
            expect(ListTD.at(1).text()).toEqual(DATA[tr].base + '');
            expect(ListTD.at(2).text()).toEqual(DATA[tr].user_id + '');
            expect(ListTD.at(3).text()).toEqual(DATA[tr].user_name + '');
            expect(ListTD.at(4).text()).toEqual('BUTTON.EDIT');
            expect(ListTD.at(5).text()).toEqual('BUTTON.DELETE');
        }

        await wrapper.setData({ vItems: [] });

        wrapper.destroy();
    });

    test('Test render User Management Create', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(UserManagementCreate, {
            localVue,
            router,
            store,
        });

        const UserManagementCreateScreen = wrapper.find('.user-management-create');
        expect(UserManagementCreateScreen.exists()).toBe(true);

        const ButtonBack = wrapper.find('#btn-back');
        expect(ButtonBack.exists()).toBe(true);
        expect(ButtonBack.text()).toEqual('BUTTON.BACK');

        const ButtonRegister = wrapper.find('#btn-save');
        expect(ButtonRegister.exists()).toBe(true);
        expect(ButtonRegister.text()).toEqual('BUTTON.REGISTER');

        const backToUserManagementIndex = jest.spyOn(wrapper.vm, 'backToUserManagementIndex');
        await ButtonBack.trigger('click');
        expect(backToUserManagementIndex).toHaveBeenCalled();

        wrapper.destroy();
    });

    test('Test render User Management Edit', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(UserManagementEdit, {
            localVue,
            router,
            store,
        });

        const UserManagementEditScreen = wrapper.find('.user-management-edit');
        expect(UserManagementEditScreen.exists()).toBe(true);

        const ButtonBack = wrapper.find('#btn-back');
        expect(ButtonBack.exists()).toBe(true);
        expect(ButtonBack.text()).toEqual('BUTTON.BACK');

        const ButtonRegister = wrapper.find('#btn-save');
        expect(ButtonRegister.exists()).toBe(true);
        expect(ButtonRegister.text()).toEqual('BUTTON.REGISTER');

        wrapper.destroy();
    });
});

