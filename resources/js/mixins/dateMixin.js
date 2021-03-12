import * as PersianDate from 'persian-date'

export default {
    methods: {
        shamsiDate(miladiDate) {
            return {
                dateTime: new PersianDate(new Date(miladiDate)).format('HH:mm:ss YYYY/MM/DD'),
                date: new PersianDate(new Date(miladiDate)).format('YYYY/MM/DD'),
                time: new PersianDate(new Date(miladiDate)).format('HH:mm:ss'),
            }
        }
    }
};
