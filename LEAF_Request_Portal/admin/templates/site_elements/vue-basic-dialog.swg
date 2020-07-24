<!-- template for the modal component -->

<script type="text/x-template" id="modal-template">

    <transition name="modal">

        <div class="vue-dialog">

            <div class="vue-dialog-content" id="modal-content">

                <div class="vue-dialog-content-title">
                    Header II
                </div>

                <div class="vue-dialog-content">
                    Body
                </div>

                <div class="vue-dialog-buttons">
                    <button class="vue-dialog-button" @click="hide">
                        OK
                    </button>
                </div>

            </div>

        </div>

    </transition>

</script>