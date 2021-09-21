<template>
	<div>
		<div id="app"></div>
		<router-view></router-view>
	</div>
</template>

<script>
export default {
	name: 'app',
	metaInfo: {
		title: 'index',
		titleTemplate: '%s - Really Social',
	},
	udpated () {
	}
}
</script>