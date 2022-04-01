export function getAllIdOption(array, key) {
    if (!key) {
        return [];
    }

    const result = [];

    let index = 0;
    const len = array.length;

    while (index < len) {
        result.push(array[index][key]);

        index++;
    }

    return result || [];
}

export function isExitOption(array = [], value) {
    return array.includes(value);
}
