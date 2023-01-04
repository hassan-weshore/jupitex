( function( $ ) {

	'use strict';

	Vue.config.devtools = true;

	var JetPopupLibraryAdminEventBus = new Vue();

	var JetPopupAdmin = {

		popupLibraryInstance: null,

		init: function() {

			JetPopupAdmin.importInit();

			if ( $( '#jet-popup-library-page')[0] ) {
				JetPopupAdmin.presetLibraryInit();
			}

			if ( $( '#jet-popup-settings-page')[0] ) {
				JetPopupAdmin.settingPageInit();
			}

			if ( $( '#jet-popup-library' )[0] ) {
				JetPopupAdmin.initPopupLibrary();
			}

			$( document ).on( 'click.JetPopupEditConditions', '.jet-popup-conditions__edit-conditions', function ( event ) {
				let $this   = $( this ),
				    popupId = $this.data( 'popup-id' );

				JetPopupAdmin.popupLibraryInstance.conditionsManagerPopupVisible = true;
				JetPopupAdmin.popupLibraryInstance.popupId = popupId;

				return false;
			} );

			$( document ).on( 'click.JetPopupEditSettings', '.jet-popup-settings__edit-settings', function ( event ) {
				let $this   = $( this ),
				    popupId = $this.data( 'popup-id' );

				JetPopupAdmin.popupLibraryInstance.settingsManagerPopupVisible = true;
				JetPopupAdmin.popupLibraryInstance.popupId = popupId;

				return false;
			} );

		},

		importInit: function() {

			if ( ! $( '#wpbody-content .page-title-action' )[0] ) {
				return false;
			}

			$( '#wpbody-content' ).find( '.page-title-action:last' ).after( `<a id="jet-popup-import-trigger" href="#" class="page-title-action">${ window.JetPopupLibraryConfig.labels.importButtonLabel }</a>` );

			let $newPopupButton = $( '.page-title-action[href*="post-new.php?post_type=jet-popup"]' ),
				$importButton   = $( '#jet-popup-import-trigger' );

			$newPopupButton.on( 'click', function( event ) {
				event.preventDefault();
				JetPopupAdmin.popupLibraryInstance.createPopupVisible = true;
			} );

			$importButton.on( 'click', function( event ) {
				event.preventDefault();
				JetPopupAdmin.popupLibraryInstance.importPopupVisible = true;
			} );
		},

		presetLibraryInit: function() {
			Vue.config.devtools = true;

			Vue.component( 'preset-list', {
				template: '#preset-list-template',

				props: {
					presets: Array
				},

				methods: {
					changePage: function( page ) {
						console.log(changePage);
					}
				}
			});

			Vue.component( 'preset-item', {
				template: '#preset-item-template',

				props: {
					presetId: Number,
					title: String,
					thumbUrl: String,
					category: Array,
					categoryNames: Array,
					install: Number,
					required: Array,
					excerpt: String,
					details: Array,
					permalink: String
				},

				data: function() {
					return {
						modalShow: false,
						requiredPluginData: window.jetPopupData.requiredPluginData
					}
				},

				computed: {
					categoryName: function() {
						var name = 'None';

						if ( 0 !== this.categoryNames.length ) {
							name = '';

							this.categoryNames.forEach( function( item, i ) {
								name += item;
							} );

							name = this.categoryNames.join( ', ' );
						}

						return name;
					},

					requiredPlugins: function() {
						var plugins            = [],
							requiredPluginData = this.requiredPluginData;

						this.required.forEach( function( item, i ) {
							if ( requiredPluginData.hasOwnProperty( item ) ) {
								plugins.push( requiredPluginData[item] );
							}
						} );

						return plugins;
					}
				},

				methods: {
					openModal: function() {
						this.modalShow = true;

						eventBus.$emit( 'openIntallPopup', this.presetId );
					},
				}
			});

			Vue.component( 'presetlibrary', {
				template: '#preset-library-template',

				data: function() {
					return ({
						spinnerShow: true,
						presetsLoaded: false,
						presetsLoadedError: false,
						categoriesLoaded: false,
						presetsData: [],
						categoryData: [],
						activeCategories: [],
						presetsLength: false,
						installPopupVisible: false,
						inactiveLicenseVisible: false,
						page: 1,
						perPage: 6,
						preset: false,
						filterBy: 'date',
						filterByOptions: [
							{
								label: 'Date',
								value: 'date'
							},
							{
								label: 'Name',
								value: 'name'
							},
							{
								label: 'Popular',
								value: 'popular'
							},
						]
					})
				},

				mounted: function() {
					var libraryPresetsUrl         = window.jetPopupData.libraryPresetsUrl,
						libraryPresetsCategoryUrl = window.jetPopupData.libraryPresetsCategoryUrl,
						categories                = [],
						presets                   = [],
						vueInstance               = this;

					axios.get( libraryPresetsUrl ).then( function ( response ) {
						var data = response.data;

						if ( data.success ) {
							for ( var preset in data.presets ) {
								var presetData = data.presets[ preset ];

								presets.push( {
									id: presetData['id'],
									title: presetData['title'],
									thumb: presetData['thumb'],
									category: presetData['category'],
									categoryNames: presetData['category_names'],
									order: presetData['order'],
									install: +presetData['install'],
									required: presetData['required'],
									excerpt: presetData['excerpt'],
									details: presetData['details'],
									permalink: presetData['permalink'],
								} );
							}

							vueInstance.presetsData = presets;
							vueInstance.spinnerShow = false;
							vueInstance.presetsLoaded = true;
						} else {
							vueInstance.spinnerShow = false;
							vueInstance.presetsLoadedError = true;
						}

					}).catch(function (error) {
						// handle error
						vueInstance.spinnerShow = false;
						vueInstance.presetsLoadedError = true;
						vueInstance.presetsData = [];
					});

					axios.get( libraryPresetsCategoryUrl ).then( function ( response ) {
						var data = response.data;

						if ( data.success ) {
							for ( var category in data.categories ) {
								categories.push( {
									id: category,
									label: data.categories[category],
									state: false
								} );
							}

							vueInstance.categoryData = categories;
						}

						vueInstance.categoriesLoaded = true;

					}).catch( function ( error ) {
						vueInstance.categoryData = [];
					});

					// Bus Events
					eventBus.$on( 'openIntallPopup', function( presetId ) {
						vueInstance.preset = presetId;

						if ( 'true' === window.jetPopupData.pluginActivated ) {
							vueInstance.installPopupVisible = true;
						} else {
							vueInstance.inactiveLicenseVisible = true;
						}

					} );
				},

				computed: {
					presetList: function() {
						var currentCategories = [],
							currentPage       = this.page,
							perPage           = this.perPage,
							filteredData      = [];

						filteredData = this.presetsData.filter( ( preset, index ) => {
							var flag = false;

							flag = this.categoryData.every( ( category ) => {
								return 'false' === category.state || false === category.state;
							} );

							for ( var category in this.categoryData ) {

								if ( 'true' === this.categoryData[category]['state']
									&& preset.category.includes( this.categoryData[category]['id'] )
								) {
									flag = true;

									break;
								}
							}

							return flag;
						} );

						this.presetsLength = filteredData.length;

						filteredData = filteredData.filter( ( preset, index ) => {
							var flag  = false,
								left  = ( currentPage - 1 ) * perPage,
								right = left + perPage;

							if ( index >= left && index < right ) {
								flag = true;
							}

							return flag;
						} );

						return filteredData;
					},

					isShowPagination: function() {
						return this.presetsLength > this.perPage;
					}
				},

				methods: {
					filterByCategory: function() {
						this.page = 1;
					},

					filterByHandler: function() {

						this.page = 1;

						switch( this.filterBy ) {
							case 'date':
								this.presetsData.sort( function ( a, b ) {

									return a.order - b.order;
								});

								break;

							case 'name':
								this.presetsData.sort( function ( a, b ) {
									var aTitle = a.title.toLowerCase(),
										bTitle = b.title.toLowerCase();

									if ( aTitle > bTitle ) {
										return 1;
									}

									if ( aTitle < bTitle ) {
										return -1;
									}

									return 0;
								});

								break;

							case 'popular':
								this.presetsData.sort( function ( a, b ) {

									return b.install - a.install;
								});

								break;
						}

					},

					changePage: function( page ) {
						this.page = page;
					},

					createPopup: function() {
						window.location.href = window.jetPopupData.createPopupLink + '&preset=' + this.preset;
					},

					activateLicense: function() {
						window.location.href = window.jetPopupData.licenseActivationLink;
					},

				}
			});

			var eventBus = new Vue();

			var libraryPage = new Vue( {
				el: '#jet-popup-library-page',
			} );
		},

		settingPageInit: function() {
			Vue.config.devtools = true;

			Vue.component( 'mailchimp-list-item', {
				template: '#mailchimp-list-item-template',

				props: {
					list: Object,
					apikey: String
				},

				data: function() {
					return {
						mergeFieldsStatusLoading: false
					}
				},

				computed: {
					isMergeFields: function() {

						return this.list.hasOwnProperty( 'mergeFields' ) && ! jQuery.isEmptyObject( this.list[ 'mergeFields' ] ) ? true : false;
					}
				},

				methods: {
					getMergeFields: function() {
						var vueInstance = this;

						jQuery.ajax( {
							type: 'POST',
							url: ajaxurl,
							data: {
								'action': 'get_mailchimp_list_merge_fields',
								'apikey': this.apikey,
								'listid': this.list.id
							},
							beforeSend: function( jqXHR, ajaxSettings ) {
								vueInstance.mergeFieldsStatusLoading = true;
							},
							error: function( data, jqXHR, ajaxSettings ) {

							},
							success: function( data, textStatus, jqXHR ) {
								vueInstance.mergeFieldsStatusLoading = false;

								switch ( data.type ) {
									case 'success':

										vueInstance.$CXNotice.add( {
											message: data.desc,
											type: 'success',
											duration: 3000,
										} );

										eventBus.$emit( 'updateListMergeFields', data.request );
										break;
									case 'error':
										vueInstance.$CXNotice.add( {
											message: data.desc,
											type: 'error',
											duration: 3000,
										} );

										break;
								}
							}
						} );
					}
				}
			});

			Vue.component( 'settingsform', {
				template: '#settings-form-template',

				data: function() {
					return ( {
						saveStatusLoading: false,
						syncStatusLoading: false,
						mergeFieldsStatusLoading: false,
						collapse: 'mailChimpPanel',
						settingsData: {
							'apikey': ''
						},
						mailchimpAccountData: {},
						mailchimpListsData: {}
					} )
				},

				computed: {
					isMailchimpAccountData: function() {
						return ! jQuery.isEmptyObject( this.mailchimpAccountData ) ? true : false;
					},

					isMailchimpListsData: function() {
						return ! jQuery.isEmptyObject( this.mailchimpListsData ) ? true : false;
					}
				},

				created: function() {
					var vueInstance = this,
						settings    = window.jetPopupAdminData.settings,
						mailchimpApiData = window.jetPopupAdminData.mailchimpApiData;

					this.settingsData = settings;

					if ( mailchimpApiData.hasOwnProperty( settings['apikey'] ) ) {
						var user = mailchimpApiData[ settings['apikey'] ];

						if ( user.hasOwnProperty( 'account' ) ) {
							var account = user.account;

							this.mailchimpAccountData = {
								account_id: account.account_id,
								username: account.username || '-',
								first_name: account.first_name || '-',
								last_name: account.last_name || '-',
								avatar_url: account.avatar_url
							};
						}

						if ( user.hasOwnProperty( 'lists' ) ) {
							var lists     = user.lists,
								tempLists = {};

							if ( ! jQuery.isEmptyObject( lists ) ) {
								for ( var key in lists ) {
									var listInfo    = lists[ key ]['info'],
										mergeFields = lists[ key ]['merge_fields'] || [],
										mergeFieldsTemp = {};

									mergeFields.forEach( function( field, i, arr ) {
										mergeFieldsTemp[ field['tag'] ] = field['name'];
									} );

									tempLists[ key ] = {
										id: listInfo.id,
										name: listInfo.name,
										memberCount: listInfo.stats.member_count,
										doubleOptin: listInfo.double_optin,
										dateCreated: listInfo.date_created,
										mergeFields: mergeFieldsTemp
									};
								}
							}

							this.mailchimpListsData = tempLists;

						}
					}

					// Bus Events
					eventBus.$on( 'updateListMergeFields', function ( request ) {
						var listid          = request.list_id,
							mergeFields     = request.merge_fields,
							mergeFieldsTemp = {};

						for ( key in mergeFields ) {
							var fieldData = mergeFields[ key ];

							mergeFieldsTemp[ fieldData['tag'] ] = fieldData['name'];
						}

						Vue.set( vueInstance.mailchimpListsData[ listid ], 'mergeFields', mergeFieldsTemp );
					});
				},

				methods: {
					mailchimpSync: function() {
						var vueInstance = this;

						jQuery.ajax( {
							type: 'POST',
							url: ajaxurl,
							data: {
								'action': 'get_mailchimp_user_data',
								'apikey': this.settingsData.apikey
							},
							beforeSend: function( jqXHR, ajaxSettings ) {
								vueInstance.syncStatusLoading = true;
							},
							error: function( jqXHR, ajaxSettings ) {},
							success: function( data, textStatus, jqXHR ) {
								switch ( data.type ) {
									case 'success':
										var dataRequest = data.request;

										vueInstance.$CXNotice.add( {
											message: data.desc,
											type: 'success',
											duration: 3000,
										} );

										vueInstance.mailchimpAccountData = {
											account_id: dataRequest.account_id,
											username: dataRequest.username || '-',
											first_name: dataRequest.first_name || '-',
											last_name: dataRequest.last_name || '-',
											avatar_url: dataRequest.avatar_url
										};

										vueInstance.mailchimpSyncLists();

										break;
									case 'error':
										vueInstance.syncStatusLoading = false;

										vueInstance.$CXNotice.add( {
											message: data.desc,
											type: 'error',
											duration: 3000,
										} );
										break;
								}
							}
						} );

					},

					mailchimpSyncLists: function() {
						var vueInstance = this;

						jQuery.ajax( {
							type: 'POST',
							url: ajaxurl,
							data: {
								'action': 'get_mailchimp_lists',
								'apikey': this.settingsData.apikey
							},
							beforeSend: function( jqXHR, ajaxSettings ) {
								vueInstance.syncStatusLoading = true;
							},
							error: function( jqXHR, ajaxSettings ) {

							},
							success: function( data, textStatus, jqXHR ) {

								vueInstance.syncStatusLoading = false;

								switch ( data.type ) {
									case 'success':

										vueInstance.$CXNotice.add( {
											message: data.desc,
											type: 'success',
											duration: 3000,
										} );

										var request = data.request;

										if ( request.hasOwnProperty( 'lists' ) ) {
											var lists     = request['lists'],
												tempLists = {};

											for ( var key in lists ) {
												var listData = lists[ key ];

												tempLists[ listData.id ] = {
													id: listData.id,
													name: listData.name,
													memberCount: listData.stats.member_count,
													doubleOptin: listData.double_optin,
													dateCreated: listData.date_created
												}
											}

											vueInstance.mailchimpListsData = tempLists;
										}

										break;
									case 'error':
										vueInstance.$CXNotice.add( {
											message: data.desc,
											type: 'error',
											duration: 3000,
										} );

										break;
								}
							}
						} );
					},

					saveSettings: function() {
						var vueInstance = this,
							data = {
								'action': 'jet_popup_save_settings',
								'data': this.settingsData
							};

						jQuery.ajax( {
							type: 'POST',
							url: ajaxurl,
							data: data,
							beforeSend: function( jqXHR, ajaxSettings ) {
								vueInstance.saveStatusLoading = true;
							},
							error: function( data, jqXHR, ajaxSettings ) {

							},
							success: function( data, textStatus, jqXHR ) {
								vueInstance.saveStatusLoading = false;

								switch ( data.type ) {
									case 'success':
										vueInstance.$CXNotice.add( {
											message: data.desc,
											type: 'success',
											duration: 3000,
										} );

									break;
									case 'error':
										vueInstance.$CXNotice.add( {
											message: data.desc,
											type: 'error',
											duration: 3000,
										} );

									break;
								}
							}
						} );
					}
				}

			});

			var eventBus = new Vue();

			var settingsPage = new Vue( {
				el: '#jet-popup-settings-page',
			} );
		},

		initPopupLibrary: function() {

			Vue.component( 'jet-popup-library-conditions-item', {
				template: '#tmpl-jet-popup-library-conditions-item',

				props: {
					id: String,
					rawCondition: Object
				},

				data: function() {
					return ( {
						сondition: this.rawCondition,
						requestLoading: false,
						remoteOptionsList: [],
					} )
				},

				created: function() {},

				watch: {
					'сondition.group': function( curr ) {

						if ( this.subGroupAvaliable ) {
							let subGroups     = this.$root.rawConditionsData[ this.сondition.group ]['sub-groups'],
							    subGroupsKeys = Object.keys( subGroups );

							if ( 0 !== subGroupsKeys.length ) {
								this.сondition.subGroup = subGroupsKeys[0];

								switch ( this.subGroupValueControl.type ) {
									case 'f-select':
									case 'f-search-select':
										this.сondition.subGroupValue = [];
										break;
									default:
										this.сondition.subGroupValue = '';
										break;
								}
							}

							this.remoteOptionsList = [];

						}
					},
					'сondition.subGroup': function( curr ) {

						if ( this.subGroupAvaliable ) {
							this.сondition.subGroupValue = '';
							this.remoteOptionsList = [];
						}
					}
				},

				computed: {

					groupVisible: function() {
						return true;
					},

					subGroupVisible: function() {
						return 0 !== this.subGroupOptions.length ? true : false;
					},

					subGroupValueVisible: function() {
						return this.subGroupValueControl ? true : false;
					},

					subGroupValueControl: function() {

						if ( ! this.subGroupAvaliable ) {
							return false;
						}

						if ( ! this.$root.rawConditionsData.hasOwnProperty( this.сondition.group )) {
							return false;
						}

						if ( ! this.$root.rawConditionsData[ this.сondition.group ]['sub-groups'].hasOwnProperty( this.сondition.subGroup )) {
							return false;
						}

						let subGroupData = this.$root.rawConditionsData[ this.сondition.group ]['sub-groups'][ this.сondition.subGroup ];

						return subGroupData.control;
					},

					subGroupItemAction: function() {

						if ( ! this.subGroupAvaliable ) {
							return false;
						}

						if ( ! this.$root.rawConditionsData.hasOwnProperty( this.сondition.group )) {
							return false;
						}

						if ( ! this.$root.rawConditionsData[ this.сondition.group ]['sub-groups'].hasOwnProperty( this.сondition.subGroup )) {
							return false;
						}

						let subGroupData = this.$root.rawConditionsData[ this.сondition.group ]['sub-groups'][ this.сondition.subGroup ];

						return subGroupData.action;
					},

					isSearch: function () {
						return 'f-search-select' === this.subGroupValueControl.type ? true : false;
					},

					groupOptions: function() {
						let groupList = [],
						    groups    = this.$root.rawConditionsData;

						for ( let group in groups ) {
							groupList.push( {
								value: group,
								label: groups[ group ]['label']
							} );
						}

						return groupList;
					},

					subGroupAvaliable: function() {
						return this.$root.rawConditionsData[ this.сondition.group ].hasOwnProperty( 'sub-groups' );
					},

					subGroupOptions: function() {
						return this.$root.rawConditionsData[ this.сondition.group ]['options'];
					},

					subGroupValueOptions: function() {
						let optionsList = [];

						if ( this.remoteOptionsList.length ) {
							return this.remoteOptionsList;
						}

						if ( ! this.subGroupAvaliable ) {
							return optionsList;
						}

						if ( ! this.$root.rawConditionsData[ this.сondition.group ]['sub-groups'].hasOwnProperty( this.сondition.subGroup ) ) {
							return optionsList;
						}

						let subGroupData = this.$root.rawConditionsData[ this.сondition.group ]['sub-groups'][ this.сondition.subGroup ];

						if ( subGroupData.options ) {
							return subGroupData.options;
						}

						if ( this.subGroupItemAction && ! this.isSearch ) {
							this.getRemoteItems();
						}

						return optionsList;
					}
				},

				methods: {

					deleteCondition: function() {
						JetPopupLibraryAdminEventBus.$emit( 'removeCondition', this.id );
					},

					remoteSearchHandler: function ( $query, $values ) {
						let requestData = Object.assign( {}, this.subGroupItemAction.params, {
							query: $query,
							values: $values,
						} );

						return wp.apiFetch( {
							method: 'post',
							path: `/jet-popup/v2/${ this.subGroupItemAction.action }`,
							data: requestData,
						} );
					},

					onChangeRemoteOptionsHandler: function( options ) {
						this.remoteOptionsList = options;
					},

					getRemoteItems: function( query = '' ) {
						let vueInstance = this;
						let requestData = this.subGroupItemAction.params;

						vueInstance.requestLoading = true;

						wp.apiFetch( {
							method: 'post',
							path: `/jet-popup/v2/${ this.subGroupItemAction.action }`,
							data: requestData,
						} ).then( function( response ) {
							vueInstance.requestLoading = false;
							vueInstance.$set(
								vueInstance.$root.rawConditionsData[ vueInstance.сondition.group ]['sub-groups'][ vueInstance.сondition.subGroup ],
								'options',
								response
							);
						} );
					}
				},

			} );

			Vue.component( 'jet-popup-library-conditions-manager', {
				template: '#tmpl-jet-popup-library-conditions-manager',

				props: {
					popupId: Number
				},

				data: function() {
					return ( {
						conditions: [],
						relationType: 'or',
						saveConditionsStatus: false,
						getConditionsStatus: false,
					} )
				},

				created: function() {
					var vueInstance = this;

					JetPopupLibraryAdminEventBus.$on( 'removeCondition', function ( id ) {
						let templateConditions = vueInstance.conditions;

						vueInstance.conditions = templateConditions.filter( function( condition ) {
							return condition['id'] !== id;
						} );
					} );

					this.getPopupConditions();
				},

				computed: {
					emptyConditions: function() {
						return ( 0 === this.conditions.length ) ? true : false;
					},

					popupConditions: function() {
						return this.conditions;
					}
				},

				methods: {
					addCondition: function() {
						var newCond = {
							id: this.$root.genetateUniqId(),
							include: 'true',
							group: 'entire',
							subGroup: 'entire',
							subGroupValue: ''
						};

						this.conditions.unshift( newCond );
					},

					getPopupConditions: function () {

						this.getConditionsStatus = true;

						wp.apiFetch( {
							method: 'post',
							path: window.JetPopupLibraryConfig.getPopupConditionsPath,
							data: {
								popup_id: this.popupId,
							},
						} ).then( ( response ) => {

							this.getConditionsStatus = false;

							if ( response.success ) {
								this.relationType = response.data.relationType;
								this.conditions   = response.data.conditions;
							} else {
								this.$CXNotice.add( {
									message: response.message,
									type: 'error',
									duration: 5000,
								} );
							}
						} );
					},

					closeConditionsManagerPopupHandler: function () {
						this.$root.conditionsManagerPopupVisible = false;
					},

					saveConditions: function() {
						this.saveConditionsStatus = true;

						wp.apiFetch( {
							method: 'post',
							path: window.JetPopupLibraryConfig.updatePopupConditionsPath,
							data: {
								popup_id: this.popupId,
								conditions: this.conditions,
								relation_type: this.relationType,
							},
						} ).then( ( response ) => {
							this.saveConditionsStatus = false;

							if ( response.success ) {
								this.closeConditionsManagerPopupHandler();

								// Rerender verbose html
								$( `.jet-popup-conditions[data-popup-id="${ this.popupId }"] .jet-popup-conditions-list` ).html( response.data.verboseHtml );

								this.$CXNotice.add( {
									message: response.message,
									type: 'success',
									duration: 5000,
								} );
							} else {
								this.$CXNotice.add( {
									message: response.message,
									type: 'error',
									duration: 5000,
								} );
							}
						} );
					}
				}

			} );

			Vue.component( 'jet-popup-library-create-popup-form', {
				template: '#tmpl-jet-popup-library-create-popup-form',

				data: function() {
					return ( {
						popupCreatingStatus: false,
						isPopupCreated: false,
						rawPresetsData: window.JetPopupLibraryConfig.presetsData || [],
						newPopupData: {
							name: '',
							preset: false,
							contentType: 'elementor',
						}
					} )
				},

				computed: {
					presetsList: function () {
						return this.rawPresetsData.map( ( presetData) => {
							presetData.checked = presetData.id === this.newPopupData.preset;

							return presetData;
						} );
					}
				},

				methods: {
					closeCreatePopupHandler: function () {
						this.$root.createPopupVisible = false;
					},

					choosePresetHandler: function ( preset ) {

						if ( preset !== this.newPopupData.preset ) {
							this.newPopupData.preset = preset;
						} else {
							this.newPopupData.preset = false;
						}
					},

					createPopupHandler: function () {
						this.popupCreatingStatus = true;

						wp.apiFetch( {
							method: 'post',
							path: window.JetPopupLibraryConfig.createPopupPath,
							data: {
								preset: this.newPopupData.preset,
								contentType: this.newPopupData.contentType,
								name: this.newPopupData.name,
							},
						} ).then( ( response ) => {

							if ( response.success ) {
								this.isPopupCreated = true;

								if ( response.data.redirect ) {
									setTimeout( () => {
										window.open( response.data.redirect, '_self' ).focus();
									}, 2000 );
								}

								this.$CXNotice.add( {
									message: response.message,
									type: 'success',
									duration: 5000,
								} );
							} else {
								this.popupCreatingStatus = false;

								this.$CXNotice.add( {
									message: response.message,
									type: 'error',
									duration: 5000,
								} );
							}
						} );
					}
				}

			} );

			Vue.component( 'jet-popup-library-import-popup-form', {
				template: '#tmpl-jet-popup-library-import-popup-form',

				data: function() {
					return ( {
						importAction: window.JetPopupLibraryConfig.popupImportAction || false,
					} )
				},

				methods: {
					closeImportPopupHandler: function () {
						this.$root.importPopupVisible = false;
					},
				}

			} );

			Vue.component( 'jet-popup-library-settings-manager', {
				template: '#tmpl-jet-popup-library-settings-manager',

				props: {
					popupId: Number
				},

				data: function() {
					return ( {
						settings: [],
						saveSettingsStatus: false,
						getSettingsStatus: false,
						debounceSavingInterval: null,
						onDateValue: '',
						onDateTimeValue: '',
					} )
				},

				watch: {
					onDateValue: function ( curr ) {
						this.settings['jet_popup_on_date_value'] = curr + ' ' + this.onDateTimeValue;
					},
					onDateTimeValue: function ( curr ) {
						let currTime = curr.length ? curr : '00:00';

						this.settings['jet_popup_on_date_value'] = this.onDateValue + ' ' + currTime;
					},
					settings: {
						handler( options, prevOptions ) {

							if ( 0 === Object.keys( prevOptions ).length ) {
								return;
							}

							clearInterval( this.debounceSavingInterval );
							//this.debounceSavingInterval = setTimeout( this.saveSettings, 1000 );
						},
						deep: true
					}
				},

				created: function() {
					var vueInstance = this;

					this.getPopupSettings();
				},

				computed: {
					popupSettings: function() {
						return this.settings;
					},
					animationTypeOptions: function () {
						return window.JetPopupLibraryConfig.popupAnimationTypeOptions || [];
					},
					triggerTypeOptions: function () {
						return window.JetPopupLibraryConfig.popupOpenTriggerOptions || [];
					},
					timeDelayTypeOptions: function () {
						return window.JetPopupLibraryConfig.popupTimeDelayOptions || [];
					},
				},

				methods: {
					closeSettingsManagerPopupHandler: function () {
						this.$root.settingsManagerPopupVisible = false;
					},

					getPopupSettings: function () {

						this.getSettingsStatus = true;

						wp.apiFetch( {
							method: 'post',
							path: window.JetPopupLibraryConfig.getPopupSettingsPath,
							data: {
								popup_id: this.popupId,
							},
						} ).then( ( response ) => {

							this.getSettingsStatus = false;

							if ( response.success ) {
								let settings = response.data.settings;

								if ( settings.hasOwnProperty( 'jet_popup_on_date_value' ) ) {
									let dateTimeData = settings.jet_popup_on_date_value.split( ' ' );

									this.onDateValue = dateTimeData[0] || '';
									this.onDateTimeValue = dateTimeData[1] || '';
								}

								this.settings = settings;
							} else {
								this.$CXNotice.add( {
									message: response.message,
									type: 'error',
									duration: 5000,
								} );
							}
						} );
					},

					saveSettings: function() {
						this.saveSettingsStatus = true;
						//this.$root.savePopupSettingsStatus = true;

						wp.apiFetch( {
							method: 'post',
							path: window.JetPopupLibraryConfig.updatePopupSettingsPath,
							data: {
								popup_id: this.popupId,
								settings: this.settings,
							},
						} ).then( ( response ) => {
							this.saveSettingsStatus = false;
							//this.$root.savePopupSettingsStatus = false;

							if ( response.success ) {

								// Rerender verbose html
								$( `.jet-popup-settings[data-popup-id="${ this.popupId }"] .jet-popup-settings-list` ).html( response.data.verboseHtml );

								this.closeSettingsManagerPopupHandler();

								/*this.$CXNotice.add( {
									message: response.message,
									type: 'success',
									duration: 5000,
								} );*/
							} else {
								this.$CXNotice.add( {
									message: response.message,
									type: 'error',
									duration: 5000,
								} );
							}
						} );
					}
				}

			} );

			this.popupLibraryInstance = new Vue( {
				el: '#jet-popup-library',
				data: {
					isMounted: false,
					getPopupConditionsStatus: false,
					conditionsManagerPopupVisible: false,
					createPopupVisible: false,
					importPopupVisible: false,
					settingsManagerPopupVisible: false,
					savePopupSettingsStatus: false,
					savePopupConditionsStatus: false,
					rawConditionsData: window.JetPopupLibraryConfig.rawConditionsData || [],
					popupId: 0,
					urlLibraryAction: false,
					urlPopupId: false
				},

				mounted: function() {
					this.isMounted = true;

					const urlParams = new URLSearchParams( window.location.href );

					this.urlLibraryAction = urlParams.get( 'library_action' ) || false;
					this.urlPopupId = +urlParams.get( 'popup_id' ) || false;

					if ( this.urlLibraryAction || this.urlPopupId ) {

						switch ( this.urlLibraryAction ) {
							case 'edit_conditions':
								this.popupId = this.urlPopupId;
								this.conditionsManagerPopupVisible = true;

								break;
							case 'edit_settings':
								this.popupId = this.urlPopupId;
								this.settingsManagerPopupVisible = true;

								break;
						}
					}
				},

				computed: {
					itemClasses() {
						return [
							'jet-popup-library',
							this.isMounted ? 'is-mounted' : '',
						];
					},

					progressStatus() {
						return this.savePopupSettingsStatus || this.savePopupConditionsStatus;
					}
				},

				methods: {
					genetateUniqId: function() {
						return '_' + Math.random().toString(36).substr(2, 9);
					},
					closeCreatePopupHandler: function () {
						this.createPopupVisible = false;
					},
					closeImportPopupHandler: function () {
						this.importPopupVisible = false;
					},
					closeConditionsManagerPopupHandler: function () {
						this.conditionsManagerPopupVisible = false;
					},
					closeSettingsManagerPopupHandler: function () {
						this.settingsManagerPopupVisible = false;
					},
				}

			} );
		},


	};

	JetPopupAdmin.init();

}( jQuery ) );
