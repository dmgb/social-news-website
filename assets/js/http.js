import axios from 'axios';

export const http = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
    },
});
