import store from '@/store';
import router from '@/router';
import { mount, createLocalVue } from '@vue/test-utils';
import AccessoryScheduleDetail from '@/pages/MaintenanceCost/Detail/AccessoryScheduleDetail';

describe('TEST COMPONENT ACCESSORY SCHEDULE DETAIL', () => {
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

    const getMaintenanceDetail = jest.fn();
    const backToMaintenanceCostIndex = jest.fn();
    const toEditScreen = jest.fn();

    const localVue = createLocalVue();
    const wrapper = mount(AccessoryScheduleDetail, {
        localVue,
        router,
        store,
        mocks,
        methods: {
            getMaintenanceDetail,
            backToMaintenanceCostIndex,
            toEditScreen,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test component render header', async() => {
        const Header = wrapper.find('.maintenance-cost__header-page');
        expect(Header.text()).toEqual('PAGE_TITLE.MAINTENANCE_COST');
    });

    test('Test function backToMaintenanceCostIndex', async() => {
        const BTN_BACK = wrapper.find('button#btn-back');
        expect(BTN_BACK.exists()).toBe(true);
        await BTN_BACK.trigger('click');
        expect(backToMaintenanceCostIndex).toHaveBeenCalled();
    });

    test('Test function toEditScreen', async() => {
        const BTN_EDIT = wrapper.find('button#btn-edit');
        expect(BTN_EDIT.exists()).toBe(true);
        await BTN_EDIT.trigger('click');
        expect(toEditScreen).toHaveBeenCalled();
    });

    test('Test function getMaintenanceDetail', async() => {
        await wrapper.setData({
            maintenance_charge: '',
            maintenance_date: '',
            base: '',
            no_number_plate: '',
            garage: '',
            mileage_last_time: '',
            total_amount_excluding_tax: '',
            total_amount_including_tax: '',
            ListAccessories: [],
            ListWages: [],
            replacement_history: [],
            discount: '',
            note: '',
        });

        await wrapper.vm.getMaintenanceDetail();
        expect(getMaintenanceDetail).toHaveBeenCalled();

        expect(wrapper.vm.maintenance_charge).not.toBeNull();
        expect(wrapper.vm.maintenance_date).not.toBeNull();
        expect(wrapper.vm.base).not.toBeNull();
        expect(wrapper.vm.no_number_plate).not.toBeNull();
        expect(wrapper.vm.garage).not.toBeNull();
        expect(wrapper.vm.mileage_last_time).not.toBeNull();
        expect(wrapper.vm.replacement_history).not.toBeNull();
        expect(wrapper.vm.discount).not.toBeNull();
        expect(wrapper.vm.total_amount_excluding_tax).not.toBeNull();
        expect(wrapper.vm.total_amount_including_tax).not.toBeNull();
        expect(wrapper.vm.ListAccessories).not.toBeNull();
        expect(wrapper.vm.ListWages).not.toBeNull();
        expect(wrapper.vm.note).not.toBeNull();
    });
});
