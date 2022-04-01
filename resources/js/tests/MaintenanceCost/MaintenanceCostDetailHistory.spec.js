import store from '@/store';
import router from '@/router';
import { mount, createLocalVue } from '@vue/test-utils';
import VehicleData from '@/pages/MaintenanceCostData/VehicleData';

describe('TEST COMPONENT MAINTENANCE COST DETAIL HISTORY', () => {
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
    };

    const localVue = createLocalVue();
    const wrapper = mount(VehicleData, {
        localVue,
        router,
        store,
        mocks,
        stubs: {
            BModal: true,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test component render modal History Vehicle', async() => {
        const ButtonShowModal = wrapper.find('span.modal-history');
        expect(ButtonShowModal.exists()).toBe(true);

        await ButtonShowModal.trigger('click');

        const ModalHistory = wrapper.find('#modal-history');
        expect(ModalHistory.exists()).toBe(true);

        const TableHistory = ModalHistory.find('table');
        expect(TableHistory.exists()).toBe(true);
    });

    test('Test component render data table History Vehicle', async() => {
        const ButtonShowModal = wrapper.find('span.modal-history');
        expect(ButtonShowModal.exists()).toBe(true);

        await ButtonShowModal.trigger('click');

        const ModalHistory = wrapper.find('#modal-history');
        expect(ModalHistory.exists()).toBe(true);

        const TableHistory = ModalHistory.find('table');
        const TableHistoryHeader = TableHistory.find('thead');
        expect(TableHistoryHeader.exists()).toBe(true);
        const ListHeaderTableHistory = TableHistoryHeader.findAll('th');
        const TableHistoryHeaderText = [
            '#',
            'MAINTENANCE_DATA.UPDATE_DATE',
            'MAINTENANCE_DATA.NO_NUMBER_PLATE',
        ];
        for (let index = 0; index < ListHeaderTableHistory.length; index++) {
            expect(ListHeaderTableHistory.at(index).text()).toEqual(TableHistoryHeaderText[index]);
        }

        expect(TableHistory.find('tbody').exists()).toBe(false);

        await wrapper.setData({
            no_number_plate_history: [
                {
                    vehicle_id: 'vehicle_id',
                    date: 'date',
                    no_number_plate: 'no_number_plate',
                },
            ],
        });

        expect(TableHistory.find('tbody').findAll('tr').length).toEqual(1);
    });

    test('Test component render modal History Mileage', async() => {
        const ButtonShowModal = wrapper.find('span.modal-mileage-history');
        expect(ButtonShowModal.exists()).toBe(true);

        await ButtonShowModal.trigger('click');

        const ModalHistory = wrapper.find('#modal-mileage-history');
        expect(ModalHistory.exists()).toBe(true);

        const TableHistory = ModalHistory.find('table');
        expect(TableHistory.exists()).toBe(true);
    });

    test('Test component render data table History Mileage', async() => {
        const ButtonShowModal = wrapper.find('span.modal-mileage-history');
        expect(ButtonShowModal.exists()).toBe(true);

        await ButtonShowModal.trigger('click');

        const ModalHistory = wrapper.find('#modal-mileage-history');
        expect(ModalHistory.exists()).toBe(true);

        const TableHistory = ModalHistory.find('table');
        const TableHistoryHeader = TableHistory.find('thead');
        expect(TableHistoryHeader.exists()).toBe(true);
        const ListHeaderTableHistory = TableHistoryHeader.findAll('th');
        const TableHistoryHeaderText = [
            '#',
            'MAINTENANCE_DATA.UPDATE_DATE',
            'MAINTENANCE_DATA.MILEAGE',
            'MAINTENANCE_DATA.UPDATED_MILEAGE',
        ];
        for (let index = 0; index < ListHeaderTableHistory.length; index++) {
            expect(ListHeaderTableHistory.at(index).text()).toEqual(TableHistoryHeaderText[index]);
        }

        expect(TableHistory.find('tbody').exists()).toBe(false);

        await wrapper.setData({
            mileage_history: [
                {
                    vehicle_id: 'vehicle_id',
                    date: 'date',
                    mileage: 'mileage',
                    updated_mileage: 'updated_mileage',
                },
            ],
        });

        expect(TableHistory.find('tbody').findAll('tr').length).toEqual(1);
    });
});
