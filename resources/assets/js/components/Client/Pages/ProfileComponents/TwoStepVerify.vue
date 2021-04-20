<template>
	
	<transition name="page" mode="out-in">

		<div id="content1" class="site-content col-md-12">

			<div class="row" :key="counter">

				<div class="col-sm-12" v-if="loading">
					
					<custom-loader :duration="4000" :color="layout.portal.client_header_color"></custom-loader>
				</div>

				<div class="col-sm-8 offset-md-2" v-if="!loading">

					<div id="form-border" class="comment-respond form-border">

						<h3 id="reply-title" class="comment-reply-title section-title">

							<i class="line" :style="lineStyle"></i>{{lang('2fa_setup')}}
						</h3> 

						<div class="row">

							<div class="col-sm-9">

								<span class="d_flex">
								
									<img class="img-responsive img-sm" :src="basePath()+'/themes/default/common/images/authenticator.png'" alt="A"
										id="auth_img">&nbsp;
										{{two_factor ? '2-Step Verification is ON since '+formattedTime(authData.user_data.google2fa_activation_date)  : lang('authenticator_app')}}
								</span>
							</div>

							<div class="col-sm-3">
								
								<button v-if="!two_factor" type="button" class="btn btn-primary pull-right" @click="showModal = true">

									<i class="fa fa-toggle-on"></i> {{lang('turn_on')}}

								</button>

								<button v-if="two_factor" type="button" class="btn btn-danger pull-right" @click="removeModal = true">

									<i class="fa fa-power-off"></i> {{lang('turn_off')}}

								</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<transition name="modal">
				
				<barcode-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :color="layout.portal.client_header_color"
				:btnStyle="btnStyle" :linkStyle="linkStyle" from="client">
					
				</barcode-modal>
			</transition>

			<transition name="modal">
				
				<remove-modal v-if="removeModal" :onClose="onClose" :showModal="removeModal" alertName="edit_profile" 
					:color="layout.portal.client_header_color" :btnStyle="btnStyle">
					
				</remove-modal>
			</transition>

		</div>
	</transition>
</template>

<script>
	
	import { mapGetters } from 'vuex';

	export default {

		name : 'two-step',

		description : 'Two step verifiction Setup page',

		props : {

			layout : { type : Object, default : ()=>{}},
			
			auth : { type : Object, default : ()=>{}},
		},

		data () {

			return {

				two_factor : false,

				showModal : false,

				removeModal : false,

				loading : false,

				lineStyle : { borderColor : this.layout.portal.client_header_color },

				linkStyle : { color : this.layout.portal.client_header_color },

				btnStyle : {

					borderColor : this.layout.portal.client_button_border_color,

					backgroundColor : this.layout.portal.client_button_color
				},

				counter : 0
			}
		},

		beforeMount(){

			this.two_factor = this.authData.user_data.is_2fa_enabled;
		},

		created(){
			window.eventHub.$on('updateEditData', this.updateUserData);
		},

		computed : {

			authData() {

				return this.auth
			},

			...mapGetters(['formattedTime'])
		},

		methods : {

			updateUserData(value){

				this.two_factor = value;

				this.$store.dispatch('deleteUser');

				this.authData.user_data['is_2fa_enabled'] = value
						
				this.authData.user_data['google2fa_activation_date'] = new Date();

      		this.$store.dispatch('updateUserData',this.authData);

      		this.counter++;
      },

			onClose(){
				
				this.showModal = false;

				this.removeModal = false; 

				this.$store.dispatch('unsetValidationError');
			},
		},

		components : {

			'barcode-modal' : require('components/Agent/Profile/BarcodeModal'),

			'remove-modal' : require('components/Agent/Profile/RemoveVerification'),

			'custom-loader' : require('components/MiniComponent/Loader'),
		}
	};
</script>

<style scoped>
	
	#content1 {

		margin-top: 20px !important;
	}

	#form-border{
		background: transparent !important;
	}

	#auth_img{
		margin-top: -2px;
		width: 25px!important;
		height: 25px!important;
	}

	.d_flex{
		display: flex;
	}
</style>