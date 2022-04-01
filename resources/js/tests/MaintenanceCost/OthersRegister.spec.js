import store from '@/store';
import router from '@/router';
import { mount, createLocalVue } from '@vue/test-utils';
import OthersRegister from '@/pages/MaintenanceCost/Create';

describe('TEST COMPONENT OTHERS REGISTER', () => {
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

    const handleGetListVehiclePlates = jest.fn();
    const handleGetListAccessory = jest.fn();
    const calculateTotalAmountET = jest.fn();
    const calculateTotalAmountIT = jest.fn();
    const handleDisableListAccessory = jest.fn();
    const handleGetVehicleInfoByPlate = jest.fn();
    const createMaintenanceCostData = jest.fn();

    const localVue = createLocalVue();
    const wrapper = mount(OthersRegister, {
        localVue,
        router,
        store,
        mocks,
        methods: {
            handleGetListVehiclePlates,
            handleGetListAccessory,
            calculateTotalAmountET,
            calculateTotalAmountIT,
            handleDisableListAccessory,
            handleGetVehicleInfoByPlate,
            createMaintenanceCostData,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test component init data', async() => {
        expect(handleGetListVehiclePlates).toHaveBeenCalled();
        expect(handleGetListAccessory).toHaveBeenCalled();
    });

    test('Test component render header', async() => {
        const Header = wrapper.find('.maintenance-cost__header-page');
        expect(Header.text()).toEqual('PAGE_TITLE.MAINTENANCE_COST');
    });

    test('Test watch', async() => {
        await wrapper.setData({
            ListAccessories: [
                {
                    selected: 1,
                    text: 'test 1',
                    quantity: 12,
                    price: 12,
                    amount: 144,
                },
            ],
        });

        expect(calculateTotalAmountET).toHaveBeenCalled();
        expect(calculateTotalAmountIT).toHaveBeenCalled();
        expect(handleDisableListAccessory).toHaveBeenCalled();

        await wrapper.setData({
            ListWages: [
                {
                    wage: 'Test Wage',
                    amount: 1,
                },
            ],
        });

        expect(calculateTotalAmountET).toHaveBeenCalled();
        expect(calculateTotalAmountIT).toHaveBeenCalled();

        await wrapper.setData({
            discount: 1,
        });

        expect(calculateTotalAmountIT).toHaveBeenCalled();

        await wrapper.setData({
            no_number_plate: 1,
        });

        expect(handleGetVehicleInfoByPlate).toHaveBeenCalled();
    });

    test('Test function add new Accessories', async() => {
        await wrapper.setData({
            ListAccessories: [],
        });

        await wrapper.setData({
            accessories_options: [
                {
                    value: 1,
                    text: 'Test 1',
                },
                {
                    value: -1,
                    text: 'Other',
                },
            ],
        });

        await wrapper.vm.addNewAccessories(-1, 1, 1, 'New Accessories');

        expect(wrapper.vm.ListAccessories).toEqual(
            [
                {
                    selected: -1,
                    text: 'New Accessories',
                    quantity: 1,
                    price: 1,
                    amount: 1,
                },
            ]
        );
    });

    test('Test function addNewWage', async() => {
        await wrapper.setData({
            ListWages: [],
        });

        await wrapper.vm.addNewWage('Test Wage', '1');

        expect(wrapper.vm.ListWages).toEqual(
            [
                {
                    wage: 'Test Wage',
                    amount: '1',
                },
            ]
        );
    });

    test('Test validate Maintenance Charge', async() => {
        const BTN_REGISTER = wrapper.find('button#btn-register');
        expect(BTN_REGISTER.exists()).toBe(true);
        await BTN_REGISTER.trigger('click');

        expect(wrapper.vm.$toast.warning).toHaveBeenCalledWith({
            content: 'TOAST.CONTENT.MAINTENANCE_COST.REQUIRED_MAINTENANCE_CHARGE',
        });
    });

    test('Test validate Maintenance Date', async() => {
        const BTN_REGISTER = wrapper.find('button#btn-register');
        expect(BTN_REGISTER.exists()).toBe(true);

        const radioInput = wrapper.find('input[type="radio"]');
        expect(radioInput.exists()).toBe(true);
        await radioInput.setChecked();
        expect(radioInput.element.checked).toBeTruthy();

        await BTN_REGISTER.trigger('click');

        expect(wrapper.vm.$toast.warning).toHaveBeenCalledWith({
            content: 'TOAST.CONTENT.MAINTENANCE_COST.REQUIRED_MAINTENANCE_DATE',
        });
    });

    test('Test function backToMaintenanceCostIndex', async() => {
        await wrapper.vm.backToMaintenanceCostIndex();

        expect(window.location.hash).toEqual('#/maintenance-cost');
    });
});
