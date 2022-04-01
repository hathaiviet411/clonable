import { mount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import router from '@/router';
import AccessoryList from '@/pages/AccessoryManagement/Index';

describe('TEST COMPONENT ACCESSORY LIST', () => {
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
    };

    const getListAccessory = jest.fn();

    const localVue = createLocalVue();
    const wrapper = mount(AccessoryList, {
        localVue,
        router,
        store,
        mocks,
        stubs: {
            BIcon: true,
        },
        methods: {
            getListAccessory,
        },
    });

    test('Test component render data', async() => {
        const vItems = [];
        const filter = {
            accessory_name: '',
        };
        const pagination = {
            currentPage: 1,
            perPage: 20,
            totalRows: 0,
        };
        const filterQuery = {
            order_column: '',
            order_type: '',
        };

        expect(wrapper.vm.vItems).toEqual(vItems);
        expect(wrapper.vm.filter).toEqual({
            'accessory_name': '',
            'isCheck': false,
        });
        expect(wrapper.vm.pagination).toEqual(pagination);
        expect(wrapper.vm.filterQuery).toEqual(filterQuery);
    });

    test('Test component render Overlay', async() => {
        const OVERLAY = wrapper.find('.b-overlay-wrap');
        expect(OVERLAY.exists()).toBe(true);
        expect(OVERLAY.classes('b-overlay-wrap')).toBe(true);
        expect(OVERLAY.classes('position-relative')).toBe(true);
    });

    test('Test component render Header Page', async() => {
        const AccessoryList = wrapper.find('.accessory-management');
        expect(AccessoryList.exists()).toBe(true);

        const Header = AccessoryList.find('.accessory-management__header-page');
        expect(Header.find('.card').exists()).toBe(true);
        expect(Header.find('.card-body').exists()).toBe(true);
        expect(Header.find('.header').exists()).toBe(true);
        expect(Header.find('.header__straight').exists()).toBe(true);
        expect(Header.find('.header__text').exists()).toBe(true);
        expect(Header.text()).toEqual('PAGE_TITLE.ACCESSORY_MANAGEMENT');
    });

    test('Test component render button Register', async() => {
        const ZoneButton = wrapper.find('.accessory-management__register-btn');
        expect(ZoneButton.find('.row').exists()).toBe(true);
        expect(ZoneButton.find('.col-12').exists()).toBe(true);

        const Button = ZoneButton.find('button');
        expect(Button.classes('btn')).toBe(true);
        expect(Button.classes('btn-secondary')).toBe(true);
        expect(Button.classes('maintenance-btn-default')).toBe(true);
        expect(Button.classes('maintenance-pdf-btn')).toBe(true);

        const ButtonText = Button.find('span');
        expect(ButtonText.exists()).toBe(true);
        expect(ButtonText.text()).toBe('BUTTON.REGISTER');
    });

    test('Test component render Filter', async() => {
        const ZoneFilter = wrapper.find('.accessory-management__zone-filter');
        expect(ZoneFilter.exists()).toBe(true);
        expect(ZoneFilter.find('.organisms-accessories-management-filter').exists()).toBe(true);

        const Filter = ZoneFilter.find('.header-filter');
        expect(Filter.exists()).toEqual(true);

        const ButtonToggle = ZoneFilter.find('h6');
        const ButtonToggleText = ButtonToggle.find('span');
        expect(ButtonToggleText.attributes('aria-controls')).toEqual('zone-filter');
        expect(ButtonToggleText.attributes('aria-expanded')).toEqual('false');
        expect(ButtonToggleText.attributes('role')).toEqual('button');
        expect(ButtonToggleText.attributes('tabindex')).toEqual('0');
        expect(ButtonToggleText.classes('not-collapsed')).toBe(false);
        expect(ButtonToggleText.attributes('id')).toBe('collapsed-show-hide-filter');

        const Text = ButtonToggleText.find('span.filter-title');
        expect(Text.exists()).toBe(true);
        expect(Text.text()).toEqual('FILTER.TITLE');

        const IconToggle = ButtonToggleText.findAll('i');
        expect(IconToggle.length).toEqual(2);

        expect(IconToggle.at(0).classes('fas')).toBe(true);
        expect(IconToggle.at(0).classes('fa-angle-up')).toBe(true);
        expect(IconToggle.at(0).classes('when-open')).toBe(true);

        expect(IconToggle.at(1).classes('fas')).toBe(true);
        expect(IconToggle.at(1).classes('fa-angle-down')).toBe(true);
        expect(IconToggle.at(1).classes('when-closed')).toBe(true);

        const ContentFilter = Filter.find('.show-filter');
        expect(ContentFilter.exists()).toBe(true);

        const TextClear = ContentFilter.find('span.text-clear-all');
        expect(TextClear.exists()).toBe(true);
        expect(TextClear.text()).toEqual('BUTTON.CLEAR_ALL');

        const FilterAccessoryName = wrapper.find('.zone-input');
        expect(FilterAccessoryName.exists()).toBe(true);
        expect(FilterAccessoryName.find('input[type="checkbox"]').exists()).toBe(true);
        expect(FilterAccessoryName.find('span').text()).toEqual('ACCESSORY_MANAGEMENT.ACCESSORY_NAME');
        expect(FilterAccessoryName.find('input#filter-by-accessory-name').exists()).toBe(true);
        expect(FilterAccessoryName.find('input#filter-by-accessory-name').attributes('type')).toEqual('text');
        expect(FilterAccessoryName.find('input#filter-by-accessory-name').attributes('name')).toEqual('');
        expect(FilterAccessoryName.find('input#filter-by-accessory-name').attributes('placeholder')).toEqual('');
        expect(FilterAccessoryName.find('input#filter-by-accessory-name').attributes('id')).toEqual('filter-by-accessory-name');
        expect(FilterAccessoryName.find('input#filter-by-accessory-name').attributes('disabled')).toEqual('disabled');
        expect(FilterAccessoryName.find('input#filter-by-accessory-name').classes('form-control')).toBe(true);
        expect(FilterAccessoryName.find('input#filter-by-accessory-name').classes('v-custom-input')).toBe(true);

        const ButtonApply = Filter.find('button.btn-summit-filter');
        expect(ButtonApply.exists()).toBe(true);
        expect(ButtonApply.classes('btn')).toBe(true);
        expect(ButtonApply.classes('btn-secondary')).toBe(true);
        expect(ButtonApply.classes('v-button-default')).toBe(true);
        expect(ButtonApply.classes('btn-summit-filter')).toBe(true);
        expect(ButtonApply.attributes('type')).toEqual('button');
        expect(ButtonApply.text()).toEqual('BUTTON.APPLY');
    });

    test('Test component render Table', async() => {
        const ZoneTable = wrapper.find('.accessory-management__table');
        expect(ZoneTable.exists()).toBe(true);

        const Table = ZoneTable.find('table');
        expect(Table.exists()).toBe(true);
        expect(Table.attributes('aria-busy')).toEqual('false');
        expect(Table.attributes('aria-colcount')).toEqual('7');
        expect(Table.attributes('id')).toEqual('table-accessory-management');
        expect(Table.attributes('role')).toEqual('table');
        expect(Table.classes('table')).toBe(true);
        expect(Table.classes('b-table')).toBe(true);
        expect(Table.classes('table-striped')).toBe(true);
        expect(Table.classes('table-bordered')).toBe(true);

        const TableHeader = Table.find('thead');
        expect(TableHeader.exists()).toBe(true);
        const ListHeader = TableHeader.findAll('th');
        expect(ListHeader.exists()).toBe(true);
        expect(ListHeader.length).toEqual(7);

        const ListTextHeader = [
            'ACCESSORY_MANAGEMENT.ACCESSORY_NAME',
            'ACCESSORY_MANAGEMENT.TONNAGE',
            'ACCESSORY_MANAGEMENT.PASSED_YEAR',
            'ACCESSORY_MANAGEMENT.MILEAGE',
        ];

        for (let th = 0; th < 4; th++) {
            expect(ListHeader.at(th).find('div').text()).toEqual(ListTextHeader[th]);
        }

        const TableBodyEmpty = Table.find('tbody');
        expect(TableBodyEmpty.exists()).toBe(true);
        const TrEmpty = TableBodyEmpty.find('tr');
        expect(TrEmpty.text()).toEqual('TABLE.EMPTY');
    });

    test('Test function sort Table', async() => {
        const Table = wrapper.find('table');
        const TableHeader = Table.find('thead');
        const Tr = TableHeader.find('tr');
        const Th = Tr.findAll('th');

        await Th.at(1).trigger('click');
        expect(wrapper.vm.$bus.emit).toHaveBeenCalledWith(
            'sendSortTableAccessory',
            {
                order_column: 'tonnage',
                order_type: 0,
            }
        );
        expect(getListAccessory).toHaveBeenCalled();

        await Th.at(2).trigger('click');
        expect(wrapper.vm.$bus.emit).toHaveBeenCalledWith(
            'sendSortTableAccessory',
            {
                order_column: 'passed_year',
                order_type: 0,
            }
        );
        expect(getListAccessory).toHaveBeenCalled();

        await Th.at(3).trigger('click');
        expect(wrapper.vm.$bus.emit).toHaveBeenCalledWith(
            'sendSortTableAccessory',
            {
                order_column: 'mileage',
                order_type: 0,
            }
        );
        expect(getListAccessory).toHaveBeenCalled();
    });

    test('Test function filter', async() => {
        const Apply = wrapper.find('button.btn-summit-filter');
        expect(Apply.exists()).toBe(true);
        expect(getListAccessory).toHaveBeenCalled();
    });

    test('Test component render pagination when total rows > 20', async() => {
        const pagination = {
            currentPage: 2,
            perPage: 20,
            totalRows: 42,
        };

        await wrapper.setData({ pagination });

        const ElPagination = wrapper.find('.accessory-management__pagination');
        expect(ElPagination.exists()).toBe(true);
    });

    test('Test component render pagination when total rows < 20', async() => {
        const pagination = {
            currentPage: 1,
            perPage: 20,
            totalRows: 0,
        };

        await wrapper.setData({ pagination });

        const ElPagination = wrapper.find('.accessory-management__pagination');
        expect(ElPagination.exists()).toBe(false);
    });

    test('Test the data was correctly fetched and displayed after created', async() => {
        wrapper.setData({
            vItems: [],
            pagination: {
                currentPage: 1,
                perPage: 20,
                totalRows: 0,
            },
        });

        await wrapper.vm.getListAccessory();
        expect(getListAccessory).toHaveBeenCalled();

        expect(wrapper.vm.vItems).not.toBeNull();
        expect(wrapper.vm.pagination.currentPage).not.toBeNull();
        expect(wrapper.vm.pagination.perPage).not.toBeNull();
        expect(wrapper.vm.pagination.totalRows).not.toBeNull();
    });

    test('Test 0 record, 1 record, multiple records in 1 page, multiple pages', async() => {
        wrapper.setData({
            vItems: [],
            pagination: {
                currentPage: 1,
                perPage: 20,
                totalRows: 0,
            },
            isShowPagination: false,
        });

        await wrapper.vm.getListAccessory();
        expect(getListAccessory).toHaveBeenCalled();

        if (wrapper.vm.vItems.length === 0) {
            wrapper.vm.isShowPagination = false;
            expect(wrapper.vm.isShowPagination).toBe(false);
        } else if (wrapper.vm.vItems.length > 20) {
            wrapper.vm.isShowPagination = true;
            expect(wrapper.vm.isShowPagination).toBe(true);
        }
    });
});
