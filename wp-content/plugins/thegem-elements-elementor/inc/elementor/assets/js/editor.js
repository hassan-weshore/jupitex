! function($, elementor){
	"use strict";
	var modules = elementor.modules;
	var presetSelect = modules.controls.Select.extend({
		isPresetControl: function() {
			return "thegem_elementor_preset" === this.model.get("name") && -1 !== this.getWidgetName().indexOf("thegem-")
		},
		onReady: function() {
			window.thegemWidgets = window.thegemWidgets || {};
			this.loadPresets();
		},
		resetStyles: function() {
			/*console.log(this.model.get('default'));
			this.container.view.allowRender = false;
			var e = elementor.getPanelView().getCurrentPageView().getOption("editedElementView");
			$e.run("document/elements/reset-style", {
				container: e.getContainer()
			});
			this.container.view.allowRender = true;*/
		},
		getElementSettingsModel: function() {
			return this.container.settings
		},
		getWidgetName: function() {
			return this.getElementSettingsModel().get("widgetType")
		},
		isPresetDataLoaded: function() {
			return !_.isUndefined(window.thegemWidgets[this.getWidgetName()])
		},
		loadPresets: function() {
			var item = this;
			this.isPresetControl() && !this.isPresetDataLoaded() && this.getWidgetName() && $.post(thegemElementor.ajaxUrl, {
				action: 'thegem_elementor_get_preset_settings',
				widget: this.getWidgetName(),
				secret: thegemElementor.secret
			}).done(function(response) {
				response.success && item.setPresetData(response.data)
			})
		},
		setPresetData: function(data) {
			window.thegemWidgets[this.getWidgetName()] = data
		},
		getPresetData: function() {
			return _.isUndefined(window.thegemWidgets) ? {} : window.thegemWidgets[this.getWidgetName()] || {}
		},
		onBaseInputChange: function(e) {
			//this.resetStyles();
			if (this.constructor.__super__.onBaseInputChange.apply(this, arguments), this.isPresetControl() && e.currentTarget.value) {
				e.stopPropagation();
				var data = this.getPresetData();
				/*_.isUndefined(data[e.currentTarget.value]) || */this.applyPresetData(data[e.currentTarget.value]);
			}
		},
		applyPresetData: function(data) {
			var widgetControls = this.getElementSettingsModel().controls,
				item = this,
				collection = {};
			_.each(widgetControls, function(control, controlName) {
				if (item.model.get('name') !== controlName && !_.isUndefined(data) && !_.isUndefined(data[controlName])) {
					if (control.is_repeater) {
						var repeater = item.getElementSettingsModel().get(controlName).clone();
						repeater.each(function(subItem, subItemIndex) {
							_.isUndefined(data[controlName][subItemIndex]) || _.each(subItem.controls, function(subControl, subControlName) {
								item.isStyleTransferControl(subControl) && repeater.at(subItemIndex).set(subControlName, data[controlName][subItemIndex][subControlName])
							})
						});
						collection[controlName] = repeater;
					}
					collection[controlName] = data[controlName];
				} else {
					if(item.isStyleTransferControl(control)) {
						collection[controlName] = control.default;
					}
				}
			});
			this.getElementSettingsModel().setExternalChange(collection);
			this.container.view.render();
			this.container.view.renderOnChange();
		},
		isStyleTransferControl: function(control) {
			return !_.isUndefined(control.style_transfer) && control.style_transfer ? control.style_transfer : "content" !== control.tab || control.selectors || control.prefix_class
		}
	});
	elementor.addControlView("select", presetSelect);

}(window.jQuery, window.elementor);