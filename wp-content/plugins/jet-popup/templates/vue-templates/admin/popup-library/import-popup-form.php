<div
    class="jet-popup-library-form import-form"
>
    <div class="jet-popup-library-form__header">
        <div class="jet-popup-library-form__header-title"><?php _e( 'Import a Popup', 'jet-popup' ); ?></div>
        <p class="jet-popup-library-form__header-sub-title"><?php _e( 'Here you can import a popup.', 'jet-popup' ); ?></p>
    </div>
    <div class="jet-popup-library-form__body">
        <form id="jet-popup-import-form" class="jet-popup-import-form" method="post" action="<?php echo add_query_arg( [ 'action' => 'jet_popup_import_preset', ], esc_url( admin_url( 'admin.php' ) ) ); ?>" enctype="multipart/form-data">
            <fieldset id="jet-popup-import-form-inputs" class="jet-popup-import-form__inputs">
                <input type="file" class="file-button" name="file" accept=".json,application/json,.zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed" required>
            </fieldset>
            <div class="jet-popup-import-form__controls">
                <cx-vui-button
                    button-style="default"
                    class="cx-vui-button--style-accent-border"
                    size="mini"
                    @on-click="closeImportPopupHandler"
                >
                    <template v-slot:label>
                        <span><?php _e( 'Cancel', 'jet-popup' ); ?></span>
                    </template>
                </cx-vui-button>
                <input type="submit" class="cx-vui-button--style-accent-border cx-vui-button cx-vui-button--style-default cx-vui-button--size-mini" value="<?php echo esc_attr__( 'Import', 'jet-popup' ); ?>">
            </div>
        </form>
    </div>
</div>