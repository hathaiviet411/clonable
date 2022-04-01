export function handleListPermissions(permissions) {
    const PERMISSIONS = [];

    const len = permissions.length;
    let index = 0;

    while (index < len) {
        PERMISSIONS.push(permissions[index]['name']);

        index++;
    }

    return PERMISSIONS;
}
