export const WhatsappMixin = {
    data() {
        return {
            template:'',
            password: 'password',
            name: '',
            sid: '',
            token: '',
            business_phone: '',
            webhook_url:'',
            app_exists:false,
            required: true,
            loading:false,
            isLoading : false,
            loadingSpeed: 4000,
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

            showModal : false,

            elements_is_image_inline : [
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
                name: "One Day",
                value: "1",
            },

            is_image_inline: {
                name: "Yes",
                value: 1,
            },
        }
    },

    methods: {
        
        resetFields() {

            this.name = '';
            this.sid = '';
            this.token = '';
            this.business_phone = '';
            this.is_image_inline = 1;
            this.new_ticket_interval = "";
            this.template = '';
        },
    }
}