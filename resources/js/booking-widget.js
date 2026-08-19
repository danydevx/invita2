import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap'

import { createApp } from 'vue'
import BookingWidget from './Components/Public/BookingWidget.vue'

const mount = (el) => {
    const props = {
        initialBusinessSlug: el.dataset.businessSlug || '',
        apiBase: el.dataset.apiBase || '/api/book',
    }

    createApp(BookingWidget, props).mount(el)
}

const elements = document.querySelectorAll('[data-booking-widget]')
elements.forEach(mount)