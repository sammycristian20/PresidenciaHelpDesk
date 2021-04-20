<template>
    <div>
        <div class="wrapping-div">
            <p class="text-left url-paragraph">{{ (data.textToCopy) ? subString(data.textToCopy,50) : '--' }}</p>
            <button title="Click to copy" class="pull-right unstyled-button" v-if="data.textToCopy" @click.prevent="copyToClipboard">
                <i :class="copied ? 'glyphicon glyphicon-ok green' : 'fa fa-clipboard'"></i>
            </button>
        </div>
    </div>
</template>

<script>

    import copy from 'clipboard-copy';
    import { getSubStringValue } from 'helpers/extraLogics'

    export default {
        name: 'click-to-copy',
        description: 'Reusable component which handles copying of text content to clipboard on click.',
        props: {
            //Components other than Datatable which are willing to use this, should send props like this {textToCopy:'Text to copy'}
            //If textToCopy property is not set, this wont work
            data: {
                type: Object,
                required: true,
            },
        },
        data() {
            return {
                copied: false,
            }
        },
        methods: {
            copyToClipboard() {
                copy(this.data.textToCopy);
                this.copied = true;
                this.showCopyIconAfterCopying()
            },
            subString(value,length = 15){
                return getSubStringValue(value,length)
            },
            showCopyIconAfterCopying() {
                //for showing copy icon after 5 seconds.
                setTimeout(() => {
                    this.copied = false;
                }, 5000)
            }
        }
    }

</script>


<style scoped>
    .url-paragraph {
        display: inline !important;
    }

    .green {
        color: green;
    }

    .unstyled-button {
        border: none;
        padding: 0;
        background: none;
        outline: none !important;
    }

</style>
