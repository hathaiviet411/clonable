<?php

define('DEFAULT_ZERO_PAD', 5);
define('DEFAULT_STR_ZERO', '0');

define('CODE_SUCCESS', 200);
define('CODE_CREATE_FAILED', 201);
define('CODE_DELETE_FAILED', 202);
define('CODE_MULTI_STATUS', 207);
define('CODE_NO_ACCESS', 403);
define('CODE_NOT_FOUND', 404);
define('CODE_ERROR_SERVER', 500);
define('CODE_UNAUTHORIZED', 401);

define('IMAGE', 'upload/image');

define("TEMP_PASS", '123456789');


define("PATH_UPLOAD_DATA_ITEM", 'data_item');


define("ROLE_HEADQUARTER", 'headquarter');
define("ROLE_OPERATOR", 'operator');
define("ROLE_TEAM", 'team');

define("PERMISSION_ACCESSORIES_CREATE", 'accessories_create');
define("PERMISSION_ACCESSORIES_DELETE", 'accessories_delete');
define("PERMISSION_ACCESSORIES_EDIT", 'accessories_edit');
define("PERMISSION_ACCESSORIES_LIST", 'accessories_list');

define("PERMISSION_MAINTENANCE_CREATE", 'maintenance_create');
define("PERMISSION_MAINTENANCE_DELETE", 'maintenance_delete');
define("PERMISSION_MAINTENANCE_DETAIL", 'maintenance_detail');
define("PERMISSION_MAINTENANCE_EDIT", 'maintenance_edit');
define("PERMISSION_MAINTENANCE_LIST", 'maintenance_list');

define("PERMISSION_MAINTENANCE_RESULT_CREATE", 'maintenance_result_create');
define("PERMISSION_MAINTENANCE_RESULT_DELETE", 'maintenance_result_delete');
define("PERMISSION_MAINTENANCE_RESULT_DETAIL", 'maintenance_result_detail');
define("PERMISSION_MAINTENANCE_RESULT_EDIT", 'maintenance_result_edit');
define("PERMISSION_MAINTENANCE_RESULT_LIST", 'maintenance_result_list');

define("PERMISSION_MAINTENANCE_SCHEDULE_AND_RESULTS_EXPORT", 'maintenance_schedule_and_results_export');
define("PERMISSION_MAINTENANCE_SCHEDULE_AND_RESULTS_LIST", 'maintenance_schedule_and_results_list');

define("PERMISSION_MAINTENANCE_SCHEDULE_CREATE", 'maintenance_schedule_create');
define("PERMISSION_MAINTENANCE_SCHEDULE_DELETE", 'maintenance_schedule_delete');
define("PERMISSION_MAINTENANCE_SCHEDULE_DETAIL", 'maintenance_schedule_detail');
define("PERMISSION_MAINTENANCE_SCHEDULE_EDIT", 'maintenance_schedule_edit');
define("PERMISSION_MAINTENANCE_SCHEDULE_LIST", 'maintenance_schedule_list');

define("PERMISSION_USER_CREATE", 'user_create');
define("PERMISSION_USER_DELETE", 'user_delete');
define("PERMISSION_USER_EDIT", 'user_edit');
define("PERMISSION_USER_LIST", 'user_list');

define("PERMISSION_VEHICLE_CREATE", 'vehicle_create');
define("PERMISSION_VEHICLE_DELETE", 'vehicle_delete');
define("PERMISSION_VEHICLE_DETAIL", 'vehicle_detail');
define("PERMISSION_VEHICLE_EDIT", 'vehicle_edit');
define("PERMISSION_VEHICLE_LIST", 'vehicle_list');


//"1. 3 month | 2. 12 month | 3.  accessory change | 4. other"

define("TYPE_THREE_MONTH", 1);
define("TYPE_TWELVE_MONTH", 2);
define("TYPE_ACCESSORY_CHANGE", 3);
define("TYPE_OTHER", 4);

define("LIST_TYPE", [
    TYPE_THREE_MONTH => '3ヶ月点検',
    TYPE_TWELVE_MONTH => '12ヶ月点検',
    TYPE_ACCESSORY_CHANGE => '部品交換',
    TYPE_OTHER => 'その他',
]);

//"charge_type": "1:external | 2: Internal"
define("CHARGE_TYPE_EXTERNAL", 1);
define("CHARGE_TYPE_INTERNAL", 2);


//・未入力 Not inputted
//・入力中 In inputting
//・入力済 Inputted
define("STATUS_NOT_INPUTTED", 1);
define("STATUS_IN_INPUTTING", 2);
define("STATUS_INPUTTED", 3);


define("LIST_STATUS", [
    STATUS_NOT_INPUTTED => '未入力',
    STATUS_IN_INPUTTING => '入力中',
    STATUS_INPUTTED => '入力済',
]);

define("VEHICLE_BATTERY_3_ID", 27);
define("VEHICLE_BATTERY_4_ID", 97);
define("VEHICLE_BATTERY_3_TEXT", 'バッテリー');
define("VEHICLE_BATTERY_4_TEXT", 'バッテリー');

define("VEHICLE_START_MOTOR_3_ID", 31);
define("VEHICLE_START_MOTOR_4_ID", 88);
define("VEHICLE_START_MOTOR_3_TEXT", 'セルモータ');
define("VEHICLE_START_MOTOR_4_TEXT", 'セルモータ');

define("VEHICLE_ALTERNATOR_3_ID", 150);
define("VEHICLE_ALTERNATOR_4_ID", 151);
define("VEHICLE_ALTERNATOR_3_TEXT", 'オルタネーター');
define("VEHICLE_ALTERNATOR_4_TEXT", 'オルタネーター');

define("LIST_ID_ACCESSORY", [
        VEHICLE_BATTERY_3_ID,
        VEHICLE_BATTERY_4_ID,
        VEHICLE_START_MOTOR_3_ID,
        VEHICLE_START_MOTOR_4_ID,
        VEHICLE_ALTERNATOR_3_ID,
        VEHICLE_ALTERNATOR_4_ID
]);

define("LIST_KEY_ACCESSORY", [
    VEHICLE_BATTERY_3_ID => 'battery',
    VEHICLE_BATTERY_4_ID => 'battery',
    VEHICLE_START_MOTOR_3_ID => 'starter_motor',
    VEHICLE_START_MOTOR_4_ID => 'starter_motor',
    VEHICLE_ALTERNATOR_3_ID => 'alternator',
    VEHICLE_ALTERNATOR_4_ID => 'alternator'
]);

define("CLOUD_URL", [
    "local" => "https://izumi-cloud.vw-dev.com/",
    "staging" => "https://cloud-stage.izumilogi.com/",
    "production" => "https://cloud.izumilogi.com/"
]);
