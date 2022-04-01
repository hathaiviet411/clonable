import { mount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import router from '@/router';
import AccessoryCreate from '@/pages/AccessoryManagement/Create';

describe('TEST PAGE ACCESSORY CREATE', () => {
    const backToAccessoryIndex = jest.fn();
    const validation = jest.fn();
    const doCreateAccessory = jest.fn();

    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
        $toast: {
            show: jest.fn(),
            success: jest.fn(),
            warning: jest.fn(),
            error: jest.fn(),
            hide: jest.fn(),
        },
    };

    const localVue = createLocalVue();
    const wrapper = mount(AccessoryCreate, {
        localVue,
        router,
        store,
        mocks,
        stubs: {
            BIcon: true,
        },
        methods: {
            backToAccessoryIndex,
            validation,
            doCreateAccessory,
        },
    });

    test('Test component render data', async() => {
        const isProcess = false;
        const accessory_name = '';
        const tonnage = null;
        const tonnage_options = [
            { value: null, text: 'PLACE_HOLDER.TONNAGE' },
            { value: 3, text: 3 },
            { value: 4, text: 4 },
        ];
        const passed_year = null;
        const passed_year_options = [
            { value: null, text: 'PLACE_HOLDER.PASSED_YEAR' },
            { value: 0, text: 0 },
            { value: 1, text: 1 },
            { value: 2, text: 2 },
            { value: 3, text: 3 },
            { value: 4, text: 4 },
            { value: 5, text: 5 },
            { value: 6, text: 6 },
            { value: 7, text: 7 },
            { value: 8, text: 8 },
            { value: 9, text: 9 },
            { value: 10, text: 10 },
        ];
        const mileage = '';

        expect(wrapper.vm.isProcess).toEqual(isProcess);
        expect(wrapper.vm.accessory_name).toEqual(accessory_name);
        expect(wrapper.vm.tonnage).toEqual(tonnage);
        expect(wrapper.vm.tonnage_options).toEqual(tonnage_options);
        expect(wrapper.vm.passed_year).toEqual(passed_year);
        expect(wrapper.vm.passed_year_options).toEqual(passed_year_options);
        expect(wrapper.vm.mileage).toEqual(mileage);
    });

    test('Test component render form create', async() => {
        const AccessoryCreate = wrapper.find('.accessory-management-create');
        expect(AccessoryCreate.exists()).toBe(true);

        const Content = AccessoryCreate.find('.content-body');
        expect(Content.exists()).toBe(true);

        const ListInputRow = Content.findAll('.input-row');
        expect(ListInputRow.length).toEqual(5);

        const AccessoryName = ListInputRow.at(0);
        const Tonnage = ListInputRow.at(1);
        const PassedYear = ListInputRow.at(2);
        const Mileage = ListInputRow.at(3);
        const ButtonFunction = ListInputRow.at(4);

        const LabelAccessoryName = AccessoryName.find('label');
        expect(LabelAccessoryName.exists()).toBe(true);
        expect(LabelAccessoryName.classes('accessories-management-label')).toBe(true);
        expect(LabelAccessoryName.classes('accessory-name')).toBe(true);
        expect(LabelAccessoryName.text()).toBe('ACCESSORY_MANAGEMENT.ACCESSORY_NAME');
        const InputAccessoryName = AccessoryName.find('input');
        expect(InputAccessoryName.exists()).toBe(true);
        expect(InputAccessoryName.classes('form-control')).toBe(true);
        expect(InputAccessoryName.classes('accessories-management-input')).toBe(true);
        expect(InputAccessoryName.classes('accessory-name')).toBe(true);
        expect(InputAccessoryName.attributes('type')).toBe('text');
        expect(InputAccessoryName.attributes('placeholder')).toBe('PLACE_HOLDER.ACCESSORY_NAME');

        const LabelTonnage = Tonnage.find('label');
        expect(LabelTonnage.exists()).toBe(true);
        expect(LabelTonnage.classes('accessories-management-label')).toBe(true);
        expect(LabelTonnage.classes('tonnage')).toBe(true);
        expect(LabelTonnage.text()).toEqual('ACCESSORY_MANAGEMENT.TONNAGE');
        const SelectTonnage = Tonnage.find('select');
        expect(SelectTonnage.exists()).toBe(true);
        expect(SelectTonnage.classes('custom-select')).toBe(true);
        expect(SelectTonnage.classes('custom-select-md')).toBe(true);
        expect(SelectTonnage.classes('accessories-management-select')).toBe(true);
        expect(SelectTonnage.classes('tonnage')).toBe(true);
        expect(SelectTonnage.attributes('placeholder')).toEqual('PLACE_HOLDER.PLEASE_SELECT');
        const DataTonnage = [
            {
                value: '',
                text: 'PLACE_HOLDER.TONNAGE',
            },
            {
                value: '3',
                text: '3',
            },
            {
                value: '4',
                text: '4',
            },
        ];
        const OptionTonnage = SelectTonnage.findAll('option');
        expect(OptionTonnage.length).toEqual(3);
        for (let option = 0; option < OptionTonnage.length; option++) {
            expect(OptionTonnage.at(option).attributes('value')).toEqual(DataTonnage[option].value);
            expect(OptionTonnage.at(option).text()).toEqual(DataTonnage[option].text);
        }

        const LabelPassedYear = PassedYear.find('label');
        expect(LabelPassedYear.exists()).toBe(true);
        expect(LabelPassedYear.classes('accessories-management-label')).toBe(true);
        expect(LabelPassedYear.classes('passed-year')).toBe(true);
        expect(LabelPassedYear.text()).toEqual('ACCESSORY_MANAGEMENT.PASSED_YEAR');
        const SelectPassedYear = PassedYear.find('select');
        expect(SelectPassedYear.exists()).toBe(true);
        expect(SelectPassedYear.classes('custom-select')).toBe(true);
        expect(SelectPassedYear.classes('custom-select-md')).toBe(true);
        expect(SelectPassedYear.classes('accessories-management-select')).toBe(true);
        expect(SelectPassedYear.classes('passed-years')).toBe(true);
        expect(SelectPassedYear.attributes('placeholder')).toEqual('PLACE_HOLDER.PLEASE_SELECT');
        const DataPassedYear = [
            {
                value: '',
                text: 'PLACE_HOLDER.PASSED_YEAR',
            },
            {
                value: '0',
                text: '0',
            },
            {
                value: '1',
                text: '1',
            },
            {
                value: '2',
                text: '2',
            },
            {
                value: '3',
                text: '3',
            },
            {
                value: '4',
                text: '4',
            },
            {
                value: '5',
                text: '5',
            },
            {
                value: '6',
                text: '6',
            },
            {
                value: '7',
                text: '7',
            },
            {
                value: '8',
                text: '8',
            },
            {
                value: '9',
                text: '9',
            },
            {
                value: '10',
                text: '10',
            },
        ];
        const OptionPassedYear = SelectPassedYear.findAll('option');
        expect(OptionPassedYear.length).toEqual(12);
        for (let option = 0; option < OptionPassedYear.length; option++) {
            expect(OptionPassedYear.at(option).attributes('value')).toEqual(DataPassedYear[option].value);
            expect(OptionPassedYear.at(option).text()).toEqual(DataPassedYear[option].text);
        }

        const LabelMileage = Mileage.find('label');
        expect(LabelMileage.exists()).toBe(true);
        expect(LabelMileage.classes('accessories-management-label')).toBe(true);
        expect(LabelMileage.classes('mileage')).toBe(true);
        expect(LabelMileage.text()).toEqual('ACCESSORY_MANAGEMENT.MILEAGE');
        const InputMileage = Mileage.find('input');
        expect(InputMileage.exists()).toBe(true);
        expect(InputMileage.classes('form-control')).toBe(true);
        expect(InputMileage.classes('accessories-management-input')).toBe(true);
        expect(InputMileage.classes('mileage')).toBe(true);
        expect(InputMileage.attributes('type')).toBe('number');
        expect(InputMileage.attributes('placeholder')).toBe('PLACE_HOLDER.MILEAGE');

        const ButtonBack = ButtonFunction.find('button#btn-back');
        expect(ButtonBack.exists()).toBe(true);
        expect(ButtonBack.classes('btn')).toBe(true);
        expect(ButtonBack.classes('btn-secondary')).toBe(true);
        expect(ButtonBack.classes('v-button-default')).toBe(true);
        expect(ButtonBack.text()).toEqual('BUTTON.BACK');
        await ButtonBack.trigger('click')
            .then(() => {
                expect(backToAccessoryIndex).toHaveBeenCalled();
            });

        const ButtonRegister = ButtonFunction.find('button#btn-save');
        expect(ButtonRegister.exists()).toBe(true);
        expect(ButtonRegister.classes('btn')).toBe(true);
        expect(ButtonRegister.classes('btn-secondary')).toBe(true);
        expect(ButtonRegister.classes('v-button-default')).toBe(true);
        expect(ButtonRegister.text()).toEqual('BUTTON.REGISTER');
        await ButtonRegister.trigger('click')
            .then(() => {
                expect(validation).toHaveBeenCalled();
            });
    });

    test('Test component run function backToAccessoryIndex', async() => {
        const ButtonBack = wrapper.find('button#btn-back');

        await ButtonBack.trigger('click');

        expect(backToAccessoryIndex).toHaveBeenCalled();
    });

    test('Test component run function validation with not fill accessory name', async() => {
        setTimeout(async() => {
            const ButtonRegister = wrapper.find('button#btn-save');

            await ButtonRegister.trigger('click');

            expect(validation).toHaveBeenCalled();
            expect(wrapper.vm.$toast.warning).toHaveBeenCalled();
            expect(wrapper.vm.$toast.warning).toHaveBeenCalledWith({
                content: 'TOAST.CONTENT.ACCESSORY_MANAGEMENT.REQUIRED_ACCESSORY_NAME',
            });
            expect(doCreateAccessory).not.toHaveBeenCalled();
        }, 1000);
    });

    test('Test component run function validation with not fill tonnage', async() => {
        setTimeout(async() => {
            wrapper.vm.accessory_name = 'Accessory Name';

            const ButtonRegister = wrapper.find('button#btn-save');

            await ButtonRegister.trigger('click');

            expect(validation).toHaveBeenCalled();
            expect(wrapper.vm.$toast.warning).toHaveBeenCalled();
            expect(wrapper.vm.$toast.warning).toHaveBeenCalledWith({
                content: 'TOAST.CONTENT.ACCESSORY_MANAGEMENT.REQUIRED_TONNAGE',
            });
            expect(doCreateAccessory).not.toHaveBeenCalled();
        }, 1000);
    });

    test('Test component run function validation with not fill passed year', async() => {
        setTimeout(async() => {
            wrapper.vm.accessory_name = 'Accessory Name';
            wrapper.vm.tonnage = '3';

            const ButtonRegister = wrapper.find('button#btn-save');

            await ButtonRegister.trigger('click');

            expect(validation).toHaveBeenCalled();
            expect(wrapper.vm.$toast.warning).toHaveBeenCalled();
            expect(wrapper.vm.$toast.warning).toHaveBeenCalledWith({
                content: 'TOAST.CONTENT.ACCESSORY_MANAGEMENT.REQUIRED_PASSED_YEAR',
            });
            expect(doCreateAccessory).not.toHaveBeenCalled();
        }, 1000);
    });

    test('Test component run function validation with not fill mileage', async() => {
        setTimeout(async() => {
            wrapper.vm.accessory_name = 'Accessory Name';
            wrapper.vm.tonnage = '3';
            wrapper.vm.passed_year = '1';

            const ButtonRegister = wrapper.find('button#btn-save');

            await ButtonRegister.trigger('click');

            expect(validation).toHaveBeenCalled();
            expect(wrapper.vm.$toast.warning).toHaveBeenCalled();
            expect(wrapper.vm.$toast.warning).toHaveBeenCalledWith({
                content: 'TOAST.CONTENT.ACCESSORY_MANAGEMENT.REQUIRED_MILEAGE',
            });
            expect(doCreateAccessory).not.toHaveBeenCalled();
        }, 1000);
    });

    test('Test component run function validation with full data', async() => {
        setTimeout(async() => {
            wrapper.vm.accessory_name = 'Accessory Name';
            wrapper.vm.tonnage = '3';
            wrapper.vm.passed_year = '1';
            wrapper.vm.mileage = '100';

            const ButtonRegister = wrapper.find('button#btn-save');

            await ButtonRegister.trigger('click');

            expect(validation).toHaveBeenCalled();
            expect(doCreateAccessory).toHaveBeenCalled();
        }, 1000);
    });
});
