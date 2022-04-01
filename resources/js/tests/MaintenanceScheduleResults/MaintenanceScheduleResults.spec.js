import { mount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import router from '@/router';
import MaintenanceScheduleResultsIndex from '@/pages/MaintenanceScheduleResults/Index';
import MaintenanceScheduleResultsTemplate from '@/components/template/MaintenanceScheduleResults';
import MaintenanceScheduleResultHeaderContent from '@/components/organisms/MaintenanceScheduleResultHeaderContent';

describe('TEST COMPONENT MAINTENANCE SCHEDULE RESULTS', () => {
    test('Test component render the general layout', () => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceScheduleResultsIndex, {
            localVue,
            router,
            store,
        });

        const MaintenanceScheduleResultLayout = wrapper.find('.maintenance-schedule-results');
        expect(MaintenanceScheduleResultLayout.exists()).toBe(true);

        wrapper.destroy();
    });

    test('Test component render the header', () => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceScheduleResultsIndex, {
            localVue,
            router,
            store,
        });

        const HeaderTemplate = wrapper.find('.header-template');
        expect(HeaderTemplate.exists()).toBe(true);

        const HeaderContent = wrapper.find('.header-content');
        expect(HeaderContent.exists()).toBe(true);

        wrapper.destroy();
    });

    test('Test the content render of the header template', () => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceScheduleResultsTemplate, {
            localVue,
            router,
            store,
        });

        const PageTitle = wrapper.find('.maintenance-schedule-results__title-header');
        expect(PageTitle.exists()).toBe(true);
        expect(PageTitle.text()).toEqual('PAGE_TITLE.MAINTENANCE_SCHEDULE_RESULTS');

        wrapper.destroy();
    });

    test('Test the content render of the header content', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceScheduleResultHeaderContent, {
            localVue,
            router,
            store,
        });

        const ButtonPeriodicInspectionSchedule = wrapper.find('.btn-periodic');
        expect(ButtonPeriodicInspectionSchedule.exists()).toBe(true);
        expect(ButtonPeriodicInspectionSchedule.text()).toEqual('BUTTON.PERIODIC_INSPECTION_SCHEDULE_TABLE');

        const handleDisplayTable = jest.spyOn(wrapper.vm, 'handleDisplayTable');
        await ButtonPeriodicInspectionSchedule.trigger('click');
        expect(handleDisplayTable).toHaveBeenCalled();

        const ButtonScheduleResult = wrapper.find('.btn-schedule-result');
        expect(ButtonScheduleResult.exists()).toBe(true);
        expect(ButtonScheduleResult.text()).toEqual('BUTTON.SCHEDULE_AND_RESULT_TABLE');

        await ButtonScheduleResult.trigger('click');
        expect(handleDisplayTable).toHaveBeenCalled();

        const ButtonPDF = wrapper.find('.maintenance-pdf-btn');
        expect(ButtonPDF.exists()).toBe(true);
        expect(ButtonPDF.text()).toEqual('BUTTON.PDF');

        const implementPDF = jest.spyOn(wrapper.vm, 'implementPDF');
        await ButtonPDF.trigger('click');
        expect(implementPDF).toHaveBeenCalled();

        wrapper.destroy();
    });

    test('Test component render the content of the Periodic Inspection Schedule Table', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceScheduleResultsIndex, {
            localVue,
            router,
            store,
        });

        const DATA = [
            {
                id: 1,
                'vehicle_identify_number': 'NPR85-7142279',
                'jan': '1',
                'feb': '2',
                'mar': '3',
                'apr': '4',
                'may': '5',
                'jun': '6',
                'jul': '7',
                'aug': '8',
                'sep': '9',
                'oct': '10',
                'nov': '11',
                'dec': '12',
                'remark': 'No remark.',
            },
            {
                id: 2,
                'vehicle_identify_number': 'NPR86-8253380',
                'jan': '1',
                'feb': '2',
                'mar': '3',
                'apr': '4',
                'may': '5',
                'jun': '6',
                'jul': '7',
                'aug': '8',
                'sep': '9',
                'oct': '10',
                'nov': '11',
                'dec': '12',
                'remark': 'No remark.',
            },
            {
                id: 3,
                'vehicle_identify_number': 'NPR87-9364481',
                'jan': '1',
                'feb': '2',
                'mar': '3',
                'apr': '4',
                'may': '5',
                'jun': '6',
                'jul': '7',
                'aug': '8',
                'sep': '9',
                'oct': '10',
                'nov': '11',
                'dec': '12',
                'remark': 'No remark.',
            },
            {
                id: 4,
                'vehicle_identify_number': 'NPR88-1075582',
                'jan': '1',
                'feb': '2',
                'mar': '3',
                'apr': '4',
                'may': '5',
                'jun': '6',
                'jul': '7',
                'aug': '8',
                'sep': '9',
                'oct': '10',
                'nov': '11',
                'dec': '12',
                'remark': 'No remark.',
            },
        ];

        await wrapper.setData({ items: DATA });

        const ButtonPeriodicInspectionSchedule = wrapper.find('.btn-periodic');
        await ButtonPeriodicInspectionSchedule.trigger('click');

        const PeriodicInspectionScheduleTable = wrapper.find('.table-periodic-inspection-schedule');
        expect(PeriodicInspectionScheduleTable.exists()).toBe(true);

        const TableHeader = wrapper.find('thead');
        const ListHeader = TableHeader.findAll('th');
        expect(ListHeader.length).toEqual(15);

        const ListHeaderText = [
            'MAINTENANCE_SCHEDULE_RESULTS.VEHICLE_IDENTIFY_NUMBER',
            'MAINTENANCE_SCHEDULE_RESULTS.MONTH',
            'MAINTENANCE_SCHEDULE_RESULTS.APR',
            'MAINTENANCE_SCHEDULE_RESULTS.MAY',
            'MAINTENANCE_SCHEDULE_RESULTS.JUN',
            'MAINTENANCE_SCHEDULE_RESULTS.JUL',
            'MAINTENANCE_SCHEDULE_RESULTS.AUG',
            'MAINTENANCE_SCHEDULE_RESULTS.SEP',
            'MAINTENANCE_SCHEDULE_RESULTS.OCT',
            'MAINTENANCE_SCHEDULE_RESULTS.NOV',
            'MAINTENANCE_SCHEDULE_RESULTS.DEC',
            'MAINTENANCE_SCHEDULE_RESULTS.JAN',
            'MAINTENANCE_SCHEDULE_RESULTS.FEB',
            'MAINTENANCE_SCHEDULE_RESULTS.MAR',
            'MAINTENANCE_SCHEDULE_RESULTS.REMARK',
        ];

        for (let th = 0; th < ListHeader.length; th++) {
            expect(ListHeader.at(th).text()).toEqual(ListHeaderText[th]);
        }

        const ListTableBody = wrapper.findAll('tbody');
        expect(ListTableBody.length).toEqual(4);

        await wrapper.setData({ items: [] });

        wrapper.destroy();
    });

    test('Test component render the content of the Schedule Result Table', async() => {
        const localVue = createLocalVue();
        const wrapper = mount(MaintenanceScheduleResultsIndex, {
            localVue,
            router,
            store,
        });

        const DATA = [
            {
                'id': 1,
                'number_plate': '横浜800あ8220',
                'manufacturer': 'いすゞ',
                'end_of_lease': '04-11-2000/29-02-2000',
                'inspection_expiration_date': '03/04',
                'initial_registration': '31年2月',
                'jan': '-',
                'feb': '検 111,111',
                'mar': '③',
                'apr': '-',
                'may': '-',
                'jun': '③',
                'jul': '-',
                'aug': '-',
                'sep': '③',
                'oct': '-',
                'nov': '-',
                'dec': '⑫',
                'leasing_company': '西出',
            },
            {
                'id': 2,
                'number_plate': '横浜800あ8220',
                'manufacturer': 'いすゞ',
                'end_of_lease': '04-11-2000/29-02-2000',
                'inspection_expiration_date': '03/04',
                'initial_registration': '31年2月',
                'jan': '-',
                'feb': '検 111,111',
                'mar': '③',
                'apr': '-',
                'may': '-',
                'jun': '③',
                'jul': '-',
                'aug': '-',
                'sep': '③',
                'oct': '-',
                'nov': '-',
                'dec': '⑫',
                'leasing_company': '西出',
            },
            {
                'id': 3,
                'number_plate': '横浜800あ8220',
                'manufacturer': 'いすゞ',
                'end_of_lease': '04-11-2000/29-02-2000',
                'inspection_expiration_date': '03/04',
                'initial_registration': '31年2月',
                'jan': '-',
                'feb': '検 111,111',
                'mar': '③',
                'apr': '-',
                'may': '-',
                'jun': '③',
                'jul': '-',
                'aug': '-',
                'sep': '③',
                'oct': '-',
                'nov': '-',
                'dec': '⑫',
                'leasing_company': '西出',
            },
            {
                'id': 4,
                'number_plate': '横浜800あ8220',
                'manufacturer': 'いすゞ',
                'end_of_lease': '04-11-2000/29-02-2000',
                'inspection_expiration_date': '03/04',
                'initial_registration': '31年2月',
                'jan': '-',
                'feb': '検 111,111',
                'mar': '③',
                'apr': '-',
                'may': '-',
                'jun': '③',
                'jul': '-',
                'aug': '-',
                'sep': '③',
                'oct': '-',
                'nov': '-',
                'dec': '⑫',
                'leasing_company': '西出',
            },
            {
                'id': 5,
                'number_plate': '横浜800あ8220',
                'manufacturer': 'いすゞ',
                'end_of_lease': '04-11-2000/29-02-2000',
                'inspection_expiration_date': '03/04',
                'initial_registration': '31年2月',
                'jan': '-',
                'feb': '検 111,111',
                'mar': '③',
                'apr': '-',
                'may': '-',
                'jun': '③',
                'jul': '-',
                'aug': '-',
                'sep': '③',
                'oct': '-',
                'nov': '-',
                'dec': '⑫',
                'leasing_company': '西出',
            },
            {
                'id': 6,
                'number_plate': '横浜800あ8220',
                'manufacturer': 'いすゞ',
                'end_of_lease': '04-11-2000/29-02-2000',
                'inspection_expiration_date': '03/04',
                'initial_registration': '31年2月',
                'jan': '-',
                'feb': '検 111,111',
                'mar': '③',
                'apr': '-',
                'may': '-',
                'jun': '③',
                'jul': '-',
                'aug': '-',
                'sep': '③',
                'oct': '-',
                'nov': '-',
                'dec': '⑫',
                'leasing_company': '西出',
            },
        ];

        await wrapper.setData({ items: DATA });

        const ButtonScheduleResult = wrapper.find('.btn-schedule-result');
        await ButtonScheduleResult.trigger('click');

        const ScheduleResultTable = wrapper.find('.table-schedule-result');
        expect(ScheduleResultTable.exists()).toBe(true);

        const TableHeader = wrapper.find('thead');
        const ListHeader = TableHeader.findAll('th');
        expect(ListHeader.length).toEqual(18);

        const ListHeaderText = [
            'MAINTENANCE_SCHEDULE_RESULTS.NUMBER_PLATE',
            'MAINTENANCE_SCHEDULE_RESULTS.MANUFACTURER',
            'MAINTENANCE_SCHEDULE_RESULTS.END_OF_LEASE',
            'MAINTENANCE_SCHEDULE_RESULTS.INSPECTION_EXPIRATION_DATE',
            'MAINTENANCE_SCHEDULE_RESULTS.INITIAL_REGISTRATION',
            '28/1',
            '28/2',
            '28/3',
            '28/4',
            '28/5',
            '28/6',
            '28/7',
            '28/8',
            '28/9',
            '28/10',
            '28/11',
            '28/12',
            'MAINTENANCE_SCHEDULE_RESULTS.LEASING_COMPANY',
        ];

        for (let th = 0; th < ListHeader.length; th++) {
            expect(ListHeader.at(th).text()).toEqual(ListHeaderText[th]);
        }

        const ListTableBody = wrapper.findAll('tbody');
        expect(ListTableBody.length).toEqual(6);

        await wrapper.setData({ items: [] });

        wrapper.destroy();
    });
});

