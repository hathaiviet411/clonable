import store from '@/store';
import router from '@/router';
import { mount, createLocalVue } from '@vue/test-utils';
import Layout from '@/layout';

const HEADQUARTER = [
    '/login',
    '/',
    '/maintenance-schedule-results/index',
    '/dev',
    '/error-page',
    '*',
    '/maintenance-cost/index',
    '/maintenance-cost/detail/:id',
    '/maintenance-cost/create',
    '/maintenance-cost/edit/:id',
    '/maintenance-cost/warning/:id',
    '/accessory-management/index',
    '/accessory-management/create',
    '/accessory-management/edit/:id',
    '/user-management/index',
    '/user-management/create',
    '/user-management/edit/:id',
    '/vehicle-cost/index',
];

const OPERATOR = [
    '/login',
    '/',
    '/maintenance-schedule-results/index',
    '/dev',
    '/error-page',
    '*',
    '/maintenance-cost/index',
    '/maintenance-cost/detail/:id',
    '/maintenance-cost/create',
    '/maintenance-cost/edit/:id',
    '/maintenance-cost/warning/:id',
    '/vehicle-cost/index',
];

const TEAM = [
    '/login',
    '/',
    '/maintenance-schedule-results/index',
    '/dev',
    '/error-page',
    '*',
    '/maintenance-cost/index',
    '/maintenance-cost/detail/:id',
    '/maintenance-cost/create',
    '/maintenance-cost/edit/:id',
    '/maintenance-cost/warning/:id',
    '/vehicle-cost/index',
];

async function getRoutes(ROLES = [], PERMISSIONS = []) {
    const CONSTANT_ROUTES = [
        '/login',
        '/',
        '/maintenance-schedule-results/index',
        '/dev',
        '/error-page',
        '*',
    ];

    const ACCESS = [];

    await store.dispatch('permissions/generateRoutes', { roles: ROLES, permissions: PERMISSIONS })
        .then((res) => {
            let index = 0;
            const len = res.length;

            while (index < len) {
                const PATH = res[index]['path'];
                const CHILDREN = res[index]['children'];

                for (let i = 0; i < CHILDREN.length; i++) {
                    ACCESS.push(`${PATH}/${CHILDREN[i]['path']}`);
                }

                index++;
            }
        });

    return CONSTANT_ROUTES.concat(ACCESS);
}

function validateRoutes(ROUTES = [], ROUTES_CHECK = []) {
    let count = 0;
    let index = 0;
    const len = ROUTES.length;

    while (index < len) {
        if (ROUTES_CHECK.includes(ROUTES[index])) {
            count = count + 1;
        }

        index++;
    }

    return count === ROUTES_CHECK.length;
}

describe('TEST ROLE', () => {
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
    };

    const localVue = createLocalVue();
    const wrapper = mount(Layout, {
        localVue,
        router,
        store,
        mocks,
        stubs: {
            BIcon: true,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test generate routes with role Headquater', async() => {
        const ROLES = ['headquarter'];
        const PERMISSIONS = [];

        let ACCESS = [];

        await getRoutes(ROLES, PERMISSIONS)
            .then((res) => {
                ACCESS = res;
            });

        expect(validateRoutes(ACCESS, HEADQUARTER)).toBe(true);
    });

    test('Test generate routes with role Operator', async() => {
        const ROLES = ['operator'];
        const PERMISSIONS = [];

        let ACCESS = [];

        await getRoutes(ROLES, PERMISSIONS)
            .then((res) => {
                ACCESS = res;
            });

        expect(validateRoutes(ACCESS, OPERATOR)).toBe(true);
    });

    test('Test generate routes with role Team', async() => {
        const ROLES = ['team'];
        const PERMISSIONS = [];

        let ACCESS = [];

        await getRoutes(ROLES, PERMISSIONS)
            .then((res) => {
                ACCESS = res;
            });

        expect(validateRoutes(ACCESS, TEAM)).toBe(true);
    });

    wrapper.destroy();
});
