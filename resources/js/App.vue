<template>
	<v-app>
		<v-navigation-drawer app> </v-navigation-drawer>
		<v-toolbar app></v-toolbar>
		<v-content>
			<v-container fluid>
				<router-link tag="li" to="/">Home</router-link>
				<router-view></router-view>
			</v-container>
		</v-content>
	</v-app>
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