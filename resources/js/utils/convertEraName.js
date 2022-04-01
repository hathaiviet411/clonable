const ERA_NAME = [
    {
        start: 1926,
        end: 1988,
        name: '昭和',
    },
    {
        start: 1989,
        end: 2018,
        name: '平成',
    },
    {
        start: 2019,
        end: 2030,
        name: '令和',
    },
];

const YEAR_MIN = findBy(ERA_NAME, 'start', minComp).start;
const YEAR_MAX = findBy(ERA_NAME, 'end', maxComp).end;
const YEAR_TEXT = '年';

export function convertEraName(year = 0) {
    year = parseInt(year);

    if (!year) {
        return '';
    }

    let idx = 0;
    const len = ERA_NAME.length;
    let result = '';

    while (idx < len) {
        const item = ERA_NAME[idx];

        if (year >= YEAR_MIN && year <= YEAR_MAX) {
            if (year >= item.start && year <= item.end) {
                result = `${year + YEAR_TEXT} (${item.name}${year - item.start + 1}${YEAR_TEXT})`;

                return result;
            }
        } else {
            result = '';
        }

        idx++;
    }

    return year + YEAR_TEXT;
}

function findBy(arr, key, comparatorFn) {
    return arr.reduce(function(prev, curr, index, arr) {
        return comparatorFn.call(arr, prev[key], curr[key]) ? prev : curr;
    });
}

function minComp(prev, curr) {
    return prev < curr;
}

function maxComp(prev, curr) {
    return prev > curr;
}
