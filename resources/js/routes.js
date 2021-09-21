import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const router = new VueRouter({
	mode: 'history',
	routes: [
		{ path: '/', redirect: { name: 'discover' } },
		{ path: '/download', redirect: { name: 'download-a' } },

		
		{ path: '/download/a', name: 'download-a', component: () => import('@/js/components/public/templates/download/c1') },
		{ path: '/download/a/v2', name: 'download-a-v2', component: () => import('@/js/components/public/templates/download/c2') },

		 { path: '/thankyou/:id', name: 'thankyou', component: () => import('@/js/components/public/templates/thankyou/thankyou') },

		// 404
		{ path: "*", component: function () {
			window.location.href = "/";
		} }
	]
})


export default router;