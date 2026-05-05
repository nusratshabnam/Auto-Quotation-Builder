/**
 * Service Quote Builder - Admin JavaScript
 */

(function($) {
    'use strict';

    const SQAdmin = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Select all checkboxes
            $('#sqb-select-all').on('change', function() {
                $('input[name="sqb_quotes[]"]').prop('checked', $(this).prop('checked'));
            });

            // View quote details
            $(document).on('click', '.sqb-view-quote', function(e) {
                e.preventDefault();
                var quoteId = $(this).data('id');
                SQAdmin.viewQuote(quoteId);
            });

            // Close modal
            $(document).on('click', '.sqb-modal-close, .sqb-admin-modal', function(e) {
                if ($(e.target).hasClass('sqb-admin-modal') || $(e.target).hasClass('sqb-modal-close')) {
                    $('#sqb-quote-modal').hide();
                }
            });

            // Change quote status
            $(document).on('click', '.sqb-change-status', function(e) {
                e.preventDefault();
                var quoteId = $(this).data('id');
                var status = $(this).data('status');
                SQAdmin.changeStatus(quoteId, status);
            });

            // Delete quote
            $(document).on('click', '.sqb-delete-quote', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this quote?')) {
                    var quoteId = $(this).data('id');
                    SQAdmin.deleteQuote(quoteId);
                }
            });

            // Search functionality
            $('#sqb-search').on('input', function() {
                var search = $(this).val().toLowerCase();
                SQAdmin.filterQuotes(search);
            });

            // Filter by date range
            $('#sqb-date-from, #sqb-date-to').on('change', function() {
                SQAdmin.filterByDate();
            });
        },

        viewQuote: function(quoteId) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'sqb_get_quote',
                    nonce: sqb_admin.nonce,
                    quote_id: quoteId
                },
                beforeSend: function() {
                    $('#sqb-quote-content').html('<p class="sqb-loading">Loading...</p>');
                    $('#sqb-quote-modal').show();
                },
                success: function(response) {
                    if (response.success) {
                        $('#sqb-quote-content').html(response.data.html);
                    } else {
                        $('#sqb-quote-content').html('<p class="sqb-error">Error loading quote details.</p>');
                    }
                },
                error: function() {
                    $('#sqb-quote-content').html('<p class="sqb-error">An error occurred.</p>');
                }
            });
        },

        changeStatus: function(quoteId, status) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'sqb_change_status',
                    nonce: sqb_admin.nonce,
                    quote_id: quoteId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        // Update status badge
                        $('.sqb-quotes-table tr').each(function() {
                            if ($(this).find('.sqb-view-quote').data('id') === quoteId) {
                                var statusBadge = $(this).find('.sqb-status');
                                statusBadge.removeClass('sqb-status-pending sqb-status-completed sqb-status-cancelled');
                                statusBadge.addClass('sqb-status-' + status);
                                statusBadge.text(status.charAt(0).toUpperCase() + status.slice(1));
                            }
                        });
                        SQAdmin.showNotice('Status updated successfully!', 'success');
                    } else {
                        SQAdmin.showNotice('Error updating status.', 'error');
                    }
                }
            });
        },

        deleteQuote: function(quoteId) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'sqb_delete_quote',
                    nonce: sqb_admin.nonce,
                    quote_id: quoteId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove row from table
                        $('.sqb-quotes-table tr').each(function() {
                            if ($(this).find('.sqb-view-quote').data('id') === quoteId) {
                                $(this).fadeOut(function() {
                                    $(this).remove();
                                });
                            }
                        });
                        $('#sqb-quote-modal').hide();
                        SQAdmin.showNotice('Quote deleted successfully!', 'success');
                    } else {
                        SQAdmin.showNotice('Error deleting quote.', 'error');
                    }
                }
            });
        },

        filterQuotes: function(search) {
            var count = 0;
            $('.sqb-quotes-table tbody tr').each(function() {
                var text = $(this).text().toLowerCase();
                if (text.indexOf(search) > -1) {
                    $(this).show();
                    count++;
                } else {
                    $(this).hide();
                }
            });
            $('.sqb-count').text(count + ' quotes found');
        },

        filterByDate: function() {
            var fromDate = $('#sqb-date-from').val();
            var toDate = $('#sqb-date-to').val();

            $('.sqb-quotes-table tbody tr').each(function() {
                var dateText = $(this).find('td:nth-child(7)').text();
                var quoteDate = new Date(dateText);
                var show = true;

                if (fromDate && quoteDate < new Date(fromDate)) {
                    show = false;
                }
                if (toDate && quoteDate > new Date(toDate + 'T23:59:59')) {
                    show = false;
                }

                $(this).toggle(show);
            });
        },

        showNotice: function(message, type) {
            var noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
            var notice = '<div class="notice ' + noticeClass + '"><p>' + message + '</p></div>';

            $('.sqb-admin-content').prepend(notice);

            setTimeout(function() {
                notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    // Initialize
    $(document).ready(function() {
        if ($('.sqb-admin').length) {
            SQAdmin.init();
        }
    });

})(jQuery);