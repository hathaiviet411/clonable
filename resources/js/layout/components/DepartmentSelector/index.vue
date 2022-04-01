<template>
	<div>
		<b-dropdown
			v-if="!($store.getters.roles[0] === 'team')"
			id="department-selector"
			text="Department Selector"
			block
			class="dropdown-scrollable"
			right
		>
			<template #button-content>
				<span class="department-name">{{ currentDepartmentName }}</span>
			</template>

			<b-dropdown-item
				v-for="(department, index) in ListDepartment"
				:key="index"
				class="text"
				style="text-align: center;"
				:disabled="department.disabled"
				@click="handleSelectDepartment(department.department_id, department.department_name)"
			>
				<span class="department-name">{{ (department.department_name) }}</span>
			</b-dropdown-item>

		</b-dropdown>

		<b-button v-else id="department-selector" block class="department-card" right>
			<span class="department-name">{{ currentDepartmentName }}</span>
		</b-button>
	</div>
</template>

<script>

export default {
    name: 'DepartmentSelector',
    data() {
        return {
            ListDepartment: [],
            currentDepartmentID: '',
            currentDepartmentName: '',
        };
    },
    created() {
        this.handleGetListDepartment();
    },
    methods: {
        handleGetListDepartment() {
            this.ListDepartment = this.$store.getters.listDepartment;
            this.currentDepartmentName = this.$store.getters.department_name;
            this.currentDepartmentID = this.$store.getters.department_id;
            this.disableSelectedDepartment(this.currentDepartmentID);
        },

        disableSelectedDepartment(department_id) {
            for (let index = 0; index < this.ListDepartment.length; index++) {
                if (this.ListDepartment[index].department_id === department_id) {
                    this.ListDepartment[index].disabled = true;
                } else {
                    this.ListDepartment[index].disabled = false;
                }
            }
        },

        handleSelectDepartment(department_id, department_name) {
            this.currentDepartmentID = department_id;
            this.$store.dispatch('department/setDepartment', {
                department_id: department_id,
                department_name: department_name,
            });
            this.disableSelectedDepartment(department_id);
            this.currentDepartmentName = this.$store.getters.department_name;
        },
    },
};
</script>

<style scoped>
    .dropdown-scrollable /deep/ .dropdown-menu {
        max-height: 300px;
        overflow-y: auto;
    }

    .department-name {
        font-weight: bold;
    }

    .department-card {
        min-width: 160px;
        background: #c55730;
    }

    .department-card:hover {
        background: #c55730;
        cursor: pointer;
    }

    .department-card:focus {
        background: #c55730;
    }

    .department-card:active {
        background: #c55730;
    }
</style>
