import { validateYYYYMMDD } from '@/utils/validate';

export function handleChooseDate(from, to, type) {
    if ((!from && validateYYYYMMDD(to)) || (validateYYYYMMDD(from) && !to)) {
        return 0;
    }

    if (validateYYYYMMDD(from) && validateYYYYMMDD(to)) {
        if (new Date(from) > new Date(to)) {
            switch (type) {
                case 'FROM': {
                    return 1;
                }

                case 'TO': {
                    return 2;
                }

                default: {
                    return -1;
                }
            }
        }

        if (new Date(to) >= new Date(from)) {
            return 0;
        }
    }

    return -1;
}

/**
 * Function format month 2 digit
 * @param {Number} m
 * @returns String | 01, 10
 */
export function formatMonth(m) {
    if (!m) {
        return '';
    }

    return m < 10 ? '0' + m : m;
}

/**
 * Function change 2000/10 -> to Object
 * @param {String} value
 * @returns Object | { year: 2000, month: 10 }
 */
export function getObjectYM(value) {
    if (!value) {
        return '';
    }

    const SPLIT = value.split('/');

    if (SPLIT.length === 2) {
        return {
            year: SPLIT[0] || '',
            month: SPLIT[1] || '',
        };
    }

    return {
        year: '',
        month: '',
    };
}

/**
 * Function change format Year/Month-> Year-Month
 * @param {*} value
 * @returns String | 2000-10
 */
export function formatBindingYM(value) {
    if (!value) {
        return '';
    }

    return value.replace(/(\d{4})[/](\d{1,2})/, function(match, y, m) {
        return `${y}-${m}`;
    });
}

/**
 * Function change format Year/Month-> Year-Month
 * @param {*} value
 * @returns String | 2000-10
 */
export function formatSetYM(value) {
    if (!value) {
        return '';
    }

    return value.replace(/(\d{4})-(\d{1,2})/, function(match, y, m) {
        return `${y}/${m}`;
    });
}
