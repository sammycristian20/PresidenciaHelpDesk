<template>

	<div class="col-sm-4 margin-bottom-15">

		<label>{{label}}</label>

		<v-select
			multiple
			class='faveo-dynamic-select'
			ref='faveoDynamicSelect'
			:id="api+keyname"
			v-model="filter_selected"
			label="name"
			:options="filter_options"
			:filterable="false"
			@input="function(val) { tagsChanged(keyname,val); }"
			@search="onSearch"
			@open="onOpen"
			@close="onClose"
			@search:blur="clearSearchQuery"
		>
			<template slot="no-options" slot-scope="{search, loading, searching}">
				<template v-if="searching">No results found for <em>{{ search }}</em>. </template>
				<template v-else><span v-if="!isLoading">No options found.</span> </template>
			</template>

			<template slot="option" slot-scope="option">

				<div class="d-center">

					<faveo-image-element v-if="option.profile_pic" id="faveo-dynamic-select-img" :source-url="option.profile_pic" alternative-text="User"/>

					{{ option.name }}

				</div>
			</template>

			<template slot="selected-option" slot-scope="option">

				<div class="selected d-center">

					<faveo-image-element id="faveo-dynamic-select-img" v-if="option.profile_pic" :source-url="option.profile_pic"/>

					{{ option.name }}
				</div>
			</template>

			<template slot="list-footer" v-if="hasNextPage">
				<li ref="load" class="loader-area">
					<loader :duration="4000" :size="25"></loader>
				</li>
			</template>
		</v-select>

	</div>
</template>
<script>

	import vSelect from "vue-select";

	import { boolean } from 'helpers/extraLogics';

	import _ from 'lodash';

		export default {

			props: {
				label: {
					type: String,
					required: true
				},
				api: {
					type: String,
					required: true
				},
				keyname: {
					type: String
				},
				base: {
					type: String
				},
				meta: {
					type: Boolean
				},

				/**
			 * selected options(Gets updated to `filter_selected` as soon as its value changes) (Array of objects)
			 * @type {Array}
			 */
			value: {
				type: Array
			},

			/**
			 * method that gets called as soon as any option is selected here, to notify parent about the updated values
			 * @type {Function}
			 */
			updateFilter: {
				type: Function
			},

			/**
			 * If this component is required to be reset
			 * @type {Boolean}
			 */
			reset: {
				type: Boolean
			}
		},

		data(){
			return{
				filter_options:[],
				filter_selected:[],
				filter_obj: {},
				isLoading: false,
				page: 0,
				observer: null,
				next_page_url: '',
				searchQuery: ''
			}
		},

		components: {
			'v-select': vSelect,
			'faveo-image-element': require('components/Common/FaveoImageElement'),
			'loader': require('components/Extra/Loader'),
		},

		mounted() {
			this.observer = new IntersectionObserver(this.infiniteScroll);
		},

		watch: {

			value(val){
				/**
				 * Triger `input` event if value changed
				 * Used in case of autofill the selected values
				 * Though it's not a good practice to emit event of plugin wrapper, we don't have any solution to update the sected valyes to the parent
				 * This should be ommited when this component will be rewritten
				 */
				this.$refs.faveoDynamicSelect.$emit("input", val);
			},

			reset(val){
				if(val){
					this.filter_selected = [];
					this.filter_obj = {};
				}
			}
		},

		computed: {
			hasNextPage() {
				return this.next_page_url !== null;
			}
		},

		methods: {

			onSearch(searchQuery) {
				this.page = 1;
				this.searchQuery = searchQuery;
				this.search(this);
			},

			search: _.debounce((vm) => {
				vm.getDataFromServer(true);
			}, 350),

			getDataFromServer(isRefresh, target) {
				this.isLoading = true;
				axios.get('api/' + this.base + '/' + this.api, { params: this.getApiParams() })
				.then((response) => {
					this.postApiResponseOperations(response.data.data, isRefresh, target)
				})
				.catch((error) => {
					console.log(error);
				})
				.finally(() => {
					this.isLoading = false;
				})
			},

			async postApiResponseOperations(responseData, isRefresh, target) {
				this.next_page_url = responseData.next_page_url;
				if(isRefresh) {
					this.filter_options = responseData.data;
				} else {
					const ul = target.offsetParent;
      		const scrollTop = target.offsetParent.scrollTop;
					this.filter_options.push(...responseData.data);
					await this.$nextTick();
      		ul.scrollTop = scrollTop;
				}
			},

			getApiParams() {
				return {
					'search-query': this.searchQuery || undefined,
					'page': this.page || undefined,
					'meta': this.meta || undefined,
					'paginate': 1
				}
			},

			clearSearchQuery() {
				this.$refs.faveoDynamicSelect.onEscape()
			},

			//tag changed
			tagsChanged(x,arrayList){
				if(arrayList.length!=0){
					var filterArray=[];
					for(var i in arrayList){
						filterArray.push(arrayList[i].id);
					};
					this.filter_obj[x]=filterArray;
				}
				else{
					this.filter_obj[x]=[];
				}

				this.$emit('selected',this.filter_obj);
				let selectedFilterWithKey = {};
				selectedFilterWithKey[this.keyname] = this.filter_selected;
				this.updateFilter(selectedFilterWithKey);
			},

			async infiniteScroll ([{isIntersecting, target}]) {
				if (isIntersecting) {
					this.page += 1;
					this.getDataFromServer(false, target)
				}
			},

			async onOpen(something) {
				if (this.hasNextPage) {
					await this.$nextTick();
					this.observer.observe(this.$refs.load);
				}
			},

			onClose() {
				this.observer.disconnect();
			},
		}
	}
</script>

<style scoped>
#faveo-dynamic-select-img {
	border: 1px solid transparent;
	width: 18px;
	border-radius: 10px;
	margin-top: -1px;
}

.loader-area {
	padding-top: 3px;
	height: 37px;
}

</style>
