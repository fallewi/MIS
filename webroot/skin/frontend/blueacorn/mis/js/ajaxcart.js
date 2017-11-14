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
			btnLabelAdd:       'Add to Cart',
			btnLabelAdding:    'Adding',
			btnLabelAdded:     'Added',
			btnLabelRemove:    'Remove from Cart',
			btnLabelRemoving:  'Removing',
			btnLabelRemoved:   'Removed',
			cartLink:          '.header-minicart .skip-cart',
			cartId:            '#header-cart'
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
				button.addClass('cart-processing');
				btnLabel.end().text($this.config.btnLabelAdding);
			break;
			case 'added':
				button.prop('disabled', false);
				button.removeClass('cart-processing');
				btnLabel.end().text($this.config.btnLabelAdded);
				
				setTimeout(function() {
					btnLabel.end().text($this.config.btnLabelAdd);
				},
				1500);
			break;
			case 'failed_add':
				button.prop('disabled', false);
				button.removeClass('cart-processing');
				btnLabel.end().text($this.config.btnLabelAdd);
			break;
			case 'remove':
				button.prop('disabled', false);
				button.removeClass('cart-processing');
				button.prop('title', $this.config.btnLabelRemove);
				button.addClass('cart-remove');
				button.parent().prev().hide();
				button.parent().css("width", "100%");
				
				btnLabel.end().text($this.config.btnLabelRemove);
				button.off('click');
				button.on('click', function(event) {
					event.preventDefault();
					$this.removeProduct(button, 'related');
				});
			break;
			case 'removing':
				button.prop('disabled', true);
				btnLabel.end().text($this.config.btnLabelRemoving);
			break;
			case 'removed':
				button.prop('disabled', false);
				button.removeClass('cart-remove');
				button.prop('title', $this.config.btnLabelAdd);
				button.parent().css("width", "74%");
				button.parent().prev().show();
				btnLabel.end().text($this.config.btnLabelRemoved);
				
				button.off('click');
				button.on('click', function(event) {
					event.preventDefault();
					$this.ajaxAddToCartRelated(this);
				});
				
				setTimeout(function() {
					btnLabel.end().text($this.config.btnLabelAdd);
				},
				1500);
			break;
		}
	},
	
	addProduct: function(url, params, button, type) {
		var $this = this;
		
		$this.onChange('adding', button);
		
		$j.ajax({
			url: url,
			method: 'POST',
			data: params
		}).done(function(result) {
			try {
				var jsonData = JSON.parse(result);
				var msgClass = 'error-msg';
				if ( jsonData.success ) {
					$j($this.config.cartId).html(jsonData.carthtml);
					$j($this.config.cartLink).children('span.count').html(jsonData.qty);
					$j($this.config.cartLink).children('span.subtotal').html(jsonData.total);
					$j($this.config.cartLink).removeClass('no-count');
					msgClass = 'success-msg';
				}
				
				switch ( type ) {
					case 'related':
						$this.showMessage(
							$j('#block-related-list'),
							'before',
							'<ul class="messages"><li class="' + msgClass + '"><ul><li><span>' + jsonData.message + '</span></li></ul></li></ul>',
							'.block-related ul.messages',
							jsonData.duration
						);
						
						if ( jsonData.success ) {
							$this.onChange('remove', button);
						}
						else {
							$this.onChange('failed_add', button);
						}
					break;
					default:
						$this.showMessage(
							button,
							'after',
							'<ul class="messages"><li class="' + msgClass + '"><ul><li><span>' + jsonData.message + '</span></li></ul></li></ul>',
							'#product_addtocart_form ul.messages',
							jsonData.duration
						);
						
						$this.onChange('added', button);
				}
			}
			catch (e) {
				console.log('Ajax Cart: ' + e.message);
				
				$this.onChange('failed_add', button);
			}
		}).error(function(jqXHR, textStatus, errorThrown) {
			switch ( type ) {
				case 'related':
					$this.showMessage(
						$j('#block-related-list'),
						'before',
						'<ul class="messages"><li class="' + msgClass + '"><ul><li><span>' + errorThrown + '</span></li></ul></li></ul>',
						'.block-related ul.messages',
						jsonData.duration
					);
				break;
				default:
					$this.showMessage(
						button,
						'after',
						'<ul class="messages"><li class="' + msgClass + '"><ul><li><span>' + errorThrown + '</span></li></ul></li></ul>',
						'#product_addtocart_form ul.messages',
						jsonData.duration
					);
			}
			
			$this.onChange('failed_add', button);
		});
	},
	
	removeProduct: function(button, type) {
		var $this = this;
		
		$this.onChange('removing', button);
		
		var url;
		switch ( type ) {
			case 'related':
					var relForm = button.closest('.related_addtocart_form')[0];
					if ( relForm.length ) {
						var form = $j('#' + relForm.id);
						url = form.attr('action');
						url = url.replace('checkout/cart/add', 'checkout/cart/remove');
				}
			break;
		}
		
		$j.ajax({
			url: url,
			method: 'POST'
		}).done(function(result) {
			try {
				var jsonData = JSON.parse(result);
				var msgClass = 'error-msg';
				if ( jsonData.success ) {
					$j($this.config.cartId).html(jsonData.carthtml);
					$j($this.config.cartLink).children('span.count').html(jsonData.qty);
					$j($this.config.cartLink).children('span.subtotal').html(jsonData.total);
					$j($this.config.cartLink).removeClass('no-count');
					msgClass = 'success-msg';
				}
				
				switch ( type ) {
					case 'related':
						$this.showMessage(
							$j('#block-related-list'),
							'before',
							'<ul class="messages"><li class="' + msgClass + '"><ul><li><span>' + jsonData.message + '</span></li></ul></li></ul>',
							'.block-related ul.messages',
							jsonData.duration
						);
						
						$this.onChange('removed', button);
					break;
				}
			}
			catch (e) {
				console.log('Ajax Cart: ' + e.message);
				
				$this.onChange('removed', button);
			}
		}).error(function(jqXHR, textStatus, errorThrown) {
			switch ( type ) {
				case 'related':
					$this.showMessage(
						$j('#block-related-list'),
						'before',
						'<ul class="messages"><li class="' + msgClass + '"><ul><li><span>' + errorThrown + '</span></li></ul></li></ul>',
						'.block-related ul.messages',
						jsonData.duration
					);
				break;
			}
			
			$this.onChange('removed', button);
		});
	},
	
	showMessage: function(msgElem, position, msgText, msgNode, duration) {
		switch ( position ) {
			case 'before':
				msgElem.before(msgText);
			break;
			case 'after':
				msgElem.after(msgText);
			break;
		}
		
		var msgNodeObject = $j(msgNode);
		if ( msgNodeObject.length ) {
			setTimeout(function() {
				msgNodeObject.hide('blind', {}, 500);
				msgNodeObject.remove();
			}, duration);
		}
	},
	
	ajaxAddToCart: function(button) {
		var varienForm = new VarienForm('product_addtocart_form');
		
		if (varienForm.validator.validate()) {
			var form = $j('#product_addtocart_form');
			var url = form.attr('action');
			var params = form.serialize();
			this.addProduct(url, params, button, 'default');
		}
	},
	
	ajaxAddToCartRelated: function(button) {
		var relForm = $j(button).closest('.related_addtocart_form')[0];
		if ( relForm.length ) {
			var varienForm = new VarienForm(relForm.id);
			if (varienForm.validator.validate()) {
				var form = $j('#' + relForm.id);
				var url = form.attr('action');
				var params = form.serialize();
				this.addProduct(url, params, $j(button), 'related');
			}
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
		
		var relButtons = $j('.related_addtocart_form .btn-cart');
		if ( relButtons.length ) {
			relButtons.on('click', function(event) {
				event.preventDefault();
				$this.ajaxAddToCartRelated(this);
			});
		}
	}
});

$j(document).ready(function() {
	var ajaxAddToCart = new BA_AjaxAddToCart();
	ajaxAddToCart.init();
});