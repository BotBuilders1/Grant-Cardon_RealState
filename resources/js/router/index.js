import Vue from 'vue';
import Router from 'vue-router';

import publicPages from './public.js';


Vue.use(Router);


const ROUTES = [
	{
		path: '',
		redirect: '/'
	}
]

let router = new Router({
	base: '/',
	mode: 'history',
	routes: ROUTES
})

/**
 * called ager each route
 **/
router.afterEach(() => {
	// on small screen will adjust here
})


export default router