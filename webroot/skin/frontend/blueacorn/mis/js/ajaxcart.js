/**
 * @package     BlueAcorn_AjaxCart
 * @version
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */
var $j = $j || jQuery.noConflict();

var BA_AjaxAddToCart = Class.create({
	init: function() {
		this.config = {
			btnLabelAdd:    'Add To Cart',
			btnLabelAdding: 'Adding',
			btnLabelAdded:  'Added',
			cartLink:       '.header-minicart .skip-cart',
			cartId:         '#header-cart'
		};
		this.setOnClickEvent();
	},
	
	onChange: function(state, button) {
		var $this = this;
		var btnLabel = button.children();
		
		while (btnLabel.length) {
			btnLabel = btnLabel.children();
		}
		
		switch (state) {
			case 'adding':
				button.prop('disabled', true);
				btnLabel.end().text($this.config.btnLabelAdding);
			break;
			
			case 'added':
				button.prop('disabled', false);
				btnLabel.end().text($this.config.btnLabelAdded);
				
				setTimeout(function() {
					btnLabel.end().text($this.config.btnLabelAdd);
				},
				100);
			break;
		}
	},
	
	addProduct: function(url, params, button) {
		var $this = this;
		
		$this.onChange('adding', button);
		
		$j.ajax({
			url: url,
			method: 'POST',
			data: params
		}).done(function(result) {
			try {
				var jsonData = JSON.parse(result);
				if ( jsonData.success ) {
					$j($this.config.cartId).html(jsonData.carthtml);
					$j($this.config.cartLink).children('span.count').html(jsonData.qty);
					$j($this.config.cartLink).children('span.subtotal').html(jsonData.total);
					$j($this.config.cartLink).removeClass('no-count');
					
					$j('#product_addtocart_form .btn-cart').after('<ul class="messages"><li class="success-msg"><ul><li><span>' + jsonData.message + '</span></li></ul></li></ul>');
				}
				else {
					$j('#product_addtocart_form .btn-cart').after('<ul class="messages"><li class="error-msg"><ul><li><span>' + jsonData.message + '</span></li></ul></li></ul>');
				}
				
				var messageNode = $j('#product_addtocart_form ul.messages');
				if ( messageNode.length ) {
					setTimeout(function() {
						messageNode.hide('blind', {}, 500);
						messageNode.remove();
					}, jsonData.duration);
				}
			}
			catch (e) {
				console.log('Ajax Cart: ' + e.message);
			}
			$this.onChange('added', button);
		}).error(function(jqXHR, textStatus, errorThrown) {
			console.log('Ajax Cart Error: ' + errorThrown);
			$this.onChange('added', button);
		});
	},
	
	ajaxAddToCart: function(button) {
		var varienForm = new VarienForm('product_addtocart_form');
		
		if (varienForm.validator.validate()) {
			var form = $j('#product_addtocart_form');
			var url = form.attr('action');
			var params = form.serialize();
			this.addProduct(url, params, button);
		}
	},
	
	setOnClickEvent: function() {
		var $this = this;
		
		var button = $j('#product_addtocart_form .btn-cart');
		button.removeAttr('onclick');
		button.on('click', function(event) {
			event.preventDefault();
			$this.ajaxAddToCart(button);
		});
	}
});

$j(document).ready(function() {
	var ajaxAddToCart = new BA_AjaxAddToCart();
	ajaxAddToCart.init();
});