/**
 * Frontend Script.
 * 
 * @package FutureWordPress WeMakeContent.
 */

import Swal from "sweetalert2"; // "success", "error", "warning", "info" or "question"
import { toast } from 'toast-notification-alert';
import validator from 'validator';

// const stripe = require('stripe')('your_stripe_api_key');
// import stripe from 'stripe';
// import {loadStripe} from '@stripe/stripe-js';
// const stripe = await loadStripe('pk_test_TYooMQauvdEDq54NiTphI7jx');

/**
 * import Stripe from 'stripe';
const stripe = new Stripe('sk_test_...');

const customer = await stripe.customers.create({
  email: 'customer@example.com',
});

console.log(customer.id);
 */



( function ( $ ) {
	class FutureWordPress_Frontend {
		/**
		 * Constructor
		 */
		constructor() {
			this.ajaxUrl = fwpSiteConfig?.ajaxUrl ?? '';
			this.ajaxNonce = fwpSiteConfig?.ajax_nonce ?? '';
			this.lastAjax	 = false;this.profile	 = fwpSiteConfig?.profile ?? false;
			var i18n = fwpSiteConfig?.i18n ?? {};this.noToast	 = true;
			this.i18n = {
				confirm_cancel_subscribe	: 'Do you really want to cancel this Subscription?',
				i_confirm_it							: 'Yes I confirm it',
				confirming								: 'Confirming',
				successful								: 'Successful',
				submit										: 'Submit',
				request_failed						: 'Request failed',
				give_your_old_password		: 'Give here your old password',
				you_paused								: 'Subscription Paused',
				you_paused_msg						: 'Your retainer subscription has been successfully paused. We\'ll keep your account on hold until you\'re ready to resume. Thank you!',
				you_un_paused							: 'Subscription Resumed',
				you_un_paused_msg					: 'Welcome back! Your retainer subscription has been successfully resumed. We\'ll continue to provide you with our services as before. Thank you!',
				sometextfieldmissing			: 'Some required field you missed. Pleae fillup them first, then we can proceed.',
				rqstrongpass							: 'Strong password required',
				renumber									: 'Only field allowed number only. Please recheck.',
				rqemail										: 'You provide a wrong email address. Please fix.',
				passnotmatched						: 'Password not matched',
				are_u_sure								: 'Are you sure?',
				sure2logout								: 'Are you to Logout?',
				subscription_toggled			: 'Thank you for submitting your request. We have reviewed and accepted it, and it is now pending for today. You will have the option to change your decision tomorrow. Thank you for your patience and cooperation.',
				say2wait2pause						: 'You\'ve already paused your subscription this month. Please wait until 60 days over to pause again. If you need further assistance, please contact our administrative team.',
				rusure2unsubscribe				: 'You can only pause you retainer once every 60 days. Are you sure you want to pause your retainer?',
				rusure2subscribe					: 'We are super happy you want to resume your retainer. Are you sure you want to start now?',
				...i18n
			}
			this.init();this.toOpenEdit();this.inputEventListner();
			this.cancelSubscription();this.changePassword();
			this.passwordToggle();this.toggleSubscriptions();
			this.regWidget();this.handleTabHref();
			this.submitArchiveFiles();this.trackWpformAjax();
			this.setup_hooks();this.deleteArchive();
			this.handlePayCardError();this.selectRegistration();
			this.changePaymentCard();this.profileImgUpload();
			// this.btnLogOutConfirm();
			// this.toggleStatus();
			// this.fetchDataWidthContract();
			// this.accordion();
			// console.log( 'frontend init...' );
		}
		init() {
			const thisClass = this;var theInterval, selector;
			selector = '.fwp-tabs__navs';
			theInterval = setInterval( () => {
				document.querySelectorAll( selector + ':not([data-handled])' ).forEach( ( e, i ) => {
					e.dataset.handled = true;
					thisClass.tabs( e );
				} );
			}, 1000 );
		}
		tabs( e ) {
			e.querySelectorAll( '.fwp-tabs__nav-item' ).forEach( ( tabEl, tabI ) => {
				tabEl.addEventListener( 'click', function( event ) {
					if( this.dataset.target ) {
						e.querySelector( '.active' ).classList.remove( 'active' );
						this.classList.add( 'active' );
						document.querySelector( this.dataset.target ).parentElement.querySelector( '.active' ).classList.remove( 'active' );
						document.querySelector( this.dataset.target ).classList.add( 'active' );
					}
				} );
			} );
		}
		cancelSubscription() {
			const thisClass = this;var node, theInterval;
			theInterval = setInterval(() => {
				node = document.querySelector( '.fwp-sweetalert-field:not([data-handled])' );
				if( node ) {
					node.dataset.handled = true;
					node.addEventListener( 'click', ( event ) => {
						const swalWithBootstrapButtons = Swal.mixin({
							customClass: {
								confirmButton			: 'btn btn-danger',
								cancelButton			: 'btn btn-primary'
							},
							buttonsStyling: false
						})
						swalWithBootstrapButtons.fire({
							title: 'Attantion!',
							text: thisClass.i18n.confirm_cancel_subscribe,
							icon: 'warning',
							confirmButtonText: thisClass.i18n.i_confirm_it,
							// input: 'text',
							// inputAttributes: {
							//   autocapitalize: 'off'
							// },
							showCancelButton: true,
							showLoaderOnConfirm: true,
							preConfirm: (login) => {
								return fetch( thisClass.ajaxUrl + `?action=futurewordpress/project/action/cancelsubscription&userid=` + node.dataset.userid )
								.then(response => {
									if (!response.ok) {
									throw new Error(response.statusText)
									}
									return response.json()
								})
								.catch(error => {
									Swal.showValidationMessage(
									thisClass.i18n.request_failed + `: ${error}`
									)
								})
							},
							allowOutsideClick: () => !Swal.isLoading()
							}).then((result) => {
							if (result.isConfirmed ) {
								Swal.fire({
								// title: `${result.value.login}'s avatar`,
								// imageUrl: result.value.avatar_url: 
								title: ( result.value.success ) ? 'Success' : 'Failed',icon: ( result.value.success ) ? 'success' : 'error',
								text: result.value?.data??'Request sent but doesn\'t update anything.',
								}).then( () => {
									location.reload();
								} )
							}
							})
					} );
				}
			}, 1000 );
				
		}
		changePassword() {
			const thisClass = this;var node;
			node = document.querySelector( '#change-password-field' );
			if( node ) {
				node.addEventListener( 'change', ( event ) => {
					Swal.fire({
					title: thisClass.i18n.confirming,
					text: thisClass.i18n.give_your_old_password,
					icon: 'warning',
					confirmButtonText: thisClass.i18n.submit,
					input: 'text',
					inputAttributes: {
						autocapitalize: 'off'
					},
					showCancelButton: true,
					showLoaderOnConfirm: true,
					preConfirm: (login) => {
						return fetch( thisClass.ajaxUrl + `?action=futurewordpress/project/action/changepassword` )
						.then(response => {
							if (!response.ok) {
							throw new Error(response.statusText)
							}
							return response.json()
						})
						.catch(error => {
							Swal.showValidationMessage(
							thisClass.i18n.request_failed + `: ${error}`
							)
						})
					},
					allowOutsideClick: () => !Swal.isLoading()
					}).then((result) => {
					if (result.isConfirmed ) {
						Swal.fire({
						title: ( result.value.success ) ? 'Success' : 'Failed',icon: ( result.value.success ) ? 'success' : 'error',
						text: result.value?.data??'Request sent but doesn\'t update anything.',
						})
					}
					})
				} );
			}
		}
		toOpenEdit() {
			const thisClass = this;var i, el, theInterval;
			theInterval = setInterval(() => {
				document.querySelectorAll( '[id^=basic-editopen-]:not([data-handled])' ).forEach( ( pen ) => {
					pen.dataset.handled = true;
					pen.addEventListener( 'click', ( e ) => {
						el = pen.previousElementSibling;
						if( el.hasAttribute( 'disabled' ) ) {
							el.removeAttribute( 'disabled' );el.classList.remove( 'form-control-solid' );
							i = pen.querySelector( 'i' );if( i ) {i.classList.remove( 'fa-pencil-alt' );i.classList.add( 'fa-circle-notch' );}
							if( el.parentElement ) {el.parentElement.classList.remove( 'input-group-solid' );}
							// fa-circle-notch | fa-times | afa-check | fa-spinner  | 'fa-spin'
						} else {}
					} );
				} );
			}, 1000 );
		}
		inputEventListner() {
			const thisClass = this;var i, el, userid, theInterval;
			theInterval = setInterval(() => {
				userid = document.querySelector( 'input[type="hidden"][name="userid"]' );
				if( userid ) {userid = userid.value;} else {userid = false;}
				document.querySelectorAll( 'input:not([data-handled])' ).forEach( ( input ) => {
					input.dataset.handled = true;
					input.addEventListener( 'change', ( e ) => {
						el = input.nextElementSibling;
						// console.log( [ el, input, this ] );
						if( el && el.classList.contains( 'input-group-text' ) ) {
							// input.setAttribute( 'disabled' );
							i = el.querySelector( 'i' );
							if( i ) {i.classList.remove( 'fa-circle-notch' );i.classList.add( 'fa-spinner', 'fa-spin' );}
							//  | fa-times | fa-check |   | 
							var formdata = new FormData();
							formdata.append( 'action', 'futurewordpress/project/action/singlefield' );
							formdata.append( 'field', input.name );
							formdata.append( 'value', input.value );
							formdata.append( 'userid', userid );
							formdata.append( '_nonce', thisClass.ajaxNonce );
							thisClass.sendToServer( formdata, (i)?i:false );
						}
					} );
				} );
			}, 1000 );
		}
		toggleStatus() {
			const thisClass = this;var userid, theInterval;
			// theInterval = setInterval(() => {
				userid = document.querySelector( 'input[type="hidden"][name="userid"]' );
				if( userid ) {userid = userid.value;} else {userid = false;}
				document.querySelectorAll( '.fwp-form-checkbox-pause-subscribe:not([data-handled])' ).forEach( ( el, ei ) => {
					el.dataset.handled = true;
					document.body.addEventListener( 'subscription-status-on subscription-status-off', () => {
						Swal.fire( { position: 'top-end', icon: 'success', title: ( el.checked ) ? thisClass.i18n.you_paused : thisClass.i18n.you_un_paused, showConfirmButton: false, timer: 3500 } );
					} );
					
					el.addEventListener( 'change', ( event ) => {
						var formdata = new FormData();
							formdata.append( 'action', 'futurewordpress/project/action/singlefield' );
							formdata.append( 'field', el.name );
							formdata.append( 'value', ( el.checked ) ? 'on' : 'off' );
							formdata.append( 'userid', userid );
							formdata.append( '_nonce', thisClass.ajaxNonce );
							thisClass.sendToServer( formdata );
					} );
				} );
			// }, 1000 );
		}
		accordion() {
			document.querySelectorAll( '.accordion' ).forEach( ( al, ai ) => {
				al.querySelectorAll( '.accordion-header' ).forEach( ( el, ei ) => {
						el.addEventListener( 'toggle', ( event ) => {
								let node = document.querySelector( el.dataset.bsTarget );
								if( node ) {
										node.classList.toggle( 'show' );
								}
						} );
				} );
			} );
		
		}
		datatable() {
			const thisClass = this;
			document.querySelectorAll( '#kt_datatable_content_library' ).forEach( ( el, ei ) => {
				$( el ).DataTable( {
					ajax: thisClass.ajaxUrl + '?action=futurewordpress/project/database/contents&_nonce=' + thisClass.ajaxNonce
				} );
			} );
		
		}
		passwordToggle() {
			var theInterval = setInterval(() => {
				document.querySelectorAll( '.input-group-text.password-toggle:not([data-handled])' ).forEach( ( el, ei ) => {
					el.dataset.handled = true;
					el.addEventListener( 'click', ( event ) => {
							el.classList.toggle( 'showing' );
							ei = el.nextElementSibling;
							if( ei ) {
									ei.type = ( ei.type == 'text' ) ? 'password' : 'text';
							}
					} );
				} );
			}, 1000 );
		}
		fetchDataWidthContract() {
			document.querySelectorAll( '.document-sign-page *' ).forEach( ( e ) => {
				var replacer = {};
				Object.keys( replacer ).forEach(function( lsi ) {
						e.textContent = e.textContent.replace( '{{' + lsi + '}}', replacer[ lsi ] );
				} );
			} );
		}
		sendToServer( data, i = false ) {
			const thisClass = this;var message;
			$.ajax({
				url: thisClass.ajaxUrl,
				type: "POST",
				data: data,    
				cache: false,
				contentType: false,
				processData: false,
				success: function( json ) {
					thisClass.lastAjax = json;
					message = ( json.data.message ) ? json.data.message : json.data;
					if( json.success ) {
						if( typeof message === 'string' ) {
							if( thisClass.noToast ) {Swal.fire( { position: 'center', icon: 'success', text: message, showConfirmButton: false, timer: 3000 } );} else {toast.show({title: message, position: 'bottomright', type: 'info'});}
						}
						if( i ) {i.classList.remove( 'fa-spinner', 'fa-spin' );i.classList.add( 'fa-check' );}
					} else {
						if( typeof message === 'string' ) {
							if( thisClass.noToast ) {Swal.fire( { position: 'center', icon: 'error', text: message, showConfirmButton: false, timer: 3000 } );} else {toast.show({title: message, position: 'bottomright', type: 'warn'});}
						}
						if( i ) {i.classList.remove( 'fa-spinner', 'fa-spin' );i.classList.add( 'fa-times' );}
					}
					if( json.data.hooks ) {
						json.data.hooks.forEach( ( hook ) => {
							document.body.dispatchEvent( new Event( hook ) );
						} );
					}
				},
				error: function( err ) {
					console.log( err.responseText );toast.show({title: err.responseText, position: 'bottomright', type: 'alert'});
					if( i ) {i.classList.remove( 'fa-spinner', 'fa-spin' );i.classList.add( 'fa-times' );}
				}
			});
		}
		addEventListener( elem, event ) {
			
			elem.dispatchEvent( new Event( 'fuck' ) )
		}
		setup_hooks() {
			document.body.addEventListener( 'reload-page', () => {location.reload();} );
		}
		regWidget() {
			const thisClass = this;var currentTab = 0, isNotOkay, tab, wrap, widzard = document.querySelector( '#register-existing-account-wizard' );
			if( ! widzard ) {return;}this.prevImage();this.submitWidget( widzard );
			var rendWidget = function( done ) {
				tab = widzard.querySelector( 'fieldset.active' );tab.classList.remove( 'active' );
				if( done && currentTab >= 1 ) {widzard.querySelectorAll( '#top-tab-list li' )[(currentTab-1)].classList.add( 'done' );}
				widzard.querySelectorAll( 'fieldset' )[currentTab].classList.add( 'active' );
				tab = widzard.querySelectorAll( '#top-tab-list li' )[currentTab];tab.classList.add( 'active' );
			}
			document.querySelectorAll( 'button[name="next"]' ).forEach( ( al, ai ) => {
				al.addEventListener( 'click', ( e ) => {isNotOkay = false;
					if( e.target.previousElementSibling ) {
						wrap = e.target.previousElementSibling;
						wrap.querySelectorAll( 'input[required]' ).forEach( ( input ) => {
							switch( input.type ) {
								case 'email' :
									if( ! validator.isEmail( input.value ) ) {isNotOkay = thisClass.i18n.rqemail;thisClass.handleError( input );}
									break;
								case 'text' :
									if( validator.isEmpty( input.value ) ) {isNotOkay = thisClass.i18n.sometextfieldmissing;thisClass.handleError( input );}
									break;
								case 'number' :
									if( ! validator.isNumeric( input.value ) ) {isNotOkay = thisClass.i18n.renumber;thisClass.handleError( input );}
									break;
								case 'password' :
									if( ! validator.isStrongPassword( input.value ) ) {isNotOkay = thisClass.i18n.rqstrongpass;thisClass.handleError( input );}
									if( wrap.querySelector( '#password-field-1' ) && wrap.querySelector( '#password-field-2' ) && wrap.querySelector( '#password-field-1' ).value != wrap.querySelector( '#password-field-2' ).value ) {isNotOkay = thisClass.i18n.passnotmatched;}
									break;
								default:
									break;
							}
						} );
					}
					if( isNotOkay === false ) {
						currentTab = ( currentTab + 1 );rendWidget( true );
					} else {
						toast.show({title: isNotOkay, position: 'bottomright', type: 'warn'});
					}
				} );
			} );
			document.querySelectorAll( 'button[name="previous"]' ).forEach( ( al, ai ) => {
				al.addEventListener( 'click', ( e ) => {
					currentTab = ( currentTab - 1 );rendWidget( false );
				} );
			} );
		}
		handleError( e ) {
			e.classList.add( 'border-danger' );
		}
		prevImage() {
			var preview, image, reader, file, input;
			image = document.querySelector( '.profile-image-preview' );
			input = document.querySelector( '[name="profile-image"]' );
			if( input && image ) {
				input.addEventListener( 'change', ( e ) => {
					preview = document.getElementById("preview");
					file = input.files[0];
					reader = new FileReader();
					reader.onloadend = function () {
						preview.src = reader.result;
						image.classList.add( 'active' );
					}
					if (file) {
						reader.readAsDataURL(file);
					} else {
						preview.src = "";
						image.classList.remove( 'remove' );
					}
				} );
			}
		}
		submitWidget( widzard ) {
			const thisClass = this;var el, message;
			document.body.addEventListener( 'register-existing-account-wizard-success', ( event ) => {
				el = document.querySelector( '#avaters' );if( el ) {el.classList.toggle( 'active', 'done' );}
				el = document.querySelector( '#confirm' );if( el ) {el.classList.add( 'active', 'done' );}
				el = document.querySelectorAll( '#register-existing-account-wizard fieldset' );if( el[3] ) {el[2].classList.remove( 'active' );el[3].classList.add( 'active' );}
				if( thisClass.lastAjax && thisClass.lastAjax.data.redirect ) {
					location.href = thisClass.lastAjax.data.redirect;
				} else {
					Swal.fire({
						title: thisClass.i18n.successful,
						text: ( thisClass.lastAjax.data.message ) ? thisClass.lastAjax.data.message : thisClass.lastAjax.data,
					})
				}
			} );
			widzard.addEventListener( 'submit', ( event ) => {
				event.preventDefault();
				var formdata = new FormData( event.target );
				thisClass.sendToServer( formdata );
			} );
		}
		tryHttpXhr() {
			var params = '', form = event.target, data = new FormData( form );
			for (var [key, value] of data.entries()) {params += encodeURIComponent(key) + "=" + encodeURIComponent(value) + "&";}
			params = params.slice(0, -1);

			var xhr = new XMLHttpRequest();
			xhr.open( "POST", thisClass.ajaxUrl + "?" + params, true );
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.upload.addEventListener("progress", function(e) {
				if (e.lengthComputable) {
					var percentComplete = (e.loaded / e.total) * 100;
					// document.getElementById("progressBar").value = percentComplete;
				}
			});
			xhr.onreadystatechange = function() {
				if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
					console.log(this.responseText);
				}
			};
			xhr.send(  ); // new FormData( event.target )
		}
		handleStripeBtn() {
			this.stripePublishaleKey = 'sk_test_51MYvdBI8VOGXMyoFiYpojuTUhvmS1Cxwhke4QK6jfJopnRN4fT8Qq6sy2Rmf2uvyHBtbafFpWVqIHBFoZcHp0vqq00HaOBUh1P';
			const thisClass = this;var el, widget;
			if( typeof Stripe !=='undefined' ) {return;}
			widget = document.querySelector( '.pay_retainer-amount' );
			if( ! widget ) {return;}
			widget.addEventListener( 'click', async ( event ) => {
				event.preventDefault();
				const stripe = new Stripe( thisClass.stripePublishaleKey );

				thisClass.stripe = await loadStripe(  thisClass.stripePublishaleKey );
				window.stripe = thisClass.stripe;
				var customer = await thisClass.stripe.customers.create({
					email: 'customer@example.com',
				});
				
				console.log(customer.id);
			} );
		}
		handleTabHref() {
			const thisClass = this;var selector, href, split, tab;selector = '.nav-pills .nav-link[role="tab"]';
			if( thisClass.profile && thisClass.profile.profilePath ) {
				split = thisClass.profile.profilePath.split( '/' );
				thisClass.profile.profilePath =  thisClass.profile.profilePath.substring( 0, ( thisClass.profile.profilePath.length - split[ ( split.length - 1 ) ].length - 1 ) );
			}
			document.querySelectorAll( selector ).forEach( link => {
				link.addEventListener( 'click', e => {
					e.preventDefault();
					href = link.href.split('#');href = ( href[1] ) ? href[1] : href[0];
					href = href.split('-');href = ( href[1] ) ? href[1] : href[0];
					thisClass.navigateTo( thisClass.profile.profilePath + '/' + href );
				} );
			} );
			setTimeout(() => {
				if( thisClass.profile && thisClass.profile.currentTab ) {
					tab = document.querySelector( selector + '[href="#profile-' + thisClass.profile.currentTab + '"]' );
					if( tab ) {tab.click();}
				}
			}, 500);
		}
		navigateTo( url ) {
			// if( this.profile ) {}
			// if( history.pushState && performance && performance.navigation.type == 0  ) {
			// 	history.pushState({}, '', url);
			// } else {
			if( url ) {
				history.replaceState({}, '', url);
			}
			// fetch( url ).then( response => response.text() ).then( html => {
			// 	document.querySelector('#content').innerHTML = html;
			// } );
		}
		deleteArchive() {
			const thisClass = this;var el, message;
			document.querySelectorAll( '.archive-delete-btn' ).forEach( ( archive ) => {
				archive.addEventListener( 'click', ( event ) => {
					event.preventDefault();
					Swal.fire({
						icon: 'info',
						title: thisClass.i18n.are_u_sure,
						showCancelButton: true,
						showLoaderOnConfirm: true
					} ).then((result) => {
						if (result.isConfirmed ) {
							var formdata = new FormData();
								formdata.append( 'action', 'futurewordpress/project/action/deletearchives' );
								formdata.append( 'archive', archive.dataset.archive );
								formdata.append( 'userid', archive.dataset.userid );
								formdata.append( '_nonce', thisClass.ajaxNonce );
								thisClass.sendToServer( formdata );
						}
					} );
				} );
			} );
		}
		submitArchiveFiles() {
			const thisClass = this;
			var node = document.querySelector( '.submit-archive-files' ), json;
			if( node ) {
				node.addEventListener( 'click', function( e ) {
					json = JSON.parse( node.dataset.config );
					json.preConfirm = (login) => {
						var form = document.querySelector( '#the-raw-video-archive-upload' ), formData, data = {};
						formData = new FormData(form);
						// formData.forEach(function(value, key) {
						// 	if( key == '_wp_http_referer' ) {} else {
						// 		data[key] = value;
						// 	}
						// });
						formData.append( 'title', login );

						// + '?action=futurewordpress/project/action/submitarchives&title=' + data.title + '&month=' + data.month + '&year=' + data.year
						return fetch( thisClass.ajaxUrl, {
							method: "POST",
							// headers: {"Content-Type": "application/json; charset=utf-8"},
							body: formData // JSON.stringify( data )
						} ).then(response => {
							if (!response.ok) {
								throw new Error(response.statusText)
							}
							return response.json()
						}).catch(error => {
							Swal.showValidationMessage(
								thisClass.i18n.request_failed + `: ${error}`
							)
						})
					};
					json.allowOutsideClick = () => !Swal.isLoading();
					Swal.fire( json ).then((result) => {
						if (result.isConfirmed ) {
							var data = result.value?.data??'Request sent but doesn\'t update anything.';
							data = data?.message??data;
							Swal.fire({
								title: ( result.value.success ) ? 'Success' : 'Failed', icon: ( result.value.success ) ? 'success' : 'error',
								text: data,
							}).then((result) => {
								location.reload();
							});
						}
					})
				} );
			}
		}
		trackWpformAjax() {
			const thisClass = this;var form;
			// wpformsAjaxSubmitSuccess | wpformsAjaxSubmitActionRequired | wpformsAjaxSubmitFailed | wpformsAjaxSubmitError | wpformsAjaxSubmitCompleted
			form  = document.querySelector( 'form.wpforms-form' );
			if( form ) {
				form.addEventListener( 'wpformsAjaxSubmitSuccess', ( event ) => {
					// Show success Message
					Swal.fire( {
						title: thisClass.i18n.confirming,
						text: thisClass.i18n.give_your_old_password,
						icon: 'info',
					} ).then( (result) => {
						// if (result.isConfirmed ) {}
					} )
				} )
			}
		}
		handlePayCardError() {
			var interval;
			interval = setInterval(() => {
				document.querySelectorAll( '.wpforms-error-container:not([data-handled])' ).forEach( ( e ) => {
					e.dataset.handled = true;
					if( e.innerText.includes( 'Credit Card Payment Error' ) ) {
						var selector = '.wpforms-container-full .wpforms-form .wpforms-page-indicator.connector .wpforms-page-indicator-page', tabs = document.querySelectorAll( selector ), active = document.querySelector( selector + '.active' ),
						container = document.querySelectorAll( 'div.wpforms-container-full .wpforms-form .wpforms-field-container .wpforms-page' ), submit;
						if( active ) {
							active.classList.remove( 'active' );
							active.querySelector( '.wpforms-page-indicator-page-number' ).setAttribute( 'style', '' );
							active.querySelector( '.wpforms-page-indicator-page-triangle' ).setAttribute( 'style', '' );
							tabs[ ( tabs.length - 1 ) ].classList.add( 'active' );
							tabs[ ( tabs.length - 1 ) ].querySelector( '.wpforms-page-indicator-page-number' ).style.backgroundColor = '#72b239';
							tabs[ ( tabs.length - 1 ) ].querySelector( '.wpforms-page-indicator-page-triangle' ).style.borderTopColor = '#72b239';
						}
						else {console.log( 'error 2' );}
						if( container.length >= 1 ) {
							container.forEach( ( c ) => {
								c.style.display = 'none';
							} );
							container[ ( container.length - 1 ) ].style.display = 'block';
						}
						else {console.log( 'error 3' );}
						submit = document.querySelector( '.wpforms-submit-container' );
						if( submit ) {submit.style.display = 'block';}
					}
					else {console.log( 'This error is not payment issue detected.' );}
				} );
			}, 100 );
		}
		selectRegistration() {
			const thisClass = this;var theInterval, select;
			select = document.querySelector( '.fwp-select-registration-type-to-proceed' );
			if( select ) {
				select.addEventListener( 'change', ( event ) => {
					var formdata = new FormData();
						formdata.append( 'action', 'futurewordpress/project/action/selecttoregister' );
						formdata.append( 'field', select.name );
						formdata.append( 'value', select.value );
						formdata.append( 'userid', select.dataset.userid );
						formdata.append( '_nonce', thisClass.ajaxNonce );
						thisClass.sendToServer( formdata );
				} );
			}
		}
		toggleSubscriptions() {
			const thisClass = this;var userid, theInterval;
			// theInterval = setInterval(() => {
				userid = document.querySelector( 'input[type="hidden"][name="userid"]' );
				if( userid ) {userid = userid.value;} else {userid = false;}
				document.querySelectorAll( '.pause-unpause-subscription:not([data-handled])' ).forEach( ( el, ei ) => {
					el.dataset.handled = true;
					document.body.addEventListener( 'subscription-status-success', () => {
						Swal.fire( { position: 'top-end', icon: 'success', title: thisClass.i18n.subscription_toggled, showConfirmButton: false, timer: 6500 } );
						// console.log( thisClass.lastAjax );
						el.innerText = el.dataset?.pendingTitle??'Pending';
						el.disabled = true;
						el.classList.remove( 'btn-outline-warning', 'border-active', 'btn-primary' );
						el.classList.add( 'btn-light' );
					} );
					document.body.addEventListener( 'subscription-status-pause', () => {
						Swal.fire( { position: 'center', icon: 'success', title: thisClass.i18n.you_paused, text: thisClass.i18n.you_paused_msg, showConfirmButton: false, timer: 6500 } );
						el.innerText = el.dataset?.unpauseTitle??'Resume My Retainer';
						el.dataset.current = 'unpause';// el.disabled = true;
						el.classList.remove( 'btn-outline-warning', 'border-active', 'btn-light' );
						el.classList.add( 'btn-primary' );
					} );
					document.body.addEventListener( 'subscription-status-unpause', () => {
						Swal.fire( { position: 'center', icon: 'success', title: thisClass.i18n.you_un_paused, text: thisClass.i18n.you_un_paused_msg, showConfirmButton: false, timer: 6500 } );
						el.innerText = el.dataset?.pauseTitle??'Pause My Retainer';
						el.disabled = true;el.dataset.current = 'pending';
						el.classList.remove( 'btn-primary', 'btn-light' );
						el.classList.add( 'btn-outline-warning', 'border-active' );
					} );
					el.addEventListener( 'click', ( event ) => {
						event.preventDefault();
						if( event.target.dataset.current == 'pending' ) {
							Swal.fire( {
								icon: 'info',
								text: thisClass.i18n.say2wait2pause
							} )
						} else {
							Swal.fire( {
								icon: 'warning',
								title: thisClass.i18n.are_u_sure,
								text: ( event.target.dataset.current == 'pause' ) ? thisClass.i18n.rusure2unsubscribe : thisClass.i18n.rusure2subscribe,
								showCancelButton: true
							} ).then( (result) => {
								if( result.isConfirmed ) {
									var formdata = new FormData();
									formdata.append( 'action', 'futurewordpress/project/action/singlefield' );
									formdata.append( 'field', el.name );
									formdata.append( 'value', ( event.target.dataset.current == 'pause' ) ? 'off' : 'on' );
									formdata.append( 'userid', userid );
									formdata.append( '_nonce', thisClass.ajaxNonce );
									thisClass.sendToServer( formdata );
								}
							} );
						}
					} );
				} );
			// }, 1000 );
		}
		changePaymentCard() {
			const thisClass = this;var theInterval, userid, config, html, error, ajaxNonce, message;
			// theInterval = setInterval(() => {
				document.querySelectorAll( '.change-payment-card:not([data-handled])' ).forEach( ( el, ei ) => {
					el.dataset.handled = true;
					document.body.addEventListener( 'change-payment-card-success', () => {
						Swal.fire( { position: 'top-end', icon: 'success', title: thisClass.i18n.subscription_toggled, showConfirmButton: false, timer: 3500 } );
						el.innerText = el.dataset?.pendingTitle??'Pending';
						el.classList.remove( 'btn-outline-warning', 'border-active', 'btn-primary' );
						el.classList.add( 'btn-light' );el.disabled = true;
					} );
					el.addEventListener( 'click', ( event ) => {
						// event.preventDefault();
						config = JSON.parse( event.target.dataset.config );
						userid = event.target.dataset.userid;
						// console.log( config );
						html = '<div class="form-group"><label for="cardNumber">' + ( config?.card_number??'Card number:' ) + '</label><input type="number" id="cardNumber" class="form-control" /></div><div class="form- row"><div class="col-6"><label for="expMonth">' + ( config?.expire_month??'Expiration month:' ) + '</label><input type="number" id="expMonth" class="form-control" /></div><div class="col-6"><label for="expYear">' + ( config?.expire_year??'Expiration year:' ) + '</label><input type="number" id="expYear" class="form-control" /></div></div><div class="form-group"><label for="cvc">' + ( config?.card_ccv??'CVC:' ) + '</label><input type="number" id="cvc" class="form-control" /></div>';
						// console.log( html );
						Swal.fire({
							title: config?.popup_title??'Enter payment card details',
							html: html,
							focusConfirm: false,
							showCancelButton: true,
							showLoaderOnConfirm: true,
							preConfirm: () => {
								// Get the payment card details entered by the user
								const cardNumber = Swal.getPopup().querySelector('#cardNumber').value;
								const expMonth = Swal.getPopup().querySelector('#expMonth').value;
								const expYear = Swal.getPopup().querySelector('#expYear').value;
								const cvc = Swal.getPopup().querySelector('#cvc').value;

								error = false;// Validate if inputs are correct.
								if( cardNumber == '' ) {error = config?.pls_fillall??'Please fillup all fields first.';}
								else if( cardNumber.length < 10 ) {error = config?.pls_fixwrngcdnm??'Seems you\'ve inputed a wrong card number';}
								else if( expMonth < 1 || expMonth > 12 ) {error = config?.pls_fillmonth??'Please input Month in numeric format';}
								else if( expYear < new Date().getFullYear() ) {error = config?.pls_fixyear??'Card expiration year should be future date.';}
								else if( cvc.length < 3 ) {error = config?.pls_fixccv??'Please provide valid CVC number.';} else {}
								if( error ) {
									Swal.showValidationMessage( error );
								} else {
									// Create a Stripe token using the card details
									ajaxNonce = thisClass.ajaxNonce;
									return fetch( thisClass.ajaxUrl, {
										method: 'POST',
										headers: {
											// 'Authorization': 'Bearer YOUR_STRIPE_SECRET_KEY',
											'Content-Type': 'application/x-www-form-urlencoded'
										},
										body: `action=futurewordpress/project/action/switchpayementcard&_nonce=${ajaxNonce}&userid=${userid}&card[number]=${cardNumber}&card[exp_month]=${expMonth}&card[exp_year]=${expYear}&card[cvc]=${cvc}`
									})
									.then(response => {
										if (!response.ok) {
											throw new Error(response.statusText);
										}
										return response.json();
									})
									.then( json => {
										return json;
									})
									.catch(error => {
										Swal.showValidationMessage(`Request failed: ${error}`);
									});
								}
							}
						}).then(result => {
							if (result.isConfirmed) {
								// The user entered valid payment card details and a token was created
								// const cardToken = result.value;
								message = ( typeof result.value.data.message == 'string' ) ? result.value.data.message : (
									( typeof result.value.data == 'string' ) ? result.value.data : false
								);
								if( message !== false ) {
									if( thisClass.noToast ) {Swal.fire( { position: 'center', icon: ( result.value.success ) ? 'success' : 'error', text: message, showConfirmButton: false, timer: 3000 } );} else {toast.show({title: message, position: 'bottomright', type: 'info'});}
								}
								// Use the token to process the payment or save it for later
								// console.log('Card token:', cardToken);
							}
						});
					} );
				} );
			// }, 1000 );
		}
		profileImgUpload() {
			const thisClass = this;var theInterval, reader, file, preview;
			// theInterval = setInterval( () => {
				document.querySelectorAll( '.profile-image-upload:not([data-handled])' ).forEach( ( el, ei ) => {
					el.dataset.handled = true;
					el.addEventListener( 'change', ( event ) => {
						if( el.dataset.preview ) {
							preview = document.querySelector( el.dataset.preview );
							file = el.files[0];
							reader = new FileReader();
							reader.onloadend = function () {
								preview.src = reader.result;
								var formdata = new FormData();
								formdata.append( 'action', 'futurewordpress/project/filesystem/uploadavater' );
								formdata.append( 'lead', el.dataset.lead );
								formdata.append( 'avater', el.files[0] );
								formdata.append( '_nonce', thisClass.ajaxNonce );
								thisClass.sendToServer( formdata );
							}
							if (file) {
								reader.readAsDataURL(file);
							} else {
								if( preview.dataset.default ) {
									preview.src = preview.dataset.default;
								} else {
									preview.src = "";
								}
							}
						}
					} );
				} );
			// }, 3000 );
		}
		btnLogOutConfirm() {
			const thisClass = this;var interval;
			interval = setInterval(() => {
				document.querySelectorAll( '.btn-logout-confirm:not([data-handled])' ).forEach( ( e ) => {
					e.dataset.handled = true;
					e.addEventListener( 'click', ( event ) => {
						Swal.fire({
							icon: 'info',
							title: thisClass.i18n.sure2logout,
							showCancelButton: true
						} ).then((result) => {
							if (result.isConfirmed ) {
								return true;
							} else {
								return false;
							}
						} );
					} );
				} );
			}, 3000 );
		}
	}
	new FutureWordPress_Frontend();
} )( jQuery );
