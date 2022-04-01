import { mount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import router from '@/router';
import AccessoryManagementIndex from '@/pages/AccessoryManagement/Index';
import AccessoryManagementCreate from '@/pages/AccessoryManagement/Create';
import AccessoryManagementEdit from '@/pages/AccessoryManagement/Edit';

describe('TEST COMPONENT ACCESSORY MANAGEMENT', () => {
    test('Test render Accessory Management Index', () => {
        const localVue = createLocalVue();
        const wrapper = mount(AccessoryManagementIndex, {
            localVue,
            router,
            store,
        });

        const AccessoryManagementIndexScreen = wrapper.find('.accessory-management');
        expect(AccessoryManagementIndexScreen.exists()).toBe(true);

        wrapper.destroy();
    });

    test('Test render Table Accessory Management', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(AccessoryManagementIndex, {
            localVue,
            router,
            store,
        });

        const DATA = [
            {
                'id': 1,
                'accessory_name': 'Tire',
                'tonnage': 3,
                'passed_years': 1,
                'mileage': '10,000',
            },
            {
                'id': 2,
                'accessory_name': 'Door',
                'tonnage': 10,
                'passed_years': 2,
                'mileage': '30,000',
            },
            {
                'id': 3,
                'accessory_name': 'Engine',
                'tonnage': 2,
                'passed_years': 1,
                'mileage': '20,000',
            },
            {
                'id': 4,
                'accessory_name': 'Radar',
                'tonnage': 5,
                'passed_years': 1,
                'mileage': '50,000',
            },
            {
                'id': 5,
                'accessory_name': 'Headlights',
                'tonnage': 7,
                'passed_years': 3,
                'mileage': '60,000',
            },
        ];

        const TableAccessoryManagement = wrapper.find('#table-accessory-management');
        expect(TableAccessoryManagement.exists()).toBe(true);

        await wrapper.setData({ vItems: DATA });

        const TableHeader = wrapper.find('thead');
        const ListHeader = TableHeader.findAll('th');
        expect(ListHeader.length).toEqual(6);

        const ListHeaderText = [
            'ACCESSORY_MANAGEMENT.ACCESSORY_NAME',
            'ACCESSORY_MANAGEMENT.TONNAGE (Click to sort Ascending)',
            'ACCESSORY_MANAGEMENT.PASSED_YEARS (Click to sort Ascending)',
            'ACCESSORY_MANAGEMENT.MILEAGE (Click to sort Ascending)',
            '',
            '',
        ];

        for (let th = 0; th < ListHeader.length; th++) {
            expect(ListHeader.at(th).text()).toEqual(ListHeaderText[th]);
        }

        const TableBody = wrapper.find('tbody');
        const ListRow = TableBody.findAll('tr');
        expect(ListRow.length).toEqual(5);

        for (let tr = 0; tr < ListRow.length; tr++) {
            const TR = ListRow.at(tr);
            const ListTD = TR.findAll('td');

            expect(ListTD.length).toEqual(6);
            expect(ListTD.at(0).text()).toEqual(DATA[tr].accessory_name + '');
            expect(ListTD.at(1).text()).toEqual(DATA[tr].tonnage + '');
            expect(ListTD.at(2).text()).toEqual(DATA[tr].passed_years + '');
            expect(ListTD.at(3).text()).toEqual(DATA[tr].mileage + '');
            expect(ListTD.at(4).text()).toEqual('BUTTON.EDIT');
            expect(ListTD.at(5).text()).toEqual('BUTTON.DELETE');
        }

        await wrapper.setData({ vItems: [] });

        wrapper.destroy();
    });

    test('Test render Accessory Management Create', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(AccessoryManagementCreate, {
            localVue,
            router,
            store,
        });

        const AccessoryManagementCreateScreen = wrapper.find('.accessory-management-create');
        expect(AccessoryManagementCreateScreen.exists()).toBe(true);

        const ButtonBack = wrapper.find('#btn-back');
        expect(ButtonBack.exists()).toBe(true);
        expect(ButtonBack.text()).toEqual('BUTTON.BACK');

        const ButtonRegister = wrapper.find('#btn-save');
        expect(ButtonRegister.exists()).toBe(true);
        expect(ButtonRegister.text()).toEqual('BUTTON.REGISTER');

        const backToAccessoryIndex = jest.spyOn(wrapper.vm, 'backToAccessoryIndex');
        await ButtonBack.trigger('click');
        expect(backToAccessoryIndex).toHaveBeenCalled();

        // const validation = jest.spyOn(wrapper.vm, 'validation');
        // await ButtonRegister.trigger('click');
        // expect(validation).toHaveBeenCalled();

        wrapper.destroy();
    });

    test('Test render Accessory Management Edit', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(AccessoryManagementEdit, {
            localVue,
            router,
            store,
        });

        const AccessoryManagementEditScreen = wrapper.find('.accessory-management-edit');
        expect(AccessoryManagementEditScreen.exists()).toBe(true);

        const ButtonBack = wrapper.find('#btn-back');
        expect(ButtonBack.exists()).toBe(true);
        expect(ButtonBack.text()).toEqual('BUTTON.BACK');

        const ButtonRegister = wrapper.find('#btn-save');
        expect(ButtonRegister.exists()).toBe(true);
        expect(ButtonRegister.text()).toEqual('BUTTON.REGISTER');

        wrapper.destroy();
    });
});

