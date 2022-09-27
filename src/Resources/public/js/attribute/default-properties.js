'use strict';

define([
   'jquery', 
   'underscore',
   'oro/translator',
   'pim/form/common/fields/field',
   'ewave-attribute/template/attribute/default-fields',
   'pim/fetcher-registry',   
   'jquery.select2',
   'bootstrap.bootstrapswitch',
 ], 
 function (
  $,
  _,
  __,
  BaseField,
  template,
  fetcherRegistry
) {
  return BaseField.extend({
	   template: _.template(template),
	   events: {
		   'change input': 'updateModel',
		   'change select': 'updateModel',
	   },

	   /**
		* {@inheritdoc}
		*/
	  	render() {
			fetcherRegistry
				.getFetcher('locale')
				.fetchActivated()
				.then(
					function (locales) {
						var formData = this.getFormData();
						console.log('formData : ', formData, locales);
						this.$el.html(
							this.template({
								isEditable: false,
								__: __,
								formData: formData,
								locales: locales,
								availableLocales: [],
							})
						);
						this.postRender();
						this.renderExtensions();
					}.bind(this)
				);

			return this;
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
				this.$('.select2').select2();
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
