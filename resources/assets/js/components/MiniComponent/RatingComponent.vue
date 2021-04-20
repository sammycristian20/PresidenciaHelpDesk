<template>

	<div class="row">
		
		<div class="col-md-12">

			<div :class="{left1: lang_locale == 'ar'}" class="ticketratings" id="ticketratings"> &nbsp;

				<table>

					<tbody>

						<tr v-if="matched_rating.id">

							<th><div class="ticketratingtitle" :style="labelStyle">{{ matched_rating.name }}&nbsp;</div></th>

							<td  class="star-rating-control">

								<div>

									<star-rating v-model="rating_value" :star-size="18" :show-rating="false" :max-rating="matched_rating.rating_scale"
									:read-only="true"></star-rating>

								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>
<script>

import { findObjectByKey } from 'helpers/extraLogics'

import { mapGetters } from 'vuex'

	export default {

		props:{

			/**
			 * rating data
			 * @type {Object}
			 */
			rating : {type : Object},

			ratings : {type : Array},

			labelStyle : { type : Object, default : ()=> {} },

		},

		data() {
			return {
				/**
				 * getting matched rating with props data
				 * @type {Object}
				 */
				matched_rating:{},

				/**
				 * rating value
				 * @type {Number}
				 */
				rating_value:0,

				/**
				 * locale of the user
				 * @type {String}
				 */
				lang_locale:''
			}
		},

		watch:{
			// getting updated values using watch function
			rating(newValue,oldValue) {

				console.log(newValue,oldValue,'watch')
				return newValue;
			},

		},

		computed: {
			// getting rating types from vuex
			...mapGetters(['getRatingTypes'])
		},

		created() {
			// locale of the user
			this.lang_locale = localStorage.getItem('LANGUAGE');

			// matching rating value with system ratings to getting values
			if(this.rating !== undefined){

				this.rating_value = this.rating.rating_value

				this.matched_rating = findObjectByKey(this.getRatingTypes, 'id',this.rating.id);

			}

		},

		methods: {

		}

	};
</script>

<style scoped>

#ticketratings{
	float: right; margin-top: -10px;
}

.left1 {
	float :left !important;
}

.ticketratingtitle { font-weight: 400 !important; }
</style>
