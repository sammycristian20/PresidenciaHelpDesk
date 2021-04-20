<template>
	
	<div class="input-group color-picker" ref="colorpicker">
	
	<input type="text" class="form-control" v-model="colorValue" @focus="showPicker()" @input="updateFromInput" :disabled="disabled" />
	
	<span class="input-group-addon color-picker-container">
	
		<span class="current-color" :style="'background-color: ' + colorValue" @click="togglePicker()"></span>
	
		<chrome-picker :value="colors" @input="updateFromPicker" v-if="displayPicker" />
	
	</span>
</div>
</template>
	
<script>
	
	import { Chrome } from 'vue-color'

	export default {

		props: ['color', 'disabled'],
		
		data() {
		
			return {
			
				colors: {
					hex: '#000000',
				},
			
				colorValue: '',
			
				displayPicker: false,
			}
		},

		mounted() {

			this.setColor(this.color || '#000000');
		},

		methods: {
			
			setColor(color) {

				this.updateColors(color);

				this.colorValue = color === 'null' || color === null ? '#000000' : color;
			},
		
			updateColors(color) {
				
				if(color.slice(0, 1) == '#') {
				
					this.colors = {
						hex: color
					};
				}
				else if(color.slice(0, 4) == 'rgba') {
				
					var rgba = color.replace(/^rgba?\(|\s+|\)$/g,'').split(','),
					
					hex = '#' + ((1 << 24) + (parseInt(rgba[0]) << 16) + (parseInt(rgba[1]) << 8) + parseInt(rgba[2])).toString(16).slice(1);
					
					this.colors = {
						hex: hex,
						a: rgba[3],
					}
				}
			},
		
			showPicker() {

				if(this.disabled) {
					return;
				}
				
				document.addEventListener('click', this.documentClick);
				
				this.displayPicker = true;
			},
		
			hidePicker() {
				
				document.removeEventListener('click', this.documentClick);
				
				this.displayPicker = false;
			},
			
			togglePicker() {

				if(this.disabled) {
					return;
				}
				
				this.displayPicker ? this.hidePicker() : this.showPicker();
			},
			
			updateFromInput() {
			
				this.updateColors(this.colorValue);
			},
		
			updateFromPicker(color) {
				this.colors = color;
				
				if(color.rgba.a == 1) {
					this.colorValue = color.hex;
				}
				else {
				
					this.colorValue = 'rgba(' + color.rgba.r + ', ' + color.rgba.g + ', ' + color.rgba.b + ', ' + color.rgba.a + ')';
				}
			},
			
			documentClick(e) {
				
				var el = this.$refs.colorpicker,
				target = e.target;
				if(el !== target && !el.contains(target)) {
					this.hidePicker()
				}
			}
		},
		
		watch: {
			colorValue(val) {
				if(val) {
					this.updateColors(val);
					this.$emit('input', val);
				}
			}
		},

		components: {
			'chrome-picker': Chrome,
		},

	};
</script>

<style>
	.vc-chrome {
		position: absolute;
		top: 35px;
		right: 0;
		left : 0;
		width : 50%;
		z-index: 9;
	}
	.current-color {
		display: inline-block;
		width: 16px;
		height: 16px;
		background-color: #000;
		cursor: pointer;
		margin: 9px;
		margin-bottom: 0px !important;
	}
	.footer {
		margin-top: 20px;
		text-align: center;
	}
	.input-group-addon { 
		border-bottom-right-radius: 0.25rem;
	   border: 1px solid #ced4da !important;
	   border-top-right-radius: 0.25rem;
	}
</style>