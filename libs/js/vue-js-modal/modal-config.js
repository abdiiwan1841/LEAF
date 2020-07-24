var VueJSModal = window['vue-js-modal'].default;

Vue.use(VueJSModal, {
    dynamicDefaults: {
        draggable: true,
        resizable: true,
        height: 'auto'
    }
});

var app = new Vue({
    el: '.leaf-app',
    data: {
        //state: FormEditorStore.state,
        //form: FormEditorStore.state.form,
        //message: 'test!',
    },
    methods: {
        show (id) {
            this.$modal.show(id);
        },
        hide (id) {
            this.$modal.hide(id);
        }
    },
});