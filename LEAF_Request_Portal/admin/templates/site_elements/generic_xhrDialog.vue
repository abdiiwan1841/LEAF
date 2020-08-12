    <modal name="test-modal" id="test-modal">

        <div class="vue-dialog">


            <div class="vue-dialog-content">

                <div class="vue-dialog-content-title" id="vue-title">
                    {{ this.title }}
                </div>

                <form id="record" enctype="multipart/form-data" action="javascript:void(0);">
                    <div>
                        <button id="button_cancelchange" class="buttonNorm" style="position: absolute; left: 10px"><img src="../../libs/dynicons/?img=process-stop.svg&amp;w=16" alt="cancel" /> Cancel</button>
                        <button id="button_save" class="buttonNorm" style="position: absolute; right: 10px"><img src="../../libs/dynicons/?img=media-floppy.svg&amp;w=16" alt="save" /> Save Change</button>
                        <div style="border-bottom: 2px solid black; line-height: 30px"><br /></div>
                        <div id="loadIndicator" style="visibility: hidden; z-index: 9000; position: absolute; text-align: center; font-size: 24px; font-weight: bold; background-color: #f2f5f7; padding: 16px; height: 400px; width: 526px"><img src="../images/largespinner.gif" alt="loading..." />
                    </div>

                    <div class="vue-dialog-content" id="vue-content" v-html="this.content">

                    </div>

                    </div>
                </form>



                <div class="vue-dialog-buttons">
                    <button class="vue-dialog-button" @click="hide('test-modal')" style="border: solid 3px blue;">
                        Cancel
                    </button>
                </div>

            </div>

        </div>
        
    </modal>