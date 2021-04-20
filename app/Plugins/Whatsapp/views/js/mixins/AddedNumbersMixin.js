export const AddedNumbersMixin = {
    data() {
        return {

            apiEndPoint: '/whatsapp/api/accounts',
            columns: ['name', 'sid', 'token', 'action'],
            options: {},
        }
    },

    beforeMount() {

        this.options = {

				headings: { name: 'Name', sid : 'Account SID', token : 'Auth Token', action:'Action'},
				
				columnsClasses : {

					name: 'name', 

					sid: 'sid', 

					token: 'token',

					action: 'action',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-up',
						
					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

				templates: {
					action: 'table-actions'
				},

				sortable:  ['name', 'sid', 'token'],
				
				filterable:  ['name', 'sid', 'token'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
	      
                    return {
                    
                        'sort_field' : data.orderBy ? data.orderBy : 'id',
                        
                        'sort_order' : data.ascending ? 'desc' : 'asc',
                        
                        'search_term' : data.query.trim(),
                        
                        page : data.page,
                        
                        limit : data.limit,
                    }
                },

			 	responseAdapter({data}) {

					return {
						data: data.data.accounts.map(data => {

						data.edit_url = this.basePath() + '/whatsapp/edit/' + data.id;

						data.delete_url = this.basePath() + '/whatsapp/api/delete/' + data.id;

						return data;
					}),
						count: data.data.total
					}
				},
			}


    },
}