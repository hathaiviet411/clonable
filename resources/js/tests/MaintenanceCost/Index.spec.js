import store from '@/store';
import router from '@/router';
import { mount, createLocalVue } from '@vue/test-utils';
import MaintenanceCostFilter from '@/components/organisms/MaintenanceCost/Filter/MaintenanceCostFilter';
import MaintenanceCostTemplate from '@/components/template/MaintenanceCost';
import MaintenanceCostTable from '@/components/organisms/MaintenanceCost/Table/TableMaintenanceCost';

describe('TEST COMPONENT MAINTENANCE COST LIST', () => {
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
    };

    const doClearFilter = jest.fn();
    const doApply = jest.fn();
    const getListNoNumberPlate = jest.fn();
    const getListStatusAndType = jest.fn();
    const getListGarage = jest.fn();
    const toRegisterScreen = jest.fn();
    const goToDetail = jest.fn();

    const localVue = createLocalVue();

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test component render Filter', async() => {
        const wrapper = mount(MaintenanceCostFilter, {
            localVue,
            router,
            store,
            mocks,
            methods: {
                doClearFilter,
                doApply,
                getListNoNumberPlate,
                getListStatusAndType,
                getListGarage,
            },
        });

        expect(getListNoNumberPlate).toHaveBeenCalled();
        expect(getListStatusAndType).toHaveBeenCalled();
        expect(getListGarage).toHaveBeenCalled();

        const ZoneFilter = wrapper.find('.organisms-maintenance-cost-filter');
        expect(ZoneFilter.exists()).toBe(true);

        const ButtonClear = ZoneFilter.find('span.text-clear-all');
        expect(ButtonClear.text()).toEqual('BUTTON.CLEAR_ALL');
        await ButtonClear.trigger('click');
        expect(doClearFilter).toHaveBeenCalled();

        const ButtonApply = ZoneFilter.find('button.btn-summit-filter');
        expect(ButtonApply.exists()).toBe(true);
        expect(ButtonApply.text()).toEqual('BUTTON.APPLY');
        await ButtonApply.trigger('click');
        expect(doApply).toHaveBeenCalled();
    });

    test('Test redirect to page register', async() => {
        const wrapper = mount(MaintenanceCostTemplate, {
            localVue,
            router,
            store,
            mocks,
            methods: {
                toRegisterScreen,
            },
        });

        const ButtonRegister = wrapper.find('.maintenance-btn-default');
        expect(ButtonRegister.exists()).toBe(true);
        expect(ButtonRegister.text()).toEqual('BUTTON.REGISTER');
        await ButtonRegister.trigger('click');
        expect(toRegisterScreen).toHaveBeenCalled();
    });

    test('Test table Maintenance Cost List', async() => {
        const wrapper = mount(MaintenanceCostTable, {
            localVue,
            router,
            store,
            mocks,
            methods: {
                goToDetail,
            },
        });

        const Fields = [
            { key: 'no_number_plate', sortable: true, label: 'MAINTENANCE_COST.NUMBER_PLATE', tdClass: 'text-center', thClass: 'text-center' },
            { key: 'garage', sortable: true, label: 'MAINTENANCE_COST.GARAGE', tdClass: 'text-center', thClass: 'text-center' },
            { key: 'type', sortable: true, label: 'MAINTENANCE_COST.MAINTENANCE_TYPE', tdClass: 'text-center', thClass: 'text-center' },
            { key: 'scheduled_date', sortable: true, label: 'MAINTENANCE_COST.MAINTENANCE_SCHEDULED_DATE', tdClass: 'text-center', thClass: 'text-center' },
            { key: 'maintained_date', sortable: true, label: 'MAINTENANCE_COST.MAINTENANCE_DATE', tdClass: 'text-center', thClass: 'text-center' },
            { key: 'status', sortable: true, label: 'MAINTENANCE_COST.STATUS', tdClass: 'text-center', thClass: 'text-center' },
            { key: 'detail', sortable: false, label: '', tdClass: 'text-center', thClass: 'text-center' },
        ];
        const Items = [
            {
                'id': 2029,
                'vehicle_id': 57,
                'status': 1,
                'type': 1,
                'department_name': '東京',
                'scheduled_date': '2022-03-03',
                'maintained_date': null,
                'no_number_plate': '葛飾800あ86',
                'garage': '?',
                'status_text': '未入力',
                'type_text': '3ヶ月点検',
            },
        ];

        await wrapper.setProps({
            fields: Fields,
        });

        const Table = wrapper.find('table');
        expect(Table.exists()).toBe(true);

        const ListTextHeader = [
            'MAINTENANCE_COST.NUMBER_PLATE (Click to sort Ascending)',
            'MAINTENANCE_COST.GARAGE (Click to sort Ascending)',
            'MAINTENANCE_COST.MAINTENANCE_TYPE (Click to sort Ascending)',
            'MAINTENANCE_COST.MAINTENANCE_SCHEDULED_DATE (Click to sort Ascending)',
            'MAINTENANCE_COST.MAINTENANCE_DATE (Click to sort Ascending)',
            'MAINTENANCE_COST.STATUS (Click to sort Ascending)',
            '',
        ];
        const TableHeader = wrapper.find('thead');
        expect(TableHeader.exists()).toBe(true);
        const ListTh = TableHeader.findAll('th');
        const lenTh = ListTh.length;
        expect(lenTh).toEqual(7);
        let idxTh = 0;
        while (idxTh < lenTh) {
            expect(ListTh.at(idxTh).text()).toEqual(ListTextHeader[idxTh]);

            idxTh++;
        }

        await ListTh.at(0).trigger('click');
        expect(wrapper.vm.$bus.emit).toHaveBeenCalledWith(
            'sendSortTableMaintenanceCost',
            {
                order_column: 'no_number_plate',
                order_type: 'desc',
            }
        );
        await ListTh.at(1).trigger('click');
        expect(wrapper.vm.$bus.emit).toHaveBeenCalledWith(
            'sendSortTableMaintenanceCost',
            {
                order_column: 'garage',
                order_type: 'desc',
            }
        );
        await ListTh.at(2).trigger('click');
        expect(wrapper.vm.$bus.emit).toHaveBeenCalledWith(
            'sendSortTableMaintenanceCost',
            {
                order_column: 'type',
                order_type: 'desc',
            }
        );
        await ListTh.at(3).trigger('click');
        expect(wrapper.vm.$bus.emit).toHaveBeenCalledWith(
            'sendSortTableMaintenanceCost',
            {
                order_column: 'scheduled_date',
                order_type: 'desc',
            }
        );
        await ListTh.at(4).trigger('click');
        expect(wrapper.vm.$bus.emit).toHaveBeenCalledWith(
            'sendSortTableMaintenanceCost',
            {
                order_column: 'maintained_date',
                order_type: 'desc',
            }
        );
        await ListTh.at(5).trigger('click');
        expect(wrapper.vm.$bus.emit).toHaveBeenCalledWith(
            'sendSortTableMaintenanceCost',
            {
                order_column: 'status',
                order_type: 'desc',
            }
        );

        const TbodyEmpty = wrapper.find('tbody');
        expect(TbodyEmpty.find('tr').text()).toEqual('TABLE.EMPTY');

        await wrapper.setProps({
            items: Items,
        });

        const TbodyData = wrapper.find('tbody');
        const ListTr = TbodyData.findAll('tr');
        const lenTr = ListTr.length;
        expect(lenTr).toEqual(1);

        const ListTd = ListTr.at(0).findAll('td');
        expect(ListTd.at(0).text()).toEqual('葛飾800あ86');
        expect(ListTd.at(1).text()).toEqual('?');
        expect(ListTd.at(2).text()).toEqual('MAINTENANCE_COST.THREE_MONTH_INSPECTIONS');
        expect(ListTd.at(3).text()).toEqual('2022-03-03');
        expect(ListTd.at(4).text()).toEqual('');
        expect(ListTd.at(5).text()).toEqual('MAINTENANCE_COST.NOT_ENTERED');
        expect(ListTd.at(6).text()).toEqual('BUTTON.DETAIL');

        const TdDetail = ListTd.at(6).find('span.btn-function');
        expect(TdDetail.text()).toEqual('BUTTON.DETAIL');
        await TdDetail.trigger('click');
        expect(goToDetail).toHaveBeenCalled();
    });
});
