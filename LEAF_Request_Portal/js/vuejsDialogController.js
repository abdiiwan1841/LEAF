/************************
    Dialog Controller

*/

function vuejsDialogController(containerID, contentID, indicatorID, btnSaveID, btnCancelID) {
    this.containerID = containerID;
    this.contentID = contentID;
    this.indicatorID = indicatorID;
    this.btnSaveID = btnSaveID;
    this.btnCancelID = btnCancelID;
    this.dialogControllerXhrEvent = null;
    this.prefixID = 'dialog' + Math.floor(Math.random()*1000) + '_';
    this.validators = {};
    this.validatorErrors = {};
    this.validatorOks = {};
    this.requirements = {};
    this.requirementErrors = {};
    this.requirementOks = {};
    this.invalid = 0;
    this.incomplete = 0;

    var VueJSModal = window['vue-js-modal'].default;

    //alert("in here");
    Vue.use(VueJSModal, {
        dynamicDefaults: {
            draggable: false,
            resizable: false,
            height: 'auto'
        }
    });

    this.app = new Vue({
        el: '#' + containerID,
        data: {
            title: 'Editor',
            content: ''
        },
        methods: {
            show() {
                this.$modal.show(containerID);
            },
            hide () {
                this.$modal.hide(containerID);
            }
        },
        mount() {
            this.show();
        }
    });

    this.clearDialog();

    // xhrDialog controls
    $('#' + this.btnCancelID).on('click', function() {
        this.app.hide();
    });

}

vuejsDialogController.prototype.clear = function() {
    this.clearDialog();
};

vuejsDialogController.prototype.clearDialog = function() {
    this.app.content = '';
    this.app.title = 'Editor';
    $('#' + this.btnSaveID).off();
    this.clearValidators();
};

vuejsDialogController.prototype.setTitle = function(title) {
    this.app.title = title;
};

vuejsDialogController.prototype.hide = function() {
    this.app.hide();
    this.clearDialog();
};

vuejsDialogController.prototype.show = function () {
    this.app.show();
};

vuejsDialogController.prototype.setContent = function(content) {
    this.app.content = content;
};

vuejsDialogController.prototype.indicateBusy = function() {
    $('#' + this.indicatorID).css('visibility', 'visible');
};

vuejsDialogController.prototype.indicateIdle = function() {
    $('#' + this.indicatorID).css('visibility', 'hidden');
};

vuejsDialogController.prototype.enableLiveValidation = function() {
    var t = this;
    $('input[type="text"]').on('keyup', function() {
        t.isValid();
    });
};

vuejsDialogController.prototype.isValid = function() {
    this.invalid = 0;
    for(var item in this.validators) {
        if(!this.validators[item]()) {
            console.log('Data entry error on indicator ID: ' + item); // helps identify validator triggers when custom styles hide the normal error UI
            this.invalid = 1;
            if(this.validatorErrors[item] != undefined) {
                this.validatorErrors[item]();
            }
            else {
                alert('Data entry error. Please check your input.');
            }
        }
        else {
            if(this.validatorOks[item] != undefined) {
                this.validatorOks[item]();
            }
        }
    }
    if(this.invalid == 1) {
        return 0;
    }
    return 1;
};

vuejsDialogController.prototype.isComplete = function() {
    this.incomplete = 0;
    for(var item in this.requirements) {
        if(this.requirements[item]()) {
            this.incomplete = 1;
            if(this.requirementErrors[item] != undefined) {
                this.requirementErrors[item]();
            }
            else {
                alert('Required field missing. Please check your input.');
            }
        }
        else {
            if(this.requirementOks[item] != undefined) {
                this.requirementOks[item]();
            }
        }
    }
    if(this.incomplete == 1) {
        return 0;
    }
    return 1;
};

vuejsDialogController.prototype.setSaveHandler = function(funct) {
    $('#' + this.btnSaveID).off();
    var t = this;
    this.dialogControllerXhrEvent = $('#' + this.btnSaveID).on('click', function() {
        if(t.isValid() == 1 && t.isComplete() == 1) {           
            funct();
            $('#' + t.btnSaveID).off();
        }
        else {
            t.indicateIdle();
        }
    });
};

vuejsDialogController.prototype.setCancelHandler = function(funct) {
    $('#' + this.containerID).off('dialogbeforeclose');
    var t = this;
    $('#' + this.containerID).on('dialogbeforeclose', function() {
        if(t.isValid() == 1 && t.isComplete() == 1) {           
            funct();
            $('#' + this.containerID).off('dialogbeforeclose');
        }
        else {
            t.indicateIdle();
        }
    });
};

vuejsDialogController.prototype.setJqueryButtons = function(buttons) {
    $('#' + this.containerID).dialog('option', 'buttons', buttons);
};

vuejsDialogController.prototype.clickSave = function() {
    $('#' + this.btnSaveID).click();
};

vuejsDialogController.prototype.setValidator = function(id, func) {
    this.validators[id] = func;
};

vuejsDialogController.prototype.clearValidators = function() {
    this.validators = {};
    this.validatorErrors = {};
    this.requirements = {};
    this.requirementErrors = {};
    $('input[type="text"]').off();
};

vuejsDialogController.prototype.setValidatorError = function(id, func) {
    this.validatorErrors[id] = func;
};

vuejsDialogController.prototype.setValidatorOk = function(id, func) {
    this.validatorOks[id] = func;
};

vuejsDialogController.prototype.setRequired = function(id, func) {
    this.requirements[id] = func;
};

vuejsDialogController.prototype.setRequiredError = function(id, func) {
    this.requirementErrors[id] = func;
};

vuejsDialogController.prototype.setRequiredOk = function(id, func) {
    this.requirementOks[id] = func;
};