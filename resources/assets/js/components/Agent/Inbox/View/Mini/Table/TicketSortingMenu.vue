<template>
	
	<div class="btn-group float-right" id="ticket-sort">
		
		<button type="button" class="btn btn-sm btn-default dropdown-toggle sortval" data-toggle="dropdown" 
			:style="buttonStyle">
			<i class="fas fa-sort"></i>&nbsp;
		</button>
		
		<div class="dropdown-menu dropdown-menu-right" style="">
				
			<template v-for="(sorting,index) in sortingMenu">
				
				<a class="dropdown-item" @click="sort(sorting,index)" href="javascript:;">
					
					<i :class="sorting.class"> </i> {{sorting.name}} in {{sorting.order_name}}
				</a>
			</template>
		</div>
	</div>
</template>

<script>
	
	export default {
	
	props : {

		tableHeader : { type : String, default : ''},
	},

	data() {
		
		return {

			buttonStyle : { 'background-color':this.tableHeader + ' !important','color':'#fff', 'margin-top' : '2px' },

			sortingMenu: [
				{
					name: "Ticket Number",
					value: "ticket_number",
					order: "asc",
					class: "fas fa-sort-amount-down",
					order_name: "Ascending"
				},
				{
					name: "Updated Date",
					value: "updated_at",
					order: "asc",
					class: "fas fa-sort-amount-down",
					order_name: "Ascending"
				},
				{
					name: "Created Date",
					value: "created_at",
					order: "asc",
					class: "fas fa-sort-amount-down",
					order_name: "Ascending"
				}
			],
		};
	},

	methods: {

		sort(x, y) {
		
			if (x.order == "asc") {
		
				this.$emit("sort", x);
		
				$(".sortval").html(
					x.name + " in " + x.order_name + '&nbsp<span class="caret"></span>'
				);
		
				this.sortingMenu[y].order = "desc";
		
				this.sortingMenu[y].class = "fas fa-sort-amount-up";
		
				this.sortingMenu[y].order_name = "Descending";
			} else {
		
				this.$emit("sort", x);
		
				$(".sortval").html(
		
					x.name + " in " + x.order_name + '&nbsp<span class="caret"></span>'
				);
		
				this.sortingMenu[y].order = "asc";
		
				this.sortingMenu[y].class = "fas fa-sort-amount-down";
		
				this.sortingMenu[y].order_name = "Ascending";
			}
		}
	}
};
</script>