import store from '@/store';
import router from '@/router';
import { mount, createLocalVue } from '@vue/test-utils';
import AccessorySchedule from '@/components/organisms/MaintenanceScheduleResults/Table/TableVehicleMaintenanceSchedule';
import {
    getListMonth,
    handelData,
    handleStatus,
} from '@/components/organisms/MaintenanceScheduleResults/Table/AccessorySchedule';

describe('TEST COMPONENT ACCESSORY SCHEDULE TABLE', () => {
    const DATA = {
        '横浜800あ6772-12': [
            {
                'month': '2023-02-01',
                'content': [
                    {
                        'color': 3,
                        'text': 'ミッション・デフオイル',
                    },
                    {
                        'color': 3,
                        'text': 'ブレーキ/クラッチフルード',
                    },
                    {
                        'color': 3,
                        'text': '差圧パイプ',
                    },
                    {
                        'color': 3,
                        'text': 'ワイヤレスキー電池',
                    },
                    {
                        'color': 3,
                        'text': 'キャビン灯',
                    },
                    {
                        'color': 3,
                        'text': 'ワイパーゴム',
                    },
                ],
            },
        ],
    };
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
    };

    const implementPDF = jest.fn();

    const localVue = createLocalVue();
    const wrapper = mount(AccessorySchedule, {
        localVue,
        router,
        store,
        mocks,
        methods: {
            implementPDF,
        },
        stubs: {
            BTooltip: true,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    wrapper.setProps({
        items: [],
    });

    test('Test component render table total row = 0', async() => {
        const TABLE = wrapper.find('table');

        const BODY = TABLE.find('tbody');
        const LIST_ROW = BODY.findAll('tr');

        expect(LIST_ROW.length).toEqual(1);

        console.log(LIST_ROW.at(0).find('td').text());
    });

    wrapper.setProps({
        items: handelData(DATA),
    });

    test('Test component render Description', async() => {
        const MaintenanceCompleted = wrapper.find('i.fa-check-circle');
        expect(MaintenanceCompleted.exists()).toBe(true);

        const MaintenanceIncompleteAlert = wrapper.find('i.fa-circle');
        expect(MaintenanceIncompleteAlert.exists()).toBe(true);
    });

    test('Test component render Button PDF', async() => {
        const BTN_PRINT_PDF = wrapper.find('button.maintenance-pdf-btn');
        expect(BTN_PRINT_PDF.exists()).toBe(true);
        expect(BTN_PRINT_PDF.find('span').text()).toEqual('BUTTON.PDF');

        await BTN_PRINT_PDF.trigger('click');

        expect(implementPDF).toHaveBeenCalled();
    });

    test('Test component render table', async() => {
        const TABLE = wrapper.find('table');

        const HEADER_TABLE = TABLE.find('thead');
        const LIST_HEADER = HEADER_TABLE.findAll('th');
        expect(LIST_HEADER.length).toEqual(14);

        const LEN_HEADER = LIST_HEADER.length;
        let IndexHeader = 0;

        const HEADER_TEXT = [
            'MAINTENANCE_SCHEDULE_RESULTS.MONTH  MAINTENANCE_SCHEDULE_RESULTS.NUMBER_PLATE',
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
            '',
        ];

        while (IndexHeader < LEN_HEADER) {
            expect(LIST_HEADER.at(IndexHeader).text()).toEqual(HEADER_TEXT[IndexHeader]);

            IndexHeader++;
        }

        const TABLE_BODY = TABLE.find('tbody');
        expect(TABLE_BODY.exists()).toBe(true);
        const LIST_ROW = TABLE_BODY.findAll('tr');
        expect(LIST_ROW.length).toEqual(1);
        const LIST_COL = LIST_ROW.at(0).findAll('td');
        expect(LIST_COL.length).toEqual(14);

        for (let index = 0; index < LIST_COL.length; index++) {
            if (index === 0) {
                expect(LIST_COL.at(index).text()).toEqual('横浜800あ6772');
            }
        }
    });

    test('Test function getListMonth', async() => {
        const LIST_MONTH = [4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3];

        expect(getListMonth()).toEqual(LIST_MONTH);
    });

    test('Test function handleStatus', async() => {
        expect(handleStatus(0)).toEqual('icon-alert');
        expect(handleStatus(1)).toEqual('icon-check');
        expect(handleStatus()).toEqual('');
        expect(handleStatus(null)).toEqual('');
        expect(handleStatus(undefined)).toEqual('');
        expect(handleStatus('123123123')).toEqual('');
        expect(handleStatus('Veho Company')).toEqual('');
    });
});
