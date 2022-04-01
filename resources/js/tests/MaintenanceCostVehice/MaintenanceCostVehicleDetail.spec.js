import store from '@/store';
import router from '@/router';
import { mount, createLocalVue } from '@vue/test-utils';
import MaintenanceCostDataDetail from '@/pages/MaintenanceCostData/Detail';
import VehicleDataDetail from '@/pages/MaintenanceCostData/VehicleData';
import MaintenanceDataDetail from '@/pages/MaintenanceCostData/MaintenanceData';

describe('TEST COMPONENT MAINTENANCE COST EDIT', () => {
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
        $toast: {
            warning: jest.fn(),
        },
    };

    const getMaintenanceCostVehicleData = jest.fn();
    const getMaintenanceCostMaintenanceData = jest.fn();
    const backToMaintenanceCostIndex = jest.fn();
    const toEditScreen = jest.fn();

    const localVue = createLocalVue();
    const wrapper = mount(MaintenanceCostDataDetail, {
        localVue,
        router,
        store,
        mocks,
        methods: {
            getMaintenanceCostVehicleData,
            getMaintenanceCostMaintenanceData,
            backToMaintenanceCostIndex,
            toEditScreen,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test component render header', async() => {
        const Header = wrapper.find('.maintenance-cost-vehicle__header-page');
        expect(Header.text()).toEqual('PAGE_TITLE.MAINTENANCE_COST_VEHICLE');
    });

    // test('Test function backToMaintenanceCostIndex', async() => {
    //     const BTN_BACK = wrapper.find('button#btn-back');
    //     expect(BTN_BACK.exists()).toBe(true);
    //     await BTN_BACK.trigger('click');
    //     expect(backToMaintenanceCostIndex).toHaveBeenCalled();
    // });

    // test('Test function toEditScreen', async() => {
    //     const BTN_EDIT = wrapper.find('button#btn-edit');
    //     expect(BTN_EDIT.exists()).toBe(true);
    //     await BTN_EDIT.trigger('click');
    //     expect(toEditScreen).toHaveBeenCalled();
    // });

    test('Test function getMaintenanceCostVehicleData', async() => {
        await wrapper.vm.getMaintenanceCostVehicleData();
        expect(getMaintenanceCostVehicleData).toHaveBeenCalled();
    });

    test('Test function getMaintenanceCostMaintenanceData', async() => {
        await wrapper.vm.getMaintenanceCostMaintenanceData();
        expect(getMaintenanceCostMaintenanceData).toHaveBeenCalled();
    });

    wrapper.destroy();
});

describe('TEST COMPONENT VEHICLE DATA DETAIL', () => {
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
        $toast: {
            warning: jest.fn(),
        },
    };

    const getMaintenanceCostVehicleData = jest.fn();
    const setDataMaintenanceCostVehicle = jest.fn();
    const checkToLockInputs = jest.fn();

    const localVue = createLocalVue();
    const wrapper = mount(VehicleDataDetail, {
        localVue,
        router,
        store,
        mocks,
        methods: {
            getMaintenanceCostVehicleData,
            setDataMaintenanceCostVehicle,
            checkToLockInputs,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test function getMaintenanceCostVehicleData', async() => {
        await wrapper.setData({
            date: '',
            number_plate: '',
            mileage: '',
            type: '',
            first_registration: '',
            leasing_company: '',
            leasing_period: '',
            start_of_leasing: '',
            end_of_leasing: '',
            oil_change: '',
            oil_element: '',
            inspection_expiration_date: '',
            remaining_days: '',
            no_number_plate_history: [],
            mileage_history: [],
        });

        await wrapper.vm.getMaintenanceCostVehicleData();
        expect(getMaintenanceCostVehicleData).toHaveBeenCalled();

        expect(wrapper.vm.date).not.toBeNull();
        expect(wrapper.vm.number_plate).not.toBeNull();
        expect(wrapper.vm.mileage).not.toBeNull();
        expect(wrapper.vm.type).not.toBeNull();
        expect(wrapper.vm.first_registration).not.toBeNull();
        expect(wrapper.vm.leasing_company).not.toBeNull();
        expect(wrapper.vm.leasing_period).not.toBeNull();
        expect(wrapper.vm.start_of_leasing).not.toBeNull();
        expect(wrapper.vm.end_of_leasing).not.toBeNull();
        expect(wrapper.vm.oil_change).not.toBeNull();
        expect(wrapper.vm.oil_element).not.toBeNull();
        expect(wrapper.vm.inspection_expiration_date).not.toBeNull();
        expect(wrapper.vm.remaining_days).not.toBeNull();
        expect(wrapper.vm.no_number_plate_history).not.toBeNull();
        expect(wrapper.vm.mileage_history).not.toBeNull();
    });

    test('Test call function checkToLockInputs', async() => {
        await wrapper.setData({
            number_plate: '',
            mileage: '',
            type: '',
            first_registration: '',
            inspection_expiration_date: '',
            remaining_days: '',
            leasing_company: '',
            leasing_period: '',
            start_of_leasing: '',
            end_of_leasing: '',
            mileage_history: [],
            no_number_plate_history: [],
        });

        await wrapper.vm.checkToLockInputs();
        expect(checkToLockInputs).toHaveBeenCalled();

        expect(wrapper.vm.number_plate).not.toBeNull();
        expect(wrapper.vm.mileage).not.toBeNull();
        expect(wrapper.vm.type).not.toBeNull();
        expect(wrapper.vm.first_registration).not.toBeNull();
        expect(wrapper.vm.inspection_expiration_date).not.toBeNull();
        expect(wrapper.vm.remaining_days).not.toBeNull();
        expect(wrapper.vm.leasing_company).not.toBeNull();
        expect(wrapper.vm.leasing_period).not.toBeNull();
        expect(wrapper.vm.start_of_leasing).not.toBeNull();
        expect(wrapper.vm.end_of_leasing).not.toBeNull();
        expect(wrapper.vm.mileage_history).not.toBeNull();
        expect(wrapper.vm.no_number_plate_history).not.toBeNull();
    });

    test('Test watcher call function itemsVehicleData', async() => {
        await wrapper.setData({
            itemsVehicleData: {
                end_of_leasing: '2022-04-30',
                first_registration: '2012-5',
                garage: '?',
                inspection_expiration_date: '2022-5-26',
                leasing_company: '日本カーソリューションズ㈱',
                leasing_period: '12',
                maintenance_data: {
                    alternator: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    battery: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    body_id: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    camera_monitor: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    gate: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    glass: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    other: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    starter_motor: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    tire: {
                        date: '',
                        name: '',
                    },
                },
                mileage: 500000,
                mileage_history: [
                    {
                        created_at: '2022-02-18 12:53:16',
                        date: '2022-03-31 00:00:00',
                        id: 318,
                        mileage: 500000,
                        updated_at: '2022-02-18 12:53:16',
                        updated_mileage: '',
                        vehicle_id: 1,
                    },
                    {
                        created_at: '2022-02-18 12:53:16',
                        date: '2021-03-31 00:00:00',
                        id: 1,
                        mileage: 362600,
                        updated_at: '2022-02-18 12:53:16',
                        updated_mileage: 137400,
                        vehicle_id: 1,
                    },
                ],
                no_number_plate: '横浜800あ8220',
                plate_history: [
                    {
                        created_at: '2022-02-18 12:53:16',
                        date: '2021-03-31 00:00:00',
                        id: 1,
                        no_number_plate: '横浜800あ8220',
                        updated_at: '2022-02-18 12:53:16',
                        vehicle_id: 1,
                    },
                ],
                remaining_days: 91,
                remark_01: 'Mèo Remark 01',
                remark_02: 'Mèo Remark 02',
                start_of_leasing: '2021-05-01',
                tel: '?',
                type: 'SKG-NPR85AN',
                vehicle_id: 1,
            },
        });

        expect(setDataMaintenanceCostVehicle).toHaveBeenCalled();
    });
});

describe('TEST COMPONENT MAINTENANCE DATA', () => {
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
        $toast: {
            wrapper: jest.fn(),
        },
    };

    const checkToLockInputs = jest.fn();
    const setDataMaintenanceSchedule = jest.fn();
    const setDataMaintenanceCostData = jest.fn();
    const setDefaultYears = jest.fn();
    const getPrevYears = jest.fn();
    const getNextYears = jest.fn();
    const emitDataToUpdate = jest.fn();
    const handleStatus = jest.fn();

    const localVue = createLocalVue();
    const wrapper = mount(MaintenanceDataDetail, {
        localVue,
        router,
        store,
        mocks,
        methods: {
            checkToLockInputs,
            setDataMaintenanceSchedule,
            setDataMaintenanceCostData,
            setDefaultYears,
            getPrevYears,
            getNextYears,
            emitDataToUpdate,
            handleStatus,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test call function checkToLockInputs', async() => {
        await wrapper.vm.checkToLockInputs();
        expect(checkToLockInputs).toHaveBeenCalled();
    });

    test('Test function setDataMaintenanceSchedule', async() => {
        await wrapper.setData({
            itemsSchedule: [
                [
                    [
                        {
                            content: 'complete',
                            month: '2021-05-01',
                        },
                        {
                            content: 'complete',
                            month: '2021-05-01',
                        },
                    ],
                    [
                        {
                            content: [
                                {
                                    color: 3,
                                    text: 'ミッション・デフオイル',

                                },
                                {
                                    color: 3,
                                    text: 'ブレーキ/クラッチフルード',

                                },
                                {
                                    color: 3,
                                    text: '差圧パイプ',

                                },
                                {
                                    color: 3,
                                    text: 'ワイヤレスキー電池',

                                },
                                {
                                    color: 3,
                                    text: 'キャビン灯',

                                },
                                {
                                    color: 3,
                                    text: 'ワイパーゴム',

                                },
                            ],
                            month: '2022-05-01',
                        },
                    ],
                    [
                        {
                            content: [
                                {
                                    color: 3,
                                    text: 'ミッション・デフオイル',

                                },
                                {
                                    color: 3,
                                    text: 'ブレーキ/クラッチフルード',

                                },
                                {
                                    color: 3,
                                    text: '差圧パイプ',

                                },
                                {
                                    color: 3,
                                    text: 'ワイヤレスキー電池',
                                },
                                {
                                    color: 3,
                                    text: 'キャビン灯',
                                },
                                {
                                    color: 3,
                                    text: 'ワイパーゴム',

                                },
                            ],
                            month: '2023-05-01',
                        },
                    ],
                ],
            ],
        });
        await wrapper.vm.setDataMaintenanceSchedule();
        expect(setDataMaintenanceSchedule).toHaveBeenCalled();
    });

    test('Test function setDataMaintenanceCostData', async() => {
        await wrapper.setData({
            itemsMaintenanceData: {
                end_of_leasing: '2022-04-30',
                first_registration: '2012-5',
                garage: '?',
                inspection_expiration_date: '2022-5-26',
                leasing_company: '日本カーソリューションズ㈱',
                leasing_period: '12',
                maintenance_data: {
                    alternator: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    battery: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    body_id: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    camera_monitor: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    gate: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    glass: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    other: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    starter_motor: {
                        date: '2022-01-01',
                        name: 'Meo',
                    },
                    tire: {
                        date: '',
                        name: '',
                    },
                },
                mileage: 500000,
                mileage_history: [
                    {
                        created_at: '2022-02-18 12:53:16',
                        date: '2022-03-31 00:00:00',
                        id: 318,
                        mileage: 500000,
                        updated_at: '2022-02-18 12:53:16',
                        updated_mileage: '',
                        vehicle_id: 1,
                    },
                    {
                        created_at: '2022-02-18 12:53:16',
                        date: '2021-03-31 00:00:00',
                        id: 1,
                        mileage: 362600,
                        updated_at: '2022-02-18 12:53:16',
                        updated_mileage: 137400,
                        vehicle_id: 1,
                    },
                ],
                no_number_plate: '横浜800あ8220',
                plate_history: [
                    {
                        created_at: '2022-02-18 12:53:16',
                        date: '2021-03-31 00:00:00',
                        id: 1,
                        no_number_plate: '横浜800あ8220',
                        updated_at: '2022-02-18 12:53:16',
                        vehicle_id: 1,
                    },
                ],
                remaining_days: 91,
                remark_01: 'Mèo Remark 01',
                remark_02: 'Mèo Remark 02',
                start_of_leasing: '2021-05-01',
                tel: '?',
                type: 'SKG-NPR85AN',
                vehicle_id: 1,
            },
        });
        await wrapper.vm.setDataMaintenanceCostData();
        expect(setDataMaintenanceCostData).toHaveBeenCalled();
    });

    test('Test call function setDefaultYears', () => {
        wrapper.vm.setDefaultYears();
        expect(setDefaultYears).toHaveBeenCalled();
    });

    test('Test call function emitDataToUpdate', () => {
        wrapper.vm.emitDataToUpdate();
        expect(emitDataToUpdate).toHaveBeenCalled();
    });

    test('Test call function getPrevYears', async() => {
        const BTN_PREV = wrapper.find('#get-prev-year');
        expect(BTN_PREV.exists()).toBe(true);

        await BTN_PREV.trigger('click');
        expect(getPrevYears).toHaveBeenCalled();
    });

    test('Test call function getNextYears', async() => {
        const BTN_NEXT = wrapper.find('#get-next-year');
        expect(BTN_NEXT.exists()).toBe(true);

        await BTN_NEXT.trigger('click');
        expect(getNextYears).toHaveBeenCalled();
    });

    test('Test call function handleStatus', async() => {
        await wrapper.vm.handleStatus();
        expect(handleStatus).toHaveBeenCalled();
    });

    wrapper.destroy();
});
