export const plugins = [
    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'print', 'preview',
    'searchreplace', 'visualblocks', 'fullscreen',
    'insertdatetime', 'media', 'table', 'paste', 'help', 'wordcount',
    'codesample', 'importcss', 'directionality', 'visualchars', 'code',
    'hr', 'pagebreak', 'nonbreaking', 'imagetools', 'textpattern', 'noneditable',
];

export const editorProps = {

    height : {type: Number, default: 400},

    showMenubar: {type: Boolean, default: true},

    toolbarSticky: {type: Boolean, default: false,},

    imageAdvancedTab: {type: Boolean, default: true},

    imageCaption: {type: Boolean, default: true},

    toolbarMode: {type: String, default: 'sliding'},

    contentStyle: {type:String, default:'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'},

    draggableModal: {type: Boolean, default: true},

    elementPath: {type: Boolean, default:false},

    resize: {type:Boolean, default: false},

    branding: {type:Boolean, default: false},

    browserSpellcheck: {type:Boolean, default:true},

    customUndoRedoLevels: {type:Number, default: 10},

    pasteDataImages: {type: Boolean, default: true},

    statusbar: {type: Boolean, default: false},

    autoFocus: {type: Boolean, default: false},

    automaticUploads: {type: Boolean, default: true},

    relativeUrls : {type: Boolean, default: false},

    removeScriptHost: {type: Boolean, default: false},

    width: {type: Number|String, default: "103%"},
}
