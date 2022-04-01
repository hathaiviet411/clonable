import { mount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import router from '@/router';
import MaintenanceCostIndex from '@/pages/MaintenanceCost/Index';
import MaintenanceCostCreate from '@/pages/MaintenanceCost/Create';
import MaintenanceCostEdit from '@/pages/MaintenanceCost/Edit';
import MaintenanceCostDetail from '@/pages/MaintenanceCost/Detail';

describe('TEST COMPONENT Maintenance Cost', () => {
    test('Test render Maintenance Cost Index', () => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceCostIndex, {
            localVue,
            router,
            store,
        });

        const MaintenanceCostIndexScreen = wrapper.find('.maintenance-cost');
        expect(MaintenanceCostIndexScreen.exists()).toBe(true);

        wrapper.destroy();
    });

    test('Test render Table Maintenance Cost', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceCostIndex, {
            localVue,
            router,
            store,
        });

        const DATA = [
            {
                'id': 1,
                'maintenance_date': '2021/10/10',
                'number_plate': 'NPR00-00000000',
                'garage': 'A工場',
            },
            {
                'id': 2,
                'maintenance_date': '2021/10/09',
                'number_plate': 'NPR00-11111111',
                'garage': 'B工場',
            },
            {
                'id': 3,
                'maintenance_date': '2021/10/08',
                'number_plate': 'NPR00-22222222',
                'garage': 'C工場',
            },
            {
                'id': 4,
                'maintenance_date': '2021/10/07',
                'number_plate': 'NPR00-33333333',
                'garage': 'D工場',
            },
            {
                'id': 5,
                'maintenance_date': '2021/10/08',
                'number_plate': 'NPR00-44444444',
                'garage': 'E工場',
            },
        ];

        const TableMaintenanceCost = wrapper.find('#table-maintenance-cost');
        expect(TableMaintenanceCost.exists()).toBe(true);

        await wrapper.setData({ vItems: DATA });

        const TableHeader = wrapper.find('thead');
        const ListHeader = TableHeader.findAll('th');
        expect(ListHeader.length).toEqual(5);

        const ListHeaderText = [
            'MAINTENANCE_COST.MAINTENANCE_DATE (Click to sort Ascending)',
            'MAINTENANCE_COST.NUMBER_PLATE (Click to sort Ascending)',
            'MAINTENANCE_COST.GARAGE (Click to sort Ascending)',
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

            expect(ListTD.length).toEqual(5);
            expect(ListTD.at(0).text()).toEqual(DATA[tr].maintenance_date + '');
            expect(ListTD.at(1).text()).toEqual(DATA[tr].number_plate + '');
            expect(ListTD.at(2).text()).toEqual(DATA[tr].garage + '');
            expect(ListTD.at(3).text()).toEqual('BUTTON.DETAIL');
            expect(ListTD.at(4).text()).toEqual('BUTTON.DELETE');
        }

        await wrapper.setData({ vItems: [] });

        wrapper.destroy();
    });

    test('Test render Maintenance Cost Create', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceCostCreate, {
            localVue,
            router,
            store,
        });

        const MaintenanceCostCreateScreen = wrapper.find('.maintenance-cost-create');
        expect(MaintenanceCostCreateScreen.exists()).toBe(true);

        const ButtonBack = wrapper.find('#btn-back');
        expect(ButtonBack.exists()).toBe(true);
        expect(ButtonBack.text()).toEqual('BUTTON.BACK');

        const ButtonRegister = wrapper.find('#btn-save');
        expect(ButtonRegister.exists()).toBe(true);
        expect(ButtonRegister.text()).toEqual('BUTTON.SAVE');

        const backToMaintenanceCostIndex = jest.spyOn(wrapper.vm, 'backToMaintenanceCostIndex');
        await ButtonBack.trigger('click');
        expect(backToMaintenanceCostIndex).toHaveBeenCalled();

        // const validation = jest.spyOn(wrapper.vm, 'validation');
        // await ButtonRegister.trigger('click');
        // expect(validation).toHaveBeenCalled();

        wrapper.destroy();
    });

    test('Test render Maintenance Cost Edit', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceCostEdit, {
            localVue,
            router,
            store,
        });

        const MaintenanceDataEditScreen = wrapper.find('.maintenance-cost-edit');
        expect(MaintenanceDataEditScreen.exists()).toBe(true);

        const ButtonBack = wrapper.find('#btn-back');
        expect(ButtonBack.exists()).toBe(true);
        expect(ButtonBack.text()).toEqual('BUTTON.BACK');

        const ButtonRegister = wrapper.find('#btn-save');
        expect(ButtonRegister.exists()).toBe(true);
        expect(ButtonRegister.text()).toEqual('BUTTON.SAVE');

        wrapper.destroy();
    });

    test('Test render Maintenance Cost Detail', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceCostDetail, {
            localVue,
            router,
            store,
        });

        const MaintenanceCostDetailScreen = wrapper.find('.maintenance-cost-detail');
        expect(MaintenanceCostDetailScreen.exists()).toBe(true);

        const ButtonBack = wrapper.find('#btn-back');
        expect(ButtonBack.exists()).toBe(true);
        expect(ButtonBack.text()).toEqual('BUTTON.BACK');

        const ButtonSave = wrapper.find('#btn-edit');
        expect(ButtonSave.exists()).toBe(true);
        expect(ButtonSave.text()).toEqual('BUTTON.EDIT');

        wrapper.destroy();
    });
});

