/**
 * Service Quote Builder - Frontend JavaScript
 */

(function($) {
    'use strict';

    const SQB = {
        currentStep: 1,
        totalSteps: 6,
        quoteData: {
            items: [],
            vehicle: null,
            condition: null,
            polishing: null,
            protection: null,
            extras: [],
            kits: [],
            hours: 0
        },

        init: function() {
            this.bindEvents();
            this.updateProgressBar();
            this.loadFromStorage();
        },

        bindEvents: function() {
            // Step navigation
            $('#sqb-next').on('click', $.proxy(this.nextStep, this));
            $('#sqb-prev').on('click', $.proxy(this.prevStep, this));
            $('#sqb-submit').on('click', $.proxy(this.submitQuote, this));

            // Card selection (radio-style for single selection steps)
            $('.sqb-card[data-value]').on('click', $.proxy(this.selectCard, this));

            // Checkbox selection (multi-select)
            $('.sqb-checkbox-card input[type="checkbox"]').on('change', $.proxy(this.toggleCheckbox, this));

            // Vehicle cards
            $('.sqb-vehicle-grid .sqb-card').on('click', function() {
                const value = $(this).data('value');
                const price = parseInt($(this).data('price')) || 0;
                SQB.selectVehicle(value, price);
            });

            // Condition cards
            $('.sqb-condition-grid .sqb-card-cond').on('click', function() {
                const value = $(this).data('value');
                const hours = $(this).data('hours') || 0;
                SQB.selectCondition(value, hours);
            });

            // Polishing cards
            $('.sqb-polishing-grid .sqb-card-poly').on('click', function() {
                const value = $(this).data('value');
                const price = parseInt($(this).data('price')) || 0;
                const hours = $(this).data('hours') || '0';
                SQB.selectPolishing(value, price, hours);
            });

            // Protection cards
            $('.sqb-protection-grid .sqb-card-pro').on('click', function() {
                const value = $(this).data('value');
                const price = parseInt($(this).data('price')) || 0;
                const duration = $(this).data('duration') || '';
                SQB.selectProtection(value, price, duration);
            });

            // Save cart button
            $('#sqb-save-cart').on('click', $.proxy(this.showSaveModal, this));
            $('#sqb-confirm-save').on('click', $.proxy(this.saveQuote, this));

            // Modal close buttons
            $('.sqb-modal-close').on('click', function() {
                $(this).closest('.sqb-modal').hide();
            });

            // Start new quote button
            $('.sqb-modal-btn').on('click', $.proxy(this.resetBuilder, this));

            // Close modal when clicking outside
            $('.sqb-modal').on('click', function(e) {
                if ($(e.target).hasClass('sqb-modal')) {
                    $(this).hide();
                }
            });
        },

        selectCard: function(e) {
            const $card = $(e.currentTarget);
            const $parent = $card.parent();

            // Remove selected from siblings
            $parent.find('.sqb-card').removeClass('selected');

            // Add selected to clicked card
            $card.addClass('selected');

            // Get data
            const value = $card.data('value');
            const price = parseInt($card.data('price')) || 0;

            // Store selection based on parent grid
            if ($parent.hasClass('sqb-vehicle-grid')) {
                this.selectVehicle(value, price);
            } else if ($parent.hasClass('sqb-condition-grid')) {
                const hours = $card.data('hours') || 0;
                this.selectCondition(value, hours);
            } else if ($parent.hasClass('sqb-polishing-grid')) {
                const hours = $card.data('hours') || '0';
                this.selectPolishing(value, price, hours);
            } else if ($parent.hasClass('sqb-protection-grid')) {
                const duration = $card.data('duration') || '';
                this.selectProtection(value, price, duration);
            }
        },

        selectVehicle: function(value, price) {
            // Remove existing vehicle item
            this.quoteData.items = this.quoteData.items.filter(item => item.type !== 'vehicle');

            // Add vehicle
            this.quoteData.vehicle = value;
            this.quoteData.items.push({
                type: 'vehicle',
                name: this.getVehicleLabel(value),
                price: price,
                hours: 0
            });

            this.updateTotals();
            this.enableNextButton();
        },

        selectCondition: function(value, hours) {
            // Remove existing condition item
            this.quoteData.items = this.quoteData.items.filter(item => item.type !== 'condition');

            // Add condition
            this.quoteData.condition = value;
            this.quoteData.items.push({
                type: 'condition',
                name: this.getConditionLabel(value),
                price: 0,
                hours: hours
            });

            this.updateTotals();
            this.enableNextButton();
        },

        selectPolishing: function(value, price, hours) {
            // Remove existing polishing item
            this.quoteData.items = this.quoteData.items.filter(item => item.type !== 'polishing');

            // Add polishing
            this.quoteData.polishing = value;
            this.quoteData.items.push({
                type: 'polishing',
                name: this.getPolishingLabel(value),
                price: price,
                hours: hours
            });

            this.updateTotals();
            this.enableNextButton();
        },

        selectProtection: function(value, price, duration) {
            // Remove existing protection item
            this.quoteData.items = this.quoteData.items.filter(item => item.type !== 'protection');

            // Add protection
            this.quoteData.protection = value;
            this.quoteData.items.push({
                type: 'protection',
                name: this.getProtectionLabel(value, duration),
                price: price,
                hours: 0
            });

            this.updateTotals();
            this.enableNextButton();
        },

        toggleCheckbox: function(e) {
            const $checkbox = $(e.currentTarget);
            const $card = $checkbox.closest('.sqb-checkbox-card');
            const name = $checkbox.data('name');
            const price = parseInt($checkbox.data('price')) || 0;
            const hours = parseInt($checkbox.data('hours')) || 0;
            const type = $checkbox.attr('name');

            if ($checkbox.is(':checked')) {
                $card.addClass('checked');

                // Determine if it's a kit or extra
                const isKit = type.startsWith('kit_');

                if (isKit) {
                    this.quoteData.items = this.quoteData.items.filter(item => item.name !== name);
                    this.quoteData.items.push({
                        type: 'kit',
                        name: name,
                        price: price,
                        hours: 0
                    });
                    this.quoteData.kits.push(name);
                } else {
                    this.quoteData.items = this.quoteData.items.filter(item => item.name !== name);
                    this.quoteData.items.push({
                        type: 'extra',
                        name: name,
                        price: price,
                        hours: hours
                    });
                    this.quoteData.extras.push(name);
                }
            } else {
                $card.removeClass('checked');
                this.quoteData.items = this.quoteData.items.filter(item => item.name !== name);
                this.quoteData.extras = this.quoteData.extras.filter(e => e !== name);
                this.quoteData.kits = this.quoteData.kits.filter(k => k !== name);
            }

            this.updateTotals();
        },

        getVehicleLabel: function(value) {
            const labels = {
                'small': 'Small/Compact Vehicle',
                'mid': 'Mid Size Vehicle',
                'full': 'Full Size Vehicle',
                'large': 'Large/X-Large Vehicle'
            };
            return labels[value] || value;
        },

        getConditionLabel: function(value) {
            const labels = {
                'new': 'New Car / Near Perfect',
                'light': 'Light Swirls',
                'medium': 'Large Swirls & Some Deep Scratches',
                'deep': 'Deep Scratches On All Panels'
            };
            return labels[value] || value;
        },

        getPolishingLabel: function(value) {
            const labels = {
                'single': 'Single Stage Polish',
                'enhancement': 'Enhancement',
                'correction': 'Full Correction',
                'wetsand': 'Wet Sanding'
            };
            return labels[value] || value;
        },

        getProtectionLabel: function(value, duration) {
            const labels = {
                'c2': 'C2 Liquid Crystal (' + duration + ')',
                'exo': 'EXO (' + duration + ')',
                'csl': 'Crystal Serum Light (' + duration + ')',
                'csl-exo': 'CSL + EXO (' + duration + ')',
                'csu': 'Crystal Serum Ultra (' + duration + ')',
                'csu-black': 'CSU Black (' + duration + ')'
            };
            return labels[value] || value;
        },

        updateTotals: function() {
            const $itemsContainer = $('#sqb-total-items');
            const currencySymbol = sqb_ajax.currency_symbol || '$';
            const currencyPos = sqb_ajax.currency_position || 'before';

            // Clear and rebuild items list
            $itemsContainer.empty();

            let subtotal = 0;

            this.quoteData.items.forEach(function(item) {
                subtotal += item.price;

                const priceDisplay = currencyPos === 'before'
                    ? currencySymbol + item.price.toFixed(2)
                    : item.price.toFixed(2) + currencySymbol;

                $itemsContainer.append(
                    '<div class="sqb-total-item">' +
                    '<span class="sqb-total-item-name">' + item.name + '</span>' +
                    '<span class="sqb-total-item-price">' + priceDisplay + '</span>' +
                    '</div>'
                );
            });

            // Calculate VAT if applicable
            const vatRate = parseFloat($('#sqb-vat').closest('.sqb-vat').length ?
                $('#sqb-vat').closest('.sqb-vat').data('rate') : 0);

            const vatAmount = subtotal * (vatRate / 100);
            const grandTotal = subtotal + vatAmount;

            // Update totals display
            const subtotalDisplay = currencyPos === 'before'
                ? currencySymbol + subtotal.toFixed(2)
                : subtotal.toFixed(2) + currencySymbol;

            const vatDisplay = currencyPos === 'before'
                ? currencySymbol + vatAmount.toFixed(2)
                : vatAmount.toFixed(2) + currencySymbol;

            const totalDisplay = currencyPos === 'before'
                ? currencySymbol + grandTotal.toFixed(2)
                : grandTotal.toFixed(2) + currencySymbol;

            $('#sqb-subtotal').text(subtotalDisplay);
            $('#sqb-vat').text(vatDisplay);
            $('#sqb-grand-total').text(totalDisplay);

            // Save to storage
            this.saveToStorage();
        },

        updateProgressBar: function() {
            $('.sqb-progress-step').each(function() {
                const step = parseInt($(this).data('step'));
                $(this).removeClass('active completed');

                if (step === SQB.currentStep) {
                    $(this).addClass('active');
                } else if (step < SQB.currentStep) {
                    $(this).addClass('completed');
                }
            });
        },

        nextStep: function() {
            if (!this.validateCurrentStep()) {
                this.showToast('Please complete the current step before proceeding.', 'error');
                return;
            }

            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.showStep(this.currentStep);
                this.updateProgressBar();
                this.updateNavigationButtons();
                this.scrollToTop();
            }
        },

        prevStep: function() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.showStep(this.currentStep);
                this.updateProgressBar();
                this.updateNavigationButtons();
                this.scrollToTop();
            }
        },

        showStep: function(step) {
            $('.sqb-step').removeClass('active');
            $('.sqb-step[data-step="' + step + '"]').addClass('active');
        },

        validateCurrentStep: function() {
            switch (this.currentStep) {
                case 1:
                    return this.quoteData.vehicle !== null;
                case 2:
                    return this.quoteData.condition !== null;
                case 3:
                    return this.quoteData.polishing !== null;
                case 4:
                    return this.quoteData.protection !== null;
                case 5:
                    return true; // Extras are optional
                case 6:
                    return this.validateForm();
                default:
                    return true;
            }
        },

        validateForm: function() {
            const required = ['customer_name', 'customer_email', 'customer_phone', 'vehicle_make', 'vehicle_model'];
            let valid = true;

            required.forEach(function(field) {
                const $field = $('#' + field);
                if (!$field.val().trim()) {
                    $field.addClass('sqb-error');
                    valid = false;
                } else {
                    $field.removeClass('sqb-error');
                }
            });

            // Validate email format
            const email = $('#customer_email').val();
            if (email && !this.isValidEmail(email)) {
                $('#customer_email').addClass('sqb-error');
                valid = false;
            }

            return valid;
        },

        isValidEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        enableNextButton: function() {
            // Enable next button when current step has selection
            const stepValid = this.validateStep(this.currentStep);
            if (stepValid) {
                $('#sqb-next').prop('disabled', false);
            }
        },

        validateStep: function(step) {
            switch (step) {
                case 1:
                    return this.quoteData.vehicle !== null;
                case 2:
                    return this.quoteData.condition !== null;
                case 3:
                    return this.quoteData.polishing !== null;
                case 4:
                    return this.quoteData.protection !== null;
                default:
                    return true;
            }
        },

        updateNavigationButtons: function() {
            // Show/hide prev button
            if (this.currentStep > 1) {
                $('#sqb-prev').show();
            } else {
                $('#sqb-prev').hide();
            }

            // Show/hide next/submit buttons
            if (this.currentStep < this.totalSteps) {
                $('#sqb-next').show();
                $('#sqb-submit').hide();

                // Disable next if current step not complete
                if (!this.validateStep(this.currentStep)) {
                    $('#sqb-next').prop('disabled', true);
                } else {
                    $('#sqb-next').prop('disabled', false);
                }
            } else {
                $('#sqb-next').hide();
                $('#sqb-submit').show();
            }
        },

        submitQuote: function(e) {
            e.preventDefault();

            if (!this.validateForm()) {
                this.showToast('Please fill in all required fields.', 'error');
                return;
            }

            const $form = $('#sqb-form');
            const customerInfo = {
                name: $('#customer_name').val(),
                email: $('#customer_email').val(),
                phone: $('#customer_phone').val(),
                make: $('#vehicle_make').val(),
                model: $('#vehicle_model').val(),
                preferred_date: $('#preferred_date').val(),
                address: $('#address').val(),
                country: $('#country').val()
            };

            // Show loading state
            $('#sqb-submit').addClass('sqb-loading').prop('disabled', true);

            // Send AJAX request
            $.ajax({
                url: sqb_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'submit_quote_request',
                    nonce: sqb_ajax.nonce,
                    quote_data: this.quoteData,
                    customer_info: customerInfo
                },
                success: $.proxy(function(response) {
                    if (response.success) {
                        this.showSuccessModal(response.data.message);
                        this.clearStorage();
                    } else {
                        this.showToast(response.data.message || 'An error occurred.', 'error');
                    }
                }, this),
                error: $.proxy(function() {
                    this.showToast('An error occurred. Please try again.', 'error');
                }, this),
                complete: $.proxy(function() {
                    $('#sqb-submit').removeClass('sqb-loading').prop('disabled', false);
                }, this)
            });
        },

        showSaveModal: function() {
            $('#sqb-save-modal').show();
        },

        saveQuote: function() {
            const email = $('#save_email').val();

            if (!email || !this.isValidEmail(email)) {
                this.showToast('Please enter a valid email address.', 'error');
                return;
            }

            $.ajax({
                url: sqb_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'save_quote',
                    nonce: sqb_ajax.nonce,
                    quote_data: this.quoteData,
                    email: email
                },
                success: $.proxy(function(response) {
                    if (response.success) {
                        $('#sqb-save-modal').hide();
                        this.showSaveSuccess(response.data.share_code);
                    } else {
                        this.showToast(response.data.message || 'Failed to save quote.', 'error');
                    }
                }, this),
                error: $.proxy(function() {
                    this.showToast('An error occurred. Please try again.', 'error');
                }, this)
            });
        },

        showSaveSuccess: function(shareCode) {
            const $modal = $('#sqb-success-modal');
            $modal.find('h2').text('Quote Saved!');
            $modal.find('.sqb-modal-message').html(
                'Your quote has been saved.<br>' +
                'Share Code: <span class="sqb-share-code">' + shareCode + '</span>'
            );
            $modal.show();
        },

        showSuccessModal: function(message) {
            const $modal = $('#sqb-success-modal');
            $modal.find('.sqb-modal-message').text(message || '');
            $modal.show();
        },

        resetBuilder: function() {
            // Reset form
            $('#sqb-form')[0].reset();

            // Reset quote data
            this.quoteData = {
                items: [],
                vehicle: null,
                condition: null,
                polishing: null,
                protection: null,
                extras: [],
                kits: [],
                hours: 0
            };

            // Reset selections
            $('.sqb-card').removeClass('selected');
            $('.sqb-checkbox-card').removeClass('checked');

            // Reset to step 1
            this.currentStep = 1;
            this.showStep(1);
            this.updateProgressBar();
            this.updateNavigationButtons();
            this.updateTotals();

            // Hide modals
            $('.sqb-modal').hide();

            // Clear storage
            this.clearStorage();
        },

        saveToStorage: function() {
            try {
                localStorage.setItem('sqb_quote', JSON.stringify(this.quoteData));
            } catch (e) {
                // Storage not available
            }
        },

        loadFromStorage: function() {
            try {
                const saved = localStorage.getItem('sqb_quote');
                if (saved) {
                    const data = JSON.parse(saved);
                    // Restore selections
                    if (data.vehicle) {
                        this.quoteData.vehicle = data.vehicle;
                        $('.sqb-vehicle-grid .sqb-card[data-value="' + data.vehicle + '"]').addClass('selected');
                    }
                    if (data.condition) {
                        this.quoteData.condition = data.condition;
                        $('.sqb-condition-grid .sqb-card-cond[data-value="' + data.condition + '"]').addClass('selected');
                    }
                    if (data.polishing) {
                        this.quoteData.polishing = data.polishing;
                        $('.sqb-polishing-grid .sqb-card-poly[data-value="' + data.polishing + '"]').addClass('selected');
                    }
                    if (data.protection) {
                        this.quoteData.protection = data.protection;
                        $('.sqb-protection-grid .sqb-card-pro[data-value="' + data.protection + '"]').addClass('selected');
                    }

                    // Restore checkboxes
                    if (data.extras && data.extras.length) {
                        data.extras.forEach(function(name) {
                            $('input[data-name="' + name + '"]').prop('checked', true).closest('.sqb-checkbox-card').addClass('checked');
                        });
                    }
                    if (data.kits && data.kits.length) {
                        data.kits.forEach(function(name) {
                            $('input[data-name="' + name + '"]').prop('checked', true).closest('.sqb-checkbox-card').addClass('checked');
                        });
                    }

                    // Restore items
                    this.quoteData.items = data.items || [];
                    this.updateTotals();
                }
            } catch (e) {
                // Storage not available or corrupted
            }
        },

        clearStorage: function() {
            try {
                localStorage.removeItem('sqb_quote');
            } catch (e) {
                // Storage not available
            }
        },

        scrollToTop: function() {
            $('html, body').animate({
                scrollTop: $('.sqb-header').offset().top - 20
            }, 300);
        },

        showToast: function(message, type) {
            // Remove existing toasts
            $('.sqb-toast').remove();

            // Create toast
            const $toast = $('<div class="sqb-toast ' + type + '">' + message + '</div>');
            $('body').append($toast);

            // Auto remove after 4 seconds
            setTimeout(function() {
                $toast.fadeOut(function() {
                    $(this).remove();
                });
            }, 4000);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        if ($('#sqb-quote-builder').length) {
            SQB.init();
        }
    });

})(jQuery);