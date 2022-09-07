 'use strict';

 define([
    'jquery', 
    'underscore',
	'oro/translator',
	'pim/initselect2',
    'pim/form/common/fields/field',
	'ewave-attribute/attribute/properties',
    'ewave-attribute/template/attribute/fields',
	'bootstrap.bootstrapswitch',
  ], 
  function (
   $,
   _,
   __,
   initSelect2,
   BaseField,
   attributeProperties,
   template
 ) {
   return BaseField.extend({
		attributeProperties: attributeProperties,
     	template: _.template(template),
		events: {
			'change input': 'updateModel',
			'change select': 'updateModel',
		},

		/**
		 * Renders the container template.
		 */
		render() {
			this.updateformData();
			this.getTemplateContext().then(
				function (templateContext) {
					this.$el.html(this.template({ 
						__:__, 
						attributeProperties: this.attributeProperties, 
						formdata: this.getFormData() 
					}));

					this.postRender(templateContext);
					this.renderExtensions();
					this.delegateEvents();
				}.bind(this)
			);

			return this;
		},

		/**
		 * update form data
		 */
		updateformData: function() 
		{
			var data = this.getFormData();			
			_.each(this.attributeProperties, function(attributeProperty){
					if (_.isUndefined(data[attributeProperty.config.fieldName])) {
						data[attributeProperty.config.fieldName] = attributeProperty.default;						
					}
				});
			this.setData(data);
		},

		/**
		 * {@inheritdoc}
		 */
		updateModel: function(event) {
			var data = this.getFormData();
			if($(event.target).hasClass('select2') && ($(event.target).hasClass('select2-container-multi'))) {
				val = $(event.target).select2('data')
				val = val.map(function(obj) { return obj.id });                    
			} else if( $(event.target).is('input[type="checkbox"]')) {
				var val = $(event.target).is(':checked');
				if(val == true) {
					val = true;
				} else{
					val = false;
				}    
			} else {
				val = $(event.target).val();
			}

			data[$(event.target).attr('name')] = val;

			this.setData(data);
			this.render();
		},

		/**
		 * {@inheritdoc}
		 */
		postRender: function () {
			this.$('.switch').bootstrapSwitch();
		},

		/**
		 * {@inheritdoc}
		 */
		getFieldValue: function (field) {
			return this.getFormData()[this.config.fieldName];
		},
	});
 });
 
