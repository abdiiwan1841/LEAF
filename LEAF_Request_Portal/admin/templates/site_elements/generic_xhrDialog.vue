<modal id="xhrDialog" name="xhrDialog" role="dialog" class="ui-dialog leaf-padAll1rem" :height="this.height" :minWidth="this.minWidth" :adaptive="true" :scrollable="true">
    
    <form id="record" enctype="multipart/form-data" action="javascript:void(0);">

        <div role="document" class="dialog-container ui-dialog-content leaf-padAll1rem">
            
            <div id="loadIndicator" class="leaf-dialog-loader">
                Loading...<img src="../images/largespinner.gif" alt="loading..." />
            </div>
            
            <div id="vue-title" class="ui-dialog-titlebar">
                <span class="ui-dialog-title">{{ this.title }}</span>
                <span style="float: right;"><button @click="this.hide"><i class="fas fa-window-close" alt="Close" title="Close"></i></button></span>
            </div>
 
            <main id="xhr"role="main" class="ui-dialog-content leaf-dialog-content" role="main" v-html="this.content"></main>

            <aside class="leaf-buttonBar" role="complementary">
                <div class="leaf-float-right">
                    <button id="button_cancelchange" class="usa-button usa-button--outline leaf-btn-med leaf-float-left" @click="this.hide">
                        Cancel
                    </button>
                </div>

                <div class="leaf-float-left">
                    <button id="button_save" class="usa-button leaf-btn-med leaf-float-right" @click="this.clickSave">
                        Save
                    </button>
                </div>
            </aside>

        </div>

    </form>

</modal>