import store from '@/store';
import router from '@/router';
import { mount, createLocalVue } from '@vue/test-utils';
import TablePeriodicInspection from '@/components/organisms/MaintenanceScheduleResults/Table/TablePeriodicInspectionSchedule';
import TablePeriodicInspectionFilter from '@/components/organisms/MaintenanceScheduleResults/Filter/TablePeriodicInspectionScheduleFilter';
import {
    getListMonth,
    getVehicleInfo,
    formatScheduleResultDate,
    colorInspection,
    getRemark,
} from '@/components/organisms/MaintenanceScheduleResults/Table/helper';

describe('TEST COMPONENT TABLE PERIODIC INSPECTION', () => {
    const Schedule = [
        [
            {
                'vehicle_id': 153,
                'no_number_plate': 'つくば800あ3193',
                'first_register': '2015-7',
                'expiration_date': '2022-7-12',
                'date': '2022-4-1',
                'result': null,
                'color': 1,
                'schedule_remark': '1',
                'result_remark': '1',
            },
            {
                'vehicle_id': 153,
                'no_number_plate': 'つくば800あ3193',
                'first_register': '2015-7',
                'expiration_date': '2022-7-12',
                'date': '2022-7-1',
                'result': null,
                'color': 2,
                'schedule_remark': '1',
                'result_remark': '1',
            },
            {
                'vehicle_id': 153,
                'no_number_plate': 'つくば800あ3193',
                'first_register': '2015-7',
                'expiration_date': '2022-7-12',
                'date': '2022-10-1',
                'result': null,
                'color': 1,
                'schedule_remark': '1',
                'result_remark': '1',
            },
            {
                'vehicle_id': 153,
                'no_number_plate': 'つくば800あ3193',
                'first_register': '2015-7',
                'expiration_date': '2022-7-12',
                'date': '2023-1-1',
                'result': null,
                'color': 1,
                'schedule_remark': '1',
                'result_remark': '1',
            },
        ],
        [
            {
                'vehicle_id': 155,
                'no_number_plate': '足立800い2835',
                'first_register': '2015-7',
                'expiration_date': '2022-7-8',
                'date': '2022-4-2',
                'result': null,
                'color': 1,
                'schedule_remark': '2',
                'result_remark': '2',
            },
            {
                'vehicle_id': 155,
                'no_number_plate': '足立800い2835',
                'first_register': '2015-7',
                'expiration_date': '2022-7-8',
                'date': '2022-7-2',
                'result': null,
                'color': 2,
                'schedule_remark': '2',
                'result_remark': '2',
            },
            {
                'vehicle_id': 155,
                'no_number_plate': '足立800い2835',
                'first_register': '2015-7',
                'expiration_date': '2022-7-8',
                'date': '2022-10-2',
                'result': null,
                'color': 1,
                'schedule_remark': '2',
                'result_remark': '2',
            },
            {
                'vehicle_id': 155,
                'no_number_plate': '足立800い2835',
                'first_register': '2015-7',
                'expiration_date': '2022-7-8',
                'date': '2023-1-2',
                'result': null,
                'color': 1,
                'schedule_remark': '2',
                'result_remark': '2',
            },
        ],
    ];

    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
    };

    const localVue = createLocalVue();
    const wrapper = mount(TablePeriodicInspection, {
        localVue,
        router,
        store,
        mocks,
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Check table is exits', async() => {
        const Table = wrapper.find('table');
        expect(Table.exists()).toBe(true);
    });

    test('Check function getListMonth', async() => {
        expect(getListMonth(Schedule)).toEqual([4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3]);
        expect(getListMonth([])).toEqual([4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3]);
        expect(getListMonth(null)).toEqual([4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3]);
        expect(getListMonth(undefined)).toEqual([4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3]);
        expect(getListMonth('')).toEqual([4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3]);
        expect(getListMonth('Test component')).toEqual([4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3]);
    });

    test('Check function getVehilceInfo', async() => {
        expect(getVehicleInfo(Schedule[0])).toEqual({
            vehicle_id: 153,
            no_number_plate: 'つくば800あ3193',
            expiration_date: '2022年 7月 12日',
        });
        expect(getVehicleInfo(Schedule[1])).toEqual({
            vehicle_id: 155,
            no_number_plate: '足立800い2835',
            expiration_date: '2022年 7月 8日',
        });
        expect(getVehicleInfo([])).toEqual({
            vehicle_id: '',
            no_number_plate: '',
            expiration_date: '',
        });
        expect(getVehicleInfo(null)).toEqual({
            vehicle_id: '',
            no_number_plate: '',
            expiration_date: '',
        });
        expect(getVehicleInfo(undefined)).toEqual({
            vehicle_id: '',
            no_number_plate: '',
            expiration_date: '',
        });
        expect(getVehicleInfo('')).toEqual({
            vehicle_id: '',
            no_number_plate: '',
            expiration_date: '',
        });
        expect(getVehicleInfo('Test component')).toEqual({
            vehicle_id: '',
            no_number_plate: '',
            expiration_date: '',
        });
    });

    test('Check function formatScheduleResultDate', async() => {
        expect(formatScheduleResultDate('2022-12-20')).toEqual('12/20');
        expect(formatScheduleResultDate('2022-1-1')).toEqual('1/1');
        expect(formatScheduleResultDate(null)).toEqual('');
        expect(formatScheduleResultDate(undefined)).toEqual('');
        expect(formatScheduleResultDate('')).toEqual('');
        expect(formatScheduleResultDate('Test component')).toEqual('');
    });

    test('Check function colorInspection', async() => {
        expect(colorInspection(1)).toEqual('months-3');
        expect(colorInspection(2)).toEqual('months-12');
        expect(colorInspection(0)).toEqual('default');
        expect(colorInspection(null)).toEqual('default');
        expect(colorInspection(undefined)).toEqual('default');
        expect(colorInspection('')).toEqual('default');
        expect(colorInspection('Test component')).toEqual('default');
    });

    test('Check function getRemark', async() => {
        expect(getRemark(Schedule[0])).toEqual({
            schedule_remark: '1',
            result_remark: '1',
        });
        expect(getRemark(Schedule[1])).toEqual({
            schedule_remark: '2',
            result_remark: '2',
        });
        expect(getRemark('')).toEqual({
            schedule_remark: '',
            result_remark: '',
        });
        expect(getRemark(null)).toEqual({
            schedule_remark: '',
            result_remark: '',
        });
        expect(getRemark(undefined)).toEqual({
            schedule_remark: '',
            result_remark: '',
        });
        expect(getRemark('Test component')).toEqual({
            schedule_remark: '',
            result_remark: '',
        });
    });

    test('Check render table periodic inspection', async() => {
        await wrapper.setProps({
            items: Schedule,
        });

        const Table = wrapper.find('table');

        expect(Table.exists()).toBe(true);

        const ListHeader = Table.findAll('th');
        expect(ListHeader.length).toEqual(14);

        const lenHeader = ListHeader.length;
        let idxHeader = 0;
        const TextHeader = [
            'MAINTENANCE_SCHEDULE_RESULTS.MONTH  MAINTENANCE_SCHEDULE_RESULTS.VEHICLE_IDENTIFY_NUMBER',
            '4月',
            '5月',
            '6月',
            '7月',
            '8月',
            '9月',
            '10月',
            '11月',
            '12月',
            '1月',
            '2月',
            '3月',
            'MAINTENANCE_SCHEDULE_RESULTS.REMARK',
        ];

        while (idxHeader < lenHeader) {
            expect(ListHeader.at(idxHeader).text()).toEqual(TextHeader[idxHeader]);

            idxHeader++;
        }

        const TableBody = Table.find('tbody');
        const ListTR = TableBody.findAll('tr');
        expect(ListTR.length).toEqual(2);

        expect(TableBody.findAll('span.months-3').length).toEqual(6);
        expect(TableBody.findAll('span.months-12').length).toEqual(2);
    });

    test('Test 0 record, 1 record, multiple records in 1 page, multiple pages(?)', async() => {

    });
});

describe('TEST COMPONENT TABLE PERIODIC INSPECTION FILTER', () => {
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
    };

    const doClearFilter = jest.fn();
    const handleGetAllVehiclePlates = jest.fn();
    const doApply = jest.fn();

    const localVue = createLocalVue();
    const wrapper = mount(TablePeriodicInspectionFilter, {
        localVue,
        router,
        store,
        mocks,
        methods: {
            doClearFilter,
            handleGetAllVehiclePlates,
            doApply,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test Filter Periodic Inspections Table', async() => {
        await wrapper.setData({
            number_plate: null,
            number_plate_options: [],
        });

        // Check if reset all the data and checkbox when click clear all button.

        const ButtonClearAll = wrapper.find('#btn-clear-all');
        expect(ButtonClearAll.exists()).toBe(true);

        await ButtonClearAll.trigger('click');
        expect(doClearFilter).toHaveBeenCalled();

        // Check if get list vehicle plates in the filter.

        await wrapper.vm.handleGetAllVehiclePlates();
        expect(handleGetAllVehiclePlates).toHaveBeenCalled();

        expect(wrapper.vm.number_plate_options).not.toBeNull();

        // Check when click in the button apply filter.

        await wrapper.vm.doApply();
        expect(doApply).toHaveBeenCalled();
    });

    wrapper.destroy();
});
