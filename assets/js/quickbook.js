jQuery(document).ready(function($) {
	let confirmation = $('#confirmation-alert');

	if ($('#auto-sync-alert').length > 0) {
		let autoSync = $('#auto-sync-alert');
		autoSync.modal('toggle');
	}
	
	function removeConfirmationClass() {
		confirmation.attr('class', 'modal');
	}

	function newWindowPopup(myURL, title, myWidth, myHeight) {
		var left = (screen.width - myWidth) / 2;
		var top = (screen.height - myHeight) / 4;
		return window.open(myURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + myWidth + ', height=' + myHeight + ', top=' + top + ', left=' + left);
	}

	// select plan and pay for it
    $('.pricing-button').click(function(){
		let plan_id = $(this).data('id');
		let seller_uuid = $(this).data('seller-uuid');
		let change = $(this).data('change');
		let haschangePlan = $('.pricing-table').hasClass('seller-change-plan');

		if(change == "1") {
			removeConfirmationClass();
			confirmation.addClass('confirm-change-plan');
			confirmation.find('h4').html('Confirmation');
			confirmation.find('p').html('Are you sure you want to change plan?');
			confirmation.find('.confirmation-submit').data('seller-uuid', seller_uuid);
			confirmation.find('.confirmation-submit').data('plan_id', plan_id);
			confirmation.modal('toggle');
		} else {
			let planAjaxUrl = quickbookVariables.ajaxPostPlanUrl.replace("PLAN_ID", plan_id).replace("SELLER_UUID", seller_uuid);
			let params = '?domain='+quickbookVariables.domain;

			if(haschangePlan !== false) {
				params = params + '&change_plan=1';
			}
			let child = newWindowPopup(planAjaxUrl+params,'Plan', 630, 850);
			
			window.addEventListener("message", (event)=>{
				if((event.data.success == 1 || event.data.success == 0) && event.data.domain == quickbookVariables.domain) {
					child.close();
					location.reload();
				}
			});
		}
	});
	
	$(document).on('click', '.confirm-change-plan .confirmation-submit', function() {
		confirmation.modal('toggle');
		let plan_id = $(this).data('plan_id');
		let seller_uuid = $(this).data('seller-uuid');

		let data = [];
		data.push({name: 'plan_id', value: plan_id});
		ajaxSyncSettings(seller_uuid, "change_plan", data);
	});

	// Connect quickbooks
    $('.quickbook-connect').click(function(){
		quickBookConnect($(this).data('type'), $(this).data('seller-uuid'));
	});

	// Connect quickbooks
    $('.switch-quickbook-connect').click(function(){
		removeConfirmationClass();
		confirmation.addClass('confirm-switch-qb');
		confirmation.find('h4').html('Switch Quickbook connection');
		confirmation.find('p').html('Are you sure you want to switch account? After switching account you will lost old sync data...');
		confirmation.find('.confirmation-submit').data('type', $(this).data('type'));
		confirmation.find('.confirmation-submit').data('seller-uuid', $(this).data('seller-uuid'));
		confirmation.modal('toggle');
	});

	// Connect quickbooks
    $('.disconnect-quickbook').click(function(){
		removeConfirmationClass();
		confirmation.addClass('confirm-disconnect-qb');
		confirmation.find('h4').html('Are you sure you want to disconnect QB?');
		//confirmation.find('p').html('After disconnect account you will lost old sync data...');
		confirmation.find('.confirmation-submit').data('seller-uuid', $(this).data('seller-uuid'));
		confirmation.modal('toggle');
	});
	
	$(document).on('click', '.confirm-disconnect-qb .confirmation-submit', function() {
		confirmation.modal('toggle');
		ajaxQbo($(this).data('seller-uuid'), 'disconnect_quickbook');
	});

	$(document).on('click', '.confirm-switch-qb .confirmation-submit', function() {
		confirmation.modal('toggle');
		quickBookConnect($(this).data('type'), $(this).data('seller-uuid'), true);
	});

	$('.auto-sync-btn').click( function() {
		ajaxQbo($(this).data('seller-uuid'), 'update_auto_sync', [], false);
	});
	
	// auto click on button when find reconnect to quickbooks
	$('#quickbook-connect-btn').click();

	// function call or quickbook
	function quickBookConnect(type, seller_uuid, qb_switch = false) {
		let quickBookConnectUrl = quickbookVariables.quickbookConnectUrl.replace("SELLER_UUID", seller_uuid).replace("CONNECTIONTYPE", type);
		
		let child = newWindowPopup(quickBookConnectUrl+'?qb_switch='+qb_switch+'&domain='+quickbookVariables.domain, 'QuickBook Connect', 630, 850);
        
        window.addEventListener("message", (event)=>{
            if(event.data.connected == 1 && event.data.domain == quickbookVariables.domain) {
                child.close();
                location.reload();
            }
        });
	}

	// ajax call for sync settings
	function ajaxQbo(seller_uuid, api_action, objData = [], modal = true) {
		objData.push({name: 'action', value: "kt_ajax_actionCall"});
		objData.push({name: 'api_action', value: api_action});
		objData.push({name: 'seller_uuid', value: seller_uuid});

		$("body").append('<div class="quickbook-overlay"></div>').addClass("quickbook-loading"); 

		jQuery.post(
			quickbookVariables.ajaxUrl,
			objData, 
		function(data, status, xhr) {
			if (typeof data != "string") {
				if (modal) {
					window.location.href = quickbookVariables.settingUrl;
				} else {
					if('Message' in data) {
						alert(data.Message);
					}
					location.reload();
				}
			} else {
				alert('failed, ' + data);
			}
		})
		.done( function() { 
			$("body").removeClass("quickbook-loading"); 
			$('.quickbook-overlay').remove();
		})
		.fail(
			function(jqxhr, settings, ex) { alert('failed, ' + ex); 
		});
	}

	// get customers or products from quickbook with ajax
	function getListofCustomerProduct(val, firstElement, apiAction, listClass, appendId) {
		$('#'+appendId).html('');

		if (val == 'SAME') {
			$("body").addClass("quickbook-loading").append('<div class="quickbook-overlay"></div>'); 

			let syncData = [];
			let seller_uuid = $('.form-wizard-submit').data('seller-uuid');
			syncData.push({name: 'action', value: "kt_ajax_actionCall"});
			syncData.push({name: 'api_action', value: apiAction});
			syncData.push({name: 'seller_uuid', value: seller_uuid});

			var listSel = document.getElementById(appendId);

			jQuery.post(
				quickbookVariables.ajaxUrl,
				syncData, 
			function(data, status, xhr) {
				if ('errorcode' in data) {
					quickBookConnect(data.connection_type, seller_uuid);
				} else {
					$.each(data.lists, function(index, list){
						var opt = document.createElement('option');
						opt.innerHTML = list;
						opt.value = index;
						listSel.appendChild(opt);
					});
					listSel.insertBefore(new Option(firstElement, ''), listSel.firstChild);
					listSel.value = '';
				}
			})
			.done( function() { 
				$('.'+listClass).removeClass('hidden');
				$('#'+appendId).addClass('wizard-required');
				$("body").removeClass("quickbook-loading"); 
				$('.quickbook-overlay').remove();
			})
			.fail(
				function(jqxhr, settings, ex) { alert('failed, ' + ex); 
			});
		} else {
			$('.'+listClass).addClass('hidden');
			$('#'+appendId).removeClass('wizard-required');
		}
	}

	// Form on change dropdown events
	$('#customer_type').change(function(){
		let val = $(this).val();
		getListofCustomerProduct(val, '--- Select Customer ---', "get_qb_customers", 'customer_list', 'customer_id');
	});

	$('#product_type').change(function(){
		let val = $(this).val();
		if ("UNIQUE" === val) {
			$(".product-matching").show();
			$('.product_list').addClass('hidden');
			$('#product_id').removeClass('wizard-required');
		} else {
			$(".product-matching").hide();
			getListofCustomerProduct(val, '--- Select Product ---', "get_qb_products", 'product_list', 'product_id');
		}
	});
	
	$('#shipping_product_type').change(function(){
		let val = $(this).val();
		getListofCustomerProduct(val, '--- Select Product ---', "get_qb_products", 'shipping_product_list', 'shipping_product_id');
	});

	// sync date datepicker initiate
	let today = new Date();
	let startDate = new Date(today.getFullYear(), today.getMonth()-2, 1);
	$('#sync_start_date').datepicker({
        clearBtn: true,
		format: "mm/dd/yyyy",
		orientation: "bottom auto",
		startDate: startDate,
        endDate: "+0D"
    });

	function validationCheck(parentFieldset) {
		let error = false;
		parentFieldset.find('.wizard-required').each(function() {
			var thisValue = $(this).val();
			var thisInput = $(this).attr('name');

			if( thisValue == "" || thisValue == "_empty_" ) {
				error = true;
				$(this).siblings(".wizard-form-error").slideDown();
			}
			else {
				if(thisInput == 'email') {
					if(!emailValidate(thisValue)){
						error = true;
						$(this).siblings(".wizard-form-error").slideDown();
					} else {
						$(this).siblings(".wizard-form-error").slideUp();
					}
				} else {
					$(this).siblings(".wizard-form-error").slideUp();
				}
			}
		});

		return error;
	}
	// click on next button
	$('.form-wizard-next-btn').click(function() {
		var parentFieldset = $(this).parents('.wizard-fieldset');
		var currentActiveStep = $(this).parents('.form-wizard').find('.form-wizard-steps .active');
		var next = $(this);
		var nextWizardStep = validationCheck(parentFieldset);

		if(!nextWizardStep) {
			next.parents('.wizard-fieldset').removeClass("show","400");
			currentActiveStep.removeClass('active').addClass('activated').next().addClass('active',"400");
			next.parents('.wizard-fieldset').next('.wizard-fieldset').addClass("show","400");
			$(document).find('.wizard-fieldset').each(function(){
				if($(this).hasClass('show')){
					var formAtrr = $(this).attr('data-tab-content');
					$(document).find('.form-wizard-steps .form-wizard-step-item').each(function(){
						if($(this).attr('data-attr') == formAtrr){
							$(this).addClass('active');
							var innerWidth = $(this).innerWidth();
							var position = $(this).position();
							$(document).find('.form-wizard-step-move').css({"left": position.left, "width": innerWidth});
						}else{
							$(this).removeClass('active');
						}
					});
				}
			});
		}
	});
	//click on previous button
	$('.form-wizard-previous-btn').click(function() {
		var counter = parseInt($(".wizard-counter").text());;
		var prev =$(this);
		var currentActiveStep = $(this).parents('.form-wizard').find('.form-wizard-steps .active');
		prev.parents('.wizard-fieldset').removeClass("show","400");
		prev.parents('.wizard-fieldset').prev('.wizard-fieldset').addClass("show","400");
		currentActiveStep.removeClass('active').prev().removeClass('activated').addClass('active',"400");
		$(document).find('.wizard-fieldset').each(function(){
			if($(this).hasClass('show')){
				var formAtrr = $(this).attr('data-tab-content');
				$(document).find('.form-wizard-steps .form-wizard-step-item').each(function(){
					if($(this).attr('data-attr') == formAtrr){
						$(this).addClass('active');
						var innerWidth = $(this).innerWidth();
						var position = $(this).position();
						$(document).find('.form-wizard-step-move').css({"left": position.left, "width": innerWidth});
					}else{
						$(this).removeClass('active');
					}
				});
			}
		});
	});
	//click on form submit button
	$(".form-wizard.setting-form .form-wizard-submit").click(function(e){
		e.preventDefault();
		var parentFieldset = $(this).parents('.wizard-fieldset');
		let error = validationCheck(parentFieldset);

		if (!error) {
			let syncData = $("#quickbooks_tabs form").serializeArray();
			ajaxSyncSettings($(this).data('seller-uuid'), "store_syncsettings", syncData);
		}
	});
	
	// ajax call for sync settings
	function ajaxSyncSettings(seller_uuid, api_action, syncData, modal = false, dropdownId = null, modalId = null) {
		syncData.push({name: 'action', value: "kt_ajax_actionCall"});
		syncData.push({name: 'api_action', value: api_action});
		syncData.push({name: 'seller_uuid', value: seller_uuid});

		$("body").append('<div class="quickbook-overlay"></div>').addClass("quickbook-loading"); 
		$('.statusMsg').removeClass('alert alert-danger').html('');

		jQuery.post(
			quickbookVariables.ajaxUrl,
			syncData, 
		function(data, status, xhr) {
			if (typeof data != "string") {
				if (modal) {
					if (data.error_code) {
						$('.statusMsg').html(data.error_msg).addClass('alert alert-danger');
					} else {
						if(dropdownId) {
							let listTypeSel = document.getElementById(dropdownId);
							var opt = document.createElement('option');
							if ('DisplayName' in data.obj) {
								opt.innerHTML = data.obj.DisplayName;
							} else {
								opt.innerHTML = data.obj.Name;
							}
							opt.value = data.obj.Id;
							listTypeSel.appendChild(opt);
							listTypeSel.value = data.obj.Id;
							$('#'+modalId).modal('toggle');
							$(`#${modalId} form`)[0].reset();
						} else {
							if(modalId == 'contactusModal') {
								$(`#${modalId} .statusMsg`).html(data.message).addClass('alert alert-success');
								$(`#${modalId} form`)[0].reset();
								setTimeout(function() { 
									$('#'+modalId).modal('toggle');
									$(`#${modalId} .statusMsg`).html('').removeClass('alert alert-success');
								}, 3000);
							} else {
								location.reload();
							}
						}
					}
				} else {
					window.location.href = quickbookVariables.dashboardUrl;
				}
			} else {
				alert('failed, ' + data);
			}
		})
		.done( function() { 
			$("body").removeClass("quickbook-loading"); 
			$('.quickbook-overlay').remove();
		})
		.fail(
			function(jqxhr, settings, ex) { alert('failed, ' + ex); 
		});
	}

	// open account modal for add account in quickbooks
	$('.account-modal-open').click(function() {
		let accountType = $(this).data('account-type');
		let accountlist = JSON.parse(accountLists);
		let listTypeSel = document.getElementById('account_type');
		let listSubTypeSel = document.getElementById('account_sub_type');
		let accountDropdownId = $(this).data('account-dropdown-id');
		$('#account_name').val('');
		listTypeSel.innerHTML = '';
		listSubTypeSel.innerHTML = '';
		listSubTypeSel.parentNode.classList.add("hidden");

		$('.saveAccount').data('account-dropdownId', accountDropdownId);

		jQuery.each(accountlist.type[accountType], function(index, value) {
			var opt = document.createElement('option');
			opt.innerHTML = value;
			opt.value = value;
			listTypeSel.appendChild(opt);
		});

		if(accountType in accountlist.subtype) {
			listSubTypeSel.parentNode.classList.remove("hidden");
			jQuery.each(accountlist.subtype[accountType], function(index, value) {
				var opt = document.createElement('option');
				opt.innerHTML = value;
				opt.value = value;
				listSubTypeSel.appendChild(opt);
			});
		}
		removeStatusMsg();
		$('#accountModalForm').modal('show');
	});

	$('.saveAccount').click(function() {
		var parentFieldset = $(this).parents('.modal-content');
		let error = validationCheck(parentFieldset);

		if (!error) {
			let syncData = $("#accountForm").serializeArray();
			let accountDropdownId = $(this).data('account-dropdownId');
			ajaxSyncSettings($(this).data('seller-uuid'), "create_qbAccount", syncData, true, accountDropdownId, 'accountModalForm');
		}
	});

	$('#prefix_required').change(function() {
		if(this.checked) {
			$('#prefix').removeClass('wizard-required');
		} else {
			$('#prefix').addClass('wizard-required');
		}
	});

	$('#tax_agency_required').change(function() {
		if(this.checked) {
			$('#tax_agency').removeClass('wizard-required');
		} else {
			$('#tax_agency').addClass('wizard-required');
		}
	});

	$('#tax_code_required').change(function() {
		if(this.checked) {
			$('#tax_code').removeClass('wizard-required');
			$('#zero_tax_code').removeClass('wizard-required');
		} else {
			$('#tax_code').addClass('wizard-required');
			$('#zero_tax_code').addClass('wizard-required');
		}
	});

	$('#cad-tax-rate-submit').click(function() {
		let cadTaxData = {};
		$("#CADTaxRates select option:selected[value!='']").parent().each(function(){
			cadTaxData[$(this).data('provience')] = $(this).val();
		})
		$("#cad_multi_tax").val(JSON.stringify(cadTaxData));
	});

	$('#gbp-tax-rate-submit').click(function() {
		let gbpTaxData = {};
		$("#GBPTaxRates select option:selected[value!='']").parent().each(function(){
			gbpTaxData[$(this).data('country')] = $(this).val();
		})
		$("#gbp_multi_tax").val(JSON.stringify(gbpTaxData));
	});

	$('.add-more-tax').click(function() {
		let multiTax = $('.add_more_tax_code').clone().removeClass('hidden add_more_tax_code').addClass('col-md-12');
		$("#MultiTaxRates").append(multiTax);
	});

	$(document).on('click', '.remove-tax-code', function() {
		$(this).closest('.col-md-12').remove();
	});

	$('#multi-tax-rate-submit').click(function(e) {
		let multiTaxData = {};
		let error = false;
		$('.multitax_error').html('');
		$('#MultiTaxRates').find('.col-md-12').each(function(){
			let inputVal = $(this).find('input').val();
			let selectVal =  $(this).find('select').val();
			if (inputVal == '' || selectVal == '') {
				error = true;
			}
			multiTaxData[$(this).find('input').val()] = $(this).find('select').val();
		});

		if(error) {
			$('.multitax_error').html('Please fill all fields.');
		} else {
			$("#multi_tax_codes").val(JSON.stringify(multiTaxData));
			$('#multi-tax-rates').modal('toggle');
		}
	});

	$('.sync-orders').click(function() {
		ajaxQbo($(this).data('seller-uuid'), 'sync_orders', [], false);
	});

	// open customer or product modal for add in quickbooks
	$('.cust-prod-modal-open').click(function() {
		let dataType = $(this).data('type');
		let custProdDropdownId = $(this).data('custprod-dropdown-id');
		$('#cust_prod_name').val('');
		$('#custProdModalLabel').html('Add '+dataType+' in QBO Account');
		$('.saveCustProd').data('custprod-dropdownId', custProdDropdownId);
		$('.saveCustProd').data('type', dataType);
		$('.product_account_div').addClass('hidden');
		$('#product_account').val('');
		$('#product_account').removeClass('wizard-required');
		$('.wizard-required').siblings(".wizard-form-error").slideUp();

		if(dataType == 'Product') {
			$('.product_account_div').removeClass('hidden');
			$('#product_account').addClass('wizard-required');
		}
		removeStatusMsg();
		$('#custProdModalForm').modal('show');
	});

	$('.saveCustProd').click(function() {
		var parentFieldset = $(this).parents('.modal-content');
		let error = validationCheck(parentFieldset);

		if (!error) {
			let syncData = $("#custProdForm").serializeArray();
			let custProdDropdownId = $(this).data('custprod-dropdownId');
			let dataType = $(this).data('type');
			if (dataType == 'Product') {
				syncData.push({name: 'accountName', value: $( "#product_account option:selected" ).text()});
			}
			ajaxSyncSettings($(this).data('seller-uuid'), "create_qb"+dataType, syncData, true, custProdDropdownId, 'custProdModalForm');
		}
		
	});

	$('#contact-us-send').click(function() {
		var parentFieldset = $(this).parents('.modal-content');
		let error = validationCheck(parentFieldset);
		
		tinyMCE.triggerSave();
		if (!error) {
			let syncData = $("#contactus").serializeArray();

			ajaxSyncSettings($(this).data('seller-uuid'), "send_query", syncData, true, '', 'contactusModal');
		}
	});

	$('.reply-Y').each(function() {
		$(this).closest('tr').addClass('admin_reply');
	});

	function emailValidate(email) {
		if (/^([a-zA-Z0-9_\-\.]+)\+?([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/.test(email)) {
			return true;
		} else {
			return false;
		};
	}
	function removeStatusMsg() {
		jQuery('#accountModalForm form')[0].reset();
		jQuery('#custProdModalForm form')[0].reset();
		$('.statusMsg').removeClass('alert alert-danger').html('');
	}
	$('#seller-contacts-send').click(function() {
		var parentFieldset = $(this).parents('.modal-content');
		let error = validationCheck(parentFieldset);

		if (!error) {
			let syncData = $("#seller_contacts").serializeArray();

			ajaxSyncSettings($(this).data('seller-uuid'), "add_seller_contacts", syncData, true, '', 'addContactsModal');
		}
	});

	$('.kento-alert .notice-dismiss').click(function() {
		$(this).parents('.kento-alert-notify').remove();
	});

	$('.setting-confirm-btn').click(function() {
		let that = $(this).parents('.setting-confirm');
		if (that.hasClass('currency-confirmation') && $('.tax-confirmation').length > 0) {
			that.addClass("hide");
			$('.tax-confirmation').removeClass("hide");
		} else {
			that.addClass("hide");
			$('#qb-settings').removeClass("hide");
		}
	});
});

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5f9b9cc616ea1756a6dec64f/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();