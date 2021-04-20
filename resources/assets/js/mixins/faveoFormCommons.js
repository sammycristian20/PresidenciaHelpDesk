import { mapGetters } from 'vuex';
import axios from 'axios';

import { validateFormData, getFormUniqueKey, convertMapToSubmitableData } from 'helpers/formUtils';
import { errorHandler, successHandler } from 'helpers/responseHandler';
import moment from "moment";

export const faveoFormCommons = {

    beforeMount() {
      // beforemounting the component we will create a new instance for the FaveoForm
        this.formUniqueKey = getFormUniqueKey(this.category);
        this.$store.dispatch('createNewFormInstance', { formUniqueKey: this.formUniqueKey, scenario: this.scenario || this.mode });
    },

    computed: {
        ...mapGetters({
            getFaveoFormData: 'getFaveoFormData',
            getRecurAdditionalInfo: 'getRecurAdditionalInfo',
            showLoader: 'showLoader',
            getEditorAttachments: 'getEditorAttachments',
            getRecaptchaKey: 'getRecaptchaKey',
            isChildFormLoading: 'getChildFormLoading',
        }),
    },

    methods: {

      // triggered when submit button got a click
        async onSubmit(additionalParams = null) {
            const isValid = await validateFormData(this.$refs.faveoFormObserver);

            if (isValid) {
        // if form is valid submit form data to the server
                this.submitFormToServer(additionalParams);
            }
        },

        submitFormToServer(additionalParams = null) {

            this.$store.dispatch('unsetValidationError');

            this.submitFormInProgress = true;

          // get formValues and submitApiEndpoint from store
            const { submitApiEndpoint, formDataMap } = this.getFaveoFormData[this.formUniqueKey];

          // create new instance of FormData
            let _formData = new FormData();

          // append panel to the the formData
            _formData.append('panel', this.panel);

            _formData.append('g-recaptcha-response', this.getRecaptchaKey);

            if (this.getEditorAttachments.length) {
                _formData.append('attachments', JSON.stringify(this.getEditorAttachments))
            }

          // iterate over the formValues and append each value to the formData instance
            const formValueObj = convertMapToSubmitableData(formDataMap);
            for (const [key, value] of Object.entries(formValueObj)) {
                _formData.append(key, value);
            }

            // appending additional params too to the api call
            additionalParams && Object.keys(additionalParams).forEach(key => {
                _formData.append(key, additionalParams[key]);
            });


            // incase of recur, iterate over the recur object(get it form store) and append each value to the formData instance
        if (this.scenario === 'recur') {
            for (const [key, value] of Object.entries(this.getRecurAdditionalInfo)) {
                _formData.append(['recur[' + key + ']'], value);
            }
        }
      
          // Set HTTP header to post a multipart/form-data
            let headers = { 'Content-Type': 'multipart/form-data' };

            axios.post(submitApiEndpoint, _formData, { headers })
            .then(response => {
                successHandler(response, 'faveo-form');

              // Call afterSubmit function to perform postSubmit operations
                this.afterSubmit(response);
            })
        .catch(error => {
        
            errorHandler(error, 'faveo-form');

            if(error.response.status === 400) {

                // Call afterError function to close Modal
                this.afterError();
            }
        })
        .finally(() => {
            this.submitFormInProgress = false;
            })
        },
    },

    beforeDestroy() {

      // destroy the form instance before killing the component
        this.$store.dispatch('destroyFormInsatnce', this.formUniqueKey)
    },
}