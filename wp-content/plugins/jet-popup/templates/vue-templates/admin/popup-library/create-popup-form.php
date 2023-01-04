<div
    class="jet-popup-library-form create-form"
    :class="{ 'progress-state': popupCreatingStatus }"
>
    <div class="jet-popup-library-form__header">
        <div class="jet-popup-library-form__header-title"><?php _e( 'Create a Popup', 'jet-popup' ); ?></div>
        <p class="jet-popup-library-form__header-sub-title"><?php _e( 'Here you can create a new blank popup or use template preset.', 'jet-popup' ); ?></p>
    </div>
    <div class="jet-popup-library-form__body">
        <cx-vui-input
            name="popupName"
            label="<?php _e( 'Name(optional)', 'jet-popup' ); ?>"
            placeholder="<?php _e( 'Enter popup name', 'jet-popup' ); ?>"
            :wrapper-css="[ 'vertical-fullwidth' ]"
            size="fullwidth"
            type="text"
            v-model="newPopupData.name"
        >
        </cx-vui-input>
        <div class="cx-vui-component cx-vui-component--vertical-fullwidth">
            <div class="cx-vui-component__meta">
                <label class="cx-vui-component__label" for="cx_popupName"><?php _e( 'Choose Preset(optional)', 'jet-popup' ); ?></label>
            </div>
            <div class="cx-vui-component__control">
                <div class="jet-popup-library-presets">
                    <div
                        class="jet-popup-library-presets-item"
                        :class="{ 'checked': presetData.checked }"
                        v-for="presetData in presetsList"
                        :key="presetData.id"
                        v-on:click="choosePresetHandler( presetData.id )"
                    >
                        <div class="jet-popup-library-presets-item-thumb" v-html="presetData.svg"></div>
                        <div class="jet-popup-library-presets-item-name">{{ presetData.title }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="jet-popup-library-form__footer">
        <cx-vui-button
            button-style="default"
            class="cx-vui-button--style-accent-border"
            size="mini"
            @on-click="closeCreatePopupHandler"
        >
            <template v-slot:label>
                <span><?php _e( 'Cancel', 'jet-popup' ); ?></span>
            </template>
        </cx-vui-button>
        <cx-vui-button
            button-style="default"
            class="cx-vui-button--style-accent"
            size="mini"
            @click="createPopupHandler"
            :loading="popupCreatingStatus"
        >
            <span slot="label"><?php _e( 'Create', 'jet-popup' ); ?></span>
        </cx-vui-button>
    </div>
</div>