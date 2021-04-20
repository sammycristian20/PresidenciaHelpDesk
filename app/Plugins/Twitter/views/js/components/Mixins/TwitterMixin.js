export const TwitterMixin = {
    data() {
        return {
            loadingSpeed : 4000,
            app_exists : false,
            isLoading : false,
            loading:false,
            consumer_api_key: '',
			consumer_api_secret: '',
			access_token: '',
			access_token_secret: '',
			required: true,
			mode: 'create',
			twitterModelId: null,
            hashtag_text : [],
            elements_hashtag: [],
            isDisabled : false,
            appID:'',
            elements_new_ticket: [
                {
                    name: "One Day",
                    value: 1,
                },

                {
                    name: "Five Days",
                    value: 5,
                },

                {
                    name: "Ten Days",
                    value: 10,
                },

                {
                    name: "Fifteen Days",
                    value: 15,
                },

                {
                    name: "Thirty Days",
                    value: 30,
                },
            ],

            elements_cron : [
                {
                    name: "Yes",
                    value: 1,
                },

                {
                    name: "No",
                    value: 0
                }
            ],

            new_ticket_interval: {
                name: "Ten Days",
                value: "10",
            },

            cron_confirm: {
                name: "Yes",
                value: 1,
            },

            showModal : false,
            deleteUrl: 'twitter/api/delete',

        }
    },

}