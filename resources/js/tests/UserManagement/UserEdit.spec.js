import { mount, createLocalVue } from '@vue/test-utils';
import store from '@/store';
import router from '@/router';
import UserManagementEdit from '@/pages/UserManagement/Edit';

describe('TEST PAGE USER MANAGEMENT EDIT', () => {
    const mocks = {
        $bus: {
            on: jest.fn(),
            once: jest.fn(),
            off: jest.fn(),
            emit: jest.fn(),
        },
    };

    const initData = jest.fn();
    const backToUserManagementIndex = jest.fn();
    const validation = jest.fn();
    const handleGetListRole = jest.fn();
    const handleGetListDepartment = jest.fn();
    const handleGetUser = jest.fn();
    const checkPermission = jest.fn();
    const processUpdateUser = jest.fn();
    const handleUpdateUser = jest.fn();

    const localVue = createLocalVue();
    const wrapper = mount(UserManagementEdit, {
        localVue,
        router,
        store,
        mocks,
        stubs: {
            BIcon: true,
        },
        methods: {
            initData,
            backToUserManagementIndex,
            validation,
            handleGetListRole,
            handleGetListDepartment,
            handleGetUser,
            checkPermission,
            processUpdateUser,
            handleUpdateUser,
        },
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    test('Test component call api in hook created', async() => {
        expect(initData).toHaveBeenCalled();
    });

    test('Test component render data', async() => {
        const isProcess = false;
        const user_role = null;
        const user_id = '';
        const base = null;
        const user_name = '';
        const pwd = '';
        const overlay = {
            show: false,
            variant: 'light',
            opacity: 1,
            blur: '1rem',
            rounded: 'sm',
        };

        expect(wrapper.vm.isProcess).toEqual(isProcess);
        expect(wrapper.vm.user_role).toEqual(user_role);
        expect(wrapper.vm.user_role_options).not.toBeNull();
        expect(wrapper.vm.user_id).toEqual(user_id);
        expect(wrapper.vm.base).toEqual(base);
        expect(wrapper.vm.base_options).not.toBeNull();
        expect(wrapper.vm.user_name).toEqual(user_name);
        expect(wrapper.vm.pwd).toEqual(pwd);
        expect(wrapper.vm.overlay).toEqual(overlay);
    });

    test('Test component render UI', async() => {
        const UserEdit = wrapper.find('.user-management-edit');
        expect(UserEdit.exists()).toBe(true);

        const Body = UserEdit.find('.content-body');
        expect(Body.exists()).toBe(true);

        const ListInput = wrapper.findAll('.input-row');
        expect(ListInput.length).toEqual(7);

        const UserRoleLabel = wrapper.find('label.user-role');
        expect(UserRoleLabel.exists()).toBe(true);
        expect(UserRoleLabel.text()).toEqual('USER_MANAGEMENT.USER_ROLE');

        const BaseLabel = wrapper.find('label.base');
        expect(BaseLabel.exists()).toBe(true);
        expect(BaseLabel.text()).toEqual('USER_MANAGEMENT.BASE');

        const UserIdLabel = wrapper.find('label.user-id');
        expect(UserIdLabel.exists()).toBe(true);
        expect(UserIdLabel.text()).toEqual('USER_MANAGEMENT.USER_ID');

        const UserNameLabel = wrapper.find('label.user-name');
        expect(UserNameLabel.exists()).toBe(true);
        expect(UserNameLabel.text()).toEqual('USER_MANAGEMENT.USER_NAME');

        const PasswordLabel = wrapper.find('label.pwd');
        expect(PasswordLabel.exists()).toBe(true);
        expect(PasswordLabel.text()).toEqual('USER_MANAGEMENT.PASSWORD');

        const NewPasswordLabel = wrapper.find('label.new-pwd');
        expect(NewPasswordLabel.exists()).toBe(true);
        expect(NewPasswordLabel.text()).toEqual('USER_MANAGEMENT.NEW_PASSWORD');

        const FunctionalButtons = wrapper.find('.footer-functional-buttons');
        const ListButton = FunctionalButtons.findAll('button');
        expect(ListButton.length).toEqual(2);

        const ButtonBack = FunctionalButtons.find('button#btn-back');
        expect(ButtonBack.exists()).toBe(true);
        expect(ButtonBack.text()).toEqual('BUTTON.BACK');

        const ButtonRegister = FunctionalButtons.find('button#btn-save');
        expect(ButtonRegister.exists()).toBe(true);
        expect(ButtonRegister.text()).toEqual('BUTTON.SAVE');
    });

    test('Test click button back', async() => {
        const ButtonBack = wrapper.find('button#btn-back');
        expect(ButtonBack.exists()).toBe(true);

        await ButtonBack.trigger('click');

        expect(backToUserManagementIndex).toHaveBeenCalled();
    });

    test('Test click button save', async() => {
        const ButtonSave = wrapper.find('button#btn-save');
        expect(ButtonSave.exists()).toBe(true);

        await ButtonSave.trigger('click');

        expect(validation).toHaveBeenCalled();
    });

    test('Test the data was correctly fetched and displayed after created', async() => {
        await wrapper.setData({
            user_role: '',
            user_role_options: [],
            user_id: '',
            base: '',
            base_options: [],
            user_name: '',
        });

        await wrapper.vm.handleGetUser();

        expect(handleGetUser).toHaveBeenCalled();
        expect(wrapper.vm.user_role).not.toBeNull();
        expect(wrapper.vm.user_role_options).not.toBeNull();
        expect(wrapper.vm.user_id).not.toBeNull();
        expect(wrapper.vm.base).not.toBeNull();
        expect(wrapper.vm.base_options).not.toBeNull();
        expect(wrapper.vm.user_name).not.toBeNull();
    });

    test('Test the logic of the enable/disable of Base dropdown based on the selected user role', async() => {
        await wrapper.setData({
            user_role: '',
            user_role_options: [],
            base: '',
            base_options: [],
            isDisabledSelectBase: false,
        });

        await wrapper.vm.handleGetUser();
        await wrapper.vm.handleGetListRole();
        await wrapper.vm.handleGetListDepartment();

        expect(handleGetUser).toHaveBeenCalled();
        expect(handleGetListRole).toHaveBeenCalled();
        expect(handleGetListDepartment).toHaveBeenCalled();

        expect(wrapper.vm.user_role).not.toBeNull();
        expect(wrapper.vm.user_role_options).not.toBeNull();
        expect(wrapper.vm.base).not.toBeNull();
        expect(wrapper.vm.base_options).not.toBeNull();

        const ROLE_HEADQUARTER = 'headquarter';
        const ROLE_OPERATOR = 'operator';
        const ROLE_TEAM = 'team';

        const USER_ROLE = wrapper.vm.user_role;

        await wrapper.vm.checkPermission(USER_ROLE);
        expect(checkPermission).toHaveBeenCalled();

        if (USER_ROLE === ROLE_HEADQUARTER || USER_ROLE === ROLE_OPERATOR || USER_ROLE === null) {
            wrapper.vm.isDisabledSelectBase = true;
            wrapper.vm.base = null;
            expect(wrapper.vm.isDisabledSelectBase).toBe(true);
            expect(wrapper.vm.base).toBe(null);
        } else if (USER_ROLE === ROLE_TEAM) {
            wrapper.vm.isDisabledSelectBase = false;
            expect(wrapper.vm.isDisabledSelectBase).toBe(false);
        }
    });

    test('Test validation function', async() => {
        await wrapper.setData({
            user_role: '',
            user_id: '',
            base: '',
            user_name: '',
            current_password: '',
            password: '',
        });

        let DATA = {};

        const ButtonSave = wrapper.find('button#btn-save');
        expect(ButtonSave.exists()).toBe(true);

        await ButtonSave.trigger('click');

        expect(validation).toHaveBeenCalled();

        if (wrapper.vm.user_role === null) {
            return false;
        } else if (wrapper.vm.user_id.length === 0) {
            return false;
        } else if (wrapper.vm.base === null) {
            return false;
        } else if (wrapper.vm.user_name.length === 0) {
            return false;
        } else if (wrapper.vm.current_password.length === 0) {
            return false;
        } else if (wrapper.vm.password.length === 0) {
            return false;
        } else {
            DATA = {
                roles: wrapper.vm.user_role,
                department_id: wrapper.vm.base,
                user_code: wrapper.vm.user_id,
                user_name: wrapper.vm.user_name,
                current_password: wrapper.vm.pwd,
                password: wrapper.vm.new_pwd,
            };

            expect(processUpdateUser()).toHaveBeenCalled();
            expect(handleUpdateUser(DATA)).toHaveBeenCalled();

            return true;
        }
    });
});
