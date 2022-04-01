/**
 * Function helper pagination
 * @param {Array} arr
 * @param {Number} size
 * @returns List array with pagination
 */
export function handlePaginate(arr, size) {
    return arr.reduce((acc, val, i) => {
        const idx = Math.floor(i / size);
        const page = acc[idx] || (acc[idx] = []);
        page.push(val);

        return acc;
    }, []);
}

export function getLisIndexBreak(arr, size) {
    const pagination = arr.reduce((acc, val, i) => {
        const idx = Math.floor(i / size);
        const page = acc[idx] || (acc[idx] = []);
        page.push(val);

        return acc;
    }, []);

    let idx = 0;
    const len = pagination.length;
    const listIndex = [];

    while (idx < len) {
        const arr = pagination[idx];

        if (arr.length === size && idx < (len - 1)) {
            const last = ((arr.length - 1) + (size * idx));
            listIndex.push(last);
        }

        idx++;
    }

    return listIndex;
}
