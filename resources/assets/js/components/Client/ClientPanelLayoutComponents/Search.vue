<template>

	<form @submit.prevent class="search-form" role="search">
			
		<div class="form-border">

			<div class="form-inline">
					
				<div class="form-group input-group" style="width: 100%;">
						
					<input type="text" name="s" class="search-field form-control input-lg" v-model="search" 
						:class="[(language === 'ar') ? 'ml-3' : 'mr-3']" @keypress="searchOnEnter"
						title="Enter search term" :placeholder="lang('have_a_question?_type_your_search_term_here')"/>

					<span class="input-group-btn">
						
						<button type="submit" class="btn btn-custom btn-md" :style="btnStyle" 
							:class="{search_button : language === 'ar'}" @click="searchOnClick()">{{lang('search')}}
						</button>
					</span>
				</div>												
			</div>
		</div>
	</form>
</template>

<script>
	
	export default {

		name : 'client-panel-header-search',

		description : 'Client panel search component',

		props : {

			layout : { type : Object , default : ()=>{}},
		},

		data(){

			return {

				search : '',

				border_color : this.layout.portal.client_input_field_color,

				language : this.layout.language,

				btnStyle : {

					borderColor : this.layout.portal.client_button_border_color,

					backgroundColor : this.layout.portal.client_button_color,
				} 
			}
		},

		watch : {

			'$route.path'(newValue,oldValue){

				if(newValue != '/search'){

					this.search = '';
				}
			}
		},

		methods : {

			searchOnClick(){

				this.$Progress.start();

				if(this.search){

					this.$router.replace({ name: "SearchResult",path: '/search', query: { s: this.search }});

					// this.search='';

					this.$Progress.finish();
				}
			},

			searchOnEnter(e){

				if(e.keyCode === 13 || e.which === 13){

					this.searchOnClick()
				}
			}
		}
	};
</script>
<style scoped>
	
	.input-group>.form-control:not(:last-child) {
    border-top-right-radius: 5px !important;
    border-bottom-right-radius: 5px !important;
}
	.form-border{

		z-index: 1 !important;
	}
</style>
